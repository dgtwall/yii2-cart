<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "cart_item".
 *
 * @property int $id
 * @property int $shopId 店铺id
 * @property int $productId 商品id
 * @property int $addUserId 买家id
 * @property int $skuId 规格id
 * @property int $productNum 添加数量
 * @property int $status
 * @property float $addPrice 加入购物车价格
 * @property float $discountPrice 折扣价格
 * @property float $payPrice 付款价格
 * @property string|null $addTime 加入购物车时间
 * @property string|null $createdTime 创建时间
 * @property string|null $updatedTime 更新时间
 * @property string|null $deletedTime 删除时间
 * @property string|null $cartId
 */
class CartItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cart_item';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdTime',
                'updatedAtAttribute' => 'updatedTime',
                'value' => function () {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }
    /**
     * {@inheritdoc} 为了使用grid-view组件的数据结构，理想的数据结构看下面的 formatCartItemsGroupShop 方法
     * @param $cartItems
     * @return array
     */
    public static function formatCartItemsList($cartItems)
    {
        $productIds = array_column($cartItems, 'productId');
        $products = Product::find()->where(['id' => $productIds])->asArray()->all();
        $products = ArrayHelper::index($products, 'id');
        $result = [];
        foreach ($cartItems as $cartItem) {
            //如果店铺id+商品id+skuid不存在则初始化商品数组
            $shopProductSku = $cartItem['shopId'].'-'.$cartItem['productId'].'-'.$cartItem['skuId'];
            if (!key_exists($shopProductSku, $result)) {
                $result[$shopProductSku] = $cartItem;
            } else {
                //这里对商品的价格和数量等需要聚合展示的数据做一个计算
                $result[$shopProductSku]['productNum'] += $cartItem['productNum'];
            }

            if (key_exists($cartItem['productId'],$products)) {
                $result[$shopProductSku]['productPrice'] = $products[$cartItem['productId']]['price'];
            } else {
                $result[$shopProductSku]['productPrice'] = '/';
            }
        }

        return $result;
    }

    /**
     * 计算商品的价格详情
     * @param $cartItems
     * @return array
     */
    public static function calCartItemsPrice($cartItems)
    {
        $productIds = array_column($cartItems, 'productId');
        $products = Product::find()->where(['id' => $productIds])->asArray()->all();
        $products = ArrayHelper::index($products, 'id');

        //先算店铺优惠-满减和折扣
        $shopCartItems = self::formatCartItemsGroupShop($cartItems);
        $allTotalPrice = 0.00;
        $allDiscountPrice = 0;
        foreach ($shopCartItems as &$shopCartItem) {
            $shopTotalPrice = 0;
            foreach ($shopCartItem as &$item) {
                //计算每个商品的折扣价格
                $allTotalPrice += $item['productNum'] * $products[$item['productId']]['price'];
                $itemPrice = self::callDiscountPrice($item['productNum'] * $products[$item['productId']]['price'], 'discount');
                $item['discountPrice'] = $itemPrice['discountPrice'] / $item['productNum'];
                $item['discountValue'] = $itemPrice['discountValue'];
                //计算店铺的累计价格
                $shopTotalPrice += $itemPrice['discountPrice'];
            }
            //店铺商品总价格
            $shopCartItem['shopTotalPrice'] = $shopTotalPrice;
            //计算店铺的满减价格
            $discountShopTotalPrice = self::callDiscountPrice($shopTotalPrice, 'shopDiscount');
            //店铺商品优惠后总价格
            $shopCartItem['discountShopTotalPrice'] = $discountShopTotalPrice['discountPrice'];
            //店铺商品优惠幅度值
            $shopCartItem['discountShopTotalValue'] = $discountShopTotalPrice['discountValue'];

            //所有商品总价格
            $allDiscountPrice += $shopCartItem['discountShopTotalPrice'];
        }

        //无任何折扣商品总价
        $shopCartItems['allTotalPrice'] = $allTotalPrice;
        //所有折扣满减之后的价格
        $shopCartItems['allDiscountPrice'] = self::callDiscountPrice($allDiscountPrice, 'fullDiscount')['discountPrice'];

        //最后实际支付的价格
        $shopCartItems['realPayPrice'] = $shopCartItems['allDiscountPrice'] - self::getDiscountRuleConfig(0)['redPacket']['value'];

        //最后有一步是根据计算的价格优惠，根据价格权重和每个商品设置的最低价格重新计算出每个商品最后实际应该付的价格，暂时省略

        //恢复成一维数据
        return $shopCartItems;
    }

    public static function callDiscountPrice($price, $ruleType)
    {
        $ruleConfig = self::getDiscountRuleConfig(0);
        $rule = $ruleConfig[$ruleType];
        $discountValue = 0;
        if ($ruleType == 'shopDiscount') {
            if ($price > $rule['full']) {
                $discountValue = floor($price % $rule['full']) * $rule['value'];
                $discountPrice = $price - $discountValue;
            } else {
                $discountValue = 0;
                $discountPrice = $price - $discountValue;
            }
        }

        if ($ruleType == 'fullDiscount') {
            $discountValue = floor(intval($price) % $rule['full']) * $rule['value'];
            $discountPrice = $price - $discountValue;
        }

        if ($ruleType == 'discount') {
            $discountPrice = $price * $rule['value'];
            $discountValue = $price - $price * $rule['value'];
        }

        if ($ruleType == 'redPacket') {
            $discountValue = $rule['value'];
            $discountPrice = $price - $rule['value'];
        }

        return [
            'discountValue' => $discountValue,
            'discountPrice' => $discountPrice,
        ];

    }

    public static function getDiscountRuleConfig($activityId)
    {
        return [
            //满减:满300减40
            'fullDiscount' => [
                'full' => 300,
                'value' => 40,

            ],
            //店铺满减满100减5
            'shopDiscount' => [
                'full' => 100,
                'value' => 5,
            ],
            //红包 3
            'redPacket' => [
                'value' => 3
            ],
            //折扣9折
            'discount' => [
                'value' => 0.9
            ]
        ];
    }



    /**
     * 会返回以店铺Id为一级索引，商品ID-skuID为二级索引的数据结构
     *
     * $data = [
     *  'shopIdA' =>[
     *      'productId-skuId' => [
     *          'id' => 1,
     *          'price' => 1,
     *       ],
     *      'productId-skuId' => [],
     *  ],
     *  'shopIdB' =>[
     *      'productId-skuId' => [],
     *      'productId-skuId' => [],
     *  ],
     * ];
     *
     * @param $cartItems
     * @return array
     */
    public static function formatCartItemsGroupShop($cartItems)
    {

        $result = [];
        $shops = ArrayHelper::index($cartItems, 'shopId');
        foreach ($cartItems as $cartItem) {
            //如果没有shopId则初始化商店数组
            if (!key_exists($cartItem['shopId'], $result)) {
                $result[$cartItem['shopId']] = [];
            }

            //如果商品id+skuId不存在则初始化商品数组
            $productSku = $cartItem['productId'].'-'.$cartItem['skuId'];
            if (!key_exists($productSku, $result[$cartItem['shopId']])) {
                $result[$cartItem['shopId']][$productSku] = $cartItem;
            } else {
                //这里对商品的价格和数量等需要聚合展示的数据做一个计算
                $result[$cartItem['shopId']][$productSku]['productNum'] += $cartItem['productNum'];
            }

        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['shopId', 'productId', 'addUserId', 'skuId', 'productNum', 'status'], 'integer'],
            [['addPrice',  'discountPrice', 'payPrice'], 'number'],
            [['addTime', 'createdTime', 'updatedTime', 'deletedTime'], 'safe'],
            [['cartId'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shopId' => '店铺ID',
            'productId' => '商品ID',
            'addUserId' => '添加人ID',
            'cartId' => '购物车ID',
            'skuId' => '规格 ID',
            'productNum' => '数量',
            'status' => '状态',
            'addPrice' => '添加时价格',
            'productPrice' => '商品价格',
            'discountPrice' => '折扣价格',
            'payPrice' => '支付价格',
            'addTime' => '添加时间',
            'createdTime' => '创建时间',
            'updatedTime' => '更新时间',
            'deletedTime' => '删除时间',
        ];
    }
}

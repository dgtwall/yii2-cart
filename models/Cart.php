<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cart".
 *
 * @property int $id
 * @property int $userId
 * @property string|null $createdTime
 * @property string|null $updatedTime
 * @property string|null $deletedTime
 */
class Cart extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cart';
    }

    public static function getUserCart()
    {
        if (Yii::$app->user->isGuest) {
            // 用户未登录，获取sessionId
            $sessionId = Yii::$app->session->id;
            // 根据sessionId获取购物车信息
            $cart = Cart::find()->where(['sessionId' => $sessionId])->one();
        } else {
            // 用户已登录，获取当前登录用户信息
            $user = Yii::$app->user->identity;
            // 根据用户ID获取购物车信息
            //再判断当前登录用户的sessionId是否之前添加过购物车，如果之前未登录添加过购物车则直接更新userId
            $sessionId = Yii::$app->session->id;
            $cart = Cart::find()->where(['sessionId' => $sessionId])->one();
            if ($cart === null) {
                $cart = Cart::find()->where(['userId' => $user->id])->one();
            } else {
                $cart->userId = $user->id;
                $cart->save();
            }
        }

        //如果没有购物车则创建购物车
        if ($cart === null) {
            $cart = new Cart();
            Yii::$app->user->isGuest ? $cart->sessionId = $sessionId : $cart->userId = $user->id;
            $cart->save();
        }

        return $cart;
    }

    public function getProducts()
    {
        return $this->hasMany(Product::class, ['cartId' => 'id']);
    }

    public function getCartItem()
    {
        return $this->hasMany(CartItem::class, ['cartId' => 'id']);
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId'], 'integer'],
            [['createdTime', 'updatedTime', 'deletedTime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'createdTime' => 'Created Time',
            'updatedTime' => 'Updated Time',
            'deletedTime' => 'Deleted Time',
        ];
    }
}

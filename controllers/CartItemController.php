<?php

namespace app\controllers;

use app\models\CartItem;
use app\models\Product;
use app\models\Cart;
use app\models\CartItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;

/**
 * CartItemController implements the CRUD actions for CartItem model.
 */
class CartItemController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all CartItem models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CartItemSearch();
        $cart = Cart::getUserCart();

        $queryParams = $this->request->queryParams;
        $queryParams['CartItemSearch']['cartId'] = $cart['id'];
        $cartItems = $searchModel->search($queryParams);

        $cartItemsArr = ArrayHelper::toArray($cartItems->getModels());

        $products = Product::find()->all();

        //格式化购物车的展示数据
        if (count($cartItemsArr) > 0) {
            $cartItemsArr = CartItem::formatCartItemsList($cartItemsArr);
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $cartItemsArr,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'products' => $products,
            'cart' => $cart,
            'cartItems' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CartItem model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CartItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $cartItemModel = new CartItem();

        if ($this->request->isPost) {
            $productId = $this->request->post('productId');
            $product = Product::find()->where(['id' => $productId])->one();
            $cart = Cart::getUserCart();
            if ($product === null) {
                //报错重定向
                return $this->redirect(Url::to(['cart-item/index']));
            } else {
                //商品信息
                $cartItemModel->productId = $product->id;
                $cartItemModel->shopId = $product->shopId;
                $cartItemModel->skuId = $product->skuId;
                $cartItemModel->addPrice = $product->price;

                //购物车信息
                $cartItemModel->cartId = $cart->id;
                $cartItemModel->addUserId = $cart->userId;
                $cartItemModel->addUserSessionId = $cart->sessionId;
            }

            if ($cartItemModel->load($this->request->post(), '') && $cartItemModel->save()) {
                return $this->redirect(Url::to(['cart-item/index']));
            } else {
                $errors = $cartItemModel->getErrors();
            }
        }

        return $this->redirect(Url::to(['cart-item/index']));
    }


    /**
     * Updates an existing CartItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 删除失效商品
     * @param $id
     * @return void
     */
    public function actionClearInvalid($id){
    }

    /**
     *
     */
    public function actionCalTotalPrice()
    {
        $cart = Cart::getUserCart();
        $cartItems = CartItem::find()->where(['cartId' => $cart['id']])->asArray()->all();
        $result = CartItem::calCartItemsPrice($cartItems);
        echo '商品总价:' . $result['allTotalPrice'] .'元'. '<br>';
/*        echo '商品优惠价格--' . $result['allDiscountPrice'] .'元'. '<br>';
        echo '商品支付价格--' . $result['realPayPrice'] .'元'. '<br>';
        echo '<pre>';
        var_dump($result);
        echo '</pre>';
        exit;*/
    }


    /**
     * Deletes an existing CartItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CartItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return CartItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CartItem::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

<?php

use app\models\CartItem;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\CartItemSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '购物车';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cart-item-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <div style="display:flex;flex-wrap:wrap;">
        <?php foreach ($products as $product): ?>
            <form method="post" action="<?= Yii::$app->urlManager->createUrl(['cart-item/create']) ?>" style="margin-right:10px">
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
                <input type="hidden" name="productId" value="<?= $product->id ?>">
                <input type="hidden" name="cartId" value="<?= $cart->id ?>">
                <button type="submit" class="btn btn-success">添加<?= $product->name ?></button>
            </form>
        <?php endforeach; ?>

        <form method="get" action="<?= Yii::$app->urlManager->createUrl(['cart-item/calTotalPrice', 'cartId' => $cart->id]) ?>" style="margin-right:10px">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
            <button type="submit" class="btn btn-success">总价计算</button>
        </form>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $cartItems,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'shopId',
            'productId',
            'addUserId',
            'skuId',
            'productPrice',
            'productNum',
            'status',
            //'cartId',
            //'discountPrice',
            //'payPrice',
            //'addTime',
            //'createdTime',
            //'updatedTime',
            //'deletedTime',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action,  $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model['id']]);
                 }
            ],
        ],
    ]); ?>

</div>

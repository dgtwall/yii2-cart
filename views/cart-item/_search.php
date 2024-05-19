<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\CartItemSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="cart-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'shopId') ?>

    <?= $form->field($model, 'productId') ?>

    <?= $form->field($model, 'addUserId') ?>

    <?= $form->field($model, 'skuId') ?>

    <?= $form->field($model, 'productPrice') ?>

    <?php // echo $form->field($model, 'productNum') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'addPrice') ?>


    <?php // echo $form->field($model, 'discountPrice') ?>

    <?php // echo $form->field($model, 'payPrice') ?>

    <?php // echo $form->field($model, 'addTime') ?>

    <?php // echo $form->field($model, 'createdTime') ?>

    <?php // echo $form->field($model, 'updatedTime') ?>

    <?php // echo $form->field($model, 'deletedTime') ?>

    <?php // echo $form->field($model, 'cartId') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

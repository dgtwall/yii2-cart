<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\CartItem $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="cart-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'shopId')->textInput() ?>

    <?= $form->field($model, 'productId')->textInput() ?>

    <?= $form->field($model, 'skuId')->textInput() ?>

    <?= $form->field($model, 'productNum')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'addPrice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'discountPrice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'payPrice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'productPrice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'addTime')->textInput() ?>

    <?= $form->field($model, 'createdTime')->textInput() ?>

    <?= $form->field($model, 'updatedTime')->textInput() ?>

    <?= $form->field($model, 'deletedTime')->textInput() ?>

    <?= $form->field($model, 'cartId')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

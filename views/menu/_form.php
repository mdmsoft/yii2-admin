<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use mdm\admin\components\MenuHelper;
use mdm\admin\models\Menu;

/**
 * @var yii\web\View $this
 * @var mdm\admin\models\Menu $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="menu-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'parent_name')->widget(AutoComplete::className(),[
        'options'=>['class'=>'form-control'],
        'clientOptions'=>[
            'source'=>  Menu::find()->select(['name'])->column()
        ]
    ]) ?>

    <?= $form->field($model, 'route')->widget(AutoComplete::className(),[
        'options'=>['class'=>'form-control'],
        'clientOptions'=>[
            'source'=> Menu::getSavedRoutes()
        ]
    ]) ?>

    <?= $form->field($model, 'order')->input('number') ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 4]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
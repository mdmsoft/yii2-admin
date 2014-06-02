<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use mdm\admin\components\AccessHelper;
use mdm\admin\models\Menu;

/**
 * @var yii\web\View $this
 * @var mdm\admin\models\Menu $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="menu-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'menu_name')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'menu_parent_name')->widget(AutoComplete::className(),[
        'options'=>['class'=>'form-control'],
        'clientOptions'=>[
            'source'=>  Menu::find()->select(['menu_name'])->column()
        ]
    ]) ?>

    <?= $form->field($model, 'menu_route')->widget(AutoComplete::className(),[
        'options'=>['class'=>'form-control'],
        'clientOptions'=>[
            'source'=>  AccessHelper::getSavedRoutes()
        ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
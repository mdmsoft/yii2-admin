<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;


/* @var $this yii\web\View */
/* @var $model mdm\admin\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-form">
    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>

        <?= $form->field($model, 'ruleName')->textInput(['id'=>'rule-name']) ?>

        <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

        <div class="form-group">
            <?php
            echo Html::submitButton($model->isNewRecord ? Yii::t('rbac-admin', 'Create') : Yii::t('rbac-admin', 'Update'), [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
<?php
list(,$url) = $this->assetManager->publish('@mdm/admin/assets');
$this->registerJsFile($url.'/jquery-ui.js', ['depends'=>'yii\web\JqueryAsset']);
$this->registerCssFile($url.'/jquery-ui.css');
$options = Json::htmlEncode([
    'source' => array_keys(Yii::$app->authManager->getRules())
]);
$this->registerJs("$('#rule-name').autocomplete($options);");
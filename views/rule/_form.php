<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var mdm\admin\models\AuthItem $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'className')->textInput() ?>

    <?=
        $form->field($model, 'expresion')
        ->textarea(['rows' => 2,
            'disabled' => $model->className != '' && $model->className != 'mdm\admin\components\BizRule'])
        ->hint('Simple PHP expresion')
    ?>

    <div class="form-group">
        <?php
        echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
        ?>
    </div>

<?php ActiveForm::end(); ?>
</div>
<?php
$js = <<< JS
\$('#bizrule-classname').change(function(){
    var val = this.value;
    var className = 'mdm\\\\admin\\\\components\\\\BizRule';
    if(val=='' || val==className){
        \$('#bizrule-expresion').removeAttr('disabled');
    }else{
        \$('#bizrule-expresion').prop('disabled',true);
    }
    
})
JS;

$this->registerJs($js);

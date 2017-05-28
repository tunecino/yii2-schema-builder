<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model tunecino\models\EntitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="entity-search" style="display:none;">

    <?php $form = ActiveForm::begin([
        'action' => ['view', 'id' => $schema->id],
        'method' => 'get',
        'layout' => 'inline',
        'options' => ['style' => ['display' => 'inherit']],
    ]); ?>

    <?= $form->field($model, 'name', [
        'template' => ' {input}<span class="fui-search form-control-feedback"></span>{error}{hint}'
    ])->textInput(['placeholder' => 'Search Entity']); ?>

    <div class="form-group">
        <?= Html::submitButton('search', ['class' => 'btn btn-info']) ?>
        <a class="btn btn-default" onclick="$('.entity-search').toggle();">cancel</a>
    </div>

    <?php ActiveForm::end(); ?>

</div>

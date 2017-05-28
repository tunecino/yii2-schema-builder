<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model tunecino\models\Entity */
/* @var $form yii\widgets\ActiveForm */
?>

<span class="entity-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => ['data-type'=>'ajax', 'style' => ['display' => 'inherit']],
        'action' => $model->isNewRecord ? ['create-entity'] : ['update-entity', 'id' => $model->id],
        'enableAjaxValidation' => true
    ]); ?>

    <?php
    Modal::begin([
        'id' => 'entity-' . ($model->isNewRecord ? 'new' : $model->id),
        'size' => modal::SIZE_LARGE,
        'options' => ['data-backdrop' => 'false', 'data-dismiss' => 'modal' ],
        'header' =>  $model->isNewRecord ? 
            '<h6 class="text-center">Create & add a new Entity to Schema '.$model->schema->name.'</h6>' : '<h6 class="text-center">Update Entity '.$model->name.'</h6>',
        'toggleButton' => $model->isNewRecord ? 
            ['label' => '<span class="fui-plus"></span> NEW', 'class' => 'btn btn-primary pull-right'] :
            ['label' => '<span class="fui-new"></span> UPDATE', 'class' => 'btn btn-info'] ,
        'footer' => 
            Html::resetButton('Reset', ['class' => 'btn btn-default']) .
            Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info'])
    ]);
    ?>

    <?= $form->field($model, 'schema_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= Html::hiddenInput('reload-pjax', 'entity-form', ['disabled' => true, 'data-close-modal' => 'true']); ?>
    <?= Html::hiddenInput('reload-pjax', 'entity-list', ['disabled' => true]); ?>
    <?= Html::hiddenInput('reload-pjax', 'entity-info', ['disabled' => true]); ?>
    <?= Html::hiddenInput('reload-pjax', 'entity-title', ['disabled' => true]); ?>
    <?= Html::hiddenInput('reload-pjax', 'breadcrumbs', ['disabled' => true]); ?>

    <?php Modal::end(); ActiveForm::end(); ?>

</span>

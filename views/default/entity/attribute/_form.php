<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model tunecino\models\Attribute */
/* @var $form yii\widgets\ActiveForm */

$rid = uniqid();
?>

<div class="attribute-form">

    <?php $form = ActiveForm::begin([
        'options' => ['data-type' => 'ajax'],
        'action' => $model->isNewRecord ? ['create-attribute'] : ['update-attribute', 'id' => $model->id],
        'enableClientValidation' => false,
        'enableAjaxValidation' => true
    ]); ?>

    <?php
    Modal::begin([
        'id' => 'attribute-' . ($model->isNewRecord ? 'new-'.$rid : $model->id.'-'.$rid),
        'size' => Modal::SIZE_LARGE,
        'header' =>  $model->isNewRecord ? '<h6>Create Attribute</h6>' : '<h6>Update Attribute '.$model->name.'</h6>',
        'toggleButton' => $model->isNewRecord ? 
            ['label' => '<span class="fui-plus"></span>', 'class' => 'btn btn-primary pull-right'] :
            ['label' => '', 'tag' => 'a', 'class' => 'glyphicon glyphicon-pencil text-default', 'data-pjax' => 0] ,
        'footer' => 
            Html::resetButton('Reset', ['class' => 'btn btn-default']) .
            Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info'])
    ]);
    ?>

    <div class="row text-left">
     <div class="col-sm-6" style="border-right: 1px solid #ddd; padding: 0 25px;">

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'type')->dropDownList($model->types, [
            'prompt' => '',
            'data' => [
                'url' => Url::to(['default/attribute-extra-fields'], true),
                'rid' => $rid,
            ],
            'onchange' => "
                var url = $(this).data('url'),
                    rid = $(this).data('rid'),
                    type = $(this).val();

                var lengthField = $('.field-length-' + rid),
                    precisionField = $('.field-precision-' + rid),
                    scaleField = $('.field-scale-' + rid);

                $.get(url, {type: type}, function(fields) {
                    fields.lengthRequired ? lengthField.show() : lengthField.hide();
                    fields.precisionRequired ? precisionField.show() : precisionField.hide();
                    fields.scaleRequired ? scaleField.show() : scaleField.hide();
                });
            "]) ?>

        <?= $form->field($model, 'entity_id')->hiddenInput()->label(false) ?>

        <hr>

        <div class="form-group">
            <label class="checkbox" for="req-<?= $rid ?>">
                <input type="hidden" name="Attribute[required]" value="0">
                <input name="Attribute[required]" type="checkbox" data-toggle="checkbox" value="1" id="req-<?= $rid ?>" <?= $model->required ? 'checked' : '' ?>>
                Required
            </label>
        </div>

        <div class="form-group">
            <label class="checkbox" for="unq-<?= $rid ?>">
                <input type="hidden" name="Attribute[unique]" value="0">
                <input name="Attribute[unique]" type="checkbox" data-toggle="checkbox" value="1" id="unq-<?= $rid ?>" <?= $model->unique ? 'checked' : '' ?>>
                Unique
            </label>
        </div>

     </div>

     <div class="col-sm-6" style="padding: 0 25px;">

        <?= $form->field($model, 'length', $model->lengthRequired() ? [] : ['options' => ['style' => 'display:none;']])->textInput(['id' => 'length-' . $rid]) ?>
        <?= $form->field($model, 'precision', $model->precisionRequired() ? [] : ['options' => ['style' => 'display:none;']])->textInput(['id' => 'precision-' . $rid]) ?>
        <?= $form->field($model, 'scale', $model->scaleRequired() ? [] : ['options' => ['style' => 'display:none;']])->textInput(['id' => 'scale-' . $rid]) ?>
        <?= $form->field($model, 'default')->textInput(['maxlength' => true]) ?>
    </div>

    <?= Html::hiddenInput('reload-pjax', 'attribute-form', ['disabled' => true, 'data-close-modal' => 'true']); ?>
    <?= Html::hiddenInput('reload-pjax', $model->name . '-attribute-form'); ?>
    <?= Html::hiddenInput('reload-pjax', 'attribute-list', ['disabled' => true]); ?>
    <?= Html::hiddenInput('reload-pjax', 'relationship-list', ['disabled' => true]); ?>

    </div>

    <?php Modal::end(); ActiveForm::end(); ?>

</div>


<?php
// http://designmodo.github.io/Flat-UI/docs/components.html#fui-checkbox
$this->registerJs(<<<JS
jQuery(':checkbox').radiocheck();
JS
);
?>
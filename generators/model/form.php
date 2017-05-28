<?php

use yii\gii\generators\model\Generator;

if (!$model->generateQuery) 
    $this->registerCss("#query-configs {display: none;}");

?>

<?= $form->field($model, 'ns')->textInput() ?>
<?= $form->field($model, 'db')->textInput() ?>
<?= $form->field($model, 'baseClass')->textInput() ?>
<?= $form->field($model, 'generateRelations')->dropDownList([
    Generator::RELATIONS_NONE => 'No relations',
    Generator::RELATIONS_ALL => 'All relations',
    Generator::RELATIONS_ALL_INVERSE => 'All relations with inverse',
]) ?>

<?= $form->field($model, 'generateQuery')->checkbox([
    'onchange' => "
        this.checked ? $('#query-configs').show() : $('#query-configs').hide();
    "
]) ?>
<div id="query-configs" class="breadcrumb">
	<?= $form->field($model, 'queryNs')->textInput() ?>
	<?= $form->field($model, 'queryClass')->textInput() ?>
	<?= $form->field($model, 'queryBaseClass')->textInput() ?>
</div>

<?= $form->field($model, 'useSchemaName')->checkbox() ?>

<?= $this->render('../_base_attributes_form', ['model' => $model, 'form' => $form]) ?>

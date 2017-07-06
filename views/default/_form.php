<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\gii\generators\model\Generator;

/* @var $this yii\web\View */
/* @var $schema tunecino\models\Schema */
/* @var $form yii\widgets\ActiveForm */

$schemaForms = $schema->loadForms();
?>

<span class="schema-form">

    <?php $form = ActiveForm::begin([
        'options' => ['data-type' => 'ajax', 'style' => ['display' => 'inherit']],
    	  'action' => $schema->isNewRecord ? ['create-schema'] : ['update-schema', 'id' => $schema->id],
        // https://stackoverflow.com/questions/28756397/yii2-conditional-validator-always-returns-required
        'enableClientValidation' => false,
        'enableAjaxValidation' => true
    ]); ?>

    <?php Modal::begin([
        'id' => 'schema-' . ($schema->isNewRecord ? 'new' : $schema->id),
        'options' => ['class' => 'modal modal-fullscreen fade', 'data-backdrop' => 'static'],
        'header' =>  $schema->isNewRecord ? 
            '<h6 class="text-center">Create New Schema</h6>' : '<h6 class="text-center">Update Schema '.$schema->name.'</h6>',
        'toggleButton' => $schema->isNewRecord ? 
            ['label' => '<span class="fui-plus"></span> NEW', 'class' => 'btn btn-primary pull-right mt20 mb20'] :
            ['label' => '<span class="fui-new"></span> UPDATE', 'class' => 'btn btn-info mt20 mb20'] ,
        'footer' => 
            Html::resetButton('Reset all', ['class' => 'btn btn-default']) .
            Html::submitButton($schema->isNewRecord ? 'Create' : 'Update', ['class' => $schema->isNewRecord ? 'btn btn-primary' : 'btn btn-info'])
    ]); ?>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Schema</a></li>
    <?php foreach ($schemaForms as $fname => $fobj) { ?>
        <li role="presentation"><a href="#<?= $fname ?>" aria-controls="<?= $fname ?>" role="tab" data-toggle="tab"><?= $fname ?></a></li>
    <?php } ?>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="main">
        <?= $form->field($schema, 'id')->hiddenInput()->label(false) ?>
        <?= $form->field($schema, 'name')->textInput(['maxlength' => true])?>
        <?= $form->field($schema, 'isModule')->hiddenInput()->label(false) ?>
    </div>
    <?php foreach ($schemaForms as $fname => $fobj) { ?>
        <div role="tabpanel" class="tab-pane" id="<?= $fname ?>">
            <?= $form->field($schema, 'id')->hiddenInput()->label(false) ?>
            <?= $this->renderFile($fobj['viewFile'], [
                'model' => $fobj['model'],
                'form' => $form
            ]) ?>
        </div>
    <?php } ?>
  </div>

  <?= Html::hiddenInput('reload-pjax', 'schema-form', ['disabled' => true, 'data-close-modal' => 'true']); ?>
  <?= Html::hiddenInput('reload-pjax', 'schema-list', ['disabled' => true]); ?>
  <?= Html::hiddenInput('reload-pjax', 'schema-title', ['disabled' => true]); ?>
  <?= Html::hiddenInput('reload-pjax', 'schema-info', ['disabled' => true]); ?>
  <?= Html::hiddenInput('reload-pjax', 'breadcrumbs', ['disabled' => true]); ?>
    
  <?php Modal::end(); ActiveForm::end(); ?>

</span>


<?php
// http://designmodo.github.io/Flat-UI/docs/components.html#fui-checkbox
$this->registerJs(<<<JS
jQuery(':checkbox').radiocheck();
JS
);
?>
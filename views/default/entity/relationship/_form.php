<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model tunecino\models\Relationship */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="relationship-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => ['data-type'=>'ajax', 'style' => ['display' => 'inherit']],
        'action' => $model->isNewRecord ? ['create-relationship'] : ['update-relationship', 'id' => $model->id],
        'enableClientValidation' => false,
        'enableAjaxValidation' => true
    ]); ?>

    <?php
    Modal::begin([
        'id' => 'relationship-' . ($model->isNewRecord ? 'new' : $model->id),
        'header' =>  $model->isNewRecord ? "<h6>add new relation to <b><i>".$model->entity->name."</i></b></h6>" : "<h6>update <b><i>".$model->entity->name."-".$model->relatedTo->name."</i></b> relationship</h6>",
        'toggleButton' => $model->isNewRecord ? 
            ['label' => '<span class="fui-plus"></span>', 'class' => 'btn btn-primary pull-right'] :
            ['label' => '', 'tag' => 'a', 'class' => 'glyphicon glyphicon-pencil'] ,
        'footer' => 
            Html::resetButton('Reset', ['class' => 'btn btn-default']) .
            Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info'])
    ]);
    ?>

    <?= $form->field($model, 'entity_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'rel_type')->dropDownList($model->types, ['prompt' => '',])?>

    <?php 
        if ($model->isNewRecord) echo $form->field($model, 'related_to')->dropDownList(
            ArrayHelper::map($model->entity->schema->entities, 'id', 'name'), 
            [
                'prompt' => '',
                'onchange'=> '
                    $(".reversed-rel-to").text(this.options[this.selectedIndex].text);
                    $(".field-relationship-reversed").show();
                ',
            ]
         );

        else echo $form->field($model->relatedTo, 'name')->textInput(['readonly' => true]);
    ?>

    <?= $form->field($model, 'reversed', ['options' => $model->isNewRecord ? ['style'=>'display:none'] : [] ])
             ->label('Reversed Case')
             ->radioList($model->inversedRelationLabels,
                [
                    'item' => $model-> isNewRecord
                    ? function($index, $label, $name, $checked, $value) {
                        $item = '<label class="radio text-left">';
                        $item .= '<input type="radio" name="' . $name . '" value="' . $value . '">';
                        $item .= $label;
                        $item .= '</label>';
                        return $item;
                    }
                    : function($index, $label, $name, $checked, $value) {
                        $isChecked = $checked ? 'checked' : '';
                        $item = '<label class="radio text-left">';
                        $item .= '<input type="radio" name="' . $name . '" value="' . $value . '" '. $isChecked .'>';
                        $item .= $label;
                        $item .= '</label>';
                        return $item;
                    }
                ])
    ?>

    <?= Html::hiddenInput('reload-pjax', 'relationship-form', ['disabled' => true, 'data-close-modal' => 'true']); ?>
    <?= Html::hiddenInput('reload-pjax', 'relationship-list', ['disabled' => true]); ?>

    <?php Modal::end(); ActiveForm::end(); ?>

</div>

<?php
// http://designmodo.github.io/Flat-UI/docs/components.html#fui-checkbox
$this->registerJs(<<<JS
jQuery(':checkbox').radiocheck();
jQuery(':radio').radiocheck();
JS
);
?>
<?php

$uniq = uniqid();
if (!$model->enableI18N) 
    $this->registerCss("#msgCat-". $uniq ."-configs {display: none;}");
?>

<?= $form->field($model, 'enableI18N')->checkbox([
    'onchange' => "
        this.checked ? $('#msgCat-".$uniq."-configs').show() : $('#msgCat-".$uniq."-configs').hide();
    "
]) ?>
<div id="msgCat-<?= $uniq ?>-configs" class="breadcrumb">
	<?= $form->field($model, 'messageCategory')->textInput() ?>
</div>

<?= $form->field($model, 'appconfig')->textInput() ?>
<?= $form->field($model, 'template')->textInput() ?>

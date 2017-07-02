<?= $form->field($model, 'modelNamespace')->textInput() ?>
<?= $form->field($model, 'controllerNamespace')->textInput() ?>
<?= $form->field($model, 'baseControllerClass')->textInput() ?>
<?= $form->field($model, 'searchModelNamespace')->textInput() ?>
<?= $form->field($model, 'baseViewPath')->textInput() ?>

<?= $form->field($model, 'indexWidgetType')->dropDownList([
    'grid' => 'GridView',
    'list' => 'ListView'
]) ?>

<?= $form->field($model, 'enablePjax')->checkbox() ?>

<?= $this->render('../_base_attributes_form', ['model' => $model, 'form' => $form]) ?>

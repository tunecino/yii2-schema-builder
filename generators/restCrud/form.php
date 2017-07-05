<?= $form->field($model, 'modelNamespace')->textInput() ?>
<?= $form->field($model, 'controllerNamespace')->textInput() ?>
<?= $form->field($model, 'baseControllerClass')->textInput() ?>
<?= $form->field($model, 'searchModelNamespace')->textInput() ?>

<?= $this->render('../_base_attributes_form', ['model' => $model, 'form' => $form]) ?>

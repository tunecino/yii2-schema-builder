<?= $form->field($model, 'db')->textInput() ?>
<?= $form->field($model, 'migrationTable')->textInput() ?>
<?= $form->field($model, 'migrationPath')->textInput() ?>
<?= $form->field($model, 'templateFile')->textInput() ?>
<?= $form->field($model, 'useTablePrefix')->checkbox() ?>

<?= $this->render('../_base_attributes_form', ['model' => $model, 'form' => $form]) ?>

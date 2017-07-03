<?php
    
    $moduleID = ($model->schema && $model->schema->name) ? \yii\helpers\Inflector::variablize($model->schema->name) : 'xyz';
    if (!$model->generateAsModule) $this->registerCss("#module-configs {display: none;}");

?>


<?= $form->field($model, 'generateAsModule')->checkbox([
    'onchange' => "
        this.checked ? $('#module-configs').show() : $('#module-configs').hide();
    "
])?>
<div id="module-configs" class="breadcrumb">
      <?= $form->field($model, 'moduleNamespace')->textInput() ?>
      <?= $this->render('../_base_attributes_form', ['model' => $model, 'form' => $form]) ?>

      <div class="alert alert-warning">
            <p class="help-block"><b>Note:</b> Generating Schema as Module requires further steps. You need to manually define it under your app's config file by adding a similar code to this:</p>

            <pre style="margin:15px 0 15px">
              <code>
              'modules' => [
                  '<?= $moduleID ?>' => [
                      'class' => 'app\modules\<?= $moduleID ?>\Module',
                  ],
              ],
              </code>
            </pre>

            <p class="help-block">See <a target="_blank" href="http://www.yiiframework.com/doc-2.0/guide-structure-modules.html">official docs</a> for more details.</p>
      </div>
  
</div>

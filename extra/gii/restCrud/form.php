<?php
/* @var $generator yii\gii\generators\crud\Generator */

echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'searchModelClass');
echo $form->field($generator, 'controllerClass');
echo $form->field($generator, 'baseControllerClass');

echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');

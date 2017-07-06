<?php

namespace tunecino\builder\generators\module;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\gii\generators\module\Generator as GiiGenerator;


class Generator extends \tunecino\builder\Generator
{
    
    public function getCoreAttributes()
    {
        return [
            'generateAsModule' => false,
            'moduleNamespace' => null,
        ];
    }


    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'generateAsModule' => 'Generate it as Module',
            'moduleNamespace' => 'Module Namespace',
        ]);
    }


    public function rules()
    {
        return array_merge(parent::rules(), [
            ['moduleNamespace', 'filter', 'filter' => 'trim'],
            ['generateAsModule', 'boolean'],
            ['moduleNamespace', 'filter', 'filter' => function($value) { return trim($value, '\\'); }],
            ['moduleNamespace', 'required', 'when' => function($model) { return $model->generateAsModule; }],
            ['moduleNamespace', 'match', 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            ['moduleNamespace', 'validateNamespace'],
            ['template', 'required', 'when' => function($model) { return $model->generateAsModule; }, 'message' => 'A code template must be selected.'],
            ['messageCategory', 'validateMessageCategory', 'when' => function($model) { return $model->generateAsModule; }],
            [['appconfig','template'], 'filter', 'filter' => 'trim'],
            ['appconfig', 'validateFilePath', 'when' => function($model) { return $model->generateAsModule; }],
        ]);
    }
    

    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), (new GiiGenerator)->hints(), [
            'generateAsModule' => 'This indicates whether this schema should be generated as a seperate module.</i>',
            'moduleNamespace' => 'This is the namespace of the Module class to be generated, e.g., <code>app\modules</code>. You\'ll also need to alter the <b>namecpaces</b> of the other settings like <b>models</b> and <b>controllers</b> whenever it should be generated into this Module.',
        ]);
    }


    public function getModuleID()
    {
        return ($this->schema && $this->schema->name) ? \yii\helpers\Inflector::variablize($this->schema->name) : null;
    }


    public function getConsoleCommands()
    {
        $cmd = 'yii gii/module';
        if ($this->moduleID) $cmd .= ' --moduleID="'. $this->moduleID .'"';
        if ($this->moduleNamespace) $cmd .= ' --moduleClass="'. $this->moduleNamespace . '\\' . $this->moduleID . '\\' . 'Module"';
        $cmd .= ' --interactive=0 --overwrite=1';
        return $this->moduleNamespace ? [$cmd] : [];
    }


    public function afterSave($insert, $changedAttributes)
    {
        if (($insert && $this->generateAsModule) || isset($changedAttributes['generateAsModule'])) {
            $schema = $this->schema;
            $schema->isModule = $this->generateAsModule;
            $schema->save();
        }
        parent::afterSave($insert, $changedAttributes);
    }
    
}


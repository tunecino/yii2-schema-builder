<?php

namespace tunecino\builder\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\InvalidConfigException;


class Schema extends \yii2tech\filedb\ActiveRecord
{
    
    public static function getDb()
    {
        return \tunecino\builder\Module::getInstance()->get('filedb');
    }

    public function attributes()
    {
        return array_keys($this->attributeLabels());
    }

    public function init()
    {
        parent::init();
        $this->setAttributes([
            'appconfig' => null,
            'template' => 'default',
            'enableI18N' => false,
            'messageCategory' => 'app'
        ], false);
    }

    public function getModuleID()
    {
        return \yii\helpers\Inflector::variablize($this->name);
    }


    public function getConsoleCommands()
    {
        $generatedCMD = [];

        if ($this->generateAsModule) {
            $moduleCmd = 'yii gii/module --moduleID="'. $this->moduleID .'"';
            if ($this->moduleNamespace) 
                $moduleCmd .= ' --moduleClass="'. $this->moduleNamespace . '\\' . $this->moduleID . '\\' . 'Module"';
            $generatedCMD[] = $moduleCmd;
        }

        $commands = Yii::$app->controller->module->commands;
        
        foreach ($commands as $cmd) {
            if (is_string($cmd)) $generatedCMD[] = $cmd;
            else if (is_callable($cmd)) $generatedCMD = array_merge($generatedCMD, $this->parseCallable($cmd));
            else if (is_array($cmd)) $generatedCMD = array_merge($generatedCMD, $this->parseClass($cmd));
            else throw new InvalidConfigException('commands config array should only hold "strings", "arrays" or "callable functions".');
        }

        return $generatedCMD;
    }


    protected function parseCallable($fn)
    {
        $callableCmd = call_user_func($fn, $this);

        if (is_string($callableCmd) === false && is_array($callableCmd) === false)
            throw new InvalidConfigException('unexpected output for "callable funtion".');

        return is_array($callableCmd) ? $callableCmd : [$callableCmd];
    }


    protected function parseClass($config)
    {
        $obj = Yii::createObject($config);
        $model = $this->getConfigModel($obj::className())->one();

        if ($model) {
            $modelCmd = $model->consoleCommands;

            if (is_string($modelCmd) === false && is_array($modelCmd) === false)
                throw new InvalidConfigException('unexpected output for "getConsoleCommands()".');

            return is_array($modelCmd) ? $modelCmd : [$modelCmd];
        }
        
        return [];        
    }


    protected function getConfigModel($class)
    {
        return $this->hasOne($class::className(), ['schema_id' => 'id']);
    }


    public function loadForms($modelOnly = false)
    {
        $forms = [];
        $commands = Yii::$app->controller->module->commands;
        
        foreach ($commands as $config) {
            if (is_array($config)) {
                $obj = Yii::createObject($config);
                $viewFile = $obj::formView();

                $model = $this->getConfigModel($obj::className())->one();
                $modelData = $model ?: $obj;

                if ($modelOnly) $forms[$obj::fileName()] = $modelData;
                else $forms[$obj::fileName()] = ['viewFile'=>$viewFile, 'model'=>$modelData];
            }
        }

        return $forms;
    }


    public function attributeLabels()
    {
        return [
            'id' => 'Primary Key',
            'name' => 'Name',
            'generateAsModule' => 'Generate it as Module',
            'moduleNamespace' => 'Module Namespace',
            'appconfig' => 'Application Config File',
            'enableI18N' => 'Enable I18N',
            'messageCategory' => 'Message Category',
            'template' => 'template',
        ];
    }


    public function rules()
    {
        return [
            [['name','moduleNamespace'], 'filter', 'filter' => 'trim'],
            ['name', 'required'],
            ['name', 'string'],
            ['name', 'unique', 'message' => 'schema "{value}" has already been created.'],
            ['generateAsModule', 'boolean'],
            ['moduleNamespace', 'filter', 'filter' => function($value) { return trim($value, '\\'); }],
            ['moduleNamespace', 'required', 'when' => function($model) { return $model->generateAsModule; }],
            ['moduleNamespace', 'match', 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            ['moduleNamespace', 'validateNamespace'],
            [['appconfig','template'], 'filter', 'filter' => 'trim'],
            ['appconfig', 'validateFilePath', 'when' => function($model) { return $model->generateAsModule; }],
            ['template', 'string'],
            ['template', 'required', 'when' => function($model) { return $model->generateAsModule; }, 'message' => 'A code template must be selected.'],
            ['messageCategory', 'validateMessageCategory', 'when' => function($model) { return $model->generateAsModule; }],
            ['enableI18N', 'boolean'],
        ];
    }


    public function validateNamespace($attribute)
    {
        $value = $this->$attribute;
        $value = ltrim($value, '\\');
        $path = Yii::getAlias('@' . str_replace('\\', '/', $value), false);
        if ($path === false) {
            $this->addError($attribute, 'Namespace must be associated with an existing directory.');
        }
    }

    public function validateFilePath($attribute, $params)
    {
        $file = $this->$attribute;
        $path = Yii::getAlias($file, false);
        if (substr($path, -4) !== '.php') $this->addError($attribute, "A php file is expected.");
        if ($path === false or !is_file($path)) {
            $this->addError($attribute, "File '$file' does not exist or has syntax error.");
        }
    }

    public function validateMessageCategory()
    {
        if ($this->enableI18N && empty($this->messageCategory)) {
            $this->addError('messageCategory', "Message Category cannot be blank.");
        }
    }


    public function attributeHints()
    {
        return [
            'name' => 'The Schema name e.g., <code>blog</code>, <code>v1</code>, <code>alpha-01</code>. If this is going to be generated as a seperate Module <b>yii\helpers\Inflector::variablize()</b> will be applied to it then used to fill the <b>moduleID</b> and <b>moduleClass</b> attributes.',
            'generateAsModule' => 'This indicates whether this schema should be generated as a seperate module.</i>',
            'moduleNamespace' => 'This is the namespace of the Module class to be generated, e.g., <code>app\modules</code>. You\'ll also need to alter the <b>namecpaces</b> of the other settings like <b>models</b> and <b>controllers</b> whenever it should be generated into this Module.',
            'enableI18N' => 'This indicates whether the generator should generate strings using <code>Yii::t()</code> method.
                Set this to <code>true</code> if you are planning to make your application translatable.',
            'messageCategory' => 'This is the category used by <code>Yii::t()</code> in case you enable I18N.',
            'appconfig' => 'Custom application configuration file path. If not set, default application configuration is used.',
            'template' => 'the name of the code template that the user has selected.',
        ];
    }


    public function getEntities()
    {
        return $this->hasMany(Entity::className(), ['schema_id' => 'id']);
    }


    public function readyToGenerate()
    {
        if (count($this->consoleCommands) === 0) return false;
        $entities = $this->entities;
        if (count($entities) === 0) return false;
        foreach ($entities as $entity) {
            if ($entity->getRelatedAttributes()->count() === 0) return false;
        }
        return true;
    }
    

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->id = uniqid();
        }
        return parent::beforeSave($insert);
    }


    public function afterDelete()
    {
        Entity::deleteAll(['schema_id' => $this->id]);
        return parent::afterDelete();
    }
}

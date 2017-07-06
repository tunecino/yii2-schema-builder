<?php

namespace tunecino\builder\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\InvalidConfigException;
use \yii\helpers\Inflector;


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


    public function getConsoleCommands()
    {
        $generatedCMD = [];
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
            'isModule' => 'Built as Module',
        ];
    }


    public function rules()
    {
        return [
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'required'],
            ['name', 'string'],
            ['name', 'unique', 'message' => 'schema "{value}" has already been created.'],
            ['isModule', 'boolean'],
            ['isModule', 'default', 'value' => '0'],
        ];
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


    public function attributeHints()
    {
        return [
            'name' => 'The Schema name e.g., <code>blog</code>, <code>v1</code>, <code>alpha-01</code>. If this is going to be generated as a seperate Module <b>yii\helpers\Inflector::variablize()</b> will be applied to it then used to fill the <b>moduleID</b> and <b>moduleClass</b> attributes.',
        ];
    }


    public function getEntities()
    {
        return $this->hasMany(Entity::className(), ['schema_id' => 'id']);
    }


    public function getModuleID()
    {
        return $this->isModule ? Inflector::variablize($this->name) : null;
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
        parent::afterDelete();
    }
}

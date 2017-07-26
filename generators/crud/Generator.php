<?php

namespace tunecino\builder\generators\crud;

use Yii;
use yii\gii\generators\crud\Generator as GiiGenerator;

class Generator extends \tunecino\builder\Generator
{
    public function getCoreAttributes()
    {
        return [
            'baseViewPath' => null,
            'baseControllerClass' => 'yii\web\Controller',
            'indexWidgetType' => 'grid',
            'enablePjax' => false,
            'modelNamespace' => 'app\models',
            'controllerNamespace' => 'app\controllers',
            'searchModelNamespace' => null,
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'baseViewPath' => 'Base View Path',
            'baseControllerClass' => 'Base Controller Class',
            'indexWidgetType' => 'Widget Used in Index Page',
            'searchModelClass' => 'Search Model Class',
            'enablePjax' => 'Enable Pjax',
            'modelNamespace' => 'Model Namespace',
            'controllerNamespace' => 'Controller Namespace',
        ]);
    }


    public function rules()
    {
        return array_merge(parent::rules(), [
            [['baseControllerClass', 'modelNamespace', 'controllerNamespace'], 'filter', 'filter' => 'trim'],
            [['baseControllerClass', 'indexWidgetType', 'modelNamespace', 'controllerNamespace'], 'required'],
            ['baseControllerClass', 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            ['baseControllerClass', 'validateClass', 'params' => ['extends' => \yii\web\Controller::className()]],
            ['indexWidgetType', 'in', 'range' => ['grid', 'list']],
            ['enablePjax', 'boolean'],
            ['baseViewPath', 'safe'],
            [['modelNamespace', 'controllerNamespace', 'searchModelNamespace'], 'filter', 'filter' => function($value) { return trim($value, '\\'); }],
            [['modelNamespace', 'controllerNamespace'], 'match', 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            ['searchModelNamespace', 'match', 'skipOnEmpty' => true, 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['modelNamespace', 'controllerNamespace'], 'validateNamespace'],
            ['searchModelNamespace', 'validateNamespace', 'skipOnEmpty' => true],
        ]);
    }


    public function attributeHints()
    {
        return array_merge(
            parent::attributeHints(), 
            (new GiiGenerator)->hints(), 
            [
                'modelNamespace' => 'This is the namespace of the ActiveRecord class to be generated, e.g., <code>app\models</code>',
                'controllerNamespace' => 'This is the namespace of the Controller class to be generated, e.g., <code>app\controllers</code>',
                'searchModelNamespace' => 'This is the namespace of the Search class to be generated, e.g., <code>app\models\searches</code>',
                'baseViewPath' => 'This is the base path to the directory for storing the view scripts for the controller, e.g., <code>/var/www/basic/controllers/views</code> , <code>@app/views</code> . ControllerID will be dynamically added added to it and if not set, it will be default to @app/views/ControllerID',
            ]
        );
    }


    protected function getModelClass($entity)
    {
        return $this->modelNamespace . '\\' . ucfirst($entity->name);
    }

    protected function getControllerClass($entity)
    {
        return $this->controllerNamespace . '\\' . ucfirst($entity->name) . 'Controller';
    }

    protected function getSearchModelClass($entity)
    {
        return $this->searchModelNamespace . '\\' . ucfirst($entity->name) . 'Search';
    }

    protected function getbaseViewPath($entity)
    {
        return $this->baseViewPath . '\\' . $entity->name;
    }


    public function getConsoleCommands()
    {
        $commands = [];

        foreach ($this->schema->entities as $entity) {
            $cmd = 'yii gii/crud';
            if ($this->modelNamespace) $cmd .= ' --modelClass="'.$this->getModelClass($entity).'"';
            if ($this->controllerNamespace) $cmd .= ' --controllerClass="'.$this->getControllerClass($entity).'"';
            if ($this->searchModelNamespace) $cmd .= ' --searchModelClass="'.$this->getSearchModelClass($entity).'"';
            if ($this->baseControllerClass) $cmd .= ' --baseControllerClass="'.$this->baseControllerClass.'"';
            if ($this->indexWidgetType) $cmd .= ' --indexWidgetType="'.$this->indexWidgetType.'"';
            if ($this->baseViewPath) $cmd .= ' --viewPath="'.$this->getbaseViewPath($entity).'"';
            if ($this->enablePjax) $cmd .= ' --enablePjax=1';
            $cmd .= ' --interactive=0 --overwrite=1';
            $commands[] = $cmd;
        }

        return $commands;
    }

}

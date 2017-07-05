<?php

namespace tunecino\builder\generators\restCrud;

use Yii;
use yii\gii\generators\crud\Generator as GiiGenerator;

class Generator extends \tunecino\builder\Generator
{
    public function getCoreAttributes()
    {
        return [
            'baseControllerClass' => 'yii\web\Controller',
            'modelNamespace' => 'app\models',
            'controllerNamespace' => 'app\controllers',
            'searchModelNamespace' => null,
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'baseControllerClass' => 'Base Controller Class',
            'searchModelClass' => 'Search Model Class',
            'modelNamespace' => 'Model Namespace',
            'controllerNamespace' => 'Controller Namespace',
        ]);
    }


    public function rules()
    {
        return array_merge(parent::rules(), [
            [['baseControllerClass', 'modelNamespace', 'controllerNamespace'], 'filter', 'filter' => 'trim'],
            [['baseControllerClass', 'modelNamespace', 'controllerNamespace'], 'required'],
            ['baseControllerClass', 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            ['baseControllerClass', 'validateClass', 'params' => ['extends' => \yii\web\Controller::className()]],
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


    public function getConsoleCommands()
    {
        $commands = [];

        foreach ($this->schema->entities as $entity) {
            $cmd = 'yii gii/rest-crud';
            if ($this->modelNamespace) $cmd .= ' --modelClass="'.$this->getModelClass($entity).'"';
            if ($this->controllerNamespace) $cmd .= ' --controllerClass="'.$this->getControllerClass($entity).'"';
            if ($this->searchModelNamespace) $cmd .= ' --searchModelClass="'.$this->getSearchModelClass($entity).'"';
            if ($this->baseControllerClass) $cmd .= ' --baseControllerClass="'.$this->baseControllerClass.'"';
            $cmd .= ' --interactive=0 --overwrite=1';
            $commands[] = $cmd;
        }

        return $commands;
    }

}

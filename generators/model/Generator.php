<?php

namespace tunecino\builder\generators\model;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\gii\generators\model\Generator as GiiGenerator;


class Generator extends \tunecino\builder\Generator
{
    
    public function getCoreAttributes()
    {
        return [
            'db' => 'db',
            'ns' => 'app\models',
            'baseClass' => 'yii\db\ActiveRecord',
            'generateRelations' => 'all',
            'useSchemaName' => true,
            'generateQuery' => false,
            'queryNs' => 'app\models',
            'queryClass' => null,
            'queryBaseClass' => 'yii\db\ActiveQuery',
            'useTablePrefix' => false,
        ];
    }


    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'ns' => 'Namespace',
            'db' => 'Database Connection ID',
            'baseClass' => 'Base Class',
            'generateQuery' => 'Generate ActiveQuery',
            'generateRelations' => 'Generate Relations',
            'queryNs' => 'ActiveQuery Namespace',
            'queryClass' => 'ActiveQuery Class',
            'queryBaseClass' => 'ActiveQuery Base Class',
            'useSchemaName' => 'Use Schema Name',
            'useTablePrefix' => 'Use Table Prefix',
        ]);
    }


    public function rules()
    {
        return array_merge(parent::rules(), [
            [['db', 'ns', 'baseClass', 'queryNs', 'queryClass', 'queryBaseClass'], 'filter', 'filter' => 'trim'],
            [['ns', 'queryNs'], 'filter', 'filter' => function($value) { return trim($value, '\\'); }],
            [['db', 'ns', 'baseClass', 'queryNs', 'queryBaseClass'], 'required'],
            [['db', 'queryClass'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['ns', 'baseClass', 'queryNs', 'queryBaseClass'], 'match', 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['db'], 'validateDb'],
            [['ns', 'queryNs'], 'validateNamespace'],
            [['baseClass'], 'validateClass', 'params' => ['extends' => ActiveRecord::className()]],
            [['queryBaseClass'], 'validateClass', 'params' => ['extends' => ActiveQuery::className()]],
            [['generateRelations'], 'in', 'range' => [GiiGenerator::RELATIONS_NONE, GiiGenerator::RELATIONS_ALL, GiiGenerator::RELATIONS_ALL_INVERSE]],
            [['useSchemaName', 'generateQuery', 'enableI18N', 'useTablePrefix'], 'boolean'],
        ]);
    }
    

    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), (new GiiGenerator)->hints());
    }


    public function getConsoleCommands()
    {
        $cmd = 'yii gii/model --tableName="*"';

        if ($this->appconfig) $cmd .= ' --appconfig="'.$this->appconfig.'"';
        if ($this->baseClass) $cmd .= ' --baseClass="'.$this->baseClass.'"';
        if ($this->db) $cmd .= ' --db="'.$this->db.'"';
        if ($this->enableI18N) $cmd .= ' --enableI18N=1';

        if ($this->generateQuery) $cmd .= ' --generateQuery=1';
        if ($this->generateQuery && $this->queryBaseClass) $cmd .= ' --queryBaseClass="'.$this->queryBaseClass.'"';
        if ($this->generateQuery && $this->queryNs) $cmd .= ' --queryNs="'.$this->queryNs.'"';

        if ($this->generateRelations) $cmd .= ' --generateRelations="'.$this->generateRelations.'"';
        if ($this->messageCategory) $cmd .= ' --messageCategory="'.$this->messageCategory.'"';
        if ($this->ns) $cmd .= ' --ns="'.$this->ns.'"';
        if ($this->template) $cmd .= ' --template="'.$this->template.'"';
        if (!$this->useSchemaName) $cmd .= ' --useSchemaName=0';
        if ($this->useTablePrefix) $cmd .= ' --useTablePrefix=1';
        
        $cmd .= ' --interactive=0 --overwrite=1';
        
        return [$cmd];
    }
    
}


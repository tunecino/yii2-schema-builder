<?php

namespace tunecino\builder\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\gii\generators\model\Generator as GiiGenerator;

class Entity extends \yii2tech\filedb\ActiveRecord
{
    public static function getDb()
    {
        return \tunecino\builder\Module::getInstance()->get('filedb');
    }

    public function attributes()
    {
        return array_keys($this->attributeLabels());
    }


    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'filter', 'filter' => 'strtolower'],
            [['name', 'schema_id'], 'filter', 'filter' => 'trim'],
            [['name'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['name'], 'validateKeyword', 'skipOnEmpty' => false],
            ['schema_id', 'string'],
            ['name', 'unique', 'targetAttribute' => ['name', 'schema_id'], 'comboNotUnique' => 'Entity "{value}" has already been defined.'],
            ['schema_id', 'exist', 'skipOnError' => true, 'targetClass' => Schema::className(), 'targetAttribute' => ['schema_id' => 'id']],
        ];
    }

    public function validateKeyword()
    {
        if ((new GiiGenerator)->isReservedKeyword($this->name)) {
            $this->addError('name', 'Class name cannot be a reserved PHP keyword.');
        }
    }


    public function attributeLabels()
    {
        return [
            'id' => 'Primary Key',
            'name' => 'Name',
            'schema_id' => 'Schema Id',
        ];
    }

    public function getRelatedAttributes()
    {
        return $this->hasMany(Attribute::className(), ['entity_id' => 'id']);
    }

    public function getRelationships()
    {
        return $this->hasMany(Relationship::className(), ['entity_id' => 'id']);
    }

    public function isHoldingForeignKey()
    {
        return !!$this->getRelationships()->where(['rel_type' => Relationship::HAS_ONE])->count();
    }

    public function getRelEntities()
    {
        return $this->hasMany(Entity::className(), ['id' => 'related_to'])->via('relationships');
    }

    public function getSchema()
    {
        return $this->hasOne(Schema::className(), ['id' => 'schema_id']);
    }

    public function getMigrationFields()
    {
        return implode(',', array_merge(
            array_filter(ArrayHelper::getColumn($this->relationships, 'migrationField')),
            array_filter(ArrayHelper::getColumn($this->relatedAttributes, 'migrationField'))
        ));
    }


    public function getPreviewUrl()
    {
        $fn = Yii::$app->controller->module->previewUrlCallback;
        if ($fn !== null && is_callable($fn)) return call_user_func($fn, $this);

        $module = $this->schema->moduleID;
        $moduleName = $module ? $module . '/' : '';
        return \yii\helpers\Url::toRoute( '/' . $moduleName . $this->name , true);
    }


    public function beforeSave($insert)
    {
        if ($insert) $this->id = uniqid();
        return parent::beforeSave($insert);
    }

    public function afterDelete()
    {
        Attribute::deleteAll(['entity_id' => $this->id]);
        Relationship::deleteAll(['entity_id' => $this->id]);
        return parent::afterDelete();
    }
}

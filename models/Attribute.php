<?php

namespace tunecino\builder\models;

use Yii;
use yii\db\Schema;


class Attribute extends \yii2tech\filedb\ActiveRecord
{
	const SCENARIO_JUNCTION = 'junction';


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
			[['name', 'type', 'entity_id'], 'required'],
			['type', 'in', 'range' => array_keys($this->types)],
			[['required', 'unique'], 'boolean'],
			[['entity_id', 'name', 'type', 'default'], 'string'],
			['name', 'compare', 'compareValue' => 'id', 'operator' => '!=', 'message' => 'Primary key will be added automatically. No need to create it.'],
			['name', 'unique', 'targetAttribute' => ['name', 'entity_id'], 'comboNotUnique' => 'Attribute "{value}" has already been defined.'],
			['entity_id', 'exist', 'skipOnError' => true, 'targetClass' => Entity::className(), 'targetAttribute' => ['entity_id' => 'id'], 'on' => self::SCENARIO_DEFAULT],
			['entity_id', 'exist', 'skipOnError' => true, 'targetClass' => Relationship::className(), 'targetAttribute' => ['entity_id' => 'id'], 'on' => self::SCENARIO_JUNCTION],
			['entity_id', 'validateJunctionRelation', 'on' => self::SCENARIO_JUNCTION],
			['length', 'integer', 'when' => function($model) {return $model->lengthRequired();}],
			['precision', 'integer', 'when' => function($model) {return $model->precisionRequired();}],
			['scale', 'integer', 'when' => function($model) {return $model->scaleRequired();}],
		];
	}


	public function validateJunctionRelation($attribute, $params)
	{
		if ($this->relationship->isManyToMany() === false) 
			$this->addError($attribute, "Entities to share this attribute are not sharing a many_to_many relationship");
	}


	public function attributeLabels()
	{
		return [
			'id' => 'Primary Key',
			'name' => 'Attribute Name',
			'entity_id' => 'Entity Name',
			'type' => 'Type',
			'required' => 'Required',
			'unique' => 'Unique',
			'length' => 'Length',
			'precision' => 'Precision',
			'scale' => 'Scale',
			'default' => 'Default Value',
		];
	}

	public function getMigrationField()
	{
		$field = $this->name . ':' . $this->type;

		if ($this->lengthRequired() && $this->length) $field .= '('.$this->length.')';
		else if ($this->precisionRequired() && $this->scaleRequired() && $this->precision && $this->scale) $field .= '('.$this->precision.','.$this->scale.')';
		else if ($this->precisionRequired() && $this->precision) $field .= '('.$this->precision.')';
		else if ($this->scaleRequired() && $this->scale) $field .= '(null,'.$this->scale.')';

		if ($this->required) $field .= ':notNull';
		if ($this->unique) $field .= ':unique';
		if (strlen($this->default) !== 0) {
			$default = (is_int($this->default) or ctype_digit($this->default)) ? $this->default : '\'' . $this->default . '\'';
			$field .= ':defaultValue(' . $default . ')';
		}

		return $field;
	}


	public function getEntity()
	{
		return $this->hasOne(Entity::className(), ['id' => 'entity_id']);
	}

	public function getRelationship()
	{
	  return $this->hasOne(Relationship::className(), ['id' => 'entity_id']);
	}


	public function getTypes()
	{
		 return [
			 Schema::TYPE_STRING => 'String',
			 Schema::TYPE_TEXT => 'Text',
			 Schema::TYPE_INTEGER => 'Integer',
			 Schema::TYPE_SMALLINT => 'Small Integer',
			 Schema::TYPE_BIGINT => 'Big Integer',
			 Schema::TYPE_FLOAT => 'Float',
			 Schema::TYPE_DOUBLE => 'Double',
			 Schema::TYPE_DECIMAL => 'Decimal',
			 Schema::TYPE_DATETIME => 'DateTime',
			 Schema::TYPE_TIMESTAMP => 'Timestamp',
			 Schema::TYPE_TIME => 'Time',
			 Schema::TYPE_DATE => 'Date',
			 Schema::TYPE_BINARY => 'Binary',
			 Schema::TYPE_BOOLEAN => 'Boolean',
			 Schema::TYPE_CHAR => 'Char',
			 Schema::TYPE_MONEY => 'Money',
		 ];
	}

	public function getTypeLabel()
	{
		return $this->types[$this->type];
	}


	public function lengthRequired()
	{
		return $this->type && in_array(
			 $this->type, [
			 Schema::TYPE_STRING,
			 Schema::TYPE_INTEGER,
			 Schema::TYPE_SMALLINT,
			 Schema::TYPE_BIGINT,
			 Schema::TYPE_BINARY,
			 Schema::TYPE_CHAR,
		 ]);
	}

	public function precisionRequired()
	{
		return $this->type && in_array(
		 $this->type, [
			 Schema::TYPE_FLOAT,
			 Schema::TYPE_DOUBLE,
			 Schema::TYPE_DECIMAL,
			 Schema::TYPE_DATETIME,
			 Schema::TYPE_TIMESTAMP,
			 Schema::TYPE_TIME,
			 Schema::TYPE_MONEY,
		 ]);
	}


	public function scaleRequired()
	{
			return $this->type && in_array($this->type, [Schema::TYPE_DECIMAL, Schema::TYPE_MONEY]);
	}


	public function beforeSave($insert)
	{
			if ($insert) $this->id = uniqid();

			if ($this->lengthRequired() === false) {
					if ($insert) unset($this->length); 
					else if (isset($this->length)) $this->length = null;
			}

			if ($this->precisionRequired() === false) {
					if ($insert) unset($this->precision); 
					else if (isset($this->precision)) $this->precision = null;
			}

			if ($this->scaleRequired() === false) {
					if ($insert) unset($this->scale); 
					else if (isset($this->scale)) $this->scale = null;
			}

			return parent::beforeSave($insert);
	}

}

<?php

namespace tunecino\builder\models;

use Yii;
use yii\helpers\ArrayHelper;


class Relationship extends \yii2tech\filedb\ActiveRecord
{
    const HAS_ONE = 'hasOne';
    const HAS_MANY = 'hasMany';

    private $_reversed;

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
            ['reversed', 'string'],
            [['entity_id', 'related_to'], 'string'],
            [['rel_type', 'reversed'], 'in', 'range' => array_keys($this->types)],
            [['rel_type', 'reversed'], 'in', 'range' => [self::HAS_MANY], 
                'when' => function($model) { return $model->entity_id === $model->related_to; }, 
                'message' => 'Many_to_Many is the only acceptable relationship type between 2 instances of the same model.'
            ],
            [['rel_type', 'reversed'], 'required'],
            [['entity_id', 'related_to'], 'required'],
            ['entity_id', 'exist', 'skipOnError' => true, 'targetClass' => Entity::className(), 'targetAttribute' => ['entity_id' => 'id']],
            ['related_to', 'exist', 'skipOnError' => true, 'targetClass' => Entity::className(), 'targetAttribute' => ['related_to' => 'id']],
            ['related_to', 'unique', 'targetAttribute' => ['related_to', 'entity_id'], 'comboNotUnique' => 'this relationship has already been defined.'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => '',
            'entity_id' => 'Entity Id',
            'related_to' => 'Relate To',
            'rel_type' => 'Relation Type',
        ];
    }

    public function getTypes()
    {
        return [
            self::HAS_ONE => self::HAS_ONE,
            self::HAS_MANY => self::HAS_MANY,
        ];
    }

    public function getInversedRelationLabels()
    {
        return [
            self::HAS_ONE => '<span class="reversed-rel-to">' . ($this->isNewRecord ? '' : $this->relatedTo->name) . '</span> ' . '<b>' . self::HAS_ONE . '</b> ' . ' <span class="reversed-name">'. $this->entity->name . '</span>',
            self::HAS_MANY =>'<span class="reversed-rel-to">' . ($this->isNewRecord ? '' : $this->relatedTo->name) . '</span> ' . '<b>' . self::HAS_MANY . '</b> ' . ' <span class="reversed-name">'. $this->entity->name . '</span>',
        ];
    }

    public function getReversed()
    {
        return ($this->isNewRecord or $this->_reversed) ? $this->_reversed : $this->reversedRelation->rel_type ;
    }

    public function setReversed($value)
    {
        $this->_reversed = $value;
    }

    public function isDuplicationOfSame()
    {
        return $this->entity_id === $this->related_to;
    }

    public function isManyToMany()
    {
        return $this->rel_type === self::HAS_MANY && $this->reversed === self::HAS_MANY;
    }

    public function getEntity()
    {
        return $this->hasOne(Entity::className(), ['id' => 'entity_id']);
    }

    public function getRelAttributes()
    {
        if ($this->ownRelAttributes) return $this->getOwnRelAttributes();
        // else: related attributes are then expected to be defined within reversed relation. 
        // to recheck: if empty array does the trick or an empty ActiveQuery instance to be returned instead.
        return $this->reversedRelation ? $this->reversedRelation->getOwnRelAttributes() : [];
    }

    public function getOwnRelAttributes()
    {
        return $this->hasMany(Attribute::className(), ['entity_id' => 'id']);
    }

    public function getRelatedTo()
    {
        return $this->hasOne(Entity::className(), ['id' => 'related_to']);
    }

    public function getReversedRelation()
    {
        return $this->hasOne(self::className(), ['related_to' => 'entity_id'])->andWhere(['entity_id' => $this->related_to]);
    }

    public function getMigrationField()
    {
        $field = null;
        if ($this->rel_type === self::HAS_ONE && $this->reversed) {
            $field = $this->reversedRelation->entity->name . '_id'
                   . ':foreignKey(' . $this->reversedRelation->entity->name . ')'
                   . ':integer:notNull';
        }
        return $field;
    }

    public function getJunctionFields()
    {
        return implode(',', array_filter(ArrayHelper::getColumn($this->ownRelAttributes, 'migrationField')));
    }


    public function beforeSave($insert)
    {
        if ($insert) {
            $this->id = $this->entity_id . '-' . $this->related_to ;
        }
        return parent::beforeSave($insert);
    }

    public function afterDelete()
    {
        $attributes = $this->relAttributes;
        foreach ($attributes as $attribute) {
            $attribute->delete();
        }
        $revModel = $this->reversedRelation;
        if ($revModel) $revModel->delete();
        return parent::afterDelete();
    }

}

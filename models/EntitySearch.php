<?php

namespace tunecino\builder\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use tunecino\builder\models\Entity;


class EntitySearch extends Entity
{
    public function rules()
    {
        return [
            [['name', 'schema_id'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Entity::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) return $dataProvider;

        $query->andFilterWhere([
            'schema_id' => $this->schema_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        
        return $dataProvider;
    }
}

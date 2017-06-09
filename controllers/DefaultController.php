<?php

namespace tunecino\builder\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;
use tunecino\builder\models\Schema;
use tunecino\builder\models\Entity;
use tunecino\builder\models\Attribute;
use tunecino\builder\models\Relationship;
use tunecino\builder\models\EntitySearch;


class DefaultController extends Controller
{
    public $layout = 'main';

    private $_ajaxOnlyActions = [
        'create-schema',
        'update-schema',
        'create-entity',
        'update-entity',
        'create-attribute',
        'update-attribute',
        'attribute-extra-fields',
        'create-relationship',
        'update-relationship',
        'get-commands',
        'std'
    ];

    
    public function beforeAction($action) {
        if (!parent::beforeAction($action)) return false;
        foreach ($this->_ajaxOnlyActions as $action) {
            if ($this->action->id === $action) {
                if (Yii::$app->request->isAjax === false)
                    throw new \yii\web\BadRequestHttpException();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            }
        }
        return true;
    }


    public function actionIndex()
    {
        $schemaProvider = new ActiveDataProvider(['query' => Schema::find()]);
        $schemaProvider->pagination = ['defaultPageSize' => 12];
        
        return $this->render('index', [
            'schemaProvider' =>  $schemaProvider,
            'schema' => new Schema(),
        ]);
    }


    public function actionView($id)
    {
        $entitySearchModel = new EntitySearch();
        $entitySearchModel->schema_id = $id;

        $entityProvider = $entitySearchModel->search(Yii::$app->request->queryParams);
        $entityProvider->pagination = ['defaultPageSize' => 10];

        $schema = Schema::findOne($id);
        if ($schema === null) throw new NotFoundHttpException('The requested page does not exist.');

        return $this->render('view', [
            'schema' => $schema,
            'entitySearchModel' => $entitySearchModel,
            'entityProvider' => $entityProvider,
            'entity' => new Entity(['schema_id' => $id]),
        ]);
    }


    public function actionViewEntity($id)
    {
        $entity = Entity::findOne($id);
        if ($entity === null) throw new NotFoundHttpException('The requested page does not exist.');

        $juctionAttributes = [];
        foreach ($entity->relationships as $relationship) {
            if ($relationship->isManyToMany()) {
                $juctionAttributes[$relationship->relatedTo->name] = [
                    'attribute' => new Attribute(['entity_id' => $relationship->id, 'scenario' => 'junction']),
                    'provider' => new ActiveDataProvider(['query' => $relationship->getRelAttributes()])
                ];
            }
        }

        return $this->render('entity/view', [
            'model' => $entity,
            'attributeProvider' => new ActiveDataProvider(['query' => $entity->getRelatedAttributes()]),
            'relationshipProvider' => new ActiveDataProvider(['query' => $entity->getRelationships()]),
            'juctionAttributes' => $juctionAttributes,
            'relationship' => new Relationship(['entity_id' => $id]),
            'attribute' => new Attribute(['entity_id' => $id]),
        ]);
    }


    public function actionCreateSchema()
    {
        $model = new Schema();
        return $this->_save_or_validate_schema($model);
    }


    public function actionUpdateSchema($id)
    {
        $model = Schema::findOne($id);
        return $this->_save_or_validate_schema($model);
    }


    private function _save_or_validate_schema($schema)
    {
        $data = Yii::$app->request->post();
        $ajaxValidation = isset($data['ajax']);
        $schema->load($data);

        $generators = [];
        foreach ($schema->loadForms(true) as $formName => $generator) {
            $generator->load($data);
            $generators[$formName] = $generator;
        }

        if ($ajaxValidation) return array_merge(
            ActiveForm::validate($schema),
            call_user_func_array('\yii\widgets\ActiveForm::validate',$generators)
        );

        if ($schema->save() === false) return;

        $savedGenerators = [];
        foreach ($generators as $generator) {
            $generator->schema_id = $schema->id;
            $savedGenerators[] = $generator->save();
        }

        return count(array_unique($savedGenerators)) === 1 && current($savedGenerators);
    }


    public function actionCreateEntity()
    {
        $model = new Entity();
        $model->load(Yii::$app->request->post());
        return isset(Yii::$app->request->post()['ajax']) ? ActiveForm::validate($model) : $model->save();
    }


    public function actionUpdateEntity($id)
    {
        $model = Entity::findOne($id);
        return $model && $model->load(Yii::$app->request->post()) && $model->save();
    }


    public function actionCreateAttribute()
    {
        $data = Yii::$app->request->post();
        $ajaxValidation = isset($data['ajax']);

        $entity_id = isset($data['Attribute']) && isset($data['Attribute']['entity_id']) ? $data['Attribute']['entity_id'] : null;
        $isJunction = count(explode('-', $entity_id)) === 2;

        $model = new Attribute();
        if ($isJunction) $model->scenario = Attribute::SCENARIO_JUNCTION;
        $model->load($data, 'Attribute');

        return $ajaxValidation ? ActiveForm::validate($model) : $model->save();
    }


    public function actionUpdateAttribute($id)
    {
        $data = Yii::$app->request->post();
        $ajaxValidation = isset($data['ajax']);

        $entity_id = isset($data['Attribute']) && isset($data['Attribute']['entity_id']) ? $data['Attribute']['entity_id'] : null;
        $isJunction = count(explode('-', $entity_id)) === 2;

        $model = Attribute::findOne($id);
        if ($isJunction) $model->scenario = Attribute::SCENARIO_JUNCTION;
        $model->load($data, 'Attribute');

        return $ajaxValidation ? ActiveForm::validate($model) : $model->save();
    }


    public function actionAttributeExtraFields($type)
    {
        $attribute = new Attribute(['type' => $type]);
        return [
            'lengthRequired' => $attribute->lengthRequired(),
            'precisionRequired' => $attribute->precisionRequired(),
            'scaleRequired' => $attribute->scaleRequired()
        ];
    }


    public function actionCreateRelationship()
    {
        $data = Yii::$app->request->post();
        $ajaxValidation = isset($data['ajax']);

        $model = new Relationship();
        $model->load($data, 'Relationship');

        if ($ajaxValidation) return ActiveForm::validate($model);

        if ($model->validate() && $model->isDuplicationOfSame() === false) {
            $revModel = new Relationship();
            $revModel->entity_id = $model->related_to;
            $revModel->related_to = $model->entity_id;
            $revModel->rel_type = $model->reversed;
            $revModel->reversed = $model->rel_type;
            return $revModel->save() && $model->save(false);
        }

        return $model->isDuplicationOfSame() && $model->save(false);
    }


    public function actionUpdateRelationship($id)
    {
        $data = Yii::$app->request->post();
        $ajaxValidation = isset($data['ajax']);
        
        $model = Relationship::findOne($id);
        $model->load($data, 'Relationship');

        if ($ajaxValidation) return ActiveForm::validate($model);

        $revModel = $model->reversedRelation;
        if ($revModel && $model->validate()) {
            $revModel->rel_type = $model->reversed;
            return $revModel->save() && $model->save(false);
        }
    }


    public function actionDeleteRelationship($id)
    {
        $model = Relationship::findOne($id);
        if ($model && Yii::$app->request->method === 'POST') {
            $model->delete();
            return $this->actionViewEntity($model->entity_id);
        }
    }


    public function actionDeleteAttribute($id, $entity_id = null)
    {
        $model = Attribute::findOne($id);
        if ($model && Yii::$app->request->method === 'POST') {
            $model->delete();
            return $this->actionViewEntity($entity_id ?: $model->entity_id);
        }
    }


    public function actionDeleteEntity($id)
    {
        $model = Entity::findOne($id);
        if ($model && Yii::$app->request->method === 'POST') {
            $model->delete();
            return $this->redirect(['view', 'id' => $model->schema_id]);
        }
    }


    public function actionDelete($id)
    {
        $model = Schema::findOne($id);
        if ($model && Yii::$app->request->method === 'POST') {
            $model->delete();
            return $this->redirect(['index']);
        }
    }


    public function actionStd()
    {
        $post = Yii::$app->request->post();
        list ($status, $output) = $this->runConsole($post['cmd']);
        return $output;
    }


    private function runConsole($command)
    {
        // source: https://github.com/samdark/yii2-webshell/blob/master/controllers/DefaultController.php
        $cmd = Yii::getAlias($this->module->yiiScript) . ' ' . $command . ' 2>&1';
        $handler = popen($cmd, 'r');
        $output = '';
        while (!feof($handler)) {
            $output .= fgets($handler);
        }
        $output = trim($output);
        $status = pclose($handler);
        return [$status, $output];
    }


    public function actionGetCommands($id)
    {
        $schema = Schema::findOne($id);
        if ($schema === null) throw new NotFoundHttpException('The requested page does not exist.');
        return $schema->consoleCommands;
    }


}

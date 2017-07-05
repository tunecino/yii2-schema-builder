<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    public $modelClass = '<?= ltrim($generator->modelClass, '\\') ?>';
    <?php if (!empty($generator->searchModelClass)): ?>

        /**
         * @inheritdoc
         */
        public function actions()
        {
            $actions = parent::actions();

            $actions['index'] = [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'prepareDataProvider' => function () {
                    $searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : StringHelper::basename($generator->searchModelClass) ?>;
                    return $searchModel->search(Yii::$app->request->queryParams, '');
                },
            ];

            return $actions;
        }
    <?php endif; ?>

}

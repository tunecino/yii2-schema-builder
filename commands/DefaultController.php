<?php

namespace tunecino\builder\commands;
 
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;
 

class DefaultController extends Controller
{

    public function beforeAction($action) {
        echo 'Command provided by ' . Yii::$app->controller->module->id . ' extension'. PHP_EOL. PHP_EOL;
        return parent::beforeAction($action);
    }


    public function actionRemoveDirectory($path)
    {
        $path = Yii::getAlias($path) ?: $path;
    	FileHelper::removeDirectory($path);
		echo '"' . $path .'" has been removed.'. PHP_EOL;
    }


    public function actionDropAllTables($db)
    {
        $db = Yii::$app->get($db);
        $tables = $db->schema->getTableNames();

        if (!$tables) {
            echo 'nothing to drop.'. PHP_EOL;
            return 0;
        }

        foreach ($tables as $table) {
            $db->createCommand()->dropTable($table)->execute();
            echo 'dropped table "' . $table . '".'. PHP_EOL;
        }

        echo PHP_EOL. 'database should be empty now.' . PHP_EOL;
    }
}
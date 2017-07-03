<?php

namespace tunecino\builder;

use Yii;
use yii\base\BootstrapInterface;
use yii\helpers\FileHelper;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;


class Module extends \yii\base\Module implements BootstrapInterface
{
    public $dataPath;
    public $commands = [];
    public $previewUrlCallback;
    public $yiiScript = '@app/yii';
    public $allowedIPs = ['127.0.0.1', '::1'];
    public $controllerNamespace = 'tunecino\builder\controllers';

    private $_reserved_data_files = ['Attribute', 'Entity', 'Relationship', 'Schema'];


    public function init()
    {
        parent::init();

        if (Yii::$app instanceof \yii\console\Application) $this->controllerNamespace = 'tunecino\builder\commands';
        else {
            // instanceof webapp
            if ($this->checkAccess() === false)
                throw new ForbiddenHttpException('You are not allowed to access this page.');
            // unset default bootstrap js file as a it is already included into Flat-UI code (flat-ui.js)
            Yii::$app->assetManager->bundles['yii\\bootstrap\\BootstrapPluginAsset'] = [ 'js' => [] ];
        }

        $this->setComponents([
            'filedb' => [
                'class' => 'yii2tech\filedb\Connection',
                'path' => $this->filedbPath,
            ]
        ]);

        if (empty($this->commands)) $this->commands = $this->coreCommands();
        $this->prepareDataFiles();
    }


    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules([
            [
                'class' => 'yii\web\UrlRule',
                'route' => $this->id,
                'pattern' => $this->id,
            ],
            [
                'class' => 'yii\web\UrlRule',
                'route' => $this->id . '/<controller>/<action>',
                'pattern' => $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>',
            ]
        ], false);
    }


    public function getFiledbPath()
    {
        if ($this->dataPath) return Yii::getAlias($this->dataPath) ?: $this->dataPath;
        return Yii::$app->getRuntimePath() . '/schema-builder/data';
    }


    protected function coreCommands()
    {
        return [
            ['class' => 'tunecino\builder\generators\migration\Generator'],
            ['class' => 'tunecino\builder\generators\module\Generator'],
            ['class' => 'tunecino\builder\generators\model\Generator'],
            ['class' => 'tunecino\builder\generators\crud\Generator']
        ];
    }


    protected function checkAccess()
    {
        $ip = Yii::$app->getRequest()->getUserIP();
        foreach ($this->allowedIPs as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }
        Yii::warning('Access to Gii is denied due to IP address restriction. The requested IP is ' . $ip, __METHOD__);
        return false;
    }


    protected function prepareDataFiles()
    {
        $file_names = $existing_file_names = [];
        if (is_dir($this->filedbPath) === false) FileHelper::createDirectory($this->filedbPath);

        foreach ($this->commands as $cmd) {
            if (is_array($cmd)) {
                $model = Yii::createObject($cmd);
                if ($model instanceof \tunecino\builder\Generator === false)
                    throw new InvalidConfigException('generator should extend "\tunecino\builder\Generator"');

                $fileName = $model->getName();
                if (in_array($fileName, array_map('strtolower', $this->_reserved_data_files)))
                    throw new InvalidConfigException('"'.$fileName.'" is internally used by this extension. Try using a different name for your generator.');

                $file_names[] = $fileName;
            }
        }

        $file_names = array_merge($this->_reserved_data_files, array_map('ucfirst', $file_names));
        $existingFiles = FileHelper::findFiles($this->filedbPath, ['only' => ['*.php'], 'recursive' => false]);

        foreach ($existingFiles as $path) {
            $existing_file_names[] = pathinfo($path)['filename'];
        }

        foreach ($file_names as $name) {
            if (in_array(strtolower($name), array_map('strtolower', $existing_file_names)) === false) {
                $file = $this->filedbPath .'/'. $name . '.php';
                $content = '<?php' . PHP_EOL . PHP_EOL . 'return [];';
                if (file_put_contents($file, $content) === false) {
                    throw new InvalidConfigException("Unable to write the file '{$file}'.");
                }
            }
        }
    }

}

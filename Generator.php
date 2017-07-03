<?php

namespace tunecino\builder;

use Yii;
use ReflectionClass;
use yii\helpers\Inflector;
use tunecino\builder\models\Schema;


abstract class Generator extends \yii2tech\filedb\ActiveRecord
{
    public $defaultAttributes = [];
    public $baseAttributes = [
        'appconfig' => null,
        'template' => 'default',
        'enableI18N' => false,
        'messageCategory' => 'app'
    ];


    public function init()
    {
        parent::init();
        $this->loadDefaultValues();
    }


    abstract public function getConsoleCommands();


    public static function getDb()
    {
        return \tunecino\builder\Module::getInstance()->get('filedb');
    }


    public function attributes()
    {
        return array_merge(
            array_keys($this->baseAttributes),
            array_keys($this->getCoreAttributes()), 
            array_keys($this->defaultAttributes),
            ['id','schema_id']
        );
    }


    public function getCoreAttributes()
    {
        return [];
    }


    public function loadDefaultValues()
    {
        $defaultValues = array_merge($this->baseAttributes, $this->getCoreAttributes(), $this->defaultAttributes);
        $this->setAttributes($defaultValues, false);
    }


    public static function getName()
    {
        $file = (new ReflectionClass(static::class))->getFileName();
        return basename(dirname($file));
    }


    public static function fileName()
    {
        return Inflector::camelize(static::getName());
    }


    public function formName()
    {
        return static::fileName();
    }


    public function getSchema()
    {
        return $this->hasOne(Schema::className(), ['id' => 'schema_id']);
    }


    public static function formView()
    {
        $class = new ReflectionClass(static::class);
        return dirname($class->getFileName()) . '/form.php';
    }


    public function rules()
    {
        return [
            [['appconfig','template'], 'filter', 'filter' => 'trim'],
            ['appconfig', 'validateFilePath'],
            ['template', 'required', 'message' => 'A code template must be selected.'],
            ['enableI18N', 'boolean'],
            ['template', 'string'],
            ['messageCategory', 'validateMessageCategory', 'skipOnEmpty' => false],
        ];
    }


    public function validateDb()
    {
        if (!Yii::$app->has($this->db)) {
            $this->addError('db', 'There is no application component named "db".');
        } elseif (!Yii::$app->get($this->db) instanceof \yii\db\Connection) {
            $this->addError('db', 'The "db" application component must be a DB connection instance.');
        }
    }


    public function validateFilePath($attribute, $params)
    {
        $file = $this->$attribute;
        $path = Yii::getAlias($file, false);
        if (substr($path, -4) !== '.php') $this->addError($attribute, "A php file is expected.");
        if ($path === false or !is_file($path)) {
            $this->addError($attribute, "File '$file' does not exist or has syntax error.");
        }
    }


    public function validateMessageCategory()
    {
        if ($this->enableI18N && empty($this->messageCategory)) {
            $this->addError('messageCategory', "Message Category cannot be blank.");
        }
    }


    public function validateNamespace($attribute)
    {
        $value = $this->$attribute;
        $value = ltrim($value, '\\');
        $path = Yii::getAlias('@' . str_replace('\\', '/', $value), false);
        if ($path === false) {
            $this->addError($attribute, 'Namespace must be associated with an existing directory.');
        }
    }


    public function validateClass($attribute, $params)
    {
        $class = $this->$attribute;
        try {
            if (class_exists($class)) {
                if (isset($params['extends'])) {
                    if (ltrim($class, '\\') !== ltrim($params['extends'], '\\') && !is_subclass_of($class, $params['extends'])) {
                        $this->addError($attribute, "'$class' must extend from {$params['extends']} or its child class.");
                    }
                }
            } else {
                $this->addError($attribute, "Class '$class' does not exist or has syntax error.");
            }
        } catch (\Exception $e) {
            $this->addError($attribute, "Class '$class' does not exist or has syntax error.");
        }
    }


    public function attributeLabels()
    {
        return [
            'id' => 'Primary Key',
            'schema_id' => 'Schema Id',
            'appconfig' => 'Application Config File',
            'enableI18N' => 'Enable I18N',
            'messageCategory' => 'Message Category',
            'template' => 'template',
        ];
    }


    public function attributeHints()
    {
        return [
            'enableI18N' => 'This indicates whether the generator should generate strings using <code>Yii::t()</code> method.
                Set this to <code>true</code> if you are planning to make your application translatable.',
            'messageCategory' => 'This is the category used by <code>Yii::t()</code> in case you enable I18N.',
            'appconfig' => 'Custom application configuration file path. If not set, default application configuration is used.',
            'template' => 'the name of the code template that the user has selected.',
        ];
    }


    public function beforeSave($insert)
    {
        if ($insert) $this->id = uniqid();
        return parent::beforeSave($insert);
    }

}

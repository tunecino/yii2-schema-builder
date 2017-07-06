<?php

namespace tunecino\builder\generators\migration;

use Yii;

class Generator extends \tunecino\builder\Generator
{
	private $_toJunction = [];

	private function createdJunction($rel1, $rel2)
    {
        return (isset($this->_toJunction[$rel2]) && $this->_toJunction[$rel2] === $rel1)
            or (isset($this->_toJunction[$rel1]) && $this->_toJunction[$rel1] === $rel2);
    }

    public function getCoreAttributes()
    {
        return [
            'db' => 'db',
            'migrationTable' => '{{%migration}}',
            'migrationPath' => '@runtime/schema-builder/migrations',
            'templateFile' => '@yii/views/migration.php',
            'useTablePrefix' => false,
        ];
    }

	public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'db' => 'Migration Database',
            'migrationTable' => 'Migration Table',
            'migrationPath' => 'Migration Path',
            'templateFile' => 'Template File',
            'useTablePrefix' => 'Use Table Prefix',
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
        	[['db'], 'required'],
            [['db'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['db'], 'validateDb'],
            [['migrationTable', 'migrationPath'], 'string'], /*could be improved*/
            [['templateFile'], 'validateFilePath'],
            [['useTablePrefix'], 'boolean'],
        ]);
    }

    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            'db' => 'This is the ID of the DB application component that will be used by the migration tool.',
            'migrationTable' => 'The name of the table for keeping applied migration information.',
        	'migrationPath' => 'The directory containing the migration classes. This can be either a path alias or a directory path.',
            'useTablePrefix' => 'Indicates whether the table names generated should consider the <code>tablePrefix</code> setting of the DB connection. For example, if the table name is <code>post</code> the generator wil return <code>{{%post}}</code>.'
        ]);
    }
    

    public function getConsoleCommands()
    {
        return array_merge(
            $this->getMigrationClearCommands(),
            $this->getMigrationCreateCommands()
        );
    }


    public function getMigrationFolder()
    {
        $path = Yii::getAlias($this->migrationPath) ?: $this->migrationPath;
        return $this->migrationPath . '/' . $this->schema->name;
    }


    /**
     * @return array
     */
    protected function getMigrationClearCommands()
    {
        $migDownCmd = 'yii migrate/down all';
        if ($this->appconfig) $migDownCmd .= ' --appconfig="'.$this->appconfig.'"';
        if ($this->db) $migDownCmd .= ' --db="'.$this->db.'"';
        if ($this->migrationPath) $migDownCmd .= ' --migrationPath="'.$this->migrationFolder.'"';
        if ($this->migrationTable) $migDownCmd .= ' --migrationTable="'.$this->migrationTable.'"';
        $migDownCmd .= ' --interactive=0';

        $dropDbCmd = 'yii '. Yii::$app->controller->module->id . '/helpers/drop-all-tables';
        if ($this->db) $dropDbCmd .= ' '.$this->db;

        $flushDbCmd = 'yii cache/flush-schema ' . $this->db;
        if ($this->appconfig) $flushDbCmd .= ' --appconfig="'.$this->appconfig.'"';
        $flushDbCmd .= ' --interactive=0';

        $rmvDirectoryCmd = 'yii '. Yii::$app->controller->module->id . '/helpers/remove-directory';
        if ($this->migrationPath) $rmvDirectoryCmd .= ' '.$this->migrationFolder;

        return (file_exists($this->migrationFolder))
            ? [$migDownCmd, $dropDbCmd, $flushDbCmd, $rmvDirectoryCmd]
            : [$dropDbCmd, $flushDbCmd, $rmvDirectoryCmd];
    }


    protected function getMigrationCreateCommands()
    {
        $dbIndexFree = $dbIndexRequired = $junction = [];

        foreach ($this->schema->entities as $entity) {
            $cmd = 'yii migrate/create create_'.$entity->name.'_table';
            if ($entity->migrationFields) $cmd .= ' --fields="'.$entity->migrationFields.'"';
            if ($this->appconfig) $cmd .= ' --appconfig="'.$this->appconfig.'"';
            if ($this->db) $cmd .= ' --db="'.$this->db.'"';
            if ($this->migrationPath) $cmd .= ' --migrationPath="'.$this->migrationFolder.'"';
            if ($this->migrationTable) $cmd .= ' --migrationTable="'.$this->migrationTable.'"';
            if ($this->templateFile) $cmd .= ' --templateFile="'.$this->templateFile.'"';
            if ($this->useTablePrefix) $cmd .= ' --useTablePrefix=1';
            $cmd .= ' --interactive=0';

            if ($entity->isHoldingForeignKey()) $dbIndexRequired[] = $cmd;
            else $dbIndexFree[] = $cmd;

            foreach ($entity->relationships as $relation) {
                if ($relation->isManyToMany() && $this->createdJunction($entity->name, $relation->relatedTo->name) === false) {
                    $this->_toJunction[$relation->relatedTo->name]= $entity->name;
                    $jcmd = 'yii migrate/create create_junction_table_for_'.$entity->name.'_and_'.$relation->relatedTo->name.'_tables';
                    if ($relation->ownRelAttributes) $jcmd .= ' --fields="'.$relation->junctionFields.'"';
                    if ($this->appconfig) $jcmd .= ' --appconfig="'.$this->appconfig.'"';
                    if ($this->db) $jcmd .= ' --db="'.$this->db.'"';
                    if ($this->migrationPath) $jcmd .= ' --migrationPath="'.$this->migrationFolder.'"';
                    if ($this->migrationTable) $jcmd .= ' --migrationTable="'.$this->migrationTable.'"';
                    if ($this->templateFile) $jcmd .= ' --templateFile="'.$this->templateFile.'"';
                    if ($this->useTablePrefix) $jcmd .= ' --useTablePrefix=1';
                    $jcmd .= ' --interactive=0';

                    $junction[] = $jcmd;
                }
            }
        }

        $commands = array_merge($dbIndexFree, $dbIndexRequired, $junction);

        $upcmd = 'yii migrate/up';
        if ($this->migrationPath) $upcmd .= ' --migrationPath="'.$this->migrationFolder.'"';
        if ($this->migrationTable) $upcmd .= ' --migrationTable="'.$this->migrationTable.'"';
        $upcmd .= ' --interactive=0';
        $commands[] = $upcmd;
        
        return $commands;
    }
   
}

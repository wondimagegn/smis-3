<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Backups Table
 */
class BackupsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('backups');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    /**
     * Retrieves the latest backups with file existence check
     *
     * @param int $limit Number of backups to retrieve
     * @return array List of backups
     */
    public function getLatestBackups(int $limit = 10)
    {
        $backups = $this->find()
            ->order(['Backups.created' => 'DESC'])
            ->limit($limit)
            ->toArray();

        foreach ($backups as $backup) {
            $backup->file_exists = file_exists($backup->location . DIRECTORY_SEPARATOR . $backup->name);
        }

        return $backups;
    }
}

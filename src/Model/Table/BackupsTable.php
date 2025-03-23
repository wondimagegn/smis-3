<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

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
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 250)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->integer('size')
            ->requirePresence('size', 'create')
            ->notEmptyString('size');

        $validator
            ->scalar('mime')
            ->maxLength('mime', 100)
            ->requirePresence('mime', 'create')
            ->notEmptyString('mime');

        $validator
            ->scalar('operation_type')
            ->maxLength('operation_type', 10)
            ->requirePresence('operation_type', 'create')
            ->notEmptyString('operation_type');

        $validator
            ->scalar('location')
            ->requirePresence('location', 'create')
            ->notEmptyString('location');

        $validator
            ->boolean('backup_taken')
            ->requirePresence('backup_taken', 'create')
            ->notEmptyString('backup_taken');

        $validator
            ->dateTime('first_backup_taken_date')
            ->allowEmptyDateTime('first_backup_taken_date');

        $validator
            ->dateTime('last_backup_taken_date')
            ->allowEmptyDateTime('last_backup_taken_date');

        return $validator;
    }
    function getLatestBackups($limit = 10)
    {
        $backups = $this->find('all', array('order' => array('Backup.created' => 'DESC'), 'limit' => $limit));

        if (!empty($backups)) {
            foreach ($backups as &$backup) {
                if (file_exists($backup['Backup']['location'] . DS . $backup['Backup']['name'])) {
                    $backup['Backup']['file_exists'] = true;
                } else {
                    $backup['Backup']['file_exists'] = false;
                }
            }
        }
        return $backups;
    }
}

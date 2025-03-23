<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class AttachmentsTable extends Table
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

        $this->setTable('attachments');
        $this->setDisplayField('id');
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
            ->scalar('id')
            ->maxLength('id', 36)
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('model')
            ->maxLength('model', 255)
            ->requirePresence('model', 'create')
            ->notEmptyString('model');

        $validator
            ->scalar('foreign_key')
            ->maxLength('foreign_key', 36)
            ->requirePresence('foreign_key', 'create')
            ->notEmptyString('foreign_key');

        $validator
            ->scalar('dirname')
            ->maxLength('dirname', 255)
            ->allowEmptyString('dirname');

        $validator
            ->scalar('basename')
            ->maxLength('basename', 255)
            ->requirePresence('basename', 'create')
            ->notEmptyString('basename');

        $validator
            ->scalar('checksum')
            ->maxLength('checksum', 255)
            ->requirePresence('checksum', 'create')
            ->notEmptyString('checksum');

        $validator
            ->scalar('group')
            ->maxLength('group', 255)
            ->allowEmptyString('group');

        $validator
            ->scalar('alternative')
            ->maxLength('alternative', 50)
            ->allowEmptyString('alternative');

        return $validator;
    }

    public function emptyTable()
    {
        $table = $this->tablePrefix . $this->table;
        $result = $this->query("TRUNCATE $table");
        //$this->setDataSource('default');
        return $result;
    }
}

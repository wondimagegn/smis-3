<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class OfficialRequestStatusesTable extends Table
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

        $this->setTable('official_request_statuses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('OfficialTranscriptRequests', [
            'foreignKey' => 'official_transcript_request_id',
            'joinType' => 'INNER',
        ]);
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
            ->scalar('status')
            ->maxLength('status', 200)
            ->requirePresence('status', 'create')
            ->notEmptyString('status');

        $validator
            ->scalar('remark')
            ->maxLength('remark', 255)
            ->requirePresence('remark', 'create')
            ->notEmptyString('remark');

        return $validator;
    }

}

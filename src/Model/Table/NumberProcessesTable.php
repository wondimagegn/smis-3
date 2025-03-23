<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class NumberProcessesTable extends Table
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

        $this->setTable('number_processes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
    }

    public function recoredAsRunning($user_id = null, $initiated_by = null)
    {

        $check_record_is_existed = $this->find(
            'count',
            array('conditions' => array('NumberProcess.user_id' => $user_id))
        );
        if ($check_record_is_existed > 0) {
        } else {
            $data['NumberProcess']['user_id'] = $user_id;
            $data['NumberProcess']['initiated_by'] = $initiated_by;
            $this->save($data);
        }
    }

    public function jobDoneDelete($user_id = null)
    {

        $processRunning = $this->find('first', array(
            'conditions' =>
                array('NumberProcess.user_id' => $user_id),
            'recursive' => -1
        ));

        $this->delete($processRunning['NumberProcess']['id']);
    }
}

<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ExamRoomConstraintsTable extends Table
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

        $this->setTable('exam_room_constraints');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ClassRooms', [
            'foreignKey' => 'class_room_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('academic_year')
            ->maxLength('academic_year', 9)
            ->allowEmptyString('academic_year');

        $validator
            ->scalar('semester')
            ->maxLength('semester', 3)
            ->allowEmptyString('semester');

        $validator
            ->date('exam_date')
            ->allowEmptyDate('exam_date');

        $validator
            ->allowEmptyString('session');

        $validator
            ->boolean('active')
            ->allowEmptyString('active');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {

        $rules->add($rules->existsIn(['class_room_id'], 'ClassRooms'));

        return $rules;
    }


    public function get_already_recorded_exam_room_constraint($class_room_id = null)
    {

        if (!empty($class_room_id)) {
            $examRoomConstraints = $this->find(
                'all',
                array(
                    'conditions' => array('ExamRoomConstraint.class_room_id' => $class_room_id),
                    'order' => array('ExamRoomConstraint.exam_date', 'ExamRoomConstraint.session'),
                    'recursive' => -1
                )
            );
            $exam_room_constraints_by_date = array();
            foreach ($examRoomConstraints as $examRoomConstraint) {
                $exam_room_constraints_by_date[$examRoomConstraint['ExamRoomConstraint']['exam_date']][$examRoomConstraint['ExamRoomConstraint']['session']]['id'] = $examRoomConstraint['ExamRoomConstraint']['id'];
                $exam_room_constraints_by_date[$examRoomConstraint['ExamRoomConstraint']['exam_date']][$examRoomConstraint['ExamRoomConstraint']['session']]['active'] = $examRoomConstraint['ExamRoomConstraint']['active'];
            }
            return $exam_room_constraints_by_date;
        }
    }

    public function is_class_room_used($id = null)
    {

        $count = $this->find(
            'count',
            array('conditions' => array('ExamRoomConstraint.class_room_id' => $id), 'limit' => 2)
        );
        return $count;
    }
}

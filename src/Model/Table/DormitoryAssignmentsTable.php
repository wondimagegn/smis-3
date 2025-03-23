<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DormitoryAssignmentsTable extends Table
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

        $this->setTable('dormitory_assignments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Dormitories', [
            'foreignKey' => 'dormitory_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
        ]);
        $this->belongsTo('AcceptedStudents', [
            'foreignKey' => 'accepted_student_id',
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
            ->date('leave_date')
            ->allowEmptyDate('leave_date');

        $validator
            ->boolean('received')
            ->notEmptyString('received');

        $validator
            ->date('received_date')
            ->allowEmptyDate('received_date');

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
        $rules->add($rules->existsIn(['dormitory_id'], 'Dormitories'));
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['accepted_student_id'], 'AcceptedStudents'));

        return $rules;
    }
    //get free space (block capacity) of a given block.
    function get_block_capacity($dormitories_data = null)
    {
        $total_capacity = 0;
        $free_capacity = 0;
        foreach ($dormitories_data as $dormitory_data) {
            $occupied_dorm_count = $this->find('count', array('conditions' => array('DormitoryAssignment.dormitory_id' => $dormitory_data['Dormitory']['id'], 'DormitoryAssignment.leave_date' => null)));
            $free_capacity = $free_capacity + ($dormitory_data['Dormitory']['capacity'] - $occupied_dorm_count);
            $total_capacity = $total_capacity + $dormitory_data['Dormitory']['capacity'];
        }
        $capacity = array();
        $capacity['free_capacity'] = $free_capacity;
        $capacity['total_capacity'] = $total_capacity;
        return $capacity;
    }
    //get free space of a given dormitory
    function get_free_dormitory_space($selected_dormitory = null)
    {
        $occupied_dorm_count = $this->find('count', array('conditions' => array('DormitoryAssignment.dormitory_id' => $selected_dormitory['Dormitory']['id'], 'DormitoryAssignment.leave_date' => null)));
        $free_dormitory_space = $selected_dormitory['Dormitory']['capacity'] - $occupied_dorm_count;

        return $free_dormitory_space;
    }
    //Get student_id that have dormitory in there name
    function get_student_have_dormitory()
    {
        $student_ids = $this->find('list', array('fields' => array('DormitoryAssignment.student_id'), 'conditions' => array('DormitoryAssignment.leave_date' => null, 'NOT' => array('DormitoryAssignment.student_id' => null))));
        return $student_ids;
    }
    //Get accepted_student_id that have dormitory in there name
    function get_accepted_student_have_dormitory()
    {
        $accepted_student_ids = $this->find('list', array('fields' => array('DormitoryAssignment.accepted_student_id'), 'conditions' => array('DormitoryAssignment.leave_date' => null, "NOT" => array('DormitoryAssignment.accepted_student_id' => null))));
        return $accepted_student_ids;
    }

    //Get user assigned dormitory blocks
    function get_assigned_dormitory_blocks($user_id = null)
    {
        $dormitory_block_ids = ClassRegistry::init('UserDormAssignment')->find('list', array('fields' => array('UserDormAssignment.dormitory_block_id', 'UserDormAssignment.dormitory_block_id'), 'conditions' => array('UserDormAssignment.user_id' => $user_id)));
        return $dormitory_block_ids;
    }

    //Check this dormitory is ever been used in dormitory assignment or not
    function is_dormitory_ever_used($dormitory_id = null)
    {
        if (!empty($dormitory_id)) {
            $count = 0;
            $count = $this->find('count', array('conditions' => array('DormitoryAssignment.dormitory_id' => $dormitory_id), 'limit' => 2));
            if ($count == 0) {
                return false;
            } else {
                return true;
            }
        }
    }

    function takeNDormAndReturn($student_id = null, $request_date = null)
    {
        $check = $this->find('count', array(
            'conditions' => array(
                'DormitoryAssignment.leave_date is null',
                'DormitoryAssignment.received = 1',
                'DormitoryAssignment.student_id' => $student_id
            )
        ));
        return $check;
    }


    public function getStudentAssignedDormitory($student_id = null, $request_date = null)
    {
        $student = $this->find('first', array(
            'conditions' => array(
                'DormitoryAssignment.leave_date is null ',

                'DormitoryAssignment.student_id' => $student_id
            ),
            'order' => array('DormitoryAssignment.created' => 'DESC'),
            'contain' => array('Dormitory' => array('DormitoryBlock' => array('Campus')))
        ));

        return $student;
    }


    public function getDormitoryAssignmentForSMS($phoneNumber)
    {
        $studentDetail = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.phone_mobile' => $phoneNumber), 'contain' => array('User')));
        //return $studentDetail;
        if (!empty($studentDetail)) {
            $dormAssignment = $this->find('first', array(
                'conditions' => array(
                    'DormitoryAssignment.leave_date is null ',
                    'DormitoryAssignment.received=1',
                    'DormitoryAssignment.student_id' => $studentDetail['Student']['id']
                ),
                'contain' => array('Student', 'Dormitory' => array('DormitoryBlock' => array('Campus')))
            ));
            //return $dormAssignment;
            //return $mostRecentStatus;
            return $this->formateDormForSMS($dormAssignment);
        } else {
            // parent phone number ? what if the parent has more than one child ?
            $parentPhone = ClassRegistry::init('Contact')->find('all', array('conditions' => array('Contact.phone_mobile' => $phoneNumber), 'contain' => array('Student', 'AcademicStatus')));

            if (!empty($parentPhone)) {
                $allofTheirKids = 'Your child ';
                foreach ($parentPhone as $k => $pv) {
                    $dormAssignment = $this->find('first', array(
                        'conditions' => array(
                            'DormitoryAssignment.leave_date is null ',
                            'DormitoryAssignment.received = 1',
                            'DormitoryAssignment.student_id' => $pv['Student']['id']
                        ),
                        'contain' => array('Student', 'Dormitory' => array('DormitoryBlock' => array('Campus')))
                    ));

                    $allofTheirKids .= $this->formateDormForSMS($dormAssignment);
                }
                return $allofTheirKids;
            }
        }
        return "You dont have the privilage to view student dorm.";
    }

    public function formateDormForSMS($dormAssignment)
    {
        $display = '';
        if (!empty($dormAssignment)) {
            $display .= $dormAssignment['Student']['first_name'] . ' ' . $dormAssignment['Student']['last_name'] . '(' . $dormAssignment['Student']['studentnumber'] . ') is in block:' . $dormAssignment['Dormitory']['DormitoryBlock']['block_name'] . 'floor:' . $dormAssignment['Dormitory']['floor'] . ' Room:' . $dormAssignment['Dormitory']['dorm_number'] . ' Capacity:' . $dormAssignment['Dormitory']['capacity'];
            return $display;
        }
        return "no dormitory assignment.";
    }
}

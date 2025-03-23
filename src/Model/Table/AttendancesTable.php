<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AttendancesTable extends Table
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

        $this->setTable('attendances');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
            'propertyName' => 'Student',
        ]);
        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'INNER',
            'propertyName' => 'PublishedCourse',
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
            ->scalar('attendace_type')
            ->maxLength('attendace_type', 10)
            ->requirePresence('attendace_type', 'create')
            ->notEmptyString('attendace_type');

        $validator
            ->date('attendance_date')
            ->requirePresence('attendance_date', 'create')
            ->notEmptyDate('attendance_date');

        $validator
            ->boolean('attendance')
            ->notEmptyString('attendance');

        $validator
            ->scalar('remark')
            ->allowEmptyString('remark');

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

        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['published_course_id'], 'PublishedCourses'));

        return $rules;
    }

    public function getListOfDateAttendanceTaken($published_course_id = null)
    {

        $attendance_dates = $this->find(
            'list',
            array(
                'conditions' =>
                    array(
                        'Attendance.published_course_id' => $published_course_id
                    ),
                'fields' => array('attendance_date'),
                'recursive' => -1,
                'order' => array('Attendance.attendance_date ASC')
            )
        );
        $attendance_dates = array_unique($attendance_dates);
        $attendance_dates_formatted = array();
        foreach ($attendance_dates as $key => $value) {
            $attendance_dates_formatted[$value] = date(
                'D M d, Y',
                mktime(
                    substr($value, 11, 2),
                    substr($value, 14, 2),
                    substr($value, 17, 2),
                    substr($value, 5, 2),
                    substr($value, 8, 2),
                    substr($value, 0, 4)
                )
            );
        }
        return $attendance_dates_formatted;
    }

    public function getCourseAttendanceDetail(
        $published_course_id = null,
        $attendance_start_date = null,
        $attendance_end_date = null,
        $student_registers,
        $student_adds
    ) {

        foreach ($student_registers as $key => $student_register) {
            $attendance_detail = $this->find(
                'all',
                array(
                    'conditions' =>
                        array(
                            'Attendance.published_course_id' => $published_course_id,
                            'Attendance.attendance_date >=' => $attendance_start_date,
                            'Attendance.attendance_date <=' => $attendance_end_date,
                            'Attendance.student_id' => $student_register['Student']['id']
                        ),
                    'order' => array('Attendance.attendance_date ASC'),
                    'recursive' => -1
                )
            );
            if (!empty($attendance_detail)) {
                $student_registers[$key]['Attendance'] = $attendance_detail;
            } else {
                $student_registers[$key]['Attendance'] = array();
            }
        }
        foreach ($student_adds as $key => $student_add) {
            $attendance_detail = $this->find(
                'all',
                array(
                    'conditions' =>
                        array(
                            'Attendance.published_course_id' => $published_course_id,
                            'Attendance.attendance_date >=' => $attendance_start_date,
                            'Attendance.attendance_date <=' => $attendance_end_date,
                            'Attendance.student_id' => $student_add['Student']['id']
                        ),
                    'order' => array('Attendance.attendance_date ASC'),
                    'recursive' => -1
                )
            );
            if (!empty($attendance_detail)) {
                $student_adds[$key]['Attendance'] = $attendance_detail;
            } else {
                $student_adds[$key]['Attendance'] = array();
            }
        }

        $course_attendance['register'] = $student_registers;
        $course_attendance['add'] = $student_adds;

        return $course_attendance;
    }

    // Check whether the course schedule used in attendance or not
    public function is_course_schedule_uesd_in_attendance($course_schedule_published_course_ids = null)
    {

        $count = $this->find(
            'count',
            array(
                'conditions' => array('Attendance.published_course_id' => $course_schedule_published_course_ids),
                'limit' => 2
            )
        );
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
}

<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ExamPeriodsTable extends Table
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

        $this->setTable('exam_periods');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('YearLevels', [
            'foreignKey' => 'year_level_id',
        ]);
        $this->hasMany('ExamExcludedDateAndSessions', [
            'foreignKey' => 'exam_period_id',
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
            ->allowEmptyString('default_number_of_invigilator_per_exam');

        $validator
            ->date('start_date')
            ->allowEmptyDate('start_date');

        $validator
            ->date('end_date')
            ->allowEmptyDate('end_date');

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

        $rules->add($rules->existsIn(['college_id'], 'Colleges'));
        $rules->add($rules->existsIn(['program_id'], 'Programs'));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));
        $rules->add($rules->existsIn(['year_level_id'], 'YearLevels'));

        return $rules;
    }

    public function compareenddatewithtoday()
    {

        if ($this->data['ExamPeriod']['end_date'] >= date("Y-m-d")) {
            return true;
        }
        return false;
    }

    public function comparestartdatewithtoday()
    {

        if ($this->data['ExamPeriod']['start_date'] >= date("Y-m-d")) {
            return true;
        }
        return false;
    }

    public function field_comparison($check1, $operator, $field2)
    {

        foreach ($check1 as $key => $value1) {
            $value2 = $this->data[$this->alias][$field2];
            if (!Validation::comparison($value1, $operator, $value2)) {
                return false;
            }
        }
        return true;
    }

    public function get_maximum_year_levels_of_college($college_id = null)
    {

        $departments = $this->College->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $college_id), 'fields' => array('Department.id'))
        );
        $largest_yearLevel_department_id = null;
        $yearLevel_count = 0;
        foreach ($departments as $department_id) {
            $yearLevel_count_latest = $this->College->Department->YearLevel->find(
                'count',
                array('conditions' => array('YearLevel.department_id' => $department_id))
            );
            if ($yearLevel_count_latest > $yearLevel_count) {
                $yearLevel_count = $yearLevel_count_latest;
                $largest_yearLevel_department_id = $department_id;
            }
        }

        $yearLevels = null;
        if (!empty($largest_yearLevel_department_id)) {
            $yearLevels = $this->College->Department->YearLevel->find(
                'list',
                array(
                    'conditions' => array('YearLevel.department_id' => $largest_yearLevel_department_id),
                    'fields' => array('name', 'name')
                )
            );
        }
        return $yearLevels;
    }

    public function alreadyRecorded($data = null)
    {

        // validation of repeation
        $selected_college_id = $data['ExamPeriod']['college_id'];
        $selected_academic_year = $data['ExamPeriod']['academic_year'];
        $selected_program_id = $data['ExamPeriod']['program_id'];
        $selected_semester = $data['ExamPeriod']['semester'];
        foreach ($data['ExamPeriod']['program_type_id'] as $ptk => $ptv) {
            foreach ($data['ExamPeriod']['year_level_id'] as $ylk => $ylv) {
                $repeation = $this->find(
                    'count',
                    array(
                        'conditions' => array(
                            'ExamPeriod.college_id' => $selected_college_id,
                            'ExamPeriod.academic_year' => $selected_academic_year,
                            'ExamPeriod.program_id' => $selected_program_id,
                            'ExamPeriod.program_type_id' => $ptv,
                            'ExamPeriod.year_level_id' => $ylv,
                            'ExamPeriod.semester' => $selected_semester
                        )
                    )
                );
                if ($repeation > 0) {
                    $program_type_name = $this->ProgramType->field('ProgramType.name', array('ProgramType.id' => $ptv));
                    $this->invalidate(
                        'already_recorded_exam_perid',
                        'The exam period is already recorded for ' . $program_type_name . ' ' . $ylv . ' year students'
                    );
                    return false;
                }
            }
        }
        return true;
    }

    public function beforeDeleteCheckEligibility($id = null, $college_id = null)
    {

        $count = $this->find(
            'count',
            array('conditions' => array('ExamPeriod.college_id' => $college_id, 'ExamPeriod.id' => $id))
        );
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
}

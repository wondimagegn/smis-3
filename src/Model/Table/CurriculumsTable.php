<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CurriculumsTable extends Table
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

        $this->setTable('curriculums');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
        ]);
        $this->belongsTo('DepartmentStudyPrograms', [
            'foreignKey' => 'department_study_program_id',
        ]);
        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'curriculum_id',
        ]);
        $this->hasMany('CourseCategories', [
            'foreignKey' => 'curriculum_id',
        ]);
        $this->hasMany('Courses', [
            'foreignKey' => 'curriculum_id',
        ]);
        $this->hasMany('CurriculumAttachments', [
            'foreignKey' => 'curriculum_id',
        ]);
        $this->hasMany('OtherAcademicRules', [
            'foreignKey' => 'curriculum_id',
        ]);
        $this->hasMany('Sections', [
            'foreignKey' => 'curriculum_id',
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'curriculum_id',
        ]);
        $this->hasMany('Attachments', [
            'foreignKey' => 'foreign_key',
            'conditions' => ['Attachments.model' => 'Curriculum'],
            'dependent' => true,
            'cascadeCallbacks' => true
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
            ->scalar('name')
            ->maxLength('name', 250)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->date('year_introduced')
            ->requirePresence('year_introduced', 'create')
            ->notEmptyDate('year_introduced');

        $validator
            ->scalar('type_credit')
            ->maxLength('type_credit', 25)
            ->allowEmptyString('type_credit');

        $validator
            ->scalar('certificate_name')
            ->maxLength('certificate_name', 30)
            ->requirePresence('certificate_name', 'create')
            ->notEmptyString('certificate_name');

        $validator
            ->scalar('amharic_degree_nomenclature')
            ->maxLength('amharic_degree_nomenclature', 255)
            ->allowEmptyString('amharic_degree_nomenclature');

        $validator
            ->scalar('specialization_amharic_degree_nomenclature')
            ->maxLength('specialization_amharic_degree_nomenclature', 255)
            ->allowEmptyString('specialization_amharic_degree_nomenclature');

        $validator
            ->scalar('english_degree_nomenclature')
            ->maxLength('english_degree_nomenclature', 100)
            ->requirePresence('english_degree_nomenclature', 'create')
            ->notEmptyString('english_degree_nomenclature');

        $validator
            ->scalar('specialization_english_degree_nomenclature')
            ->maxLength('specialization_english_degree_nomenclature', 100)
            ->requirePresence('specialization_english_degree_nomenclature', 'create')
            ->notEmptyString('specialization_english_degree_nomenclature');

        $validator
            ->integer('minimum_credit_points')
            ->requirePresence('minimum_credit_points', 'create')
            ->notEmptyString('minimum_credit_points');

        $validator
            ->boolean('lock')
            ->notEmptyString('lock');

        $validator
            ->boolean('registrar_approved')
            ->notEmptyString('registrar_approved');

        $validator
            ->boolean('active')
            ->notEmptyString('active');

        $validator
            ->integer('curriculum_type')
            ->notEmptyString('curriculum_type');

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
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['program_id'], 'Programs'));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));
        $rules->add($rules->existsIn(['department_study_program_id'], 'DepartmentStudyPrograms'));

        return $rules;
    }

    function sumCreditCurriculum()
    {
        $sum_course_category = 0;

        if (!empty($this->data['CourseCategory'])) {
            foreach ($this->data['CourseCategory'] as $ck => $cv) {
                $sum_course_category += $cv['total_credit'];
            }
        }

        if ($sum_course_category >= $this->data['Curriculum']['minimum_credit_points']) {
            return true;
        }

        return false;
    }

    function sumMandatoryCredit()
    {
        $sum_course_category = 0;

        if (!empty($this->data['CourseCategory'])) {
            foreach ($this->data['CourseCategory'] as $ck => $cv) {
                $sum_course_category += $cv['mandatory_credit'];
            }
        }

        if ($sum_course_category == $this->data['Curriculum']['minimum_credit_points']) {
            return true;
        }

        return false;
    }


    function canItBeDeleted($curriculum_id = null)
    {
        if ($this->Course->find('count', array('conditions' => array('Course.curriculum_id' => $curriculum_id))) > 0) {
            return false;
        } elseif ($this->Student->find('count', array('conditions' => array('Student.curriculum_id' => $curriculum_id))
            ) > 0) {
            return false;
        }
        /* else if ($this->Attachment->find('count', array('conditions' => array('Attachment.model' => 'Curriculum', 'Attachment.foreign_key' => $curriculum_id))) > 0) {
            return false;
        } else if ($this->CourseCategory->find('count', array('conditions' => array('CourseCategory.curriculum_id' => $curriculum_id))) > 0) {
            return false;
        } */
        else {
            return true;
        }
    }

    function organized_course_of_curriculum_by_year_semester($data = null)
    {
        $courses_organized_by_year = array();

        if (!empty($data['id'])) {
            foreach ($data['Course'] as $index => $value) {
                if (empty($value['Course'])) {
                    $courses_organized_by_year[$value['YearLevel']['name']][$value['semester']][] = $value;
                    // $value['hasEquivalentMap']=ClassRegistry::init('EquivalentCourse')->checkCourseHasEquivalentCourse($value['id'],$studentAttachedCurriculumID);
                }
            }
            $data['Course'] = $courses_organized_by_year;
        }
        return $data;
    }

    function preparedAttachment($data = null)
    {
        if (!empty($data['Attachment'])) {
            foreach ($data['Attachment'] as $in => &$dv) {
                if (empty($dv['file']['name']) && empty($dv['file']['type']) && empty($dv['tmp_name'])) {
                    unset($data['Attachment'][$in]);
                } else {
                    $dv['model'] = 'Curriculum';
                    $dv['group'] = 'attachment';
                }
            }
            return $data;
        }
    }

    function getDepartmentStudyProgramDetails($department_id = null, $program_modality_id = null, $qualification_id = null)
    {
        $conditions = array();

        if ($department_id) {
            $conditions[] = array('DepartmentStudyProgram.department_id' => $department_id);
        }

        if ($program_modality_id) {
            $conditions[] = array('DepartmentStudyProgram.program_modality_id' => $program_modality_id);
        }

        if ($qualification_id) {
            $conditions[] = array('DepartmentStudyProgram.qualification_id' => $qualification_id);
        }

        //debug($conditions);

        $departmentStudyProgramDetails = array();
        $departmentStudyProgramListForSelect = array();


        if (!empty($conditions)) {
            $departmentStudyProgramDetails = $this->DepartmentStudyProgram->find('all', array(
                'conditions' => $conditions,
                'contain' => array(
                    'StudyProgram' => array('fields' => array('id', 'study_program_name', 'code')),
                    'ProgramModality' => array('fields' => array('id', 'modality', 'code')),
                    'Qualification'  => array('fields' => array('id', 'qualification', 'code')),
                ),
                'fields' => array('DepartmentStudyProgram.id', 'DepartmentStudyProgram.study_program_id')
            ));
        }

        if (!empty($departmentStudyProgramDetails)) {
            foreach ($departmentStudyProgramDetails as $dspkey => $dspval) {
                //debug($dspval);
                $departmentStudyProgramListForSelect[$dspval['DepartmentStudyProgram']['id']] = $dspval['StudyProgram']['study_program_name'] . '(' . $dspval['StudyProgram']['code'] . ') => ' . $dspval['ProgramModality']['modality'] . '(' . $dspval['ProgramModality']['code'] . ') => ' . $dspval['Qualification']['qualification'] . '(' . $dspval['Qualification']['code'] . ')';
            }
        }

        return $departmentStudyProgramListForSelect;
    }
}

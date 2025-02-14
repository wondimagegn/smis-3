<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DepartmentStudyProgramsTable extends Table
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

        $this->setTable('department_study_programs');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('StudyPrograms', [
            'foreignKey' => 'study_program_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ProgramModalities', [
            'foreignKey' => 'program_modality_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Qualifications', [
            'foreignKey' => 'qualification_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Curriculums', [
            'foreignKey' => 'department_study_program_id',
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
            ->maxLength('academic_year', 10)
            ->allowEmptyString('academic_year');

        $validator
            ->boolean('apply_for_current_students')
            ->notEmptyString('apply_for_current_students');

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
        $rules->add($rules->existsIn(['study_program_id'], 'StudyPrograms'));
        $rules->add($rules->existsIn(['program_modality_id'], 'ProgramModalities'));
        $rules->add($rules->existsIn(['qualification_id'], 'Qualifications'));

        return $rules;
    }

    function isUniqueDepartmentStudyProgram($data = null)
    {
        $count = 0;

        debug($data);

        if (!empty($data['DepartmentStudyProgram']['id'])) {
            $count = $this->find('count', array(
                'conditions' => array(
                    'DepartmentStudyProgram.department_id' => $data['DepartmentStudyProgram']['department_id'],
                    'DepartmentStudyProgram.study_program_id' => $data['DepartmentStudyProgram']['study_program_id'],
                    'DepartmentStudyProgram.program_modality_id' => $data['DepartmentStudyProgram']['program_modality_id'],
                    'DepartmentStudyProgram.qualification_id' => $data['DepartmentStudyProgram']['qualification_id'],
                    'DepartmentStudyProgram.academic_year' => $data['DepartmentStudyProgram']['academic_year'],
                    'DepartmentStudyProgram.id <> ' => $data['DepartmentStudyProgram']['id']
                )
            ));
        } else if (!empty($data['DepartmentStudyProgram'])) {
            $count = $this->find('count', array(
                'conditions' => array(
                    'DepartmentStudyProgram.department_id' => $data['DepartmentStudyProgram']['department_id'],
                    'DepartmentStudyProgram.study_program_id' => $data['DepartmentStudyProgram']['study_program_id'],
                    'DepartmentStudyProgram.program_modality_id' => $data['DepartmentStudyProgram']['program_modality_id'],
                    'DepartmentStudyProgram.academic_year' => $data['DepartmentStudyProgram']['academic_year'],
                    'DepartmentStudyProgram.qualification_id' => $data['DepartmentStudyProgram']['qualification_id']
                )
            ));
        }

        debug($count);

        if ($count > 0) {
            return false;
        }
        return true;
    }

    function canItBeDeleted($department_study_program_id = null)
    {
        if (ClassRegistry::init('Curriculum')->find('count', array('conditions' => array('Curriculum.department_study_program_id' => $department_study_program_id))) > 0) {
            return false;
        } else {
            return true;
        }
    }
}

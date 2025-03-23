<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CollegesTable extends Table
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

        $this->setTable('colleges');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Campuses', [
            'foreignKey' => 'campus_id',
            'joinType' => 'INNER',
            'propertyName' => 'Campuse',
        ]);
        $this->belongsTo('MoodleCategories', [
            'foreignKey' => 'moodle_category_id',
            'joinType' => 'INNER',
            'propertyName' => 'MoodleCategory',
        ]);
        $this->hasMany('AcademicCalendars', [
            'foreignKey' => 'college_id',
            'propertyName' => 'AcademicCalendar',
        ]);
        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'college_id',
            'propertyName' => 'AcceptedStudent',
        ]);
        $this->hasMany('ClassPeriods', [
            'foreignKey' => 'college_id',
            'propertyName' => 'ClassPeriod',
        ]);
        $this->hasMany('ClassRoomBlocks', [
            'foreignKey' => 'college_id',
            'propertyName' => 'ClassRoomBlock',
        ]);
        $this->hasMany('Departments', [
            'foreignKey' => 'college_id',
            'propertyName' => 'Department',
        ]);
        $this->hasMany('ExamPeriods', [
            'foreignKey' => 'college_id',
            'propertyName' => 'ExamPeriod',
        ]);
        $this->hasMany('InstructorClassPeriodCourseConstraints', [
            'foreignKey' => 'college_id',
            'propertyName' => 'InstructorClassPeriodCourseConstraint',
        ]);
        $this->hasMany('InstructorNumberOfExamConstraints', [
            'foreignKey' => 'college_id',
            'propertyName' => 'InstructorNumberOfExamConstraint',
        ]);
        $this->hasMany('Notes', [
            'foreignKey' => 'college_id',
            'propertyName' => 'Note',
        ]);
        $this->hasMany('OnlineApplicants', [
            'foreignKey' => 'college_id',
            'propertyName' => 'OnlineApplicant',
        ]);
        $this->hasMany('ParticipatingDepartments', [
            'foreignKey' => 'college_id',
            'propertyName' => 'ParticipatingDepartment',
        ]);
        $this->hasMany('PeriodSettings', [
            'foreignKey' => 'college_id',
            'propertyName' => 'PeriodSetting',
        ]);
        $this->hasMany('PlacementLocks', [
            'foreignKey' => 'college_id',
            'propertyName' => 'PlacementLock',
        ]);
        $this->hasMany('PlacementsResultsCriterias', [
            'foreignKey' => 'college_id',
            'propertyName' => 'PlacementsResultsCriteria',
        ]);
        $this->hasMany('PreferenceDeadlines', [
            'foreignKey' => 'college_id',
            'propertyName' => 'PreferenceDeadline',
        ]);
        $this->hasMany('Preferences', [
            'foreignKey' => 'college_id',
            'propertyName' => 'Preference',
        ]);
        $this->hasMany('PublishedCourses', [
            'foreignKey' => 'college_id',
            'propertyName' => 'PublishedCourse',
        ]);
        $this->hasMany('Quotas', [
            'foreignKey' => 'college_id',
            'propertyName' => 'Quota',
        ]);
        $this->hasMany('ReservedPlaces', [
            'foreignKey' => 'college_id',
            'propertyName' => 'ReservedPlace',
        ]);
        $this->hasMany('Sections', [
            'foreignKey' => 'college_id',
            'propertyName' => 'Section',
        ]);
        $this->hasMany('StaffAssignes', [
            'foreignKey' => 'college_id',
            'propertyName' => 'StaffAssignee',
        ]);
        $this->hasMany('StaffForExams', [
            'foreignKey' => 'college_id',
            'propertyName' => 'StaffForExam',
        ]);
        $this->hasMany('Staffs', [
            'foreignKey' => 'college_id',
            'propertyName' => 'Staff',
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'college_id',
            'propertyName' => 'Student',
        ]);
        $this->hasMany('TakenProperties', [
            'foreignKey' => 'college_id',
            'propertyName' => 'TakenProperty',
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
            ->maxLength('name', 200)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('shortname')
            ->maxLength('shortname', 10)
            ->allowEmptyString('shortname');

        $validator
            ->scalar('amharic_name')
            ->maxLength('amharic_name', 150)
            ->requirePresence('amharic_name', 'create')
            ->notEmptyString('amharic_name');

        $validator
            ->scalar('amharic_short_name')
            ->maxLength('amharic_short_name', 50)
            ->requirePresence('amharic_short_name', 'create')
            ->notEmptyString('amharic_short_name');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('phone')
            ->maxLength('phone', 36)
            ->allowEmptyString('phone');

        $validator
            ->scalar('type')
            ->maxLength('type', 200)
            ->notEmptyString('type');

        $validator
            ->scalar('type_amharic')
            ->maxLength('type_amharic', 100)
            ->notEmptyString('type_amharic');

        $validator
            ->scalar('name_start_date')
            ->allowEmptyString('name_start_date');

        $validator
            ->scalar('name_end_date')
            ->allowEmptyString('name_end_date');

        $validator
            ->boolean('applay')
            ->requirePresence('applay', 'create')
            ->notEmptyString('applay');

        $validator
            ->boolean('deligate_scale')
            ->requirePresence('deligate_scale', 'create')
            ->notEmptyString('deligate_scale');

        $validator
            ->boolean('deligate_for_graduate_study')
            ->notEmptyString('deligate_for_graduate_study');

        $validator
            ->integer('available_for_placement')
            ->notEmptyString('available_for_placement');

        $validator
            ->boolean('active')
            ->notEmptyString('active');

        $validator
            ->scalar('institution_code')
            ->maxLength('institution_code', 100)
            ->allowEmptyString('institution_code');

        $validator
            ->scalar('idnumber_prefix')
            ->maxLength('idnumber_prefix', 10)
            ->allowEmptyString('idnumber_prefix');

        $validator
            ->integer('stream')
            ->notEmptyString('stream');

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
        $rules->add($rules->existsIn(['campus_id'], 'Campuses'));
        $rules->add($rules->existsIn(['moodle_category_id'], 'MoodleCategories'));

        return $rules;
    }

    function isUniqueCollegeInCampus()
    {
        $count = 0;

        if (!empty($this->data['College']['id'])) {
            $count = $this->find('count', array('conditions' => array('College.campus_id' => $this->data['College']['campus_id'], 'College.name' => trim($this->data['College']['name']), 'College.id <> ' => $this->data['College']['id'])));
        } else {
            $count = $this->find('count', array('conditions' => array('College.campus_id' => $this->data['College']['campus_id'], 'College.name' => trim($this->data['College']['name']))));
        }

        if ($count > 0) {
            return false;
        }
        return true;
    }

    function allowDelete($id = null)
    {
        if ($this->Student->find('count', array('conditions' => array('Student.college_id' => $id))) > 0) {
            return false;
        } else {
            return true;
        }
    }

    function canItBeDeleted($college_id = null)
    {
        if ($this->PublishedCourse->find('count', array('conditions' => array('PublishedCourse.college_id' => $college_id))) > 0) {
            return false;
        }
        if ($this->Student->find('count', array('conditions' => array('Student.college_id' => $college_id))) > 0) {
            return false;
        } elseif ($this->Section->find('count', array('conditions' => array('Section.college_id' => $college_id))
            ) > 0) {
            return false;
        } elseif ($this->GradeScale->find(
                'count',
                array('conditions' => array('GradeScale.model' => 'College', 'GradeScale.foreign_key' => $college_id))
            ) > 0) {
            return false;
        } else {
            return true;
        }
    }
}

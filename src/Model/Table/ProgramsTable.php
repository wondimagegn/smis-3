<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ProgramsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('programs');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        // Associations
        $this->hasMany('GraduationStatuses', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('GraduationRequirements', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('TranscriptFooters', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('GraduationWorks', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('GradeScales', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('Sections', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('AcademicCalendars', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('ClassPeriods', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('ProgramProgramTypeClassRooms', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('StudentStatusPatterns', [
            'foreignKey' => 'program_id',
        ]);
        $this->hasMany('ExamPeriods', [
            'foreignKey' => 'program_id',
        ]);

        $this->hasMany('AcademicCalendars', [
            'foreignKey' => 'program_id',
        ]);

        // Add timestamp behavior
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('name', 'Program name is required.')
            ->add('name', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'This program name is already taken.',
            ]);

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['name'], 'This program name is already taken.'));
        return $rules;
    }
}

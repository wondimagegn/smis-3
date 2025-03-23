<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class SectionSplitForExamsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('section_split_for_exams');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');

        // Add behaviors
        $this->addBehavior('Timestamp');

        // Define Associations
        $this->belongsTo('Sections', [
            'foreignKey' => 'section_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('ExamSplitSections', [
            'foreignKey' => 'section_split_for_exam_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('section_id', 'create')
            ->notEmptyString('section_id', 'Section is required.')
            ->integer('section_id', 'Section ID must be an integer.');

        $validator
            ->requirePresence('published_course_id', 'create')
            ->notEmptyString('published_course_id', 'Published Course is required.')
            ->integer('published_course_id', 'Published Course ID must be an integer.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['section_id'], 'Sections'), 'validSection', [
            'errorField' => 'section_id',
            'message' => 'Invalid section ID.',
        ]);

        $rules->add($rules->existsIn(['published_course_id'], 'PublishedCourses'), 'validCourse', [
            'errorField' => 'published_course_id',
            'message' => 'Invalid published course ID.',
        ]);

        return $rules;
    }
}

<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StudentEvalutionCommentsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('student_evalution_comments');
        $this->setPrimaryKey('id');

        // BelongsTo Associations
        $this->belongsTo('InstructorEvaluationQuestions', [
            'foreignKey' => 'instructor_evalution_question_id',
        ]);

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
        ]);

        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('instructor_evalution_question_id', 'Must be a valid number')
            ->requirePresence('instructor_evalution_question_id', 'create')
            ->notEmptyString('instructor_evalution_question_id');

        $validator
            ->integer('student_id', 'Must be a valid number')
            ->requirePresence('student_id', 'create')
            ->notEmptyString('student_id');

        $validator
            ->integer('published_course_id', 'Must be a valid number')
            ->requirePresence('published_course_id', 'create')
            ->notEmptyString('published_course_id');

        $validator
            ->scalar('comment')
            ->requirePresence('comment', 'create')
            ->notEmptyString('comment', 'Comment cannot be empty');

        return $validator;
    }
}

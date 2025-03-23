<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class SurveyQuestionsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('survey_questions');
        $this->setPrimaryKey('id');
        $this->setDisplayField('question_english');

        $this->hasMany('AlumniResponses', [
            'foreignKey' => 'survey_question_id',
            'dependent' => false,
        ]);

        $this->hasMany('SurveyQuestionAnswers', [
            'foreignKey' => 'survey_question_id',
            'dependent' => false,
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('question_english', 'create')
            ->notEmptyString('question_english', 'Please provide the survey question')
            ->maxLength('question_english', 255);

        return $validator;
    }

    public function unsetdata($data = [])
    {
        if (!empty($data)) {
            if ($data['answer_required_yn'] == 0 && $data['allow_multiple_answers'] == 0) {
                unset($data['SurveyQuestionAnswer']);
            }
        }
        return $data;
    }
}

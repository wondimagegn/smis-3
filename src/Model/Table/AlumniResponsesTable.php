<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AlumniResponses Table
 */
class AlumniResponsesTable extends Table
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

        $this->setTable('alumni_responses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Alumni', [
            'foreignKey' => 'alumni_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('SurveyQuestions', [
            'foreignKey' => 'survey_question_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('SurveyQuestionAnswers', [
            'foreignKey' => 'survey_question_answer_id',
            'joinType' => 'LEFT',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    /**
     * Checks if an alumni has completed all round one survey questions
     *
     * @param int|null $alumniId Alumni ID
     * @return bool True if all questions answered, false otherwise
     */
    public function completedRoundOneQuestionnaire($alumniId = null)
    {
        if (!$alumniId) {
            return false;
        }

        $surveyQuestions = $this->SurveyQuestions->find()
            ->contain(['SurveyQuestionAnswers'])
            ->toArray();

        if (!empty($surveyQuestions)) {
            foreach ($surveyQuestions as $question) {
                $responseCount = $this->find()
                    ->where([
                        'AlumniResponses.alumni_id' => $alumniId,
                        'AlumniResponses.survey_question_id' => $question->id
                    ])
                    ->count();

                if ($responseCount === 0) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }
}

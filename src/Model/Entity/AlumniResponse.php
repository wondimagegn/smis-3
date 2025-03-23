<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class AlumniResponse extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'alumni_id' => true,
        'survey_question_id' => true,
        'survey_question_answer_id' => true,
        'specifiy' => true,
        'mother' => true,
        'father' => true,
        'created' => true,
        'modified' => true,
        'alumni' => true,
        'survey_question' => true,
        'survey_question_answer' => true,
    ];

    public function completedRoundOneQuestionner($alumni_id)
    {

        $surveyQuestions = $this->SurveyQuestion->find(
            'all',
            array('contain' => array('SurveyQuestionAnswer'))
        );
        debug($surveyQuestions);
        if (!empty($surveyQuestions)) {
            foreach ($surveyQuestions as $k => $v) {
                $response = $this->find(
                    'count',
                    array(
                        'conditions' => array(
                            'AlumniResponse.alumni_id' => $alumni_id,
                            'AlumniResponse.survey_question_id' => $v['SurveyQuestion']['id']
                        )
                    )
                );
                if (empty($response)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}

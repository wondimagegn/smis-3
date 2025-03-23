<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SurveyQuestionAnswer Entity
 *
 * @property int $id
 * @property int $survey_question_id
 * @property string $answer_english
 * @property string|null $answer_amharic
 * @property int $order
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\SurveyQuestion $survey_question
 * @property \App\Model\Entity\AlumniResponse[] $alumni_responses
 */
class SurveyQuestionAnswer extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'survey_question_id' => true,
        'answer_english' => true,
        'answer_amharic' => true,
        'order' => true,
        'created' => true,
        'modified' => true,
        'survey_question' => true,
        'alumni_responses' => true,
    ];
}

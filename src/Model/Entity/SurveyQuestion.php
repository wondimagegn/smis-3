<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * SurveyQuestion Entity
 *
 * @property int $id
 * @property string $question_english
 * @property string|null $question_amharic
 * @property bool $allow_multiple_answers
 * @property bool $answer_required_yn
 * @property bool $mother
 * @property bool $father
 * @property \Cake\I18n\FrozenTime|null $created
 * @property bool $require_remark_text
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\AlumniResponse[] $alumni_responses
 * @property \App\Model\Entity\SurveyQuestionAnswer[] $survey_question_answers
 */
class SurveyQuestion extends Entity
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
        'question_english' => true,
        'question_amharic' => true,
        'allow_multiple_answers' => true,
        'answer_required_yn' => true,
        'mother' => true,
        'father' => true,
        'created' => true,
        'require_remark_text' => true,
        'modified' => true,
        'alumni_responses' => true,
        'survey_question_answers' => true,
    ];
}

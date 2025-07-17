<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AlumniResponse Entity
 */
class AlumniResponse extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'alumni_id' => true,
        'survey_question_id' => true,
        'survey_question_answer_id' => true,
        'alumni' => true,
        'survey_question' => true,
        'survey_question_answer' => true
    ];
}

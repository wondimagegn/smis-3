<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * InstructorEvalutionQuestion Entity
 *
 * @property int $id
 * @property string|null $question
 * @property string|null $question_amharic
 * @property string $type
 * @property string $for
 * @property bool $active
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\ColleagueEvalutionRate[] $colleague_evalution_rates
 * @property \App\Model\Entity\StudentEvalutionComment[] $student_evalution_comments
 * @property \App\Model\Entity\StudentEvalutionRate[] $student_evalution_rates
 */
class InstructorEvalutionQuestion extends Entity
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
        'question' => true,
        'question_amharic' => true,
        'type' => true,
        'for' => true,
        'active' => true,
        'created' => true,
        'modified' => true,
        'colleague_evalution_rates' => true,
        'student_evalution_comments' => true,
        'student_evalution_rates' => true,
    ];
}

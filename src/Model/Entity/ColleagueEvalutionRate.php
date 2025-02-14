<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class ColleagueEvalutionRate extends Entity
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
        'instructor_evalution_question_id' => true,
        'staff_id' => true,
        'evaluator_id' => true,
        'dept_head' => true,
        'academic_year' => true,
        'semester' => true,
        'rating' => true,
        'created' => true,
        'modified' => true,
        'instructor_evalution_question' => true,
        'staff' => true,
        'evaluator' => true,
    ];
}

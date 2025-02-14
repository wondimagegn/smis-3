<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExamExcludedDateAndSession Entity
 *
 * @property int $id
 * @property int $exam_period_id
 * @property \Cake\I18n\FrozenDate $excluded_date
 * @property int $session
 *
 * @property \App\Model\Entity\ExamPeriod $exam_period
 */
class ExamExcludedDateAndSession extends Entity
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
        'exam_period_id' => true,
        'excluded_date' => true,
        'session' => true,
        'exam_period' => true,
    ];
}

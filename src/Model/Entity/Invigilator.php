<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Invigilator Entity
 *
 * @property int|null $staff_id
 * @property int|null $staff_for_exam_id
 * @property int $exam_schedule_id
 * @property int $id
 *
 * @property \App\Model\Entity\Staff $staff
 * @property \App\Model\Entity\StaffForExam $staff_for_exam
 * @property \App\Model\Entity\ExamSchedule $exam_schedule
 */
class Invigilator extends Entity
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
        'staff_id' => true,
        'staff_for_exam_id' => true,
        'exam_schedule_id' => true,
        'staff' => true,
        'staff_for_exam' => true,
        'exam_schedule' => true,
    ];
}

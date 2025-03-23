<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ExamPeriod Entity
 *
 * @property int $id
 * @property int $college_id
 * @property int $program_id
 * @property int $program_type_id
 * @property string|null $academic_year
 * @property string|null $semester
 * @property string|null $year_level_id
 * @property int|null $default_number_of_invigilator_per_exam
 * @property \Cake\I18n\FrozenDate|null $start_date
 * @property \Cake\I18n\FrozenDate|null $end_date
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\College $college
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 * @property \App\Model\Entity\YearLevel $year_level
 * @property \App\Model\Entity\ExamExcludedDateAndSession[] $exam_excluded_date_and_sessions
 */
class ExamPeriod extends Entity
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
        'college_id' => true,
        'program_id' => true,
        'program_type_id' => true,
        'academic_year' => true,
        'semester' => true,
        'year_level_id' => true,
        'default_number_of_invigilator_per_exam' => true,
        'start_date' => true,
        'end_date' => true,
        'created' => true,
        'modified' => true,
        'college' => true,
        'program' => true,
        'program_type' => true,
        'year_level' => true,
        'exam_excluded_date_and_sessions' => true,
    ];
}

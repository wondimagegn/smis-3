<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GraduationCertificate Entity
 *
 * @property int $id
 * @property int $program_id
 * @property int $program_type_id
 * @property string|null $department
 * @property string $amharic_title
 * @property string $amharic_content
 * @property int $am_title_font_size
 * @property int $am_content_font_size
 * @property string $english_title
 * @property string $english_content
 * @property int $en_title_font_size
 * @property int $en_content_font_size
 * @property int $academic_year
 * @property bool $applicable_for_current_student
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 */
class GraduationCertificate extends Entity
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
        'program_id' => true,
        'program_type_id' => true,
        'department' => true,
        'amharic_title' => true,
        'amharic_content' => true,
        'am_title_font_size' => true,
        'am_content_font_size' => true,
        'english_title' => true,
        'english_content' => true,
        'en_title_font_size' => true,
        'en_content_font_size' => true,
        'academic_year' => true,
        'applicable_for_current_student' => true,
        'created' => true,
        'modified' => true,
        'program' => true,
        'program_type' => true,
    ];
}

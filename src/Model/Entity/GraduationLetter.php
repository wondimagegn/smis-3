<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GraduationLetter Entity
 *
 * @property int $id
 * @property string $type
 * @property int $program_id
 * @property int $program_type_id
 * @property string|null $department
 * @property string $title
 * @property int $title_font_size
 * @property string $content
 * @property int $content_font_size
 * @property int $academic_year
 * @property bool $applicable_for_current_student
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 */
class GraduationLetter extends Entity
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
        'type' => true,
        'program_id' => true,
        'program_type_id' => true,
        'department' => true,
        'title' => true,
        'title_font_size' => true,
        'content' => true,
        'content_font_size' => true,
        'academic_year' => true,
        'applicable_for_current_student' => true,
        'created' => true,
        'modified' => true,
        'program' => true,
        'program_type' => true,
    ];
}

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PlacementRoundParticipant Entity
 *
 * @property int $id
 * @property string $type
 * @property string|null $name
 * @property string $applied_for
 * @property int $group_identifier
 * @property int $program_id
 * @property int $program_type_id
 * @property int $foreign_key
 * @property string $academic_year
 * @property int $placement_round
 * @property int|null $intake_capacity
 * @property int|null $female_quota
 * @property int|null $disability_quota
 * @property int|null $region_quota
 * @property string|null $developing_region
 * @property int $exam_giver
 * @property string|null $semester
 * @property bool $require_all_selected
 * @property int $require_cgpa
 * @property float|null $minimum_cgpa
 * @property float|null $maximum_cgpa
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 * @property \App\Model\Entity\PlacementEntranceExamResultEntry[] $placement_entrance_exam_result_entries
 * @property \App\Model\Entity\PlacementParticipatingStudent[] $placement_participating_students
 * @property \App\Model\Entity\PlacementPreference[] $placement_preferences
 */
class PlacementRoundParticipant extends Entity
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
        'name' => true,
        'applied_for' => true,
        'group_identifier' => true,
        'program_id' => true,
        'program_type_id' => true,
        'foreign_key' => true,
        'academic_year' => true,
        'placement_round' => true,
        'intake_capacity' => true,
        'female_quota' => true,
        'disability_quota' => true,
        'region_quota' => true,
        'developing_region' => true,
        'exam_giver' => true,
        'semester' => true,
        'require_all_selected' => true,
        'require_cgpa' => true,
        'minimum_cgpa' => true,
        'maximum_cgpa' => true,
        'created' => true,
        'modified' => true,
        'program' => true,
        'program_type' => true,
        'placement_entrance_exam_result_entries' => true,
        'placement_participating_students' => true,
        'placement_preferences' => true,
    ];
}

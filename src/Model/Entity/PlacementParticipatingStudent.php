<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PlacementParticipatingStudent Entity
 *
 * @property int $id
 * @property int $accepted_student_id
 * @property int $student_id
 * @property int $program_id
 * @property int $program_type_id
 * @property string $applied_for
 * @property string $academic_year
 * @property int $round
 * @property float|null $result_weight
 * @property float $total_placement_weight
 * @property float $female_placement_weight
 * @property float $disability_weight
 * @property float $developing_region_weight
 * @property int|null $placement_round_participant_id
 * @property string|null $placementtype
 * @property string|null $placement_based
 * @property int $status
 * @property string|null $remark
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\AcceptedStudent $accepted_student
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Program $program
 * @property \App\Model\Entity\ProgramType $program_type
 * @property \App\Model\Entity\PlacementRoundParticipant $placement_round_participant
 */
class PlacementParticipatingStudent extends Entity
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
        'accepted_student_id' => true,
        'student_id' => true,
        'program_id' => true,
        'program_type_id' => true,
        'applied_for' => true,
        'academic_year' => true,
        'round' => true,
        'result_weight' => true,
        'total_placement_weight' => true,
        'female_placement_weight' => true,
        'disability_weight' => true,
        'developing_region_weight' => true,
        'placement_round_participant_id' => true,
        'placementtype' => true,
        'placement_based' => true,
        'status' => true,
        'remark' => true,
        'created' => true,
        'modified' => true,
        'accepted_student' => true,
        'student' => true,
        'program' => true,
        'program_type' => true,
        'placement_round_participant' => true,
    ];
}

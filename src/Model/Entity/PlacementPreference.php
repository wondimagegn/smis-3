<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PlacementPreference Entity
 *
 * @property int $id
 * @property int $accepted_student_id
 * @property int|null $student_id
 * @property int|null $placement_round_participant_id
 * @property string $academic_year
 * @property int $round
 * @property int $preference_order
 * @property string|null $user_id
 * @property string|null $edited_by
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\AcceptedStudent $accepted_student
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\PlacementRoundParticipant $placement_round_participant
 * @property \App\Model\Entity\User $user
 */
class PlacementPreference extends Entity
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
        'placement_round_participant_id' => true,
        'academic_year' => true,
        'round' => true,
        'preference_order' => true,
        'user_id' => true,
        'edited_by' => true,
        'created' => true,
        'modified' => true,
        'accepted_student' => true,
        'student' => true,
        'placement_round_participant' => true,
        'user' => true,
    ];
}

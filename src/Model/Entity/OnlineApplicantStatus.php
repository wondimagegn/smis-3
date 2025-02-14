<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * OnlineApplicantStatus Entity
 *
 * @property int $id
 * @property int $online_applicant_id
 * @property string $status
 * @property string $remark
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\OnlineApplicant $online_applicant
 */
class OnlineApplicantStatus extends Entity
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
        'online_applicant_id' => true,
        'status' => true,
        'remark' => true,
        'created' => true,
        'modified' => true,
        'online_applicant' => true,
    ];
}

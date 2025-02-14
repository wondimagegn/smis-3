<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * OneCardsManagementSy Entity
 *
 * @property string $FullName
 * @property string|null $FullNameA
 * @property string $Department
 * @property string|null $Department1
 * @property string|null $IDNum
 * @property string|null $IDNum1
 * @property string|null $photoID
 * @property string|null $photoID2
 * @property int $serialno
 * @property \Cake\I18n\FrozenDate|null $dateissued
 * @property int|null $PrintCount
 * @property string|null $PrintRemarks
 * @property int $role
 * @property int|null $College
 */
class OneCardsManagementSy extends Entity
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
        'FullName' => true,
        'FullNameA' => true,
        'Department' => true,
        'Department1' => true,
        'IDNum' => true,
        'IDNum1' => true,
        'photoID' => true,
        'photoID2' => true,
        'serialno' => true,
        'dateissued' => true,
        'PrintCount' => true,
        'PrintRemarks' => true,
        'role' => true,
        'College' => true,
    ];
}

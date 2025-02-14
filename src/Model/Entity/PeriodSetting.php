<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PeriodSetting Entity
 *
 * @property int $id
 * @property int $college_id
 * @property int $period
 * @property \Cake\I18n\FrozenTime $hour
 *
 * @property \App\Model\Entity\College $college
 * @property \App\Model\Entity\ClassPeriod[] $class_periods
 */
class PeriodSetting extends Entity
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
        'period' => true,
        'hour' => true,
        'college' => true,
        'class_periods' => true,
    ];
}

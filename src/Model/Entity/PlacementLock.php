<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PlacementLock Entity
 *
 * @property int $id
 * @property string|null $academic_year
 * @property int|null $college_id
 * @property \Cake\I18n\FrozenTime|null $start_time
 * @property \Cake\I18n\FrozenTime|null $end_time
 * @property string|null $result
 * @property bool|null $process_start
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\College $college
 */
class PlacementLock extends Entity
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
        'academic_year' => true,
        'college_id' => true,
        'start_time' => true,
        'end_time' => true,
        'result' => true,
        'process_start' => true,
        'created' => true,
        'modified' => true,
        'college' => true,
    ];
}

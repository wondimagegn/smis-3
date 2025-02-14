<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class PlacementsResultsCriteria extends Entity
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
        'name' => true,
        'admissionyear' => true,
        'prepartory_result' => true,
        'college_id' => true,
        'result_from' => true,
        'result_to' => true,
        'created' => true,
        'modified' => true,
        'college' => true,
        'reserved_places' => true,
    ];
    protected  $_virtual = ['result_category'];
    protected function _getResultCategory()
    {
        return trim("{$this->name} ( {$this->result_from} - {$this->result_to}) ");
    }

}

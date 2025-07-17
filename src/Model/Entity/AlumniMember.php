<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AlumniMember Entity
 */
class AlumniMember extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    /**
     * Virtual fields
     *
     * @var array
     */
    protected $_virtual = ['full_name'];

    /**
     * Getter for full_name virtual field
     *
     * @return string|null
     */
    protected function _getFullName()
    {
        if ($this->first_name !== null && $this->last_name !== null) {
            return $this->first_name . ' ' . $this->last_name;
        }
        return null;
    }
}

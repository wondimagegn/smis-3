<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

class User extends Entity
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
        '*' => true,
        'id' => false,
    ];

    protected $_virtual = ['full_name']; // Define virtual fields

    protected function _getFullName()
    {

        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
        'token',
    ];

    public function parentNode()
    {
        if (!$this->role_id) {
            return null;
        }
        return ['Roles' => ['id' => $this->role_id]];
    }
}

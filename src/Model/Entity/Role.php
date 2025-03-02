<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Role Entity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property int|null $parent_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\ParentRole $parent_role
 * @property \App\Model\Entity\MoodleUser[] $moodle_users
 * @property \App\Model\Entity\PasswordChanageVote[] $password_chanage_votes
 * @property \App\Model\Entity\ChildRole[] $child_roles
 * @property \App\Model\Entity\User[] $users
 */
class Role extends Entity
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
        'description' => true,
        'parent_id' => true,
        'created' => true,
        'modified' => true,
        'parent_role' => true,
        'moodle_users' => true,
        'password_chanage_votes' => true,
        'child_roles' => true,
        'users' => true,
    ];
    /**
     * ACL Behavior parentNode method
     */

    public function parentNode()
    {
        return null;
    }

}

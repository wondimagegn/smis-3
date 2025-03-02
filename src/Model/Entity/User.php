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
        'username' => true,
        'password' => true,
        'force_password_change' => true,
        'last_password_change_date' => true,
        'first_name' => true,
        'middle_name' => true,
        'last_name' => true,
        'email' => true,
        'role_id' => true,
        'is_admin' => true,
        'last_login' => true,
        'failed_login' => true,
        'active' => true,
        'token' => true,
        'token_expires' => true,
        'api_token' => true,
        'activation_date' => true,
        'secret' => true,
        'secret_verified' => true,
        'tos_date' => true,
        'email_verified' => true,
        'last_email_verified_date' => true,
        'created' => true,
        'modified' => true,
        'role' => true,
        'accepted_students' => true,
        'announcements' => true,
        'auto_messages' => true,
        'logs' => true,
        'medical_histories' => true,
        'messages' => true,
        'moodle_users' => true,
        'notes' => true,
        'number_processes' => true,
        'online_users' => true,
        'password_chanage_votes' => true,
        'password_histories' => true,
        'placement_preferences' => true,
        'preference_deadlines' => true,
        'preferences' => true,
        'staff_assignes' => true,
        'staffs' => true,
        'students' => true,
        'user_dorm_assignments' => true,
        'user_meal_assignments' => true,
    ];

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

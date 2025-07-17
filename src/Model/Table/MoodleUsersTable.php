<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class MoodleUsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('moodle_users');
        $this->setDisplayField('username');
        $this->setPrimaryKey('username');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Tables', [
            'foreignKey' => 'table_id',
            'joinType' => 'INNER',
            'className' => 'App\Model\Table\TablesTable',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('username', __('Please provide a valid username.'))
            ->add('username', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => __('This username is already taken.')
            ])
            ->notEmptyString('firstname', __('Please enter first name.'))
            ->notEmptyString('lastname', __('Please enter last name.'))
            ->email('email', false, __('Please provide a valid email address.'))
            ->allowEmptyString('phone')
            ->add('phone', 'length', [
                'rule' => [$this, 'validatePhoneLength'],
                'message' => __('Phone number must be exactly 13 characters (e.g., +251123456789).')
            ])
            ->numeric('user_id', __('User ID must be a valid number.'))
            ->notEmptyString('user_id', __('Please provide a valid user ID for Moodle user.'))
            ->add('user_id', 'exists', [
                'rule' => ['existsIn', 'user_id', 'Users'],
                'message' => __('User ID must reference an existing user.')
            ])
            ->numeric('role_id', __('Role ID must be a valid number.'))
            ->notEmptyString('role_id', __('Please provide a valid role ID.'))
            ->add('role_id', 'exists', [
                'rule' => ['existsIn', 'role_id', 'Roles'],
                'message' => __('Role ID must reference an existing role.')
            ])
            ->numeric('table_id', __('Table ID must be a valid number.'))
            ->notEmptyString('table_id', __('Please provide a valid table ID.'))
            ->add('table_id', 'exists', [
                'rule' => ['existsIn', 'table_id', 'Tables'],
                'message' => __('Table ID must reference an existing table.')
            ]);

        return $validator;
    }

    /**
     * Custom validation rule to check phone number length.
     *
     * @param string|null $value The phone number value.
     * @param array $context The validation context.
     * @return bool
     */
    public function validatePhoneLength(?string $value, array $context): bool
    {
        if (empty($value)) {
            return true; // Allow empty phone (handled by allowEmptyString)
        }

        return strlen($value) === 13;
    }
}

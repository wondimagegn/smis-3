<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class SecuritysettingsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('securitysettings');
        $this->setPrimaryKey('id');

        // Add Timestamp behavior for tracking created/modified times
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('session_duration', 'The session duration should be numeric.')
            ->notEmptyString('session_duration', 'Session duration is required.');

        $validator
            ->integer('minimum_password_length', 'The minimum password length should be numeric.')
            ->notEmptyString('minimum_password_length', 'Minimum password length is required.');

        $validator
            ->integer('maximum_password_length', 'The maximum password length should be numeric.')
            ->notEmptyString('maximum_password_length', 'Maximum password length is required.');

        $validator
            ->integer('password_duration', 'The password duration should be numeric.')
            ->notEmptyString('password_duration', 'Password duration is required.');

        $validator
            ->boolean('previous_password_use_allowance', 'Invalid boolean value.');

        $validator
            ->integer('number_of_login_attempt', 'The number of login attempts should be numeric.')
            ->notEmptyString('number_of_login_attempt', 'Number of login attempts is required.');

        $validator
            ->integer('falsify_duration', 'The falsify duration should be numeric.')
            ->notEmptyString('falsify_duration', 'Falsify duration is required.');

        return $validator;
    }
}

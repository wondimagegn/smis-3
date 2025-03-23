<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
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
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('moodle_users');
        $this->setDisplayField('username');
        $this->setPrimaryKey('username');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
        ]);
        $this->belongsTo('Tables', [
            'foreignKey' => 'table_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('firstname', 'Please enter first name.')
            ->notEmptyString('lastname', 'Please enter last name.')
            ->email('email', false, 'Please provide a valid email address.')
            ->notEmptyString('user_id', 'Please provide a valid user ID for Moodle user.');

        return $validator;
    }


    function checkLengthPhone($data, $fieldName)
    {

        $valid = true;
        if (isset($fieldName) && $this->hasField($fieldName)) {
            $check = strlen($data[$fieldName]);
            debug($check);
            if ($check != 13) {
                $valid = false;
            }
        }
        return $valid;
    }
}

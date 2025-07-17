<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AlumniMembers Table
 */
class AlumniMembersTable extends Table
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

        $this->setTable('alumni_members');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create')
            ->scalar('first_name')
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name', 'Please provide first name.')
            ->scalar('last_name')
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name', 'Please provide last name.')
            ->email('email', false, 'Please enter a valid email address.')
            ->requirePresence('email', 'create')
            ->notEmptyString('email', 'Please enter a valid email address.')
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'The email address is used by someone. Please provide a unique different email.'
            ])
            ->scalar('gradution')
            ->requirePresence('gradution', 'create')
            ->notEmptyString('gradution', 'Please provide gradution.')
            ->scalar('gender')
            ->requirePresence('gender', 'create')
            ->notEmptyString('gender', 'Please select gender.')
            ->scalar('phone')
            ->requirePresence('phone', 'create')
            ->notEmptyString('phone', 'Please provide phone number.')
            ->scalar('institute_college')
            ->requirePresence('institute_college', 'create')
            ->notEmptyString('institute_college', 'Please provide college.')
            ->scalar('department')
            ->requirePresence('department', 'create')
            ->notEmptyString('department', 'Please provide department.')
            ->scalar('program')
            ->requirePresence('program', 'create')
            ->notEmptyString('program', 'Please provide program.');

        return $validator;
    }

    /**
     * Generates the next tracking number
     *
     * @return int Next tracking number
     */
    public function nextTrackingNumber()
    {
        $latest = $this->find()
            ->select(['trackingnumber'])
            ->order(['AlumniMembers.created' => 'DESC'])
            ->first();

        if ($latest && !empty($latest->trackingnumber)) {
            return $latest->trackingnumber + 1;
        }

        return 20011;
    }
}

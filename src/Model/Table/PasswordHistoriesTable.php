<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PasswordHistoriesTable extends Table
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

        $this->setTable('password_histories');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {

        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    public function isThePasswordUsedBefore($user_id = null, $password = null)
    {

        $passwordHistories = $this->find(
            'all',
            array(
                'conditions' =>
                    array(
                        'PasswordHistory.user_id' => $user_id,
                    )
            )
        );
        $user = $this->User->find(
            'first',
            array(
                'conditions' =>
                    array(
                        'User.id' => $user_id
                    )
            )
        );
        $password = Security::hash($password, null, true);
        foreach ($passwordHistories as $passwordHistory) {
            if (strcmp($passwordHistory['PasswordHistory']['password'], $password) == 0) {
                return true;
            }
        }
        if (strcmp($user['User']['password'], $password) == 0) {
            return true;
        }
        return false;
    }
}

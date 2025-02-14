<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Utility\Text;


class RolesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('roles');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        // Define Associations
        $this->hasMany('Users', [
            'foreignKey' => 'role_id',
        ]);

        $this->hasMany('PasswordChangeVotes', [
            'className' => 'PasswordChangeVotes',
            'foreignKey' => 'role_id',
        ]);

        // Enable ACL Behavior
        $this->addBehavior('Acl.Acl', ['type' => 'requester']);
    }
    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('name', 'create')
            ->notEmptyString('name', 'Role name is required.')
            ->maxLength('name', 255, 'Role name cannot exceed 255 characters.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['name'], 'This role name is already taken.'));

        return $rules;
    }

    public function parentNode(EntityInterface $entity = null)
    {
        return null;
    }
}

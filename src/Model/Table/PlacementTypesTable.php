<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PlacementTypesTable extends Table
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

        $this->setTable('placement_types');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'placement_type_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('code', 'Code is required')
            ->add('code', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'The code should be unique. This code is already taken. Try another one.'
            ]);

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        // Ensure 'code' is unique
        $rules->add($rules->isUnique(['code'], 'The code should be unique. This code is already taken. Try another one.'));

        return $rules;
    }

    function checkUniqueCode()
    {
        $count = 0;

        if (!empty($this->data['PlacementType']['id'])) {
            $count = $this->find('count', array('conditions' => array('PlacementType.id <> ' => $this->data['PlacementType']['id'], 'PlacementType.code' => trim($this->data['PlacementType']['code']))));
        } else {
            $count = $this->find('count', array('conditions' => array('PlacementType.code' => trim($this->data['PlacementType']['code']))));
        }

        if ($count > 0) {
            return false;
        }

        return true;
    }

    function canItBeDeleted($id = null)
    {
        if (ClassRegistry::init('AcceptedStudent')->find('count', array('conditions' => array('AcceptedStudent.placement_type_id' => $id))) > 0) {
            return false;
        } else {
            return true;
        }
    }
}

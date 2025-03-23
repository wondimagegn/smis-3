<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PositionsTable extends Table
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

        $this->setTable('positions');
        $this->setDisplayField('position');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('ServiceWings', [
            'foreignKey' => 'service_wing_id',
        ]);
        $this->hasMany('Staffs', [
            'foreignKey' => 'position_id',
        ]);
    }
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('position', 'Provide position name.')
            ->add('position', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'Position name already recorded. Use another'
            ]);

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        // Ensure 'position' is unique
        $rules->add($rules->isUnique(['position'], 'Position name already recorded. Use another'));

        return $rules;
    }
}

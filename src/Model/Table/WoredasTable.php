<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class WoredasTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('woredas');
        $this->setPrimaryKey('id');

        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'woreda_id',
        ]);

        $this->hasMany('Contacts', [
            'foreignKey' => 'woreda_id',
        ]);

        $this->hasMany('Staffs', [
            'foreignKey' => 'woreda_id',
        ]);

        $this->hasMany('Students', [
            'foreignKey' => 'woreda_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('name', 'Provide woreda name.')
            ->notEmptyString('code', 'Provide woreda code.')
            ->notEmptyString('zone_id', 'Please select a zone.')
            ->add('zone_id', 'numeric', [
                'rule' => 'numeric',
                'message' => 'Zone ID must be numeric.'
            ]);

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        // Ensure uniqueness of woreda name within a zone
        $rules->add($rules->isUnique(
            ['name', 'zone_id'],
            'The woreda name must be unique in the selected zone.'
        ));

        // Ensure uniqueness of woreda code within a zone
        $rules->add($rules->isUnique(
            ['code', 'zone_id'],
            'The woreda code must be unique in the selected zone.'
        ));

        return $rules;
    }

    public function canItBeDeleted($woreda_id)
    {
        return !$this->AcceptedStudents->exists(['woreda_id' => $woreda_id]) &&
            !$this->Contacts->exists(['woreda_id' => $woreda_id]) &&
            !$this->Staffs->exists(['woreda_id' => $woreda_id]) &&
            !$this->Students->exists(['woreda_id' => $woreda_id]);
    }
}

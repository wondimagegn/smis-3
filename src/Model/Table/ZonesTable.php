<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ZonesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('zones');
        $this->setPrimaryKey('id');

        $this->belongsTo('Regions', [
            'foreignKey' => 'region_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'zone_id',
        ]);

        $this->hasMany('Cities', [
            'foreignKey' => 'zone_id',
        ]);

        $this->hasMany('Contacts', [
            'foreignKey' => 'zone_id',
        ]);

        $this->hasMany('Staffs', [
            'foreignKey' => 'zone_id',
        ]);

        $this->hasMany('Students', [
            'foreignKey' => 'zone_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('name', 'Provide zone name.')
            ->add('name', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'The zone name must be unique in the selected region.',
            ])
            ->notEmptyString('short', 'Provide zone short name.')
            ->add('short', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'The zone short name must be unique in the given region.',
            ])
            ->notEmptyString('region_id', 'Please select a region.')
            ->numeric('region_id', 'Region ID must be numeric.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['name', 'region_id'], 'The zone name must be unique in the selected region.'));
        $rules->add($rules->isUnique(['short', 'region_id'], 'The zone short name must be unique in the given region.'));

        return $rules;
    }

    /**
     * Checks if the zone can be deleted (ensures no dependent records exist).
     */
    public function canItBeDeleted($zoneId)
    {
        $relatedModels = ['Students', 'Contacts', 'AcceptedStudents', 'Staffs'];

        foreach ($relatedModels as $model) {
            if ($this->$model->exists(['zone_id' => $zoneId])) {
                return false;
            }
        }
        return true;
    }

}

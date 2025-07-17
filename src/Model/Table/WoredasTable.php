<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Woredas Table
 */
class WoredasTable extends Table
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

        $this->setTable('woredas');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
            'joinType' => 'LEFT'
        ]);

        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'woreda_id',
            'dependent' => false
        ]);

        $this->hasMany('Contacts', [
            'foreignKey' => 'woreda_id',
            'dependent' => false
        ]);

        $this->hasMany('Staffs', [
            'foreignKey' => 'woreda_id',
            'dependent' => false
        ]);

        $this->hasMany('Students', [
            'foreignKey' => 'woreda_id',
            'propertyName' => 'woreda_record', // Avoids conflict with 'woreda' field in students table
            'dependent' => false
        ]);
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
            ->scalar('name')
            ->requirePresence('name', 'create')
            ->notEmptyString('name', 'Provide woreda name.')
            ->add('name', 'isUniqueWoredaInZone', [
                'rule' => function ($value, $context) {
                    $conditions = [
                        'Woredas.zone_id' => $context['data']['zone_id'] ?? null,
                        'Woredas.name' => trim($value)
                    ];
                    if (!empty($context['data']['id'])) {
                        $conditions['Woredas.id !='] = $context['data']['id'];
                    }
                    $count = $this->find()
                        ->where($conditions)
                        ->count();
                    return $count === 0;
                },
                'message' => 'The woreda name must be unique in the selected zone. The name is already taken. Use another one.'
            ])
            ->scalar('code')
            ->requirePresence('code', 'create')
            ->notEmptyString('code', 'Provide woreda code.')
            ->add('code', 'isUniqueWoredaCode', [
                'rule' => function ($value, $context) {
                    $conditions = [
                        'Woredas.code' => $value,
                        'Woredas.zone_id' => $context['data']['zone_id'] ?? null
                    ];
                    if (!empty($context['data']['id'])) {
                        $conditions['Woredas.id !='] = $context['data']['id'];
                    }
                    $count = $this->find()
                        ->where($conditions)
                        ->count();
                    return $count === 0;
                },
                'message' => 'The woreda code must be unique in the given zone. The code is already taken. Use another one.'
            ])
            ->integer('zone_id')
            ->requirePresence('zone_id', 'create')
            ->notEmptyString('zone_id', 'Please Select Zone.');

        return $validator;
    }

    /**
     * Checks if a woreda can be deleted based on associated records
     *
     * @param int|null $woredaId Woreda ID
     * @return bool True if can be deleted, false otherwise
     */
    public function canItBeDeleted($woredaId = null): bool
    {
        if (!$woredaId) {
            return false;
        }

        if ($this->Students->find()->where(['Students.woreda_id' => $woredaId])->count() > 0) {
            return false;
        }

        if ($this->Contacts->find()->where(['Contacts.woreda_id' => $woredaId])->count() > 0) {
            return false;
        }

        if ($this->AcceptedStudents->find()->where(['AcceptedStudents.woreda_id' => $woredaId])->count() > 0) {
            return false;
        }

        if ($this->Staffs->find()->where(['Staffs.woreda_id' => $woredaId])->count() > 0) {
            return false;
        }

        return true;
    }
}

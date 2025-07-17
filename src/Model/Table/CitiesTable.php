<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Cities Table
 */
class CitiesTable extends Table
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

        $this->setTable('cities');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Regions', [
            'foreignKey' => 'region_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
            'joinType' => 'LEFT',
        ]);

        $this->hasMany('Contacts', [
            'foreignKey' => 'city_id',
            'dependent' => false,
        ]);

        $this->hasMany('Staffs', [
            'foreignKey' => 'city_id',
            'dependent' => false,
        ]);

        $this->hasMany('Students', [
            'foreignKey' => 'city_id',
            'dependent' => false,
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
            ->notEmptyString('name', 'Provide City name.')
            ->add('name', 'isUniqueCityInRegion', [
                'rule' => function ($value, $context) {
                    $conditions = [
                        'Cities.region_id' => $context['data']['region_id'] ?? null,
                        'Cities.name' => trim($value)
                    ];
                    if (!empty($context['data']['id'])) {
                        $conditions['Cities.id !='] = $context['data']['id'];
                    }
                    $count = $this->find()
                        ->where($conditions)
                        ->count();
                    return $count === 0;
                },
                'message' => 'The city name should be unique in the selected region. The name is already taken. Use another one.'
            ])
            ->add('name', 'isUniqueCityInZone', [
                'rule' => function ($value, $context) {
                    $conditions = [
                        'Cities.zone_id' => $context['data']['zone_id'] ?? null,
                        'Cities.name' => trim($value)
                    ];
                    if (!empty($context['data']['id'])) {
                        $conditions['Cities.id !='] = $context['data']['id'];
                    }
                    $count = $this->find()
                        ->where($conditions)
                        ->count();
                    return $count === 0;
                },
                'message' => 'The city name should be unique in the selected zone. The name is already taken. Use another one.'
            ])
            ->scalar('short')
            ->requirePresence('short', 'create')
            ->notEmptyString('short', 'Provide city short name.')
            ->add('short', 'isUniqueCityCode', [
                'rule' => function ($value, $context) {
                    $conditions = [
                        'Cities.short IS NOT NULL',
                        'Cities.short' => $value
                    ];
                    if (!empty($context['data']['id'])) {
                        $conditions['Cities.id !='] = $context['data']['id'];
                    }
                    $count = $this->find()
                        ->where($conditions)
                        ->count();
                    return $count === 0;
                },
                'message' => 'The city short name must be unique. The short name is already taken. Use another one.'
            ]);

        return $validator;
    }

    /**
     * Checks if a city can be deleted based on associated records
     *
     * @param int|null $cityId City ID
     * @return bool True if can be deleted, false otherwise
     */
    public function canItBeDeleted($cityId = null)
    {
        if (!$cityId) {
            return false;
        }

        if ($this->Students->find()->where(['Students.city_id' => $cityId])->count() > 0) {
            return false;
        }

        if ($this->Contacts->find()->where(['Contacts.city_id' => $cityId])->count() > 0) {
            return false;
        }

        if ($this->Staffs->find()->where(['Staffs.city_id' => $cityId])->count() > 0) {
            return false;
        }

        return true;
    }
}

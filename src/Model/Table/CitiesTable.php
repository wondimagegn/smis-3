<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

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

        $this->addBehavior('Timestamp');

        $this->belongsTo('Regions', [
            'foreignKey' => 'region_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
        ]);
        $this->hasMany('Contacts', [
            'foreignKey' => 'city_id',
        ]);
        $this->hasMany('Staffs', [
            'foreignKey' => 'city_id',
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'city_id',
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
            ->scalar('name')
            ->maxLength('name', 64)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('city')
            ->maxLength('city', 200)
            ->allowEmptyString('city');

        $validator
            ->scalar('short')
            ->maxLength('short', 10)
            ->allowEmptyString('short');

        $validator
            ->scalar('city_2nd_language')
            ->maxLength('city_2nd_language', 200)
            ->allowEmptyString('city_2nd_language');

        $validator
            ->integer('priority_order')
            ->allowEmptyString('priority_order');

        $validator
            ->notEmptyString('active');

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
        $rules->add($rules->existsIn(['region_id'], 'Regions'));
        $rules->add($rules->existsIn(['zone_id'], 'Zones'));

        return $rules;
    }

    function isUniqueCityInRegion()
    {
        $count = 0;

        if (!empty($this->data['City']['id'])) {
            $count = $this->find('count', array('conditions' => array('City.region_id' => $this->data['City']['region_id'], 'City.name' => $this->data['City']['name'], 'City.id <>' => $this->data['City']['id'])));
        } else {
            $count = $this->find('count', array('conditions' => array('City.region_id' => $this->data['City']['region_id'], 'City.name' => $this->data['City']['name'])));
        }

        if ($count > 0) {
            return false;
        }
        return true;
    }

    function isUniqueCityInZone()
    {
        $count = 0;

        if (!empty($this->data['City']['id'])) {
            $count = $this->find('count', array('conditions' => array('City.zone_id' => $this->data['City']['zone_id'], 'City.name' => $this->data['City']['name'], 'City.id <>' => $this->data['City']['id'])));
        } else {
            $count = $this->find('count', array('conditions' => array('City.zone_id' => $this->data['City']['zone_id'], 'City.name' => $this->data['City']['name'])));
        }

        if ($count > 0) {
            return false;
        }
        return true;
    }

    function isUniqueCityCode()
    {
        $count = 0;

        if (!empty($this->data['Zone']['id'])) {
            $count = $this->find('count', array('conditions' => array('City.short IS NOT NULL', 'City.short' => $this->data['City']['short'], 'City.id <> ' => $this->data['City']['id'])));
        } else {
            $count = $this->find('count', array('conditions' => array('City.short IS NOT NULL', 'City.short' => $this->data['City']['short'])));
        }

        if ($count > 0) {
            return false;
        }
        return true;
    }

    function canItBeDeleted($city = null)
    {
        if ($this->Student->find('count', array('conditions' => array('Student.city_id' => $city))) > 0) {
            return false;
        } else if ($this->Contact->find('count', array('conditions' => array('Contact.city_id' => $city))) > 0) {
            return false;
        } else if ($this->Staff->find('count', array('conditions' => array('Staff.city_id' => $city))) > 0) {
            return false;
        } else {
            return true;
        }
    }
}

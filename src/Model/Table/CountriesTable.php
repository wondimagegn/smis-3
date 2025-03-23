<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class CountriesTable extends Table
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

        $this->setTable('countries');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Contacts', [
            'foreignKey' => 'country_id',
        ]);
        $this->hasMany('Regions', [
            'foreignKey' => 'country_id',
        ]);
        $this->hasMany('StaffStudies', [
            'foreignKey' => 'country_id',
        ]);
        $this->hasMany('Staffs', [
            'foreignKey' => 'country_id',
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'country_id',
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
            ->maxLength('name', 50)
            ->allowEmptyString('name');

        $validator
            ->scalar('code')
            ->maxLength('code', 3)
            ->allowEmptyString('code');

        return $validator;
    }

    public function checkUnique()
    {

        $count = 0;
        if (!empty($this->data['Country']['id'])) {
            $count = $this->find(
                'count',
                array(
                    'conditions' => array(
                        'Country.id <> ' => $this->data['Country']['id'],
                        'Country.name' => trim($this->data['Country']['name'])
                    )
                )
            );
        } else {
            $count = $this->find(
                'count',
                array('conditions' => array('Country.name' => trim($this->data['Country']['name'])))
            );
        }

        if ($count > 0) {
            return false;
        }
        return true;
    }

    public function canItBeDeleted($country_id = null)
    {

        if (
            $this->Student->find('count', array(
                'conditions' => array(
                    'Student.country_id'
                    => $country_id
                )
            )) > 0
        ) {
            return false;
        } elseif (
            $this->Contact->find('count', array(
                'conditions' =>
                    array('Contact.country_id' => $country_id)
            )) > 0
        ) {
            return false;
        } elseif (
            $this->Region->find('count', array(
                'conditions' =>
                    array('Region.country_id' => $country_id)
            )) > 0
        ) {
            return false;
        } else {
            return true;
        }
    }
}

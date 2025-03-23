<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class DormitoryBlocksTable extends Table
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

        $this->setTable('dormitory_blocks');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Campuses', [
            'foreignKey' => 'campus_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Dormitories', [
            'foreignKey' => 'dormitory_block_id',
        ]);
        $this->hasMany('UserDormAssignments', [
            'foreignKey' => 'dormitory_block_id',
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
            ->scalar('block_name')
            ->maxLength('block_name', 6)
            ->requirePresence('block_name', 'create')
            ->notEmptyString('block_name');

        $validator
            ->scalar('type')
            ->maxLength('type', 6)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->scalar('location')
            ->maxLength('location', 16777215)
            ->allowEmptyString('location');

        $validator
            ->scalar('telephone_number')
            ->maxLength('telephone_number', 15)
            ->allowEmptyString('telephone_number');

        $validator
            ->scalar('alt_telephone_number')
            ->maxLength('alt_telephone_number', 15)
            ->allowEmptyString('alt_telephone_number');

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

        $rules->add($rules->existsIn(['campus_id'], 'Campuses'));

        return $rules;
    }

    public function get_floor_data($number = null)
    {

        $floor_data = array();
        if ($number >= 1) {
            $floor_data[1] = "Ground Floor";
            for ($i = 2; $i <= $number; $i++) {
                if ($i == 2) {
                    $floor_data[$i] = ($i - 1) . "st Floor";
                } elseif ($i == 3) {
                    $floor_data[$i] = ($i - 1) . "nd Floor";
                } elseif ($i == 4) {
                    $floor_data[$i] = ($i - 1) . "rd Floor";
                } else {
                    $floor_data[$i] = ($i - 1) . "th Floor";
                }
            }
            return $floor_data;
        }
    }

    public function send_dormitory_block_data()
    {

        return $this->data;
    }

    public function getDormitoryBlock()
    {

        $dormitoryBlocks = $this->find('all', array('contain' => array('Campus')));

        $reformateBlocks = array();

        foreach ($dormitoryBlocks as $in => $name) {
            $reformateBlocks[$name['Campus']['name']][$name['DormitoryBlock']['id']] = $name['DormitoryBlock']['block_name'] . '-' .
                $name['DormitoryBlock']['type'];
        }
        return $reformateBlocks;
    }
}

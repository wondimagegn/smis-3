<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class RegionsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('regions');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        // Define associations
        $this->belongsTo('Countries', [
            'foreignKey' => 'country_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'region_id',
        ]);

        $this->hasMany('Cities', [
            'foreignKey' => 'region_id',
        ]);

        $this->hasMany('Contacts', [
            'foreignKey' => 'region_id',
        ]);

        $this->hasMany('Staffs', [
            'foreignKey' => 'region_id',
        ]);

        $this->hasMany('Students', [
            'foreignKey' => 'region_id',
        ]);

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('name', 'create')
            ->notEmptyString('name', 'Provide region name.')
            ->add('name', 'unique', [
                'rule' => [$this, 'isUniqueRegionInCountry'],
                'message' => 'The region name must be unique in the selected country.',
            ]);

        $validator
            ->requirePresence('short', 'create')
            ->notEmptyString('short', 'Provide region short name.')
            ->add('short', 'unique', [
                'rule' => [$this, 'isUniqueRegionCode'],
                'message' => 'The region short name must be unique.',
            ]);

        $validator
            ->requirePresence('country_id', 'create')
            ->notEmptyString('country_id', 'Please select a country.')
            ->add('country_id', 'numeric', [
                'rule' => 'numeric',
                'message' => 'Country ID must be a number.',
            ]);

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['country_id'], 'Countries'));
        return $rules;
    }

    public function isUniqueRegionInCountry($value, $context)
    {
        return !$this->exists([
            'name' => $value,
            'country_id' => $context['data']['country_id'] ?? null,
        ]);
    }

    public function isUniqueRegionCode($value, $context)
    {
        return !$this->exists(['short' => $value]);
    }

    public function canItBeDeleted($region_id = null)
    {
        if ($this->Student->find('count', array('conditions' => array('Student.region_id' => $region_id))) > 0) {
            return false;
        } else if ($this->Contact->find('count', array('conditions' => array('Contact.region_id' => $region_id))) > 0) {
            return false;
        } else if ($this->AcceptedStudent->find('count', array('conditions' => array('AcceptedStudent.region_id' => $region_id))) > 0) {
            return false;
        } else if ($this->Staff->find('count', array('conditions' => array('Staff.region_id' => $region_id))) > 0) {
            return false;
        } else {
            return true;
        }
    }

}

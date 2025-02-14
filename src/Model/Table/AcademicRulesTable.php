<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AcademicRulesTable extends Table
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

        $this->setTable('academic_rules');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('AcademicStands', [
            'foreignKey' => 'academic_stand_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsToMany('AcademicStands', [
            'foreignKey' => 'academic_rule_id',
            'targetForeignKey' => 'academic_stand_id',
            'joinTable' => 'academic_stands_academic_rules',
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
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('scmo')
            ->maxLength('scmo', 4)
            ->requirePresence('scmo', 'create')
            ->notEmptyString('scmo');

        $validator
            ->numeric('sgpa')
            ->requirePresence('sgpa', 'create')
            ->notEmptyString('sgpa');

        $validator
            ->scalar('operatorI')
            ->maxLength('operatorI', 3)
            ->requirePresence('operatorI', 'create')
            ->notEmptyString('operatorI');

        $validator
            ->scalar('ccmo')
            ->maxLength('ccmo', 4)
            ->requirePresence('ccmo', 'create')
            ->notEmptyString('ccmo');

        $validator
            ->numeric('cgpa')
            ->requirePresence('cgpa', 'create')
            ->notEmptyString('cgpa');

        $validator
            ->scalar('operatorII')
            ->maxLength('operatorII', 3)
            ->requirePresence('operatorII', 'create')
            ->notEmptyString('operatorII');

        $validator
            ->boolean('tcw')
            ->requirePresence('tcw', 'create')
            ->notEmptyString('tcw');

        $validator
            ->scalar('operatorIII')
            ->maxLength('operatorIII', 3)
            ->requirePresence('operatorIII', 'create')
            ->notEmptyString('operatorIII');

        $validator
            ->boolean('pfw')
            ->requirePresence('pfw', 'create')
            ->notEmptyString('pfw');

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
        $rules->add($rules->existsIn(['academic_stand_id'], 'AcademicStands'));

        return $rules;
    }

    function checkExeclusiveNessOFGradeRule($academic_year=null) {

        /*$already_recorded_range=$this->find('all',
                     array('conditions'=>array(
                 'AcademicStand.academic_year_from'=>$academic_year)));*/

        $already_recorded_range=$this->AcademicStand->find('all',
            array('conditions'=>array('AcademicStand.academic_year_from'=>$academic_year)));
        debug($already_recorded_range);
        /*
        foreach($already_recorded_range as $ar=>$sr) {
            $sr = $sr['PlacementsResultsCriteria'];
            //debug($sr);
                 if( ($data['result_from']<=$sr['result_from'] && $sr['result_from'] <=$data['result_to'])
                 || ($data['result_from']<=$sr['result_to'] && $sr['result_to'] <=$data['result_to'])
                 || ($sr['result_from']<=$data['result_from'] && $data['result_to'] <= $sr['result_to'])
                 || $data['result_from']<=$sr['result_from'] && $sr['result_to'] <= $data['result_to']){

                  $this->invalidate('result_from_to',
            'The given grade range is not uniqe. Please make sure that "result from" and/or "result to" is
            not already recorded.');
                  return false;
                 }
        }
        */

    }
}

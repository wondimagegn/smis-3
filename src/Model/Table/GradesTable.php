<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class GradesTable extends Table
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

        $this->setTable('grades');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('GradeTypes', [
            'foreignKey' => 'grade_type_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('GradeScaleDetails', [
            'foreignKey' => 'grade_id',
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
            ->notEmptyString('grade', 'This field can\'t be left blank')

            ->numeric('point_value', 'Numeric value required.')
            ->greaterThanOrEqual('point_value', 0, 'Point value must be greater than or equal to zero.');

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
        $rules->add($rules->existsIn(['grade_type_id'], 'GradeTypes'));

        return $rules;
    }


    function allowDelete($grade_id = null)
    {
        if ($this->GradeScaleDetail->find('count', array('conditions' => array('GradeScaleDetail.grade_id' => $grade_id))) > 0) {
            return false;
        } else {
            return true;
        }
    }

    function checkGradeIsUnique($data = null)
    {
        if (!empty($data)) {

            $frequencey_count = array();
            $point_value_count = array();

            // Count the frequency of grade repeation and display invalidation message if grade is duplicated

            if (!empty($data['Grade'])) {

                foreach ($data['Grade'] as $grade_id => $grade_value) {
                    $frequencey_count[] = $grade_value['grade'];
                    $point_value_count[] = $grade_value['point_value'];
                }

                debug($frequencey_count);
                $how_many_times = array_count_values($frequencey_count);

                if (count($how_many_times) > 0) {
                    foreach ($how_many_times as $grade_id => $frequency) {
                        if ($frequency > 1) {
                            $this->invalidate('checkGradeIsUnique', 'Grade ' . $grade_id . ' is duplicated ' . $frequency . ' times. Please change the grade.');
                            return false;
                        }
                    }
                }

                if ($data['GradeType']['used_in_gpa'] == 1){

                    debug($point_value_count);
                    $how_many_timesPV = array_count_values($point_value_count);

                    if (count($how_many_timesPV) > 0) {
                        foreach ($how_many_timesPV as $point_value => $frequencyPV) {
                            if ($frequencyPV > 1) {
                                $this->invalidate('checkGradeIsUnique', 'Point Value ' . $point_value . ' is duplicated ' . $frequencyPV . ' times. Please change the Point Value in one of the grades.');
                                return false;
                            }
                        }
                    }
                }

                return true;
            }
        }
        return false;
    }
}

<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ExamExcludedDateAndSessionsTable extends Table
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

        $this->setTable('exam_excluded_date_and_sessions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ExamPeriods', [
            'foreignKey' => 'exam_period_id',
            'joinType' => 'INNER',
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
            ->date('excluded_date')
            ->requirePresence('excluded_date', 'create')
            ->notEmptyDate('excluded_date');

        $validator
            ->integer('session')
            ->requirePresence('session', 'create')
            ->notEmptyString('session');

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
        $rules->add($rules->existsIn(['exam_period_id'], 'ExamPeriods'));

        return $rules;
    }


    function count_ExamExcludedDateAndSessions_in_this_ExamPeriod($id=null){

        $count = $this->find('count',array('conditions'=>array('ExamExcludedDateAndSession.exam_period_id'=>$id)));

        return $count;
    }

    function count_ExamExcludedDateAndSessions_out_side_edited_ExamPeriod($id=null,$start_date=null,$end_date=null){

        $count = $this->find('count',array('conditions'=>array('ExamExcludedDateAndSession.exam_period_id'=>$id,"OR"=>array('ExamExcludedDateAndSession.excluded_date <'=>$start_date,'ExamExcludedDateAndSession.excluded_date >'=>$end_date))));

        return $count;
    }
    function get_list_of_exam_period_dates($exam_period_id=null){
        $examPeriods = $this->ExamPeriod->find('first',array('conditions'=>array('ExamPeriod.id'=>$exam_period_id),'recursive'=>-1));
        $start_date = $examPeriods['ExamPeriod']['start_date'];
        $end_date = $examPeriods['ExamPeriod']['end_date'];
        $start_date = strtotime($start_date); // Convert date to a UNIX timestamp
        $end_date = strtotime($end_date); // Convert date to a UNIX timestamp
        $date_array = array();
        // Loop from the start date to end date and output all dates inbetween
        for($i=$start_date; $i<=$end_date; $i+=86400) {
            $date_array[] = date("Y-m-d", $i);
        }
        return $date_array;
    }

    function get_already_excluded_date_and_session($exam_period_id=null){
        if(!empty($exam_period_id)){
            $already_excluded_date_and_session_array = array();
            $examExcludedDateAndSessions = $this->find('all',array('conditions'=>array('ExamExcludedDateAndSession.exam_period_id'=>$exam_period_id),'order'=>array('ExamExcludedDateAndSession.excluded_date','ExamExcludedDateAndSession.session')));
            $excluded_session_by_date = array();
            foreach($examExcludedDateAndSessions as $examExcludedDateAndSession){
                $excluded_session_by_date[$examExcludedDateAndSession['ExamExcludedDateAndSession']['excluded_date']][$examExcludedDateAndSession['ExamExcludedDateAndSession']['session']] = $examExcludedDateAndSession['ExamExcludedDateAndSession']['session'];
            }
            $already_excluded_date_and_session_array[0]=$examExcludedDateAndSessions;
            $already_excluded_date_and_session_array[1]=$excluded_session_by_date;
            return $already_excluded_date_and_session_array;
        }
    }
}

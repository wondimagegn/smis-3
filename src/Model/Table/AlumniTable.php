<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AlumniTable extends Table
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

        $this->setTable('alumni');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
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
            ->scalar('full_name')
            ->maxLength('full_name', 200)
            ->requirePresence('full_name', 'create')
            ->notEmptyString('full_name');

        $validator
            ->scalar('father_name')
            ->maxLength('father_name', 200)
            ->requirePresence('father_name', 'create')
            ->notEmptyString('father_name');

        $validator
            ->scalar('region')
            ->maxLength('region', 50)
            ->requirePresence('region', 'create')
            ->notEmptyString('region');

        $validator
            ->scalar('woreda')
            ->maxLength('woreda', 50)
            ->requirePresence('woreda', 'create')
            ->notEmptyString('woreda');

        $validator
            ->scalar('kebele')
            ->maxLength('kebele', 50)
            ->requirePresence('kebele', 'create')
            ->notEmptyString('kebele');

        $validator
            ->scalar('housenumber')
            ->maxLength('housenumber', 50)
            ->allowEmptyString('housenumber');

        $validator
            ->scalar('mobile')
            ->maxLength('mobile', 15)
            ->requirePresence('mobile', 'create')
            ->notEmptyString('mobile');

        $validator
            ->scalar('home_second_phone')
            ->maxLength('home_second_phone', 15)
            ->allowEmptyString('home_second_phone');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('facebookaddress')
            ->maxLength('facebookaddress', 200)
            ->allowEmptyString('facebookaddress');

        $validator
            ->scalar('studentnumber')
            ->maxLength('studentnumber', 200)
            ->requirePresence('studentnumber', 'create')
            ->notEmptyString('studentnumber');

        $validator
            ->scalar('sex')
            ->maxLength('sex', 6)
            ->requirePresence('sex', 'create')
            ->notEmptyString('sex');

        $validator
            ->scalar('placeofbirthregion')
            ->maxLength('placeofbirthregion', 20)
            ->requirePresence('placeofbirthregion', 'create')
            ->notEmptyString('placeofbirthregion');

        $validator
            ->scalar('placeofbirthworeda')
            ->maxLength('placeofbirthworeda', 20)
            ->allowEmptyString('placeofbirthworeda');

        $validator
            ->scalar('fieldofstudy')
            ->maxLength('fieldofstudy', 200)
            ->requirePresence('fieldofstudy', 'create')
            ->notEmptyString('fieldofstudy');

        $validator
            ->integer('age')
            ->requirePresence('age', 'create')
            ->notEmptyString('age');

        $validator
            ->scalar('gradution_academic_year')
            ->maxLength('gradution_academic_year', 10)
            ->requirePresence('gradution_academic_year', 'create')
            ->notEmptyString('gradution_academic_year');

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
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['student_id'], 'Students'));

        return $rules;
    }


    public function formatResponse($data){
        $formattedData=array();
        $formattedData['Alumnus']=$data['Alumnus'];
        $count=0;
        foreach($data['AlumniResponse'] as $k=>$v){
            if(isset($v['answer']['mother']) && !empty($v['answer']['mother']) && isset($v['answer']['father']) &&
                !empty($v['answer']['father'])){
                $formattedData['AlumniResponse'][$count]['survey_question_id']=$v['survey_question_id'];
                $formattedData['AlumniResponse'][$count]['mother']=1;
                $formattedData['AlumniResponse'][$count]['survey_question_answer_id']=$v['answer']['mother'];

                $count++;
                $formattedData['AlumniResponse'][$count]['survey_question_id']=$v['survey_question_id'];
                $formattedData['AlumniResponse'][$count]['father']=1;
                $formattedData['AlumniResponse'][$count]['survey_question_answer_id']=$v['answer']['father'];

            } else {
                if(empty($v['answer'])){
                    $formattedData['AlumniResponse'][$count]['survey_question_id']=$v['survey_question_id'];

                    $formattedData['AlumniResponse'][$count]['specifiy']=$v['specifiy'];
                } else if(is_array($v['answer'])) {
                    foreach($v['answer'] as $ak=>$av){
                        if($av==1){
                            $formattedData['AlumniResponse'][$count]['survey_question_id']=$v['survey_question_id'];

                            $formattedData['AlumniResponse'][$count]['specifiy']=$v['specifiy'];
                            $formattedData['AlumniResponse'][$count]['survey_question_answer_id']=$ak;
                            $count++;
                        }

                    }
                } else if(!empty($v['answer']) && !is_array($v['answer'])){
                    $formattedData['AlumniResponse'][$count]['survey_question_id']=$v['survey_question_id'];

                    $formattedData['AlumniResponse'][$count]['specifiy']=$v['specifiy'];
                    $formattedData['AlumniResponse'][$count]['survey_question_answer_id']=$v['answer'];
                }
            }
            $count++;
        }

        return $formattedData;

    }

    function completedRoundOneQuestionner($student_id){
        $surveyQuestions=ClassRegistry::init('SurveyQuestion')->find('all',
            array('contain'=>array('SurveyQuestionAnswer')));
        $alumni_id=$this->find('first',array('conditions'=>array('Alumnus.student_id'=>$student_id),'recursive'=>-1));
        if(empty($alumni_id['Alumnus']['student_id'])){
            return false;
        } else {
            return true;
        }

        if(!empty($surveyQuestions)){

            foreach($surveyQuestions as $k=>$v){

                $response=ClassRegistry::init('AlumniResponse')->find('count',
                    array('conditions'=>array('AlumniResponse.alumni_id'=>$alumni_id['Alumnus']['id'],
                        'AlumniResponse.survey_question_id'=>$v['SurveyQuestion']['id'])));
                if(empty($response)){
                    debug($v['SurveyQuestion']['id']);
                    return false;
                }
            }
            return true;
        }
        return false;
    }
    public function getSelectedAlumniSurvey($student_ids){
        $alumniresponse=$this->find('all',array('conditions'=>array('Alumnus.student_id'=>$student_ids),'contain'=>array('AlumniResponse'=>array('SurveyQuestion','SurveyQuestionAnswer'))));
        return $alumniresponse;

    }
    public function getCompletedSurvey($student_ids){
        $alumniresponse=$this->find('all',array('conditions'=>array('Alumnus.student_id'=>$student_ids),'contain'=>array('AlumniResponse'=>array('SurveyQuestion','SurveyQuestionAnswer'))));
        $student=array();
        foreach($alumniresponse as $k=>$v){
            foreach($v['AlumniResponse'] as $alk=>$alv){

                if($alv['mother']==1){
                    $student[$v['Alumnus']['full_name'].'~'.$v['Alumnus']['student_id']][$alv['survey_question_id']]['mother']=$alv;
                } else if ($alv['father']==1){
                    $student[$v['Alumnus']['full_name'].'~'.$v['Alumnus']['student_id']][$alv['survey_question_id']]['father']=$alv;
                } else {
                    if($alv['SurveyQuestion']['allow_multiple_answers']==1){
                        $student[$v['Alumnus']['full_name'].'~'.$v['Alumnus']['student_id']][$alv['survey_question_id']]['answer'][]=$alv;

                    } else if ($alv['SurveyQuestion']['answer_required_yn']==1 && !empty($alv['survey_question_answer_id'])){
                        $student[$v['Alumnus']['full_name'].'~'.$v['Alumnus']['student_id']][$alv['survey_question_id']]['answer']=$alv;
                    } else if(empty($alv['survey_question_answer_id']) && !empty($alv['specifiy'])){
                        $student[$v['Alumnus']['full_name'].'~'.$v['Alumnus']['student_id']][$alv['survey_question_id']]['answer']=$alv['specifiy'];
                    }
                }

            }
        }
        return $student;

    }

    public function checkIfStudentGradutingClass($student_id){
        $studentCurriculum=ClassRegistry::init('Student')->find('first',array('conditions'=>array('Student.id'=>$student_id),
            'contain'=>array('Curriculum')));

        $allRegistration=ClassRegistry::init('CourseRegistration')->find('all',array('conditions'=>array('CourseRegistration.student_id'=>$student_id),
            'contain'=>array('PublishedCourse'=>array('Course'))));
        $sumRegistered=0;
        $graduatingCourseTaken=0;
        foreach($allRegistration as $k=>$v){
            $sumRegistered+=$v['PublishedCourse']['Course']['credit'];
            if($v['PublishedCourse']['Course']['thesis']){
                $graduatingCourseTaken=1;
                break;
            }
        }

        $exemptionMaximum=$this->query(
            "SELECT SUM(course_taken_credit) as sumex
		FROM  course_exemptions
		WHERE student_id =".$student_id."
		order by SUM(course_taken_credit)
		DESC limit 1
		");

        if(($sumRegistered+$exemptionMaximum[0][0]['sumex'])>=$studentCurriculum['Curriculum']['minimum_credit_points'] ){
            return true;
        } else if ($graduatingCourseTaken==1){
            return true;
        }
        return false;

    }
}

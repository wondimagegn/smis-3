<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class FxResitRequestTable extends Table
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

        $this->setTable('fx_resit_request');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CourseRegistrations', [
            'foreignKey' => 'course_registration_id',
        ]);
        $this->belongsTo('CourseAdds', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
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
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['course_registration_id'], 'CourseRegistrations'));
        $rules->add($rules->existsIn(['course_add_id'], 'CourseAdds'));
        $rules->add($rules->existsIn(['published_course_id'], 'PublishedCourses'));

        return $rules;
    }

    function doesStudentAppliedFxSit($course_reg_add_id,$register=1){
        if($register==1){
            //find registration details
            $registration=$this->CourseRegistration->find('first',array('conditions'=>array('CourseRegistration.id'=>$course_reg_add_id),
                'recursive'=>-1));
            $regListRegistered=$this->CourseRegistration->find('list',
                array('conditions'=>array(
                    'CourseRegistration.student_id'=>$registration['CourseRegistration']['student_id'],
                    'CourseRegistration.academic_year'=>$registration['CourseRegistration']['academic_year'],
                    'CourseRegistration.semester'=>$registration['CourseRegistration']['semester'],
                ),
                    'fields'=>array('CourseRegistration.id')
                ));
            $applied=$this->find('first',
                array('conditions'=>array('FxResitRequest.course_registration_id'=>$regListRegistered),
                    'recursive'=>-1));
            if(isset($applied) && !empty($applied)){
                return true;
            }
            return false;

        } else {
            //find course add details
            $courseAdds=$this->CourseAdd->find('first',array('conditions'=>array('CourseAdd.id'=>$course_reg_add_id),
                'recursive'=>-1));
            $regListAdded=$this->CourseAdd->find('list',
                array('conditions'=>array(
                    'CourseAdd.student_id'=>$courseAdds['CourseAdd']['student_id'],
                    'CourseAdd.academic_year'=>$courseAdds['CourseAdd']['academic_year'],
                    'CourseAdd.semester'=>$courseAdds['CourseAdd']['semester'],
                ),
                    'fields'=>array('CourseAdd.id')
                ));
            $applied=$this->find('first',
                array('conditions'=>array('FxResitRequest.course_add_id'=>$regListAdded),
                    'recursive'=>-1));
            if(isset($applied) && !empty($applied)){
                return true;
            }
            return false;
        }
    }
    function fxresetId($course_reg_add_id,$register=1){
        if($register==1){
            $applied=$this->find('first',array('conditions'=>array('FxResitRequest.course_registration_id'=>$course_reg_add_id),
                'recursive'=>-1));
            debug($applied);
            return $applied['FxResitRequest']['id'];
        } else {
            $applied=$this->find('first',
                array('conditions'=>array('FxResitRequest.course_add_id'=>$course_reg_add_id),
                    'recursive'=>-1));
            return $applied['FxResitRequest']['id'];
        }
    }

    public function publishedCourseSelected($published_course_id){
        $applied=$this->find('count',array('conditions'=>array('FxResitRequest.published_course_id'=>$published_course_id),'recursive'=>-1));
        return $applied;
    }
    public function doesFxAppliedandQuotaUsed($student_id,$academic_year){
        $mostRecentRegistrationAC=$this->getLastestStudentSemesterAndAcademicYearForFx($student_id);
        $applicationCountRecentACS=0;
        debug($mostRecentRegistrationAC);
        //get registration,or add within the given academic year and semester
        $applicationCountRecentACS=0;
        if(isset($mostRecentRegistrationAC)
            && !empty($mostRecentRegistrationAC)){
            $registrationsList=$this->CourseRegistration->find('list',array('conditions'=>array('CourseRegistration.academic_year'=>$mostRecentRegistrationAC['academic_year'],
                'CourseRegistration.semester'=>$mostRecentRegistrationAC['semester'],
                'CourseRegistration.student_id'=>$student_id
            ),

                'fields'=>array('CourseRegistration.id','CourseRegistration.id'),
                'recursive'=>-1
            ));

            if(isset($registrationsList) && !empty($registrationsList)){
                $applicationCountRecentACS+=$this->find('count',array('conditions'=>array('FxResitRequest.course_registration_id'=>$registrationsList),'recursive'=>-1));

            }

            $addList=$this->CourseAdd->find('list',array('conditions'=>array('CourseAdd.academic_year'=>$mostRecentRegistrationAC['academic_year'],
                'CourseAdd.semester'=>$mostRecentRegistrationAC['semester'],
                'CourseAdd.student_id'=>$student_id,
                'CourseAdd.department_approval'=>1,
                'CourseAdd.registrar_confirmation'=>1,
            ),'fields'=>array('CourseAdd.id','CourseAdd.id'),
                'recursive'=>-1,
            ));

            if(isset($addList) && !empty($addList)){
                $applicationCountRecentACS+=$this->find('count',array('conditions'=>array('FxResitRequest.course_add_id'=>$addList),
                    'recursive'=>-1));
                debug($addList);
                debug($applicationCountRecentACS);
            }
        }

        if($applicationCountRecentACS>0 && $applicationCountRecentACS<3){
            //already applied
            return 2;//current acs and semester applied
        } else if($applicationCountRecentACS==3 || $this->find('count',array('conditions'=>array('FxResitRequest.student_id'=>$student_id)))==3 ){
            //check
            return 3; // quota of fx full, dont allow
        } else {
            $applicationCountRecentACS=0;
            $recent_add=$this->CourseAdd->find('first',array('conditions'=>array('CourseAdd.student_id'=>$student_id,
                'CourseAdd.department_approval'=>1,
                'CourseAdd.registrar_confirmation'=>1,
            ),'order'=>array('CourseAdd.created DESC')
            ));
            debug($addList);
            debug($recent_add);
            if(isset($recent_add)
                && !empty($recent_add)){

                $addList=$this->CourseAdd->find('list',array('conditions'=>array('CourseAdd.academic_year'=>$recent_add['CourseAdd']['academic_year'],
                    'CourseAdd.semester'=>$recent_add['CourseAdd']['semester'],
                    'CourseAdd.student_id'=>$student_id,
                    'CourseAdd.department_approval'=>1,
                    'CourseAdd.registrar_confirmation'=>1,
                ),
                    'fields'=>array('CourseAdd.id','CourseAdd.id')
                ));
                if(isset($addList) && !empty($addList)){
                    $applicationCountRecentACS+=$this->find('count',array('conditions'=>array('FxResitRequest.course_add_id'=>$addList),'recursive'=>-1));
                }
            }
            $recent_registration=
                $this->CourseRegistration->find('first',
                    array('conditions'=>array('CourseRegistration.student_id'=>$student_id
                    ),
                        'order'=>array('CourseRegistration.created DESC')
                    ));
            if(isset($recent_registration) && !empty($recent_registration)){
                $registrationsList=$this->CourseRegistration->find('list',array('conditions'=>array('CourseRegistration.academic_year'=>$recent_registration['CourseRegistration']['academic_year'],
                    'CourseRegistration.semester'=>$recent_registration['CourseRegistration']['semester'],
                    'CourseRegistration.student_id'=>$student_id
                ),
                    'fields'=>array('CourseRegistration.id','CourseRegistration.id')
                ));
                if(isset($registrationsList) && !empty($registrationsList)){
                    $applicationCountRecentACS+=$this->find('count',array('conditions'=>array('FxResitRequest.course_registration_id'=>$registrationsList),'recursive'=>-1));
                }
            }
            if($applicationCountRecentACS==0){
                return 1; //allow current semester
            } else if($applicationCountRecentACS>0) {
                return 2;
            }
        }
        return 2;
    }


    function getLastestStudentSemesterAndAcademicYearForFx ($student_id=null) {
        $academicYearSem=array();
        $regFirst=$this->CourseRegistration->find('first',array('conditions'=>array(
            'CourseRegistration.student_id'=>$student_id,
            'CourseRegistration.id in (select course_registration_id from exam_grades where course_registration_id is not null) '
        ),
            'order'=>array('CourseRegistration.created DESC'),
            'recursive'=>-1

        ));
        debug($regFirst);
        $addFirst=$this->CourseAdd->find('first',array('conditions'=>array(
            'CourseAdd.student_id'=>$student_id,
            'CourseAdd.id in (select course_add_id from exam_grades where course_add_id is not null) '
        ),
            'order'=>array('CourseAdd.created DESC'),
            'recursive'=>-1

        ));
        debug($addFirst);
        if(isset($regFirst) && !empty($regFirst) && isset($addFirst) && !empty($addFirst)){
            if($regFirst['CourseRegistration']['academic_year']>=$addFirst['CourseAdd']['academic_year']){
                $academicYearSem['academic_year']=$regFirst['CourseRegistration']['academic_year'];
                $academicYearSem['semester']=$regFirst['CourseRegistration']['semester'];
            } else {
                $academicYearSem['academic_year']=$addFirst['CourseAdd']['academic_year'];
                $academicYearSem['semester']=$addFirst['CourseAdd']['semester'];
            }

        } else if(isset($regFirst) && !empty($regFirst)){
            $academicYearSem['academic_year']=$regFirst['CourseRegistration']['academic_year'];
            $academicYearSem['semester']=$regFirst['CourseRegistration']['semester'];
        } else if(isset($addFirst) && !empty($addFirst)){
            $academicYearSem['academic_year']=$addFirst['CourseAdd']['academic_year'];
            $academicYearSem['semester']=$addFirst['CourseAdd']['semester'];
        }
        return $academicYearSem;
    }

    public function makeUpExamApplied($student_id,$published_course_id,$reg_add_id,$reg=0){

        if($reg==1){
            $return=$this->find('first',
                array(
                    'conditions' =>
                        array(
                            'FxResitRequest.student_id' => $student_id,
                            'FxResitRequest.published_course_id' => $published_course_id,
                            'FxResitRequest.course_registration_id' => $reg_add_id,
                        ),
                    'recursive'=>-1
                )
            );

        } else if($reg==0){
            $return=$this->find('first',
                array(
                    'conditions' =>
                        array(
                            'FxResitRequest.student_id' => $student_id,
                            'FxResitRequest.published_course_id' => $published_course_id,
                            'FxResitRequest.course_add_id' => $reg_add_id,
                        ),
                    'recursive'=>-1
                )
            );
            debug($return);
            debug($reg_add_id);
            debug($student_id);
        }
        if(isset($return['FxResitRequest']['id'])
            && !empty($return['FxResitRequest']['id'])){
            return $return['FxResitRequest']['id'];
        }

        return 0;
    }

}

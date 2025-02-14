<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class OtherAcademicRulesTable extends Table
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

        $this->setTable('other_academic_rules');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('YearLevels', [
            'foreignKey' => 'year_level_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Curriculums', [
            'foreignKey' => 'curriculum_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CourseCategories', [
            'foreignKey' => 'course_category_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('AcademicStatuses', [
            'foreignKey' => 'academic_status_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('academic_status_id', 'Please provide to be applied.')
            ->notEmptyString('grade', 'Please provide to which grade the rule applies.')
            ->notEmptyString('number_courses', 'Please provide number of courses.');

        return $validator;
    }

    /**
     *Check duplicate entry
     */
    function check_duplicate_entry($data) {
        debug($data);
        $existed_stand=$this->find('count',array(
            'conditions'=>array(
                'department_id'=>$data['OtherAcademicRule']['department_id'],
                'program_id'=>$data['OtherAcademicRule']['program_id'],
                'program_type_id'=>$data['OtherAcademicRule']['program_type_id'],
                'curriculum_id'=>$data['OtherAcademicRule']['curriculum_id'],
                //'course_category_id'=>$data['OtherAcademicRule']['course_category_id'],
                'year_level_id'=>$data['OtherAcademicRule']['year_level_id'],
                'academic_status_id'=>$data['OtherAcademicRule']['academic_status_id'],
                'number_courses'=>$data['OtherAcademicRule']['number_courses'],
                'grade'=>$data['OtherAcademicRule']['grade'],
            ),'recursive'=>-1));

        if ($existed_stand>0) {
            $this->invalidate('duplicate',
                'You have already defined the academic rule.');

            return false;
        }

        return true;
    }

    function whatIsTheStatus($semCourseLists=array(),
                             $student,$year=null){
        $studentDetail=ClassRegistry::init('Student')->find('first',
            array('conditions'=>array('Student.id'=>$student['id']),
                'recursive'=>-1));
        if(isset($studentDetail) && !empty($studentDetail)){

            $or=$this->find('all',
                array('conditions'=>array(
                    'OtherAcademicRule.curriculum_id'=>$studentDetail['Student']['curriculum_id']),'recursive'=>-1));
            $otherAcademicRules=array();
            foreach($or as $otr=>$otv){
                if(
                    $otv['OtherAcademicRule']['year_level_id']
                    ==$year['year']){
                    $otherAcademicRules=$otv;
                    break;
                }
            }
            if(!isset($otherAcademicRules)
                && empty($otherAcademicRules)){
                $otherAcademicRules=$this->find('first',
                    array('conditions'=>array(
                        'OtherAcademicRule.curriculum_id'=>$studentDetail['Student']['curriculum_id']),'recursive'=>-1));
            }
            if(isset($otherAcademicRules) &&
                !empty($otherAcademicRules)){

                $countRuleFound=0;
                $academicStatus=null;
                foreach($semCourseLists as $ck=>$cv){
                    $courseDetail=ClassRegistry::init('Course')->find('first',array('conditions'=>array('Course.id'=>$cv['course_id']),'contain'=>array('CourseCategory')));
                    if(isset($courseDetail['CourseCategory'])
                        && !empty($courseDetail['CourseCategory'])){

                        if($courseDetail['CourseCategory']['id']==$otv['OtherAcademicRule']['course_category_id'] &&
                            strcasecmp($otherAcademicRules['OtherAcademicRule']['course_category_id'],
                                $cv['grade'])==0){
                            $countRuleFound++;

                        }
                    }

                }
                if($countRuleFound>=$otherAcademicRules['OtherAcademicRule']['number_courses']){
                    return $otherAcademicRules['OtherAcademicRule']['academic_status_id'];
                }
            }
        }
        return null;



    }
}

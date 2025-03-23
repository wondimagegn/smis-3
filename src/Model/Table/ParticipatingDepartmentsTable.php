<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ParticipatingDepartmentsTable extends Table
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

        $this->setTable('participating_departments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
        ]);
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
        ]);
        $this->belongsTo('DevelopingRegions', [
            'foreignKey' => 'developing_regions_id',
        ]);
        $this->hasMany('ReservedPlaces', [
            'foreignKey' => 'participating_department_id',
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
            ->notEmptyString('academic_year', 'Select academic year')
            ->numeric('number', 'Enter number required')
            ->numeric('female', 'Enter number required')
            ->numeric('regions', 'Enter number required');

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
        $rules->add($rules->existsIn(['college_id'], 'Colleges'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['developing_regions_id'], 'Regions'));

        return $rules;
    }


    function canEditOwn($college_id=null,$id=null){

        $canEditOwn= $this->find('all', array('conditions'=>array('ParticipatingDepartment.college_id'=>$college_id)));

        foreach($canEditOwn as $key=>$value){

            if($value['ParticipatingDepartment']['id']==$id){
                return true;
            }

        }
        return false;
    }

    /**
     *Check if the given department is from other college
     */
    function checkIfOtherCollege($department_id=null,$college_id=null){
        $count = $this->Department->find('count',
            array('conditions'=>array('Department.college_id '=>$college_id,
                'Department.id'=>$department_id)));
        if($count){
            return true;
        }
        return false;
    }
    /**
     *Check participation department has already recorded for current academic year
    @return boolean
     */
    function isAlreadyRecordedParticipationgDepartments($college_id=null,
                                                        $academicyear=null,$reformatparticipatingdepartments=null){

        if($academicyear && $college_id){
            //check that any preference student has started filed preference
            $check=ClassRegistry::init('Preference')
                ->find('count',array('conditions'=>array('Preference.college_id'=>$college_id,'Preference.academicyear'=>$academicyear)));
            if($check){
                $this->invalidate('alreadyrecorded','Validation Error:
                      Student has started to fill their preference.You can not add more participating departments for  '.$academicyear.' academic year.');
                return FALSE;
            }
        }

        //check each department that was not recorded to the given academic year
        // debug($reformatparticipatingdepartments);
        if(!empty($reformatparticipatingdepartments)&&!empty($academicyear)&&
            !empty($college_id)){
            foreach($reformatparticipatingdepartments['ParticipatingDepartment']
                    as $key =>&$value){
                $check=$this->find('count',array('conditions'=>
                    array('ParticipatingDepartment.college_id '=>$college_id,
                        'ParticipatingDepartment.academic_year LIKE'
                        =>$academicyear.'%','ParticipatingDepartment.department_id'
                    =>$value['department_id'])));

                if($check==1){
                    unset($reformatparticipatingdepartments['ParticipatingDepartment'][$key]);
                }


            }

            if(!empty($reformatparticipatingdepartments['ParticipatingDepartment']
            )){

                return $reformatparticipatingdepartments;
            } else {
                $this->invalidate('alreadyrecorded','Validation Error:
                      The participating department for placement has already recorded for '.$academicyear.' academic year.');
                return false;

            }
        }


    }
    /**
     *Check others college participationg departments available students
    @return boolean
     */
    function checkAgainstAvailableStudentFromOtherCollege($data=null){
        $array=array();
        $number=0;
        $count=0;
        $academicyear=null;

        if(!empty($data)){
            foreach($data as $key=>$value){
                if(!empty($value['other_college_department'])){
                    $array[]=$value['department_id'];
                    $number +=$value['number'];
                    $academicyear=$value['academic_year'];

                }
                // this happens during editing
                if($key=='other_college_department'){
                    $count++;
                }

            }
        }
        // this happens during editing of own department participation
        if($count==0){
            return true;
        }

        if(!empty($array)){
            $findcollege=$this->Department->find('first',array('conditions'=>array('
	            Department.id'=>$array),'contain'=>array(
                    'College'=>array(
                        'fields'=>array(
                            'id',
                            'name')
                    )
                )
                )

            );
            if(!empty($findcollege['College']['id'])){
                $total_accepted_students_unsigned_to_department=
                    $this->ReservedPlace->total_accepted_students_unsigned_to_department(
                        $findcollege['College']['id'],$academicyear);
                if ($number<=$total_accepted_students_unsigned_to_department) {
                    return  true;

                } else {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     *This method validate the  female qutoa should be less than or equal to total
     * accepted female students.
    @return boolean
     */
    function checkAvailableFemaleInTheGivenAcademicYear($data,$college_id=null,
                                                        $academicyear=null){
        $female=$this->College->AcceptedStudent->find('count',array(
            'conditions'=>array('AcceptedStudent.sex'=>'female',
                'AcceptedStudent.college_id'=>$college_id,'AcceptedStudent.academicyear'=>$academicyear)));


        if($this->sumQuota($data,'female')<=$female){
            return true;
        } else {
            $this->invalidate('female','Validation Error:
                      The female quota should be
		               less than or equal to the number of female students. The total
		               female students in your college is
		             '.$female
                .' Please adjust number again');
            return false;

        }
    }

    /**
     *This method validate the  regions qutoa should be less than or equal to total
     * students in the given region.
    @return boolean
     */
    function checkAvailableRegionStudentInTheGivenAcademicYear($data,
                                                               $college_id=null,$region_ids,$academicyear=null){
        if(empty($region_ids)){
            return true;
        }

        $regions=$this->College->AcceptedStudent->find('count',array(
            'conditions'=>array('AcceptedStudent.region_id'=>$region_ids,
                'AcceptedStudent.college_id'=>$college_id,'AcceptedStudent.academicyear'=>$academicyear)));
        if($this->sumQuota($data,'regions')<=$regions){
            return true;
        } else {
            $this->invalidate('regions','Validation Error:
                      The region quota should be less than
			            or equal to the number of student in the given regions. The total
			            students in selected regions is'.$regions
                .'Please adjust number');
            return false;
        }
    }
    /**
     *This method validate the  regions qutoa should be less than or equal to total
     * students in the given region.
    @return boolean
     */
    function checkAvailableDisableStudentInTheGivenAcademicYear($data,
                                                                $college_id=null,$academicyear=null){
        if(empty($region_ids)){
            return true;
        }
        $disable=$this->College->AcceptedStudent->find('count',array(
            'conditions'=>array('AcceptedStudent.disability <> '=>null,
                'AcceptedStudent.college_id'=>$college_id,'AcceptedStudent.academicyear'=>$academicyear)));
        //debug($disable);
        if($this->sumQuota($data,'disability')<=$disable){
            return true;
        } else {
            $this->invalidate('regions','Validation Error:
                      The disability quota should be less than
			            or equal to the number of student in the given college. The total
			            students in who are disable in your college is '.$disable
                .'Please adjust number');
            return false;
        }
    }
    function sumQuota($data=null,$field=null){
        $sumquota=0;
        if(!empty($data)){
            foreach($data as $k =>$v){
                if( $field == 'female'){
                    $sumquota = $sumquota+$v['female'];
                } elseif( $field == 'regions'){
                    $sumquota = $sumquota+$v['regions'];
                } elseif( $field == 'disability'){
                    $sumquota = $sumquota+$v['disability'];
                } elseif( $field == 'number') {
                    $sumquota = $sumquota + $v['number'];
                }
            }
            return $sumquota;
        }
        return 0;
    }

    function checkAvailableNumberOfStudentAgainstGivenQuotaOfDepartment(
        $data=null,$college_id=null,$academicyear=null){
        $isPrepartory = ClassRegistry::init('PlacementsResultsCriteria')->isPrepartoryResult($academicyear, $college_id);
        $conditions['OR'] = array(
            array('AcceptedStudent.department_id' =>
                array('', 0)),
            array('AcceptedStudent.department_id is null'),
            array('AcceptedStudent.placementtype' =>
                array(NULL,CANCELLED_PLACEMENT)
            ),

        );

        $conditions['AND'] = array(array("AcceptedStudent.academicyear LIKE "
        => $academicyear.'%',"AcceptedStudent.college_id" =>
            $college_id,
            "AcceptedStudent.Placement_Approved_By_Department is null",

            "AcceptedStudent.college_id"=>$college_id

        ));
        if($isPrepartory == 0) {
            $conditions['AND'][] = 'AcceptedStudent.freshman_result IS NOT NULL';
        }
        $total=$this->College->AcceptedStudent->find('count',array(
            'conditions'=>$conditions));
        //dont allow the user to have zero department capacity
        $female=$this->sumQuota($data,'female');
        $totaldepartmentsnumber=$this->sumQuota($data,'number');
        $disability=$this->sumQuota($data,'disability');
        $regions=$this->sumQuota($data,'regions');
        $privilaged_quota=$female+$disability+$regions;
        //debug($data);
        foreach($data as $k=>$v){
            $privilaged_quota_sum=$v['female'] + $v['regions']+$v['disability'];
            if($v['number']==0){
                $this->invalidate('DepartmentCapacity','
                       Department capacity of the participating department should be greater than zero, or you have to remove/delete from the participating department list before adding quota. Please adjust number');
                return false;
            }

            $dep_name=$this->Department->field('Department.name',array('Department.id'=>$v['department_id']));
            if($v['number']< $v['female']){
                $this->invalidate('DepartmentCapacity','
                      The total quota for female students should be less than or equal the department capacity for '.$dep_name.' department');
                return false;
            }
            if($v['number'] < $v['regions']){
                $this->invalidate('DepartmentCapacity','
                      The total quota for region students should be less than or equal the department capacity for '.$dep_name.' department');
                return false;
            }

            if($v['number'] < $v['disability']){
                $this->invalidate('DepartmentCapacity','
                      The total quota for disability students should be less than or equal the department capacity for '.$dep_name.' department');
                return false;
            }
            //debug($privilaged_quota_sum);
            if($v['number'] < ($privilaged_quota_sum)){
                $this->invalidate('DepartmentCapacity','
                      The sum of the privilaged quota should be less than or equal the department capacity for '.$dep_name.' department');
                return false;
            }

        }





        if(($totaldepartmentsnumber) == $total){
            return true;
        } else {
            $this->invalidate('DepartmentCapacity','
                       The sum of all department capacity should be equal to the total number of students who can participate in your college/institute for placement. The total number of students in your college who are eligible for placement are '.$total.'. Please adjust department capacity accordinglly.');
            return false;
        }

    }
    /**
     * Method to return the quota
    @return array
     */
    function quotaNameAndValue($academicyear=null,$college_id=null){
        if($college_id){
            $result=$this->find('all',
                array('fields'=>array('ParticipatingDepartment.department_id','ParticipatingDepartment.female',
                    'ParticipatingDepartment.disability',
                    'ParticipatingDepartment.regions'),'conditions'=>array('ParticipatingDepartment.college_id'=>$college_id,
                    'ParticipatingDepartment.academic_year LIKE '=>$academicyear.'%'),
                    'recursive'=>-1));
            return $result;
        }
    }
    /**
     * Method to check others department capacity quota against the available students
     * in the given college
     */
    function checkDepartmentCapacityBeforeEditing($data=null,$college_id=null,
                                                  $academicyear=null) {
        $conditions['OR'] = array(
            array('AcceptedStudent.department_id' =>
                array('', 0)),
            array('AcceptedStudent.department_id' => NULL),
            array('AcceptedStudent.placementtype' =>
                array(NULL,CANCELLED_PLACEMENT)
            ),

        );

        $conditions['AND'] = array(array("AcceptedStudent.academicyear LIKE "
        => $academicyear.'%',"AcceptedStudent.college_id" =>
            $college_id,
            "AcceptedStudent.Placement_Approved_By_Department is null",

            "AcceptedStudent.college_id"=>$college_id

        ));
        $total=$this->College->AcceptedStudent->find('count',array(
            'conditions'=>$conditions));
        if($total>0){
            //others capacity user to have zero department capacity
            $others_capacity=$this->find('all',array('conditions'=>array('ParticipatingDepartment.college_id'=>$college_id,'ParticipatingDepartment.academic_year LIKE'=>$academicyear.'%','ParticipatingDepartment.department_id <>'=>$data['department_id'])));
            $other_sum=0;
            foreach($others_capacity as $k=>$v){
                $other_sum+=$v['ParticipatingDepartment']['number'];
            }
            if($other_sum != ($total - $data['number'])){
                $this->invalidate('DepartmentCapacity','
                           The  department capacity should be equal to the total number of students in your college minus others department capacity. The total number of students in your college eligible for placement is '.$total.' and others department capacity is '.$other_sum.'. Please adjust number');
                return false;
            } else {
                return true;
            }
        }
        return false;

    }

    function getParticipatingDepartment($collegeId,$academicYear){

        $departmentList=array();
        $departments=$this->find('all',
            array('conditions'=>array('ParticipatingDepartment.college_id '=>$collegeId,
                'ParticipatingDepartment.academic_year'=>$academicYear),'contain'=>array('Department')));
        foreach($departments as $k=>$v){
            $departmentList[$v['Department']['id']]=$v['Department']['name'];
        }
        return $departmentList;
    }
}

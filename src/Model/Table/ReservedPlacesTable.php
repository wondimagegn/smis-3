<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ReservedPlacesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('reserved_places');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');

        // Define associations
        $this->belongsTo('PlacementsResultsCriteria', [
            'foreignKey' => 'placements_results_criteria_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('ParticipatingDepartments', [
            'foreignKey' => 'participating_department_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
        ]);

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('number', 'create')
            ->notEmptyString('number', 'Enter number required')
            ->numeric('number', 'Enter a valid number');

        $validator
            ->requirePresence('academicyear', 'create')
            ->notEmptyString('academicyear', 'Select Academic Year.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['placements_results_criteria_id'], 'PlacementsResultsCriteria'));
        $rules->add($rules->existsIn(['participating_department_id'], 'ParticipatingDepartments'));
        $rules->add($rules->existsIn(['college_id'], 'Colleges'));

        return $rules;
    }


    /**
     *Find the total number of students in the given college for a particular
     * academic year, and result category
    @return total
     */
    function find_total_number_accepted_student_in_given_category($placements_results_criteria_id=null,
                                                                  $college_id=null,$academicyear=null) {

        $result_criteria_data = $this->PlacementsResultsCriteria->find('first',
            array('conditions'=>array('PlacementsResultsCriteria.id'=>$placements_results_criteria_id)));
        //debug($result_criteria_data);
        if(!empty($result_criteria_data['PlacementsResultsCriteria'])) {

            if($result_criteria_data['PlacementsResultsCriteria']['prepartory_result']){
                $total_students_category=$this->College->AcceptedStudent->find('count',
                    array(
                        'conditions'=>
                            array("AcceptedStudent.academicyear LIKE" =>$academicyear.'%',
                                "AcceptedStudent.EHEECE_total_results >=" =>$result_criteria_data['PlacementsResultsCriteria']['result_from'],
                                "AcceptedStudent.EHEECE_total_results <="=>$result_criteria_data['PlacementsResultsCriteria']['result_to'],"AcceptedStudent.college_id"=>$college_id,
                                "OR"=>array("AcceptedStudent.department_id is null","AcceptedStudent.department_id"=>array(0,''))))
                );
                //debug($total_students_category);
                return $total_students_category;
            } else {
                $total_students_category=$this->College->AcceptedStudent->find('count',
                    array(
                        'conditions'=>array("AcceptedStudent.academicyear LIKE" =>$academicyear.'%',
                            "AcceptedStudent.freshman_result >="
                            =>$result_criteria_data['PlacementsResultsCriteria']['result_from'],
                            "AcceptedStudent.freshman_result <="=>$result_criteria_data['PlacementsResultsCriteria']['result_to'],"AcceptedStudent.college_id"=>$college_id,"OR"=>array("AcceptedStudent.department_id is null","AcceptedStudent.department_id"=>array(0,''))))
                );
                return $total_students_category;
            }


        }

    }
    /**
     *Find the total number of students in the given college for a particular
     *academic year, and result category
    @return total
     */
    function find_student_quota_given_participating_department($departmentquota=null,$participating_department_id=null,
                                                               $college_id=null,$academicyear=null,$placements_results_criteria_id=null) {
        // change all to sum if there is
        //check the participating department is from other college and sum it up
        // the number of place students set in participation department
        $participating_department=$this->find('all',array('conditions'=>array(
            'ReservedPlace.college_id'=>$college_id,'ReservedPlace.academicyear LIKE' =>$academicyear.'%',
            'ReservedPlace.placements_results_criteria_id'=>$placements_results_criteria_id)));
        $sum=0;
        foreach($participating_department as $key=>$value){
            $sum=$sum+$value['ReservedPlace']['number'];
        }

        if($departmentquota<=$sum){
            return true;
        }
        return false;

    }

    /*
    *Method to check the given result_category has been recorded
    @return boolean
    */

    function checkGivenCategoryReserved($placements_result_criteria_id=null,
                                        $college_id=null,$academicyear=null) {
        $count=$this->find('count',array('placements_results_criteria_id'=>$placements_result_criteria_id,
            'college_id'=>$college_id,'academicyear LIKE' =>$academicyear.'%'));

        // return $count;
        if($count>0) {
            return false;
        } else {
            return true;
        }
        return true;
    }

    function total_accepted_students_unsigned_to_department($college_id=null, $academicyear=null){

        $isPrepartory = ClassRegistry::init('PlacementsResultsCriteria')->isPrepartoryResult($academicyear, $college_id);
        $options =
            array(
                'conditions'=>
                    array(
                        "AcceptedStudent.academicyear LIKE" =>$academicyear.'%',
                        'AcceptedStudent.college_id'=>$college_id,
                        "OR"=>
                            array(
                                "AcceptedStudent.department_id is null",
                                "AcceptedStudent.department_id" =>
                                    array(
                                        0,
                                        ''
                                    )
                            ),
                        'AcceptedStudent.program_type_id'=>PROGRAM_TYPE_REGULAR,
                        'AcceptedStudent.program_id'=>PROGRAM_UNDEGRADUATE
                    )
            );
        if($isPrepartory == 0) {
            $options['conditions'][] = 'AcceptedStudent.freshman_result IS NOT NULL';
        }
        debug($options);
        if(!empty($college_id)){
            $total = $this->College->AcceptedStudent->find('count', $options);

            return $total;
        }
        return 0;
    }


    /**
     * Method to check place is reserved for department already
     */
    function isAlreadyRecorded(
        $college_id=null,$selectedAcademicYear=null){
        // return $selectedAcademicYear;
        $count=$this->find('count',array('conditions'=>array('ReservedPlace.college_id'=>$college_id,'ReservedPlace.academicyear'=>$selectedAcademicYear)));

        if($count){
            $this->invalidate('duplicate','Validation Error: You have already reserved place
	        for '.$selectedAcademicYear.' academic year. Please edit.');
            return false;

        } else {
            return true;
        }
        //$count=$this->find('count',array('conditions'))
    }
    /**
     *
     */
    function checkCategoryReservedPlaceIsWithinRanage($data=null,
                                                      $placementsResultsCriterias=null,$college_id=null) {
        $resultplacement=array();
        $check_against_quota=array();

        $academicyear=null;
        $quota_sum=0;
        //debug($placementsResultsCriterias);
        if(!empty($placementsResultsCriterias)){
            //initialize just to solve undefined
            foreach($placementsResultsCriterias as $pk=>$pv){
                $resultplacement[$pk]['number']=0;

            }
            //debug($data);
            if(!empty($data)){
                $academicyear=$data['academicyear'];
                foreach($placementsResultsCriterias as $k=>$v){
                    //$resultplacement[$v]['number']=0;
                    foreach($data as $key=>$value){
                        if( is_array($value) && $k==$value['placements_results_criteria_id']){
                            //debug($k);
                            //debug($value);
                            //debug($value['number']);

                            $resultplacement[$k]['number']+=$value['number'];
                            // $college_id=$college_id;
                            //$academicyear=$value['academicyear'];
                            $check_against_quota[$value['participating_department_id']][$k]=$value['number'];
                        }
                    }
                }
                // participationg departments without quota.
                $department_capacity = $this->ParticipatingDepartment->find('all',
                    array('conditions'=>array('ParticipatingDepartment.college_id'=>$college_id,
                        'ParticipatingDepartment.academic_year LIKE '=>$academicyear.'%'
                    )));

                foreach($department_capacity as $dck => $dcv){
                    $quota_sum +=$dcv['ParticipatingDepartment']['female']+
                        $dcv['ParticipatingDepartment']['disability']+
                        $dcv['ParticipatingDepartment']['regions'];
                }

                // validation against the quota
                foreach($check_against_quota as $pk1=>$pv1){
                    $sum=0;
                    foreach($pv1 as $pv1k=>$pv1v){
                        $sum+=$pv1v;
                    }
                    $quotas=ClassRegistry::init('ParticipatingDepartment')->find('first',
                        array(
                            'conditions'=>array(
                                'ParticipatingDepartment.college_id'=>$college_id,'ParticipatingDepartment.academic_year'=>$academicyear,'ParticipatingDepartment.department_id'=>$pk1)));

                    if(!empty($quotas)){
                        $department_capacity=$quotas['ParticipatingDepartment']['number'];
                        $privilaged_quota=$quotas['ParticipatingDepartment']['female']+
                            $quotas['ParticipatingDepartment']['regions']+$quotas['ParticipatingDepartment']['disability'];

                        if($sum>$department_capacity) {
                            $this->invalidate('resultcategory','
                           The maximum number of reserved place  for each result category for '.$quotas['Department']['name'].' department should be  equal to department capacity minus privilaged quota.Please adjust number, for '.$quotas['Department']['name'].'department');
                            return false;
                        }
                        //
                        if ($sum != ($department_capacity - $privilaged_quota)){
                            $this->invalidate('resultcategory','
                           The sum of the  reserved place  for each result category for '.$quotas['Department']['name'].' department should be  equal to department capacity minus privilaged quota which is  '.($department_capacity - $privilaged_quota).' .Please adjust the number');
                            return false;
                        }
                    }
                }
            }
        }

        foreach($resultplacement as $k=>$v){
            //  debug($v);

            $total_students_category=$this->find_total_number_accepted_student_in_given_category(
                $k,$college_id,$academicyear);

            if($quota_sum==0){
                if($v['number']==$total_students_category){

                    continue;
                } else {

                    $this->invalidate('resultcategory','Validation Error:
                           The maximum number of reserved  place(s) for result category should be
                           equal to the total number of students in that result category which is
                           '.$total_students_category.'. Please adjust number.');
                    return false;

                }

            } else {

                if($v['number']<=$total_students_category && (
                        $v['number']>=($total_students_category-$quota_sum))){
                    //debug($v['number']);
                    continue;
                } else {
                    $this->invalidate('resultcategory','
                           The maximum number of reserved  place(s) for result category should be  less than or equal to  '.$total_students_category.' and greater than or equal to '.($total_students_category-$quota_sum).' number of students. Please adjust number.');
                    return false;
                }

            }
        }


        return true;

    }

}

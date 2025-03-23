<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AcceptedStudentsTable extends Table
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

        $this->setTable('accepted_students');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Regions', [
            'foreignKey' => 'region_id',
            'propertyName' => 'Region',
        ]);
        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
            'propertyName' => 'Zone',
        ]);
        $this->belongsTo('Woredas', [
            'foreignKey' => 'woreda_id',
            'propertyName' => 'Woreda',
        ]);
        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'propertyName' => 'College',
        ]);
        $this->belongsTo('Campuses', [
            'foreignKey' => 'campus_id',
            'joinType' => 'INNER',
            'propertyName' => 'Campus',
        ]);

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'propertyName' => 'Department',
        ]);
        $this->belongsTo('Curriculums', [
            'foreignKey' => 'curriculum_id',
            'propertyName' => 'Curriculum',
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'propertyName' => 'Program',
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'propertyName' => 'ProgramType',
        ]);
        $this->belongsTo('Specializations', [
            'foreignKey' => 'specialization_id',
            'propertyName' => 'Specialization',
        ]);
        $this->belongsTo('PlacementTypes', [
            'foreignKey' => 'placement_type_id',
            'propertyName' => 'PlacementType',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'propertyName' => 'User',
        ]);
        $this->belongsTo('OnlineApplicants', [
            'foreignKey' => 'online_applicant_id',
            'propertyName' => 'OnlineApplicant',
        ]);


        $this->hasMany('DormitoryAssignments', [
            'foreignKey' => 'accepted_student_id',
            'propertyName' => 'DormitoryAssignment',
        ]);
        $this->hasMany('MealHallAssignments', [
            'foreignKey' => 'accepted_student_id',
            'propertyName' => 'MealHallAssignment',
        ]);
        $this->hasMany('PlacementEntranceExamResultEntries', [
            'foreignKey' => 'accepted_student_id',
            'propertyName' => 'PlacementEntranceExamResultEntry',
        ]);
        $this->hasMany('PlacementParticipatingStudents', [
            'foreignKey' => 'accepted_student_id',
            'propertyName' => 'PlacementParticipatingStudent',
        ]);
        $this->hasMany('PlacementPreferences', [
            'foreignKey' => 'accepted_student_id',
            'propertyName' => 'PlacementPreference',
        ]);
        $this->hasMany('Preferences', [
            'foreignKey' => 'accepted_student_id',
            'propertyName' => 'Preference',
        ]);
        $this->hasMany('Students', [
            'foreignKey' => 'accepted_student_id',
            'propertyName' => 'Student',
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
            ->scalar('first_name')
            ->maxLength('first_name', 200)
            ->allowEmptyString('first_name');

        $validator
            ->scalar('middle_name')
            ->maxLength('middle_name', 200)
            ->allowEmptyString('middle_name');

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 200)
            ->allowEmptyString('last_name');

        $validator
            ->scalar('sex')
            ->maxLength('sex', 10)
            ->allowEmptyString('sex');

        $validator
            ->scalar('studentnumber')
            ->maxLength('studentnumber', 200)
            ->allowEmptyString('studentnumber');

        $validator
            ->scalar('assignment_type')
            ->maxLength('assignment_type', 200)
            ->allowEmptyString('assignment_type');

        $validator
            ->numeric('EHEECE_total_results')
            ->allowEmptyString('EHEECE_total_results');

        $validator
            ->decimal('freshman_result')
            ->allowEmptyString('freshman_result');

        $validator
            ->scalar('high_school')
            ->maxLength('high_school', 100)
            ->allowEmptyString('high_school');

        $validator
            ->scalar('moeadmissionnumber')
            ->maxLength('moeadmissionnumber', 100)
            ->allowEmptyString('moeadmissionnumber');

        $validator
            ->scalar('benefit_group')
            ->maxLength('benefit_group', 100)
            ->allowEmptyString('benefit_group');

        $validator
            ->scalar('academicyear')
            ->maxLength('academicyear', 100)
            ->allowEmptyString('academicyear');

        $validator
            ->boolean('Placement_Approved_By_Department')
            ->allowEmptyString('Placement_Approved_By_Department');

        $validator
            ->uuid('minute_number')
            ->allowEmptyString('minute_number');

        $validator
            ->scalar('applicationstatus')
            ->maxLength('applicationstatus', 100)
            ->allowEmptyString('applicationstatus');

        $validator
            ->scalar('currentstatus')
            ->maxLength('currentstatus', 100)
            ->allowEmptyString('currentstatus');

        $validator
            ->scalar('disability')
            ->maxLength('disability', 20)
            ->allowEmptyString('disability');

        $validator
            ->scalar('placementtype')
            ->maxLength('placementtype', 100)
            ->allowEmptyString('placementtype');

        $validator
            ->scalar('placement_based')
            ->maxLength('placement_based', 1)
            ->allowEmptyString('placement_based');

        return $validator;
    }


    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['region_id'], 'Regions'));
        $rules->add($rules->existsIn(['zone_id'], 'Zones'));
        $rules->add($rules->existsIn(['woreda_id'], 'Woredas'));
        $rules->add($rules->existsIn(['college_id'], 'Colleges'));
        $rules->add($rules->existsIn(['campus_id'], 'Campuses'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['curriculum_id'], 'Curriculums'));
        $rules->add($rules->existsIn(['program_id'], 'Programs'));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));
        $rules->add($rules->existsIn(['specialization_id'], 'Specializations'));
        $rules->add($rules->existsIn(['placement_type_id'], 'PlacementTypes'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['online_applicant_id'], 'OnlineApplicants'));

        return $rules;
    }

    function isUniqueStudentNumber()
    {
        $count = 0;

        if (!empty($this->data['AcceptedStudent']['id'])) {
            $count = $this->find('count', array(
                'conditions' => array(
                    'AcceptedStudent.studentnumber LIKE ' => (trim($this->data['AcceptedStudent']['studentnumber'])) . '%',
                    'AcceptedStudent.id <> ' => $this->data['AcceptedStudent']['id']
                )
            ));
        } else {
            $count = $this->find('count', array(
                'conditions' => array(
                    'AcceptedStudent.studentnumber LIKE ' => (trim($this->data['AcceptedStudent']['studentnumber'])) . '%',
                )
            ));
        }

        if ($count > 0) {
            return false;
        }
        return true;
    }

    public function updateAcceptedStudentCollege($college_id, $academicyear, $program_id = 1, $program_type_id = 1)
    {
        $acceptedStudents = $this->find('all', array(
            'conditions' => array(
                "AcceptedStudent.academicyear" => $academicyear,
                "AcceptedStudent.original_college_id" => $college_id,
                "AcceptedStudent.program_id" => $program_id,
                "AcceptedStudent.program_type_id" => $program_type_id,
                "AcceptedStudent.campus_id!=0"
            ),
            'contain' => array('Student')
        ));

        if (isset($acceptedStudents) && !empty($acceptedStudents)) {
            foreach ($acceptedStudents as $pk => $pv) {
                if (isset($pv['Student']['id']) && !empty($pv['Student']['id'])) {
                    //find section college,
                    $sectionCollege = ClassRegistry::init('StudentsSection')->find('first', array('conditions' => array('StudentsSection.student_id' => $pv['Student']['id']), 'contain' => array('Section')));

                    //debug($sectionCollege);
                    //debug($pv);

                    //update accepted and admitted student college
                    $acceptedStudentToBeAssigned = array();
                    $admittedStudentToBeAssigned = array();

                    if (isset($sectionCollege['Section']['college_id']) && !empty($sectionCollege['Section']['college_id'])) {
                        $acceptedStudentToBeAssigned['AcceptedStudent']['id'] = $pv['AcceptedStudent']['id'];
                        $acceptedStudentToBeAssigned['AcceptedStudent']['college_id'] = $sectionCollege['Section']['college_id'];
                        $admittedStudentToBeAssigned['Student']['id'] = $pv['Student']['id'];
                        $admittedStudentToBeAssigned['Student']['college_id'] = $sectionCollege['Section']['college_id'];

                        if (isset($acceptedStudentToBeAssigned) && !empty($acceptedStudentToBeAssigned)) {
                            if ($this->save($acceptedStudentToBeAssigned)) {
                                if (isset($admittedStudentToBeAssigned) && !empty($admittedStudentToBeAssigned)) {
                                    $this->Student->id = $admittedStudentToBeAssigned['Student']['id'];
                                    $this->Student->saveField('college_id', $admittedStudentToBeAssigned['Student']['college_id']);
                                }
                            } else {
                                debug($this->invalidFields());
                            }
                        }
                    }
                }
            }
        }
    }

    function getRecentAcceptedStudent($college_id = null, $academicyear = null)
    {
        if (!empty($college_id) && !empty($academicyear)) {
            $recentAcceptedStudent = $this->find('all', array(
                'conditions' => array(
                    'AcceptedStudent.college_id' => $college_id,
                    'AcceptedStudent.academicyear' => $academicyear
                ),
                'limit' => 100
            ));

            return  $recentAcceptedStudent;
        }
    }

    function readAllById($id = null)
    {

        if ($id) {
            $data = $this->find("first", array(
                "conditions" => array('AcceptedStudent.id' => $id),
                'contain' => array(
                    'Program',
                    'ProgramType',
                    'College',
                    'Department'
                )
            ));

            return $data;
        }
    }

    function countId($collegeid = null, $year = null, $program_id = null, $program_type_id = null)
    {
        $count = $this->find("count", array(
            "conditions" => array(
                'AcceptedStudent.academicyear LIKE' => $year . '%',
                'AcceptedStudent.college_id' => $collegeid,
                'AcceptedStudent.program_id' => $program_id,
                'AcceptedStudent.program_type_id' => $program_type_id,
                "NOT" => array(
                    'AcceptedStudent.studentnumber' => array('', '0', 'NULL')
                )
            ),
            'recursive' => -1
        ));

        return $count;
    }

    function getidlessstudentsummery($thisacademicyear = null)
    {
        if (!empty($thisacademicyear)) {
            $colleges = $this->College->find('list', array('conditions' => array('College.active' => 1)));
            $programs = $this->Program->find('list');
            $programtypes = $this->ProgramType->find('list');

            $data = array();

            if (!empty($colleges)) {
                foreach ($colleges as $kc => $vc) {
                    foreach ($programs as $kp => $vp) {
                        foreach ($programtypes as $kpt => $vpt) {
                            $data[$vc][$vp][$vpt] = $this->find('count', array(
                                'conditions' => array(
                                    'AcceptedStudent.academicyear' => $thisacademicyear,
                                    'AcceptedStudent.college_id' => $kc,
                                    'AcceptedStudent.program_id' => $kp,
                                    'AcceptedStudent.program_type_id' => $kpt,
                                    "OR" => array(
                                        "AcceptedStudent.studentnumber is null",
                                        "AcceptedStudent.studentnumber = ''",
                                        //"AcceptedStudent.studentnumber = 0",
                                    )
                                )
                            ));
                        }
                    }
                }
            }


            // to be completed, Neway
            // only show the relevant ones

            /* if (!empty($data)) {
                //debug($data);
                foreach ($data as $coll => $progs) {
                    //debug($progs);
                    foreach ($progs as $prgkey => $progtypes) {
                        //debug($progtypes);
                        foreach ($progtypes as $progTypekey => $student_count) {
                            debug($student_count);
                            if ($student_count == 0) {
                                unset($data[$coll][$prgkey][$progTypekey]);
                            }
                        }
                    }
                }
            } */

            return $data;
        }
    }


    function isApproved($accepted_student_id = null)
    {
        if ($accepted_student_id) {
            $isApproved = $this->find('first', array('conditions' => array('id' => $accepted_student_id)));
            if ($isApproved['AcceptedStudent']['Placement_Approved_By_Department']) {
                return true;
            }
            return false;
        }
        return false;
    }

    // Check placement setting is recorded for the given academic year

    function checkPlacementSettingIsRecorded($academicyear = null, $college_id = null)
    {
        $checkPlacementSettingIsRecord = array();

        $checkPlacementSettingIsRecord['placement_result_criteria'] = ClassRegistry::init('PlacementsResultsCriteria')->isPlacementResultRecorded($academicyear, $college_id);
        $checkPlacementSettingIsRecord['ReservedPlace'] = ClassRegistry::init('PlacementsResultsCriteria')->isReservedPlaceRecorded($academicyear, $college_id);
        $checkPlacementSettingIsRecord['participating_departemnt'] = ClassRegistry::init('PlacementsResultsCriteria')->isParticipationgDepartmentRecorded($academicyear, $college_id);

        if (empty($checkPlacementSettingIsRecord['placement_result_criteria'])) {
            $this->invalidate('placement_result_criteria', 'Please record placement result criteria before running auto placement.');
            return false;
        } elseif (empty($checkPlacementSettingIsRecord['ReservedPlace'])) {
            $this->invalidate('reserved_place', 'Please record reserved place for each department you want to participate in auto placement before running the auto placement.');
            return false;
        } elseif (empty($checkPlacementSettingIsRecord['participating_departemnt'])) {
            $this->invalidate('participating_department', 'Please record participating department you want in auto placement before running the auto placement.');
            return false;
        } else {
            return true;
        }
        //return $checkPlacementSettingIsRecord;
    }

    // check preference deadline is passed before running auto placement

    function isPreferenceDeadlinePassed($academicyear = null, $college_id = null)
    {
        $checkPreferenceDeadline = ClassRegistry::init('PreferenceDeadline')->find("count", array(
            "conditions" => array(
                'academicyear LIKE ' => $academicyear . '%',
                'college_id' => $college_id,
                'deadline >' => date("Y-m-d H:i:s")
            )
        ));

        if ($checkPreferenceDeadline) {
            $this->invalidate('preferencedeadline', 'The deadline for filling the preference is not passed. Please wait till the deadline is passed to run  the auto placement.');
            return false;
        }

        return true;
    }

    // Total number of students for given academicyear and college, who doesnt assigned department

    function total_no_assigned_to_department($college_id = null, $academicyear = null)
    {
        if ($college_id && $academicyear) {
            $conditions['OR'] = array(
                array('AcceptedStudent.department_id' => array('', 0)),
                array('AcceptedStudent.department_id' => null),
                array('AcceptedStudent.placementtype' => array(null, CANCELLED_PLACEMENT))
            );

            $conditions['AND'] = array(array(
                "AcceptedStudent.academicyear LIKE " => $academicyear . '%',
                "AcceptedStudent.college_id" => $college_id,
                "AcceptedStudent.placementtype" => null,
                "AcceptedStudent.Placement_Approved_By_Department" => 0,
                "AcceptedStudent.academicyear LIKE " => $academicyear . '%',
                "AcceptedStudent.college_id" => $college_id
            ));

            $count = $this->find('count', array('conditions' => $conditions));
            return $count;
        }
    }

    // Sort out participationg department in the placement based on the demand by privileage student most

    function getListOfDepartmentRequesteByPrivilegageStudentMost_old($academicyear = null, $college_id = null)
    {

        $regions = ClassRegistry::init('ParticipatingDepartment')->find("first", array(
            "conditions" => array(
                'ParticipatingDepartment.academic_year LIKE' => $academicyear . '%',
                'ParticipatingDepartment.college_id' => $college_id
            )
        ));

        $prvilaged = null;

        if (!empty($regions['ParticipatingDepartment']['developing_regions_id'])) {
            $prvilaged = array(
                "OR" => array(
                    "AcceptedStudent.region_id" => array($regions['ParticipatingDepartment']['developing_regions_id']),
                    "AcceptedStudent.sex" => "female",
                    "AcceptedStudent.disability <> null", "AcceptedStudent.disability <> ''"
                )
            );
        } else {
            $prvilaged = array(
                "OR" => array(
                    "AcceptedStudent.sex" => "female",
                    "AcceptedStudent.disability <> null",
                    "AcceptedStudent.disability <> ''"
                )
            );
        }

        $prefrenceMatrixOfDepartments = $this->Preference->find('all', array(
            'fields' => array(
                'Preference.department_id',
                'Preference.preferences_order',
                'count(Preference.accepted_student_id) as student_count'
            ),
            'conditions' => array(
                "Preference.academicyear LIKE" => $academicyear . '%',
                "Preference.college_id" => $college_id,
                $prvilaged,
                "OR" => array(
                    'AcceptedStudent.department_id is null',
                    'AcceptedStudent.department_id = ""',
                    'AcceptedStudent.department_id = 0',
                ),
                "AcceptedStudent.academicyear LIKE " => $academicyear . '%',
                "AcceptedStudent.college_id" => $college_id,
                "OR" => array(
                    "AcceptedStudent.placementtype IS NULL",
                    "AcceptedStudent.placementtype" => CANCELLED_PLACEMENT
                )
            ),

            'group' => array('Preference.department_id', 'Preference.preferences_order'),
            'order' => array('Preference.department_id', 'Preference.preferences_order')
        ));

        $prefrenceMatrix = array();

        if (!empty($prefrenceMatrixOfDepartments)) {
            foreach ($prefrenceMatrixOfDepartments as $key => $prefrenceMatrixOfDepartment) {
                $prefrenceMatrix[$prefrenceMatrixOfDepartment['Preference']['department_id']][$prefrenceMatrixOfDepartment['Preference']['preferences_order']] = $prefrenceMatrixOfDepartment[0]['student_count'];
            }
        } else {
            //return "NO PREFERENCE LIST";
        }

        $weight = array();
        $count = count($prefrenceMatrix);

        if (count($prefrenceMatrix)) {
            for ($i = 1; $i <= count($prefrenceMatrix); $i++) {
                $weight[$i] = $count--;
            }
        }

        $departmentsprivilagedorder = array();

        if (!empty($prefrenceMatrix)) {
            foreach ($prefrenceMatrix as $key => $value) {
                $sum = 0;
                $total_student = array_sum($value);

                //multipied each number of students by weight
                foreach ($value as $preference_key => $number_students) {
                    foreach ($weight as $weight_preference_key => $weight_preference_point) {
                        if ($preference_key == $weight_preference_key) {
                            $sum = $sum + ($weight_preference_point * $number_students);
                        }
                    }
                }

                //$departmentsprivilagedorder[$key]['sum']=$sum;
                $departmentsprivilagedorder[$key]['weight'] = $sum / (($total_student > 0) ? $total_student : 1);
            }
        }

        uasort($departmentsprivilagedorder, array(&$this, 'compare'));
        // need to clearification otherwise it is a mess, so need to comment out since top student can be assinged to their least privilaged.
        // we need to sort departments that don't have any quota for placement.

        $departments_without_privilaged_quota = ClassRegistry::init('ParticipatingDepartment')->find("all", array(
            "fields" => "ParticipatingDepartment.department_id", "recursive" => -1,
            "conditions" => array(
                'ParticipatingDepartment.academic_year LIKE ' => $academicyear . '%',
                'ParticipatingDepartment.college_id' => $college_id,
                array(
                    "OR" => array(
                        "ParticipatingDepartment.female" => 0,
                        "ParticipatingDepartment.female is null",
                        "ParticipatingDepartment.female = ''"
                    )
                ),
                array("OR" => array(
                    "ParticipatingDepartment.disability" => 0,
                    "ParticipatingDepartment.disability is null",
                    "ParticipatingDepartment.disability = ''"
                )),
                array(
                    "OR" => array(
                        "ParticipatingDepartment.regions" => 0,
                        "ParticipatingDepartment.regions is null",
                        "ParticipatingDepartment.regions = ''"
                    )
                )
            )
        ));

        //debug($departments_without_privilaged_quota);
        $merged_department_order = array();

        if (!empty($departments_without_privilaged_quota)) {
            foreach ($departments_without_privilaged_quota as $key => $value) {
                $merged_department_order[$value['ParticipatingDepartment']['department_id']]['weight'] = 10000000;
            }
        }

        if (!empty($departmentsprivilagedorder)) {
            foreach ($departmentsprivilagedorder as $k => $v) {
                if (!array_key_exists($k, $merged_department_order)) {
                    $merged_department_order[$k] = $v;
                }
            }
        }

        //retrive all departments
        $participating_departments = ClassRegistry::init('ParticipatingDepartment')->find('all', array(
            "fields" => "ParticipatingDepartment.department_id", "recursive" => -1,
            "conditions" => array(
                'ParticipatingDepartment.academic_year LIKE ' => $academicyear . '%',
                'ParticipatingDepartment.college_id' => $college_id
            )
        ));

        //debug($merged_department_order);

        if (!empty($participating_departments)) {
            foreach ($participating_departments as $y => $ParticipatingDepartment1) {
                $found = false;
                if (!empty($merged_department_order)) {
                    foreach ($merged_department_order as $department_id => $ParticipatingDepartment2) {
                        if ($ParticipatingDepartment1['ParticipatingDepartment']['department_id'] == $department_id) {
                            $found = true;
                            break;
                        }
                    }
                }
                if ($found == false) {
                    $merged_department_order[$ParticipatingDepartment1['ParticipatingDepartment']['department_id']]['weight'] = 10000;
                }
            }
        }
        //debug($merged_department_order);
        return $merged_department_order;
    }

    function getListOfDepartmentRequesteByPrivilegageStudentMost($academicyear = null, $college_id = null)
    {

        $prefrenceMatrixOfDepartments = $this->Preference->find('all', array(
            'fields' => array(
                'Preference.department_id',
                'Preference.preferences_order',
                'count(Preference.accepted_student_id) as student_count'
            ),
            'conditions' => array(
                "Preference.academicyear LIKE" => $academicyear . '%',
                "Preference.college_id" => $college_id,
                "OR" => array(
                    'AcceptedStudent.department_id is null ',
                    'AcceptedStudent.department_id = ""',
                    'AcceptedStudent.department_id = 0',
                ),
                "AcceptedStudent.academicyear LIKE " => $academicyear . '%',
                "AcceptedStudent.college_id" => $college_id,
                "OR" => array(
                    "AcceptedStudent.placementtype IS NULL",
                    "AcceptedStudent.placementtype" => CANCELLED_PLACEMENT
                )
            ),
            'group' => array('Preference.department_id', 'Preference.preferences_order'),
            'order' => array('Preference.department_id', 'Preference.preferences_order')
        ));

        $prefrenceMatrix = array();

        if (!empty($prefrenceMatrixOfDepartments)) {
            foreach ($prefrenceMatrixOfDepartments as $key => $prefrenceMatrixOfDepartment) {
                $prefrenceMatrix[$prefrenceMatrixOfDepartment['Preference']['department_id']][$prefrenceMatrixOfDepartment['Preference']['preferences_order']] = $prefrenceMatrixOfDepartment[0]['student_count'];
            }
        }

        //department capacity
        $department_capacity = ClassRegistry::init('ParticipatingDepartment')->find("all", array(
            "fields" => array("ParticipatingDepartment.department_id", "ParticipatingDepartment.number"),
            "recursive" => -1,
            "conditions" => array(
                'ParticipatingDepartment.academic_year LIKE ' => $academicyear . '%',
                'ParticipatingDepartment.college_id' => $college_id
            )
        ));

        $weight = array();
        $count = count($prefrenceMatrix);

        if (count($prefrenceMatrix)) {
            for ($i = 1; $i <= count($prefrenceMatrix); $i++) {
                $weight[$i] = $count--;
            }
        }

        $departmentsprivilagedorder = array();

        if (!empty($prefrenceMatrix)) {
            foreach ($prefrenceMatrix as $key => $value) {
                $sum = 0;
                $total_student = array_sum($value);

                //multipied each number of students by weight

                foreach ($value as $preference_key => $number_students) {
                    foreach ($weight as $weight_preference_key => $weight_preference_point) {
                        if ($preference_key == $weight_preference_key) {
                            $sum = $sum + ($weight_preference_point * $number_students);
                        }
                    }
                }
                //debug($department_capacity);
                $department_capacity_number = 1;

                if (!empty($department_capacity)) {
                    foreach ($department_capacity as $depat_key => $dept_value) {
                        if ($dept_value['ParticipatingDepartment']['department_id'] == $key) {
                            $department_capacity_number = $dept_value['ParticipatingDepartment']['number'];
                            break;
                        }
                    }
                }
                //$departmentsprivilagedorder[$key]['sum']=$sum;

                $departmentsprivilagedorder[$key]['weight'] = $sum / $department_capacity_number;
            }
        }

        uasort($departmentsprivilagedorder, array(&$this, 'compare'));
        return $departmentsprivilagedorder;

        // need to clearification otherwise it is a mess, so need to comment out since top student can be assinged to their least privilaged.
        // we need to sort departments that don't have any quota for placement.

        $departments_without_privilaged_quota = ClassRegistry::init('ParticipatingDepartment')->find("all", array(
            "fields" => "ParticipatingDepartment.department_id",
            "recursive" => -1,
            "conditions" => array(
                'ParticipatingDepartment.academic_year LIKE ' => $academicyear . '%',
                'ParticipatingDepartment.college_id' => $college_id,
                array(
                    "OR" => array(
                        "ParticipatingDepartment.female" => 0,
                        "ParticipatingDepartment.female is null",
                        "ParticipatingDepartment.female = ''"
                    )
                ),
                array(
                    "OR" => array(
                        "ParticipatingDepartment.disability" => 0,
                        "ParticipatingDepartment.disability is null",
                        "ParticipatingDepartment.disability = ''"
                    )
                ),
                array(
                    "OR" => array(
                        "ParticipatingDepartment.regions" => 0,
                        "ParticipatingDepartment.regions is null",
                        "ParticipatingDepartment.regions = ''"
                    )
                )
            )
        ));

        //debug($departments_without_privilaged_quota);
        $merged_department_order = array();

        if (!empty($departments_without_privilaged_quota)) {
            foreach ($departments_without_privilaged_quota as $key => $value) {
                $merged_department_order[$value['ParticipatingDepartment']['department_id']]['weight'] = 10000000;
            }
        }

        if (!empty($departmentsprivilagedorder)) {
            foreach ($departmentsprivilagedorder as $k => $v) {
                if (!array_key_exists($k, $merged_department_order)) {
                    $merged_department_order[$k] = $v;
                }
            }
        }

        //retrive all departments
        $participating_departments = ClassRegistry::init('ParticipatingDepartment')->find('all', array(
            "fields" => "ParticipatingDepartment.department_id",
            "recursive" => -1,
            "conditions" => array(
                'ParticipatingDepartment.academic_year LIKE ' => $academicyear . '%',
                'ParticipatingDepartment.college_id' => $college_id
            )
        ));

        if (!empty($participating_departments)) {
            foreach ($participating_departments as $y => $ParticipatingDepartment1) {
                $found = false;
                if (!empty($merged_department_order)) {
                    foreach ($merged_department_order as $department_id => $ParticipatingDepartment2) {
                        if ($ParticipatingDepartment1['ParticipatingDepartment']['department_id'] == $department_id) {
                            $found = true;
                            break;
                        }
                    }
                }

                if ($found == false) {
                    $merged_department_order[$ParticipatingDepartment1['ParticipatingDepartment']['department_id']]['weight'] = 10000;
                }
            }
        }
        //debug($merged_department_order);
        return $merged_department_order;
    }

    function compare($x, $y)
    {
        if ($x['weight'] < $y['weight']) {
            return true;
        } else {
            return false;
        }
    }


    function checkAndAdjustAllocationWithAvailability($academicyear = null, $college_id = null, $resulttype = null, $department_id = null, $reservedQuotaNumber = array())
    {

        //Here comes the majic of internal quota adjustment

        if (!empty($reservedQuotaNumber)) {
            $gap = 0;

            do {
                $recheck = false;

                foreach ($reservedQuotaNumber as $result_critiera_id => &$allocation_value) {
                    if ($allocation_value['reservedquota'] > $allocation_value['available']) {
                        $gap = $allocation_value['reservedquota'] - $allocation_value['available'];
                        $allocation_value['reservedquota'] = $allocation_value['reservedquota'] - $gap;
                        $recheck = true;
                        $allocation_value['adjusted'] = 1;
                        $reserved_sum = 0;

                        //this is get sum of reserved place
                        foreach ($reservedQuotaNumber as $result_critiera_id1 => $allocation_value1) {
                            if (!$allocation_value1['adjusted']) {
                                $reserved_sum += $allocation_value1["reservedquota"];
                            }
                        }

                        //distributing the gap to the remaining quota  proportionaly
                        $gap_distribution_sum = 0;
                        $max_reserved_quota = array('max_quota' => 0, 'max_index' => 0);

                        if ($reserved_sum > 0) {
                            foreach ($reservedQuotaNumber as $result_critiera_id2 => &$allocation_value2) {
                                if (!$allocation_value2['adjusted']) {
                                    $gap_distribution_sum += round($gap * ($allocation_value2['reservedquota'] / $reserved_sum));
                                    $allocation_value2['reservedquota'] += round($gap * ($allocation_value2['reservedquota'] / $reserved_sum));

                                    if ($allocation_value2['reservedquota'] >= $max_reserved_quota['max_quota']) {
                                        $max_reserved_quota['max_quota'] = $allocation_value2['reservedquota'];
                                        $max_reserved_quota['max_index'] = $result_critiera_id2;
                                    }
                                }
                            }
                        }

                        //check for excessive or lower allocation of gap and discard  if there is no student any of assigned quota
                        if ($gap_distribution_sum != $gap && $max_reserved_quota['max_index'] != 0) {
                            $reservedQuotaNumber[$max_reserved_quota['max_index']]['reservedquota'] += ($gap - $gap_distribution_sum);
                        } else {
                            // in case of availability is greater than reserved quota after adjustment has done dont discard the students,
                            // rather set  available to reserved quota of result category, in the next version think of proportionality ?

                            if (($gap - $gap_distribution_sum) > 0) {
                                foreach ($reservedQuotaNumber as $result_critiera_id3 => &$allocation_value3) {
                                    if ($allocation_value3['reservedquota'] == 0 && !$allocation_value3['adjusted'] && $allocation_value3['available'] > 0) {
                                        $allocation_value3['reservedquota'] = ($allocation_value3['available'] >= ($gap - $gap_distribution_sum) ? ($gap - $gap_distribution_sum) : $allocation_value3['available']);
                                        $gap_distribution_sum += $allocation_value3['reservedquota'];

                                        if (($gap - $gap_distribution_sum) <= 0) {
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } while ($recheck);

            return $reservedQuotaNumber;
        }
    }

    // Method to adjust privilaged quota  return adjusted value of the privilaged quota

    function checkAndAdjustPrivilagedQuota($academicyear = null, $college_id = null, $resulttype = null, $department_id = null, $adjusted_privilaged_quota = array(), $reservedQuotaNumber = array())
    {

        //get count of participating deparment for the given college and academic year
        $number_of_participating_department = ClassRegistry::init('ParticipatingDepartment')->find('count', array('conditions' => array('ParticipatingDepartment.college_id' => $college_id, 'ParticipatingDepartment.academic_year' => $academicyear)));
        // do for the three privilaged

        if (!empty($adjusted_privilaged_quota)) {
            foreach ($adjusted_privilaged_quota as $privilage_type => &$quota) {
                $privilagedcondition = null;

                if (strcasecmp($privilage_type, "female") == 0) {
                    $privilagedcondition = "AcceptedStudent.sex='female'";
                } elseif (strcasecmp($privilage_type, "disability") == 0) {
                    $privilagedcondition = "AcceptedStudent.disability IS NOT NULL";
                } else {
                    $regions = ClassRegistry::init('ParticipatingDepartment')->find('first', array('conditions' => array('ParticipatingDepartment.college_id' => $college_id, 'ParticipatingDepartment.academic_year' => $academicyear)));

                    if (empty($regions['ParticipatingDepartment']['developing_regions_id'])) {
                        continue;
                    }

                    $privilagedcondition = "AcceptedStudent.region_id IN (" . $regions['ParticipatingDepartment']['developing_regions_id'] . ")";
                }

                // iterate each students availabilty against preference order for the given deparment_id

                $sum_available_students_privilaged = 0;
                $list_students_in_x_preference = array();

                if ($number_of_participating_department && $quota) {
                    // the logic is unkown ?
                    for ($i = 1; $i < $number_of_participating_department; $i++) {
                        $list_students_in_x_preference = $this->Preference->find('all', array(
                                'fields' => array('Preference.accepted_student_id', 'Preference.department_id'),
                                'conditions' => array(
                                    'Preference.academicyear LIKE ' => $academicyear . '%',
                                    'Preference.college_id' => $college_id,
                                    'Preference.department_id' => $department_id,
                                    'Preference.preferences_order' => $i,
                                    $privilagedcondition,
                                    'AcceptedStudent.college_id' => $college_id,
                                    'AcceptedStudent.academicyear LIKE ' => $academicyear . '%',
                                    array(
                                        "OR" => array(
                                            'AcceptedStudent.department_id is null',
                                            'AcceptedStudent.department_id = ""',
                                            'AcceptedStudent.department_id = 0',
                                        )
                                    )
                                )
                        ));

                        // simply count privilaged students in preference 1  for a particular department
                        if ($i == 1) {
                            $sum_available_students_privilaged += count($list_students_in_x_preference);
                            // if there are enough students by their first preference for allocated quota for the department.
                            // no need to continue the loop if there are enough privilaged students in system
                            if ($sum_available_students_privilaged >= $quota) {
                                break;
                            }
                            continue;
                        }

                        // we need to have already allocated departments_id
                        $reformat_list_of_department_ids = array();

                        $list_of_departments_id = $this->find('all', array(
                            'fields' => array('DISTINCT AcceptedStudent.department_id'),
                            'conditions' => array(
                                'AcceptedStudent.academicyear LIKE ' => $academicyear . '%',
                                'AcceptedStudent.college_id' => $college_id,
                                'AcceptedStudent.department_id is not null ',
                                'AcceptedStudent.department_id not ' => array('', 0)
                            ),
                            'recursive' => -1
                        ));

                        if (!empty($list_of_departments_id)) {
                            foreach ($list_of_departments_id as $key => $value) {
                                $reformat_list_of_department_ids[] = $value['AcceptedStudent']['department_id'];
                            }
                        }

                        $excluded_student_count = 0;
                        //per students check for departments assingment and exclude

                        if (!empty($list_students_in_x_preference)) {
                            foreach ($list_students_in_x_preference as &$student) {
                                //check students back preference if they are not assigned.
                                for ($j = 1; $j < $i; $j++) {
                                    $department_id_accepted_student = $this->Preference->find('first', array(
                                        'fields' => array('Preference.department_id'),
                                        'conditions' => array(
                                            'Preference.accepted_student_id' => $student['Preference']['accepted_student_id'],
                                            'Preference.preferences_order' => $j
                                        )
                                    ));

                                    // is her/his previous preference selected department was processed?
                                    if (in_array($department_id_accepted_student['Preference']['department_id'], $reformat_list_of_department_ids) === false) {
                                        $excluded_student_count++;
                                        break;
                                    }
                                }
                            }
                        }

                        $sum_available_students_privilaged += (count($list_students_in_x_preference) - $excluded_student_count);

                        if ($sum_available_students_privilaged >= $quota) {
                            break;
                        }
                    }

                    //$adjusted_privilaged_quota=array(),$reservedQuotaNumber
                    // Adjust female quota if the supply is scarce and proportional
                    // give to result range which is reserved quota.

                    if ($sum_available_students_privilaged < $quota) {
                        //call function
                        $privilaged_quota_gap = ($quota - $sum_available_students_privilaged);
                        $quota -= $privilaged_quota_gap;

                        ///////////////////////COPIED FROM checkAndAdjustAllocationWithAvailability FUNCTION//////////////
                        $reserved_sum = 0;
                        //this is get sum of reserved place

                        if (!empty($reservedQuotaNumber)) {
                            foreach ($reservedQuotaNumber as $result_critiera_id1 => $allocation_value1) {
                                //if(!isset($allocation_value1['adjusted']) || (isset($allocation_value1['adjusted']) && $allocation_value1['adjusted'] != 1)){
                                if (!$allocation_value1['adjusted']) {
                                    $reserved_sum += $allocation_value1["reservedquota"];
                                }
                            }
                        }

                        //distributing the gap to the remaining quota  proportionaly
                        $gap_distribution_sum = 0;
                        $max_reserved_quota = array('max_quota' => 0, 'max_index' => 0);

                        if ($reserved_sum > 0) {
                            foreach ($reservedQuotaNumber as $result_critiera_id2 => &$allocation_value2) {
                                if (!$allocation_value2['adjusted']) {
                                    $gap_distribution_sum += round($privilaged_quota_gap * ($allocation_value2['reservedquota'] / $reserved_sum));
                                    $allocation_value2['reservedquota'] += round($privilaged_quota_gap * ($allocation_value2['reservedquota'] / $reserved_sum));

                                    if ($allocation_value2['reservedquota'] >= $max_reserved_quota['max_quota']) {
                                        $max_reserved_quota['max_quota'] = $allocation_value2['reservedquota'];
                                        $max_reserved_quota['max_index'] = $result_critiera_id2;
                                    }
                                }
                            }
                        }

                        //check for excessive or lower allocation of gap and discard if there is no student any of assigned quota
                        if ($gap_distribution_sum != $privilaged_quota_gap && $max_reserved_quota['max_index'] != 0) {
                            $reservedQuotaNumber[$max_reserved_quota['max_index']]['reservedquota'] += ($privilaged_quota_gap - $gap_distribution_sum);
                        }
                    }
                }
            } // end of the three privilages
        }

        $array_reserved_privilaged_merged[] = $reservedQuotaNumber;
        $array_reserved_privilaged_merged[] = $adjusted_privilaged_quota;

        return $array_reserved_privilaged_merged;
    }


    function privilagedStudentsFilterOut($academicyear = null, $college_id = null, $resulttype = null, $department_id = null, $adjusted_privilaged_quota = array(), $reservedQuotaNumber = array(), $placedStudents = array(), $privilage_type = null)
    {

        $competitivly_assigned_students = (empty($placedStudents['C']) ? array() : $placedStudents['C']);
        $quota_assigned_students = (empty($placedStudents['Q']) ? array() : $placedStudents['Q']);

        //get count of participating deparment for the given college and academic year
        $number_of_participating_department = ClassRegistry::init('ParticipatingDepartment')->find('count', array('conditions' => array('ParticipatingDepartment.college_id' => $college_id, 'ParticipatingDepartment.academic_year' => $academicyear)));

        if (strcasecmp($privilage_type, "female") == 0) {
            $privilagedcondition = "AcceptedStudent.sex='female'";
        } elseif (strcasecmp($privilage_type, "disability") == 0) {
            $privilagedcondition = "AcceptedStudent.disability IS NOT NULL";
        } else {
            $regions = ClassRegistry::init('ParticipatingDepartment')->find('first', array('conditions' => array('ParticipatingDepartment.college_id' => $college_id, 'ParticipatingDepartment.academic_year' => $academicyear)));

            if (empty($regions['ParticipatingDepartment']['developing_regions_id'])) {
                return array();
            }

            $privilagedcondition = "AcceptedStudent.region_id IN (" . $regions['ParticipatingDepartment']['developing_regions_id'] . ")";
        }

        $list_students_in_x_preference = array();
        $list_of_students_selected = array();
        $result_order_by = null;

        if ($resulttype) {
            $result_order_by = 'AcceptedStudent.EHEECE_total_results desc';
        } else {
            $result_order_by = 'AcceptedStudent.freshman_result desc';
        }
        //debug($number_of_participating_department);
        //debug($adjusted_privilaged_quota);

        if ($number_of_participating_department && $adjusted_privilaged_quota[$privilage_type] > 0) {
            // the logic is unkown ?
            for ($i = 1; $i < $number_of_participating_department; $i++) {
                $list_students_in_x_preference = $this->Preference->find('all', array(
                        'fields' => array('Preference.accepted_student_id'),
                        'order' => $result_order_by,
                        'conditions' => array(
                            'Preference.academicyear LIKE ' => $academicyear . '%',
                            'Preference.college_id' => $college_id,
                            'Preference.department_id' => $department_id,
                            'Preference.preferences_order' => $i,
                            $privilagedcondition,
                            'AcceptedStudent.college_id' => $college_id,
                            'AcceptedStudent.academicyear LIKE ' => $academicyear . '%',
                            array(
                                "OR" => array(
                                    'AcceptedStudent.department_id is null',
                                    'AcceptedStudent.department_id = ""',
                                    'AcceptedStudent.department_id = 0',

                                )
                            )
                        )
                ));
                // simply count privilaged students in preference 1 for a particular department
                //debug($department_id);
                //debug($privilage_type);
                //debug($list_students_in_x_preference);

                if ($i == 1) {
                    //if the students is not in competitive list, please  consider me in the quota.
                    //debug($competitivly_assigned_students);
                    //debug($quota_assigned_students);
                    foreach ($list_students_in_x_preference as $student) {
                        if (in_array($student['Preference']['accepted_student_id'], $competitivly_assigned_students) === false && in_array($student['Preference']['accepted_student_id'], $quota_assigned_students) === false) {
                            $list_of_students_selected[] = $student['Preference']['accepted_student_id'];
                            //echo "Giba = ".$student['Preference']['accepted_student_id'];
                        }
                        //else
                        //  echo "Atigba = ".$student['Preference']['accepted_student_id'];
                    }
                    //debug($list_of_students_selected);
                    // if there are enough students by their first preference for allocated quota for the department. no need to continue the loop if there are enough privilaged students in system
                    if (count($list_of_students_selected) >= $adjusted_privilaged_quota[$privilage_type]) {
                        break;
                    }

                    continue;
                }

                // we need to have already allocated departments_id
                $reformat_list_of_department_ids = array();

                $list_of_departments_id = $this->find('all', array(
                    'fields' => array('DISTINCT AcceptedStudent.department_id'),
                    'conditions' => array(
                        'AcceptedStudent.academicyear LIKE ' => $academicyear . '%',
                        'AcceptedStudent.college_id' => $college_id,
                        'AcceptedStudent.department_id is not null ',
                        'AcceptedStudent.department_id not ' => array('', 0)
                    ),
                    'recursive' => -1
                ));

                if (!empty($list_of_departments_id)) {
                    foreach ($list_of_departments_id as $key => $value) {
                        $reformat_list_of_department_ids[] = $value['AcceptedStudent']['department_id'];
                    }
                }

                $excluded_student_count = 0;
                //per students check for departments assingment and exclude
                $preliminary_students_filter = array();

                if (!empty($list_students_in_x_preference)) {
                    foreach ($list_students_in_x_preference as &$student) {
                        //check students back preferenc if they are not assigned.
                        $exclude_student = false;
                        for ($j = 1; $j < $i; $j++) {
                            $department_id_accepted_student = $this->Preference->find('first', array(
                                    'fields' => array('Preference.department_id'),
                                    'conditions' => array(
                                        'Preference.accepted_student_id' => $student['Preference']['accepted_student_id'],
                                        'Preference.preferences_order' => $j
                                    )
                            ));

                            // is her/his previous preference selected department was
                            // processed ? Exclude from selecting, wait till her
                            // preference runs.
                            if (in_array($department_id_accepted_student['Preference']['department_id'], $reformat_list_of_department_ids) === false) {
                                $exclude_student = true;
                                break;
                            }
                        }

                        if (!$exclude_student) {
                            $preliminary_students_filter[] = $student['Preference']['accepted_student_id'];
                        }
                    }

                    //if the students is not in competitive list, please  consider me in the quota.

                    if (!empty($preliminary_students_filter)) {
                        foreach ($preliminary_students_filter as $student_id) {
                            if (in_array($student_id, $competitivly_assigned_students) === false && in_array($student_id, $quota_assigned_students) === false) {
                                $list_of_students_selected[] = $student_id;
                            }
                        }
                    }
                }

                if (count($list_of_students_selected) >= $adjusted_privilaged_quota[$privilage_type]) {
                    break;
                }
            }

            $privilaged_selected[$privilage_type] = $list_of_students_selected;
            return $privilaged_selected;
        }
        //nothing
        return array();
    }

    function runAutoParallelAssignmentAfterSeq($academicyear = null, $college_id = null, $resulttype = null)
    {
        //find students who is assigned by quota
        $acceptedStudentPlacedByQuota = $this->find('all', array(
            'conditions' => array(
                'AcceptedStudent.college_id' => $college_id,
                'AcceptedStudent.academicyear like' => $academicyear . '%',
                "AcceptedStudent.placement_based" => 'Q',
                "AcceptedStudent.placementtype" => 'AUTO PLACED',
                "AcceptedStudent.Placement_Approved_By_Department is null"
            ),
            'contain' => array('Department')
        ));

        debug($acceptedStudentPlacedByQuota);
        //find students who is assigned by quota
        $acceptedStudents = $this->find('all', array(
            'conditions' => array(
                'AcceptedStudent.college_id' => $college_id,
                "AcceptedStudent.academicyear like " => $academicyear . '%',
                //'AcceptedStudent.placement_based'=>'C',
                "AcceptedStudent.placementtype" => "AUTO PLACED",
                "AcceptedStudent.Placement_Approved_By_Department is null",
                "AcceptedStudent.program_id" => PROGRAM_UNDEGRADUATE,
                'OR' => array(
                    "AcceptedStudent.program_type_id" => PROGRAM_TYPE_REGULAR,
                    "AcceptedStudent.program_type_id" => PROGRAM_TYPE_ADVANCE_STANDING,
                ),
                //"AcceptedStudent.department_id is not null"
            ),
            'limit' => 1000000,
            'recursive' => -1
        ));

        //cancel sequential placement
        $placement_cancelation_list = array();

        if (!empty($acceptedStudents)) {
            foreach ($acceptedStudents as $acceptedStudent) {
                $index = count($placement_cancelation_list);
                $placement_cancelation_list[$index]['id'] = $acceptedStudent['AcceptedStudent']['id'];
                $placement_cancelation_list[$index]['placementtype'] = CANCELLED_PLACEMENT;
                $placement_cancelation_list[$index]['minute_number'] = null;
                $placement_cancelation_list[$index]['department_id'] = null;
            }
        }

        $autoplacedstudents = array();

        if (!empty($placement_cancelation_list)) {
            if ($this->saveAll($placement_cancelation_list)) {
                //run the sequential
                $autoplacedstudents = $this->auto_parallel_assignment($academicyear, $college_id, $resulttype);

                if (isset($autoplacedstudents) && !empty($autoplacedstudents)) {
                    $placedStudentsSave = array();
                    $count = 0;

                    if (!empty($acceptedStudentPlacedByQuota)) {
                        foreach ($acceptedStudentPlacedByQuota as $ack => $acv) {
                            debug($acv);
                            $placedStudentsSave['AcceptedStudent'][$count]['id'] = $acv['AcceptedStudent']['id'];
                            $placedStudentsSave['AcceptedStudent'][$count]['placementtype'] = AUTO_PLACEMENT;
                            $placedStudentsSave['AcceptedStudent'][$count]['placement_based'] = 'Q';
                            $placedStudentsSave['AcceptedStudent'][$count]['department_id'] = $acv['AcceptedStudent']['department_id'];
                            $count++;
                            //$autoplacedstudents[$acv['Department']['name']][]=$acceptedStudentPlacedByQuota[$ack];
                        }
                    }

                    if (!empty($placedStudentsSave)) {
                        if ($this->saveAll($placedStudentsSave['AcceptedStudent'])) {
                            //
                        } else {
                            debug($this->invalidFields());
                        }
                    }
                }
            }
        }

        return $autoplacedstudents;
    }

    function auto_placement_algorithm($academicyear = null, $college_id = null, $resulttype = null, $high_proprity_for_high_result = null, $first_consider_first = null)
    {
        //list of departments orderd by based on the demand by the privilaged students
        $departments = $this->getListOfDepartmentRequesteByPrivilegageStudentMost($academicyear, $college_id, $resulttype);

        //debug($departments);
        //return;
        if (!empty($departments)) {
            foreach ($departments as $department_id => $weight) {
                //// privilaged quota adjustment
                // retrieve and reformat privilaged quota
                //simply read quota for each deparment
                $adjusted_privilaged_quota = array();

                $detail_of_participating_department = ClassRegistry::init('ParticipatingDepartment')->find('first', array(
                    'conditions' => array(
                        'ParticipatingDepartment.college_id' => $college_id,
                        'ParticipatingDepartment.academic_year' => $academicyear,
                        'ParticipatingDepartment.department_id' => $department_id
                    )
                ));

                $adjusted_privilaged_quota['female'] = $detail_of_participating_department['ParticipatingDepartment']['female'];
                $adjusted_privilaged_quota['regions'] = $detail_of_participating_department['ParticipatingDepartment']['regions'];
                $adjusted_privilaged_quota['disability'] = $detail_of_participating_department['ParticipatingDepartment']['disability'];


                ///retrive and reformat placement result criteria with reserved quota

                $reservedPlaceForDepartmentByGradeRange = ClassRegistry::init('PlacementsResultsCriteria')->reservedPlaceForDepartmentByGradeRange($academicyear, $college_id, $department_id);
                $reservedQuotaNumber = array();

                if (!empty($reservedPlaceForDepartmentByGradeRange)) {
                    foreach ($reservedPlaceForDepartmentByGradeRange as $key => $grade_ranage_quota) {
                        $reservedQuotaNumber[$grade_ranage_quota["PlacementsResultsCriteria"]["id"]]['reservedquota'] = $grade_ranage_quota['ReservedPlace']['number'];

                        $availablestudentInGivenRanage = $this->availableStudentInGivenRangeAndQuota($academicyear, $college_id, $resulttype, $grade_ranage_quota);

                        $reservedQuotaNumber[$grade_ranage_quota["PlacementsResultsCriteria"]["id"]]['available'] = $availablestudentInGivenRanage;
                        $reservedQuotaNumber[$grade_ranage_quota["PlacementsResultsCriteria"]["id"]]['adjusted'] = 0;
                    }
                }
                /// retreive and reformat end ===================

                //// adjusted privilaged  quota
                $pre_ready_normal_privilaged_department_allocation = $this->checkAndAdjustPrivilagedQuota(
                    $academicyear,
                    $college_id,
                    $resulttype,
                    $department_id,
                    $adjusted_privilaged_quota,
                    $reservedQuotaNumber
                );

                //// competitive quota adjustment
                $ready_competitive_department_allocation = $this->checkAndAdjustAllocationWithAvailability(
                    $academicyear,
                    $college_id,
                    $resulttype,
                    $department_id,
                    $pre_ready_normal_privilaged_department_allocation[0]
                );

                // do allocation for each result category
                $placedStudents = array();
                //make ready for competitive assignment before saving to the database
                //debug($pre_ready_normal_privilaged_department_allocation);
                //debug($ready_competitive_department_allocation);

                do {
                    $completed = false;
                    //debug($department_id);
                    //debug($ready_competitive_department_allocation);
                    if (isset($ready_competitive_department_allocation) && !empty($ready_competitive_department_allocation)) {
                        foreach ($ready_competitive_department_allocation as $result_category_id => $reserved_quota_adjusted) {
                            if ($reserved_quota_adjusted['reservedquota'] > 0) {
                                $sortedStudentByPreferenceAndGrade = $this->sortOutStudentByPreference(
                                    $college_id,
                                    $academicyear,
                                    $result_category_id,
                                    $resulttype,
                                    $department_id,
                                    $high_proprity_for_high_result,
                                    $first_consider_first
                                );
                                //debug($department_id);
                                //debug($sortedStudentByPreferenceAndGrade);
                                if (!empty($sortedStudentByPreferenceAndGrade)) {
                                    {
                                    $n = ($reserved_quota_adjusted['reservedquota'] <= count($sortedStudentByPreferenceAndGrade) ? $reserved_quota_adjusted['reservedquota'] : count($sortedStudentByPreferenceAndGrade));

                                    if ($n) {
                                        for ($i = 0; $i < $n; $i++) {
                                            $placedStudents['C'][] = $sortedStudentByPreferenceAndGrade[$i]['AcceptedStudent']['id'];
                                        }
                                    }
                                    //debug($department_id);
                                    //debug($reserved_quota_adjusted['reservedquota']);
                                    //debug($placedStudents);
                                    //debug($sortedStudentByPreferenceAndGrade);
                                    unset($sortedStudentByPreferenceAndGrade);
                                    //debug($sortedStudentByPreferenceAndGrade);
                                    }
                                }
                            }
                        }
                    }
                    //$academicyear=null,$college_id=null,$resulttype=null
                    //debug($department_id);
                    //debug($pre_ready_normal_privilaged_department_allocation);
                    if (isset($pre_ready_normal_privilaged_department_allocation[1]) && !empty($pre_ready_normal_privilaged_department_allocation[1])) {
                        if ($pre_ready_normal_privilaged_department_allocation[1]['female'] == 0 && $pre_ready_normal_privilaged_department_allocation[1]['regions'] == 0 && $pre_ready_normal_privilaged_department_allocation[1]['disability'] == 0) {
                            $completed = true;
                        }
                        //iterate for three famious privilage
                        foreach ($pre_ready_normal_privilaged_department_allocation[1] as $privilage_type => &$quota) {
                            if ($quota > 0) {
                                $completed = false;
                                $privilaged_selected = $this->privilagedStudentsFilterOut(
                                    $academicyear,
                                    $college_id,
                                    $resulttype,
                                    $department_id,
                                    $pre_ready_normal_privilaged_department_allocation[1],
                                    $ready_competitive_department_allocation,
                                    $placedStudents,
                                    $privilage_type
                                );
                                //debug($department_id);
                                //debug($privilage_type);
                                //debug($privilaged_selected);
                                if (!empty($privilaged_selected) && $quota <= count($privilaged_selected[$privilage_type])) {
                                    $n = $quota;

                                    if ($n) {
                                        for ($i = 0; $i < $n; $i++) {
                                            $placedStudents['Q'][] = $privilaged_selected[$privilage_type][$i];
                                        }
                                    }

                                    $completed = true;
                                } else {
                                    unset($placedStudents);
                                    $placedStudents = array();
                                    $gap = $quota - (!empty($privilaged_selected) ? count($privilaged_selected[$privilage_type]) : 0);
                                    $quota -= $gap;
                                    ///////////////////////COPIED FROM checkAndAdjustAllocationWithAvailability FUNCTION//////////////
                                    $reserved_sum = 0;
                                    //this is get sum of reserved place
                                    foreach ($ready_competitive_department_allocation as $result_category_id => $reserved_quota_adjusted) {
                                        $reserved_quota_adjusted['adjusted'] = 0;
                                        $reserved_sum += $reserved_quota_adjusted["reservedquota"];
                                    }
                                    //distributing the gap to the remaining quota
                                    // proportionaly
                                    $gap_distribution_sum = 0;
                                    $max_reserved_quota = array('max_quota' => 0, 'max_index' => 0);

                                    if ($reserved_sum > 0) {
                                        foreach ($ready_competitive_department_allocation as $result_critiera_id2 => &$allocation_value2) {
                                            $gap_distribution_sum += round($gap * ($allocation_value2['reservedquota'] / $reserved_sum));
                                            $allocation_value2['reservedquota'] += round($gap * ($allocation_value2['reservedquota'] / $reserved_sum));
                                            if ($allocation_value2['reservedquota'] >= $max_reserved_quota['max_quota']) {
                                                $max_reserved_quota['max_quota'] = $allocation_value2['reservedquota'];
                                                $max_reserved_quota['max_index'] = $result_critiera_id2;
                                            }
                                        }
                                    }
                                    //check for excessive or lower allocation of gap and discard
                                    // if there is no student any of assigned quota
                                    if ($gap_distribution_sum != $gap && $max_reserved_quota['max_index'] != 0) {
                                        $ready_competitive_department_allocation[$max_reserved_quota['max_index']]['reservedquota'] += ($gap - $gap_distribution_sum);
                                    } else {
                                        ////////////////////////////////Used to handle if all competitive quota is zero but we have privilaged quota////////////////////////////////////////

                                        // in case of availability is greater than
                                        // reserved quota after adjustment has done
                                        // dont descard the students, rather set
                                        // available to reserved quota of result
                                        // category, in the next version think of proportionality ?
                                        if (($gap - $gap_distribution_sum) > 0) {
                                            //debug($ready_competitive_department_allocation);
                                            //$ready_competitive_department_allocation
                                            foreach ($ready_competitive_department_allocation as $result_critiera_id3 => &$allocation_value3) {
                                                if ($allocation_value3['reservedquota'] == 0 && !$allocation_value3['adjusted'] && $allocation_value3['available'] > 0) {
                                                    {
                                                    $allocation_value3['reservedquota'] = ($allocation_value3['available'] >= ($gap - $gap_distribution_sum) ? ($gap - $gap_distribution_sum) : $allocation_value3['available']);
                                                    $gap_distribution_sum += $allocation_value3['reservedquota'];
                                                    }

                                                    if (($gap - $gap_distribution_sum) <= 0) {
                                                        break;
                                                    }
                                                }
                                            }
                                            //debug($ready_competitive_department_allocation);
                                            //debug($completed);
                                        }
                                    }
                                    //////////////////////////////////////////////////////////////////////////


                                    //debug($ready_competitive_department_allocation);
                                    $ready_competitive_department_allocation = $this->checkAndAdjustAllocationWithAvailability(
                                        $academicyear,
                                        $college_id,
                                        $resulttype,
                                        $department_id,
                                        $ready_competitive_department_allocation
                                    );
                                    //debug($ready_competitive_department_allocation);
                                    break;
                                }
                            }
                        } // end of privilaged student quota selection loop
                    }
                } while ($completed == false);

                // reformat placedStudents to make suitable for saveAll and
                // flag C and Q in the database.
                //debug($department_id);
                //debug($placedStudents);
                if (!empty($placedStudents)) {
                    $placedStudentsSave = array();
                    $count = 0;
                    foreach ($placedStudents as $key => $value) {
                        //ras
                        foreach ($value as $k => $student_id) {
                            $placedStudentsSave['AcceptedStudent'][$count]['id'] = $student_id;
                            $placedStudentsSave['AcceptedStudent'][$count]['placementtype'] = AUTO_PLACEMENT;
                            $placedStudentsSave['AcceptedStudent'][$count]['placement_based'] = $key;
                            $placedStudentsSave['AcceptedStudent'][$count]['department_id'] = $department_id;
                            $count++;
                        }
                    }

                    //debug($department_id);
                    //debug($placedStudentsSave);
                    if (!empty($placedStudentsSave)) {
                        //assign students to departments
                        //debug($placedStudentsSave['AcceptedStudent']);
                        $this->saveAll($placedStudentsSave['AcceptedStudent']);
                    }
                }
            } //  end department by department

            $result_order_by = null;

            if ($resulttype) {
                $result_order_by = 'AcceptedStudent.EHEECE_total_results desc';
            } else {
                $result_order_by = 'AcceptedStudent.freshman_result desc';
            }

            // $x=$this->runAutoParallelAssignmentAfterSeq($academicyear,$college_id,$resulttype);

            $placedstudent = $this->find('all', array(
                'conditions' => array(
                    'AcceptedStudent.college_id' => $college_id,
                    'AcceptedStudent.academicyear LIKE ' => $academicyear . '%',
                    'AcceptedStudent.placementtype' => AUTO_PLACEMENT
                ),
                'order' => array('AcceptedStudent.department_id asc', $result_order_by)
            ));

            $dep_id = array_keys($departments);
            $dep_name = $this->Department->find('list', array('conditions' => array('Department.id' => $dep_id)));
            $newly_placed_student = array();

            if (!empty($dep_name)) {
                foreach ($dep_name as $dk => $dv) {
                    if (!empty($placedstudent)) {
                        foreach ($placedstudent as $k => $v) {
                            if ($dk == $v['Department']['id']) {
                                $newly_placed_student[$dv][$k] = $v;
                            }
                        }
                    }

                    $newly_placed_student['auto_summery'][$dv]['C'] = $this->find('count', array(
                        'conditions' => array(
                            'AcceptedStudent.academicyear LIKE' => $academicyear . '%',
                            'AcceptedStudent.department_id' => $dk,
                            'AcceptedStudent.college_id' => $college_id,
                            'AcceptedStudent.placement_based' => 'C'
                        )
                    ));

                    $newly_placed_student['auto_summery'][$dv]['Q'] = $this->find('count', array(
                        'conditions' => array(
                            'AcceptedStudent.academicyear LIKE' => $academicyear . '%',
                            'AcceptedStudent.department_id' => $dk,
                            'AcceptedStudent.college_id' => $college_id,
                            'AcceptedStudent.placement_based' => 'Q'
                        )
                    ));
                }
            }

            return $newly_placed_student;
        }  // no participating department algorithm will not executed.
    } // function end


    function sortOutStudentByPreference($college_id = null, $academicyear = null, $result_category_id = null, $result_type = null, $department_id = null, $high_proprity_for_high_result = null, $first_consider_first = null)
    {
        $result_type_condition = null;
        $result_order_by = null;

        $resultcategoryvalue = ClassRegistry::init('PlacementsResultsCriteria')->find('first', array(
            'conditions' => array(
                'PlacementsResultsCriteria.id' => $result_category_id,
                'PlacementsResultsCriteria.admissionyear' => $academicyear,
                'PlacementsResultsCriteria.college_id' => $college_id
            )
        ));

        if ($result_type) {
            $result_type_condition = '`AcceptedStudent.EHEECE_total_results` >=' . $resultcategoryvalue["PlacementsResultsCriteria"]["result_from"] . ' and `AcceptedStudent.EHEECE_total_results` <=' . $resultcategoryvalue["PlacementsResultsCriteria"]["result_to"];
            $result_order_by = 'AcceptedStudent.EHEECE_total_results desc';
        } else {
            $result_type_condition = '`AcceptedStudent.freshman_result` >=' . $resultcategoryvalue["PlacementsResultsCriteria"]["result_from"] . ' and `AcceptedStudent.freshman_result` <=' . $resultcategoryvalue["PlacementsResultsCriteria"]["result_to"];
            $result_order_by = 'AcceptedStudent.freshman_result desc';
            debug($result_type_condition);
        }

        // students who completed their preference on time
        $sort_out_students_result_category = $this->find('all', array(
            'joins' => array(array(
                'table' => 'preferences',
                'alias' => 'Preference',
                'type' => 'LEFT',
                'conditions' => array('Preference.accepted_student_id = AcceptedStudent.id')
            )),

            'fields' => array(
                'AcceptedStudent.id',
                'AcceptedStudent.EHEECE_total_results',
                'AcceptedStudent.freshman_result',
                'Preference.preferences_order'
            ),
            'recursive' => -1,
            'conditions' => array(
                "OR" => array(
                    'AcceptedStudent.department_id IS NULL',
                    'AcceptedStudent.department_id = ""',
                    'AcceptedStudent.department_id = 0',
                ),
                "AcceptedStudent.academicyear LIKE " => $academicyear . '%',
                "AcceptedStudent.college_id" => $college_id,
                "Preference.department_id" => $department_id,
                $result_type_condition
            ),
            'order' => array('Preference.preferences_order asc', $result_order_by)
        ));

        // students who doesnt complete their preference on time
        $sort_out_students_not_completed_category = $this->find('all', array(
            'joins' => array(array(
                'table' => 'preferences',
                'alias' => 'Preference',
                'type' => 'LEFT',
                'conditions' => array('Preference.accepted_student_id = AcceptedStudent.id')
            )),

            'fields' => array(
                'AcceptedStudent.id',
                'AcceptedStudent.EHEECE_total_results',
                'AcceptedStudent.freshman_result',
                'Preference.preferences_order'
            ),
            'recursive' => -1,
            'conditions' => array(
                "OR" => array(
                    'AcceptedStudent.department_id IS NULL',
                    'AcceptedStudent.department_id = ""',
                    'AcceptedStudent.department_id = 0',
                ),
                "AcceptedStudent.academicyear LIKE " => $academicyear . '%',
                "AcceptedStudent.college_id" => $college_id,
                "AcceptedStudent.id not in (select accepted_student_id from preferences where preferences.department_id = " . $department_id . ")", $result_type_condition
            ),
            'order' => array('Preference.preferences_order asc', $result_order_by)
        ));

        debug($sort_out_students_not_completed_category);

        if (count($sort_out_students_not_completed_category)) {
            for ($i = 0; $i < count($sort_out_students_not_completed_category); $i++) {
                array_push($sort_out_students_result_category, $sort_out_students_not_completed_category[$i]);
            }
        }

        //If enforce student other prferences as long as s/he has good result irespective of other chance
        $students_to_be_sorted = array();
        $students_to_be_removed = array();

        if ($high_proprity_for_high_result || $first_consider_first) {
            foreach ($sort_out_students_result_category as $k => $v) {
                if (!empty($v['Preference']['preferences_order']) && $v['Preference']['preferences_order'] > 1) {
                    $previous_preferences = ClassRegistry::init('Preference')->find('all', array(
                        'conditions' => array(
                            'Preference.accepted_student_id' => $v['AcceptedStudent']['id'],
                            'Preference.academicyear' => $academicyear,
                            'Preference.preferences_order < ' => $v['Preference']['preferences_order'],
                        ),
                        'recursive' => -1
                    ));

                    $placement_pp_dept_done = true;

                    if (!empty($previous_preferences)) {
                        foreach ($previous_preferences as $pp_v) {
                            $placed_students_count = $this->find('count', array(
                                'conditions' => array(
                                    'AcceptedStudent.academicyear LIKE ' => $academicyear . '%',
                                    //'AcceptedStudent.college_id' => $college_id,
                                    'AcceptedStudent.department_id' => $pp_v['Preference']['department_id'],
                                ),
                                'recursive' => -1
                            ));
                            if ($placed_students_count <= 0) {
                                $placement_pp_dept_done = false;
                                break;
                            }
                        }
                    }

                    if ($placement_pp_dept_done == true) {
                        //Sort the student to keep more competitive
                        $students_to_be_sorted[] = $v;
                    } else {
                        $students_to_be_removed[] = $v['AcceptedStudent']['id'];
                    }
                }
            }
        }

        if ($high_proprity_for_high_result && !empty($students_to_be_sorted)) {
            foreach ($students_to_be_sorted as $to_sort_k => $to_sort_v) {
                if (isset($sort_out_students_result_category) && !empty($sort_out_students_result_category)) {
                    foreach ($sort_out_students_result_category as $for_sort_k => $for_sort_v) {
                        $insert_position = 0;
                        if (($result_type && $to_sort_v['AcceptedStudent']['EHEECE_total_results'] > $for_sort_v['AcceptedStudent']['EHEECE_total_results']) || (!$result_type && $to_sort_v['AcceptedStudent']['freshman_result'] > $for_sort_v['AcceptedStudent']['freshman_result']) || empty($for_sort_v['Preference']['preferences_order'])) {
                            $insert_position = $for_sort_k;
                            break;
                        }
                    }
                }

                $tmp = array();

                if (isset($sort_out_students_result_category) && !empty($sort_out_students_result_category)) {
                    foreach ($sort_out_students_result_category as $for_sort_k => $for_sort_v) {
                        if ($insert_position == $for_sort_k) {
                            $tmp[] = $to_sort_v;
                        }
                        if ($for_sort_v['AcceptedStudent']['id'] != $to_sort_v['AcceptedStudent']['id']) {
                            $tmp[] = $for_sort_v;
                        }
                    }
                }

                if (isset($sort_out_students_result_category)) {
                    unset($sort_out_students_result_category);
                }

                $sort_out_students_result_category = $tmp;
                unset($tmp);
            }
        }


        if ($first_consider_first && !empty($students_to_be_removed)) {
            //debug($students_to_be_removed);
            $tmp = array();
            if (isset($sort_out_students_result_category) && !empty($sort_out_students_result_category)) {
                foreach ($sort_out_students_result_category as $for_sort_k => $for_sort_v) {
                    if (!in_array($for_sort_v['AcceptedStudent']['id'], $students_to_be_removed)) {
                        $tmp[] = $for_sort_v;
                    }
                }
            }

            unset($sort_out_students_result_category);
            $sort_out_students_result_category = $tmp;
            unset($tmp);
        }

        debug(count($sort_out_students_result_category));
        debug($sort_out_students_result_category);
        debug($students_to_be_removed);

        return $sort_out_students_result_category;
    }

    function getReservedQuota($departments = null, $academicyear = null, $college_id = null, $resulttype = null)
    {
        $reservedQuotaNumber = array();
        $resrprivilagedQuota = ClassRegistry::init('ParticipatingDepartment')->quotaNameAndValue($academicyear, $college_id);

        $ajdustedPrivilagedQuota = array();

        if (!empty($resrprivilagedQuota)) {
            foreach ($resrprivilagedQuota as $value) {
                $ajdustedPrivilagedQuota[$value["ParticipatingDepartment"]["department_id"]] = $value["ParticipatingDepartment"];
                unset($ajdustedPrivilagedQuota[$value["ParticipatingDepartment"]["department_id"]]['department_id']);
            }
        }

        if (!empty($departments)) {
            foreach ($departments as $department_id => $weight) {
                ///Do assignment for the reserved department
                $reservedDepartmentNumber = ClassRegistry::init('PlacementsResultsCriteria')->reservedPlaceCategory($academicyear, $college_id, $department_id);
                //debug($reservedDepartmentNumber);

                if (!empty($reservedDepartmentNumber)) {
                    foreach ($reservedDepartmentNumber as $category => $categoryvalue) {
                        $reservedQuotaNumber[$department_id][$categoryvalue["PlacementsResultsCriteria"]["id"]]['reservedquota'] = $categoryvalue['ReservedPlace']['number'];

                        $availablestudentInGivenRanage = $this->availableStudentInGivenRangeAndQuota($academicyear, $college_id, $resulttype, $categoryvalue);

                        $reservedQuotaNumber[$department_id][$categoryvalue["PlacementsResultsCriteria"]["id"]]['available'] = $availablestudentInGivenRanage;
                    }
                }
            }
            //debug($reservedQuotaNumber);
            return $reservedQuotaNumber;
        } else {
            return "NO DEPARTMENT FOUND";
        }

        return $reservedQuotaNumber;
    }

    function getPrivilagedQuota($departments = null, $academicyear = null, $college_id = null, $resultcategory = null)
    {
        //privilage quota
        $privilagedQuotaa = array();
        $privilagedQuota = ClassRegistry::init('ParticipatingDepartment')->quotaNameAndValue($academicyear, $college_id);
        return $privilagedQuota;
    }

    function shrinkAndEnlarge($reservedQuotaNumber = null)
    {
        $adjustedReservedQuota = array();
        $gap = 0;

        if (!empty($reservedQuotaNumber)) {
            foreach ($reservedQuotaNumber as $key => $value) {
                foreach ($value as $k => $v) {
                    if ($v['available'] < $v['reservedquota']) {
                        $gap = $v['reservedquota'] - $v['available'];
                        $adjustedReservedQuota[$key][$k] = $v['reservedquota'] - $gap;
                    }
                }
            }
        }
    }

    // available number of students in a given category

    function availableStudentInGivenRangeAndQuota($academicyear = null, $college_id = null, $resulttype = null, $categoryvalue = null)
    {
        // Freshman result will be pushed in to the system, please come back when you
        //are done with grading.
        // pull all students who are in the given academic year and college and
        // update the freshman_result field for each student using saveAll.
        $availableStudentInGivenResultCategory = $this->find('count', array(
            'conditions' => array(
                "OR" => array(
                    'AcceptedStudent.department_id is null',
                    'AcceptedStudent.department_id = ""',
                    'AcceptedStudent.department_id = 0',
                ),
                "AcceptedStudent.academicyear LIKE " => $academicyear . '%',
                "AcceptedStudent.college_id" => $college_id,
                !empty($resulttype) ? '`AcceptedStudent.EHEECE_total_results` >=' . $categoryvalue["PlacementsResultsCriteria"]["result_from"] . ' and `AcceptedStudent.EHEECE_total_results` <=' . $categoryvalue["PlacementsResultsCriteria"]["result_to"] : '`AcceptedStudent.freshman_result` >=' . $categoryvalue["PlacementsResultsCriteria"]["result_from"] . ' and `AcceptedStudent.freshman_result` <=' . $categoryvalue["PlacementsResultsCriteria"]["result_to"]
            )
        ));
        return $availableStudentInGivenResultCategory;
    }

    //Detect quota presence

    function detect_privilaged_qutoa_presence($academicyear = null, $college_id = null)
    {
        $is_quota_present = ClassRegistry::init('ParticipatingDepartment')->find("all", array(
            "conditions" => array(
                'ParticipatingDepartment.academic_year LIKE' => $academicyear . '%',
                'ParticipatingDepartment.college_id' => $college_id
            )
        ));

        $quota_sum = 0;

        if (!empty($is_quota_present)) {
            foreach ($is_quota_present as $qp => $qv) {
                $quota_sum += ($qv['ParticipatingDepartment']['female'] + $qv['ParticipatingDepartment']['regions'] + $qv['ParticipatingDepartment']['disability']);
            }
        }

        return  $quota_sum;
    }

    //Run parallel placement

    function auto_parallel_assignment($academicyear = null, $college_id = null, $result_type = null)
    {
        ///retrive  placement result criteria with reserved quota
        $list_of_result_category = ClassRegistry::init('PlacementsResultsCriteria')->getListOfGradeCategory($academicyear, $college_id);

        if (!empty($list_of_result_category)) {
            $placedStudentsSave = array();
            $count = 0;

            foreach ($list_of_result_category as $unwanted => $placementsResultsCriteria) {
                $students_without_preference = array();
                //debug($placementsResultsCriteria);
                $reserved_quota_by_department_draft = ClassRegistry::init('ReservedPlace')->find('all', array(
                    'fields' => array(
                        'ReservedPlace.id',
                        'ReservedPlace.participating_department_id',
                        'ReservedPlace.number'
                    ), 'conditions' => array(
                        'ReservedPlace.college_id' => $college_id,
                        'ReservedPlace.academicyear LIKE' => $academicyear . '%',
                        'ReservedPlace.placements_results_criteria_id' => $placementsResultsCriteria['PlacementsResultsCriteria']['id']
                    )
                ));
                //debug($placementsResultsCriteria['PlacementsResultsCriteria']['id']);
                //debug($reserved_quota_by_department_draft);
                //reformat each department assigned quota

                $reserved_quota_by_department = array();

                if (!empty($reserved_quota_by_department_draft)) {
                    foreach ($reserved_quota_by_department_draft as $unwanted => $reservedPlace) {
                        $reserved_quota_by_department[$reservedPlace['ReservedPlace']['participating_department_id']]['quota'] = $reservedPlace['ReservedPlace']['number'];
                        $reserved_quota_by_department[$reservedPlace['ReservedPlace']['participating_department_id']]['assigned'] = 0;
                    }
                }

                //List of students who has a result for the current running grade range
                if ($result_type) {
                    $result_condition = array(
                        'AcceptedStudent.EHEECE_total_results >=' => $placementsResultsCriteria['PlacementsResultsCriteria']['result_from'],
                        'AcceptedStudent.EHEECE_total_results <=' => $placementsResultsCriteria['PlacementsResultsCriteria']['result_to']
                    );
                    $result_order = 'AcceptedStudent.EHEECE_total_results DESC';
                } else {
                    $result_condition = array(
                        'AcceptedStudent.freshman_result >=' => $placementsResultsCriteria['PlacementsResultsCriteria']['result_from'],
                        'AcceptedStudent.freshman_result <=' => $placementsResultsCriteria['PlacementsResultsCriteria']['result_to']
                    );
                    $result_order = 'AcceptedStudent.freshman_result DESC';
                }

                $students_with_x_grade_range = $this->find('all', array(
                    'fields' => array('AcceptedStudent.id'),
                    'conditions' => array(
                        'AcceptedStudent.academicyear LIKE' => $academicyear . '%',
                        'AcceptedStudent.placementtype !="DIRECT PLACED"',
                        'AcceptedStudent.college_id' => $college_id,
                        $result_condition
                    ),
                    'order' => array($result_order),
                    'recursive' => -1
                ));

                //debug($placementsResultsCriteria);
                //debug($students_with_x_grade_range);
                if (!empty($students_with_x_grade_range)) {
                    foreach ($students_with_x_grade_range as $unwanted => $student) {
                        //retrive student preference and place him/her accordingly
                        //if there is enough space in the selected department
                        //under the current range quota
                        $student_prefrence = $this->Preference->find('all', array(
                            'fields' => array('Preference.department_id', 'Preference.preferences_order'),
                            'conditions' => array('Preference.accepted_student_id' => $student['AcceptedStudent']['id']),
                            'order' => array('preferences_order ASC')
                        ));

                        if (!empty($student_prefrence)) {
                            foreach ($student_prefrence as $unwanted => $preference) {
                                if ($reserved_quota_by_department[$preference['Preference']['department_id']]['assigned'] < $reserved_quota_by_department[$preference['Preference']['department_id']]['quota']) {
                                    $placedStudentsSave['AcceptedStudent'][$count]['id'] = $student['AcceptedStudent']['id'];
                                    $placedStudentsSave['AcceptedStudent'][$count]['placementtype'] = AUTO_PLACEMENT;
                                    $placedStudentsSave['AcceptedStudent'][$count]['placement_based'] = 'C';
                                    $placedStudentsSave['AcceptedStudent'][$count]['department_id'] = $preference['Preference']['department_id'];
                                    $reserved_quota_by_department[$preference['Preference']['department_id']]['assigned']++;
                                    $count++;
                                    break;
                                }
                            } //end of for each student preference loop
                        } else {
                            $students_without_preference[] = $student['AcceptedStudent']['id'];
                        }
                    } //end of for each student in x result range loop
                }

                if (count($students_without_preference)) {
                    for ($i = 0; $i < count($students_without_preference); $i++) {
                        foreach ($reserved_quota_by_department as $department_id => &$assigned_and_quota) {
                            if ($assigned_and_quota['assigned'] < $assigned_and_quota['quota']) {
                                $placedStudentsSave['AcceptedStudent'][$count]['id'] = $students_without_preference[$i];
                                $placedStudentsSave['AcceptedStudent'][$count]['placementtype'] = AUTO_PLACEMENT;
                                $placedStudentsSave['AcceptedStudent'][$count]['placement_based'] = 'C';
                                $placedStudentsSave['AcceptedStudent'][$count]['department_id'] = $department_id;
                                $assigned_and_quota['assigned']++;
                                $count++;
                                break;
                            }
                        }
                    }
                }
            } //end of for each result category loop

            if (!empty($placedStudentsSave)) {
                //assign students to departments
                $this->saveAll($placedStudentsSave['AcceptedStudent']);
            }

            $result_order_by = null;

            if ($result_type) {
                $result_order_by = 'AcceptedStudent.EHEECE_total_results desc';
            } else {
                $result_order_by = 'AcceptedStudent.freshman_result desc';
            }

            $placedstudent = $this->find('all', array(
                'conditions' => array(
                    'AcceptedStudent.college_id' => $college_id,
                    'AcceptedStudent.academicyear LIKE ' => $academicyear . '%',
                    'AcceptedStudent.placementtype' => AUTO_PLACEMENT
                ),
                'order' => array('AcceptedStudent.department_id asc', $result_order_by)
            ));

            //debug($placedstudent);
            $departments = ClassRegistry::init('ParticipatingDepartment')->find("all", array(
                'fields' => 'ParticipatingDepartment.department_id',
                "conditions" => array(
                    'ParticipatingDepartment.academic_year LIKE' => $academicyear . '%',
                    'ParticipatingDepartment.college_id' => $college_id
                )
            ));

            $dep_id = array();

            if (!empty($departments)) {
                foreach ($departments as $k => $v) {
                    $dep_id[] = $v['ParticipatingDepartment']['department_id'];
                }
            }
            //debug($dep_id);
            //$dep_id=array_keys($departments);

            $dep_name = $this->Department->find('list', array('conditions' => array('Department.id' => $dep_id)));
            $newly_placed_student = array();

            if (!empty($dep_name)) {
                foreach ($dep_name as $dk => $dv) {
                    foreach ($placedstudent as $k => $v) {
                        if ($dk == $v['Department']['id']) {
                            $newly_placed_student[$dv][$k] = $v;
                        }
                    }

                    $newly_placed_student['auto_summery'][$dv]['C'] = $this->find('count', array(
                        'conditions' => array(
                            'AcceptedStudent.academicyear LIKE' => $academicyear . '%',
                            'AcceptedStudent.department_id' => $dk,
                            'AcceptedStudent.college_id' => $college_id,
                            'AcceptedStudent.placement_based' => 'C'
                        )
                    ));

                    $newly_placed_student['auto_summery'][$dv]['Q'] = $this->find('count', array(
                        'conditions' => array(
                            'AcceptedStudent.academicyear LIKE' => $academicyear . '%',
                            'AcceptedStudent.department_id' => $dk,
                            'AcceptedStudent.college_id' => $college_id,
                            'AcceptedStudent.placement_based' => 'Q'
                        )
                    ));
                }
            }

            return   $newly_placed_student;
        } else {
            die("No result criteria recorded. Technical Detail:Model:AcceptedStudent,Function: auto_parallel_assignment");
        }
    }

    function check_program_type($data = null, $role_id = null)
    {
        if ($role_id == ROLE_COLLEGE) {
            return true;
        }

        if ($data['AcceptedStudent']['program_id'] != PROGRAM_UNDEGRADUATE) {
            if (empty($data['AcceptedStudent']['department_id']) || $data['AcceptedStudent']['department_id'] == 0) {
                $this->invalidate('program', 'For post graduate  student, you need to select department.');
                return false;
            }
        }

        if (($data['AcceptedStudent']['program_type_id'] != PROGRAM_TYPE_REGULAR) && empty($data['AcceptedStudent']['department_id'])) {
            $this->invalidate('program', 'For non regular  student, you need to select department.');
            return false;
        }

        return true;
    }


    function copyFreshmanResult($admissionyear = null, $college_id = null)
    {
        $student_results = $this->find('all', array(
            'conditions' => array(
                'AcceptedStudent.academicyear' => $admissionyear,
                'AcceptedStudent.department_id IS NULL',
                'AcceptedStudent.program_id' => PROGRAM_UNDEGRADUATE,
                'OR' => array(
                    'AcceptedStudent.program_type_id' => PROGRAM_TYPE_REGULAR,
                    'AcceptedStudent.program_type_id' => PROGRAM_TYPE_ADVANCE_STANDING,
                ),
                'AcceptedStudent.college_id' => $college_id
            ),
            'fields' => array(
                'AcceptedStudent.id'
            ),
            'contain' => array(
                'Student' => array(
                    'StudentExamStatus' => array(
                        'conditions' => array(
                            'StudentExamStatus.academic_status_id <> ' => DISMISSED_ACADEMIC_STATUS_ID
                        ),
                        'fields' => array(
                            'StudentExamStatus.sgpa'
                        ),
                        'order' => 'StudentExamStatus.created ASC'
                    )
                )
            )
        ));

        $acceptedStudentsResult = array();
        $acceptedStudentsEmptyFreshResult = array();

        if (!empty($student_results)) {
            foreach ($student_results as $student_result) {
                if (isset($student_result['Student']['StudentExamStatus']) && !empty($student_result['Student']['StudentExamStatus'])) {
                    $index = count($acceptedStudentsResult);
                    $acceptedStudentsResult[$index]['id'] = $student_result['AcceptedStudent']['id'];
                    $acceptedStudentsResult[$index]['freshman_result'] = $student_result['Student']['StudentExamStatus'][0]['sgpa'];
                }
                $index = count($acceptedStudentsEmptyFreshResult);
                $acceptedStudentsEmptyFreshResult[$index]['id'] = $student_result['AcceptedStudent']['id'];
                $acceptedStudentsEmptyFreshResult[$index]['freshman_result'] = null;
            }
        }

        if (!empty($acceptedStudentsEmptyFreshResult)) {
            $this->saveAll($acceptedStudentsEmptyFreshResult);
        }

        if (!empty($acceptedStudentsResult)) {
            $this->saveAll($acceptedStudentsResult);
        }
        //debug($acceptedStudentsResult);
        //debug($student_results);
    }
}

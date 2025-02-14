<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CostSharesTable extends Table
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

        $this->setTable('cost_shares');
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
            ->scalar('academic_year')
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

        $validator
            ->scalar('sharing_cycle')
            ->notEmptyString('sharing_cycle');

        $validator
            ->numeric('education_fee')
            ->allowEmptyString('education_fee');

        $validator
            ->numeric('accomodation_fee')
            ->allowEmptyString('accomodation_fee');

        $validator
            ->numeric('cafeteria_fee')
            ->allowEmptyString('cafeteria_fee');

        $validator
            ->numeric('medical_fee')
            ->allowEmptyString('medical_fee');

        $validator
            ->dateTime('cost_sharing_sign_date')
            ->requirePresence('cost_sharing_sign_date', 'create')
            ->notEmptyDateTime('cost_sharing_sign_date');

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

        return $rules;
    }

    public function getCostSharingGraduated($data){
        debug($data);
        App::import('Component','AcademicYear');
        $AcademicYear= new AcademicYearComponent(new ComponentRegistry);
        $graduateDate= $AcademicYear->get_academicYearBegainingDate($data['Report']['graduated_academic_year']);
        $options=array();
        $options['contain']=array(
            'Student'=>array('order'=>array('Student.first_name ASC'),'Department','GraduateList'),

        );
        if (isset($data['Report']['department_id']) && !empty($data['Report']['department_id']))
        {
            $college_ids = explode('~', $data['Report']['department_id']);
            if(count($college_ids) > 1) {
                $options['conditions']['Student.college_id']=$college_ids[1];
            } else {
                $options['conditions']['Student.department_id']=$data['Report']['department_id'];
            }
        }

        if (isset($data['Report']['program_id']) && !empty($data['Report']['program_id'])) {
            $options['conditions']['Student.program_id']=$data['Report']['program_id'];
        }
        if (isset($data['Report']['program_type_id']) && !empty($data['Report']['program_type_id'])) {
            $options['conditions']['Student.program_type_id']=$data['Report']['program_type_id'];
        }


        if(isset($data['Report']['graduated_academic_year']) && !empty($data['Report']['graduated_academic_year'])){
            // it should be in between
            $nextGraduateAcademicYear=$this->Student->StudentExamStatus->getNextSemster($data['Report']['graduated_academic_year'])['academic_year'];
            $options['conditions'][] = "Student.id IN (SELECT student_id FROM graduate_lists where graduate_date >='$graduateDate' and graduate_date <='$nextGraduateAcademicYear' )";
        }
        if (isset($data['Report']['name']) && !empty($data['Report']['name'])) {
            $options['conditions']['Student.first_name LIKE ']=$data['Report']['name'].'%';
        }

        if (isset($data['Report']['studentnumber']) && !empty($data['Report']['studentnumber'])) {
            unset($options['conditions']);
            $options['conditions']['Student.studentnumber']=$data['Report']['studentnumber'];
        }

        $studentCosts=$this->find('all',$options);

        $formattedStudentList=array();
        if(!empty($studentCosts)){
            App::import('Component','EthiopicDateTime');
            $EthiopicDateTimeAC= new EthiopicDateTimeComponent();
            foreach ($studentCosts as $key => $value) {

                $formattedStudentList['StudentList'][$value['Student']['Department']['name'].'~'.$value['Student']['full_name'].'~'.$value['Student']['studentnumber'].'~'.$value['Student']['gender'].'~'.$value['Student']['GraduateList']['graduate_date']][$EthiopicDateTimeAC->GetEthiopicYear(1,9,$value['CostShare']['academic_year'])]=$value['CostShare'];
                $formattedStudentList['CostSharingYearList'][$EthiopicDateTimeAC->GetEthiopicYear(1,9,$value['CostShare']['academic_year'])]=$EthiopicDateTimeAC->GetEthiopicYear(1,9,$value['CostShare']['academic_year']);
            }

        }
        asort($formattedStudentList['CostSharingYearList']);

        return $formattedStudentList;
    }

    public function getCostSharingNotGraduated($data){
        debug($data);
        App::import('Component','AcademicYear');
        $AcademicYear= new AcademicYearComponent(new ComponentRegistry);
        $graduateDate= $AcademicYear->get_academicYearBegainingDate($data['Report']['graduated_academic_year']);
        $options=array();
        $options['contain']=array('CostShare','Department');
        $options['order']=array('Student.first_name ASC');
        if (isset($data['Report']['department_id']) && !empty($data['Report']['department_id']))
        {
            $college_ids = explode('~', $data['Report']['department_id']);
            if(count($college_ids) > 1) {
                $options['conditions']['Student.college_id']=$college_ids[1];
            } else {
                $options['conditions']['Student.department_id']=$data['Report']['department_id'];
            }
        }

        if (isset($data['Report']['program_id']) && !empty($data['Report']['program_id'])) {
            $options['conditions']['Student.program_id']=$data['Report']['program_id'];
        }
        if (isset($data['Report']['program_type_id']) && !empty($data['Report']['program_type_id'])) {
            $options['conditions']['Student.program_type_id']=$data['Report']['program_type_id'];
        }

        if (isset($data['Report']['name']) && !empty($data['Report']['name'])) {
            $options['conditions']['Student.first_name LIKE ']=$data['Report']['name'].'%';
        }


        if(isset($data['Report']['graduated_academic_year']) && !empty($data['Report']['graduated_academic_year'])){
            // it should be in between
            $nextGraduateAcademicYear=$this->Student->StudentExamStatus->getNextSemster($data['Report']['graduated_academic_year'])['academic_year'];


            $options['conditions'][] = "Student.admissionyear >='$graduateDate' and Student.admissionyear <='$nextGraduateAcademicYear'";

        }

        if (isset($data['Report']['studentnumber']) && !empty($data['Report']['studentnumber'])) {
            unset($options['conditions']);
            $options['conditions']['Student.studentnumber']=$data['Report']['studentnumber'];
        }
        $studentCosts=$this->Student->find('all',$options);

        $formattedStudentList=array();
        if(!empty($studentCosts)){
            App::import('Component','EthiopicDateTime');
            $EthiopicDateTimeAC= new EthiopicDateTimeComponent();
            foreach ($studentCosts as $key => $value) {
                foreach($value['CostShare'] as $cs=>$cv){
                    $formattedStudentList['StudentList'][$value['Department']['name'].'~'.$value['Student']['full_name'].'~'.$value['Student']['studentnumber'].'~'.$value['Student']['gender']][$EthiopicDateTimeAC->GetEthiopicYear(1,9,$cv['academic_year'])]=$cv;
                    $formattedStudentList['CostSharingYearList'][$EthiopicDateTimeAC->GetEthiopicYear(1,9,$cv['academic_year'])]=$EthiopicDateTimeAC->GetEthiopicYear(1,9,$cv['academic_year']);
                }
            }

        }
        asort($formattedStudentList['CostSharingYearList']);

        return $formattedStudentList;
    }
}

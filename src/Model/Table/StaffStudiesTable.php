<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StaffStudiesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('staff_studies');
        $this->setPrimaryKey('id');

        // Define Relationships
        $this->belongsTo('Staffs', [
            'foreignKey' => 'staff_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Countries', [
            'foreignKey' => 'country_id',
            'joinType' => 'LEFT',
        ]);

        $this->hasMany('Attachments', [
            'className' => 'Media.Attachments',
            'foreignKey' => 'foreign_key',
            'conditions' => ['Attachments.model' => 'StaffStudy'],
            'dependent' => true,
            'order' => ['Attachments.created' => 'DESC'],
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('education')
            ->requirePresence('education', 'create')
            ->notEmptyString('education', 'Education field is required.');

        $validator
            ->date('leave_date')
            ->requirePresence('leave_date', 'create')
            ->notEmptyDate('leave_date', 'Leave date is required.');

        $validator
            ->date('return_date')
            ->requirePresence('return_date', 'create')
            ->notEmptyDate('return_date', 'Return date is required.');

        $validator
            ->scalar('specialization')
            ->requirePresence('specialization', 'create')
            ->notEmptyString('specialization', 'Specialization is required.');

        $validator
            ->scalar('university_joined')
            ->requirePresence('university_joined', 'create')
            ->notEmptyString('university_joined', 'University joined is required.');

        return $validator;
    }

    function preparedAttachment($data=null,$group=null){
        foreach ($data['Attachment'] as $in=>  &$dv) {

            if (empty($dv['file']['name']) && empty($dv['file']['type'])
                && empty($dv['tmp_name'])) {
                unset($data['Attachment'][$in]);
            } else {
                $dv['model']='StaffStudy';
                $dv['group']=$group;
            }
        }
        return $data;
    }

    function getStaffCompletedHDPStatistics($acadamic_year=null,$department_id=null,$sex='all'){

        $graph['data']=array();
        $graph['labels']=array();
        // list out the department
        if (isset($department_id) && !empty($department_id))
        {
            debug($department_id);
            $college_id = explode('~', $department_id);
            if(count($college_id) > 1) {
                $departments=$this->Staff->Department->find('all',
                    array('conditions'=>array('Department.college_id'=>$college_id[1]
                    ),'contain'=>array('College','YearLevel')));
            } else {
                $departments=$this->Staff->Department->find('all',array('conditions'=>array('Department.id'=>$department_id
                ),'contain'=>array('College','YearLevel')));
            }
        } else {
            $departments=$this->Staff->Department->find('all',array('contain'=>array('College','YearLevel')
            ));
        }
        //debug($departments);
        if($sex=="all"){
            $sexList=array('male'=>'male','female'=>'female');
        } else {
            $sexList[$sex]=$sex;
        }
        App::import('Component','AcademicYear');
        $AcademicYear= new AcademicYearComponent(new ComponentRegistry);
        $acadamicYearBegDate=$AcademicYear->get_academicYearBegainingDate($acadamic_year);

        $hdpTrainningStatistics=array();
        $graph['series']=array('male','female');
        $completed=array('0'=>'Not Completed','1'=>'Completed');
        foreach ($departments as $key => $value) {
            foreach($sexList as $skey => $svalue) {
                foreach($completed as $ckey => $cvalue) {
                    $check=$this->find('all',array('conditions'=>array('StaffStudy.education'=>'HDP','StaffStudy.study_completed'=>$ckey,
                        'StaffStudy.leave_date >= '=>
                            $acadamicYearBegDate,

                        'StaffStudy.staff_id in (select id from staffs where gender="'.$skey.'" and department_id='.$value['Department']['id'].')'
                    ),
                        'contain'=>array('Staff')
                    ));
                    debug($check);
                    if(!empty($check)){
                        $hdpTrainningStatistics[$value['College']['name']][$value['Department']['name']][$ckey][$skey]=$this->find('count',array('conditions'=>array('StaffStudy.education'=>'HDP','StaffStudy.study_completed'=>$ckey)));
                    }
                }
            }
        }
        return $hdpTrainningStatistics;
    }

}

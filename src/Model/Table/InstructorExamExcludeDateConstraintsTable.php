<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class InstructorExamExcludeDateConstraintsTable extends Table
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

        $this->setTable('instructor_exam_exclude_date_constraints');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Staffs', [
            'foreignKey' => 'staff_id',
        ]);
        $this->belongsTo('StaffForExams', [
            'foreignKey' => 'staff_for_exam_id',
        ]);
    }


    function get_already_recorded_instructor_exam_excluded_date_constraint($instructor_id=null){
        if(!empty($instructor_id)){
            $instructorExamExcludeDateConstraints = $this->find('all',array('conditions'=>array("OR"=>array('InstructorExamExcludeDateConstraint.staff_id'=>$instructor_id, 'InstructorExamExcludeDateConstraint.staff_for_exam_id'=>$instructor_id)),'order'=>array('InstructorExamExcludeDateConstraint.exam_date','InstructorExamExcludeDateConstraint.session'),'recursive'=>-1));
            $instructorExamExcludeDateConstraint_by_date = array();
            foreach($instructorExamExcludeDateConstraints as $instructorExamExcludeDateConstraint){
                $instructorExamExcludeDateConstraint_by_date[$instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['exam_date']][$instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['session']]['id'] = $instructorExamExcludeDateConstraint['InstructorExamExcludeDateConstraint']['id'];

            }
            return $instructorExamExcludeDateConstraint_by_date;
        }
    }

}

<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class InstructorClassPeriodCourseConstraintsTable extends Table
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

        $this->setTable('instructor_class_period_course_constraints');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Staffs', [
            'foreignKey' => 'staff_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ClassPeriods', [
            'foreignKey' => 'class_period_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
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
            ->notEmptyString('staff_id', 'Staff ID is required.')
            ->notEmptyString('class_period_id', 'Class Period is required.')
            ->notEmptyString('college_id', 'College ID is required.');

        return $validator;
    }

    /**
     * Check if a record exists before deletion.
     */
    public function beforeDeleteCheckEligibility($id = null, $college_id = null)
    {
        $count = $this->find()
            ->where([
                'InstructorClassPeriodCourseConstraints.college_id' => $college_id,
                'InstructorClassPeriodCourseConstraints.id' => $id
            ])
            ->count();

        return $count > 0;
    }
}

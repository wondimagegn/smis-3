<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ClassPeriods Table
 */
class ClassPeriodsTable extends Table
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

        $this->setTable('class_periods');
        $this->setDisplayField('week_day');
        $this->setPrimaryKey('id');

        $this->belongsTo('PeriodSettings', [
            'foreignKey' => 'period_setting_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'LEFT',
        ]);

        $this->hasMany('ClassPeriodCourseConstraints', [
            'foreignKey' => 'class_period_id',
            'dependent' => false,
        ]);

        $this->hasMany('ClassRoomClassPeriodConstraints', [
            'foreignKey' => 'class_period_id',
            'dependent' => false,
        ]);

        $this->hasMany('InstructorClassPeriodCourseConstraints', [
            'foreignKey' => 'class_period_id',
            'dependent' => false,
        ]);

        $this->belongsToMany('CourseSchedules', [
            'foreignKey' => 'class_period_id',
            'targetForeignKey' => 'course_schedule_id',
            'joinTable' => 'course_schedules_class_periods'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }
}

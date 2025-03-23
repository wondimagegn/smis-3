<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ClassPeriods Model
 *
 * @property \App\Model\Table\PeriodSettingsTable&\Cake\ORM\Association\BelongsTo $PeriodSettings
 * @property \App\Model\Table\CollegesTable&\Cake\ORM\Association\BelongsTo $Colleges
 * @property \App\Model\Table\ProgramTypesTable&\Cake\ORM\Association\BelongsTo $ProgramTypes
 * @property \App\Model\Table\ProgramsTable&\Cake\ORM\Association\BelongsTo $Programs
 * @property \App\Model\Table\ClassPeriodCourseConstraintsTable&\Cake\ORM\Association\HasMany $ClassPeriodCourseConstraints
 * @property \App\Model\Table\ClassRoomClassPeriodConstraintsTable&\Cake\ORM\Association\HasMany $ClassRoomClassPeriodConstraints
 * @property \App\Model\Table\InstructorClassPeriodCourseConstraintsTable&\Cake\ORM\Association\HasMany $InstructorClassPeriodCourseConstraints
 * @property \App\Model\Table\CourseSchedulesTable&\Cake\ORM\Association\BelongsToMany $CourseSchedules
 *
 * @method \App\Model\Entity\ClassPeriod get($primaryKey, $options = [])
 * @method \App\Model\Entity\ClassPeriod newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ClassPeriod[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClassPeriod|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClassPeriod saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClassPeriod patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ClassPeriod[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClassPeriod findOrCreate($search, callable $callback = null, $options = [])
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
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('PeriodSettings', [
            'foreignKey' => 'period_setting_id',
            'joinType' => 'INNER',
            'propertyName' => 'PeriodSetting',
        ]);
        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
            'propertyName' => 'College',
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER',
            'propertyName' => 'ProgramType',
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
            'propertyName' => 'Program',
        ]);
        $this->hasMany('ClassPeriodCourseConstraints', [
            'foreignKey' => 'class_period_id',
            'propertyName' => 'ClassPeriodCourseConstraint',
        ]);
        $this->hasMany('ClassRoomClassPeriodConstraints', [
            'foreignKey' => 'class_period_id',
            'propertyName' => 'ClassRoomClassPeriodConstraint',
        ]);
        $this->hasMany('InstructorClassPeriodCourseConstraints', [
            'foreignKey' => 'class_period_id',
            'propertyName' => 'InstructorClassPeriodCourseConstraint',
        ]);
        $this->belongsToMany('CourseSchedules', [
            'foreignKey' => 'class_period_id',
            'targetForeignKey' => 'course_schedule_id',
            'joinTable' => 'course_schedules_class_periods',
            'propertyName' => 'CourseSchedule',
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
            ->requirePresence('week_day', 'create')
            ->notEmptyString('week_day');

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
        $rules->add($rules->existsIn(['period_setting_id'], 'PeriodSettings'));
        $rules->add($rules->existsIn(['college_id'], 'Colleges'));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));
        $rules->add($rules->existsIn(['program_id'], 'Programs'));

        return $rules;
    }
}

<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ExtendingAcademicCalendarsTable extends Table
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

        $this->setTable('extending_academic_calendars');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('AcademicCalendars', [
            'foreignKey' => 'academic_calendar_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('YearLevels', [
            'foreignKey' => 'year_level_id',
            'joinType' => 'INNER',
        ]);
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
            ->scalar('activity_type')
            ->maxLength('activity_type', 200)
            ->requirePresence('activity_type', 'create')
            ->notEmptyString('activity_type');

        $validator
            ->integer('days')
            ->requirePresence('days', 'create')
            ->notEmptyString('days');

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
        $rules->add($rules->existsIn(['academic_calendar_id'], 'AcademicCalendars'));
        $rules->add($rules->existsIn(['year_level_id'], 'YearLevels'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['program_id'], 'Programs'));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));

        return $rules;
    }

    public function getExtendedDays($academicCalendarId, $yearLevel, $departmentId, $programId, $programTypeId, $activity = ""): int
    {
        $days = $this->find()
            ->where([
                'academic_calendar_id' => $academicCalendarId,
                'year_level_id' => $yearLevel,
                'department_id' => $departmentId,
                'program_id' => $programId,
                'program_type_id' => $programTypeId,
                'activity_type' => $activity
            ])
            ->order(['created' => 'DESC'])
            ->first();

        return $days->days ?? 0;
    }

}

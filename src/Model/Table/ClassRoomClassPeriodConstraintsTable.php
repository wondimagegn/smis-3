<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ClassRoomClassPeriodConstraints Table
 */
class ClassRoomClassPeriodConstraintsTable extends Table
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

        $this->setTable('class_room_class_period_constraints');
        $this->setDisplayField('class_room_id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ClassRooms', [
            'foreignKey' => 'class_room_id',
            'joinType' => 'LEFT'
        ]);

        $this->belongsTo('ClassPeriods', [
            'foreignKey' => 'class_period_id',
            'joinType' => 'LEFT'
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

    /**
     * Checks if a class room class period constraint can be deleted
     *
     * @param int|null $id Constraint ID
     * @param int|null $collegeId College ID
     * @return bool True if eligible for deletion, false otherwise
     */
    public function checkDeleteEligibility($id = null, $collegeId = null): bool
    {
        if (!$id || !$collegeId) {
            return false;
        }

        $classPeriodIds = $this->ClassPeriods->find('list')
            ->select(['ClassPeriods.id'])
            ->where(['ClassPeriods.college_id' => $collegeId])
            ->toArray();

        if (empty($classPeriodIds)) {
            return false;
        }

        $count = $this->find()
            ->where([
                'ClassRoomClassPeriodConstraints.class_period_id IN' => array_values($classPeriodIds),
                'ClassRoomClassPeriodConstraints.id' => $id
            ])
            ->count();

        return $count > 0;
    }

    /**
     * Counts the number of constraints using a class room
     *
     * @param int|null $id Class room ID
     * @return int Number of constraints
     */
    public function isClassRoomUsed($id = null): int
    {
        if (!$id) {
            return 0;
        }

        return $this->find()
            ->where(['ClassRoomClassPeriodConstraints.class_room_id' => $id])
            ->limit(2)
            ->count();
    }
}

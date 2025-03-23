<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class InvigilatorsTable extends Table
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

        $this->setTable('invigilators');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Staffs', [
            'foreignKey' => 'staff_id',
        ]);
        $this->belongsTo('StaffForExams', [
            'foreignKey' => 'staff_for_exam_id',
        ]);
        $this->belongsTo('ExamSchedules', [
            'foreignKey' => 'exam_schedule_id',
            'joinType' => 'INNER',
        ]);
    }
}

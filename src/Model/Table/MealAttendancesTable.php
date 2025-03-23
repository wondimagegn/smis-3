<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class MealAttendancesTable extends Table
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

        $this->setTable('meal_attendances');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('MealTypes', [
            'foreignKey' => 'meal_type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
    }

    public function auto_detect_current_meal_type($current_time = null)
    {

        $detected_meal_type = null;
        if (strtotime($current_time) >= strtotime("06:00:00") && strtotime($current_time) <= strtotime("10:00:00")) {
            $detected_meal_type = 1;
        } elseif (strtotime($current_time) > strtotime("10:00:00") && strtotime($current_time) <= strtotime(
                "15:00:00"
            )) {
            $detected_meal_type = 2;
        } elseif (strtotime($current_time) > strtotime("15:00:00") && strtotime($current_time) <= strtotime(
                "21:00:00"
            )) {
            $detected_meal_type = 3;
        }
        return $detected_meal_type;
    }

    //return meal hall attendance per meal type as an array
    public function get_mealhall_attendance($meal_hall_id = null, $academic_year = null, $selected_date = null)
    {

        $student_ids = $this->Student->MealHallAssignment->find(
            'list',
            array(
                'fields' => array('MealHallAssignment.student_id', 'MealHallAssignment.student_id'),
                'conditions' => array(
                    'MealHallAssignment.meal_hall_id' => $meal_hall_id,
                    'MealHallAssignment.academic_year' => $academic_year,
                    'NOT' => array('MealHallAssignment.student_id' => null)
                )
            )
        );
        $accepted_student_ids = $this->Student->MealHallAssignment->find(
            'list',
            array(
                'fields' => array('MealHallAssignment.accepted_student_id', 'MealHallAssignment.accepted_student_id'),
                'conditions' => array(
                    'MealHallAssignment.meal_hall_id' => $meal_hall_id,
                    'MealHallAssignment.academic_year' => $academic_year,
                    'NOT' => array('MealHallAssignment.accepted_student_id' => null)
                )
            )
        );

        $student_ids_from_accepted_student_ids = $this->Student->find(
            'list',
            array(
                'fields' => array('Student.id', 'Student.id'),
                'conditions' => array('Student.accepted_student_id' => $accepted_student_ids)
            )
        );

        $student_ids_array = array_merge($student_ids, $student_ids_from_accepted_student_ids);
        $mealTypes = $this->MealType->find('list');

        $meal_hall_attendances = array();

        foreach ($mealTypes as $mtk => $mtv) {
            $meal_hall_attendances[$mtv] = $this->find(
                'count',
                array(
                    'conditions' => array(
                        'MealAttendance.meal_type_id' => $mtk,
                        'MealAttendance.student_id' => $student_ids_array,
                        'MealAttendance.created LIKE' => $selected_date . "%"
                    )
                )
            );
        }

        return $meal_hall_attendances;
    }

    public function get_meal_hall_campus($meal_hall_id = null)
    {

        if (!empty($meal_hall_id)) {
            $campus_id = $this->Student->MealHallAssignment->MealHall->field(
                'MealHall.campus_id',
                array('MealHall.id' => $meal_hall_id)
            );
            $campus_name = $this->Student->MealHallAssignment->MealHall->Campus->field(
                'Campus.name',
                array('Campus.id' => $campus_id)
            );
            return $campus_name;
        }
    }
}

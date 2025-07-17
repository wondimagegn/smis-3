<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\Time;

/**
 * Attendances Table
 */
class AttendancesTable extends Table
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

        $this->setTable('attendances');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'LEFT',
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
            ->allowEmptyString('id', null, 'create')
            ->numeric('student_id')
            ->requirePresence('student_id', 'create')
            ->notEmptyString('student_id', 'Please provide a valid student ID.')
            ->numeric('published_course_id')
            ->requirePresence('published_course_id', 'create')
            ->notEmptyString('published_course_id', 'Please provide a valid published course ID.')
            ->numeric('attendance_type')
            ->requirePresence('attendance_type', 'create')
            ->notEmptyString('attendance_type', 'Please provide a valid attendance type.')
            ->date('attendance_date')
            ->requirePresence('attendance_date', 'create')
            ->notEmptyDate('attendance_date', 'Please provide a valid attendance date.')
            ->boolean('attendance')
            ->requirePresence('attendance', 'create')
            ->notEmptyString('attendance', 'Please provide a valid attendance status.');

        return $validator;
    }

    /**
     * Retrieves a list of unique attendance dates for a published course
     *
     * @param int|null $publishedCourseId Published course ID
     * @return array Formatted attendance dates
     */
    public function getListOfDateAttendanceTaken($publishedCourseId = null)
    {
        if (!$publishedCourseId) {
            return [];
        }

        $attendanceDates = $this->find()
            ->select(['attendance_date'])
            ->where(['Attendances.published_course_id' => $publishedCourseId])
            ->distinct(['attendance_date'])
            ->order(['Attendances.attendance_date' => 'ASC'])
            ->toArray();

        $formattedDates = [];
        foreach ($attendanceDates as $date) {
            $time = new Time($date->attendance_date);
            $formattedDates[$time->format('Y-m-d')] = $time->format('D M d, Y');
        }

        return $formattedDates;
    }

    /**
     * Retrieves attendance details for students in a course within a date range
     *
     * @param int|null $publishedCourseId Published course ID
     * @param string|null $attendanceStartDate Start date (Y-m-d)
     * @param string|null $attendanceEndDate End date (Y-m-d)
     * @param array $studentRegisters Registered students
     * @param array $studentAdds Added students
     * @return array Attendance details for registered and added students
     */
    public function getCourseAttendanceDetail($publishedCourseId = null, $attendanceStartDate = null, $attendanceEndDate = null, array $studentRegisters = [], array $studentAdds = [])
    {
        if (!$publishedCourseId || !$attendanceStartDate || !$attendanceEndDate) {
            return ['register' => $studentRegisters, 'add' => $studentAdds];
        }

        foreach ($studentRegisters as $key => $studentRegister) {
            if (empty($studentRegister['Student']['id'])) {
                $studentRegisters[$key]['Attendance'] = [];
                continue;
            }

            $attendanceDetail = $this->find()
                ->where([
                    'Attendances.published_course_id' => $publishedCourseId,
                    'Attendances.attendance_date >=' => $attendanceStartDate,
                    'Attendances.attendance_date <=' => $attendanceEndDate,
                    'Attendances.student_id' => $studentRegister['Student']['id']
                ])
                ->order(['Attendances.attendance_date' => 'ASC'])
                ->toArray();

            $studentRegisters[$key]['Attendance'] = $attendanceDetail ?: [];
        }

        foreach ($studentAdds as $key => $studentAdd) {
            if (empty($studentAdd['Student']['id'])) {
                $studentAdds[$key]['Attendance'] = [];
                continue;
            }

            $attendanceDetail = $this->find()
                ->where([
                    'Attendances.published_course_id' => $publishedCourseId,
                    'Attendances.attendance_date >=' => $attendanceStartDate,
                    'Attendances.attendance_date <=' => $attendanceEndDate,
                    'Attendances.student_id' => $studentAdd['Student']['id']
                ])
                ->order(['Attendances.attendance_date' => 'ASC'])
                ->toArray();

            $studentAdds[$key]['Attendance'] = $attendanceDetail ?: [];
        }

        return [
            'register' => $studentRegisters,
            'add' => $studentAdds
        ];
    }

    /**
     * Checks if a course schedule is used in attendance records
     *
     * @param int|array|null $courseSchedulePublishedCourseIds Published course ID(s)
     * @return bool True if used, false otherwise
     */
    public function isCourseScheduleUsedInAttendance($courseSchedulePublishedCourseIds = null)
    {
        if (empty($courseSchedulePublishedCourseIds)) {
            return false;
        }

        $count = $this->find()
            ->where(['Attendances.published_course_id IN' => (array)$courseSchedulePublishedCourseIds])
            ->limit(2)
            ->count();

        return $count > 0;
    }
}

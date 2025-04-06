<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class CourseSchedulesTable extends Table
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

        $this->setTable('course_schedules');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ClassRooms', [
            'foreignKey' => 'class_room_id',
        ]);
        $this->belongsTo('Sections', [
            'foreignKey' => 'section_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CourseSplitSections', [
            'foreignKey' => 'course_split_section_id',
        ]);
        $this->belongsToMany('ClassPeriods', [
            'foreignKey' => 'course_schedule_id',
            'targetForeignKey' => 'class_period_id',
            'joinTable' => 'course_schedules_class_periods',
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
            ->scalar('academic_year')
            ->maxLength('academic_year', 9)
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

        $validator
            ->scalar('semester')
            ->maxLength('semester', 3)
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester');

        $validator
            ->scalar('type')
            ->maxLength('type', 20)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->boolean('is_auto')
            ->notEmptyString('is_auto');

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

        $rules->add($rules->existsIn(['class_room_id'], 'ClassRooms'));
        $rules->add($rules->existsIn(['section_id'], 'Sections'));
        $rules->add($rules->existsIn(['published_course_id'], 'PublishedCourses'));
        $rules->add($rules->existsIn(['course_split_section_id'], 'CourseSplitSections'));

        return $rules;
    }


    public function get_sorted_published_courses($conditions = null)
    {

        $publishedCourses = $this->PublishedCourse->find(
            'all',
            array(
                'conditions' => $conditions,
                'fields' => array('PublishedCourse.id'),
                'contain' => array('ClassPeriodCourseConstraint', 'ClassRoomCourseConstraint')
            )
        );
        //debug($publishedCourses);
        //calculate weight of course period and course room constraints
        $sorted_publishedCourses = null;
        foreach ($publishedCourses as $publishedCourse) {
            $weight = 0;
            foreach ($publishedCourse['ClassPeriodCourseConstraint'] as $cpcck => $cpccv) {
                if ($cpccv['active'] == 1) {
                    if ($cpcck == 0) {
                        $weight = $weight + 100;
                    } else {
                        $weight = $weight - 5;
                    }
                } else {
                    $weight + 5;
                }
            }

            foreach ($publishedCourse['ClassRoomCourseConstraint'] as $crcck => $crccv) {
                if ($crccv['active'] == 1) {
                    if ($crcck == 0) {
                        $weight = $weight + 100;
                    } else {
                        $weight = $weight - 5;
                    }
                } else {
                    $weight + 5;
                }
            }
            $sorted_publishedCourses[$publishedCourse['PublishedCourse']['id']] = $weight;
        }
        //sort published course by its weight descending order
        if (!empty($sorted_publishedCourses)) {
            arsort($sorted_publishedCourses);
        }
        return $sorted_publishedCourses;
    }

    public function getPublishedCourseDetails($publishedCourse_id = null): array
    {
        if (empty($publishedCourse_id)) {
            return [];
        }

        $query = $this->PublishedCourses
            ->find()
            ->enableHydration(false)
            ->select([
                'PublishedCourses.id',
                'PublishedCourses.lecture_number_of_session',
                'PublishedCourses.lab_number_of_session',
                'PublishedCourses.tutorial_number_of_session'
            ])
            ->where(['PublishedCourses.id' => $publishedCourse_id])
            ->contain([
                'Sections' => function ($q) {
                    return $q->select(['Sections.id', 'Sections.name']);
                },
                'SectionSplitForPublishedCourses.CourseSplitSections',
                'Courses' => function ($q) {
                    return $q->select([
                        'Courses.id',
                        'Courses.course_title',
                        'Courses.course_code',
                        'Courses.credit',
                        'Courses.lecture_hours',
                        'Courses.tutorial_hours',
                        'Courses.laboratory_hours'
                    ]);
                },
                'CourseInstructorAssignments' => function ($q) {
                    return $q->select([
                        'CourseInstructorAssignments.id',
                        'CourseInstructorAssignments.staff_id',
                        'CourseInstructorAssignments.type',
                        'CourseInstructorAssignments.isprimary'
                    ])
                        ->contain([
                            'Staffs' => function ($q) {
                                return $q->select(['Staffs.full_name'])
                                    ->where(['Staffs.active' => 1])
                                    ->contain([
                                        'Titles' => function ($q) {
                                            return $q->select(['Titles.title']);
                                        },
                                        'Positions' => function ($q) {
                                            return $q->select(['Positions.position']);
                                        }
                                    ]);
                            }
                        ]);
                },
                'ClassPeriodCourseConstraints',
                'ClassRoomCourseConstraints'
            ]);

        $result = $query->first();

        return $result ?: [];
    }
    /*
    *calculate the number of period per session, the highest comes at top.
    */
    public function get_number_period_per_session($contact_hours = null, $number_of_session = null)
    {

        $remaining_hours = $contact_hours;
        $number_of_period = 0;
        $number_of_period_per_session = array();
        for ($s = $number_of_session; $s >= 1; $s--) {
            $remaining_hours = $remaining_hours - $number_of_period;
            $number_of_period = (int)($remaining_hours / $s);
            $number_of_period_per_session[] = $number_of_period;
        }
        rsort($number_of_period_per_session);
        return $number_of_period_per_session;
    }


    public function get_last_assigned_week_day($section_id = null, $academic_year = null, $semester = null)
    {

        $last_course_schedule_id = $this->find(
            'first',
            array(
                'fields' => array('CourseSchedule.id'),
                'conditions' => array(
                    'CourseSchedule.section_id' => $section_id,
                    'CourseSchedule.academic_year' => $academic_year,
                    'CourseSchedule.semester' => $semester
                ),
                'order' => array('CourseSchedule.created DESC', 'CourseSchedule.id DESC'),
                'recursive' => -1
            )
        );
        if (!empty($last_course_schedule_id)) {
            $last_assigned_class_period_id = $this->CourseSchedulesClassPeriod->find(
                'first',
                array(
                    'fields' => array('CourseSchedulesClassPeriod.class_period_id'),
                    'conditions' => array('CourseSchedulesClassPeriod.course_schedule_id' => $last_course_schedule_id['CourseSchedule']['id']),
                    'order' => array('CourseSchedulesClassPeriod.created DESC'),
                    'recursive' => -1
                )
            );
            $last_assigned_week_day = $this->ClassPeriod->field(
                'ClassPeriod.week_day',
                array('ClassPeriod.id' => $last_assigned_class_period_id['CourseSchedulesClassPeriod']['class_period_id'])
            );

            return $last_assigned_week_day;
        } else {
            return false;
        }
    }

    //get week day possibly two days jump from the last assigned week day
    public function get_next_week_day_from_last_assigned_week_day(
        $last_assigned_week_day = null,
        $college_id = null,
        $program_id = null,
        $program_type_id = null
    ) {

        $list_of_assign_week_day = $this->ClassPeriod->find(
            'list',
            array(
                'fields' => array('ClassPeriod.week_day', 'ClassPeriod.week_day'),
                'conditions' => array(
                    'ClassPeriod.college_id' => $college_id,
                    'ClassPeriod.program_id' => $program_id,
                    'ClassPeriod.program_type_id' => $program_type_id
                ),
                'order' => array('ClassPeriod.week_day'),
                'recursive' => -1
            )
        );

        $organized_list_of_assign_week_day = array();
        foreach ($list_of_assign_week_day as $value) {
            $organized_list_of_assign_week_day[] = $value;
        }

        $index_of_potential_week_day = array_search($last_assigned_week_day, $organized_list_of_assign_week_day);

        $count = count($list_of_assign_week_day);
        $index = null;
        if (($count % 2 == 0)) {
            $index = ($index_of_potential_week_day + 1) % $count;
        } else {
            $index = ($index_of_potential_week_day + 1) % $count;
        }
        $week_day = $organized_list_of_assign_week_day[$index];

        return $week_day;
    }

    public function get_first_week_day($college_id = null, $program_id = null, $program_type_id = null)
    {

        $first_week_day = $this->ClassPeriod->find(
            'first',
            array(
                'fields' => array('ClassPeriod.week_day'),
                'conditions' => array(
                    'ClassPeriod.college_id' => $college_id,
                    'ClassPeriod.program_id' => $program_id,
                    'ClassPeriod.program_type_id' => $program_type_id
                ),
                'order' => array('ClassPeriod.week_day'),
                'recursive' => -1
            )
        );
        if (!empty($first_week_day)) {
            return $first_week_day['ClassPeriod']['week_day'];
        }
    }

    public function get_list_of_class_period_id(
        $potential_week_day = null,
        $college_id,
        $program_id = null,
        $program_type_id = null,
        $published_course_id = null,
        $academic_year = null,
        $semester = null,
        $section_id = null,
        $course_type = null
    ) {

        //To find instructor occupied class periods from constraints
        $staff_ids = array();
        $staff_ids = $this->PublishedCourse->CourseInstructorAssignment->find(
            'list',
            array(
                'fields' => array('CourseInstructorAssignment.staff_id', 'CourseInstructorAssignment.staff_id'),
                'conditions' => array(
                    'CourseInstructorAssignment.published_course_id' => $published_course_id,
                    'CourseInstructorAssignment.section_id' => $section_id,
                    'CourseInstructorAssignment.academic_year' => $academic_year,
                    'CourseInstructorAssignment.semester' => $semester,
                    'UPPER(CourseInstructorAssignment.type) LIKE' => '%' . strtoupper($course_type) . '%'
                )
            )
        );

        $occupied_instructor_class_period_from_constraint = $this->ClassPeriod->InstructorClassPeriodCourseConstraint->find(
            'list',
            array(
                'fields' => array(
                    'InstructorClassPeriodCourseConstraint.class_period_id',
                    'InstructorClassPeriodCourseConstraint.class_period_id'
                ),
                'conditions' => array(
                    'InstructorClassPeriodCourseConstraint.staff_id' => $staff_ids,
                    'InstructorClassPeriodCourseConstraint.academic_year' => $academic_year,
                    'InstructorClassPeriodCourseConstraint.semester' => $semester,
                    'InstructorClassPeriodCourseConstraint.active' => 1
                )
            )
        );

        //To find instructor already scheduled class periods from course scheduled
        $published_course_ids = $this->PublishedCourse->CourseInstructorAssignment->find(
            'list',
            array(
                'fields' => array(
                    'CourseInstructorAssignment.published_course_id',
                    'CourseInstructorAssignment.published_course_id'
                ),
                'conditions' => array(
                    'CourseInstructorAssignment.staff_id' => $staff_ids,
                    'CourseInstructorAssignment.academic_year' => $academic_year,
                    'CourseInstructorAssignment.semester' => $semester
                )
            )
        );

        $course_schedule_ids = $this->find(
            'list',
            array(
                'fields' => array('CourseSchedule.id', 'CourseSchedule.id'),
                'conditions' => array(
                    'CourseSchedule.published_course_id' => $published_course_ids,
                    'CourseSchedule.academic_year' => $academic_year,
                    'CourseSchedule.semester' => $semester
                )
            )
        );

        $occupied_instructor_class_period_from_schedule = $this->CourseSchedulesClassPeriod->find(
            'list',
            array(
                'fields' => array(
                    'CourseSchedulesClassPeriod.class_period_id',
                    'CourseSchedulesClassPeriod.class_period_id'
                ),
                'conditions' => array('CourseSchedulesClassPeriod.course_schedule_id' => $course_schedule_ids)
            )
        );

        //To find Section already Scheduled class Periods
        $section_course_schedule_ids = $this->find(
            'list',
            array(
                'fields' => array('CourseSchedule.id', 'CourseSchedule.id'),
                'conditions' => array(
                    'CourseSchedule.section_id' => $section_id,
                    'CourseSchedule.academic_year' => $academic_year,
                    'CourseSchedule.semester' => $semester
                )
            )
        );
        $occupied_section_class_periods = $this->CourseSchedulesClassPeriod->find(
            'list',
            array(
                'fields' => array(
                    'CourseSchedulesClassPeriod.class_period_id',
                    'CourseSchedulesClassPeriod.class_period_id'
                ),
                'conditions' => array('CourseSchedulesClassPeriod.course_schedule_id' => $section_course_schedule_ids)
            )
        );
        $occupied_class_rooms = array_merge(
            $occupied_instructor_class_period_from_constraint,
            $occupied_instructor_class_period_from_schedule,
            $occupied_section_class_periods
        );
        $list_of_class_period_ids = $this->ClassPeriod->find(
            'list',
            array(
                'fields' => array('ClassPeriod.id'),
                'conditions' => array(
                    'ClassPeriod.week_day' => $potential_week_day,
                    'ClassPeriod.college_id' => $college_id,
                    'ClassPeriod.program_id' => $program_id,
                    'ClassPeriod.program_type_id' => $program_type_id,
                    "NOT" => array('ClassPeriod.id' => $occupied_class_rooms)
                ),
                'order' => array('ClassPeriod.period_setting_id')
            )
        );
        return $list_of_class_period_ids;
    }

    public function get_continuous_grouped_class_period_list_into_period_number(
        $list_of_class_period_ids = null,
        $period_number = null
    ) {

        //group class periods in to period number
        $possible_group_of_class_periods_per_period_number = array();
        foreach ($list_of_class_period_ids as $cpk => $cpv) {
            if (isset($list_of_class_period_ids[($cpk + ($period_number - 1))])) {
                for ($i = 0; $i < $period_number; $i++) {
                    if (isset($list_of_class_period_ids[($cpk + $i)])) {
                        $possible_group_of_class_periods_per_period_number[$cpk][] = $list_of_class_period_ids[($cpk + $i)];
                    }
                }
            }
        }
        //for each list of class period ids get period starting hours
        $list_of_class_period_ids_hour = array();
        foreach ($list_of_class_period_ids as $lcpk => $lcpv) {
            $period_setting_id = $this->ClassPeriod->field(
                'ClassPeriod.period_setting_id',
                array('ClassPeriod.id' => $lcpv)
            );
            $list_of_class_period_ids_hour[$lcpv] = substr(
                ($this->ClassPeriod->PeriodSetting->field(
                    'PeriodSetting.hour',
                    array('PeriodSetting.id' => $period_setting_id)
                )),
                0,
                2
            );
        }
        //Check the continute of grouped class periods and put continuous class period in array
        $continuous_grouped_class_period_list_into_period_number = array();
        foreach ($possible_group_of_class_periods_per_period_number as $pgcpk => $pgcpv) {
            $iscontinous = false;
            if (count($pgcpv) == 1) {
                $iscontinous = true;
            } else {
                foreach ($pgcpv as $key => $value) {
                    if (isset($pgcpv[$key + 1])) {
                        if (($list_of_class_period_ids_hour[$pgcpv[$key + 1]] - $list_of_class_period_ids_hour[$pgcpv[$key]]) == 1) {
                            $iscontinous = true;
                        } else {
                            $iscontinous = false;
                            break 1;
                        }
                    }
                }
            }
            if ($iscontinous == true) {
                $continuous_grouped_class_period_list_into_period_number[] = $pgcpv;
            }
        }
        return $continuous_grouped_class_period_list_into_period_number;
    }

    /*
    *Get class Rooms that assigned for the program and program type by excluding class room that stated     *as donot assign in class room constraints table
    */
    public function get_potential_class_rooms(
        $college_id = null,
        $program_id = null,
        $program_type_id = null,
        $class_room_constraint_stated_donot_assign = null
    ) {

        $college_class_room_blocks = $this->ClassRoom->ClassRoomBlock->find(
            'list',
            array(
                'fields' => array('ClassRoomBlock.id'),
                'conditions' => array('ClassRoomBlock.college_id' => $college_id)
            )
        );
        $college_available_class_rooms = $this->ClassRoom->find(
            'list',
            array(
                'fields' => array('ClassRoom.id'),
                'conditions' => array(
                    'ClassRoom.class_room_block_id' => $college_class_room_blocks,
                    'ClassRoom.available_for_lecture' => 1
                )
            )
        );
        $class_rooms = $this->ClassRoom->ProgramProgramTypeClassRoom->find(
            'list',
            array(
                'fields' => array(
                    'ProgramProgramTypeClassRoom.class_room_id',
                    'ProgramProgramTypeClassRoom.class_room_id'
                ),
                'conditions' => array(
                    'ProgramProgramTypeClassRoom.program_id' => $program_id,
                    'ProgramProgramTypeClassRoom.program_type_id' => $program_type_id,
                    'ProgramProgramTypeClassRoom.class_room_id' => $college_available_class_rooms,
                    "NOT" => array('ProgramProgramTypeClassRoom.class_room_id' => $class_room_constraint_stated_donot_assign)
                )
            )
        );

        return $class_rooms;
    }

    public function sort_potential_class_rooms(
        $total_active_students_of_this_section = null,
        $sorted_potential_class_rooms = null
    ) {

        $class_room_with_coefficient = array();
        foreach ($sorted_potential_class_rooms as $class_room_id) {
            $room_capacity = $this->ClassRoom->field(
                'ClassRoom.lecture_capacity',
                array('ClassRoom.id' => $class_room_id)
            );

            $class_room_with_coefficient[$class_room_id] = abs(
                ($room_capacity - $total_active_students_of_this_section)
            );
        }
        if (!empty($class_room_with_coefficient)) {
            asort($class_room_with_coefficient);
        }

        //$sort_potential_class_rooms = array();
        return $class_room_with_coefficient;
    }

    //When continuous grouped class period list in period number given
    public function get_match_periods_and_class_room(
        $continuous_grouped_class_period_list_into_period_number = null,
        $sorted_potential_class_rooms = null,
        $academic_year = null,
        $semester = null
    ) {

        $match_periods_and_class_room = array();
        foreach ($continuous_grouped_class_period_list_into_period_number as $grouped_class_period) {
            $is_free = false;
            foreach ($sorted_potential_class_rooms as $class_room_id => $coefficient) {
                foreach ($grouped_class_period as $class_period_id) {
                    //get course schedule in the given class period id
                    $course_schedule_id = $this->CourseSchedulesClassPeriod->find(
                        'list',
                        array(
                            'fields' => array(
                                'CourseSchedulesClassPeriod.course_schedule_id',
                                'CourseSchedulesClassPeriod.course_schedule_id'
                            ),
                            'conditions' => array('CourseSchedulesClassPeriod.class_period_id' => $class_period_id)
                        )
                    );
                    //get occupied class room from course schedule table in the given course schedule, semester and academic year
                    $occupied_class_rooms_from_schedule = $this->find(
                        'list',
                        array(
                            'fields' => array('CourseSchedule.class_room_id', 'CourseSchedule.class_room_id'),
                            'conditions' => array(
                                'CourseSchedule.id' => $course_schedule_id,
                                'CourseSchedule.academic_year' => $academic_year,
                                'CourseSchedule.semester' => $semester
                            )
                        )
                    );
                    //get occpied class room from class room class period constraints in a given period, academic year and semester
                    $occupied_class_rooms_from_constraint = $this->ClassRoom->ClassRoomClassPeriodConstraint->find(
                        'list',
                        array(
                            'fields' => array(
                                'ClassRoomClassPeriodConstraint.class_room_id',
                                'ClassRoomClassPeriodConstraint.class_room_id'
                            ),
                            'conditions' => array(
                                'ClassRoomClassPeriodConstraint.class_period_id' => $class_period_id,
                                'ClassRoomClassPeriodConstraint.academic_year' => $academic_year,
                                'ClassRoomClassPeriodConstraint.semester' => $semester,
                                'ClassRoomClassPeriodConstraint.active' => 1
                            )
                        )
                    );

                    $occupied_class_rooms = array_merge(
                        $occupied_class_rooms_from_schedule,
                        $occupied_class_rooms_from_constraint
                    );

                    if (in_array($class_room_id, $occupied_class_rooms)) {
                        $is_free = false;
                    } else {
                        $is_free = true;
                    }
                }

                if ($is_free == true) {
                    $match_periods_and_class_room['class_periods'] = $grouped_class_period;
                    $match_periods_and_class_room['class_room'] = $class_room_id;

                    return $match_periods_and_class_room;
                }
            }
        }
        return false;
    }

    public function get_next_week_day(
        $potential_week_day = null,
        $college_id = null,
        $program_id = null,
        $program_type_id = null
    ) {

        $list_of_assign_week_day = $this->ClassPeriod->find(
            'list',
            array(
                'fields' => array('ClassPeriod.week_day', 'ClassPeriod.week_day'),
                'conditions' => array(
                    'ClassPeriod.college_id' => $college_id,
                    'ClassPeriod.program_id' => $program_id,
                    'ClassPeriod.program_type_id' => $program_type_id
                ),
                'order' => array('ClassPeriod.week_day'),
                'recursive' => -1
            )
        );
        $organized_list_of_assign_week_day = array();
        foreach ($list_of_assign_week_day as $value) {
            $organized_list_of_assign_week_day[] = $value;
        }
        $index_of_potential_week_day = array_search($potential_week_day, $organized_list_of_assign_week_day);
        $count = count($list_of_assign_week_day);
        $index_week_day = ($index_of_potential_week_day + 1) % $count;
        $next_week_day = $organized_list_of_assign_week_day[$index_week_day];

        return $next_week_day;
    }

    public function get_course_schedule_sections($conditions = null, $academic_year = null, $semester = null)
    {

        if (!empty($conditions)) {
            $publishedCourses = $this->PublishedCourse->find('all', array(
                'conditions' => $conditions,
                'fields' => array('PublishedCourse.id'),
                'contain' => array('Section' => array('fields' => array('Section.id', 'Section.name')))
            ));
            $section_array = array();
            foreach ($publishedCourses as $publishedCourse) {
                $section_array[$publishedCourse['Section']['id']]['id'] = $publishedCourse['Section']['id'];
                $section_array[$publishedCourse['Section']['id']]['name'] = $publishedCourse['Section']['name'];
            }
            $sections = array();
            foreach ($section_array as $sk => $sv) {
                $is_section_have_course_schedule = $this->find(
                    'count',
                    array(
                        'conditions' => array(
                            'CourseSchedule.section_id' => $sv['id'],
                            'CourseSchedule.academic_year' => $academic_year,
                            'CourseSchedule.semester' => $semester
                        )
                    )
                );
                if (!empty($is_section_have_course_schedule)) {
                    $sections[] = $sv;
                }
            }
            //debug($sections);
            return $sections;
        }
    }

    public function is_there_defined_class_period($college_id = null, $program_id = null, $program_type_id = null)
    {

        $count_classPeriods = $this->ClassPeriod->find(
            'count',
            array(
                'conditions' => array(
                    'ClassPeriod.college_id' => $college_id,
                    'ClassPeriod.program_id' => $program_id,
                    'ClassPeriod.program_type_id' => $program_type_id
                )
            )
        );
        if (!empty($count_classPeriods)) {
            return true;
        } else {
            return false;
        }
    }

    public function is_there_defined_class_room($college_id = null, $program_id = null, $program_type_id = null)
    {

        $college_class_room_blocks = $this->ClassRoom->ClassRoomBlock->find(
            'list',
            array(
                'fields' => array('ClassRoomBlock.id'),
                'conditions' => array('ClassRoomBlock.college_id' => $college_id)
            )
        );

        $college_available_class_rooms = $this->ClassRoom->find(
            'list',
            array(
                'fields' => array('ClassRoom.id'),
                'conditions' => array(
                    'ClassRoom.class_room_block_id' => $college_class_room_blocks,
                    "OR" => array(
                        'ClassRoom.available_for_lecture' => 1,
                        "NOT" => array('ClassRoom.available_for_exam' => 1)
                    )
                )
            )
        );

        $count_class_rooms = $this->ClassRoom->ProgramProgramTypeClassRoom->find(
            'count',
            array(
                'conditions' => array(
                    'ProgramProgramTypeClassRoom.program_id' => $program_id,
                    'ProgramProgramTypeClassRoom.program_type_id' => $program_type_id,
                    'ProgramProgramTypeClassRoom.class_room_id' => $college_available_class_rooms
                )
            )
        );
        if (!empty($count_class_rooms)) {
            return true;
        } else {
            return false;
        }
    }

    //get a group of continuous class_period_lists per period number of hours take the class period
    //sepcified in class period course constraints as starting period.
    public function get_continuous_grouped_class_period_list_into_period_number_from_constraint(
        $period_number = null,
        $potential_class_period_ids_from_constranints = null,
        $published_course_id = null,
        $college_id = null,
        $program_id = null,
        $program_type_id = null,
        $academic_year = null,
        $semester = null,
        $section_id = null,
        $course_type = null
    ) {

        $possible_group_of_class_periods_per_period_number = array();
        $list_of_class_period_ids_hour = array();
        foreach ($potential_class_period_ids_from_constranints as $class_period_id) {
            $week_day = $this->ClassPeriod->field('ClassPeriod.week_day', array('ClassPeriod.id' => $class_period_id));
            $list_of_class_period_ids = $this->get_list_of_class_period_id(
                $week_day,
                $college_id,
                $program_id,
                $program_type_id,
                $published_course_id,
                $academic_year,
                $semester,
                $section_id,
                $course_type
            );

            if (isset($list_of_class_period_ids[($class_period_id + ($period_number - 1))])) {
                for ($i = 0; $i < $period_number; $i++) {
                    $possible_group_of_class_periods_per_period_number[$class_period_id][] = $list_of_class_period_ids[($class_period_id + $i)];
                }
            }

            //for each list of class period ids get period starting hours
            foreach ($list_of_class_period_ids as $lcpk => $lcpv) {
                $period_setting_id = $this->ClassPeriod->field(
                    'ClassPeriod.period_setting_id',
                    array('ClassPeriod.id' => $lcpv)
                );
                $list_of_class_period_ids_hour[$lcpv] = substr(
                    $this->ClassPeriod->PeriodSetting->field(
                        'PeriodSetting.hour',
                        array('PeriodSetting.id' => $period_setting_id)
                    ),
                    0,
                    2
                );
            }
        }
        //Check the continute of grouped class periods and put continuous class period in array
        $continuous_grouped_class_period_list_into_period_number = array();
        foreach ($possible_group_of_class_periods_per_period_number as $pgcpk => $pgcpv) {
            $iscontinous = false;
            if (count($pgcpv) == 1) {
                $iscontinous = true;
            } else {
                foreach ($pgcpv as $key => $value) {
                    if (isset($pgcpv[$key + 1])) {
                        if ($list_of_class_period_ids_hour[$pgcpv[$key + 1]] - $list_of_class_period_ids_hour[$pgcpv[$key]] == 1) {
                            $iscontinous = true;
                        }
                    }
                }
            }
            if ($iscontinous == true) {
                $continuous_grouped_class_period_list_into_period_number[] = $pgcpv;
            }
        }

        return $continuous_grouped_class_period_list_into_period_number;
    }

    /********************************************************************
     * Get section course schedule in the given academic year and semester
     *********************************************************************/

    public function get_section_course_schedule($section_id = null, $academic_year = null, $semester = null)
    {

        $courseSchedules = $this->find('all', array(
            'conditions' => array(
                'CourseSchedule.section_id' => $section_id,
                'CourseSchedule.academic_year' => $academic_year,
                'CourseSchedule.semester' => $semester
            ),
            'contain' => array(
                'ClassRoom' => array(
                    'fields' => array('ClassRoom.room_code'),
                    "ClassRoomBlock" => array(
                        'fields' => array("ClassRoomBlock.id"),
                        "Campus" => array('fields' => array("Campus.name"))
                    )
                ),
                'PublishedCourse' => array(
                    'fields' => array('PublishedCourse.id'),
                    'Course' => array('fields' => array('Course.course_title', 'Course.course_code')),
                    'CourseInstructorAssignment' => array(
                        'fields' => array(
                            'CourseInstructorAssignment.id',
                            'CourseInstructorAssignment.staff_id',
                            'CourseInstructorAssignment.type',
                            'CourseInstructorAssignment.isprimary'
                        ),
                        'Staff' => array(
                            'fields' => array('Staff.full_name'),
                            'conditions' => array('Staff.active' => 1),
                            'Title' => array('fields' => array('title')),
                            'Position' => array('fields' => array('position'))
                        )
                    )
                ),
                'Section' => array('fields' => array('Section.name')),
                'CourseSplitSection',
                'ClassPeriod' => array(
                    'fields' => array(
                        'ClassPeriod.id',
                        'ClassPeriod.week_day',
                        'ClassPeriod.period_setting_id'
                    ),
                    'PeriodSetting' => array('fields' => array('PeriodSetting.hour'))
                )
            )
        ));

        return $courseSchedules;
    }

    // get starting and ending hour from class period set for college, program and program type
    public function get_starting_and_ending_hour($college_id = null, $program_id = null, $program_type_id = null)
    {

        $classPeriods = $this->ClassPeriod->find(
            'all',
            array(
                'conditions' => array(
                    'ClassPeriod.college_id' => $college_id,
                    'ClassPeriod.program_id' => $program_id,
                    'ClassPeriod.program_type_id' => $program_type_id
                ),
                'contain' => array('PeriodSetting' => array('fields' => array('PeriodSetting.hour')))
            )
        );
        $periods = array();
        foreach ($classPeriods as $classPeriod_value) {
            $periods[$classPeriod_value['PeriodSetting']['hour']] = $classPeriod_value['PeriodSetting']['hour'];
        }
        asort($periods);
        $starting_and_ending_hour['starting'] = current($periods);
        $starting_and_ending_hour['ending'] = end($periods);
        return $starting_and_ending_hour;
    }

    //get course schedule of ongoing courses for a  given instructor
    use Cake\ORM\TableRegistry;

    public function getCourseSchedulesForInstructor($userId = null, $roleId = null)
    {
        if ($roleId === ROLE_STUDENT) {
            return [];
        }

        $Staffs = TableRegistry::getTableLocator()->get('Staffs');
        $staff = $Staffs->find()
            ->enableHydration(false)
            ->where(['Staffs.user_id' => $userId])
            ->first();

        if (!$staff) {
            return [];
        }

        $Assignments = TableRegistry::getTableLocator()->get('CourseInstructorAssignments');
        $latest = $Assignments->find()
            ->enableHydration(false)
            ->where(['CourseInstructorAssignments.staff_id' => $staff['id']])
            ->order(['CourseInstructorAssignments.created' => 'DESC'])
            ->first();

        if (!$latest) {
            return [];
        }

        $assignments = $Assignments->find()
            ->enableHydration(false)
            ->contain([
                'PublishedCourses' => [
                    'Departments',
                    'Colleges',
                    'Sections',
                    'Courses',
                    'CourseRegistrations.ExamGrades' => ['sort' => ['ExamGrades.created' => 'DESC']],
                    'CourseAdds.ExamGrades' => ['sort' => ['ExamGrades.created' => 'DESC']]
                ]
            ])
            ->where([
                'CourseInstructorAssignments.staff_id' => $staff['id'],
                'CourseInstructorAssignments.academic_year' => $latest['academic_year'],
                'CourseInstructorAssignments.semester' => $latest['semester']
            ])
            ->toArray();

        $ongoing = [];

        foreach ($assignments as $assignment) {
            $published = $assignment['published_course'];
            $registrations = $published['course_registrations'] ?? [];
            $adds = $published['course_adds'] ?? [];

            $gradeSubmitted = true;

            foreach ($registrations as $cr) {
                if (!empty($cr['exam_grades'][0]['department_approval']) && $cr['exam_grades'][0]['department_approval'] === -1) {
                    $gradeSubmitted = false;
                    break;
                }
            }

            if ($gradeSubmitted) {
                foreach ($adds as $ca) {
                    if (!empty($ca['exam_grades'][0]['department_approval']) && $ca['exam_grades'][0]['department_approval'] === -1) {
                        $gradeSubmitted = false;
                        break;
                    }
                }
            }

            if (!$gradeSubmitted && $published['drop'] == 0) {
                $ongoing[] = [
                    'section_id' => $published['section']['id'],
                    'published_course_id' => $published['id'],
                    'course_type' => $assignment['type']
                ];
            }
        }

        $CourseSchedules = TableRegistry::getTableLocator()->get('CourseSchedules');
        $schedules = [];

        foreach ($ongoing as $oc) {
            $typeList = explode('+', $oc['course_type']);
            $schedules[] = $CourseSchedules->find()
                ->enableHydration(false)
                ->contain([
                    'ClassRooms' => [
                        'fields' => ['room_code'],
                        'ClassRoomBlocks' => [
                            'fields' => ['id'],
                            'Campuses' => ['fields' => ['name']]
                        ]
                    ],
                    'PublishedCourses' => [
                        'fields' => ['id'],
                        'Courses' => ['fields' => ['course_title', 'course_code']],
                        'Departments' => ['fields' => ['name']],
                        'Colleges' => ['fields' => ['name']]
                    ],
                    'Sections' => ['fields' => ['name']],
                    'CourseSplitSections',
                    'ClassPeriods' => [
                        'fields' => ['id', 'week_day', 'period_setting_id'],
                        'PeriodSettings' => ['fields' => ['hour']]
                    ]
                ])
                ->where([
                    'CourseSchedules.published_course_id' => $oc['published_course_id'],
                    'CourseSchedules.section_id' => $oc['section_id'],
                    'CourseSchedules.academic_year' => $latest['academic_year'],
                    'CourseSchedules.semester' => $latest['semester'],
                    'CourseSchedules.type IN' => $typeList
                ])
                ->toArray();
        }

        return $schedules;
    }

    //get course schedule of current academic year and semester for a given student
    public function getCourseSchedulesForStudent($student_id = null, $current_academic_year = null)
    {

        $student_course_schedules = array();
        //find student active section
        $student_section_id = $this->Section->StudentsSection->field(
            'StudentsSection.section_id',
            array('StudentsSection.student_id' => $student_id, 'StudentsSection.archive' => 0)
        );
        //debug($student_section_id);
        if (!empty($student_section_id)) {
            //get student student latest semester and academic year
            $student_latest_semester_and_academic_year = $this->PublishedCourse->CourseRegistration->ExamGrade->getListOfAyAndSemester(
                $student_id
            );
            //debug($student_latest_semester_and_academic_year);
            if (!empty($student_latest_semester_and_academic_year)) {
                $count = count($student_latest_semester_and_academic_year);
                $student_latest_semester = $student_latest_semester_and_academic_year[($count - 1)]['semester'];
                //debug($student_latest_semester);
                $student_latest_academic_year = $student_latest_semester_and_academic_year[($count - 1)]['academic_year'];


                $section_data = $this->Section->find(
                    'first',
                    array(
                        'fields' => array('Section.college_id', 'Section.program_id', 'Section.program_type_id'),
                        'conditions' => array('Section.id' => $student_section_id)
                    )
                );
                if (!empty($section_data)) {
                    $section_course_schedule = array();
                    $section_course_schedule[$student_section_id] = $this->get_section_course_schedule(
                        $student_section_id,
                        $student_latest_academic_year,
                        $student_latest_semester
                    );
                    //debug($section_course_schedule);
                    //get section college, program and program type from student section


                    $college_id = $section_data['Section']['college_id'];
                    $program_id = $section_data['Section']['program_id'];
                    $program_type_id = $section_data['Section']['program_type_id'];
                    $starting_and_ending_hour = $this->get_starting_and_ending_hour(
                        $college_id,
                        $program_id,
                        $program_type_id
                    );
                    $student_course_schedules['section_course_schedule'] = $section_course_schedule;
                    $student_course_schedules['starting_and_ending_hour'] = $starting_and_ending_hour;
                }
            }
        }
        //debug($student_course_schedules);
        return $student_course_schedules;
    }

    public function is_class_room_used($id = null)
    {

        $count = $this->find('count', array('conditions' => array('CourseSchedule.class_room_id' => $id), 'limit' => 2)
        );
        return $count;
    }

    public function getDateListToTakeAttendance($published_course_id = null)
    {

        $published_course_detail = $this->PublishedCourse->find(
            'first',
            array(
                'conditions' =>
                    array(
                        'PublishedCourse.id' => $published_course_id
                    ),
                'contain' =>
                    array(
                        'CourseInstructorAssignment' =>
                            array(
                                'conditions' =>
                                    array(
                                        'CourseInstructorAssignment.type LIKE \'%Lecture%\''
                                    )
                            ),
                        'CourseSchedule' =>
                            array(
                                'ClassPeriod' =>
                                    array(
                                        'PeriodSetting'
                                    )
                            )
                    )
            )
        );

        /*
        1. If published course date passed 2 weeks then start the date from the current date - 14 days
        2. Otherwise start from the course publish date
        3. Then build the attendance date list till today based on the course week day
        */
        $attendance_back_date = date(
            'Y-m-d H:i:s',
            mktime(date('H'), date('i'), date('s'), date('n'), date('j') - 14, date('Y'))
        );
        //Attendace end date is 4 months after the publish date
        $attendance_end_date = date(
            'Y-m-d H:i:s',
            mktime(
                substr($published_course_detail['CourseInstructorAssignment'][0]['created'], 11, 2),
                substr($published_course_detail['CourseInstructorAssignment'][0]['created'], 14, 2),
                substr($published_course_detail['CourseInstructorAssignment'][0]['created'], 17, 2),
                substr($published_course_detail['CourseInstructorAssignment'][0]['created'], 5, 2),
                substr($published_course_detail['CourseInstructorAssignment'][0]['created'], 8, 2) + 120,
                substr($published_course_detail['CourseInstructorAssignment'][0]['created'], 0, 4)
            )
        );

        if ($published_course_detail['CourseInstructorAssignment'][0]['created'] > $attendance_back_date) {
            $attendance_start_date = $published_course_detail['CourseInstructorAssignment'][0]['created'];
        } else {
            $attendance_start_date = $attendance_back_date;
        }
        $week_days = array();
        foreach ($published_course_detail['CourseSchedule'] as $courseSchedule) {
            foreach ($courseSchedule['ClassPeriod'] as $key => $classPeriod) {
                $week_days[] = $classPeriod['week_day'];
            }
        }

        /*
        //The following code is before schedule is modified
        foreach($published_course_detail['CourseSchedule'] as $key => $course_schedule) {
            $week_days[] = $course_schedule['week_day'];
        }
        */
        //To strat from the right week day
        $attendace_start_week_day = date(
            'w',
            mktime(
                substr($attendance_start_date, 11, 2),
                substr($attendance_start_date, 14, 2),
                substr($attendance_start_date, 17, 2),
                substr($attendance_start_date, 5, 2),
                substr($attendance_start_date, 8, 2),
                substr($attendance_start_date, 0, 4)
            )
        );
        $test_count = 0;
        while (!in_array($attendace_start_week_day, $week_days)) {
            if ($test_count++ > 200) {
                return array();
            }
            $attendance_start_date = date(
                'Y-m-d H:i:s',
                mktime(
                    substr($attendance_start_date, 11, 2),
                    substr($attendance_start_date, 14, 2),
                    substr($attendance_start_date, 17, 2),
                    substr($attendance_start_date, 5, 2),
                    substr($attendance_start_date, 8, 2) + 1,
                    substr($attendance_start_date, 0, 4)
                )
            );
            $attendace_start_week_day = date(
                'w',
                mktime(
                    substr($attendance_start_date, 11, 2),
                    substr($attendance_start_date, 14, 2),
                    substr($attendance_start_date, 17, 2),
                    substr($attendance_start_date, 5, 2),
                    substr($attendance_start_date, 8, 2),
                    substr($attendance_start_date, 0, 4)
                )
            );
        }
        //Build the date list (starting from the start date to till now but not exceeds 4 months)
        $attendace_date_list = array();
        $attendance_next_date = $attendance_start_date;
        $test_count = 0;
        while ($attendance_next_date <= date('Y-m-d H:i:s') && $attendance_next_date <= $attendance_end_date) {
            if ($test_count++ > 200) {
                return array();
            }
            $attendace_week_day = date(
                'w',
                mktime(
                    substr($attendance_next_date, 11, 2),
                    substr($attendance_next_date, 14, 2),
                    substr($attendance_next_date, 17, 2),
                    substr($attendance_next_date, 5, 2),
                    substr($attendance_next_date, 8, 2),
                    substr($attendance_next_date, 0, 4)
                )
            );
            if (in_array($attendace_week_day, $week_days)) {
                $attendace_date_list[] = $attendance_next_date;
            }
            $attendance_next_date = date(
                'Y-m-d H:i:s',
                mktime(
                    substr($attendance_next_date, 11, 2),
                    substr($attendance_next_date, 14, 2),
                    substr($attendance_next_date, 17, 2),
                    substr($attendance_next_date, 5, 2),
                    substr($attendance_next_date, 8, 2) + 1,
                    substr($attendance_next_date, 0, 4)
                )
            );
        }
        $formatted_attendace_date_list = array();
        foreach ($attendace_date_list as $key => $value) {
            $formatted_date = date(
                'D M d, Y',
                mktime(
                    substr($value, 11, 2),
                    substr($value, 14, 2),
                    substr($value, 17, 2),
                    substr($value, 5, 2),
                    substr($value, 8, 2),
                    substr($value, 0, 4)
                )
            );
            $formatted_attendace_date_list[substr($value, 0, 10)] = $formatted_date;
        }
        $formatted_attendace_date_list = array(0 => 'Select Attendance Date') + $formatted_attendace_date_list;
        return $formatted_attendace_date_list;
    }

    public function returnPeriodWhichAreConsequetive($period_number = null, $period_id_list = null)
    {

        $organized_period_list = array();

        ksort($period_id_list);

        foreach ($period_id_list as $week => $value) {
            if ($period_number == 1) {
                foreach ($value as $k => $v) {
                    $organized_period_list[$week][$k] =
                        date(
                            "h:i A",
                            strtotime($v)
                        );
                }
            } else {
                ///////////test

                $play = $value;
                $first_index = null;
                foreach ($play as $pi => $pv) {
                    $first_index = $pi;
                    break;
                }

                $groups = array(
                    0 =>
                        array($play[$first_index])
                );
                //debug($value);
                $i = 0;
                $jump = 0;

                foreach ($play as $key => $time) {
                    $last_time = !isset($groups[$i]) ? $time - 1 :
                        $groups[$i][count($groups[$i]) - 1]; //Get last set time

                    if (($time - $last_time) > 1) {
                        $last_time = $time;
                        $jump++;
                    }


                    if ($last_time + 1 == $time) {
                        $groups[$i][] = $time; //Add this number to the end of a group
                    }


                    if (count($groups[$i]) == $period_number) {
                        $i++; //Increment group count
                        $groups[$i][] = $time;
                        //Create a new group with this time as the first one
                    }
                    if ($jump > 0) {
                        $i++; //Increment group count
                        $groups[$i][] = $time;
                        $jump = 0;
                    }
                }


                foreach ($groups as $ki => $times) {
                    if (count($times) < $period_number) {
                        unset($groups[$ki]); //Unset any groups of less than period
                    }
                }

                sort($groups); //Reset top-level keys (group ids)


                foreach ($groups as $k => $pv) {
                    $tmpperiod_ids = null;
                    $tmpdisplay_time_format = null;
                    // period
                    foreach ($pv as $ti => $tv) {
                        $found_key = null;
                        foreach ($value as $fkk => $ffvv) {
                            if ($ffvv == $tv) {
                                $found_key = $fkk;
                                break 1;
                            }
                        }

                        $tmpperiod_ids .= $found_key . '~';
                    }


                    // time
                    foreach ($pv as $ti => $tv) {
                        $tmpdisplay_time_format .= ' ' . date(
                                "h:i A",
                                strtotime($tv)
                            );
                    }

                    $organized_period_list[$week][$tmpperiod_ids] = $tmpdisplay_time_format;
                }
            }
        }

        $tmp = array();

        foreach ($organized_period_list as $week => $vvvv) {
            $week_day_name = null;
            if ($week == 1) {
                $week_day_name = 'Sunday';
            } elseif ($week == 2) {
                $week_day_name = 'Monday';
            } elseif ($week == 3) {
                $week_day_name = 'Tuesday';
            } elseif ($week == 4) {
                $week_day_name = 'Wednesday';
            } elseif ($week == 5) {
                $week_day_name = 'Thursday';
            } elseif ($week == 6) {
                $week_day_name = 'Friday';
            } elseif ($week == 7) {
                $week_day_name = 'Saturday';
            }
            // asort($vvvv);
            foreach ($vvvv as $period => $display) {
                $tmp[$week_day_name][$period] = $display;
            }
        }
        return $tmp;
        return $organized_period_list;
    }

    public function getPotentialClassPeriods($course_schedules = null, $college_id = null)
    {

        $assigned_class_periods = array();

        foreach ($course_schedules['ClassPeriod'] as $in => $value) {
            $assigned_class_periods[] = $value['id'];
        }

        $get_all_schedules_periods = $this->find(
            'all',
            array(
                'conditions' => array(
                    'CourseSchedule.section_id' => $course_schedules['CourseSchedule']['section_id']
                ),
                'contain' => array(
                    'PublishedCourse',
                    'ClassRoom',
                    'ClassPeriod' => array(
                        'fields' => array(
                            'ClassPeriod.id',
                            'ClassPeriod.week_day',
                            'ClassPeriod.period_setting_id'
                        ),
                        'PeriodSetting' => array('fields' => array('PeriodSetting.hour'))
                    )
                )
            )
        );
        $potential_free_class_period = array();

        $assigned_week_day_hour = array();
        $unassigned_week_day_hour = array();
        $list_of_class_period_ids = array();
        $unassigned_list_of_class_period_ids = array();
        // get all scheduled time of the section.

        foreach ($get_all_schedules_periods as $scp => $scv) {
            foreach ($scv['ClassPeriod'] as $in => $value) {
                $assigned_week_day_hour[$scv['CourseSchedule']['id']][$value['week_day']][] = $value['PeriodSetting']['hour'];
                $list_of_class_period_ids[$value['week_day']][] = $value['id'];
            }
        }

        $assigned_scheduled_week_day = array();
        $assigned_scheduled_week_day = array_keys($list_of_class_period_ids);
        //
        for ($i = 1; $i <= 7; $i++) {
            if (in_array($i, $assigned_scheduled_week_day)) {
            } else {
                $list_of_class_period_ids[$i] = array();
            }
        }
        //asort($list_of_class_period_ids);

        //  debug($list_of_class_period_ids);


        $not_assigned_class_periods_of_college_for_section = array();

        foreach ($list_of_class_period_ids as $week_day => $week_value) {
            $tmp = ClassRegistry::init('ClassPeriod')->
            find('list', array(
                'conditions' => array(
                    "NOT" => array('ClassPeriod.id' => $week_value),
                    'ClassPeriod.week_day' => $week_day,
                    'ClassPeriod.college_id' => $college_id,
                    'ClassPeriod.program_id' => $course_schedules['PublishedCourse']['program_id'],
                    'ClassPeriod.program_type_id' => $course_schedules['PublishedCourse']['program_type_id']
                ),

                'order' => array('ClassPeriod.week_day', 'ClassPeriod.period_setting_id'),
                'fields' => array('ClassPeriod.id', 'ClassPeriod.id')
            ));

            if (!empty($tmp)) {
                $not_assigned_class_periods_of_college_for_section[$week_day] = $tmp;
            }
        }

        $publishedCourse_details = $this->getPublishedCourseDetails($course_schedules['PublishedCourse']['id']);
        if (strcasecmp($course_schedules['CourseSchedule']['type'], 'Lecture') === 0) {
            $number_of_period_per_session = $this->get_number_period_per_session(
                $publishedCourse_details['Course']['lecture_hours'],
                $publishedCourse_details['PublishedCourse']['lecture_number_of_session']
            );
        } elseif (strcasecmp($course_schedules['CourseSchedule']['type'], 'Lab') === 0) {
            $number_of_period_per_session = $this->get_number_period_per_session(
                $publishedCourse_details['Course']['laboratory_hours'],
                $publishedCourse_details['PublishedCourse']['lab_number_of_session']
            );
        } elseif (strcasecmp($course_schedules['CourseSchedule']['type'], 'Tutorial') === 0) {
            //Tutorial
            $number_of_period_per_session = $this->get_number_period_per_session(
                $publishedCourse_details['Course']['tutorial_hours'],
                $publishedCourse_details['PublishedCourse']['tutorial_number_of_session']
            );
        }

        $number_period = 1;
        foreach ($number_of_period_per_session as $in => $vv) {
            if (count($assigned_class_periods) == $vv) {
                $number_period = $vv;
            }
        }

        $continue_grouped_period_ids = array();

        foreach (
            $not_assigned_class_periods_of_college_for_section as $week => $not_assigned_class_periods
        ) {
            $tmp = $this->
            get_continuous_grouped_class_period_list_into_period_number(
                $not_assigned_class_periods,
                $number_period
            );
            if (!empty($tmp)) {
                $continue_grouped_period_ids[$week] = $tmp;
            }
        }


        //Sorted Assignable Potential class rooms
        $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms(
            $publishedCourse_details,
            $course_schedules['PublishedCourse']['program_id'],
            $course_schedules['PublishedCourse']['program_type_id'],
            $course_schedules['CourseSchedule']['type'],
            $college_id
        );

        //get free class room from sorted_potential_class_rooms in a given periods

        $match_periods_and_class_room = array();
        /*foreach ($continue_grouped_period_ids as $week_day=>$week_value) {

              foreach ($week_value as $in=>$vvvv) {
                    foreach ($vvvv as $vin=>$vpv) {
                      $match_periods_and_class_room[$week_day]['class_periods'][]=$vpv;
                    }

              }

        }
        */
        debug($not_assigned_class_periods_of_college_for_section);

        foreach (
            $not_assigned_class_periods_of_college_for_section as $week => $not_assigned_class_periods
        ) {
            $match_periods_and_class_room[$week]['class_periods'][] = $not_assigned_class_periods;
        }

        $reformated_class_room_class_period = array();
        $period_combo_ids = array();
        $period_dispaly_format = array();
        debug($match_periods_and_class_room);
        foreach ($match_periods_and_class_room as $week => $value) {
            $week_day_name = null;
            if ($week == 1) {
                $week_day_name = 'Sunday';
            } elseif ($week == 2) {
                $week_day_name = 'Monday';
            } elseif ($week == 3) {
                $week_day_name = 'Tuesday';
            } elseif ($week == 4) {
                $week_day_name = 'Wednesday';
            } elseif ($week == 5) {
                $week_day_name = 'Thursday';
            } elseif ($week == 6) {
                $week_day_name = 'Friday';
            } elseif ($week == 7) {
                $week_day_name = 'Saturday';
            }

            if (!empty($value['class_periods'])) {
                foreach ($value['class_periods'] as $key => $periods) {
                    $periodss = ClassRegistry::init('ClassPeriod')->find(
                        'all',
                        array(
                            'conditions' => array(
                                'ClassPeriod.id' => $periods,
                                'ClassPeriod.week_day' => $week
                            ),
                            'contain' => array('PeriodSetting' => array('order' => 'PeriodSetting.period'))
                        )
                    );

                    foreach ($periodss as $in => $vv) {
                        $period_combo_ids[$week][$vv['ClassPeriod']['id']] =
                            $vv['PeriodSetting']['hour'];
                    }
                }
            }
        }

        $rooms = $this->ClassRoom->find('all', array(
            'conditions' => array(
                'ClassRoom.id' => array_keys($sorted_potential_class_rooms)
            ),
            'contain' => array('ClassRoomBlock' => array('Campus'))
        ));

        $room_list = array();
        foreach ($rooms as $ind => $vv) {
            $room_list[$vv['ClassRoomBlock']['Campus']['name']][$vv['ClassRoom']['id']] = $vv['ClassRoom']['room_code'] . '' . $vv['ClassRoomBlock']['block_code'];
        }
        $new_array = array();
        $new_array['rooms'] = $room_list;
        debug($number_period);
        debug($period_combo_ids);
        $new_array['period'] = $this->returnPeriodWhichAreConsequetive($number_period, $period_combo_ids);

        return $new_array;
    }

    //find sorted potentialy assignable class rooms
    public function _get_sorted_potential_class_rooms(
        $publishedCourse_details = null,
        $selected_program = null,
        $selected_program_type = null,
        $course_type = null,
        $college_id = null
    ) {

        //Class room specified as assign in class room constraints for lecture
        $class_room_constraint_assign = array();
        //Class room specified as do not assign in class room constraints for lecture
        $class_room_constraint_stated_donot_assign = array();
        if (!empty($publishedCourse_details['ClassRoomCourseConstraint'])) {
            foreach ($publishedCourse_details['ClassRoomCourseConstraint'] as $classRoomConstraint) {
                if ($classRoomConstraint['active'] == 1 && strcasecmp(
                        $classRoomConstraint['type'],
                        $course_type
                    ) == 0) {
                    $class_room_constraint_assign[] = $classRoomConstraint['class_room_id'];
                } elseif ($classRoomConstraint['active'] == 0 && strcasecmp(
                        $classRoomConstraint['type'],
                        $course_type
                    ) == 0) {
                    $class_room_constraint_stated_donot_assign[] = $classRoomConstraint['class_room_id'];
                }
            }
        }
        $total_active_students_of_this_section = $this->Section->
        get_tottal_active_students_of_the_section(
            $publishedCourse_details['Section']['id']
        );
        $sorted_potential_class_rooms = null;
        if (!empty($class_room_constraint_assign)) {
            //sort class rooms set as assignable constraint  based on the the capacity of room and number of students in the section.
            $sorted_potential_class_rooms = $this->sort_potential_class_rooms(
                $total_active_students_of_this_section,
                $class_room_constraint_assign
            );
            //get free class room from sorted_potential_class_rooms in a given periods
        } else {
            //Get class Rooms assigned for the program and program type by excluding class room that stated as do not assign in class room constraints table
            $potential_class_rooms = $this->get_potential_class_rooms(
                $college_id,
                $selected_program,
                $selected_program_type,
                $class_room_constraint_stated_donot_assign
            );
            //sort potential class rooms based on the the capacity of room and number of students in the section.
            $sorted_potential_class_rooms = $this->sort_potential_class_rooms(
                $total_active_students_of_this_section,
                $potential_class_rooms
            );
        }

        return $sorted_potential_class_rooms;
    }


    public function getPotentialClassPeriodsForUnscheduledCourses(
        $published_course_id = null,
        $college_id = null,
        $type = null
    ) {

        $section_id = $this->PublishedCourse->field(
            'section_id',
            array('PublishedCourse.id' => $published_course_id)
        );
        $publish_courses = $this->PublishedCourse->find(
            'first',
            array('conditions' => array('PublishedCourse.id' => $published_course_id), 'recursive' => -1)
        );

        $get_all_schedules_periods = $this->find(
            'all',
            array(
                'conditions' => array('CourseSchedule.section_id' => $section_id),
                'contain' => array(
                    'PublishedCourse',
                    'ClassRoom',
                    'ClassPeriod' => array(
                        'fields' => array(
                            'ClassPeriod.id',
                            'ClassPeriod.week_day',
                            'ClassPeriod.period_setting_id'
                        ),
                        'PeriodSetting' => array('fields' => array('PeriodSetting.hour'))
                    )
                )
            )
        );
        $potential_free_class_period = array();

        $assigned_week_day_hour = array();
        $unassigned_week_day_hour = array();
        $list_of_class_period_ids = array();
        $unassigned_list_of_class_period_ids = array();
        // get all scheduled time of the section.
        foreach ($get_all_schedules_periods as $scp => $scv) {
            foreach ($scv['ClassPeriod'] as $in => $value) {
                $assigned_week_day_hour[$scv['CourseSchedule']['id']][$value['week_day']][] = $value['PeriodSetting']['hour'];
                $list_of_class_period_ids[$value['week_day']][] = $value['id'];
            }
        }


        $not_assigned_class_periods_of_college_for_section = array();
        foreach ($list_of_class_period_ids as $week_day => $week_value) {
            $tmp = ClassRegistry::init('ClassPeriod')->
            find('list', array(
                'conditions' => array(
                    'NOT' => array(
                        'ClassPeriod.id' => $week_value
                    ),
                    'ClassPeriod.week_day' => $week_day,
                    'ClassPeriod.college_id' => $college_id,
                    'ClassPeriod.program_id' => $publish_courses['PublishedCourse']['program_id'],
                    'ClassPeriod.program_type_id' => $publish_courses['PublishedCourse']['program_type_id']
                ),
                'order' => 'ClassPeriod.week_day',
                'fields' => array('ClassPeriod.id', 'ClassPeriod.id')
            ));
            if (!empty($tmp)) {
                $not_assigned_class_periods_of_college_for_section[$week_day] = $tmp;
            }
        }

        $publishedCourse_details = $this->getPublishedCourseDetails($publish_courses['PublishedCourse']['id']);

        $number_of_period_per_session = $this->get_number_period_per_session(
            $publishedCourse_details['Course']['lecture_hours'],
            $publishedCourse_details['PublishedCourse']['lecture_number_of_session']
        );

        $number_period = 1;
        foreach ($number_of_period_per_session as $in => $vv) {
            if ($vv > $number_period) {
                $number_period = $vv;
            }
        }

        $continue_grouped_period_ids = array();
        foreach (
            $not_assigned_class_periods_of_college_for_section as $week => $not_assigned_class_periods
        ) {
            for ($i = $number_period; $i > 0; $i--) {
                $tmp = $this->
                get_continuous_grouped_class_period_list_into_period_number(
                    $not_assigned_class_periods,
                    $i
                );

                if (!empty($tmp)) {
                    $continue_grouped_period_ids[$week] = $tmp;
                    break 1;
                }
            }
        }

        //Sorted Assignable Potential class rooms
        $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms(
            $publishedCourse_details,
            $publish_courses['PublishedCourse']['program_id'],
            $publish_courses['PublishedCourse']['program_type_id'],
            $type,
            $college_id
        );

        //get free class room from sorted_potential_class_rooms in a given periods

        $match_periods_and_class_room = array();
        foreach ($continue_grouped_period_ids as $week_day => $week_value) {
            $match_periods_and_class_room[$week_day] = $this->get_match_periods_and_class_room(
                $week_value,
                $sorted_potential_class_rooms,
                $publish_courses['PublishedCourse']['academic_year'],
                $publish_courses['PublishedCourse']['semester']
            );
        }

        $reformated_class_room_class_period = array();
        $period_combo_ids = array();
        $period_dispaly_format = array();
        foreach ($match_periods_and_class_room as $week => $value) {
            $week_day_name = null;
            if ($week == 1) {
                $week_day_name = 'Sunday';
            } elseif ($week == 2) {
                $week_day_name = 'Monday';
            } elseif ($week == 3) {
                $week_day_name = 'Tuesday';
            } elseif ($week == 4) {
                $week_day_name = 'Wednesday';
            } elseif ($week == 5) {
                $week_day_name = 'Thursday';
            } elseif ($week == 6) {
                $week_day_name = 'Friday';
            } elseif ($week == 7) {
                $week_day_name = 'Saturday';
            }

            if (!empty($value['class_periods'])) {
                foreach ($value['class_periods'] as $key => $periods) {
                    $periodss = ClassRegistry::init('ClassPeriod')->find(
                        'all',
                        array(
                            'conditions' => array(
                                'ClassPeriod.id' => $periods,
                                'ClassPeriod.week_day' => $week
                            ),
                            'contain' => array('PeriodSetting')
                        )
                    );

                    foreach ($periodss as $in => $vv) {
                        /* $period_combo_ids[$week][$vv['ClassPeriod']['id']]= date("h:i A",strtotime(
                         $vv['PeriodSetting']['hour']));
                         */
                        $period_combo_ids[$week][$vv['ClassPeriod']['id']] = $vv['PeriodSetting']['hour'];
                        //$period_dispaly_format[$week][]=
                    }
                }
            }

            $period_ids = null;
            $period_dispaly_format = null;
            foreach ($value['class_periods'] as $key => $period_id) {
                $period = ClassRegistry::init('ClassPeriod')->find(
                    'first',
                    array(
                        'conditions' => array(
                            'ClassPeriod.id' => $period_id,
                            'ClassPeriod.week_day' => $week
                        ),
                        'contain' => array('PeriodSetting')
                    )
                );
                $period_ids .= $period_id . '~';
                $period_dispaly_format .= ' ' . date("h:i A", strtotime($period['PeriodSetting']['hour']));
            }
            $room = $this->ClassRoom->find('first', array(
                'conditions' => array(
                    'ClassRoom.id' =>
                        $value['class_room']
                ),
                'contain' => array('ClassRoomBlock' => array('Campus'))
            ));
            //$value['class_room'];
            $period_ids .= 'room' . $value['class_room'];
            $reformated_class_room_class_period[$week_day_name][$period_ids] = $room['ClassRoom']['room_code'] . '' . $room['ClassRoomBlock']['block_code'] . '(' . $room['ClassRoomBlock']['Campus']['name'] . ')' . ' time ' . $period_dispaly_format;
        }

        //debug($sorted_potential_class_rooms);
        $rooms = $this->ClassRoom->find('all', array(
            'conditions' => array(
                'ClassRoom.id' => array_keys($sorted_potential_class_rooms)
            ),
            'contain' => array('ClassRoomBlock' => array('Campus'))
        ));
        //debug($rooms);
        $room_list = array();
        foreach ($rooms as $ind => $vv) {
            $room_list[$vv['ClassRoomBlock']['Campus']['name']][$vv['ClassRoom']['id']] = $vv['ClassRoom']['room_code'] . '' . $vv['ClassRoomBlock']['block_code'];
        }
        $new_array = array();
        $new_array['rooms'] = $room_list;
        // $new_array['period']=$reformated_class_room_class_period;
        $new_array['period'] = $this->returnPeriodWhichAreConsequetive($number_period, $period_combo_ids);
        //debug($period_combo_ids);
        //debug($period_dispaly_format);
        return $new_array;
    }

    /**
     *Ger Free Room in specified period
     */
    public function getFreePotentialClassRooms(
        $period_ids = null,
        $academic_year = null,
        $semester = null,
        $college_id = null,
        $type = null
    ) {

        $course_schedules = $this->CourseSchedulesClassPeriod->find('list', array(
            'conditions' => array(
                'CourseSchedulesClassPeriod.class_period_id' => $period_ids
            ),
            'fields' => array('course_schedule_id')
        ));
        $occupied_class_rooms = $this->find(
            'list',
            array(
                'conditions' => array(
                    'CourseSchedule.id' => $course_schedules,
                    'CourseSchedule.semester' => $semester,
                    'CourseSchedule.academic_year like' => $academic_year . '%',
                    'CourseSchedule.type' => $type
                ),
                'fields' => 'class_room_id'
            )
        );

        if (strcasecmp($type, 'Lecture') === 0) {
            $room_type = 'ClassRoom.available_for_lecture=1';
        } elseif (strcasecmp($course_schedules['CourseSchedule']['type'], 'Lab') === 0) {
            $room_type = 'ClassRoom.available_for_lecture=0 OR ClassRoom.available_for_exam=0';
        } elseif (strcasecmp($course_schedules['CourseSchedule']['type'], 'Tutorial') === 0) {
            $room_type = 'ClassRoom.available_for_lecture=0 OR ClassRoom.available_for_exam=0';
        }
        $class_rooms = $this->ClassRoom->find('all', array(
            'conditions' => array(
                "NOT" => array(
                    'ClassRoom.id' => $occupied_class_rooms
                ),
                $room_type,
                'ClassRoom.class_room_block_id IN (select id from class_room_blocks where college_id=' . $college_id . ' ) '
            ),
            'contain' => array(
                'ClassRoomBlock' => array('Campus')
            )
        ));
        $room_list = array();
        foreach ($class_rooms as $ind => $vv) {
            $room_list[$vv['ClassRoomBlock']['Campus']['name']][$vv['ClassRoom']['id']] = $vv['ClassRoom']['room_code'] . ' ' . $vv['ClassRoomBlock']['block_code'];
        }

        return $room_list;
    }

    public function isInstructorFreeOnSelectedPeriod($period_ids = null, $published_course_id = null)
    {

        //To find instructor already scheduled class periods from course scheduled
        $published_course_details = $this->PublishedCourse->find(
            'first',
            array(
                'conditions' => array('PublishedCourse.id' => $published_course_id),
                'contain' =>
                    array('CourseInstructorAssignment')
            )
        );
        if (!empty($published_course_details['CourseInstructorAssignment'])) {
            $similar_published_course_ids_of_sem_ac = $this->PublishedCourse->find(
                'list',
                array(
                    'conditions' => array(
                        'PublishedCourse.id <>' => $published_course_details['PublishedCourse']['id'],
                        'PublishedCourse.academic_year' => $published_course_details['PublishedCourse']['academic_year'],
                        'PublishedCourse.semester' => $published_course_details['PublishedCourse']['semester']
                    ),
                    'fields' => array('PublishedCourse.id')
                )
            );
            // list of published courses instructore is assigned.
            $instructor_assigned_published_courses = $this->PublishedCourse->CourseInstructorAssignment->
            find('list', array(
                'conditions' => array(
                    'CourseInstructorAssignment.published_course_id'
                    => $similar_published_course_ids_of_sem_ac,
                    'CourseInstructorAssignment.staff_id' =>
                        $published_course_details['CourseInstructorAssignment'][0]['staff_id']
                ),
                'fields' => array('CourseInstructorAssignment.published_course_id')
            ));

            $course_schedule_ids = $this->find(
                'list',
                array(
                    'fields' => array('CourseSchedule.id', 'CourseSchedule.id'),
                    'conditions' => array('CourseSchedule.published_course_id' => $instructor_assigned_published_courses)
                )
            );

            $occupied_instructor_class_period_from_schedule = $this->CourseSchedulesClassPeriod->
            find('list', array(
                'fields' => array(
                    'CourseSchedulesClassPeriod.class_period_id',
                    'CourseSchedulesClassPeriod.class_period_id'
                ),
                'conditions' => array('CourseSchedulesClassPeriod.course_schedule_id' => $course_schedule_ids)
            ));

            $count = 0;
            $period_count = count($period_ids);
            $occpied = false;
            foreach ($period_ids as $inn => $innv) {
                if (
                    in_array(
                        $innv,
                        $occupied_instructor_class_period_from_schedule
                    )
                ) {
                    $occpied = true;
                }
            }
            if ($occpied) {
                return 1;
            } else {
                return 2;
            }
        }
        return 3;
    }
}

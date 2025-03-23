<?php
namespace App\Controller;

use Cake\Event\Event;

class CourseSchedulesController extends AppController
{

    public $name = 'CourseSchedules';
    public $menuOptions = array(
        'exclude' => array('*'),
        'controllerButton' => false,
    );

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);

        $this->Auth->allow(
            'get_year_levels',
            'get_modal',
            'get_departments',
            'unschedule_courses_possible_causes',
            'manual_schedule_unscheduled',
            'change_schedule',
            'change_schedule',
            'manual_schedule_unscheduled',
            'isInstructorFreeOccupied',
            'instructor_free_occupied',
            'get_potential_class_rooms_combo'
        );
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyearArray();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
        unset($this->request->data['User']['password']);
    }

    public function index()
    {

//$this->CourseSchedule->recursive = 0;
//$this->set('courseSchedules', $this->paginate());
        if ($this->Session->read('selected_academic_year')) {
            $this->Session->delete('selected_academic_year');
        }
        if ($this->Session->read('selected_program')) {
            $this->Session->delete('selected_program');
        }
        if ($this->Session->read('selected_program_type')) {
            $this->Session->delete('selected_program_type');
        }
        if ($this->Session->read('selected_department')) {
            $this->Session->delete('selected_department');
        }
        if ($this->Session->read('selected_year_level')) {
            $this->Session->delete('selected_year_level');
        }
        if ($this->Session->read('selected_semester')) {
            $this->Session->delete('selected_semester');
        }
        if ($this->Session->read('selected_college')) {
            $this->Session->delete('selected_college');
        }
        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['CourseSchedule']['academic_year']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select academic year that you want to view course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseSchedule']['program_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select program that you want to view course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseSchedule']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select program type that you want to view course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseSchedule']['department_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select department that you want to view course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseSchedule']['year_level_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select Year Level that you want to view course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseSchedule']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select semester that you want to view course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                default:
                    $everythingfine = true;
            }
            if ($everythingfine) {
                $selected_academic_year = $this->request->data['CourseSchedule']['academic_year'];
                $this->Session->write('selected_academic_year', $selected_academic_year);
                $selected_program = $this->request->data['CourseSchedule']['program_id'];
                $this->Session->write('selected_program', $selected_program);
                $selected_program_type = $this->request->data['CourseSchedule']['program_type_id'];
                $this->Session->write('selected_program_type', $selected_program_type);
                $selected_department = $this->request->data['CourseSchedule']['department_id'];
                $this->Session->write('selected_department', $selected_department);
                $selected_year_level = $this->request->data['CourseSchedule']['year_level_id'];
                $this->Session->write('selected_year_level', $selected_year_level);
                $selected_semester = $this->request->data['CourseSchedule']['semester'];
                $this->Session->write('selected_semester', $selected_semester);
                $selected_college = $this->request->data['CourseSchedule']['college_id'];
                $this->Session->write('selected_college', $selected_college);

                $yearLevels = $this->_get_year_levels_list($selected_department);


//get sorted published courses of selected criteria.
                if ($selected_department == "pre") {
                    $conditions = array(
                        'PublishedCourse.academic_year' => $selected_academic_year,
                        'PublishedCourse.program_id' => $selected_program,
                        'PublishedCourse.program_type_id' => $selected_program_type,
                        'PublishedCourse.college_id' => $this->college_id,
                        'PublishedCourse.semester' => $selected_semester,
                        'PublishedCourse.drop' => 0,
                        "OR" => array(
                            "PublishedCourse.department_id is null",
                            "PublishedCourse.department_id" => array(0, '')
                        )
                    );
                } else {
                    $conditions = array(
                        'PublishedCourse.academic_year' => $selected_academic_year,
                        'PublishedCourse.program_id' => $selected_program,
                        'PublishedCourse.program_type_id' => $selected_program_type,
                        'PublishedCourse.department_id' => $selected_department,
                        'PublishedCourse.year_level_id' => $selected_year_level,
                        'PublishedCourse.semester' => $selected_semester,
                        'PublishedCourse.drop' => 0
                    );
                }
//Get sections that have course schedule in the selected criteria. If there is at least one section deny generation of new course schedule
                $sections = $this->CourseSchedule->get_course_schedule_sections(
                    $conditions,
                    $selected_academic_year,
                    $selected_semester
                );
                if (!empty($sections)) {
                    $this->set(compact('sections'));
                    $this->Session->write('sections', $sections);
                } else {
                    $this->Session->setFlash(
                        '<span></span> ' . __('There is no course scheduled section in the selected criteria. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['view'])) {
            $selected_section = $this->request->data['CourseSchedule']['section_id'];
//$this->Session->write('selected_section',$section_id);
            if ($this->Session->read('sections')) {
                $sections = $this->Session->read('sections');
            }
            $section_course_schedule = array();
            if (!empty($selected_section) && $selected_section != "pre") {
                $section_course_schedule[$selected_section] = $this->CourseSchedule->get_section_course_schedule(
                    $selected_section,
                    $this->request->data['CourseSchedule']['academic_year'],
                    $this->request->data['CourseSchedule']['semester']
                );
            } else {
                if ($selected_section == "pre") {
                    foreach ($sections as $sectionValue) {
                        $section_course_schedule[$sectionValue['id']] = $this->CourseSchedule->get_section_course_schedule(
                            $sectionValue['id'],
                            $this->request->data['CourseSchedule']['academic_year'],
                            $this->request->data['CourseSchedule']['semester']
                        );
                    }
                }
            }
            $starting_and_ending_hour = $this->CourseSchedule->get_starting_and_ending_hour(
                $this->college_id,
                $this->request->data['CourseSchedule']['program_id'],
                $this->request->data['CourseSchedule']['program_type_id']
            );
            $this->set(compact('sections', 'selected_section', 'section_course_schedule', 'starting_and_ending_hour'));
        }

        if ($this->Session->read('selected_academic_year')) {
            $selected_academic_year = $this->Session->read('selected_academic_year');
        } else {
            if (!empty($this->request->data['CourseSchedule']['academic_year'])) {
                $selected_academic_year = $this->request->data['CourseSchedule']['academic_year'];
            }
        }
        if ($this->Session->read('selected_program')) {
            $selected_program = $this->Session->read('selected_program');
        } else {
            if (!empty($this->request->data['CourseSchedule']['program_id'])) {
                $selected_program = $this->request->data['CourseSchedule']['program_id'];
            }
        }
        if ($this->Session->read('selected_program_type')) {
            $selected_program_type = $this->Session->read('selected_program_type');
        } else {
            if (!empty($this->request->data['CourseSchedule']['program_type_id'])) {
                $selected_program_type = $this->request->data['CourseSchedule']['program_type_id'];
            }
        }
        if ($this->Session->read('selected_department')) {
            $selected_department = $this->Session->read('selected_department');
        } else {
            if (isset($this->request->data['CourseSchedule']['department_id'])) {
                $selected_department = $this->request->data['CourseSchedule']['department_id'];
            }
        }
        if ($this->Session->read('selected_year_level')) {
            $selected_year_level = $this->Session->read('selected_year_level');
        } else {
            if (!empty($this->request->data['CourseSchedule']['year_level_id'])) {
                $selected_year_level = $this->request->data['CourseSchedule']['year_level_id'];
            }
        }
        if ($this->Session->read('selected_semester')) {
            $selected_semester = $this->Session->read('selected_semester');
        } else {
            if (!empty($this->request->data['CourseSchedule']['semester'])) {
                $selected_semester = $this->request->data['CourseSchedule']['semester'];
            }
        }
        $selected_college = null;
        if ($this->Session->read('selected_college')) {
            $selected_college = $this->Session->read('selected_college');
        } else {
            if (!empty($this->request->data['CourseSchedule']['college_id'])) {
                $selected_college = $this->request->data['CourseSchedule']['college_id'];
            }
        }

        if (empty($selected_college)) {
            if ($this->role_id == ROLE_COLLEGE) {
                $selected_college = $this->college_id;
            } else {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    if (empty($selected_department)) {
                        $selected_department = $this->department_id;
                    }
                    $selected_college = $this->CourseSchedule->PublishedCourse->Department->field(
                        'Department.college_id',
                        array('Department.id' => $selected_department)
                    );
                }
            }
        }
        if (!empty($selected_college)) {
            $departments = $this->CourseSchedule->PublishedCourse->Department->find(
                'list',
                array('conditions' => array('Department.college_id' => $selected_college))
            );
            $departments['pre'] = 'Pre/(Unassign Freshman)';
        } else {
            $departments = null;
        }
        if (!empty($selected_department)) {
            $yearLevels = $this->_get_year_levels_list($selected_department);
        } else {
            $yearLevels = null;
        }
        $colleges = $this->CourseSchedule->PublishedCourse->College->find('list');
        $programs = $this->CourseSchedule->PublishedCourse->Program->find('list');
        $programTypes = $this->CourseSchedule->PublishedCourse->ProgramType->find('list');

        $this->set(
            compact(
                'colleges',
                'departments',
                'programs',
                'programTypes',
                'yearLevels',
                'selected_academic_year',
                'selected_program',
                'selected_program_type',
                'selected_department',
                'selected_year_level',
                'selected_semester',
                'selected_college'
            )
        );
    }

    public function cancel_auto_generated_schedule()
    {

        if (!empty($this->request->data) && !empty($this->request->data['search'])) {
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['Search']['academic_year']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select academic year that you want to cancel  schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['program_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select program that you want to cancel course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select program type that you want cancel course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['department_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select department that you want cancel course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['year_level_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select Year Level that you want cancel course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select semester that you want to cancel course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                default:
                    $everythingfine = true;
            }
            if ($everythingfine) {
                $yearLevels = $this->_get_year_levels_list($this->request->data['Search']['department_id']);
                if ($this->request->data['Search']['department_id'] == "pre") {
                    $conditions = array(
                        'PublishedCourse.academic_year' => $this->request->data['Search']['academic_year'],
                        'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                        'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id'],
                        'PublishedCourse.college_id' => $this->college_id,
                        'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                        'PublishedCourse.drop' => 0,
                        "OR" => array(
                            "PublishedCourse.department_id is null",
                            "PublishedCourse.department_id" => array(0, '')
                        )
                    );
                } else {
                    $conditions = array(
                        'PublishedCourse.academic_year' => $this->request->data['Search']['academic_year'],
                        'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                        'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id'],
                        'PublishedCourse.department_id' => $this->request->data['Search']['department_id'],
                        'PublishedCourse.year_level_id' => $this->request->data['Search']['year_level_id'],
                        'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                        'PublishedCourse.drop' => 0
                    );
                }
//Get published course ids that have course schedule in the selected criteria.
                $published_course_ids = $this->CourseSchedule->PublishedCourse->find(
                    'list',
                    array('conditions' => $conditions, 'fields' => array('PublishedCourse.id', 'PublishedCourse.id'))
                );
                $unschedule_published_courses = $this->CourseSchedule->PublishedCourse->UnschedulePublishedCourse->find(
                    'all',
                    array(
                        'conditions' => array('UnschedulePublishedCourse.published_course_id' => $published_course_ids),
                        'contain' => array('PublishedCourse' => array('Course'))
                    )
                );
                $sections = $this->CourseSchedule->get_course_schedule_sections(
                    $conditions,
                    $this->request->data['Search']['academic_year'],
                    $this->request->data['Search']['semester']
                );
                if (!empty($sections)) {
                    $section_course_schedule = array();
                    foreach ($sections as $sectionValue) {
                        $section_course_schedule[$sectionValue['id']] = $this->CourseSchedule->get_section_course_schedule(
                            $sectionValue['id'],
                            $this->request->data['Search']['academic_year'],
                            $this->request->data['Search']['semester']
                        );
                    }
                    $section_unscheduled_courses = array();
                    foreach ($unschedule_published_courses as $inn => $unscheduled_value) {
                        $section_unscheduled_courses[$unscheduled_value['PublishedCourse']['section_id']][] = $unscheduled_value;
                    }
                    $starting_and_ending_hour = $this->CourseSchedule->get_starting_and_ending_hour(
                        $this->college_id,
                        $this->request->data['Search']['program_id'],
                        $this->request->data['Search']['program_type_id']
                    );

                    $this->set(compact('section_course_schedule', 'starting_and_ending_hour'));
                    //Get all course schedule id in the selected criteria
                    $scheduled_courses = $this->CourseSchedule->find('all', array(
                        'conditions' => array(
                            'CourseSchedule.published_course_id' => $published_course_ids,
                            'CourseSchedule.academic_year' => $this->request->data['Search']['academic_year']
                        ,
                            'CourseSchedule.semester' => $this->request->data['Search']['semester']
                        ),
                        'contain' => array('PublishedCourse' => array('Course'), 'ClassRoom', 'Section')
                    ));
                    $this->set(compact('scheduled_courses', 'section_unscheduled_courses'));

                    $this->set(compact('yearLevels'));
                } else {
                    $this->Session->setFlash(
                        '<span></span> ' . __('There is no schedule in the selected criteria that needs cancel.'),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                }
            }
        }

        if (!empty($this->request->data) && !empty($this->request->data['cancel'])) {
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['Search']['academic_year']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select academic year that you want to cancel  schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['program_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select program that you want to cancel course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select program type that you want cancel course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['department_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select department that you want cancel course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['year_level_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select Year Level that you want cancel course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select semester that you want to cancel course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                default:
                    $everythingfine = true;
            }
            if ($everythingfine) {
                $yearLevels = $this->_get_year_levels_list($this->request->data['Search']['department_id']);
                if ($this->request->data['Search']['department_id'] == "pre") {
                    $conditions = array(
                        'PublishedCourse.academic_year' => $this->request->data['Search']['academic_year'],
                        'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                        'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id'],
                        'PublishedCourse.college_id' => $this->college_id,
                        'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                        'PublishedCourse.drop' => 0,
                        "OR" => array(
                            "PublishedCourse.department_id is null",
                            "PublishedCourse.department_id" => array(0, '')
                        )
                    );
                } else {
                    $conditions = array(
                        'PublishedCourse.academic_year' =>
                            $this->request->data['Search']['academic_year'],
                        'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                        'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id'],
                        'PublishedCourse.department_id' =>
                            $this->request->data['Search']['department_id'],
                        'PublishedCourse.year_level_id' => $this->request->data['Search']['year_level_id'],
                        'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                        'PublishedCourse.drop' => 0
                    );
                }
//Get published course ids that have course schedule in the selected criteria.
                $published_course_ids = $this->CourseSchedule->PublishedCourse->find(
                    'list',
                    array('conditions' => $conditions, 'fields' => array('PublishedCourse.id', 'PublishedCourse.id'))
                );
//Get all course schedule id in the selected criteria
                $course_schedule_ids = $this->CourseSchedule->find(
                    'list',
                    array(
                        'fields' => array('CourseSchedule.id', 'CourseSchedule.id'),
                        'conditions' => array(
                            'CourseSchedule.published_course_id' => $published_course_ids,
                            'CourseSchedule.academic_year' => $this->request->data['Search']['academic_year'],
                            'CourseSchedule.semester' => $this->request->data['Search']['semester']
                        )
                    )
                );
// during cancellation delete unschedule courses too.
                $unschedule_courses_ids = $this->CourseSchedule->PublishedCourse->UnschedulePublishedCourse->find(
                    'list',
                    array(
                        'conditions' => array(
                            'UnschedulePublishedCourse.published_course_id' =>
                                $published_course_ids
                        ),
                        'fields' => array('id')
                    )
                );


//Get all course schedule class period id of all fiven course schedule
                $course_schedule_class_period_ids = $this->CourseSchedule->CourseSchedulesClassPeriod->find(
                    'list',
                    array(
                        'fields' => array('CourseSchedulesClassPeriod.id', 'CourseSchedulesClassPeriod.id'),
                        'conditions' => array('CourseSchedulesClassPeriod.course_schedule_id' => $course_schedule_ids)
                    )
                );

// Check whether the course schedule used in attendance or not
                $course_schedule_published_course_ids = $this->CourseSchedule->find(
                    'list',
                    array(
                        'fields' => array('CourseSchedule.published_course_id', 'CourseSchedule.published_course_id'),
                        'conditions' => array('CourseSchedule.id' => $course_schedule_ids)
                    )
                );
                $is_the_course_schedule_used = $this->CourseSchedule->PublishedCourse->Attendance->is_course_schedule_uesd_in_attendance(
                    $course_schedule_published_course_ids
                );
                if (empty($is_the_course_schedule_used)) {
//Delete course schedule from course schedule class period habtm table
                    $this->CourseSchedule->CourseSchedulesClassPeriod->deleteAll(
                        array('CourseSchedulesClassPeriod.id' => $course_schedule_class_period_ids),
                        false
                    );

                    $this->CourseSchedule->PublishedCourse->UnschedulePublishedCourse->deleteAll(
                        array('UnschedulePublishedCourse.id' => $unschedule_courses_ids),
                        false
                    );
//Delete Course Schedule from course schedule table
                    $this->CourseSchedule->deleteAll(array('CourseSchedule.id' => $course_schedule_ids), false);
                    $this->Session->setFlash(
                        '<span></span> ' . __('The Course schedule successfully canceled.'),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                } else {
                    $this->Session->setFlash(
                        '<span></span> ' . __('You can not cancel this course schedule since it used in attendance.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
//$this->redirect(array('action'=>'generate'));
                }
            }
        }

        $departments = $this->CourseSchedule->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $this->college_id))
        );
        $departments['pre'] = 'Pre/(Unassign Freshman)';
        $programs = $this->CourseSchedule->PublishedCourse->Program->find('list');
        $programTypes = $this->CourseSchedule->PublishedCourse->ProgramType->find('list');

        $this->set(compact('departments', 'programs', 'programTypes', 'yearLevels'));
    }

    public function generate($is_from_cancel = null)
    {

        $unscheduled_courses_reformate_for_save = array();
        if (!empty($is_from_cancel)) {
            $this->request->data['generate'] = true;
            $this->request->data['CourseSchedule']['acadamic_year'] = $this->Session->read('selected_academic_year');
            $this->request->data['CourseSchedule']['program_id'] = $this->Session->read('selected_program');
            $this->request->data['CourseSchedule']['program_type_id'] = $this->Session->read('selected_program_type');
            $this->request->data['CourseSchedule']['department_id'] = $this->Session->read('selected_department');
            $this->request->data['CourseSchedule']['year_level_id'] = $this->Session->read('selected_year_level');
            $this->request->data['CourseSchedule']['semester'] = $this->Session->read('selected_semester');
        }
        if (!empty($this->request->data) && isset($this->request->data['generate'])) {
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['CourseSchedule']['acadamic_year']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select academic year that you want to generate course schedule. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseSchedule']['program_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select program that you want to generate course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseSchedule']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select program type that you want to generate course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseSchedule']['department_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select department that you want to generate course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseSchedule']['year_level_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select Year Level that you want to generate course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseSchedule']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select semester that you want to generate course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                default:
                    $everythingfine = true;
            }
            if ($everythingfine) {
                $selected_academic_year = $this->request->data['CourseSchedule']['acadamic_year'];
                $this->Session->write('selected_academic_year', $selected_academic_year);
                $selected_program = $this->request->data['CourseSchedule']['program_id'];
                $this->Session->write('selected_program', $selected_program);
                $selected_program_type = $this->request->data['CourseSchedule']['program_type_id'];
                $this->Session->write('selected_program_type', $selected_program_type);
                $selected_department = $this->request->data['CourseSchedule']['department_id'];
                $this->Session->write('selected_department', $selected_department);
                $selected_year_level = $this->request->data['CourseSchedule']['year_level_id'];
                $this->Session->write('selected_year_level', $selected_year_level);
                $selected_semester = $this->request->data['CourseSchedule']['semester'];
                $this->Session->write('selected_semester', $selected_semester);

                $yearLevels = $this->_get_year_levels_list($selected_department);

                $yearLevelIDS = $this->_GetCollegeDepartmentYearLevelIds(
                    array_values($selected_year_level),
                    $this->college_id
                );

//get sorted published courses of selected criteria.
                if ($selected_department == "pre") {
                    $conditions = array(
                        'PublishedCourse.academic_year' => $selected_academic_year,
                        'PublishedCourse.program_id' => $selected_program,
                        'PublishedCourse.program_type_id' => $selected_program_type,
                        'PublishedCourse.college_id' => $this->college_id,
                        'PublishedCourse.semester' => $selected_semester,
                        'PublishedCourse.drop' => 0,
                        "OR" => array(
                            "PublishedCourse.department_id is null",
                            "PublishedCourse.department_id" => array(0, '')
                        )
                    );
                } else {
                    $conditions = array(
                        'PublishedCourse.academic_year' => $selected_academic_year,
                        'PublishedCourse.program_id' => $selected_program,
                        'PublishedCourse.program_type_id' => $selected_program_type,
                        'PublishedCourse.department_id' => $selected_department,
                        'PublishedCourse.year_level_id' => $yearLevelIDS,
                        'PublishedCourse.semester' => $selected_semester,
                        'PublishedCourse.drop' => 0
                    );
                }
//Check whether there is define class period for selected college, program & program type
                $is_class_period_defined = $this->CourseSchedule->is_there_defined_class_period(
                    $this->college_id,
                    $selected_program,
                    $selected_program_type
                );

                if ($is_class_period_defined == true) {
//Check whether there is defined class room for selected college, program & program type
                    $is_class_room_defined = $this->CourseSchedule->is_there_defined_class_room(
                        $this->college_id,
                        $selected_program,
                        $selected_program_type
                    );
                    if ($is_class_room_defined == true) {
//Get sections that have course schedule in the selected criteria. If there is at least one section deny generation of new course schedule
                        $sections = $this->CourseSchedule->get_course_schedule_sections(
                            $conditions,
                            $selected_academic_year,
                            $selected_semester
                        );

                        if (empty($sections)) {
                            $sorted_publishedCourses = $this->CourseSchedule->get_sorted_published_courses($conditions);
//debug($sorted_publishedCourses);
                            if (!empty($sorted_publishedCourses)) {
//used to store un assignment failed published course
                                $unschedule_published_course = array();
                                $unscheduled_count = 0;
//$unschedule_published_course_details = null;
                                /**************************************
                                 *Schedule for all lecture period
                                 ***************************************/
                                foreach ($sorted_publishedCourses as $publishedCourse_id => $weight_value) {
//get publishedcourse details
                                    $publishedCourse_details = $this->CourseSchedule->get_published_course_details(
                                        $publishedCourse_id
                                    );
//debug($publishedCourse_details);
//schedule for lecture hours
                                    if (!empty($publishedCourse_details['Course']['lecture_hours'])) {
//if the section is split for the given published course and lecture course type

                                        if (isset($publishedCourse_details['SectionSplitForPublishedCourse'][0]['type']) && ((strcasecmp(
                                                        $publishedCourse_details['SectionSplitForPublishedCourse'][0]['type'],
                                                        'Lecture'
                                                    ) == 0) ||
                                                (strcasecmp(
                                                        $publishedCourse_details['SectionSplitForPublishedCourse'][0]['type'],
                                                        "Lecture+Tutorial"
                                                    ) == 0) ||
                                                (strcasecmp(
                                                        $publishedCourse_details['SectionSplitForPublishedCourse'][0]['type'],
                                                        "Lecture+Lab"
                                                    ) == 0))) {
//schedule for each splited sections
                                            foreach ($publishedCourse_details['SectionSplitForPublishedCourse'][0]['CourseSplitSection'] as $split_section) {
//get number of session and its period number
                                                $number_of_period_per_session = $this->CourseSchedule->get_number_period_per_session(
                                                    $publishedCourse_details['Course']['lecture_hours'],
                                                    $publishedCourse_details['PublishedCourse']['lecture_number_of_session']
                                                );
//schedule for each session
                                                foreach ($number_of_period_per_session as $npsk => $period_number) {
                                                    $potential_class_period_ids_from_constranints = array();
                                                    if (!empty($publishedCourse_details['ClassPeriodCourseConstraint'])) {
                                                        //find class period ids from class period course constranits that specified as assigned
                                                        foreach ($publishedCourse_details['ClassPeriodCourseConstraint'] as $classPeriodCourseConstraint) {
                                                            if ($classPeriodCourseConstraint['active'] == 1 && strcasecmp(
                                                                    $classPeriodCourseConstraint['type'],
                                                                    "Lecture"
                                                                ) == 0) {
                                                                $potential_class_period_ids_from_constranints[] = $classPeriodCourseConstraint['class_period_id'];
                                                            }
                                                        }
                                                    }
                                                    if (!empty($potential_class_period_ids_from_constranints)) { // if the course has a defined class period constrain the below algorith will be executed.
                                                        //Get Sorted potentialy assignable class rooms
                                                        $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms(
                                                            $publishedCourse_details,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            "Lecture"
                                                        );
                                                        //get a group of continuous class_period_lists per period number of hours take the class period sepcified in class period course constraints as starting period.
                                                        $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number_from_constraint(
                                                            $period_number,
                                                            $potential_class_period_ids_from_constranints,
                                                            $publishedCourse_id,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            $selected_academic_year,
                                                            $selected_semester,
                                                            $publishedCourse_details['Section']['id'],
                                                            "Lecture"
                                                        );
                                                        //Get ready to assign, Matched class room (from sorted potential class rooms) and continuous grouped class periods (from the class period sepcified in class period course constraints)
                                                        $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                            $continuous_grouped_class_period_list_into_period_number,
                                                            $sorted_potential_class_rooms,
                                                            $selected_academic_year,
                                                            $selected_semester
                                                        );


                                                        if (!empty($match_periods_and_class_room)) {
                                                            if (isset($schedule)) {
                                                                unset($schedule);
                                                            }
                                                            $schedule['class_room_id'] = $match_periods_and_class_room['class_room'];
                                                            $schedule['section_id'] = $publishedCourse_details['Section']['id'];
                                                            $schedule['course_split_section_id'] = $split_section['id'];
                                                            $schedule['published_course_id'] = $publishedCourse_id;
                                                            $schedule['academic_year'] = $selected_academic_year;
                                                            $schedule['semester'] = $selected_semester;
                                                            $schedule['type'] = "Lecture";
                                                            $this->CourseSchedule->create();
                                                            $this->CourseSchedule->save($schedule);
                                                            //get saved course schedule id to save course id and class period id in associate table
                                                            $associate['course_schedule_id'] = $this->CourseSchedule->id;
                                                            foreach ($match_periods_and_class_room['class_periods'] as $class_period_id) {
                                                                $associate['class_period_id'] = $class_period_id;
                                                                $this->CourseSchedule->CourseSchedulesClassPeriod->create(
                                                                );
                                                                $this->CourseSchedule->CourseSchedulesClassPeriod->save(
                                                                    $associate
                                                                );
                                                            }
                                                        } else {
                                                            //$unschedule_published_course[$publishedCourse_details['Section']['id']][$split_section['id']]['Lecture'][$period_number][$publishedCourse_id][] = "dueto unable to find free class room and instructor sutable time in specified class period course constraints time";
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_title'] = $publishedCourse_details['Course']['course_title'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_code'] = $publishedCourse_details['Course']['course_code'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['credit'] = $publishedCourse_details['Course']['credit'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['lecture_hours'] = $publishedCourse_details['Course']['lecture_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['tutorial_hours'] = $publishedCourse_details['Course']['tutorial_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['laboratory_hours'] = $publishedCourse_details['Course']['laboratory_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['section_name'] = $publishedCourse_details['Section']['name'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_length'] = $period_number;
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_type'] = "Lecture";
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['split_section_name'] = $this->CourseSchedule->CourseSplitSection->field(
                                                                'CourseSplitSection.section_name',
                                                                array('CourseSplitSection.id' => $split_section['id'])
                                                            );
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['possible_reason'] = "Due to unable to find free class room and instructor suitable time in specified class period course constraint time.";
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['published_course_id'] = $publishedCourse_id;
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['course_split_section_id'] = $split_section['id'];
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['period_length'] = $period_number;
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['type'] = "Lecture";
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['description'] = "Due to unable to find free class room and instructor suitable time in specified class period course constraint time.";
                                                            $unscheduled_count++;
                                                        }
                                                    } else {
                                                        //get section last assigned week day in selected academic year and semester
                                                        $last_assigned_week_day = $this->CourseSchedule->get_last_assigned_week_day(
                                                            $publishedCourse_details['Section']['id'],
                                                            $selected_academic_year,
                                                            $selected_semester
                                                        );
                                                        if (!empty($last_assigned_week_day)) {
                                                            $potential_week_day = $this->CourseSchedule->get_next_week_day_from_last_assigned_week_day(
                                                                $last_assigned_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type
                                                            );
                                                        } else {
                                                            //if last_assigned_week_day is empty it mean the published course is the first for assignment
                                                            //find and take the first week day assign to this college, program & program type
                                                            $potential_week_day = $this->CourseSchedule->get_first_week_day(
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type
                                                            );
                                                        }
                                                        //Get list of class period of this week day,college,program and program type
                                                        $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                            $potential_week_day,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            $publishedCourse_id,
                                                            $selected_academic_year,
                                                            $selected_semester,
                                                            $publishedCourse_details['Section']['id'],
                                                            "Lecture"
                                                        );
                                                        //If list of class period is empty in the given week day try the next week day class period (this function try 7 time just to prevent infinite loop)
                                                        $loop = 1;
                                                        $next_week_day = $potential_week_day;
                                                        while (empty($list_of_class_period_ids) && $loop < 7) {
                                                            $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                $next_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type
                                                            );
                                                            $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                $next_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type,
                                                                $publishedCourse_id,
                                                                $selected_academic_year,
                                                                $selected_semester,
                                                                $publishedCourse_details['Section']['id'],
                                                                "Lecture"
                                                            );
                                                            $loop++;
                                                        }
                                                        //get a group of continuous class_period_lists per period number of hours.
                                                        $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number(
                                                            $list_of_class_period_ids,
                                                            $period_number
                                                        );
                                                        //Get Sorted potentialy assignable class rooms
                                                        $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms(
                                                            $publishedCourse_details,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            "Lecture"
                                                        );
                                                        //get free class room from sorted_potential_class_rooms in a given periods
                                                        $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                            $continuous_grouped_class_period_list_into_period_number,
                                                            $sorted_potential_class_rooms,
                                                            $selected_academic_year,
                                                            $selected_semester
                                                        );
                                                        $is_loop = true;
                                                        $loop_count = 1;
                                                        $is_save = false;
                                                        do {
                                                            if (!empty($match_periods_and_class_room)) {
                                                                if (isset($schedule)) {
                                                                    unset($schedule);
                                                                }
                                                                $schedule['class_room_id'] = $match_periods_and_class_room['class_room'];
                                                                $schedule['section_id'] = $publishedCourse_details['Section']['id'];
                                                                $schedule['course_split_section_id'] = $split_section['id'];
                                                                $schedule['published_course_id'] = $publishedCourse_id;
                                                                $schedule['academic_year'] = $selected_academic_year;
                                                                $schedule['semester'] = $selected_semester;
                                                                $schedule['type'] = "Lecture";
                                                                $this->CourseSchedule->create();
                                                                $this->CourseSchedule->save($schedule);
                                                                //get saved course schedule id to save course id and class period id in associate table
                                                                $associate['course_schedule_id'] = $this->CourseSchedule->id;
                                                                foreach ($match_periods_and_class_room['class_periods'] as $class_period_id) {
                                                                    $associate['class_period_id'] = $class_period_id;
                                                                    $this->CourseSchedule->CourseSchedulesClassPeriod->create(
                                                                    );
                                                                    $this->CourseSchedule->CourseSchedulesClassPeriod->save(
                                                                        $associate
                                                                    );
                                                                }
                                                                $is_save = true;
                                                                $is_loop = false;
                                                            } else {
                                                                //Increment the week day  by one and try again
                                                                $is_loop = true;
                                                                $loop_count++;
                                                                //find the next week day assign to this college, program & program type
                                                                $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                    $next_week_day,
                                                                    $this->college_id,
                                                                    $selected_program,
                                                                    $selected_program_type
                                                                );
                                                                //Get list of class period of this week day,college,program and program type
                                                                $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                    $next_week_day,
                                                                    $this->college_id,
                                                                    $selected_program,
                                                                    $selected_program_type,
                                                                    $publishedCourse_id,
                                                                    $selected_academic_year,
                                                                    $selected_semester,
                                                                    $publishedCourse_details['Section']['id'],
                                                                    "Lecture"
                                                                );
                                                                //If list of class period is empty in the given week day try the next week day class period (this function try 7 time just to prevent infinite loop)
                                                                $loop = 1;
                                                                while (empty($list_of_class_period_ids) && $loop < 7) {
                                                                    $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                        $next_week_day,
                                                                        $this->college_id,
                                                                        $selected_program,
                                                                        $selected_program_type
                                                                    );
                                                                    $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                        $next_week_day,
                                                                        $this->college_id,
                                                                        $selected_program,
                                                                        $selected_program_type,
                                                                        $publishedCourse_id,
                                                                        $selected_academic_year,
                                                                        $selected_semester,
                                                                        $publishedCourse_details['Section']['id'],
                                                                        "Lecture"
                                                                    );
                                                                    $loop++;
                                                                }
//get a group of continuous class_period_lists per period number of hours
                                                                $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number(
                                                                    $list_of_class_period_ids,
                                                                    $period_number
                                                                );
                                                                //get free class room from sorted_potential_class_rooms in a given periods
                                                                $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                                    $continuous_grouped_class_period_list_into_period_number,
                                                                    $sorted_potential_class_rooms,
                                                                    $selected_academic_year,
                                                                    $selected_semester
                                                                );
                                                            }
                                                        } while ($is_loop == true && $loop_count < 7);
                                                        //store assignment failed courses per period number and course type for display.
                                                        if ($is_save == false) {
                                                            //$unschedule_published_course[$period_number]['Lecture'][] = $publishedCourse_id;
                                                            //$unschedule_published_course[$publishedCourse_details['Section']['id']][$split_section['id']]['Lecture'][$period_number][$publishedCourse_id][] = "dueto unable to find free class period and instructor sutable time in specified class room or in availabel class room";
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_title'] = $publishedCourse_details['Course']['course_title'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_code'] = $publishedCourse_details['Course']['course_code'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['credit'] = $publishedCourse_details['Course']['credit'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['lecture_hours'] = $publishedCourse_details['Course']['lecture_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['tutorial_hours'] = $publishedCourse_details['Course']['tutorial_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['laboratory_hours'] = $publishedCourse_details['Course']['laboratory_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['section_name'] = $publishedCourse_details['Section']['name'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_length'] = $period_number;
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_type'] = "Lecture";
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['split_section_name'] = $this->CourseSchedule->CourseSplitSection->field(
                                                                'CourseSplitSection.section_name',
                                                                array('CourseSplitSection.id' => $split_section['id'])
                                                            );
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['possible_reason'] = "Due to unable to find free class periods and instructor suitable time in specified class room or in availabel class room";
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['published_course_id'] = $publishedCourse_id;
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['course_split_section_id'] = $split_section['id'];
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['period_length'] = $period_number;
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['type'] = "Lecture";
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['description'] = "Due to unable to find free class periods and instructor suitable time in specified class room or in availabel class room";
                                                            $unscheduled_count++;
                                                        }
                                                    }
                                                }
                                            } //end of froeach split section for lecture
                                        } else { //end of if seplit section for lecture
//If the section is not split for the given course
//get number of session and its period number

                                            $number_of_period_per_session =
                                                $this->CourseSchedule->get_number_period_per_session(
                                                    $publishedCourse_details['Course']['lecture_hours'],
                                                    $publishedCourse_details['PublishedCourse']['lecture_number_of_session']
                                                );

//schedule for each session
                                            foreach ($number_of_period_per_session as $npsk => $period_number) {
                                                $potential_class_period_ids_from_constranints = array();
                                                if (!empty($publishedCourse_details['ClassPeriodCourseConstraint'])) {
//find class period ids from class period course constranits that specified as assigned
                                                    foreach ($publishedCourse_details['ClassPeriodCourseConstraint'] as $classPeriodCourseConstraint) {
                                                        if ($classPeriodCourseConstraint['active'] == 1 && strcasecmp(
                                                                $classPeriodCourseConstraint['type'],
                                                                "Lecture"
                                                            ) == 0) {
                                                            $potential_class_period_ids_from_constranints[] = $classPeriodCourseConstraint['class_period_id'];
                                                        }
                                                    }
                                                }
                                                if (!empty($potential_class_period_ids_from_constranints)) {
                                                    //Get Sorted potentialy assignable class rooms
                                                    $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms(
                                                        $publishedCourse_details,
                                                        $selected_program,
                                                        $selected_program_type,
                                                        "Lecture"
                                                    );
                                                    //get a group of continuous class_period_lists per period number of hours take the class period sepcified in class period course constraints as starting period.
                                                    $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number_from_constraint(
                                                        $period_number,
                                                        $potential_class_period_ids_from_constranints,
                                                        $publishedCourse_id,
                                                        $this->college_id,
                                                        $selected_program,
                                                        $selected_program_type,
                                                        $selected_academic_year,
                                                        $selected_semester,
                                                        $publishedCourse_details['Section']['id'],
                                                        "lecture"
                                                    );
                                                    //Get ready to assign, Matched class room (from sorted potential class rooms) and continuous grouped class periods (from the class period sepcified in class period course constraints)
                                                    $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                        $continuous_grouped_class_period_list_into_period_number,
                                                        $sorted_potential_class_rooms,
                                                        $selected_academic_year,
                                                        $selected_semester
                                                    );

                                                    if (!empty($match_periods_and_class_room)) {
                                                        if (isset($schedule)) {
                                                            unset($schedule);
                                                        }
                                                        $schedule['class_room_id'] = $match_periods_and_class_room['class_room'];
                                                        $schedule['section_id'] = $publishedCourse_details['Section']['id'];
                                                        $schedule['published_course_id'] = $publishedCourse_id;
                                                        $schedule['academic_year'] = $selected_academic_year;
                                                        $schedule['semester'] = $selected_semester;
                                                        $schedule['type'] = "Lecture";
                                                        $this->CourseSchedule->create();
                                                        $this->CourseSchedule->save($schedule);
                                                        //get saved course schedule id to save course id and class period id in associate table
                                                        $associate['course_schedule_id'] = $this->CourseSchedule->id;
                                                        foreach ($match_periods_and_class_room['class_periods'] as $class_period_id) {
                                                            $associate['class_period_id'] = $class_period_id;
                                                            $this->CourseSchedule->CourseSchedulesClassPeriod->create();
                                                            $this->CourseSchedule->CourseSchedulesClassPeriod->save(
                                                                $associate
                                                            );
                                                        }
                                                    } else {
                                                        //$unschedule_published_course[$publishedCourse_details['Section']['id']]['Lecture'][$period_number][$publishedCourse_id][] = "dueto unable to find free class room and instructor sutable time in specified class period course constraints time";
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_title'] = $publishedCourse_details['Course']['course_title'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_code'] = $publishedCourse_details['Course']['course_code'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['credit'] = $publishedCourse_details['Course']['credit'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['lecture_hours'] = $publishedCourse_details['Course']['lecture_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['tutorial_hours'] = $publishedCourse_details['Course']['tutorial_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['laboratory_hours'] = $publishedCourse_details['Course']['laboratory_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['section_name'] = $publishedCourse_details['Section']['name'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_length'] = $period_number;
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_type'] = "Lecture";
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['possible_reason'] = "Due to unable to find free class room and instructor suitable time in specified class period course constraint time";
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['published_course_id'] = $publishedCourse_id;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['course_split_section_id'] = null;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['period_length'] = $period_number;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['type'] = "Lecture";
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['description'] = "Due to unable to find free class room and instructor suitable time in specified class period course constraint time";
                                                        $unscheduled_count++;
                                                    }
                                                } else {
//get section last assigned week day in selected academic year and semester
                                                    $last_assigned_week_day = $this->CourseSchedule->get_last_assigned_week_day(
                                                        $publishedCourse_details['Section']['id'],
                                                        $selected_academic_year,
                                                        $selected_semester
                                                    );
                                                    if (!empty($last_assigned_week_day)) {
                                                        $potential_week_day = $this->CourseSchedule->get_next_week_day_from_last_assigned_week_day(
                                                            $last_assigned_week_day,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type
                                                        );
                                                    } else {
                                                        //if last_assigned_week_day is empty it mean the published course is the first for assignment
                                                        //find take the first week day assign to this college, program & program type
                                                        $potential_week_day = $this->CourseSchedule->get_first_week_day(
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type
                                                        );
                                                    }
                                                    //Get list of class period of this week day,college,program and program type
                                                    $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                        $potential_week_day,
                                                        $this->college_id,
                                                        $selected_program,
                                                        $selected_program_type,
                                                        $publishedCourse_id,
                                                        $selected_academic_year,
                                                        $selected_semester,
                                                        $publishedCourse_details['Section']['id'],
                                                        "Lecture"
                                                    );
                                                    //If list of class period is empty in the given week day try the next week day class period (this function try 7 time just to prevent infinite loop)
                                                    $loop = 1;
                                                    $next_week_day = $potential_week_day;
                                                    while (empty($list_of_class_period_ids) && $loop < 7) {
                                                        $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                            $next_week_day,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type
                                                        );
                                                        $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                            $next_week_day,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            $publishedCourse_id,
                                                            $selected_academic_year,
                                                            $selected_semester,
                                                            $publishedCourse_details['Section']['id'],
                                                            "Lecture"
                                                        );
                                                        $loop++;
                                                    }
                                                    //get a group of continuous class_period_lists per period number of hours.
                                                    $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number(
                                                        $list_of_class_period_ids,
                                                        $period_number
                                                    );
                                                    //Get Sorted potentialy assignable class rooms
                                                    $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms(
                                                        $publishedCourse_details,
                                                        $selected_program,
                                                        $selected_program_type,
                                                        "Lecture"
                                                    );
                                                    //get free class room from sorted_potential_class_rooms in a given periods
                                                    $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                        $continuous_grouped_class_period_list_into_period_number,
                                                        $sorted_potential_class_rooms,
                                                        $selected_academic_year,
                                                        $selected_semester
                                                    );
                                                    $is_loop = true;
                                                    $loop_count = 1;
                                                    $is_save = false;
                                                    do {
                                                        if (!empty($match_periods_and_class_room)) {
                                                            if (isset($schedule)) {
                                                                unset($schedule);
                                                            }
                                                            $schedule['class_room_id'] = $match_periods_and_class_room['class_room'];
                                                            $schedule['section_id'] = $publishedCourse_details['Section']['id'];
                                                            $schedule['published_course_id'] = $publishedCourse_id;
                                                            $schedule['academic_year'] = $selected_academic_year;
                                                            $schedule['semester'] = $selected_semester;
                                                            $schedule['type'] = "Lecture";
                                                            $this->CourseSchedule->create();
                                                            $this->CourseSchedule->save($schedule);
                                                            //get saved course schedule id to save course id and class period id in associate table
                                                            $associate['course_schedule_id'] = $this->CourseSchedule->id;
                                                            foreach ($match_periods_and_class_room['class_periods'] as $class_period_id) {
                                                                $associate['class_period_id'] = $class_period_id;
                                                                $this->CourseSchedule->CourseSchedulesClassPeriod->create(
                                                                );
                                                                $this->CourseSchedule->CourseSchedulesClassPeriod->save(
                                                                    $associate
                                                                );
                                                            }
                                                            $is_save = true;
                                                            $is_loop = false;
                                                        } else {
                                                            //Increment the week day  by one and try again
                                                            $is_loop = true;
                                                            $loop_count++;
                                                            //find the next week day assign to this college, program & program type
                                                            $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                $next_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type
                                                            );
                                                            //Get list of class period of this week day,college,program and program type
                                                            $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                $next_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type,
                                                                $publishedCourse_id,
                                                                $selected_academic_year,
                                                                $selected_semester,
                                                                $publishedCourse_details['Section']['id'],
                                                                "Lecture"
                                                            );
                                                            //If list of class period is empty in the given week day try the next week day class period (this function try 7 time just to prevent infinite loop)
                                                            $loop = 1;
                                                            while (empty($list_of_class_period_ids) && $loop < 7) {
                                                                $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                    $next_week_day,
                                                                    $this->college_id,
                                                                    $selected_program,
                                                                    $selected_program_type
                                                                );
                                                                $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                    $next_week_day,
                                                                    $this->college_id,
                                                                    $selected_program,
                                                                    $selected_program_type,
                                                                    $publishedCourse_id,
                                                                    $selected_academic_year,
                                                                    $selected_semester,
                                                                    $publishedCourse_details['Section']['id'],
                                                                    "Lecture"
                                                                );
                                                                $loop++;
                                                            }
                                                            //get a group of continuous class_period_lists per period number of hours
                                                            $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number(
                                                                $list_of_class_period_ids,
                                                                $period_number
                                                            );
                                                            //get free class room from sorted_potential_class_rooms in a given periods
                                                            $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                                $continuous_grouped_class_period_list_into_period_number,
                                                                $sorted_potential_class_rooms,
                                                                $selected_academic_year,
                                                                $selected_semester
                                                            );
                                                        }
                                                    } while ($is_loop == true && $loop_count < 7);
                                                    //store assignment failed courses per period number and course type for display.
                                                    if ($is_save == false) {
                                                        //$unschedule_published_course[$period_number]['Lecture'][] = $publishedCourse_id;
                                                        //$unschedule_published_course[$publishedCourse_details['Section']['id']]['Lecture'][$period_number][$publishedCourse_id][] = "dueto unable to find free class period and instructor sutable time in specified class room or in availabel class room";
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_title'] = $publishedCourse_details['Course']['course_title'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_code'] = $publishedCourse_details['Course']['course_code'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['credit'] = $publishedCourse_details['Course']['credit'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['lecture_hours'] = $publishedCourse_details['Course']['lecture_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['tutorial_hours'] = $publishedCourse_details['Course']['tutorial_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['laboratory_hours'] = $publishedCourse_details['Course']['laboratory_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['section_name'] = $publishedCourse_details['Section']['name'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_length'] = $period_number;
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_type'] = "Lecture";
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['possible_reason'] = "Due to unable to find free class period and instructor sutable time in specified class room or in availabel class room";
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['published_course_id'] = $publishedCourse_id;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['course_split_section_id'] = null;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['period_length'] = $period_number;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['type'] = "Lecture";
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['description'] = "Due to unable to find free class period and instructor sutable time in specified class room or in availabel class room";
                                                        $unscheduled_count++;
                                                    }
                                                }
                                            }
                                        } //end else for unsplit section lecture period
                                    } // end of if of lecture hours for lecture
                                } // end of foreach loop of sorted published course for lecture

                                /****************************************
                                 *Schedule all turorial tutorial periods
                                 *****************************************/
                                foreach ($sorted_publishedCourses as $publishedCourse_id => $weight_value) {
//get publishedcourse details
                                    $publishedCourse_details = $this->CourseSchedule->get_published_course_details(
                                        $publishedCourse_id
                                    );
                                    debug($publishedCourse_details);
//schedule for tutorial hours
                                    if (!empty($publishedCourse_details['Course']['tutorial_hours'])) {
//if the section is split for the given published course and tutorial course type
                                        if ((strcasecmp(
                                                    $publishedCourse_details['SectionSplitForPublishedCourse'][0]['type'],
                                                    'Tutorial'
                                                ) == 0) || (strcasecmp(
                                                    $publishedCourse_details['SectionSplitForPublishedCourse'][0]['type'],
                                                    'Lecture+Tutorial'
                                                ) == 0)) {
//schedule for each splited sections
                                            foreach ($publishedCourse_details['SectionSplitForPublishedCourse'][0]['CourseSplitSection'] as $split_section) {
//get number of session and its period number
                                                $number_of_period_per_session = $this->CourseSchedule->get_number_period_per_session(
                                                    $publishedCourse_details['Course']['tutorial_hours'],
                                                    $publishedCourse_details['PublishedCourse']['tutorial_number_of_session']
                                                );
//schedule for each session
                                                foreach ($number_of_period_per_session as $npsk => $period_number) {
                                                    $potential_class_period_ids_from_constranints = array();
                                                    if (!empty($publishedCourse_details['ClassPeriodCourseConstraint'])) {
                                                        //find class period ids from class period course constranits that specified as assigned
                                                        foreach ($publishedCourse_details['ClassPeriodCourseConstraint'] as $classPeriodCourseConstraint) {
                                                            if ($classPeriodCourseConstraint['active'] == 1 && strcasecmp(
                                                                    $classPeriodCourseConstraint['type'],
                                                                    "Tutorial"
                                                                ) == 0) {
                                                                $potential_class_period_ids_from_constranints[] = $classPeriodCourseConstraint['class_period_id'];
                                                            }
                                                        }
                                                    }
                                                    if (!empty($potential_class_period_ids_from_constranints)) {
                                                        //Get Sorted potentialy assignable class rooms
                                                        $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms(
                                                            $publishedCourse_details,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            "Tutorial"
                                                        );
                                                        //get a group of continuous class_period_lists per period number of hours take the class period sepcified in class period course constraints as starting period.
                                                        $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number_from_constraint(
                                                            $period_number,
                                                            $potential_class_period_ids_from_constranints,
                                                            $publishedCourse_id,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            $selected_academic_year,
                                                            $selected_semester,
                                                            $publishedCourse_details['Section']['id'],
                                                            "Tutorial"
                                                        );
                                                        //Get ready to assign, Matched class room (from sorted potential class rooms) and continuous grouped class periods (from the class period sepcified in class period course constraints)
                                                        $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                            $continuous_grouped_class_period_list_into_period_number,
                                                            $sorted_potential_class_rooms,
                                                            $selected_academic_year,
                                                            $selected_semester
                                                        );

                                                        if (!empty($match_periods_and_class_room)) {
                                                            if (isset($schedule)) {
                                                                unset($schedule);
                                                            }
                                                            $schedule['class_room_id'] = $match_periods_and_class_room['class_room'];
                                                            $schedule['section_id'] = $publishedCourse_details['Section']['id'];
                                                            $schedule['course_split_section_id'] = $split_section['id'];
                                                            $schedule['published_course_id'] = $publishedCourse_id;
                                                            $schedule['academic_year'] = $selected_academic_year;
                                                            $schedule['semester'] = $selected_semester;
                                                            $schedule['type'] = "Tutorial";
                                                            $this->CourseSchedule->create();
                                                            $this->CourseSchedule->save($schedule);
                                                            //get saved course schedule id to save course id and class period id in associate table
                                                            $associate['course_schedule_id'] = $this->CourseSchedule->id;
                                                            foreach ($match_periods_and_class_room['class_periods'] as $class_period_id) {
                                                                $associate['class_period_id'] = $class_period_id;
                                                                $this->CourseSchedule->CourseSchedulesClassPeriod->create(
                                                                );
                                                                $this->CourseSchedule->CourseSchedulesClassPeriod->save(
                                                                    $associate
                                                                );
                                                            }
                                                        } else {
                                                            //$unschedule_published_course[$publishedCourse_details['Section']['id']][$split_section['id']]['Tutorial'][$period_number][$publishedCourse_id][] = "dueto unable to find free class room and instructor suitable time in specified class period course constraints time";
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_title'] = $publishedCourse_details['Course']['course_title'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_code'] = $publishedCourse_details['Course']['course_code'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['credit'] = $publishedCourse_details['Course']['credit'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['lecture_hours'] = $publishedCourse_details['Course']['lecture_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['tutorial_hours'] = $publishedCourse_details['Course']['tutorial_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['laboratory_hours'] = $publishedCourse_details['Course']['laboratory_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['section_name'] = $publishedCourse_details['Section']['name'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_length'] = $period_number;
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_type'] = "Tutorial";
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['split_section_name'] = $this->CourseSchedule->CourseSplitSection->field(
                                                                'CourseSplitSection.section_name',
                                                                array('CourseSplitSection.id' => $split_section['id'])
                                                            );
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['possible_reason'] = "Due to unable to find free class room and instructor suitable time in specified class period course constraint time";
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['published_course_id'] = $publishedCourse_id;
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['course_split_section_id'] = $split_section['id'];
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['period_length'] = $period_number;
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['type'] = "Tutorial";
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['description'] = "Due to unable to find free class room and instructor suitable time in specified class period course constraint time";
                                                            $unscheduled_count++;
                                                        }
                                                    } else {
                                                        //get section last assigned week day in selected academic year and semester
                                                        $last_assigned_week_day = $this->CourseSchedule->get_last_assigned_week_day(
                                                            $publishedCourse_details['Section']['id'],
                                                            $selected_academic_year,
                                                            $selected_semester
                                                        );
                                                        if (!empty($last_assigned_week_day)) {
                                                            $potential_week_day = $this->CourseSchedule->get_next_week_day_from_last_assigned_week_day(
                                                                $last_assigned_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type
                                                            );
                                                        } else {
                                                            //if last_assigned_week_day is empty it mean the published course is the first for assignment
                                                            //find take the first week day assign to this college, program & program type
                                                            $potential_week_day = $this->CourseSchedule->get_first_week_day(
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type
                                                            );
                                                        }
                                                        //Get list of class period of this week day,college,program and program type
                                                        $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                            $potential_week_day,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            $publishedCourse_id,
                                                            $selected_academic_year,
                                                            $selected_semester,
                                                            $publishedCourse_details['Section']['id'],
                                                            "Tutorial"
                                                        );
                                                        //If list of class period is empty in the given week day try the next week day class period (this function try 7 time just to prevent infinite loop)
                                                        $loop = 1;
                                                        $next_week_day = $potential_week_day;
                                                        while (empty($list_of_class_period_ids) && $loop < 7) {
                                                            $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                $next_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type
                                                            );
                                                            $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                $next_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type,
                                                                $publishedCourse_id,
                                                                $selected_academic_year,
                                                                $selected_semester,
                                                                $publishedCourse_details['Section']['id'],
                                                                "Tutorial"
                                                            );
                                                            $loop++;
                                                        }
                                                        //get a group of continuous class_period_lists per period number of hours.
                                                        $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number(
                                                            $list_of_class_period_ids,
                                                            $period_number
                                                        );
                                                        //Get Sorted potentialy assignable class rooms
                                                        $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms(
                                                            $publishedCourse_details,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            "Tutorial"
                                                        );
                                                        //get free class room from sorted_potential_class_rooms in a given periods
                                                        $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                            $continuous_grouped_class_period_list_into_period_number,
                                                            $sorted_potential_class_rooms,
                                                            $selected_academic_year,
                                                            $selected_semester
                                                        );
                                                        $is_loop = true;
                                                        $loop_count = 1;
                                                        $is_save = false;
                                                        do {
                                                            if (!empty($match_periods_and_class_room)) {
                                                                if (isset($schedule)) {
                                                                    unset($schedule);
                                                                }
                                                                $schedule['class_room_id'] = $match_periods_and_class_room['class_room'];
                                                                $schedule['section_id'] = $publishedCourse_details['Section']['id'];
                                                                $schedule['course_split_section_id'] = $split_section['id'];
                                                                $schedule['published_course_id'] = $publishedCourse_id;
                                                                $schedule['academic_year'] = $selected_academic_year;
                                                                $schedule['semester'] = $selected_semester;
                                                                $schedule['type'] = "Tutorial";
                                                                $this->CourseSchedule->create();
                                                                $this->CourseSchedule->save($schedule);
                                                                //get saved course schedule id to save course id and class period id in associate table
                                                                $associate['course_schedule_id'] = $this->CourseSchedule->id;
                                                                foreach ($match_periods_and_class_room['class_periods'] as $class_period_id) {
                                                                    $associate['class_period_id'] = $class_period_id;
                                                                    $this->CourseSchedule->CourseSchedulesClassPeriod->create(
                                                                    );
                                                                    $this->CourseSchedule->CourseSchedulesClassPeriod->save(
                                                                        $associate
                                                                    );
                                                                }
                                                                $is_save = true;
                                                                $is_loop = false;
                                                            } else {
                                                                //Increment the week day  by one and try again
                                                                $is_loop = true;
                                                                $loop_count++;
                                                                //find the next week day assign to this college, program & program type
                                                                $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                    $next_week_day,
                                                                    $this->college_id,
                                                                    $selected_program,
                                                                    $selected_program_type
                                                                );
                                                                //Get list of class period of this week day,college,program and program type
                                                                $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                    $next_week_day,
                                                                    $this->college_id,
                                                                    $selected_program,
                                                                    $selected_program_type,
                                                                    $$publishedCourse_id,
                                                                    $selected_academic_year,
                                                                    $selected_semester,
                                                                    $publishedCourse_details['Section']['id'],
                                                                    "Tutorial"
                                                                );
                                                                //If list of class period is empty in the given week day try the next week day class period (this function try 7 time just to prevent infinite loop)
                                                                $loop = 1;
                                                                while (empty($list_of_class_period_ids) && $loop < 7) {
                                                                    $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                        $next_week_day,
                                                                        $this->college_id,
                                                                        $selected_program,
                                                                        $selected_program_type
                                                                    );
                                                                    $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                        $next_week_day,
                                                                        $this->college_id,
                                                                        $selected_program,
                                                                        $selected_program_type,
                                                                        $$publishedCourse_id,
                                                                        $selected_academic_year,
                                                                        $selected_semester,
                                                                        $publishedCourse_details['Section']['id'],
                                                                        "Tutorial"
                                                                    );
                                                                    $loop++;
                                                                }
//get a group of continuous class_period_lists per period number of hours
                                                                $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number(
                                                                    $list_of_class_period_ids,
                                                                    $period_number
                                                                );
                                                                //get free class room from sorted_potential_class_rooms in a given periods
                                                                $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                                    $continuous_grouped_class_period_list_into_period_number,
                                                                    $sorted_potential_class_rooms,
                                                                    $selected_academic_year,
                                                                    $selected_semester
                                                                );
                                                            }
                                                        } while ($is_loop == true && $loop_count < 7);
                                                        //store assignment failed courses per period number and course type for display.
                                                        if ($is_save == false) {
                                                            //$unschedule_published_course[$period_number]['Lecture'][] = $publishedCourse_id;
                                                            //$unschedule_published_course[$publishedCourse_details['Section']['id']][$split_section['id']]['Tutorial'][$period_number][$publishedCourse_id][] = "dueto unable to find free class period and instructor suitable time in specified class room or in availabel class room";
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_title'] = $publishedCourse_details['Course']['course_title'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_code'] = $publishedCourse_details['Course']['course_code'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['credit'] = $publishedCourse_details['Course']['credit'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['lecture_hours'] = $publishedCourse_details['Course']['lecture_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['tutorial_hours'] = $publishedCourse_details['Course']['tutorial_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['laboratory_hours'] = $publishedCourse_details['Course']['laboratory_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['section_name'] = $publishedCourse_details['Section']['name'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_length'] = $period_number;
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_type'] = "Tutorial";
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['split_section_name'] = $this->CourseSchedule->CourseSplitSection->field(
                                                                'CourseSplitSection.section_name',
                                                                array('CourseSplitSection.id' => $split_section['id'])
                                                            );
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['possible_reason'] = "Due to unable to find free class period and instructor suitable time in specified class room or in availabel class room";
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['published_course_id'] = $publishedCourse_id;
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['course_split_section_id'] = $split_section['id'];
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['period_length'] = $period_number;
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['type'] = "Tutorial";
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['description'] = "Due to unable to find free class period and instructor suitable time in specified class room or in availabel class room";
                                                            $unscheduled_count++;
                                                        }
                                                    }
                                                }
                                            } //end of froeach split section for tutorial
                                        } //end of if seplit section for tutorial
//If the section is not split for the given course
                                        else {
//get number of session and its period number
                                            $number_of_period_per_session = $this->CourseSchedule->get_number_period_per_session(
                                                $publishedCourse_details['Course']['tutorial_hours'],
                                                $publishedCourse_details['PublishedCourse']['tutorial_number_of_session']
                                            );
//schedule for each session
                                            foreach ($number_of_period_per_session as $npsk => $period_number) {
                                                $potential_class_period_ids_from_constranints = array();
                                                if (!empty($publishedCourse_details['ClassPeriodCourseConstraint'])) {
//find class period ids from class period course constranits that specified as assigned
                                                    foreach ($publishedCourse_details['ClassPeriodCourseConstraint'] as $classPeriodCourseConstraint) {
                                                        if ($classPeriodCourseConstraint['active'] == 1 && strcasecmp(
                                                                $classPeriodCourseConstraint['type'],
                                                                "Tutorial"
                                                            ) == 0) {
                                                            $potential_class_period_ids_from_constranints[] = $classPeriodCourseConstraint['class_period_id'];
                                                        }
                                                    }
                                                }
                                                if (!empty($potential_class_period_ids_from_constranints)) {
                                                    //Get Sorted potentialy assignable class rooms
                                                    $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms(
                                                        $publishedCourse_details,
                                                        $selected_program,
                                                        $selected_program_type,
                                                        "Tutorial"
                                                    );
                                                    //get a group of continuous class_period_lists per period number of hours take the class period sepcified in class period course constraints as starting period.
                                                    $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number_from_constraint(
                                                        $period_number,
                                                        $potential_class_period_ids_from_constranints,
                                                        $publishedCourse_id,
                                                        $this->college_id,
                                                        $selected_program,
                                                        $selected_program_type,
                                                        $selected_academic_year,
                                                        $selected_semester,
                                                        $publishedCourse_details['Section']['id'],
                                                        "Tutorial"
                                                    );
                                                    //Get ready to assign, Matched class room (from sorted potential class rooms) and continuous grouped class periods (from the class period sepcified in class period course constraints)
                                                    $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                        $continuous_grouped_class_period_list_into_period_number,
                                                        $sorted_potential_class_rooms,
                                                        $selected_academic_year,
                                                        $selected_semester
                                                    );
                                                    if (!empty($match_periods_and_class_room)) {
                                                        if (isset($schedule)) {
                                                            unset($schedule);
                                                        }
                                                        $schedule['class_room_id'] = $match_periods_and_class_room['class_room'];
                                                        $schedule['section_id'] = $publishedCourse_details['Section']['id'];
                                                        $schedule['published_course_id'] = $publishedCourse_id;
                                                        $schedule['academic_year'] = $selected_academic_year;
                                                        $schedule['semester'] = $selected_semester;
                                                        $schedule['type'] = "Tutorial";
                                                        $this->CourseSchedule->create();
                                                        $this->CourseSchedule->save($schedule);
                                                        //get saved course schedule id to save course id and class period id in associate table
                                                        $associate['course_schedule_id'] = $this->CourseSchedule->id;
                                                        foreach ($match_periods_and_class_room['class_periods'] as $class_period_id) {
                                                            $associate['class_period_id'] = $class_period_id;
                                                            $this->CourseSchedule->CourseSchedulesClassPeriod->create();
                                                            $this->CourseSchedule->CourseSchedulesClassPeriod->save(
                                                                $associate
                                                            );
                                                        }
                                                    } else {
                                                        //$unschedule_published_course[$publishedCourse_details['Section']['id']]['Tutorial'][$period_number][$publishedCourse_id][] = "dueto unable to find free class room and instructor suitable time in specified class period course constraints time";
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_title'] = $publishedCourse_details['Course']['course_title'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_code'] = $publishedCourse_details['Course']['course_code'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['credit'] = $publishedCourse_details['Course']['credit'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['lecture_hours'] = $publishedCourse_details['Course']['lecture_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['tutorial_hours'] = $publishedCourse_details['Course']['tutorial_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['laboratory_hours'] = $publishedCourse_details['Course']['laboratory_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['section_name'] = $publishedCourse_details['Section']['name'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_length'] = $period_number;
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_type'] = "Tutorial";
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['possible_reason'] = "Due to unable to find free class room and instructor suitable time in specified class period course constraint time";
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['published_course_id'] = $publishedCourse_id;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['course_split_section_id'] = null;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['period_length'] = $period_number;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['type'] = "Tutorial";
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['description'] = "Due to unable to find free class room and instructor suitable time in specified class period course constraint time";
                                                        $unscheduled_count++;
                                                    }
                                                } else {
//get section last assigned week day in selected academic year and semester
                                                    $last_assigned_week_day = $this->CourseSchedule->get_last_assigned_week_day(
                                                        $publishedCourse_details['Section']['id'],
                                                        $selected_academic_year,
                                                        $selected_semester
                                                    );
                                                    if (!empty($last_assigned_week_day)) {
                                                        $potential_week_day = $this->CourseSchedule->get_next_week_day_from_last_assigned_week_day(
                                                            $last_assigned_week_day,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type
                                                        );
                                                    } else {
                                                        //if last_assigned_week_day is empty it mean the published course is the first for assignment
                                                        //find take the first week day assign to this college, program & program type
                                                        $potential_week_day = $this->CourseSchedule->get_first_week_day(
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type
                                                        );
                                                    }
                                                    //Get list of class period of this week day,college,program and program type
                                                    $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                        $potential_week_day,
                                                        $this->college_id,
                                                        $selected_program,
                                                        $selected_program_type,
                                                        $publishedCourse_id,
                                                        $selected_academic_year,
                                                        $selected_semester,
                                                        $publishedCourse_details['Section']['id'],
                                                        "Tutorial"
                                                    );
                                                    //If list of class period is empty in the given week day try the next week day class period (this function try 7 time just to prevent infinite loop)
                                                    $loop = 1;
                                                    $next_week_day = $potential_week_day;
                                                    while (empty($list_of_class_period_ids) && $loop < 7) {
                                                        $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                            $next_week_day,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type
                                                        );
                                                        $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                            $next_week_day,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            $publishedCourse_id,
                                                            $selected_academic_year,
                                                            $selected_semester,
                                                            $publishedCourse_details['Section']['id'],
                                                            "Tutorial"
                                                        );
                                                        $loop++;
                                                    }
                                                    //get a group of continuous class_period_lists per period number of hours.
                                                    $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number(
                                                        $list_of_class_period_ids,
                                                        $period_number
                                                    );
                                                    //Get Sorted potentialy assignable class rooms
                                                    $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms(
                                                        $publishedCourse_details,
                                                        $selected_program,
                                                        $selected_program_type,
                                                        "Tutorial"
                                                    );
                                                    //get free class room from sorted_potential_class_rooms in a given periods
                                                    $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                        $continuous_grouped_class_period_list_into_period_number,
                                                        $sorted_potential_class_rooms,
                                                        $selected_academic_year,
                                                        $selected_semester
                                                    );
                                                    $is_loop = true;
                                                    $loop_count = 1;
                                                    $is_save = false;
                                                    do {
                                                        if (!empty($match_periods_and_class_room)) {
                                                            if (isset($schedule)) {
                                                                unset($schedule);
                                                            }
                                                            $schedule['class_room_id'] = $match_periods_and_class_room['class_room'];
                                                            $schedule['section_id'] = $publishedCourse_details['Section']['id'];
                                                            $schedule['published_course_id'] = $publishedCourse_id;
                                                            $schedule['academic_year'] = $selected_academic_year;
                                                            $schedule['semester'] = $selected_semester;
                                                            $schedule['type'] = "Tutorial";
                                                            $this->CourseSchedule->create();
                                                            $this->CourseSchedule->save($schedule);
                                                            //get saved course schedule id to save course id and class period id in associate table
                                                            $associate['course_schedule_id'] = $this->CourseSchedule->id;
                                                            foreach ($match_periods_and_class_room['class_periods'] as $class_period_id) {
                                                                $associate['class_period_id'] = $class_period_id;
                                                                $this->CourseSchedule->CourseSchedulesClassPeriod->create(
                                                                );
                                                                $this->CourseSchedule->CourseSchedulesClassPeriod->save(
                                                                    $associate
                                                                );
                                                            }
                                                            $is_save = true;
                                                            $is_loop = false;
                                                        } else {
                                                            //Increment the week day  by one and try again
                                                            $is_loop = true;
                                                            $loop_count++;
                                                            //find the next week day assign to this college, program & program type
                                                            $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                $next_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type
                                                            );
                                                            //Get list of class period of this week day,college,program and program type
                                                            $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                $next_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type,
                                                                $publishedCourse_id,
                                                                $selected_academic_year,
                                                                $selected_semester,
                                                                $publishedCourse_details['Section']['id'],
                                                                "Tutorial"
                                                            );
                                                            //If list of class period is empty in the given week day try the next week day class period (this function try 7 time just to prevent infinite loop)
                                                            $loop = 1;
                                                            while (empty($list_of_class_period_ids) && $loop < 7) {
                                                                $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                    $next_week_day,
                                                                    $this->college_id,
                                                                    $selected_program,
                                                                    $selected_program_type
                                                                );
                                                                $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                    $next_week_day,
                                                                    $this->college_id,
                                                                    $selected_program,
                                                                    $selected_program_type,
                                                                    $publishedCourse_id,
                                                                    $selected_academic_year,
                                                                    $selected_semester,
                                                                    $publishedCourse_details['Section']['id'],
                                                                    "Tutorial"
                                                                );
                                                                $loop++;
                                                            }
                                                            //get a group of continuous class_period_lists per period number of hours
                                                            $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number(
                                                                $list_of_class_period_ids,
                                                                $period_number
                                                            );
                                                            //get free class room from sorted_potential_class_rooms in a given periods
                                                            $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                                $continuous_grouped_class_period_list_into_period_number,
                                                                $sorted_potential_class_rooms,
                                                                $selected_academic_year,
                                                                $selected_semester
                                                            );
                                                        }
                                                    } while ($is_loop == true && $loop_count < 7);
                                                    //store assignment failed courses per period number and course type for display.
                                                    if ($is_save == false) {
                                                        //$unschedule_published_course[$publishedCourse_details['Section']['id']]['Tutorial'][$period_number][$publishedCourse_id][] = "dueto unable to find free class period and instructor suitable time in specified class room or in availabel class room";
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_title'] = $publishedCourse_details['Course']['course_title'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_code'] = $publishedCourse_details['Course']['course_code'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['credit'] = $publishedCourse_details['Course']['credit'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['lecture_hours'] = $publishedCourse_details['Course']['lecture_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['tutorial_hours'] = $publishedCourse_details['Course']['tutorial_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['laboratory_hours'] = $publishedCourse_details['Course']['laboratory_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['section_name'] = $publishedCourse_details['Section']['name'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_length'] = $period_number;
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_type'] = "Tutorial";
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['possible_reason'] = "Due to unable to find free class period and instructor suitable time in specified class room or in availabel class room";
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['published_course_id'] = $publishedCourse_id;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['course_split_section_id'] = null;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['period_length'] = $period_number;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['type'] = "Tutorial";
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['description'] = "Due to unable to find free class period and instructor suitable time in specified class room or in availabel class room";
                                                        $unscheduled_count++;
                                                    }
                                                }
                                            }
                                        } //end of else unsplited section
                                    } // end of if of lecture hours for tutorial
                                } // end of foreach loop of sorted published course for tutorial

                                /****************************************
                                 *Schedule all laboratoriy periods
                                 ****************************************/
                                foreach ($sorted_publishedCourses as $publishedCourse_id => $weight_value) {
//get publishedcourse details
                                    $publishedCourse_details = $this->CourseSchedule->get_published_course_details(
                                        $publishedCourse_id
                                    );
//schedule for Laboratory hours
                                    if (!empty($publishedCourse_details['Course']['laboratory_hours'])) {
//if the section is split for the given published course and laboratory course type
                                        if ((strcasecmp(
                                                    $publishedCourse_details['SectionSplitForPublishedCourse'][0]['type'],
                                                    'Lab'
                                                ) == 0) || (strcasecmp(
                                                    $publishedCourse_details['SectionSplitForPublishedCourse'][0]['type'],
                                                    'Lecture+Lab'
                                                ) == 0)) {
//schedule for each splited sections
                                            foreach ($publishedCourse_details['SectionSplitForPublishedCourse'][0]['CourseSplitSection'] as $split_section) {
//get number of session and its period number
                                                $number_of_period_per_session = $this->CourseSchedule->get_number_period_per_session(
                                                    $publishedCourse_details['Course']['laboratory_hours'],
                                                    $publishedCourse_details['PublishedCourse']['lab_number_of_session']
                                                );
//schedule for each session
                                                foreach ($number_of_period_per_session as $npsk => $period_number) {
                                                    $potential_class_period_ids_from_constranints = array();
                                                    if (!empty($publishedCourse_details['ClassPeriodCourseConstraint'])) {
                                                        //find class period ids from class period course constranits that specified as assigned
                                                        foreach ($publishedCourse_details['ClassPeriodCourseConstraint'] as $classPeriodCourseConstraint) {
                                                            if ($classPeriodCourseConstraint['active'] == 1 && strcasecmp(
                                                                    $classPeriodCourseConstraint['type'],
                                                                    "Lab"
                                                                ) == 0) {
                                                                $potential_class_period_ids_from_constranints[] = $classPeriodCourseConstraint['class_period_id'];
                                                            }
                                                        }
                                                    }
                                                    if (!empty($potential_class_period_ids_from_constranints)) {
                                                        //Get Sorted potentialy assignable class rooms
                                                        $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms_from_constraint(
                                                            $publishedCourse_details,
                                                            $selected_program,
                                                            $selected_program_type
                                                        );
                                                        //get a group of continuous class_period_lists per period number of hours take the class period sepcified in class period course constraints as starting period.
                                                        $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number_from_constraint(
                                                            $period_number,
                                                            $potential_class_period_ids_from_constranints,
                                                            $publishedCourse_id,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            $selected_academic_year,
                                                            $selected_semester,
                                                            $publishedCourse_details['Section']['id'],
                                                            "Lab"
                                                        );
                                                        //Get ready to assign, Matched class room (from sorted potential class rooms) and continuous grouped class periods (from the class period sepcified in class period course constraints)
                                                        if (!empty($sorted_potential_class_rooms)) {
                                                            //get free class room from sorted_potential_class_rooms in a given periods
                                                            $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                                $continuous_grouped_class_period_list_into_period_number,
                                                                $sorted_potential_class_rooms,
                                                                $selected_academic_year,
                                                                $selected_semester
                                                            );
                                                        } else {
                                                            $match_periods_and_class_room = array();
                                                            if (!empty($continuous_grouped_class_period_list_into_period_number)) {
                                                                $match_periods_and_class_room['class_periods'] = $continuous_grouped_class_period_list_into_period_number[(count(
                                                                        $continuous_grouped_class_period_list_into_period_number
                                                                    ) - 1)];
                                                                $match_periods_and_class_room['class_room'] = null;
                                                            }
                                                        }
                                                        if (!empty($match_periods_and_class_room)) {
                                                            if (isset($schedule)) {
                                                                unset($schedule);
                                                            }
                                                            $schedule['class_room_id'] = $match_periods_and_class_room['class_room'];
                                                            $schedule['section_id'] = $publishedCourse_details['Section']['id'];
                                                            $schedule['course_split_section_id'] = $split_section['id'];
                                                            $schedule['published_course_id'] = $publishedCourse_id;
                                                            $schedule['academic_year'] = $selected_academic_year;
                                                            $schedule['semester'] = $selected_semester;
                                                            $schedule['type'] = "Lab";
                                                            $this->CourseSchedule->create();
                                                            debug($schedule);
                                                            $this->CourseSchedule->save($schedule);
                                                            //get saved course schedule id to save course id and class period id in associate table
                                                            $associate['course_schedule_id'] = $this->CourseSchedule->id;
                                                            foreach ($match_periods_and_class_room['class_periods'] as $class_period_id) {
                                                                $associate['class_period_id'] = $class_period_id;
                                                                $this->CourseSchedule->CourseSchedulesClassPeriod->create(
                                                                );
                                                                $this->CourseSchedule->CourseSchedulesClassPeriod->save(
                                                                    $associate
                                                                );
                                                            }
                                                        } else {
                                                            //$unschedule_published_course[$publishedCourse_details['Section']['id']][$split_section['id']]['Laboratory'][$period_number][$publishedCourse_id][] = "dueto unable to find free class room and instructor suitable time in specified class period course constraints time";
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_title'] = $publishedCourse_details['Course']['course_title'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_code'] = $publishedCourse_details['Course']['course_code'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['credit'] = $publishedCourse_details['Course']['credit'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['lecture_hours'] = $publishedCourse_details['Course']['lecture_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['tutorial_hours'] = $publishedCourse_details['Course']['tutorial_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['laboratory_hours'] = $publishedCourse_details['Course']['laboratory_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['section_name'] = $publishedCourse_details['Section']['name'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_length'] = $period_number;
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_type'] = "Laboratory";
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['split_section_name'] = $this->CourseSchedule->CourseSplitSection->field(
                                                                'CourseSplitSection.section_name',
                                                                array('CourseSplitSection.id' => $split_section['id'])
                                                            );
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['possible_reason'] = "Due to unable to find free class room and instructor suitable time in specified class period course constraint time";
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['published_course_id'] = $publishedCourse_id;
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['course_split_section_id'] = $split_section['id'];
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['period_length'] = $period_number;
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['type'] = "Laboratory";
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['description'] = "Due to unable to find free class room and instructor suitable time in specified class period course constraint time";
                                                            $unscheduled_count++;
                                                        }
                                                    } else {
                                                        //get section last assigned week day in selected academic year and semester
                                                        $last_assigned_week_day = $this->CourseSchedule->get_last_assigned_week_day(
                                                            $publishedCourse_details['Section']['id'],
                                                            $selected_academic_year,
                                                            $selected_semester
                                                        );
                                                        if (!empty($last_assigned_week_day)) {
                                                            $potential_week_day = $this->CourseSchedule->get_next_week_day_from_last_assigned_week_day(
                                                                $last_assigned_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type
                                                            );
                                                        } else {
                                                            //if last_assigned_week_day is empty it mean the published course is the first for assignment
                                                            //find take the first week day assign to this college, program & program type
                                                            $potential_week_day = $this->CourseSchedule->get_first_week_day(
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type
                                                            );
                                                        }
                                                        //Get list of class period of this week day,college,program and program type
                                                        $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                            $potential_week_day,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            $publishedCourse_id,
                                                            $selected_academic_year,
                                                            $selected_semester,
                                                            $publishedCourse_details['Section']['id'],
                                                            'Lab'
                                                        );
                                                        //If list of class period is empty in the given week day try the next week day class period (this function try 7 time just to prevent infinite loop)
                                                        $loop = 1;
                                                        $next_week_day = $potential_week_day;
                                                        while (empty($list_of_class_period_ids) && $loop < 7) {
                                                            $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                $next_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type
                                                            );
                                                            $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                $next_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type,
                                                                $publishedCourse_id,
                                                                $selected_academic_year,
                                                                $selected_semester,
                                                                $publishedCourse_details['Section']['id'],
                                                                'Lab'
                                                            );
                                                            $loop++;
                                                        }
                                                        //get a group of continuous class_period_lists per period number of hours.
                                                        $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number(
                                                            $list_of_class_period_ids,
                                                            $period_number
                                                        );
                                                        //Get Sorted potentialy assignable class rooms
                                                        $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms_from_constraint(
                                                            $publishedCourse_details,
                                                            $selected_program,
                                                            $selected_program_type
                                                        );

                                                        if (!empty($sorted_potential_class_rooms)) {
                                                            //get free class room from sorted_potential_class_rooms in a given periods
                                                            $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                                $continuous_grouped_class_period_list_into_period_number,
                                                                $sorted_potential_class_rooms,
                                                                $selected_academic_year,
                                                                $selected_semester
                                                            );
                                                        } else {
                                                            $match_periods_and_class_room = array();
                                                            if (!empty($continuous_grouped_class_period_list_into_period_number)) {
                                                                $match_periods_and_class_room['class_periods'] = $continuous_grouped_class_period_list_into_period_number[(count(
                                                                        $continuous_grouped_class_period_list_into_period_number
                                                                    ) - 1)];
                                                                $match_periods_and_class_room['class_room'] = null;
                                                            }
                                                        }
                                                        $is_loop = true;
                                                        $loop_count = 1;
                                                        $is_save = false;
                                                        do {
                                                            if (!empty($match_periods_and_class_room)) {
                                                                if (isset($schedule)) {
                                                                    unset($schedule);
                                                                }
                                                                $schedule['class_room_id'] = $match_periods_and_class_room['class_room'];
                                                                $schedule['section_id'] = $publishedCourse_details['Section']['id'];
                                                                $schedule['course_split_section_id'] = $split_section['id'];
                                                                $schedule['published_course_id'] = $publishedCourse_id;
                                                                $schedule['academic_year'] = $selected_academic_year;
                                                                $schedule['semester'] = $selected_semester;
                                                                $schedule['type'] = "Lab";
                                                                $this->CourseSchedule->create();
                                                                $this->CourseSchedule->save($schedule);
                                                                //get saved course schedule id to save course id and class period id in associate table
                                                                $associate['course_schedule_id'] = $this->CourseSchedule->id;
                                                                foreach ($match_periods_and_class_room['class_periods'] as $class_period_id) {
                                                                    $associate['class_period_id'] = $class_period_id;
                                                                    $this->CourseSchedule->CourseSchedulesClassPeriod->create(
                                                                    );
                                                                    $this->CourseSchedule->CourseSchedulesClassPeriod->save(
                                                                        $associate
                                                                    );
                                                                }
                                                                $is_save = true;
                                                                $is_loop = false;
                                                            } else {
                                                                //Increment the week day  by one and try again
                                                                $is_loop = true;
                                                                $loop_count++;
                                                                //find the next week day assign to this college, program & program type
                                                                $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                    $next_week_day,
                                                                    $this->college_id,
                                                                    $selected_program,
                                                                    $selected_program_type
                                                                );
                                                                //Get list of class period of this week day,college,program and program type
                                                                $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                    $next_week_day,
                                                                    $this->college_id,
                                                                    $selected_program,
                                                                    $selected_program_type,
                                                                    $$publishedCourse_id,
                                                                    $selected_academic_year,
                                                                    $selected_semester,
                                                                    $publishedCourse_details['Section']['id'],
                                                                    'Lab'
                                                                );
                                                                //If list of class period is empty in the given week day try the next week day class period (this function try 7 time just to prevent infinite loop)
                                                                $loop = 1;
                                                                while (empty($list_of_class_period_ids) && $loop < 7) {
                                                                    $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                        $next_week_day,
                                                                        $this->college_id,
                                                                        $selected_program,
                                                                        $selected_program_type
                                                                    );
                                                                    $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                        $next_week_day,
                                                                        $this->college_id,
                                                                        $selected_program,
                                                                        $selected_program_type,
                                                                        $$publishedCourse_id,
                                                                        $selected_academic_year,
                                                                        $selected_semester,
                                                                        $publishedCourse_details['Section']['id'],
                                                                        'Lab'
                                                                    );
                                                                    $loop++;
                                                                }
//get a group of continuous class_period_lists per period number of hours
                                                                $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number(
                                                                    $list_of_class_period_ids,
                                                                    $period_number
                                                                );
                                                                if (!empty($sorted_potential_class_rooms)) {
                                                                    //get free class room from sorted_potential_class_rooms in a given periods
                                                                    $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                                        $continuous_grouped_class_period_list_into_period_number,
                                                                        $sorted_potential_class_rooms,
                                                                        $selected_academic_year,
                                                                        $selected_semester
                                                                    );
                                                                } else {
                                                                    $match_periods_and_class_room = array();
                                                                    if (!empty($continuous_grouped_class_period_list_into_period_number)) {
                                                                        $match_periods_and_class_room['class_periods'] = $continuous_grouped_class_period_list_into_period_number[(count(
                                                                                $continuous_grouped_class_period_list_into_period_number
                                                                            ) - 1)];
                                                                        $match_periods_and_class_room['class_room'] = null;
                                                                    }
                                                                }
                                                            }
                                                        } while ($is_loop == true && $loop_count < 7);
                                                        //store assignment failed courses per period number and course type for display.
                                                        if ($is_save == false) {
                                                            //$unschedule_published_course[$period_number]['Lecture'][] = $publishedCourse_id;
                                                            //$unschedule_published_course[$publishedCourse_details['Section']['id']][$split_section['id']]['Laboratory'][$period_number][$publishedCourse_id][] = "dueto unable to find free class period and instructor suitable time in specified class room or in availabel class room";
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_title'] = $publishedCourse_details['Course']['course_title'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_code'] = $publishedCourse_details['Course']['course_code'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['credit'] = $publishedCourse_details['Course']['credit'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['lecture_hours'] = $publishedCourse_details['Course']['lecture_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['tutorial_hours'] = $publishedCourse_details['Course']['tutorial_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['laboratory_hours'] = $publishedCourse_details['Course']['laboratory_hours'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['section_name'] = $publishedCourse_details['Section']['name'];
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_length'] = $period_number;
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_type'] = "Laboratory";
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['split_section_name'] = $this->CourseSchedule->CourseSplitSection->field(
                                                                'CourseSplitSection.section_name',
                                                                array('CourseSplitSection.id' => $split_section['id'])
                                                            );
                                                            $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['possible_reason'] = "Due to unable to find free class period and instructor suitable time in specified class room or in availabel class room";
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['published_course_id'] = $publishedCourse_id;
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['course_split_section_id'] = $split_section['id'];
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['period_length'] = $period_number;
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['type'] = "Laboratory";
                                                            $unscheduled_courses_reformate_for_save[$unscheduled_count]['description'] = "Due to unable to find free class period and instructor suitable time in specified class room or in availabel class room";
                                                            $unscheduled_count++;
                                                        }
                                                    }
                                                }
                                            } //end of froeach split section for Laboratory
                                        } //end of if seplit section for Laboratory
//If the section is not split for the given course
                                        else {
//get number of session and its period number
                                            $number_of_period_per_session = $this->CourseSchedule->get_number_period_per_session(
                                                $publishedCourse_details['Course']['laboratory_hours'],
                                                $publishedCourse_details['PublishedCourse']['lab_number_of_session']
                                            );
//schedule for each session
                                            foreach ($number_of_period_per_session as $npsk => $period_number) {
                                                $potential_class_period_ids_from_constranints = array();
                                                if (!empty($publishedCourse_details['ClassPeriodCourseConstraint'])) {
//find class period ids from class period course constranits that specified as assigned
                                                    foreach ($publishedCourse_details['ClassPeriodCourseConstraint'] as $classPeriodCourseConstraint) {
                                                        if ($classPeriodCourseConstraint['active'] == 1 && strcasecmp(
                                                                $classPeriodCourseConstraint['type'],
                                                                "Lab"
                                                            ) == 0) {
                                                            $potential_class_period_ids_from_constranints[] = $classPeriodCourseConstraint['class_period_id'];
                                                        }
                                                    }
                                                }
                                                if (!empty($potential_class_period_ids_from_constranints)) {
                                                    //Get Sorted potentialy assignable class rooms
                                                    $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms_from_constraint(
                                                        $publishedCourse_details,
                                                        $selected_program,
                                                        $selected_program_type
                                                    );
                                                    //get a group of continuous class_period_lists per period number of hours take the class period sepcified in class period course constraints as starting period.
                                                    $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number_from_constraint(
                                                        $period_number,
                                                        $potential_class_period_ids_from_constranints,
                                                        $publishedCourse_id,
                                                        $this->college_id,
                                                        $selected_program,
                                                        $selected_program_type,
                                                        $selected_academic_year,
                                                        $selected_semester,
                                                        $publishedCourse_details['Section']['id'],
                                                        "Lab"
                                                    );
                                                    //Get ready to assign, Matched class room (from sorted potential class rooms) and continuous grouped class periods (from the class period sepcified in class period course constraints)
                                                    if (!empty($sorted_potential_class_rooms)) {
                                                        //get free class room from sorted_potential_class_rooms in a given periods
                                                        $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                            $continuous_grouped_class_period_list_into_period_number,
                                                            $sorted_potential_class_rooms,
                                                            $selected_academic_year,
                                                            $selected_semester
                                                        );
                                                    } else {
                                                        $match_periods_and_class_room = array();
                                                        if (!empty($continuous_grouped_class_period_list_into_period_number)) {
                                                            $match_periods_and_class_room['class_periods'] = $continuous_grouped_class_period_list_into_period_number[(count(
                                                                    $continuous_grouped_class_period_list_into_period_number
                                                                ) - 1)];
                                                            $match_periods_and_class_room['class_room'] = null;
                                                        }
                                                    }
                                                    if (!empty($match_periods_and_class_room)) {
                                                        if (isset($schedule)) {
                                                            unset($schedule);
                                                        }
                                                        $schedule['class_room_id'] = $match_periods_and_class_room['class_room'];
                                                        $schedule['section_id'] = $publishedCourse_details['Section']['id'];
                                                        $schedule['published_course_id'] = $publishedCourse_id;
                                                        $schedule['academic_year'] = $selected_academic_year;
                                                        $schedule['semester'] = $selected_semester;
                                                        $schedule['type'] = "Lab";

                                                        $this->CourseSchedule->create();
                                                        $this->CourseSchedule->save($schedule);
                                                        //get saved course schedule id to save course id and class period id in associate table
                                                        $associate['course_schedule_id'] = $this->CourseSchedule->id;
                                                        foreach ($match_periods_and_class_room['class_periods'] as $class_period_id) {
                                                            $associate['class_period_id'] = $class_period_id;
                                                            $this->CourseSchedule->CourseSchedulesClassPeriod->create();
                                                            $this->CourseSchedule->CourseSchedulesClassPeriod->save(
                                                                $associate
                                                            );
                                                        }
                                                    } else {
                                                        //$unschedule_published_course[$publishedCourse_details['Section']['id']]['Laboratory'][$period_number][$publishedCourse_id][] = "dueto unable to find free class room and instructor suitable time in specified class period course constraints time";
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_title'] = $publishedCourse_details['Course']['course_title'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_code'] = $publishedCourse_details['Course']['course_code'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['credit'] = $publishedCourse_details['Course']['credit'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['lecture_hours'] = $publishedCourse_details['Course']['lecture_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['tutorial_hours'] = $publishedCourse_details['Course']['tutorial_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['laboratory_hours'] = $publishedCourse_details['Course']['laboratory_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['section_name'] = $publishedCourse_details['Section']['name'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_length'] = $period_number;
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_type'] = "Laboratory";
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['possible_reason'] = "Due to unable to find free class room and instructor suitable time in specified class period course constraint time";
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['published_course_id'] = $publishedCourse_id;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['course_split_section_id'] = null;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['period_length'] = $period_number;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['type'] = "Laboratory";
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['description'] = "Due to unable to find free class room and instructor suitable time in specified class period course constraint time";
                                                        $unscheduled_count++;
                                                    }
                                                } else {
//get section last assigned week day in selected academic year and semester
                                                    $last_assigned_week_day = $this->CourseSchedule->get_last_assigned_week_day(
                                                        $publishedCourse_details['Section']['id'],
                                                        $selected_academic_year,
                                                        $selected_semester
                                                    );
                                                    if (!empty($last_assigned_week_day)) {
                                                        $potential_week_day = $this->CourseSchedule->get_next_week_day_from_last_assigned_week_day(
                                                            $last_assigned_week_day,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type
                                                        );
                                                    } else {
                                                        //if last_assigned_week_day is empty it mean the published course is the first for assignment
                                                        //find take the first week day assign to this college, program & program type
                                                        $potential_week_day = $this->CourseSchedule->get_first_week_day(
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type
                                                        );
                                                    }
                                                    //Get list of class period of this week day,college,program and program type
                                                    $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                        $potential_week_day,
                                                        $this->college_id,
                                                        $selected_program,
                                                        $selected_program_type,
                                                        $publishedCourse_id,
                                                        $selected_academic_year,
                                                        $selected_semester,
                                                        $publishedCourse_details['Section']['id'],
                                                        'Lab'
                                                    );

                                                    //If list of class period is empty in the given week day try the next week day class period (this function try 7 time just to prevent infinite loop)
                                                    $loop = 1;
                                                    $next_week_day = $potential_week_day;
                                                    while (empty($list_of_class_period_ids) && $loop < 7) {
                                                        $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                            $next_week_day,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type
                                                        );
                                                        $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                            $next_week_day,
                                                            $this->college_id,
                                                            $selected_program,
                                                            $selected_program_type,
                                                            $publishedCourse_id,
                                                            $selected_academic_year,
                                                            $selected_semester,
                                                            $publishedCourse_details['Section']['id'],
                                                            'Lab'
                                                        );
                                                        $loop++;
                                                    }
                                                    //get a group of continuous class_period_lists per period number of hours.
                                                    $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number(
                                                        $list_of_class_period_ids,
                                                        $period_number
                                                    );

                                                    //Get Sorted potentialy assignable class rooms
                                                    $sorted_potential_class_rooms = $this->_get_sorted_potential_class_rooms_from_constraint(
                                                        $publishedCourse_details,
                                                        $selected_program,
                                                        $selected_program_type
                                                    );

                                                    if (!empty($sorted_potential_class_rooms)) {
                                                        //get free class room from sorted_potential_class_rooms in a given periods
                                                        $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                            $continuous_grouped_class_period_list_into_period_number,
                                                            $sorted_potential_class_rooms,
                                                            $selected_academic_year,
                                                            $selected_semester
                                                        );
                                                    } else {
                                                        $match_periods_and_class_room = array();
                                                        if (!empty($continuous_grouped_class_period_list_into_period_number)) {
                                                            $match_periods_and_class_room['class_periods'] = $continuous_grouped_class_period_list_into_period_number[(count(
                                                                    $continuous_grouped_class_period_list_into_period_number
                                                                ) - 1)];
                                                            $match_periods_and_class_room['class_room'] = null;
                                                        }
                                                    }
                                                    $is_loop = true;
                                                    $loop_count = 1;
                                                    $is_save = false;
                                                    do {
                                                        if (!empty($match_periods_and_class_room)) {
                                                            if (isset($schedule)) {
                                                                unset($schedule);
                                                            }
                                                            $schedule['class_room_id'] = $match_periods_and_class_room['class_room'];
                                                            $schedule['section_id'] = $publishedCourse_details['Section']['id'];
                                                            $schedule['published_course_id'] = $publishedCourse_id;
                                                            $schedule['academic_year'] = $selected_academic_year;
                                                            $schedule['semester'] = $selected_semester;
                                                            $schedule['type'] = "Lab";
                                                            $this->CourseSchedule->create();
                                                            $this->CourseSchedule->save($schedule);
                                                            //get saved course schedule id to save course id and class period id in associate table
                                                            $associate['course_schedule_id'] = $this->CourseSchedule->id;
                                                            foreach ($match_periods_and_class_room['class_periods'] as $class_period_id) {
                                                                $associate['class_period_id'] = $class_period_id;
                                                                $this->CourseSchedule->CourseSchedulesClassPeriod->create(
                                                                );
                                                                $this->CourseSchedule->CourseSchedulesClassPeriod->save(
                                                                    $associate
                                                                );
                                                            }
                                                            $is_save = true;
                                                            $is_loop = false;
                                                        } else {
                                                            //Increment the week day  by one and try again
                                                            $is_loop = true;
                                                            $loop_count++;
                                                            //find the next week day assign to this college, program & program type
                                                            $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                $next_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type
                                                            );
                                                            //Get list of class period of this week day,college,program and program type
                                                            $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                $next_week_day,
                                                                $this->college_id,
                                                                $selected_program,
                                                                $selected_program_type,
                                                                $publishedCourse_id,
                                                                $selected_academic_year,
                                                                $selected_semester,
                                                                $publishedCourse_details['Section']['id'],
                                                                'Lab'
                                                            );
                                                            //If list of class period is empty in the given week day try the next week day class period (this function try 7 time just to prevent infinite loop)
                                                            $loop = 1;
                                                            while (empty($list_of_class_period_ids) && $loop < 7) {
                                                                $next_week_day = $this->CourseSchedule->get_next_week_day(
                                                                    $next_week_day,
                                                                    $this->college_id,
                                                                    $selected_program,
                                                                    $selected_program_type
                                                                );
                                                                $list_of_class_period_ids = $this->CourseSchedule->get_list_of_class_period_id(
                                                                    $next_week_day,
                                                                    $this->college_id,
                                                                    $selected_program,
                                                                    $selected_program_type,
                                                                    $publishedCourse_id,
                                                                    $selected_academic_year,
                                                                    $selected_semester,
                                                                    $publishedCourse_details['Section']['id'],
                                                                    'Lab'
                                                                );
                                                                $loop++;
                                                            }
                                                            //get a group of continuous class_period_lists per period number of hours
                                                            $continuous_grouped_class_period_list_into_period_number = $this->CourseSchedule->get_continuous_grouped_class_period_list_into_period_number(
                                                                $list_of_class_period_ids,
                                                                $period_number
                                                            );
                                                            //get free class room from sorted_potential_class_rooms in a given periods
                                                            if (!empty($sorted_potential_class_rooms)) {
                                                                //get free class room from sorted_potential_class_rooms in a given periods
                                                                $match_periods_and_class_room = $this->CourseSchedule->get_match_periods_and_class_room(
                                                                    $continuous_grouped_class_period_list_into_period_number,
                                                                    $sorted_potential_class_rooms,
                                                                    $selected_academic_year,
                                                                    $selected_semester
                                                                );
                                                            } else {
                                                                $match_periods_and_class_room = array();
                                                                if (!empty($continuous_grouped_class_period_list_into_period_number)) {
                                                                    $match_periods_and_class_room['class_periods'] = $continuous_grouped_class_period_list_into_period_number[(count(
                                                                            $continuous_grouped_class_period_list_into_period_number
                                                                        ) - 1)];
                                                                    $match_periods_and_class_room['class_room'] = null;
                                                                }
                                                            }
                                                        }
                                                    } while ($is_loop == true && $loop_count < 7);
                                                    //store assignment failed courses per period number and course type for display.
                                                    if ($is_save == false) {
                                                        //$unschedule_published_course[$publishedCourse_details['Section']['id']]['Laboratory'][$period_number][$publishedCourse_id][] = "dueto unable to find free class period and instructor suitable time in specified class room or in availabel class room";
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_title'] = $publishedCourse_details['Course']['course_title'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['course_code'] = $publishedCourse_details['Course']['course_code'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['credit'] = $publishedCourse_details['Course']['credit'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['lecture_hours'] = $publishedCourse_details['Course']['lecture_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['tutorial_hours'] = $publishedCourse_details['Course']['tutorial_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['laboratory_hours'] = $publishedCourse_details['Course']['laboratory_hours'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['section_name'] = $publishedCourse_details['Section']['name'];
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_length'] = $period_number;
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['period_type'] = "Laboratory";
                                                        $unschedule_published_course[$publishedCourse_id][$unscheduled_count]['possible_reason'] = "Due to unable to find free class period and instructor suitable time in specified class room or in availabel class room";
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['published_course_id'] = $publishedCourse_id;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['course_split_section_id'] = null;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['period_length'] = $period_number;
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['type'] = "Laboratory";
                                                        $unscheduled_courses_reformate_for_save[$unscheduled_count]['description'] = "Due to unable to find free class period and instructor suitable time in specified class room or in availabel class room";
                                                        $unscheduled_count++;
                                                    }
                                                }
                                            }
                                        } //end of else unsplited section
                                    } // end of if of lecture hours for Laboratory
                                } // end of foreach loop of sorted published course for Laboratory
//debug($unschedule_published_course);
                                if (empty($unschedule_published_course)) {
                                    $this->Session->setFlash(
                                        '<span></span> ' . __('The course schedule generated successfully.'),
                                        'default',
                                        array('class' => 'success-box success-message')
                                    );
                                } else {
                                    $this->Session->write('selected_academic_year', $selected_academic_year);
                                    $this->Session->write('selected_semester', $selected_semester);
                                    $this->Session->write('selected_program', $selected_program);
                                    $this->Session->write('selected_program_type', $selected_program_type);
                                    $this->Session->write('unschedule_published_course', $unschedule_published_course);
//$this->Session->setFlash('<span></span> '.__('The Course Schedule generate with some unscheduled course.'),'default',array('class'=>'info-box info-message'));
                                    $this->Session->setFlash(
                                        '<span></span> The Course Schedule generated with some unscheduled course. Please schedule those courses manually or regenerate the schedule after adjusting the course schedule settings and constraints. You can find possible causes for unschedule courses in ',
                                        "session_flash_link",
                                        array(
                                            "class" => 'warning-box warning-message',
                                            "link_text" => " this page",
                                            "link_url" => array(
                                                "controller" => "course_schedules",
                                                "action" => "unschedule_courses_possible_causes",
                                                "admin" => false
                                            )
                                        )
                                    );

                                    ClassRegistry::init('UnschedulePublishedCourse')->saveAll(
                                        $unscheduled_courses_reformate_for_save,
                                        array('validate' => false)
                                    );
                                }
                                $sections = $this->CourseSchedule->get_course_schedule_sections(
                                    $conditions,
                                    $selected_academic_year,
                                    $selected_semester
                                );

                                $section_course_schedule = array();
                                foreach ($sections as $sectionValue) {
                                    $section_course_schedule[$sectionValue['id']] = $this->CourseSchedule->get_section_course_schedule(
                                        $sectionValue['id'],
                                        $selected_academic_year,
                                        $selected_semester
                                    );
                                }

                                $starting_and_ending_hour = $this->CourseSchedule->get_starting_and_ending_hour(
                                    $this->college_id,
                                    $selected_program,
                                    $selected_program_type
                                );

                                $this->set(compact('section_course_schedule', 'starting_and_ending_hour'));
                            } else {
                                $this->Session->setFlash(
                                    '<span></span> ' . __(
                                        'There is no published course in the selected criteria to be scheduled.'
                                    ),
                                    'default',
                                    array('class' => 'error-box error-message')
                                );
                            }
                        } else {
                            $sectionName = null;
                            foreach ($sections as $section) {
                                $sectionName = $sectionName . ', ' . $section['name'];
                            }
                            $this->Session->setFlash(
                                '<span></span> ' . __(
                                    'There is already course schedule for sections ' . $sectionName . ' in the selected academic year and semester. To generate fresh course schedule please delete those sections course schedule first.'
                                ),
                                'default',
                                array('class' => 'error-box error-message')
                            );

                            $section_course_schedule = array();
                            foreach ($sections as $sectionValue) {
                                $section_course_schedule[$sectionValue['id']] = $this->CourseSchedule->get_section_course_schedule(
                                    $sectionValue['id'],
                                    $selected_academic_year,
                                    $selected_semester
                                );
                            }
                            $starting_and_ending_hour = $this->CourseSchedule->get_starting_and_ending_hour(
                                $this->college_id,
                                $selected_program,
                                $selected_program_type
                            );

                            $this->set(compact('section_course_schedule', 'starting_and_ending_hour'));
                        }
                    } else {
                        $this->Session->setFlash(
                            '<span></span> ' . __(
                                'There is no class room assigned to the selected college, program and program type. Please assign class room first for this college_id, program and program type.'
                            ),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                        return $this->redirect(
                            array('controller' => 'classRoomBlocks', 'action' => 'assign_program_program_type')
                        );
                    }
                } else {
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'There is no class period set to the selected college, program and program type. Please set class period first for this college_id, program and program type.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    return $this->redirect(array('controller' => 'classPeriods', 'action' => 'add'));
                }

                $this->set(
                    compact(
                        'yearLevels',
                        'selected_academic_year',
                        'selected_program',
                        'selected_program_type',
                        'selected_department',
                        'selected_year_level',
                        'selected_semester'
                    )
                );
            }
        }
        if (!empty($this->request->data) && isset($this->request->data['cancel'])) {
            $selected_academic_year = $this->request->data['CourseSchedule']['acadamic_year'];
            $this->Session->write('selected_academic_year', $selected_academic_year);
            $selected_program = $this->request->data['CourseSchedule']['program_id'];
            $this->Session->write('selected_program', $selected_program);
            $selected_program_type = $this->request->data['CourseSchedule']['program_type_id'];
            $this->Session->write('selected_program_type', $selected_program_type);
            $selected_department = $this->request->data['CourseSchedule']['department_id'];
            $this->Session->write('selected_department', $selected_department);
            $selected_year_level = $this->request->data['CourseSchedule']['year_level_id'];
            $this->Session->write('selected_year_level', $selected_year_level);
            $selected_semester = $this->request->data['CourseSchedule']['semester'];
            $this->Session->write('selected_semester', $selected_semester);

            $yearLevels = $this->_get_year_levels_list($selected_department);


            if ($selected_department == "pre") {
                $conditions = array(
                    'PublishedCourse.academic_year' => $selected_academic_year,
                    'PublishedCourse.program_id' => $selected_program,
                    'PublishedCourse.program_type_id' => $selected_program_type,
                    'PublishedCourse.college_id' => $this->college_id,
                    'PublishedCourse.semester' => $selected_semester,
                    'PublishedCourse.drop' => 0,
                    "OR" => array(
                        "PublishedCourse.department_id is null",
                        "PublishedCourse.department_id" => array(0, '')
                    )
                );
            } else {
                $conditions = array(
                    'PublishedCourse.academic_year' => $selected_academic_year,
                    'PublishedCourse.program_id' => $selected_program,
                    'PublishedCourse.program_type_id' => $selected_program_type,
                    'PublishedCourse.department_id' => $selected_department,
                    'PublishedCourse.year_level_id' => $selected_year_level,
                    'PublishedCourse.semester' => $selected_semester,
                    'PublishedCourse.drop' => 0
                );
            }
//Get published course ids that have course schedule in the selected criteria.
            $published_course_ids = $this->CourseSchedule->PublishedCourse->find(
                'list',
                array('conditions' => $conditions, 'fields' => array('PublishedCourse.id', 'PublishedCourse.id'))
            );
//Get all course schedule id in the selected criteria
            $course_schedule_ids = $this->CourseSchedule->find(
                'list',
                array(
                    'fields' => array('CourseSchedule.id', 'CourseSchedule.id'),
                    'conditions' => array(
                        'CourseSchedule.published_course_id' => $published_course_ids,
                        'CourseSchedule.academic_year' => $selected_academic_year,
                        'CourseSchedule.semester' => $selected_semester
                    )
                )
            );
// during cancellation delete unschedule courses too.
            $unschedule_courses_ids = $this->CourseSchedule->PublishedCourse->UnschedulePublishedCourse->find(
                'list',
                array(
                    'conditions' => array(
                        'UnschedulePublishedCourse.published_course_id' =>
                            $published_course_ids
                    ),
                    'fields' => array('id')
                )
            );


//Get all course schedule class period id of all fiven course schedule
            $course_schedule_class_period_ids = $this->CourseSchedule->CourseSchedulesClassPeriod->find(
                'list',
                array(
                    'fields' => array('CourseSchedulesClassPeriod.id', 'CourseSchedulesClassPeriod.id'),
                    'conditions' => array('CourseSchedulesClassPeriod.course_schedule_id' => $course_schedule_ids)
                )
            );

// Check whether the course schedule used in attendance or not
            $course_schedule_published_course_ids = $this->CourseSchedule->find(
                'list',
                array(
                    'fields' => array('CourseSchedule.published_course_id', 'CourseSchedule.published_course_id'),
                    'conditions' => array('CourseSchedule.id' => $course_schedule_ids)
                )
            );
            $is_the_course_schedule_used = $this->CourseSchedule->PublishedCourse->Attendance->is_course_schedule_uesd_in_attendance(
                $course_schedule_published_course_ids
            );
            if (empty($is_the_course_schedule_used)) {
//Delete course schedule from course schedule class period habtm table
                $this->CourseSchedule->CourseSchedulesClassPeriod->deleteAll(
                    array('CourseSchedulesClassPeriod.id' => $course_schedule_class_period_ids),
                    false
                );

                $this->CourseSchedule->PublishedCourse->UnschedulePublishedCourse->deleteAll(
                    array('UnschedulePublishedCourse.id' => $unschedule_courses_ids),
                    false
                );
//Delete Course Schedule from course schedule table
                $this->CourseSchedule->deleteAll(array('CourseSchedule.id' => $course_schedule_ids), false);
                $this->Session->setFlash(
                    '<span></span> ' . __('The Course schedule successfully canceled.'),
                    'default',
                    array('class' => 'success-box success-message')
                );
            } else {
                $is_from_cancel = 1;
                $this->Session->setFlash(
                    '<span></span> ' . __('You can not cancel this course schedule since it used in attendance.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
                return $this->redirect(array('action' => 'generate', $is_from_cancel));
            }
            $this->set(
                compact(
                    'yearLevels',
                    'selected_academic_year',
                    'selected_program',
                    'selected_program_type',
                    'selected_department',
                    'selected_year_level',
                    'selected_semester'
                )
            );
        }
        if ($this->Session->read('selected_department')) {
            $selected_department = $this->Session->read('selected_department');
        } else {
            if (!empty($this->request->data['CourseSchedule']['department_id'])) {
                $selected_department = $this->request->data['CourseSchedule']['department_id'];
            }
        }

        if (!empty($selected_department)) {
            $yearLevels = $this->_get_year_levels_list($selected_department);
        } else {
            $yearLevels = null;
        }


        $departments = $this->CourseSchedule->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $this->college_id))
        );
        $max_year_level = ClassRegistry::init('YearLevel')->get_department_max_year_level($departments);
        unset($yearLevels);

        for ($i = 1; $i <= $max_year_level; $i++) {
            $yr = $i . ($i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')));
            $yearLevels[$yr] = $yr;
        }

        $departments['pre'] = 'Pre/(Unassign Freshman)';
        $programs = $this->CourseSchedule->PublishedCourse->Program->find('list');
        $programTypes = $this->CourseSchedule->PublishedCourse->ProgramType->find('list');

        $this->set(compact('departments', 'programs', 'programTypes', 'yearLevels'));
    }

    public function get_departments($college_id = null)
    {

        if (!empty($college_id)) {
            $this->layout = 'ajax';
            $departments = $this->CourseSchedule->PublishedCourse->Department->find(
                'list',
                array('conditions' => array('Department.college_id' => $college_id))
            );
            $departments['pre'] = 'Pre/(Unassign Freshman)';

            $this->set(compact('departments'));
        }
    }

    public function get_year_levels($department_id = null)
    {

        if (!empty($department_id)) {
            $this->layout = 'ajax';
            $yearLevels = $this->_get_year_levels_list($department_id);
            $this->set(compact('yearLevels'));
        }
    }

    public function _get_year_levels_list($department_id = null)
    {

        if (!empty($department_id)) {
            if ($department_id == "pre") {
                $yearLevels["pre"] = '1st';
            } else {
                $yearLevels = $this->CourseSchedule->PublishedCourse->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $department_id))
                );
            }

            return $yearLevels;
        }
    }

//find sorted potentialy assignable class rooms
    public function _get_sorted_potential_class_rooms(
        $publishedCourse_details = null,
        $selected_program = null,
        $selected_program_type = null,
        $course_type = null
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
                } else {
                    if ($classRoomConstraint['active'] == 0 && strcasecmp(
                            $classRoomConstraint['type'],
                            $course_type
                        ) == 0) {
                        $class_room_constraint_stated_donot_assign[] = $classRoomConstraint['class_room_id'];
                    }
                }
            }
        }
        $total_active_students_of_this_section = $this->CourseSchedule->Section->get_tottal_active_students_of_the_section(
            $publishedCourse_details['Section']['id']
        );
        $sorted_potential_class_rooms = null;
        if (!empty($class_room_constraint_assign)) {
//sort class rooms set as assignable constraint  based on the the capacity of room and number of students in the section.
            $sorted_potential_class_rooms = $this->CourseSchedule->sort_potential_class_rooms(
                $total_active_students_of_this_section,
                $class_room_constraint_assign
            );
//get free class room from sorted_potential_class_rooms in a given periods
        } else {
//Get class Rooms assigned for the program and program type by excluding class room that stated as do not assign in class room constraints table
            $potential_class_rooms = $this->CourseSchedule->get_potential_class_rooms(
                $this->college_id,
                $selected_program,
                $selected_program_type,
                $class_room_constraint_stated_donot_assign
            );
//sort potential class rooms based on the the capacity of room and number of students in the section.
            $sorted_potential_class_rooms = $this->CourseSchedule->sort_potential_class_rooms(
                $total_active_students_of_this_section,
                $potential_class_rooms
            );
        }

        return $sorted_potential_class_rooms;
    }

//find sorted potentialy assignable class rooms from class room constraints
    public function _get_sorted_potential_class_rooms_from_constraint(
        $publishedCourse_details = null,
        $selected_program = null,
        $selected_program_type = null
    ) {

//Class room specified as assign in class room constraints for Laboratory
        $class_room_constraint_assign = array();

        if (!empty($publishedCourse_details['ClassRoomCourseConstraint'])) {
            foreach ($publishedCourse_details['ClassRoomCourseConstraint'] as $classRoomConstraint) {
                if ($classRoomConstraint['active'] == 1 && strcasecmp($classRoomConstraint['type'], "Lab") == 0) {
                    $class_room_constraint_assign[] = $classRoomConstraint['class_room_id'];
                }
            }
        }
        $total_active_students_of_this_section = $this->CourseSchedule->Section->get_tottal_active_students_of_the_section(
            $publishedCourse_details['Section']['id']
        );
        $sorted_potential_class_rooms = null;
        if (!empty($class_room_constraint_assign)) {
//sort class rooms set as assignable constraint  based on the the capacity of room and number of students in the section.
            $sorted_potential_class_rooms = $this->CourseSchedule->sort_potential_class_rooms(
                $total_active_students_of_this_section,
                $class_room_constraint_assign
            );
        }

        return $sorted_potential_class_rooms;
    }

    public function get_modal($published_course_id = null)
    {

        $this->layout = 'ajax';
        if (!empty($published_course_id)) {
//get publishedcourse details
            $publishedCourse_details = $this->CourseSchedule->get_published_course_details($published_course_id);
            $formatted_published_course_detail = array();
            $formatted_published_course_detail['course_code'] = $publishedCourse_details['Course']['course_code'];
            $formatted_published_course_detail['course_name'] = $publishedCourse_details['Course']['course_title'];
//Instructor assigned for lecture
            if ($publishedCourse_details['Course']['lecture_hours'] != 0) {
                if (!empty($publishedCourse_details['CourseInstructorAssignment'])) {
                    $is_instructor_assigned = false;
                    foreach ($publishedCourse_details['CourseInstructorAssignment'] as $assigned_instructor) {
                        if (strcasecmp($assigned_instructor['type'], 'Lecture') == 0 || strcasecmp(
                                $assigned_instructor['type'],
                                'Lecture+Tutorial'
                            ) == 0 || strcasecmp($assigned_instructor['type'], 'Lecture+Lab') == 0) {
                            if (isset($formatted_published_course_detail['lecture'])) {
                                $formatted_published_course_detail['lecture'] = $formatted_published_course_detail['lecture'] . ', ' . $assigned_instructor['Staff']['Title']['title'] . ' ' . $assigned_instructor['Staff']['full_name'];
                            } else {
                                $formatted_published_course_detail['lecture'] = $assigned_instructor['Staff']['Title']['title'] . ' ' . $assigned_instructor['Staff']['full_name'];
                            }
                            $is_instructor_assigned = true;
                        }
                    }
                    if ($is_instructor_assigned == false) {
                        $formatted_published_course_detail['lecture'] = "TBA";
                    }
                } else {
                    $formatted_published_course_detail['lecture'] = "TBA";
                }
            }

//Instructor assigned for Tutorial
            if ($publishedCourse_details['Course']['tutorial_hours'] != 0) {
                if (!empty($publishedCourse_details['CourseInstructorAssignment'])) {
                    $is_instructor_assigned = false;
                    foreach ($publishedCourse_details['CourseInstructorAssignment'] as $assigned_instructor) {
                        if (strcasecmp($assigned_instructor['type'], 'Tutorial') == 0 || strcasecmp(
                                $assigned_instructor['type'],
                                'Lecture+Tutorial'
                            ) == 0) {
                            if (isset($formatted_published_course_detail['tutorial'])) {
                                $formatted_published_course_detail['tutorial'] = $formatted_published_course_detail['tutorial'] . ', ' . $assigned_instructor['Staff']['Title']['title'] . ' ' . $assigned_instructor['Staff']['full_name'];
                            } else {
                                $formatted_published_course_detail['tutorial'] = $assigned_instructor['Staff']['Title']['title'] . ' ' . $assigned_instructor['Staff']['full_name'];
                            }
                            $is_instructor_assigned = true;
                        }
                    }
                    if ($is_instructor_assigned == false) {
                        $formatted_published_course_detail['tutorial'] = "TBA";
                    }
                } else {
                    $formatted_published_course_detail['tutorial'] = "TBA";
                }
            }
//Instructor assigned for Laboratory
            if ($publishedCourse_details['Course']['laboratory_hours'] != 0) {
                if (!empty($publishedCourse_details['CourseInstructorAssignment'])) {
                    $is_instructor_assigned = false;
                    foreach ($publishedCourse_details['CourseInstructorAssignment'] as $assigned_instructor) {
                        if (strcasecmp($assigned_instructor['type'], 'Lab') == 0 || strcasecmp(
                                $assigned_instructor['type'],
                                'Lecture+Lab'
                            ) == 0) {
                            if (isset($formatted_published_course_detail['lab'])) {
                                $formatted_published_course_detail['lab'] = $formatted_published_course_detail['lab'] . ', ' . $assigned_instructor['Staff']['Title']['title'] . ' ' . $assigned_instructor['Staff']['full_name'];
                            } else {
                                $formatted_published_course_detail['lab'] = $assigned_instructor['Staff']['Title']['title'] . ' ' . $assigned_instructor['Staff']['full_name'];
                            }
                            $is_instructor_assigned = true;
                        }
                    }
                    if ($is_instructor_assigned == false) {
                        $formatted_published_course_detail['lab'] = "TBA";
                    }
                } else {
                    $formatted_published_course_detail['lab'] = "TBA";
                }
            }
            $this->set(compact('formatted_published_course_detail'));
        }
    }

    public function unschedule_courses_possible_causes()
    {

        $selected_academic_year = $this->Session->read('selected_academic_year');
        $selected_semester = $this->Session->read('selected_semester');
        $selected_program = $this->Session->read('selected_program');
        $selected_program_name = $this->CourseSchedule->PublishedCourse->Program->field(
            'Program.name',
            array('Program.id' => $selected_program)
        );
        $selected_program_type = $this->Session->read('selected_program_type');
        $selected_program_type_name = $this->CourseSchedule->PublishedCourse->ProgramType->field(
            'ProgramType.name',
            array('ProgramType.id' => $selected_program_type)
        );

        $unschedule_published_courses = $this->Session->read('unschedule_published_course');

        $this->set(
            compact(
                'unschedule_published_courses',
                'selected_academic_year',
                'selected_semester',
                'selected_program_name',
                'selected_program_type_name'
            )
        );
    }

    public function manual_update_schedule($from_change = null)
    {

        $this->__init_search();
        if ($this->Session->read('search_data')) {
            $this->request->data['search'] = true;
        }
        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['Search']['academic_year']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select academic year that you want to manually  schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['program_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select program that you want to manually course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select program type that you want manually course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['department_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select department that you want manually course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['year_level_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select Year Level that you want manually course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select semester that you want to manually course schedule. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                default:
                    $everythingfine = true;
            }
            if ($everythingfine) {
                //$this->__init_search();
                $selected_academic_year = $this->request->data['Search']['academic_year'];
                $this->Session->write('selected_academic_year', $selected_academic_year);
                $selected_program = $this->request->data['Search']['program_id'];
                $this->Session->write('selected_program', $selected_program);
                $selected_program_type = $this->request->data['Search']['program_type_id'];
                $this->Session->write('selected_program_type', $selected_program_type);
                $selected_department = $this->request->data['Search']['department_id'];
                $this->Session->write('selected_department', $selected_department);
                $selected_year_level = $this->request->data['Search']['year_level_id'];
                $this->Session->write('selected_year_level', $selected_year_level);
                $selected_semester = $this->request->data['Search']['semester'];
                $this->Session->write('selected_semester', $selected_semester);

                $yearLevels = $this->_get_year_levels_list($selected_department);


                if ($this->request->data['Search']['department_id'] == "pre") {
                    $conditions = array(
                        'PublishedCourse.academic_year' => $this->request->data['Search']['academic_year'],
                        'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                        'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id'],
                        'PublishedCourse.college_id' => $this->college_id,
                        'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                        'PublishedCourse.drop' => 0,
                        "OR" => array(
                            "PublishedCourse.department_id is null",
                            "PublishedCourse.department_id" => array(0, '')
                        )
                    );
                } else {
                    $conditions = array(
                        'PublishedCourse.academic_year' => $this->request->data['Search']['academic_year'],
                        'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                        'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id'],
                        'PublishedCourse.department_id' => $this->request->data['Search']['department_id'],
                        'PublishedCourse.year_level_id' => $this->request->data['Search']['year_level_id'],
                        'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                        'PublishedCourse.drop' => 0
                    );
                }
//Get published course ids that have course schedule in the selected criteria.
                $published_course_ids = $this->CourseSchedule->PublishedCourse->find(
                    'list',
                    array('conditions' => $conditions, 'fields' => array('PublishedCourse.id', 'PublishedCourse.id'))
                );


                $unschedule_published_courses = $this->CourseSchedule->PublishedCourse->UnschedulePublishedCourse->find(
                    'all',
                    array(
                        'conditions' => array('UnschedulePublishedCourse.published_course_id' => $published_course_ids),
                        'contain' => array('PublishedCourse' => array('Course'))
                    )
                );

                $sections = $this->CourseSchedule->get_course_schedule_sections(
                    $conditions,
                    $this->request->data['Search']['academic_year'],
                    $this->request->data['Search']['semester']
                );
                if (!empty($sections)) {
                    $section_course_schedule = array();
                    foreach ($sections as $sectionValue) {
                        $section_course_schedule[$sectionValue['id']] = $this->CourseSchedule->get_section_course_schedule(
                            $sectionValue['id'],
                            $this->request->data['Search']['academic_year'],
                            $this->request->data['Search']['semester']
                        );
                    }
                    $section_unscheduled_courses = array();
                    foreach ($unschedule_published_courses as $inn => $unscheduled_value) {
                        $section_unscheduled_courses[$unscheduled_value['PublishedCourse']['section_id']][] = $unscheduled_value;
                    }

                    $starting_and_ending_hour = $this->CourseSchedule->get_starting_and_ending_hour(
                        $this->college_id,
                        $this->request->data['Search']['program_id'],
                        $this->request->data['Search']['program_type_id']
                    );

                    $this->set(compact('section_course_schedule', 'starting_and_ending_hour'));
                    //Get all course schedule id in the selected criteria
                    $scheduled_courses = $this->CourseSchedule->find('all', array(
                        'conditions' => array(
                            'CourseSchedule.published_course_id' => $published_course_ids,
                            'CourseSchedule.academic_year' => $this->request->data['Search']['academic_year']
                        ,
                            'CourseSchedule.semester' => $this->request->data['Search']['semester']
                        ),
                        'contain' => array('PublishedCourse' => array('Course'), 'ClassRoom', 'Section')
                    ));
                    $this->set(compact('scheduled_courses', 'section_unscheduled_courses'));

                    $this->set(compact('yearLevels'));
                } else {
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'There is no schedule in the selected criteria that needs manaul adjustment or change.Please generate schedule using schedule generat tool then you can modify it using this tool. '
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                }
            }
        }
        $departments = $this->CourseSchedule->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $this->college_id))
        );
        $departments['pre'] = 'Pre/Freshman';
        $programs = $this->CourseSchedule->PublishedCourse->Program->find('list');
        $programTypes = $this->CourseSchedule->PublishedCourse->ProgramType->find('list');
        $this->set(compact('departments', 'programs', 'programTypes'));
    }

    /**
     *Update the schedule change
     */
    public function schedule_change_update()
    {

        if (!empty($this->request->data)) {
            if (!empty($this->request->data['CourseSchedule']['class_period_id']) &&
                !empty($this->request->data['CourseSchedule']['class_room_id'])) {
                $class_period_id = explode('~', $this->request->data['CourseSchedule']['class_period_id']);
                $this->request->data['CourseSchedule']['id'] = $this->request->data['CourseSchedule']['id'];
                $this->request->data['CourseSchedule']['class_room_id'] = $this->request->data['CourseSchedule']['class_room_id'];
                $this->request->data['CourseSchedule']['is_auto'] = 0;

                $course_schedule_class_period_ids = $this->CourseSchedule->
                CourseSchedulesClassPeriod->find(
                    'list',
                    array(
                        'fields' => array(
                            'CourseSchedulesClassPeriod.id',
                            'CourseSchedulesClassPeriod.id'
                        ),
                        'conditions' => array(
                            'CourseSchedulesClassPeriod.course_schedule_id' =>
                                $this->request->data['CourseSchedule']['id']
                        )
                    )
                );

                if ($this->CourseSchedule->save($this->request->data)) {
                    $this->CourseSchedule->CourseSchedulesClassPeriod->deleteAll(
                        array('CourseSchedulesClassPeriod.id' => $course_schedule_class_period_ids),
                        false
                    );

                    $count = 0;
                    $associate = array();
                    foreach ($class_period_id as $k => $class_per_id) {
                        if (!empty($class_per_id)) {
                            $associate['CourseSchedulesClassPeriod'][$count]['class_period_id']
                                = $class_per_id;
                            $associate['CourseSchedulesClassPeriod'][$count]['course_schedule_id']
                                = $this->CourseSchedule->id;
                            $count++;
                        }
                    }

                    $this->CourseSchedule->CourseSchedulesClassPeriod->create();
                    if ($this->CourseSchedule->CourseSchedulesClassPeriod->saveAll(
                        $associate['CourseSchedulesClassPeriod'],
                        array('validate' => false)
                    )) {
                        $this->Session->setFlash(
                            __('<span></span> Course Schedule is updated.'),
                            'default',
                            array('class' => 'success-box success-message')
                        );
                    }
                }
            } else {
                $this->Session->setFlash(
                    __(
                        '<span></span>
Please select the class period and room  you want to change the section schedule.',
                        true
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
            $this->redirect(array('action' => 'manual_update_schedule'));
        }
    }

    /**
     * ajax to populate drop down of rooms and class periods
     */
    public function change_schedule($course_schedule_id = null, $class_period_id = null)
    {

        $this->layout = 'ajax';


        $course_schedules = $this->CourseSchedule->find(
            'first',
            array(
                'conditions' => array('CourseSchedule.id' => $course_schedule_id),
                'contain' => array('ClassRoom', 'PublishedCourse', 'ClassPeriod' => array('PeriodSetting'))
            )
        );

        $semester = $course_schedules['CourseSchedule']['semester'];
        $academic_year = str_replace('/', '-', $course_schedules['CourseSchedule']['academic_year']);
        $published_course_id = $course_schedules['CourseSchedule']['published_course_id'];
        $type = $course_schedules['CourseSchedule']['type'];
        $get_potential_class_periods_section_is_free =
            $this->CourseSchedule->getPotentialClassPeriods($course_schedules, $this->college_id);
        debug($get_potential_class_periods_section_is_free);

        $this->set(
            compact(
                'course_schedules',
                'course_schedule_id',
                'type',
                'published_course_id',
                'semester',
                'academic_year',
                'get_potential_class_periods_section_is_free'
            )
        );
    }

    public function __init_search()
    {

        // We create a search_data session variable when we fill any criteria
        // in the search form.
        if (!empty($this->request->data['Search'])) {
            $search_session = $this->request->data['Search'];
            // Session variable 'search_data'
            $this->Session->write('search_data', $search_session);
        } else {
            $search_session = $this->Session->read('search_data');
            $this->request->data['Search'] = $search_session;
        }
    }

    /**
     * ajax to populate drop down of rooms and class periods
     */
    public function manual_schedule_unscheduled($published_course_id = null, $type = null)
    {

        $this->layout = 'ajax';
        $published_course_details = $this->CourseSchedule->PublishedCourse->find(
            'first',
            array('conditions' => array('PublishedCourse.id' => $published_course_id), 'recursive' => -1)
        );
        //$academic_year=$published_course_details['PublishedCourse']['academic_year'];
        $semester = $published_course_details['PublishedCourse']['semester'];
        $academic_year = str_replace('/', '-', $published_course_details['PublishedCourse']['academic_year']);

        $get_potential_class_periods_section_is_free =
            $this->CourseSchedule->getPotentialClassPeriodsForUnscheduledCourses(
                $published_course_id,
                $this->college_id,
                $type
            );
        $this->set(
            compact(
                'get_potential_class_periods_section_is_free',
                'semester',
                'academic_year',
                'published_course_id',
                'type'
            )
        );
    }

    /**
     *Update the schedule change
     */
    public function schedule_unscheduled_course()
    {

        if (!empty($this->request->data)) {
            if (!empty($this->request->data['CourseSchedule']['class_period_id']) &&
                !empty($this->request->data['CourseSchedule']['class_room_id'])) {
                $class_period_id = explode('~', $this->request->data['CourseSchedule']['class_period_id']);
                $this->request->data['CourseSchedule']['published_course_id'] = $this->request->data['CourseSchedule']['published_course_id'];
                $this->request->data['CourseSchedule']['class_room_id'] = $this->request->data['CourseSchedule']['class_room_id'];
                $this->request->data['CourseSchedule']['type'] = $this->request->data['CourseSchedule']['type'];
                $this->request->data['CourseSchedule']['is_auto'] = 0;


                if ($this->CourseSchedule->save($this->request->data)) {
                    $count = 0;
                    $associate = array();
                    foreach ($class_period_id as $k => $class_per_id) {
                        if (!empty($class_per_id)) {
                            $associate['CourseSchedulesClassPeriod'][$count]['class_period_id']
                                = $class_per_id;
                            $associate['CourseSchedulesClassPeriod'][$count]['course_schedule_id'] = $this->CourseSchedule->id;
                            $count++;
                        }
                    }

                    $this->CourseSchedule->CourseSchedulesClassPeriod->create();
                    if ($this->CourseSchedule->CourseSchedulesClassPeriod->saveAll(
                        $associate['CourseSchedulesClassPeriod'],
                        array('validate' => false)
                    )) {
                        $this->Session->setFlash(
                            __(
                                '<span></span>
Course Schedule is scheduled manaully.',
                                true
                            ),
                            'default',
                            array('class' => 'success-box success-message')
                        );
                        $unscheduled_id = $this->CourseSchedule->PublishedCourse->UnschedulePublishedCourse->field(
                            'id',
                            array('published_course_id' => $this->request->data['CourseSchedule']['published_course_id'])
                        );
                        $delete_from_unscheduled_table = $this->CourseSchedule->PublishedCourse->
                        UnschedulePublishedCourse->delete($unscheduled_id);
                    } else {
                        $this->Session->setFlash(
                            __('<span></span> The course schedule could not be saved. Please try again.'),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                        $this->CourseSchedule->delet($this->CourseSchedule->id);
                    }
                }
            } else {
                $this->Session->setFlash(
                    __(
                        '<span></span>
Please select the class period and room  you want to schedule the selected published course',
                        true
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
            $this->redirect(array('action' => 'manual_update_schedule'));
        }
    }

    public function get_potential_class_rooms_combo(
        $period_ids = null,
        $academic_year = null,
        $semester = null,
        $type = null
    ) {

        $this->layout = 'ajax';

        $acadamic_year = str_replace('-', '/', $academic_year);
        $class_period_ids = explode('~', $period_ids);
        $rooms = $this->CourseSchedule->getFreePotentialClassRooms(
            $class_period_ids,
            $acadamic_year,
            $semester,
            $this->college_id,
            $type
        );
        $this->set(compact('rooms'));
    }

    public function instructor_free_occupied($period_ids = null, $published_course_ids = null)
    {

        $this->layout = 'ajax';

        $class_period_ids = explode('~', $period_ids);
        $instructors = $this->CourseSchedule->isInstructorFreeOnSelectedPeriod(
            $class_period_ids,
            $published_course_ids
        );

        $this->set(compact('instructors'));
    }

    public function _GetCollegeDepartmentYearLevelIds($yearLevelName, $college_ids)
    {

        $departmentIds = $this->CourseSchedule->PublishedCourse->Department->find('list', array(
            'conditions' => array('Department.college_id' => $college_ids),
            'fields' => array('Department.id')
        ));
        $yearLevels = $this->CourseSchedule->PublishedCourse->YearLevel->find('list', array(
            'conditions' => array(
                'YearLevel.department_id' => $departmentIds,
                'YearLevel.name' => $yearLevelName
            ),
            'fields' => array('YearLevel.id')
        ));
        return $yearLevels;
    }

}

<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class CourseExamConstraintsController extends AppController
{

    public $name = 'CourseExamConstraints';
    public $components = array('AcademicYear');

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
        $this->Auth->allow('get_year_level', 'get_course_exam_constraints_details');
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

        $this->CourseExamConstraint->recursive = 0;
        $this->set('courseExamConstraints', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid course exam constraint'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('courseExamConstraint', $this->CourseExamConstraint->read(null, $id));
    }

    public function add()
    {

        $from_delete = $this->Session->read('from_delete');
        if ($from_delete != 1) {
            if ($this->Session->read('selected_academicyear')) {
                $this->Session->delete('selected_academicyear');
            }
            if ($this->Session->read('selected_program')) {
                $this->Session->delete('selected_program');
            }
            if ($this->Session->read('selected_program_type')) {
                $this->Session->delete('selected_program_type');
            }
            if ($this->Session->read('selected_semester')) {
                $this->Session->delete('selected_semester');
            }
            if ($this->Session->read('selected_year_level')) {
                $this->Session->delete('selected_year_level');
            }
            if ($this->Session->read('selected_department')) {
                $this->Session->delete('selected_department');
            }
            if ($this->Session->read('selected_published_course')) {
                $this->Session->delete('selected_published_course');
            }
        }
        $programs = $this->CourseExamConstraint->PublishedCourse->Program->find('list');
        $programTypes = $this->CourseExamConstraint->PublishedCourse->ProgramType->find('list');
        $departments = $this->CourseExamConstraint->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $this->college_id))
        );
        $yearLevels = null;
        $yearLevels = $this->CourseExamConstraint->PublishedCourse->YearLevel->find(
            'list',
            array('conditions' => array('YearLevel.department_id' => $this->department_id))
        );
        $this->set(compact('programs', 'programTypes', 'departments', 'yearLevels'));

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['CourseExamConstraint']['academicyear']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the academic year of the publish courses that you want to add course exam constraints.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseExamConstraint']['program_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the program of the publish courses that you want to add course exam constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseExamConstraint']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the program type of the publish courses that you want to add course exam constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseExamConstraint']['department_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the department of the publish courses that you want to add course exam constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseExamConstraint']['year_level_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the Year Level of the publish courses that you want to add course exam constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseExamConstraint']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the semester of the publish courses that you want to add course exam constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                default:
                    $everythingfine = true;
            }
            if ($everythingfine) {
                $selected_academicyear = $this->request->data['CourseExamConstraint']['academicyear'];
                $this->Session->write('selected_academicyear', $selected_academicyear);
                $selected_program = $this->request->data['CourseExamConstraint']['program_id'];
                $this->Session->write('selected_program', $selected_program);
                $selected_program_type = $this->request->data['CourseExamConstraint']['program_type_id'];
                $this->Session->write('selected_program_type', $selected_program_type);
                $selected_semester = $this->request->data['CourseExamConstraint']['semester'];
                $this->Session->write('selected_semester', $selected_semester);

                if ($this->role_id == ROLE_COLLEGE) {
                    $selected_year_level = $this->request->data['CourseExamConstraint']['year_level_id'];
                    $this->Session->write('selected_year_level', $selected_year_level);
                    $selected_department = $this->request->data['CourseExamConstraint']['department_id'];
                    $this->Session->write('selected_department', $selected_department);
                    if (!empty($selected_department)) {
                        $yearLevels = $this->CourseExamConstraint->PublishedCourse->YearLevel->find(
                            'list',
                            array('conditions' => array('YearLevel.department_id' => $selected_department))
                        );
                    }
                }
                $sections_array = $this->_get_published_course_organized_by_section(
                    $selected_academicyear,
                    $selected_department,
                    $selected_program,
                    $selected_program_type,
                    $selected_year_level,
                    $selected_semester
                );
                $exam_period_id = $this->_get_exam_period_dates_array(
                    $selected_academicyear,
                    $selected_program,
                    $selected_program_type,
                    $selected_year_level,
                    $selected_semester
                );

                if (empty($sections_array)) {
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'There is no published courses to add  course exam constraints in the selected criteria.'
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                } else {
                    $this->Session->write('sections_array', $sections_array);
                    $this->Session->write('yearLevels', $yearLevels);
                    $this->set(
                        compact(
                            'sections_array',
                            'yearLevels',
                            'selected_academicyear',
                            'selected_department',
                            'selected_program',
                            'selected_program_type',
                            'selected_year_level',
                            'selected_semester',
                            'yearLevels'
                        )
                    );
                }
            }
        }
        if (!empty($this->request->data) && isset($this->request->data['submit'])) {
            $selected_academicyear = $this->request->data['CourseExamConstraint']['academicyear'];
            $this->Session->write('selected_academicyear', $selected_academicyear);
            $selected_department = $this->request->data['CourseExamConstraint']['department_id'];
            $this->Session->write('selected_department', $selected_department);
            $selected_program = $this->request->data['CourseExamConstraint']['program_id'];
            $this->Session->write('selected_program', $selected_program);
            $selected_program_type = $this->request->data['CourseExamConstraint']['program_type_id'];
            $this->Session->write('selected_program_type', $selected_program_type);
            $selected_year_level = $this->request->data['CourseExamConstraint']['year_level_id'];
            $this->Session->write('selected_year_level', $selected_year_level);
            $selected_semester = $this->request->data['CourseExamConstraint']['semester'];
            $this->Session->write('selected_semester', $selected_semester);
            $selected_published_course = $this->request->data['CourseExamConstraint']['published_course_id'];
            if (!empty($selected_department)) {
                $yearLevels = $this->CourseExamConstraint->PublishedCourse->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $selected_department))
                );
            }
            $exam_period_id = $this->_get_exam_period_dates_array(
                $selected_academicyear,
                $selected_program,
                $selected_program_type,
                $selected_year_level,
                $selected_semester
            );
            $exam_period_dates_array = ClassRegistry::init('ExamExcludedDateAndSession')->get_list_of_exam_period_dates(
                $exam_period_id
            );

            $selected_course_exam_constraints_array = array();
            $selected_option = array();
            if (!empty($this->request->data['CourseExamConstraint']['Selected'])) {
                foreach ($this->request->data['CourseExamConstraint']['Selected'] as $cecsk => $cecsv) {
                    if ($cecsv != '0') {
                        $explode_data = explode("-", $cecsv);
                        $selected_course_exam_constraints_array[$exam_period_dates_array[$explode_data[0]]][] = $explode_data[1];
                        $selected_option[$exam_period_dates_array[$explode_data[0]]] = $this->request->data['CourseExamConstraint']['active'][$explode_data[0]];
                    }
                }
            }
            $this->request->data['CourseExamConstraints']['published_course_id'] = $selected_published_course;
            $count_selected_course_exam_constraints = count($selected_course_exam_constraints_array);
            if ($count_selected_course_exam_constraints != 0) {
                $issave = false;
                foreach ($selected_course_exam_constraints_array as $date_key => $date_value) {
                    $this->request->data['CourseExamConstraints']['exam_date'] = $date_key;
                    $this->request->data['CourseExamConstraints']['active'] = $selected_option[$date_key];
                    foreach ($date_value as $session_key => $session_value) {
                        $this->request->data['CourseExamConstraints']['session'] = $session_value;
                        $this->CourseExamConstraint->create();
                        if ($this->CourseExamConstraint->save($this->request->data['CourseExamConstraints'])) {
                            $issave = true;
                        }
                    }
                }
                if ($issave == true) {
                    $this->Session->setFlash(
                        '<span></span>' . __('The course exam constraint has been saved.'),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    //$this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __('The course exam constraint could not be saved. Please, try again.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('Please check at least 1 session.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }

            $this->request->data['search'] = true;
            $sections_array = $this->_get_published_course_organized_by_section(
                $selected_academicyear,
                $selected_department,
                $selected_program,
                $selected_program_type,
                $selected_year_level,
                $selected_semester
            );
            $already_excluded_date_and_session_array = ClassRegistry::init(
                'ExamExcludedDateAndSession'
            )->get_already_excluded_date_and_session($exam_period_id);
            $excluded_session_by_date = $already_excluded_date_and_session_array[1];
            $already_recorded_course_exam_constraints_by_date = $this->CourseExamConstraint->get_already_recorded_course_exam_constraint(
                $selected_published_course
            );

            $this->set(
                compact(
                    'sections_array',
                    'selected_academicyear',
                    'selected_department',
                    'selected_program',
                    'selected_program_type',
                    'selected_year_level',
                    'selected_semester',
                    'selected_published_course',
                    'yearLevels',
                    'exam_period_dates_array',
                    'excluded_session_by_date',
                    'already_recorded_course_exam_constraints_by_date'
                )
            );
        }
        if ($this->Session->read('from_delete')) {
            $selected_academicyear = $this->Session->read('selected_academicyear');
            $selected_department = $this->Session->read('selected_department');
            $selected_program = $this->Session->read('selected_program');
            $selected_program_type = $this->Session->read('selected_program_type');
            $selected_year_level = $this->Session->read('selected_year_level');
            $selected_semester = $this->Session->read('selected_semester');
            $selected_published_course = $this->Session->read('selected_published_course');
            if (!empty($selected_department)) {
                $yearLevels = $this->CourseExamConstraint->PublishedCourse->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $selected_department))
                );
            }

            $exam_period_id = $this->_get_exam_period_dates_array(
                $selected_academicyear,
                $selected_program,
                $selected_program_type,
                $selected_year_level,
                $selected_semester
            );
            $exam_period_dates_array = ClassRegistry::init('ExamExcludedDateAndSession')->get_list_of_exam_period_dates(
                $exam_period_id
            );
            $sections_array = $this->_get_published_course_organized_by_section(
                $selected_academicyear,
                $selected_department,
                $selected_program,
                $selected_program_type,
                $selected_year_level,
                $selected_semester
            );
            $already_excluded_date_and_session_array = ClassRegistry::init(
                'ExamExcludedDateAndSession'
            )->get_already_excluded_date_and_session($exam_period_id);
            $excluded_session_by_date = $already_excluded_date_and_session_array[1];
            $already_recorded_course_exam_constraints_by_date = $this->CourseExamConstraint->get_already_recorded_course_exam_constraint(
                $selected_published_course
            );
            $this->set(
                compact(
                    'sections_array',
                    'selected_academicyear',
                    'selected_department',
                    'selected_program',
                    'selected_program_type',
                    'selected_year_level',
                    'selected_semester',
                    'selected_published_course',
                    'yearLevels',
                    'exam_period_dates_array',
                    'excluded_session_by_date',
                    'already_recorded_course_exam_constraints_by_date'
                )
            );
            $this->Session->delete('from_delete');
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid course exam constraint'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->CourseExamConstraint->save($this->request->data)) {
                $this->Session->setFlash(__('The course exam constraint has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The course exam constraint could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->CourseExamConstraint->read(null, $id);
        }
        $publishedCourses = $this->CourseExamConstraint->PublishedCourse->find('list');
        $this->set(compact('publishedCourses'));
    }

    public function delete($id = null, $from = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span> ' . __('Invalid id for course exam constraint.'),
                'default',
                array('class' => 'error-box error-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        }
        $from_delete = 1;
        $this->Session->write('from_delete', $from_delete);
        $selected_published_course = $this->CourseExamConstraint->field(
            'CourseExamConstraint.published_course_id',
            array('CourseExamConstraint.id' => $id)
        );
        $this->Session->write('selected_published_course', $selected_published_course);
        if ($this->CourseExamConstraint->delete($id)) {
            $this->Session->setFlash(
                '<span></span> ' . __('Course exam constraint deleted.'),
                'default',
                array('class' => 'success-box success-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        }
        $this->Session->setFlash(
            '<span></span> ' . __('Course exam constraint was not deleted.'),
            'default',
            array('class' => 'error-box error-message')
        );
        if (empty($from)) {
            return $this->redirect(array('action' => 'index'));
        } else {
            return $this->redirect(array('action' => 'add'));
        }
    }

    public function get_year_level($department_id = null)
    {

        if (!empty($department_id)) {
            $this->layout = 'ajax';
            if ($department_id == 10000) {
                $yearLevels[10000] = '1st';
            } else {
                $yearLevels = $this->CourseExamConstraint->PublishedCourse->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $department_id))
                );
            }
            $this->set(compact('yearLevels'));
        }
    }

    public function _get_published_course_organized_by_section(
        $selected_academicyear = null,
        $selected_department = null,
        $selected_program = null,
        $selected_program_type = null,
        $selected_year_level = null,
        $selected_semester = null
    ) {

        if ($this->role_id == ROLE_COLLEGE) {
            if ($selected_department != 10000) {
                $yearLevels = $this->CourseExamConstraint->PublishedCourse->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $selected_department))
                );
                $conditions = array(
                    'PublishedCourse.academic_year' => $selected_academicyear,
                    'PublishedCourse.department_id' => $selected_department,
                    'PublishedCourse.program_id' => $selected_program,
                    'PublishedCourse.program_type_id' => $selected_program_type,
                    'PublishedCourse.year_level_id LIKE' => $selected_year_level,
                    'PublishedCourse.semester' => $selected_semester,
                    'PublishedCourse.drop' => 0
                );
            } else {
                $conditions = array(
                    'PublishedCourse.academic_year' => $selected_academicyear,
                    'PublishedCourse.college_id' => $this->college_id,
                    'PublishedCourse.program_id' => $selected_program,
                    'PublishedCourse.program_type_id' => $selected_program_type,
                    'PublishedCourse.semester' => $selected_semester,
                    'PublishedCourse.drop' => 0,
                    "OR" => array(
                        "PublishedCourse.department_id is null",
                        "PublishedCourse.department_id" => array(0, '')
                    )
                );
            }
        }
        $publishedcourses = $this->CourseExamConstraint->PublishedCourse->find('all', array(
            'conditions' => $conditions,
            'fields' => array('PublishedCourse.id', 'PublishedCourse.section_id'),
            'contain' => array(
                'Section' => array('fields' => array('Section.id', 'Section.name')),
                'Course' => array(
                    'fields' => array(
                        'Course.id',
                        'Course.course_title',
                        'Course.course_code',
                        'Course.credit',
                        'Course.lecture_hours',
                        'Course.tutorial_hours',
                        'Course.laboratory_hours'
                    )
                )
            )
        ));
        $sections_array = array();
        foreach ($publishedcourses as $key => $publishedcourse) {
            $sections_array[$publishedcourse['Section']['name']][$key]['course_title'] = $publishedcourse['Course']['course_title'];
            $sections_array[$publishedcourse['Section']['name']][$key]['course_id'] = $publishedcourse['Course']['id'];
            $sections_array[$publishedcourse['Section']['name']][$key]['course_code'] = $publishedcourse['Course']['course_code'];
            $sections_array[$publishedcourse['Section']['name']][$key]['credit'] = $publishedcourse['Course']['credit'];
            $sections_array[$publishedcourse['Section']['name']][$key]['credit_detail'] = $publishedcourse['Course']['lecture_hours'] . ' ' . $publishedcourse['Course']['tutorial_hours'] . ' ' . $publishedcourse['Course']['laboratory_hours'];
            $sections_array[$publishedcourse['Section']['name']][$key]['section_id'] =
                $publishedcourse['PublishedCourse']['section_id'];
            $sections_array[$publishedcourse['Section']['name']][$key]['published_course_id'] =
                $publishedcourse['PublishedCourse']['id'];
        }
        return $sections_array;
    }

    public function _get_exam_period_dates_array(
        $selected_academicyear = null,
        $selected_program = null,
        $selected_program_type = null,
        $selected_year_level = null,
        $selected_semester = null
    ) {

        //Find Exam Periods

        //Year Level id for prep fresh student published coursed is manually set 10000
        if ($selected_year_level == 10000) {
            $year_level_name = "1st";
        } else {
            $year_level_name = $this->CourseExamConstraint->PublishedCourse->YearLevel->field(
                'YearLevel.name',
                array('YearLevel.id' => $selected_year_level)
            );
        }
        $exam_period_id = ClassRegistry::init('ExamPeriod')->find(
            'first',
            array(
                'fields' => array('ExamPeriod.id'),
                'conditions' => array(
                    'ExamPeriod.academic_year' => $selected_academicyear,
                    'ExamPeriod.college_id' => $this->college_id,
                    'ExamPeriod.program_id' => $selected_program,
                    'ExamPeriod.program_type_id' => $selected_program_type,
                    'ExamPeriod.year_level_id' => $year_level_name
                ),
                'recursive' => -1
            )
        );

        if (!empty($exam_period_id)) {
            return $exam_period_id['ExamPeriod']['id'];
        } else {
            return false;
        }
    }

    public function get_course_exam_constraints_details($published_course_id = null)
    {

        if (!empty($published_course_id)) {
            $this->layout = 'ajax';
            $publishedCourses = $this->CourseExamConstraint->PublishedCourse->find(
                'first',
                array(
                    'conditions' => array('PublishedCourse.id' => $published_course_id),
                    'fields' => array(
                        'PublishedCourse.academic_year',
                        'PublishedCourse.semester',
                        'PublishedCourse.program_id',
                        'PublishedCourse.program_type_id',
                        'PublishedCourse.year_level_id'
                    ),
                    'recursive' => -1
                )
            );
            //if year level id is 0 that means the the published course is for prep fresh student
            $year_level_id = $publishedCourses['PublishedCourse']['year_level_id'];
            if ($year_level_id == 0) {
                $year_level_id = 10000;
            }

            $exam_period_id = $this->_get_exam_period_dates_array(
                $publishedCourses['PublishedCourse']['academic_year'],
                $publishedCourses['PublishedCourse']['program_id'],
                $publishedCourses['PublishedCourse']['program_type_id'],
                $year_level_id,
                $publishedCourses['PublishedCourse']['semester']
            );

            $exam_period_dates_array = ClassRegistry::init('ExamExcludedDateAndSession')->get_list_of_exam_period_dates(
                $exam_period_id
            );
            $already_excluded_date_and_session_array = ClassRegistry::init(
                'ExamExcludedDateAndSession'
            )->get_already_excluded_date_and_session($exam_period_id);
            $excluded_session_by_date = $already_excluded_date_and_session_array[1];

            $already_recorded_course_exam_constraints_by_date = $this->CourseExamConstraint->get_already_recorded_course_exam_constraint(
                $published_course_id
            );

            $this->set(
                compact(
                    'exam_period_dates_array',
                    'excluded_session_by_date',
                    'already_recorded_course_exam_constraints_by_date'
                )
            );
        }
    }
}

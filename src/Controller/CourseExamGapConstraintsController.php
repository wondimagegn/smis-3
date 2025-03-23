<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class CourseExamGapConstraintsController extends AppController
{

    public $name = 'CourseExamGapConstraints';
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

        $this->Auth->allow('get_year_level');
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

        $departments = $this->CourseExamGapConstraint->PublishedCourse->Department->find(
            'list',
            array(
                'fields' => array('Department.id'),
                'conditions' => array('Department.college_id' => $this->college_id)
            )
        );
        $publishedCourses_id_array = $this->CourseExamGapConstraint->PublishedCourse->find(
            'list',
            array(
                'fields' => array('PublishedCourse.id'),
                'conditions' => array(
                    'PublishedCourse.drop' => 0,
                    "OR" => array(
                        array('PublishedCourse.college_id' => $this->college_id),
                        array('PublishedCourse.department_id' => $departments)
                    )
                )
            )
        );
        $this->paginate = array(
            'fields' => array(
                'CourseExamGapConstraint.id',
                'CourseExamGapConstraint.gap_before_exam',
                'CourseExamGapConstraint.published_course_id'
            ),
            'conditions' => array('CourseExamGapConstraint.published_course_id' => $publishedCourses_id_array),
            'contain' => array(
                'PublishedCourse' => array(
                    'fields' => array('PublishedCourse.id'),
                    'Section' => array('fields' => array('Section.id', 'Section.name')),
                    'Course' => array(
                        'fields' => array(
                            'Course.id',
                            'Course.course_code_title',
                            'Course.course_code',
                            'Course.credit'
                        )
                    )
                )
            )
        );
        $this->set('courseExamGapConstraints', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid course exam gap constraint'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('courseExamGapConstraint', $this->CourseExamGapConstraint->read(null, $id));
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
        }
        $programs = $this->CourseExamGapConstraint->PublishedCourse->Program->find('list');
        $programTypes = $this->CourseExamGapConstraint->PublishedCourse->ProgramType->find('list');
        $departments = $this->CourseExamGapConstraint->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $this->college_id))
        );
        $yearLevels = null;
        $yearLevels = $this->CourseExamGapConstraint->PublishedCourse->YearLevel->find(
            'list',
            array('conditions' => array('YearLevel.department_id' => $this->department_id))
        );
        $this->set(compact('programs', 'programTypes', 'departments', 'yearLevels'));

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            if ($this->Session->read('sections_array')) {
                $this->Session->delete('sections_array');
            }
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['CourseExamGapConstraint']['academicyear']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the academic year of the publish courses that you want to add course exam gap constraints.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseExamGapConstraint']['program_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the program of the publish courses that you want to add course exam gap constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseExamGapConstraint']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the program type of the publish courses that you want to add course exam gap constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['CourseExamGapConstraint']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the semester of the publish courses that you want to add course exam gap constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                default:
                    $everythingfine = true;
            }
            if ($everythingfine) {
                $selected_academicyear = $this->request->data['CourseExamGapConstraint']['academicyear'];
                $this->Session->write('selected_academicyear', $selected_academicyear);
                $selected_program = $this->request->data['CourseExamGapConstraint']['program_id'];
                $this->Session->write('selected_program', $selected_program);
                $selected_program_type = $this->request->data['CourseExamGapConstraint']['program_type_id'];
                $this->Session->write('selected_program_type', $selected_program_type);
                $selected_semester = $this->request->data['CourseExamGapConstraint']['semester'];
                $this->Session->write('selected_semester', $selected_semester);
                //$program_type_id=$this->AcademicYear->equivalent_program_type($selected_program_type);

                if ($this->role_id == ROLE_COLLEGE) {
                    $selected_year_level = $this->request->data['CourseExamGapConstraint']['year_level_id'];
                    $this->Session->write('selected_year_level', $selected_year_level);
                    if (empty($selected_year_level) || ($selected_year_level == "All")) {
                        $selected_year_level = '%';
                    }
                    $selected_department = $this->request->data['CourseExamGapConstraint']['department_id'];
                    $this->Session->write('selected_department', $selected_department);
                }
                $sections_array = $this->_get_published_course_organized_by_section(
                    $selected_academicyear,
                    $selected_department,
                    $selected_program,
                    $selected_program_type,
                    $selected_year_level,
                    $selected_semester
                );
                if (empty($sections_array)) {
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'There is no published courses to add  course exam gap constraints in the selected criteria.'
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                } else {
                    $this->Session->write('sections_array', $sections_array);
                    $this->Session->write('yearLevels', $yearLevels);
                    $this->set(compact('sections_array', 'yearLevels'));
                }
            }
        }
        if (!empty($this->request->data) && isset($this->request->data['submit'])) {
            if (!empty($this->request->data['CourseExamGapConstraint']['published_course_id'])) {
                if (!empty($this->request->data['CourseExamGapConstraint']['gap_before_exam'])) {
                    $isalready_record_this_constraints = 0;
                    $isalready_record_this_constraints = $this->CourseExamGapConstraint->find(
                        'count',
                        array('conditions' => array('CourseExamGapConstraint.published_course_id' => $this->request->data['CourseExamGapConstraint']['published_course_id']))
                    );

                    if ($isalready_record_this_constraints == 0) {
                        $this->request->data['CourseExamGapConstraints']['published_course_id'] =
                            $this->request->data['CourseExamGapConstraint']['published_course_id'];
                        $this->request->data['CourseExamGapConstraints']['gap_before_exam'] =
                            $this->request->data['CourseExamGapConstraint']['gap_before_exam'];

                        $this->CourseExamGapConstraint->create();
                        if ($this->CourseExamGapConstraint->save($this->request->data['CourseExamGapConstraints'])) {
                            $this->Session->setFlash(
                                '<span></span>' . __('The course exam period constraint has been saved'),
                                'default',
                                array('class' => 'success-box success-message')
                            );
                        } else {
                            $this->Session->setFlash(
                                '<span></span>' . __(
                                    'The course exam gap constraint could not be saved. Please, try again.'
                                ),
                                'default',
                                array('class' => 'error-box error-message')
                            );
                        }
                    } else {
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                'You have course exam period constraint in selected course. If you modify delete the record first.'
                            ),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __(' Please provide gap before exam number of day'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(' Please select course'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }

        $yearLevels = $this->Session->read('yearLevels');
        if ($this->Session->read('selected_academicyear')) {
            $selected_academicyear = $this->Session->read('selected_academicyear');
        } else {
            $selected_academicyear = $this->request->data['CourseExamGapConstraint']['academicyear'];
            $this->Session->write('selected_academicyear', $selected_academicyear);
        }
        if ($this->Session->read('selected_program')) {
            $selected_program = $this->Session->read('selected_program');
        } else {
            $selected_program = $this->request->data['CourseExamGapConstraint']['program_id'];
            $this->Session->write('selected_program', $selected_program);
        }
        if ($this->Session->read('selected_program_type')) {
            $selected_program_type = $this->Session->read('selected_program_type');
        } else {
            $selected_program_type = $this->request->data['CourseExamGapConstraint']['program_type_id'];
            $this->Session->write('selected_program_type', $selected_program_type);
        }
        if ($this->Session->read('selected_semester')) {
            $selected_semester = $this->Session->read('selected_semester');
        } else {
            $selected_semester = $this->request->data['CourseExamGapConstraint']['semester'];
            $this->Session->write('selected_semester', $selected_semester);
        }
        if ($this->role_id == ROLE_COLLEGE) {
            if ($this->Session->read('selected_year_level')) {
                $selected_year_level = $this->Session->read('selected_year_level');
            } else {
                $selected_year_level = $this->request->data['CourseExamGapConstraint']['year_level_id'];
                $this->Session->write('selected_year_level', $selected_year_level);
            }
            if (empty($selected_year_level) || ($selected_year_level == "All")) {
                $selected_year_level = '%';
            }
            if ($this->Session->read('selected_department')) {
                $selected_department = $this->Session->read('selected_department');
            } else {
                $selected_department = $this->request->data['CourseExamGapConstraint']['department_id'];
                $this->Session->write('selected_department', $selected_department);
            }
            if (empty($selected_department)) {
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
            } else {
                $conditions = array(
                    'PublishedCourse.academic_year' =>
                        $selected_academicyear,
                    'PublishedCourse.department_id' => $selected_department,
                    'PublishedCourse.program_id' => $selected_program,
                    'PublishedCourse.program_type_id' =>
                        $selected_program_type,
                    'PublishedCourse.year_level_id LIKE' => $selected_year_level,
                    'PublishedCourse.semester' => $selected_semester,
                    'PublishedCourse.drop' => 0
                );
            }
        }
        $publishedCourses_id_array = $this->CourseExamGapConstraint->PublishedCourse->find(
            'list',
            array('fields' => array('PublishedCourse.id'), 'conditions' => $conditions)
        );

        $courseExamGapConstraints = $this->CourseExamGapConstraint->find(
            'all',
            array(
                'fields' => array(
                    'CourseExamGapConstraint.id',
                    'CourseExamGapConstraint.gap_before_exam',
                    'CourseExamGapConstraint.published_course_id'
                ),
                'conditions' => array('CourseExamGapConstraint.published_course_id' => $publishedCourses_id_array),
                'contain' => array(
                    'PublishedCourse' => array(
                        'fields' => array('PublishedCourse.id'),
                        'Section' => array('fields' => array('Section.id', 'Section.name')),
                        'Course' => array(
                            'fields' => array(
                                'Course.id',
                                'Course.course_code_title',
                                'Course.course_code',
                                'Course.credit'
                            )
                        )
                    )
                )
            )
        );

        $sections_array = $this->_get_published_course_organized_by_section(
            $selected_academicyear,
            $selected_department,
            $selected_program,
            $selected_program_type,
            $selected_year_level,
            $selected_semester
        );

        $this->set(
            compact(
                'yearLevels',
                'selected_academicyear',
                'selected_program',
                'sections_array',
                'selected_program_type',
                'selected_semester',
                'selected_department',
                'selected_year_level',
                'courseExamGapConstraints'
            )
        );
        if ($this->Session->read('from_delete')) {
            $this->Session->delete('from_delete');
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid course exam gap constraint'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->CourseExamGapConstraint->save($this->request->data)) {
                $this->Session->setFlash(__('The course exam gap constraint has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The course exam gap constraint could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->CourseExamGapConstraint->read(null, $id);
        }
        $publishedCourses = $this->CourseExamGapConstraint->PublishedCourse->find('list');
        $this->set(compact('publishedCourses'));
    }

    public function delete($id = null, $from = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span> ' . __('Invalid id for course exam gap constraint.'),
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
        $areyou_eligible_to_delete = $this->CourseExamGapConstraint->beforeDeleteCheckEligibility(
            $id,
            $this->college_id
        );
        if ($areyou_eligible_to_delete == true) {
            if ($this->CourseExamGapConstraint->delete($id)) {
                $this->Session->setFlash(
                    '<span></span> ' . __('Course exam gap constraint deleted'),
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
                '<span></span> ' . __('Course exam gap constraint was not deleted.'),
                'default',
                array('class' => 'error-box error-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        } else {
            $this->Session->setFlash(
                '<span></span> ' . __('You are not eligible to delete this course exam gap constraint.'),
                'default',
                array('class' => 'error-box error-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        }
    }

    public function get_year_level($department_id = null)
    {

        if (!empty($department_id)) {
            $this->layout = 'ajax';
            $yearLevels = $this->CourseExamGapConstraint->PublishedCourse->YearLevel->find(
                'list',
                array('conditions' => array('YearLevel.department_id' => $department_id))
            );
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
            if (!empty($selected_department)) {
                $yearLevels = $this->CourseExamGapConstraint->PublishedCourse->YearLevel->find(
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
                    'PublishedCourse.drop' => 0,
                    'PublishedCourse.id NOT IN (select published_course_id from course_exam_gap_constraints)'
                );
            } else {
                $conditions = array(
                    'PublishedCourse.academic_year' => $selected_academicyear,
                    'PublishedCourse.college_id' => $this->college_id,
                    'PublishedCourse.program_id' => $selected_program,
                    'PublishedCourse.program_type_id' => $selected_program_type,
                    'PublishedCourse.semester' => $selected_semester,
                    'PublishedCourse.drop' => 0,
                    'PublishedCourse.id NOT IN (select published_course_id from course_exam_gap_constraints)',
                    "OR" => array(
                        "PublishedCourse.department_id is null",
                        "PublishedCourse.department_id" => array(0, '')
                    )
                );
            }
        }
        $publishedcourses = $this->CourseExamGapConstraint->PublishedCourse->find('all', array(
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
}

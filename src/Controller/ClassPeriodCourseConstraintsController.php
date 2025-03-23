<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;


class ClassPeriodCourseConstraintsController extends AppController
{

    public $name = 'ClassPeriodCourseConstraints';
    public $components = array('AcademicYear');

    public $menuOptions = array(
        //'parent' => 'courseConstraint',
        'exclude' => array('*'),
        'controllerButton' => false,
    );

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        //$this->Auth->allow(array('*'));
        $this->Auth->allow('get_year_level', 'get_periods', 'get_course_types');
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

        $departments = $this->ClassPeriodCourseConstraint->PublishedCourse->Department->find(
            'list',
            array(
                'fields' => array('Department.id'),
                'conditions' => array('Department.college_id' => $this->college_id)
            )
        );
        $publishedCourses_id_array = $this->ClassPeriodCourseConstraint->PublishedCourse->find(
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
            'conditions' => array('ClassPeriodCourseConstraint.published_course_id' => $publishedCourses_id_array),
            'contain' => array(
                'PublishedCourse' => array(
                    'fields' => array(
                        'PublishedCourse.id',
                        'PublishedCourse.academic_year',
                        'PublishedCourse.semester'
                    ),
                    'Course' => array('fields' => array('Course.id', 'Course.course_code_title'))
                ),
                'ClassPeriod' => array(
                    'fields' => array('ClassPeriod.week_day', 'ClassPeriod.id', 'ClassPeriod.period_setting_id'),
                    'PeriodSetting' => array('fields' => array('PeriodSetting.period', 'PeriodSetting.hour'))
                )
            )
        );

        $this->set('classPeriodCourseConstraints', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid class period course constraint'));
            return $this->redirect(array('action' => 'index'));
        }
        //debug($this->ClassPeriodCourseConstraint->read(null, $id));
        $this->set('classPeriodCourseConstraint', $this->ClassPeriodCourseConstraint->read(null, $id));
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
        $programs = $this->ClassPeriodCourseConstraint->PublishedCourse->Program->find('list');
        $programTypes = $this->ClassPeriodCourseConstraint->PublishedCourse->ProgramType->find('list');
        $departments = $this->ClassPeriodCourseConstraint->PublishedCourse->Department->find('list', array(
            'conditions' =>
                array('Department.college_id' => $this->college_id)
        ));
        $yearLevels = null;
        $yearLevels = $this->ClassPeriodCourseConstraint->PublishedCourse->YearLevel->find('list', array(
            'conditions' =>
                array('YearLevel.department_id' => $this->department_id)
        ));
        $this->set(compact('programs', 'programTypes', 'departments', 'yearLevels'));

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            if ($this->Session->read('sections_array')) {
                $this->Session->delete('sections_array');
            }
            if ($this->Session->read('selected_week_day')) {
                $this->Session->delete('selected_week_day');
            }
            if ($this->Session->read('selected_published_course_id')) {
                $this->Session->delete('selected_published_course_id');
            }
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['ClassPeriodCourseConstraint']['academicyear']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the academic year of the publish courses
						that you want to add class period course constraints.',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ClassPeriodCourseConstraint']['program_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the program of the publish courses
						that you want to add class period course constraints. ',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ClassPeriodCourseConstraint']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the program type of the publish courses
						that you want to add class period course constraints. ',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ClassPeriodCourseConstraint']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the semester of the publish courses that you want to add class period course constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                $selected_academicyear = $this->request->data['ClassPeriodCourseConstraint']['academicyear'];
                $this->Session->write('selected_academicyear', $selected_academicyear);
                $selected_program = $this->request->data['ClassPeriodCourseConstraint']['program_id'];
                $this->Session->write('selected_program', $selected_program);
                $selected_program_type = $this->request->data['ClassPeriodCourseConstraint']['program_type_id'];
                $this->Session->write('selected_program_type', $selected_program_type);
                $selected_semester = $this->request->data['ClassPeriodCourseConstraint']['semester'];
                $this->Session->write('selected_semester', $selected_semester);
                //$program_type_id=$this->AcademicYear->equivalent_program_type($selected_program_type);

                if ($this->role_id == ROLE_COLLEGE) {
                    $selected_year_level = $this->request->data['ClassPeriodCourseConstraint']['year_level_id'];
                    $this->Session->write('selected_year_level', $selected_year_level);
                    if (empty($selected_year_level) || ($selected_year_level == "All")) {
                        $selected_year_level = '%';
                    }
                    $selected_department = $this->request->data['ClassPeriodCourseConstraint']['department_id'];
                    $this->Session->write('selected_department', $selected_department);
                    if (!empty($selected_department)) {
                        $yearLevels = $this->ClassPeriodCourseConstraint->PublishedCourse->YearLevel->find(
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
                $publishedcourses = $this->ClassPeriodCourseConstraint->PublishedCourse->find('all', array(
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
                //debug($publishedcourses);
                $sections_array = array();
                foreach ($publishedcourses as $key => $publishedcourse) {
                    $sections_array[$publishedcourse['Section']['name']][$key]['course_title'] = $publishedcourse
                    ['Course']['course_title'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['course_id'] = $publishedcourse
                    ['Course']['id'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['course_code'] = $publishedcourse
                    ['Course']['course_code'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['credit'] = $publishedcourse
                    ['Course']['credit'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['credit_detail'] = $publishedcourse
                        ['Course']['lecture_hours'] . ' ' . $publishedcourse['Course']['tutorial_hours'] . ' ' .
                        $publishedcourse['Course']['laboratory_hours'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['section_id'] =
                        $publishedcourse['PublishedCourse']['section_id'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['published_course_id'] =
                        $publishedcourse['PublishedCourse']['id'];
                }
                //debug($sections_array);
                if (empty($sections_array)) {
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'There is no published courses to add class period course constraints in the selected criteria.'
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                } else {
                    $this->ClassPeriodCourseConstraint->ClassPeriod->recursive = -1;
                    $week_days = $this->ClassPeriodCourseConstraint->ClassPeriod->find('all', array(
                        'fields' => array('DISTINCT ClassPeriod.week_day'),
                        'conditions' => array(
                            'ClassPeriod.college_id' => $this->college_id,
                            'ClassPeriod.program_id' => $selected_program,
                            'ClassPeriod.program_type_id' => $selected_program_type
                        )
                    ));
                    $this->Session->write('sections_array', $sections_array);
                    $this->Session->write('yearLevels', $yearLevels);
                    $this->Session->write('week_days', $week_days);
                    $this->set(compact('sections_array', 'yearLevels', 'week_days'));
                }
            }
        }
        if (!empty($this->request->data) && isset($this->request->data['submit'])) {
            if (!empty($this->request->data['ClassPeriodCourseConstraint']['courses'])) {
                $this->Session->write(
                    'selected_published_course_id',
                    $this->request->data['ClassPeriodCourseConstraint']['courses']
                );
                if (!empty($this->request->data['ClassPeriodCourseConstraint']['week_day'])) {
                    $selected_week_day = $this->request->data['ClassPeriodCourseConstraint']['week_day'];
                    $this->Session->write('selected_week_day', $selected_week_day);
                    $selected_class_period_id_array = array();
                    if (!empty($this->request->data['ClassPeriodCourseConstraint']['Selected'])) {
                        foreach ($this->request->data['ClassPeriodCourseConstraint']['Selected'] as $cpccsk => $cpccsv) {
                            if ($cpccsv != 0) {
                                $selected_class_period_id_array[] = $cpccsv;
                            }
                        }
                        //}
                        if (count($selected_class_period_id_array) != 0) {
                            if (!empty($this->request->data['ClassPeriodCourseConstraint']['type'])) {
                                $isalready_record_this_constraints = 0;
                                $isalready_record_this_constraints = $this->ClassPeriodCourseConstraint->find(
                                    'count',
                                    array(
                                        'conditions' => array(
                                            'ClassPeriodCourseConstraint.published_course_id' => $this->request->data['ClassPeriodCourseConstraint']['courses'],
                                            'ClassPeriodCourseConstraint.class_period_id' => $selected_class_period_id_array,
                                            'ClassPeriodCourseConstraint.type' => $this->request->data['ClassPeriodCourseConstraint']['type']
                                        )
                                    )
                                );
                                if ($isalready_record_this_constraints == 0) {
                                    $this->request->data['ClassPeriodCourseConstraints']['published_course_id'] =
                                        $this->request->data['ClassPeriodCourseConstraint']['courses'];
                                    $this->request->data['ClassPeriodCourseConstraints']['type'] =
                                        $this->request->data['ClassPeriodCourseConstraint']['type'];
                                    $this->request->data['ClassPeriodCourseConstraints']['active'] =
                                        $this->request->data['ClassPeriodCourseConstraint']['active'];
                                    foreach ($selected_class_period_id_array as $class_period_id) {
                                        $this->request->data['ClassPeriodCourseConstraints']['class_period_id'] = $class_period_id;
                                        $this->ClassPeriodCourseConstraint->create();
                                        $this->ClassPeriodCourseConstraint->save(
                                            $this->request->data['ClassPeriodCourseConstraints']
                                        );
                                    }
                                    $this->Session->setFlash(
                                        '<span></span>' . __('The class period course constraint has been saved'),
                                        'default',
                                        array('class' => 'success-box success-message')
                                    );
                                } else {
                                    $this->Session->setFlash(
                                        '<span></span>' . __(
                                            'The selected course class period constraint for the one or more seclected period is already recored, Please exclude already recored periods.'
                                        ),
                                        'default',
                                        array('class' => 'error-box error-message')
                                    );
                                }
                            } else {
                                $this->Session->setFlash(
                                    '<span></span>' . __(' Please select course type'),
                                    'default',
                                    array('class' => 'error-box error-message')
                                );
                            }
                        } else {
                            $this->Session->setFlash(
                                '<span></span>' . __(' Please select at least 1 period'),
                                'default',
                                array('class' => 'error-box error-message')
                            );
                        }
                    } else {
                        $this->Session->setFlash(
                            '<span></span> Please set class period first in ',
                            "session_flash_link",
                            array(
                                "class" => 'info-box info-message',
                                "link_text" => " this page",
                                "link_url" => array(
                                    "controller" => "classPeriods",
                                    "action" => "add",
                                    "admin" => false
                                )
                            )
                        );
                    }
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __(' Please select week day'),
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
            $sections_array = $this->Session->read('sections_array');
            $yearLevels = $this->Session->read('yearLevels');
            $week_days = $this->Session->read('week_days');
            $this->set(compact('sections_array', 'yearLevels', 'week_days'));
        }
        $sections_array = $this->Session->read('sections_array');
        $yearLevels = $this->Session->read('yearLevels');
        $week_days = $this->Session->read('week_days');
        $selected_week_day = null;
        if ($this->Session->read('selected_week_day')) {
            $selected_week_day = $this->Session->read('selected_week_day');
        }
        if ($this->Session->read('selected_academicyear')) {
            $selected_academicyear = $this->Session->read('selected_academicyear');
        } else {
            if (!empty($this->request->data['ClassPeriodCourseConstraint']['academicyear'])) {
                $selected_academicyear = $this->request->data['ClassPeriodCourseConstraint']['academicyear'];
                $this->Session->write('selected_academicyear', $selected_academicyear);
            }
        }
        if ($this->Session->read('selected_program')) {
            $selected_program = $this->Session->read('selected_program');
        } else {
            if (!empty($this->request->data['ClassPeriodCourseConstraint']['program_id'])) {
                $selected_program = $this->request->data['ClassPeriodCourseConstraint']['program_id'];
                $this->Session->write('selected_program', $selected_program);
            }
        }
        if ($this->Session->read('selected_program_type')) {
            $selected_program_type = $this->Session->read('selected_program_type');
        } else {
            if (!empty($this->request->data['ClassPeriodCourseConstraint']['program_type_id'])) {
                $selected_program_type = $this->request->data['ClassPeriodCourseConstraint']['program_type_id'];
                $this->Session->write('selected_program_type', $selected_program_type);
            }
        }
        if ($this->Session->read('selected_semester')) {
            $selected_semester = $this->Session->read('selected_semester');
        } else {
            if (!empty($this->request->data['ClassPeriodCourseConstraint']['semester'])) {
                $selected_semester = $this->request->data['ClassPeriodCourseConstraint']['semester'];
                $this->Session->write('selected_semester', $selected_semester);
            }
        }
        if (!empty($selected_program) && !empty($selected_week_day) && !empty($selected_program_type)) {
            $fromadd_periods = $this->_get_periods_data($selected_week_day, $selected_program, $selected_program_type);
        }
        $selected_published_course_id = null;
        if ($this->Session->read($selected_published_course_id)) {
            $selected_published_course_id = $this->Session->read('selected_published_course_id');
        } else {
            if (isset($this->request->data['ClassPeriodCourseConstraint']['courses']) && !empty($this->request->data['ClassPeriodCourseConstraint']['courses'])) {
                $selected_published_course_id = $this->request->data['ClassPeriodCourseConstraint']['courses'];
            }
        }
        if (!empty($selected_published_course_id)) {
            $courseTypes = $this->_get_course_types_array($selected_published_course_id);
        } else {
            $courseTypes = null;
        }
        if ($this->role_id == ROLE_COLLEGE) {
            if ($this->Session->read('selected_year_level')) {
                $selected_year_level = $this->Session->read('selected_year_level');
            } else {
                if (!empty($this->request->data['ClassPeriodCourseConstraint']['year_level_id'])) {
                    $selected_year_level = $this->request->data['ClassPeriodCourseConstraint']['year_level_id'];
                    $this->Session->write('selected_year_level', $selected_year_level);
                }
            }
            if (empty($selected_year_level) || ($selected_year_level == "All")) {
                $selected_year_level = '%';
            }
            if ($this->Session->read('selected_department')) {
                $selected_department = $this->Session->read('selected_department');
            } else {
                if (!empty($this->request->data['ClassPeriodCourseConstraint']['department_id'])) {
                    $selected_department = $this->request->data['ClassPeriodCourseConstraint']['department_id'];
                    $this->Session->write('selected_department', $selected_department);
                }
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
        if (!empty($conditions)) {
            $publishedCourses_id_array = $this->ClassPeriodCourseConstraint->PublishedCourse->find(
                'list',
                array('fields' => array('PublishedCourse.id'), 'conditions' => $conditions)
            );
        }
        if (!empty($publishedCourses_id_array)) {
            $classPeriodCourseConstraints = $this->ClassPeriodCourseConstraint->find(
                'all',
                array(
                    'conditions' => array('ClassPeriodCourseConstraint.published_course_id' => $publishedCourses_id_array),
                    'contain' => array(
                        'PublishedCourse' => array(
                            'fields' => array(
                                'PublishedCourse.id',
                                'PublishedCourse.academic_year',
                                'PublishedCourse.semester'
                            ),
                            'Section' => array('fields' => array('Section.name')),
                            'Course' => array('fields' => array('Course.id', 'Course.course_code_title'))
                        ),
                        'ClassPeriod' => array(
                            'fields' => array(
                                'ClassPeriod.week_day',
                                'ClassPeriod.id',
                                'ClassPeriod.period_setting_id'
                            ),
                            'PeriodSetting' => array('fields' => array('PeriodSetting.period', 'PeriodSetting.hour'))
                        )
                    )
                )
            );
        }
        $this->set(
            compact(
                'yearLevels',
                'classPeriodCourseConstraints',
                'selected_academicyear',
                'selected_program',
                'selected_program_type',
                'selected_semester',
                'selected_department',
                'selected_year_level',
                'fromadd_periods',
                'selected_week_day',
                'selected_published_course_id',
                'courseTypes'
            )
        );
        if ($this->Session->read('from_delete')) {
            $fromadd_periods = $this->_get_periods_data($selected_week_day, $selected_program, $selected_program_type);
            $this->set(compact('sections_array', 'week_days', 'fromadd_periods', 'selected_week_day'));
            $this->Session->delete('from_delete');
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid class period course constraint'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->ClassPeriodCourseConstraint->save($this->request->data)) {
                $this->Session->setFlash(__('The class period course constraint has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('The class period course constraint could not be saved. Please, try again.')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->ClassPeriodCourseConstraint->read(null, $id);
        }
        $publishedCourses = $this->ClassPeriodCourseConstraint->PublishedCourse->find('list');
        $this->set(compact('publishedCourses'));
    }

    public function delete($id = null, $from = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span> ' . __('Invalid id for class period course constraint.'),
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
        $areyou_eligible_to_delete = $this->ClassPeriodCourseConstraint->beforeDeleteCheckEligibility(
            $id,
            $this->college_id
        );
        if ($areyou_eligible_to_delete == true) {
            if ($this->ClassPeriodCourseConstraint->delete($id)) {
                $this->Session->setFlash(
                    '<span></span> ' . __('Class period course constraint deleted'),
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
                '<span></span> ' . __('Class period course constraint was not deleted.'),
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
                '<span></span> ' . __('You are not eligible to delete this class period course constraint.'),
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
            //debug($this->request->data);
            $yearLevels = $this->ClassPeriodCourseConstraint->PublishedCourse->YearLevel->find('list', array(
                'conditions' =>
                    array('YearLevel.department_id' => $department_id)
            ));
            $this->set(compact('yearLevels'));
        }
    }

    public function get_periods($week_id = null)
    {

        $selected_program = $this->Session->read('selected_program');
        $selected_program_type = $this->Session->read('selected_program_type');
        if (!empty($week_id)) {
            $this->layout = 'ajax';
            $periods = $this->_get_periods_data($week_id, $selected_program, $selected_program_type);
            $this->set(compact('periods'));
        }
    }

    public function _get_periods_data($week_id = null, $program_id = null, $program_type_id = null)
    {

        if ($week_id) {
            $periods_data = $this->ClassPeriodCourseConstraint->ClassPeriod->find(
                'all',
                array(
                    'fields' => array('ClassPeriod.id', 'ClassPeriod.period_setting_id', 'ClassPeriod.week_day'),
                    'conditions' => array(
                        'ClassPeriod.week_day' => $week_id,
                        'ClassPeriod.program_id' => $program_id,
                        'ClassPeriod.program_type_id' => $program_type_id,
                        'ClassPeriod.college_id' => $this->college_id
                    ),
                    'contain' => array(
                        'PeriodSetting' => array(
                            'fields' => array(
                                'PeriodSetting.id',
                                'PeriodSetting.period',
                                'PeriodSetting.hour'
                            )
                        )
                    )
                )
            );
            return $periods_data;
        }
    }

    public function get_course_types($publishedCourse_id = null)
    {

        debug($publishedCourse_id);
        if (!empty($publishedCourse_id)) {
            //$this->layout = 'ajax';
            $courseTypes = $this->_get_course_types_array($publishedCourse_id);
            $this->set(compact('courseTypes'));
        }
    }

    public function _get_course_types_array($publishedCourse_id = null)
    {

        if (!empty($publishedCourse_id)) {
            $publishedCourses = $this->ClassPeriodCourseConstraint->PublishedCourse->find(
                'first',
                array(
                    'fields' => array('PublishedCourse.id'),
                    'conditions' => array('PublishedCourse.id' => $publishedCourse_id),
                    'contain' => array(
                        'Course' => array(
                            'fields' => array(
                                'Course.id',
                                'Course.lecture_hours',
                                'Course.tutorial_hours',
                                'Course.laboratory_hours'
                            )
                        )
                    )
                )
            );

            $courseTypes = array();
            if (!empty($publishedCourses['Course']['lecture_hours'])) {
                $courseTypes['lecture'] = 'Lecture';
            }
            if (!empty($publishedCourses['Course']['tutorial_hours'])) {
                $courseTypes['Tutorial'] = 'Tutorial';
            }
            if (!empty($publishedCourses['Course']['laboratory_hours'])) {
                $courseTypes['Laboratory'] = 'Laboratory';
            }
            return $courseTypes;
        }
    }
}

?>

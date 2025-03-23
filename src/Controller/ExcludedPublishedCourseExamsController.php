<?php

namespace App\Controller;

use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ExcludedPublishedCourseExamsController extends AppController
{

    public $name = 'ExcludedPublishedCourseExams';
    public $menuOptions = array(
        'parent' => 'examSchedule',
        'exclude' => array('add', 'get_year_level', 'get_year_levels_for_view'),
        'alias' => array(
            'index' => 'List Excluded Final Exams',
            'exclude' => 'Excluded Final Exams '
        )
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
        $this->Auth->allow('get_year_level', 'get_year_levels_for_view');
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

        //$this->ExcludedPublishedCourseExam->recursive = 0;
        $selected_department = null;
        if (isset($this->request->data['ExcludedPublishedCourseExam']['department_id']) && !empty($this->request->data['ExcludedPublishedCourseExam']['department_id'])) {
            $selected_department = $this->request->data['ExcludedPublishedCourseExam']['department_id'];
        }
        $programs = $this->ExcludedPublishedCourseExam->PublishedCourse->Program->find('list');
        $programTypes = $this->ExcludedPublishedCourseExam->PublishedCourse->ProgramType->find('list');
        $departments = $this->ExcludedPublishedCourseExam->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $this->college_id))
        );
        $departments['10000'] = 'Pre/(Unassign Freshman)';
        if (!empty($selected_department)) {
            $yearLevels = $this->_get_year_levels_list($selected_department);
        } else {
            $yearLevels = null;
        }
        $this->set(compact('programs', 'programTypes', 'yearLevels', 'departments'));
        $options = array();
        //$options[] = array('PublishedCourse.college_id'=>$this->college_id);

        if (isset($this->request->data['ExcludedPublishedCourseExam']['academic_year']) && !empty($this->request->data['ExcludedPublishedCourseExam']['academic_year'])) {
            $options[] = array('PublishedCourse.academic_year' => $this->request->data['ExcludedPublishedCourseExam']['academic_year']);
        }
        if (isset($this->request->data['ExcludedPublishedCourseExam']['semester']) && !empty($this->request->data['ExcludedPublishedCourseExam']['semester'])) {
            $options[] = array('PublishedCourse.semester' => $this->request->data['ExcludedPublishedCourseExam']['semester']);
        }
        if (isset($this->request->data['ExcludedPublishedCourseExam']['program_id']) && !empty($this->request->data['ExcludedPublishedCourseExam']['program_id'])) {
            $options[] = array('PublishedCourse.program_id' => $this->request->data['ExcludedPublishedCourseExam']['program_id']);
        }
        if (isset($this->request->data['ExcludedPublishedCourseExam']['program_type_id']) && !empty($this->request->data['ExcludedPublishedCourseExam']['program_type_id'])) {
            $options[] = array('PublishedCourse.program_type_id' => $this->request->data['ExcludedPublishedCourseExam']['program_type_id']);
        }
        if ($selected_department == 10000) {
            $options[] = array('PublishedCourse.college_id' => $this->college_id);
        } else {
            if (!empty($selected_department)) {
                $options[] = array('PublishedCourse.department_id' => $selected_department);
            } else {
                $department_ids = $this->ExcludedPublishedCourseExam->PublishedCourse->Department->find(
                    'list',
                    array(
                        'conditions' => array('Department.college_id' => $this->college_id),
                        'fields' => array('Department.id', 'Department.id')
                    )
                );
                $options[] = array(
                    "OR" => array(
                        'PublishedCourse.college_id' => $this->college_id,
                        'PublishedCourse.department_id' => $department_ids
                    )
                );
            }
        }
        if (isset($this->request->data['ExcludedPublishedCourseExam']['year_level_id']) && !empty($this->request->data['ExcludedPublishedCourseExam']['year_level_id'])) {
            $options[] = array('PublishedCourse.year_level_id' => $this->request->data['ExcludedPublishedCourseExam']['year_level_id']);
        }
        $published_course_ids = $this->ExcludedPublishedCourseExam->PublishedCourse->find(
            'list',
            array('fields' => array('PublishedCourse.id', 'PublishedCourse.id'), 'conditions' => $options)
        );

        $this->paginate = array(
            'conditions' => array('ExcludedPublishedCourseExam.published_course_id' => $published_course_ids),
            'contain' => array(
                'PublishedCourse' => array(
                    'fields' => array(
                        'PublishedCourse.id',
                        'PublishedCourse.academic_year',
                        'PublishedCourse.semester'
                    ),
                    'Course' => array('fields' => array('Course.id', 'Course.course_code_title')),
                    'Section' => array('fields' => array('Section.name', 'Section.id')),
                    'Department' => array('fields' => array('Department.name'))
                )
            )
        );
        //debug($this->paginate());
        $excludedPublishedCourseExams = $this->paginate();

        if (empty($excludedPublishedCourseExams)) {
            $this->Session->setFlash(
                '<span></span> ' . __(' There is no excluded course from final exam in the selected criteria.'),
                'default',
                array('class' => 'info-box info-message')
            );
        }

        $this->set(compact('excludedPublishedCourseExams'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid excluded published course exam'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('excludedPublishedCourseExam', $this->ExcludedPublishedCourseExam->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->ExcludedPublishedCourseExam->create();
            if ($this->ExcludedPublishedCourseExam->save($this->request->data)) {
                $this->Session->setFlash(__('The excluded published course exam has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('The excluded published course exam could not be saved. Please, try again.')
                );
            }
        }
        $publishedCourses = $this->ExcludedPublishedCourseExam->PublishedCourse->find('list');
        $this->set(compact('publishedCourses'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid excluded published course exam'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->ExcludedPublishedCourseExam->save($this->request->data)) {
                $this->Session->setFlash(__('The excluded published course exam has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('The excluded published course exam could not be saved. Please, try again.')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->ExcludedPublishedCourseExam->read(null, $id);
        }
        $publishedCourses = $this->ExcludedPublishedCourseExam->PublishedCourse->find('list');
        $this->set(compact('publishedCourses'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span> ' . __('Invalid id for excluded published course exam'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $areyou_eligible_to_delete = $this->ExcludedPublishedCourseExam->beforeDeleteCheckEligibility(
            $id,
            $this->college_id
        );
        if ($areyou_eligible_to_delete == true) {
            if ($this->ExcludedPublishedCourseExam->delete($id)) {
                $this->Session->setFlash(
                    '<span></span> ' . __('Excluded published course exam deleted'),
                    'default',
                    array('class' => array('success-info success-message'))
                );
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                '<span></span> ' . __('Excluded published course exam was not deleted'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(
                '<span></span> ' . __('You are not eligible to delete this excluded published course.'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
    }

    public function exclude()
    {

        $programs = $this->ExcludedPublishedCourseExam->PublishedCourse->Program->find('list');
        $programTypes = $this->ExcludedPublishedCourseExam->PublishedCourse->ProgramType->find('list');
        $departments = $this->ExcludedPublishedCourseExam->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $this->college_id))
        );
        $departments['10000'] = 'Pre/(Unassign Freshman)';
        if (!empty($selected_department)) {
            $yearLevels = $this->_get_year_levels_list($selected_department);
        } else {
            $yearLevels = null;
        }
        $this->set(compact('programs', 'programTypes', 'departments', 'yearLevels'));

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            if ($this->Session->read('sections_array')) {
                $this->Session->delete('sections_array');
            }
            $everythingfine = false;
            if ($this->role_id == ROLE_COLLEGE) {
                if (empty($this->request->data['ExcludedPublishedCourseExam']['department_id'])) {
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the department of the publish courses that you want to excluded from final exam schedule. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    $everythingfine = false;
                } else {
                    $everythingfine = true;
                }
            }
            switch ($this->request->data) {
                case empty($this->request->data['ExcludedPublishedCourseExam']['academicyear']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the academic year of the publish courses that you want to excluded from final exam schedule.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    $everythingfine = false;
                    break;
                case empty($this->request->data['ExcludedPublishedCourseExam']['program_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the program of the publish courses that you want to excluded from final exam schedule. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    $everythingfine = false;
                    break;
                case empty($this->request->data['ExcludedPublishedCourseExam']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the program type of the publish courses that you want to excluded from final exam schedule. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    $everythingfine = false;
                    break;
                case empty($this->request->data['ExcludedPublishedCourseExam']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the semester of the publish courses that you want to excluded from final exam schedule. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    $everythingfine = false;
                    break;
                default:
                    $everythingfine = true;
            }
            if ($everythingfine) {
                $selected_academicyear = $this->request->data['ExcludedPublishedCourseExam']['academicyear'];
                $selected_program = $this->request->data['ExcludedPublishedCourseExam']['program_id'];
                $selected_program_type = $this->request->data['ExcludedPublishedCourseExam']['program_type_id'];
                $selected_semester = $this->request->data['ExcludedPublishedCourseExam']['semester'];
                $program_type_id = $this->AcademicYear->equivalent_program_type($selected_program_type);

                if ($this->role_id == ROLE_COLLEGE) {
                    $selected_year_level = $this->request->data['ExcludedPublishedCourseExam']['year_level_id'];
                    if (empty($selected_year_level) || ($selected_year_level == "All")) {
                        $selected_year_level = '%';
                    }
                    $selected_department = $this->request->data['ExcludedPublishedCourseExam']['department_id'];
                    $yearLevels = $this->ExcludedPublishedCourseExam->PublishedCourse->YearLevel->find(
                        'list',
                        array('conditions' => array('YearLevel.department_id' => $selected_department))
                    );
                    if ($selected_department == 10000) {
                        $conditions = array(
                            'PublishedCourse.academic_year' => $selected_academicyear,
                            'PublishedCourse.college_id' => $this->college_id,
                            'PublishedCourse.program_id' => $selected_program,
                            'PublishedCourse.program_type_id' => $program_type_id,
                            'PublishedCourse.semester' => $selected_semester,
                            'PublishedCourse.drop' => 0
                        );
                    } else {
                        $conditions = array(
                            'PublishedCourse.academic_year' => $selected_academicyear,
                            'PublishedCourse.department_id' => $selected_department,
                            'PublishedCourse.program_id' => $selected_program,
                            'PublishedCourse.program_type_id' => $program_type_id,
                            'PublishedCourse.year_level_id LIKE' => $selected_year_level,
                            'PublishedCourse.semester' => $selected_semester,
                            'PublishedCourse.drop' => 0
                        );
                    }
                } else {
                    $conditions = array(
                        'PublishedCourse.academic_year' => $selected_academicyear,
                        'PublishedCourse.department_id' => $this->department_id,
                        'PublishedCourse.program_id' => $selected_program,
                        'PublishedCourse.program_type_id' => $program_type_id,
                        'PublishedCourse.semester' => $selected_semester,
                        'PublishedCourse.drop' => 0,
                        "OR" => array(
                            "PublishedCourse.department_id is null",
                            "PublishedCourse.department_id" => array(0, '')
                        )
                    );
                }
                $publishedcourses = $this->ExcludedPublishedCourseExam->PublishedCourse->find('all', array(
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
                    $sections_array[$publishedcourse['Section']['name']][$key]['course_title'] = $publishedcourse['Course']['course_title'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['course_id'] = $publishedcourse['Course']['id'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['course_code'] = $publishedcourse['Course']['course_code'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['credit'] = $publishedcourse['Course']['credit'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['credit_detail'] = $publishedcourse['Course']['lecture_hours'] . ' ' . $publishedcourse['Course']['tutorial_hours'] . ' ' .
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
                            'There is no published courses to exclude from final exam in the selected criteria.'
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                } else {
                    $this->Session->write('sections_array', $sections_array);
                    $this->set(compact('sections_array', 'yearLevels'));
                }
            }
        }
        if (!empty($this->request->data) && isset($this->request->data['exclude'])) {
            //debug($this->request->data);
            $selected_published_course_id_array = array();
            foreach ($this->request->data['ExcludedPublishedCourseExams']['selected'] as $pck => $pcv) {
                if ($pcv == 1) {
                    $selected_published_course_id_array[] = $pck;
                }
            }
            $count_selected_published_course_id = count($selected_published_course_id_array);
            if ($count_selected_published_course_id > 0) {
                //Now save selected published course in excluded_published_course_exams table
                if (!empty($selected_published_course_id_array)) {
                    $selected_published_courses_name = null; //use for display purpose only
                    foreach ($selected_published_course_id_array as $published_course_id) {
                        $selected_course_id = $this->ExcludedPublishedCourseExam->PublishedCourse->field(
                            'PublishedCourse.course_id',
                            array('PublishedCourse.id' => $published_course_id)
                        );
                        $selected_published_courses_name = $selected_published_courses_name . ', ' .
                            $this->ExcludedPublishedCourseExam->PublishedCourse->Course->field(
                                'Course.course_title',
                                array('Course.id' => $selected_course_id)
                            );
                        $this->request->data['ExcludedPublishedCourseExam']['published_course_id'] = $published_course_id;
                        $this->ExcludedPublishedCourseExam->create();
                        $this->ExcludedPublishedCourseExam->save($this->request->data['ExcludedPublishedCourseExam']);
                    }
                    $this->Session->setFlash(
                        __(
                            '<span></span> Courses ' . $selected_published_courses_name . ' are
					  excluded from final exam schedule.',
                            true
                        ),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    return $this->redirect(array('action' => 'index'));
                }
            } else {
                $this->Session->setFlash(
                    __(
                        '<span></span> Please select At least 1 course to excluded from final exam
				 schedule.',
                        true
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
            $sections_array = $this->Session->read('sections_array');
            $this->set(compact('sections_array'));
        }
    }

    public function get_year_level($department_id = null)
    {

        $this->layout = 'ajax';
        //debug($this->request->data);
        $yearLevels = $this->ExcludedPublishedCourseExam->PublishedCourse->YearLevel->find('list', array(
            'conditions' =>
                array('YearLevel.department_id' => $department_id)
        ));
        $this->set(compact('yearLevels'));
    }

    public function get_year_levels_for_view($department_id = null)
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
            if ($department_id == 10000) {
                $yearLevels[10000] = '1st';
            } else {
                $yearLevels = $this->ExcludedPublishedCourseExam->PublishedCourse->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $department_id))
                );
            }

            return $yearLevels;
        }
    }
}

?>

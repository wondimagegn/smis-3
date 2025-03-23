<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class MergedSectionsExamsController extends AppController
{

    public $name = 'MergedSectionsExams';
    public $menuOptions = array(
        'parent' => 'examSchedule',
        'exclude' => array('add', 'get_year_levels_for_view'),
        'alias' => array(
            'index' => 'List Merged Sections',
            'merge' => 'Merge Sections for Exam',
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
        $this->Auth->allow('get_year_levels_for_view');
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

        //$this->MergedSectionsExam->recursive = 0;
        $programs = $this->MergedSectionsExam->PublishedCourse->Program->find('list');
        $programTypes = $this->MergedSectionsExam->PublishedCourse->ProgramType->find('list');
        $departments = $this->MergedSectionsExam->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $this->college_id))
        );
        $departments['10000'] = 'Pre/(Unassign Freshman)';

        $options = array();
        $selected_department = null;
        if (isset($this->request->data['MergedSectionsExam']['department_id']) && !empty($this->request->data['MergedSectionsExam']['department_id'])) {
            $selected_department = $this->request->data['MergedSectionsExam']['department_id'];
        }
        if (!empty($selected_department)) {
            $yearLevels = $this->_get_year_levels_list($selected_department);
            if ($selected_department == 10000) {
                $options[] = array('PublishedCourse.college_id' => $this->college_id);
            } else {
                $options[] = array('PublishedCourse.department_id' => $selected_department);
            }
        } else {
            $yearLevels = null;
            $department_ids = $this->MergedSectionsExam->PublishedCourse->Department->find(
                'list',
                array(
                    'fields' => array('Department.id', 'Department.id'),
                    'conditions' => array('Department.college_id' => $this->college_id)
                )
            );
            $options[] = array(
                "OR" => array(
                    'PublishedCourse.college_id' => $this->college_id,
                    'PublishedCourse.department_id' => $department_ids
                )
            );
        }

        if (isset($this->request->data['MergedSectionsExam']['academic_year']) && !empty($this->request->data['MergedSectionsExam']['academic_year'])) {
            $options[] = array('PublishedCourse.academic_year' => $this->request->data['MergedSectionsExam']['academic_year']);
        }
        if (isset($this->request->data['MergedSectionsExam']['semester']) && !empty($this->request->data['MergedSectionsExam']['semester'])) {
            $options[] = array('PublishedCourse.semester' => $this->request->data['MergedSectionsExam']['semester']);
        }
        if (isset($this->request->data['MergedSectionsExam']['program_id']) && !empty($this->request->data['MergedSectionsExam']['program_id'])) {
            $options[] = array('PublishedCourse.program_id' => $this->request->data['MergedSectionsExam']['program_id']);
        }
        if (isset($this->request->data['MergedSectionsExam']['program_type_id']) && !empty($this->request->data['MergedSectionsExam']['program_type_id'])) {
            $options[] = array('PublishedCourse.program_type_id' => $this->request->data['MergedSectionsExam']['program_type_id']);
        }
        if (isset($this->request->data['MergedSectionsExam']['year_level_id']) && !empty($this->request->data['MergedSectionsExam']['year_level_id'])) {
            if ($selected_department != 10000) {
                $options[] = array('PublishedCourse.year_level_id' => $this->request->data['MergedSectionsExam']['year_level_id']);
            }
        }

        $published_course_ids = $this->MergedSectionsExam->PublishedCourse->find(
            'list',
            array('fields' => array('PublishedCourse.id', 'PublishedCourse.id'), 'conditions' => $options)
        );

        $this->paginate = array(
            'conditions' => array('MergedSectionsExam.published_course_id' => $published_course_ids),
            'contain' => array(
                'Section' => array('fields' => array('Section.name', 'Section.id')),
                'PublishedCourse' => array(
                    'fields' => array(
                        'PublishedCourse.id',
                        'PublishedCourse.academic_year',
                        'PublishedCourse.semester'
                    ),
                    'Course' => array('fields' => array('Course.id', 'Course.course_code_title')),
                    'Program' => array('fields' => array('Program.name')),
                    'ProgramType' => array('fields' => array('ProgramType.name')),
                    'YearLevel' => array('fields' => array('YearLevel.name'))
                )
            )
        );

        $mergedSectionsExams = $this->paginate();
        $this->set(compact('programs', 'programTypes', 'yearLevels', 'departments', 'mergedSectionsExams'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid merged sections exam'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('mergedSectionsExam', $this->MergedSectionsExam->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->MergedSectionsExam->create();
            if ($this->MergedSectionsExam->save($this->request->data)) {
                $this->Session->setFlash(__('The merged sections exam has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The merged sections exam could not be saved. Please, try again.'));
            }
        }
        $publishedCourses = $this->MergedSectionsExam->PublishedCourse->find('list');
        $sections = $this->MergedSectionsExam->Section->find('list');
        $this->set(compact('publishedCourses', 'sections'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid merged sections exam'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->MergedSectionsExam->save($this->request->data)) {
                $this->Session->setFlash(__('The merged sections exam has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The merged sections exam could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->MergedSectionsExam->read(null, $id);
        }
        $publishedCourses = $this->MergedSectionsExam->PublishedCourse->find('list');
        $sections = $this->MergedSectionsExam->Section->find('list');
        $this->set(compact('publishedCourses', 'sections'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            //$this->Session->setFlash(__('Invalid id for merged sections exam'));
            $this->Session->setFlash(
                __('<span></span> Invalid id for merged sections exam '),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->MergedSectionsExam->delete($id)) {
            $this->Session->setFlash(
                __('<span></span> Merged sections exam deleted '),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            __('<span></span> Merged sections exam was not deleted '),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }

    public function merge()
    {

        $current_academic_year = $this->AcademicYear->current_academicyear();
        $programs = $this->MergedSectionsExam->Section->Program->find('list');
        $programTypes = $this->MergedSectionsExam->Section->ProgramType->find('list');
        $isbeforesearch = 1;
        $this->set(compact('programs', 'programTypes', 'isbeforesearch'));
        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            if ($this->Session->read('formatedSections')) {
                $this->Session->delete('formatedSections');
            }
            if ($this->Session->read('sections')) {
                $this->Session->delete('sections');
            }
            $isbeforesearch = 0;
            $selected_program = $this->request->data['MergedSectionsExam']['program_id'];
            $selected_program_type = $this->request->data['MergedSectionsExam']['program_type_id'];
            $selected_semester = $this->request->data['MergedSectionsExam']['semester'];
            ////To display each section current hosted students:
            $program_type_id = $selected_program_type;
            $find_the_equvilaent_program_type = unserialize(
                $this->MergedSectionsExam->Section->ProgramType->field(
                    'ProgramType.equivalent_to_id',
                    array('ProgramType.id' => $selected_program_type)
                )
            );
            if (!empty($find_the_equvilaent_program_type)) {
                $selected_program_type_array = array();
                $selected_program_type_array[] = $selected_program_type;
                $program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
            }
            //find all college department
            $department_ids = $this->MergedSectionsExam->PublishedCourse->Department->find(
                'list',
                array(
                    'fields' => array('Department.id'),
                    'conditions' => array('Department.college_id' => $this->college_id)
                )
            );

            $conditions = array(
                'PublishedCourse.academic_year' => $current_academic_year,
                'PublishedCourse.program_id' => $selected_program,
                'PublishedCourse.program_type_id' => $program_type_id,
                'PublishedCourse.semester' => $selected_semester,
                'PublishedCourse.drop' => 0,
                "OR" => array(
                    'PublishedCourse.college_id' => $this->college_id,
                    'PublishedCourse.department_id' => $department_ids
                )
            );
            $published_course_ids = $this->MergedSectionsExam->PublishedCourse->find(
                'list',
                array('fields' => array('PublishedCourse.id', 'PublishedCourse.id'), 'conditions' => $conditions)
            );
            $this->Session->write('published_course_ids', $published_course_ids);
            $sections = $this->MergedSectionsExam->PublishedCourse->find('all', array(
                'conditions' => $conditions,
                'fields' => array('DISTINCT PublishedCourse.section_id'),
                'contain' => array(
                    'Section' => array(
                        'fields' => array('Section.id', 'Section.name'),
                        'conditions' => array('Section.archive' => 0)
                    ),
                    'YearLevel' => array('fields' => array('YearLevel.name'))
                )
            ));

            $formatedSections = array();
            foreach ($sections as $sk => $sv) {
                if (!empty($sv['Section']['name'])) {
                    if (empty($sv['YearLevel']['name'])) {
                        $formatedSections['1st'][] = $sv;
                    } else {
                        $formatedSections[$sv['YearLevel']['name']][] = $sv;
                    }
                }
            }
            $this->Session->write('sections', $sections);
            $this->Session->write('formatedSections', $formatedSections);
            $this->set(compact('sections', 'isbeforesearch', 'formatedSections'));
        }
        // save merged sections
        if (!empty($this->request->data) && isset($this->request->data['merge'])) {
            //check atleast two sections selected, and one published course is selected.
            $count_selected_sections = 0;
            $selected_sections_for_merge_id = array();
            foreach ($this->request->data['Section']['selected'] as $key => $selected_section_id) {
                if ($selected_section_id != 0) {
                    $selected_sections_for_merge_id[] = $selected_section_id;
                }
            }
            $count_selected_sections = count($selected_sections_for_merge_id);
            //check whether 2 or more sections are selected
            if ($count_selected_sections >= 2) {
                //check at least one published course is selected.
                //$count_selected_published_course =0;
                $selected_published_course_array = array();
                $selected_section_published_course_array = array();
                if (!empty($this->request->data['MergedSectionsExams'])) {
                    foreach ($this->request->data['MergedSectionsExams']['selectedcourses'] as $spck => $spcv) {
                        if (!empty($spcv)) {
                            $selected_published_course_array[] = $spcv;
                            $selected_section_published_course_array[$spcv] = $this->request->data['MergedSectionsExams'][$spcv]['section_id'];
                        }
                    }
                    //$count_selected_published_course = count($selected_published_course_array);
                }
                $unselected_published_course_section = array_diff(
                    $selected_sections_for_merge_id,
                    $selected_section_published_course_array
                );
                if (empty($unselected_published_course_section)) {
                    if (!empty($this->request->data['merged_section_name'])) {
                        $published_course_ids = $this->Session->read('published_course_ids');
                        //check uniqueness of merged section name
                        $count_merged_section_name = 0;
                        $count_merged_section_name = $this->MergedSectionsExam->find(
                            'count',
                            array(
                                'conditions' => array(
                                    'MergedSectionsExam.published_course_id' => $published_course_ids,
                                    'MergedSectionsExam.section_name' => $this->request->data['merged_section_name']
                                )
                            )
                        );

                        if ($count_merged_section_name == 0) {
                            //Save Eached selected published course id and section name in merged_sections_courses
                            //get last higher merge key from merge sections exam
                            $last_merge_key = $this->MergedSectionsExam->find(
                                'first',
                                array(
                                    'fields' => 'MergedSectionsExam.merge_key',
                                    'order' => 'MergedSectionsExam.merge_key DESC'
                                )
                            );
                            if (!empty($last_merge_key)) {
                                $merge_key = ($last_merge_key['MergedSectionsExam']['merge_key'] + 1);
                            } else {
                                $merge_key = 1;
                            }

                            unset($this->request->data['MergedSectionsExams']);
                            $selected_published_courses_name = null; //use for display purpose only
                            $selected_sections_name = null; //use for display purpose only
                            foreach ($selected_published_course_array as $published_course_id) {
                                $selected_course_id = $this->MergedSectionsExam->PublishedCourse->field(
                                    'PublishedCourse.course_id',
                                    array('PublishedCourse.id' => $published_course_id)
                                );
                                $selected_published_courses_name = $selected_published_courses_name . ', ' . $this->MergedSectionsExam->PublishedCourse->Course->field(
                                        'Course.course_title',
                                        array('Course.id' => $selected_course_id)
                                    );
                                $this->request->data['MergedSectionsExams']['published_course_id'] = $published_course_id;
                                $this->request->data['MergedSectionsExams']['section_id'] = $selected_section_published_course_array[$published_course_id];
                                $this->request->data['MergedSectionsExams']['section_name'] = $this->request->data['merged_section_name'];

                                $this->request->data['MergedSectionsExams']['merge_key'] = $merge_key;
                                $this->MergedSectionsExam->create();
                                $this->MergedSectionsExam->save($this->request->data['MergedSectionsExams']);
                            }
                            $this->Session->setFlash(
                                __(
                                    '<span></span> Sections ' . $selected_sections_name . ' are Merged for course(s) ' . $selected_published_courses_name
                                ),
                                'default',
                                array('class' => 'success-box success-message')
                            );
                            return $this->redirect(array('action' => 'index'));
                        } else {
                            $this->Session->setFlash(
                                '<span></span> The provided Merged Section Name is occupied, Please provide unique merged section name or if the occupied merged section name is outdated,
								Please first delete it from the system in ',
                                "session_flash_link",
                                array(
                                    "class" => 'error-box error-message',
                                    "link_text" => " this page",
                                    "link_url" => array(
                                        "controller" => "MergedSectionsExams",
                                        "action" => "index",
                                        "admin" => false
                                    )
                                )
                            );
                        }
                    } else {
                        $this->Session->setFlash(
                            __('<span></span> Please provide unique Merged Section Name '),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                } else {
                    $this->Session->setFlash(
                        __(
                            '<span></span> Please select At least 1 course for each selected sections
						to merge ',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    __('<span></span> Please select at least 2 sections to merge '),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
            //$this->Session->write('selected_published_course_array',$selected_published_course_array);
            //$this->request->data['merge'] = true;
            $isbeforesearch = 0;
            $sections = $this->Session->read('sections');
            $formatedSections = $this->Session->read('formatedSections');
            $this->set(compact('sections', 'isbeforesearch', 'formatedSections'));
        }
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
                $yearLevels = $this->MergedSectionsExam->PublishedCourse->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $department_id))
                );
            }

            return $yearLevels;
        }
    }
}

?>

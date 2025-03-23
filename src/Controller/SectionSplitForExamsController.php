<?php

namespace App\Controller;

use Cake\Event\Event;

class SectionSplitForExamsController extends AppController
{

    public $name = 'SectionSplitForExams';
    public $menuOptions = array(
        'parent' => 'examSchedule',
        'exclude' => array('add', 'get_year_levels_for_view'),
        'alias' => array(
            'index' => 'List Split Sections',
            'split' => 'Split Section for Exam',
        )
    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        //$this->Auth->allow(array('*'));
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

        $programs = $this->SectionSplitForExam->PublishedCourse->Program->find('list');
        $programTypes = $this->SectionSplitForExam->PublishedCourse->ProgramType->find('list');
        $departments = $this->SectionSplitForExam->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $this->college_id))
        );
        $departments['10000'] = 'Pre/(Unassign Freshman)';

        $options = array();
        $selected_department = null;
        if (isset($this->request->data['SectionSplitForExam']['department_id']) && !empty($this->request->data['SectionSplitForExam']['department_id'])) {
            $selected_department = $this->request->data['SectionSplitForExam']['department_id'];
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
            $department_ids = $this->SectionSplitForExam->PublishedCourse->Department->find(
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

        if (isset($this->request->data['SectionSplitForExam']['academic_year']) && !empty($this->request->data['SectionSplitForExam']['academic_year'])) {
            $options[] = array('PublishedCourse.academic_year' => $this->request->data['SectionSplitForExam']['academic_year']);
        }
        if (isset($this->request->data['SectionSplitForExam']['semester']) && !empty($this->request->data['SectionSplitForExam']['semester'])) {
            $options[] = array('PublishedCourse.semester' => $this->request->data['SectionSplitForExam']['semester']);
        }
        if (isset($this->request->data['SectionSplitForExam']['program_id']) && !empty($this->request->data['SectionSplitForExam']['program_id'])) {
            $options[] = array('PublishedCourse.program_id' => $this->request->data['SectionSplitForExam']['program_id']);
        }
        if (isset($this->request->data['SectionSplitForExam']['program_type_id']) && !empty($this->request->data['SectionSplitForExam']['program_type_id'])) {
            $options[] = array('PublishedCourse.program_type_id' => $this->request->data['SectionSplitForExam']['program_type_id']);
        }
        if (isset($this->request->data['SectionSplitForExam']['year_level_id']) && !empty($this->request->data['SectionSplitForExam']['year_level_id'])) {
            if ($selected_department != 10000) {
                $options[] = array('PublishedCourse.year_level_id' => $this->request->data['SectionSplitForExam']['year_level_id']);
            }
        }

        $published_course_ids = $this->SectionSplitForExam->PublishedCourse->find(
            'list',
            array('fields' => array('PublishedCourse.id', 'PublishedCourse.id'), 'conditions' => $options)
        );

        $this->paginate = array(
            'conditions' => array('SectionSplitForExam.published_course_id' => $published_course_ids),
            'contain' => array(
                'Section' => array('fields' => array('Section.name', 'Section.id')),
                'ExamSplitSection',
                'PublishedCourse' => array(
                    'fields' => array('PublishedCourse.id'),
                    'Course' => array('fields' => ('Course.course_code_title'))
                ),
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


        $sectionSplitForExams = $this->paginate();
        $this->set(compact('programs', 'programTypes', 'yearLevels', 'departments', 'sectionSplitForExams'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid section split for exam'));
            return $this->redirect(array('action' => 'index'));
        }
        $sectionSplitForExam = $this->SectionSplitForExam->find(
            'first',
            array(
                'conditions' => array('SectionSplitForExam.id' => $id),
                'contain' => array(
                    'PublishedCourse' => array(
                        'Course' => array(
                            'fields' => array(
                                'Course.id',
                                'Course.course_code_title'
                            )
                        )
                    ),
                    'Section',
                    'ExamSplitSection'
                )
            )
        );

        $this->set(compact('sectionSplitForExam'));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->SectionSplitForExam->create();
            if ($this->SectionSplitForExam->save($this->request->data)) {
                $this->Session->setFlash(__('The section split for exam has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The section split for exam could not be saved. Please, try again.'));
            }
        }
        $sections = $this->SectionSplitForExam->Section->find('list');
        $publishedCourses = $this->SectionSplitForExam->PublishedCourse->find('list');
        $this->set(compact('sections', 'publishedCourses'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid section split for exam'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->SectionSplitForExam->save($this->request->data)) {
                $this->Session->setFlash(__('The section split for exam has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The section split for exam could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->SectionSplitForExam->read(null, $id);
        }
        $sections = $this->SectionSplitForExam->Section->find('list');
        $publishedCourses = $this->SectionSplitForExam->PublishedCourse->find('list');
        $this->set(compact('sections', 'publishedCourses'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                __('<span></span> Invalid id for section split for exam.', true),
                'default',
                array('class' => 'error-box error-message')
            );

            return $this->redirect(array('action' => 'index'));
        }
        if ($this->SectionSplitForExam->delete($id)) {
            $this->Session->setFlash(
                __('<span></span> Section split for exam deleted.', true),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }

        $this->Session->setFlash(
            __('<span></span> Section split for exam was not deleted.', true),
            'default',
            array('class' => 'error-box error-message')
        );

        return $this->redirect(array('action' => 'index'));
    }

    public function split()
    {

        $current_academic_year = $this->AcademicYear->current_academicyear();
        $programs = $this->SectionSplitForExam->Section->Program->find('list');
        $programTypes = $this->SectionSplitForExam->Section->ProgramType->find('list');
        $isbeforesearch = 1;

        $this->set(compact('programs', 'programTypes', 'isbeforesearch'));
        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            if ($this->Session->read('sections')) {
                $this->Session->delete('sections');
            }
            if ($this->Session->delete('list_of_courses')) {
                $this->Session->delete('list_of_courses');
            }
            if ($this->Session->delete('course_type_array')) {
                $this->Session->delete('course_type_array');
            }
            $isbeforesearch = 0;
            $selected_program = $this->request->data['SectionSplitForExam']['program_id'];
            $selected_program_type = $this->request->data['SectionSplitForExam']['program_type_id'];
            $selected_semester = $this->request->data['SectionSplitForExam']['semester'];
            //$this->Session->write('selected_program',$selected_program);
            //$this->Session->write('selected_program_type',$selected_program_type);
            ////To display each section current hosted students:
            $program_type_id = $selected_program_type;
            $find_the_equvilaent_program_type = unserialize(
                $this->SectionSplitForExam->Section->ProgramType->
                field('ProgramType.equivalent_to_id', array('ProgramType.id' => $selected_program_type))
            );
            if (!empty($find_the_equvilaent_program_type)) {
                $selected_program_type_array = array();
                $selected_program_type_array[] = $selected_program_type;
                $program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
            }
            //find all college departments
            $department_ids = $this->SectionSplitForExam->PublishedCourse->Department->find(
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

            $sections = $this->SectionSplitForExam->PublishedCourse->find('all', array(
                'conditions' => $conditions,
                'fields' => array('DISTINCT PublishedCourse.section_id'),
                'contain' => array(
                    'Section' => array(
                        'fields' => array('Section.id', 'Section.name'),
                        'conditions' => array('Section.archive' => 0)
                    )
                )
            ));
            $this->Session->write('sections', $sections);
            $this->set(compact('sections', 'isbeforesearch'));
        }
        // save split sections
        if (!empty($this->request->data) && isset($this->request->data['split'])) {
            //check at least one published course is selected.
            $count_selected_published_course = 0;
            $selected_published_course_array = array();
            $coursetype_for_selected_published_course_array = array();
            if (!empty($this->request->data['SectionSplitForExams'])) {
                foreach ($this->request->data['SectionSplitForExams']['selected'] as $spck => $spcv) {
                    if ($spcv != 0) {
                        $selected_published_course_array[] = $this->request->data['SectionSplitForExams'][$spck]
                        ['published_course_id'];
                        //$coursetype_for_selected_published_course_array[] = $this->request->data['SectionSplitForExam']['type'][$spck];
                    }
                }
                $count_selected_published_course = count($selected_published_course_array);
            }
            if ($count_selected_published_course > 0) {
                if (!in_array(-1, $coursetype_for_selected_published_course_array)) {
                    //save in section_split_for_exam table
                    $selected_section_id = $this->request->data['Section'][$this->request->data['SectionSplitForExam']
                    ['selectedsection']]['id'];
                    $number_split_sections = $this->request->data['SectionSplitForExam']['number_of_section'];
                    unset($this->request->data['SectionSplitForExam']);
                    $selected_published_courses_name = null; //use for display purpose only
                    foreach ($selected_published_course_array as $spck => $selected_published_course_id) {
                        $selected_course_id = $this->SectionSplitForExam->PublishedCourse->field(
                            'PublishedCourse.course_id',
                            array('PublishedCourse.id' => $selected_published_course_id)
                        );
                        $selected_published_courses_name = $selected_published_courses_name . ', ' .
                            $this->SectionSplitForExam->PublishedCourse->Course->field(
                                'Course.course_title',
                                array('Course.id' => $selected_course_id)
                            );
                        $this->request->data['SectionSplitForExam']['published_course_id'] = $selected_published_course_id;
                        $this->request->data['SectionSplitForExam']['section_id'] = $selected_section_id;
                        //$this->request->data['SectionSplitForExam']['type'] = $coursetype_for_selected_published_course_array[$spck];
                        $this->SectionSplitForExam->create();
                        $this->SectionSplitForExam->save($this->request->data['SectionSplitForExam']);
                        //know save each plited selection name and section_split_for_exam_id in
                        //exam_split_sections table
                        $this->request->data['ExamSplitSection']['section_split_for_exam_id'] =
                            $this->SectionSplitForExam->id;
                        //automaticaliy generate section name for splited sections from thery parent section name.
                        $parent_section_name = $this->SectionSplitForExam->Section->field('Section.name', array(
                            'Section.id' => $selected_section_id
                        ));
                        $variable_parent_section_name = substr(
                            $parent_section_name,
                            strrpos($parent_section_name, " ") + 1
                        );
                        if (is_numeric($variable_parent_section_name)) {
                            $variable_parent_section_name = "A";
                        } else {
                            $variable_parent_section_name = 1;
                        }
                        $split_section_name_for_display = null;
                        $exam_split_section_id_array = array();
                        for ($i = 0; $i < $number_split_sections; $i++) {
                            $split_Section_name = $parent_section_name . ' ' . $variable_parent_section_name;
                            $this->request->data['ExamSplitSection']['section_name'] = $split_Section_name;
                            $split_section_name_for_display = $split_section_name_for_display . ', ' . $split_Section_name;
                            $this->SectionSplitForExam->ExamSplitSection->create();
                            $this->SectionSplitForExam->ExamSplitSection->save(
                                $this->request->data['ExamSplitSection']
                            );
                            $exam_split_section_id_array[] = $this->SectionSplitForExam->ExamSplitSection->id;
                            if (is_numeric($variable_parent_section_name)) {
                                $variable_parent_section_name = $variable_parent_section_name + 1;
                            } else {
                                $variable_parent_section_name = ord($variable_parent_section_name);
                                $variable_parent_section_name = chr($variable_parent_section_name + 1);
                            }
                        }
                        //newly created children section students is saved in associated database table
                        $studentssections = $this->SectionSplitForExam->Section->StudentsSection->find(
                            'all',
                            array('conditions' => array('StudentsSection.section_id' => $selected_section_id))
                        );
                        $k = 0; //first child section index
                        foreach ($studentssections as $ssk => $ssv) {
                            if ($ssv['StudentsSection']['archive'] == 0) {
                                $this->request->data['StudentsExamSplitSection']['student_id'] =
                                    $ssv['StudentsSection']['student_id'];
                                $this->request->data['StudentsExamSplitSection']['course_split_section_id'] =
                                    $exam_split_section_id_array[$k];
                                $this->SectionSplitForExam->ExamSplitSection->StudentsExamSplitSection->create();
                                $this->SectionSplitForExam->ExamSplitSection->StudentsExamSplitSection->save(
                                    $this->request->data['StudentsExamSplitSection']
                                );
                                $k = $k + 1;
                                if (($k % $number_split_sections) == 0) {
                                    $k = 0;
                                }
                            }
                        }
                        $this->Session->setFlash(
                            __(
                                '<span></span> Section ' . $parent_section_name . ' is split into
							sections ' . $split_section_name_for_display . ' successfully for the courses ' .
                                $selected_published_courses_name,
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
                            '<span></span> Please select course type for the selected published
						courses.',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    __('<span></span> Please select at least 1 courses to split '),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
            $isbeforesearch = 0;
            $sections = $this->Session->read('sections');
            $this->set(compact('sections', 'isbeforesearch'));
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
                $yearLevels = $this->SectionSplitForExam->PublishedCourse->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $department_id))
                );
            }

            return $yearLevels;
        }
    }
}

?>

<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class SectionSplitForPublishedCoursesController extends AppController
{

    public $name = 'SectionSplitForPublishedCourses';
    public $menuOptions = array(
        'parent' => 'publishedCourses',
        'controllerButton' => false,
        'exclude' => array('add', 'index', 'split'),
        'alias' => array(
            //'index' => 'View Split Section for Selected Courses',
            //'split' => 'Split Section for Selected Courses',
        )
    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator');
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);

        $acyear_array_data = $this->AcademicYear->acyearArray();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
    }

    public function index()
    {

        $this->paginate = array(
            'contain' => array(
                'Section' => array('fields' => array('Section.name', 'Section.id')),
                'CourseSplitSection',
                'PublishedCourse' => array(
                    'fields' => array('PublishedCourse.id'),
                    'Course' => array('fields' => ('Course.course_code_title'))
                )
            )
        );

        if (!empty($this->request->data)) {
            $everythingfine = false;

            switch ($this->request->data) {
                case empty($this->request->data['Search']['academicyear']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select
			                 the academic year you want to view splitted sections.',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['Search']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the
			                 semester you want to view splitted sections. ',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;

                case empty($this->request->data['Search']['year_level_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the
			                 yearl level you want to view splitted sections. ',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;

                case empty($this->request->data['Search']['program_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the
			                 program you want to view splitted sections. ',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;

                case empty($this->request->data['Search']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'Please select the
			                 program type you want to view splitted sections. ',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;

                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                $options = array();
                $academic_year = $this->request->data['Search']['academicyear'];

                $commaListYearLevelIds = implode(', ', $this->request->data['Search']['year_level_id']);

                $options[] = 'SectionSplitForPublishedCourse.published_course_id
		                 IN (SELECT id FROM published_courses where department_id=' . $this->department_id . '
		                 and
		                 academic_year="' . $this->request->data['Search']['academicyear'] . '" and
		                 program_id = ' . $this->request->data['Search']['program_id'] . ' and program_type_id=' .
                    $this->request->data['Search']['program_type_id'] . ' and
		                 year_level_id in (' . $commaListYearLevelIds . '))';

                $sectionSplitForPublishedCourses = $this->paginate($options);
                if (empty($sectionSplitForPublishedCourses)) {
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'There is no
		                    splitted section in given criteria. ',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                } else {
                    $this->set(compact('sectionSplitForPublishedCourses'));
                }
            }
        } else {
            $this->set('sectionSplitForPublishedCourses', $this->paginate());
        }

        $yearLevels = $this->SectionSplitForPublishedCourse->PublishedCourse->YearLevel->find('list', array(
            'conditions' => array(
                'YearLevel.department_id' => $this->department_id
            )
        ));

        $programTypes = $this->SectionSplitForPublishedCourse->PublishedCourse->ProgramType->find('list');
        $programs = $this->SectionSplitForPublishedCourse->PublishedCourse->Program->find('list');
        $this->set(compact('yearLevels', 'programTypes', 'colleges', 'programs', 'departments'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span> ' . __('Invalid section split for published course'),
                'default',
                array('class' => 'info-box info-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('sectionSplitForPublishedCourse', $this->SectionSplitForPublishedCourse->read(null, $id));
    }


    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span> ' . __('Invalid id for section split for published course'),
                'default',
                array('class' => 'info-box info-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        // check if grade is submitted

        $getChildren = $this->SectionSplitForPublishedCourse->find(
            'first',
            array(
                'conditions' => array('SectionSplitForPublishedCourse.id' => $id),
                'contain' => array('CourseSplitSection' => array('Student' => array('StudentsCourseSplitSection')))
            )
        );

        $isResultSubmitted = ClassRegistry::init('ExamResult')->isExamResultSubmitted(
            $getChildren['SectionSplitForPublishedCourse']['published_course_id']
        );
        if ($isResultSubmitted == 0) {
            $courseSplitSectionIds = array();
            $splittedSectionStudents = array();
            foreach ($getChildren['CourseSplitSection'] as $k => $v) {
                $courseSplitSectionIds[] = $v['id'];
            }
            if ($this->SectionSplitForPublishedCourse->delete($id, true)) {
                $this->Session->setFlash(
                    '<span></span> ' . __('Section split for published course deleted'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                $this->SectionSplitForPublishedCourse->
                CourseSplitSection->deleteAll(array('CourseSplitSection.id' => $courseSplitSectionIds), false);
                $this->SectionSplitForPublishedCourse->
                CourseSplitSection->Student->StudentsCourseSplitSection->deleteAll(
                    array('StudentsCourseSplitSection.course_split_section_id' => $courseSplitSectionIds),
                    false
                );

                $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                '<span></span> ' . __('Section split for published course was not deleted'),
                'default',
                array('class' => 'error-box error-message')
            );
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(
                '<span></span> ' . __(
                    'You can not delete the splitted section because
		  exam result/grade is recorded in the name of the section.',
                    true
                ),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
    }

    public function split()
    {

        $programs = $this->SectionSplitForPublishedCourse->Section->Program->find('list');
        $programTypes = $this->SectionSplitForPublishedCourse->Section->ProgramType->find('list');
        $yearLevels = $this->SectionSplitForPublishedCourse->Section->YearLevel->find('list', array(
            'conditions' =>
                array('YearLevel.department_id' => $this->department_id)
        ));

        $isbeforesearch = 1;

        if (!empty($this->request->data['SectionSplitForPublishedCourse']['academicyear'])) {
            $current_academic_year = $this->request->data['SectionSplitForPublishedCourse']['academicyear'];
        } else {
            $current_academic_year = $this->AcademicYear->current_academicyear();
        }

        $this->set(compact('programs', 'programTypes', 'isbeforesearch', 'yearLevels'));

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            if ($this->Session->read('sections_curriculum_name')) {
                $this->Session->delete('sections_curriculum_name');
            }
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
            $selected_program = $this->request->data['SectionSplitForPublishedCourse']['program_id'];
            $selected_program_type = $this->request->data['SectionSplitForPublishedCourse']['program_type_id'];
            $selected_semester = $this->request->data['SectionSplitForPublishedCourse']['semester'];
            $selected_year_level = $this->request->data['SectionSplitForPublishedCourse']['year_level_id'];
            //$this->Session->write('selected_program',$selected_program);
            //$this->Session->write('selected_program_type',$selected_program_type);
            ////To display each section current hosted students:
            $program_type_id = $selected_program_type;
            $find_the_equvilaent_program_type = unserialize(
                $this->SectionSplitForPublishedCourse->Section->ProgramType->
                field('ProgramType.equivalent_to_id', array('ProgramType.id' => $selected_program_type))
            );
            if (!empty($find_the_equvilaent_program_type)) {
                $selected_program_type_array = array();
                $selected_program_type_array[] = $selected_program_type;
                $program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
            }
            //Search using by department and year level as well if user role is not college (use role is department)
            if (ROLE_COLLEGE != $this->role_id) {
                $selected_year_level = $this->request->data['SectionSplitForPublishedCourse']['year_level_id'];
                //$this->Session->write('selected_year_level',$selected_year_level);
                $conditions = array(
                    'PublishedCourse.academic_year' => $current_academic_year,
                    'PublishedCourse.department_id' => $this->department_id,
                    'PublishedCourse.program_id' => $selected_program,
                    'PublishedCourse.program_type_id' => $program_type_id,
                    'PublishedCourse.year_level_id' => $selected_year_level,
                    'PublishedCourse.semester' => $selected_semester,
                    'PublishedCourse.drop' => 0
                );
            } else {
                $conditions = array(
                    'PublishedCourse.academic_year' => $current_academic_year,
                    'PublishedCourse.college_id' => $this->college_id,
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
            $sections = $this->SectionSplitForPublishedCourse->PublishedCourse->find('all', array(
                'conditions' => $conditions,
                'fields' => array('DISTINCT PublishedCourse.section_id'),
                'contain' => array(
                    'Section' => array(
                        'fields' => array('Section.id', 'Section.name'),
                        'conditions' => array('Section.archive' => 0)
                    )
                )
            ));
            //$sections=$this->SectionSplitForPublishedCourse->PublishedCourse->find('all');
            //to display curriculum

            $studentsections = $this->SectionSplitForPublishedCourse->Section->studentsection(
                $this->college_id,
                $this->role_id,
                $this->department_id,
                $selected_program,
                $program_type_id,
                $current_academic_year,
                $selected_year_level
            );
            //Find section curriculum for one of section students

            $sections_curriculum_name = $this->SectionSplitForPublishedCourse->Section->sectionscurriculum(
                $studentsections
            );

            //$current_sections_occupation = $this->SectionSplitForPublishedCourse->Section->currentsectionsoccupation($sections);
            $this->Session->write('sections_curriculum_name', $sections_curriculum_name);
            $this->Session->write('sections', $sections);
            $this->set(compact('sections', 'isbeforesearch', 'sections_curriculum_name'));
        }
        // save split sections
        if (!empty($this->request->data) && isset($this->request->data['split'])) {
            //check at least one published course is selected.
            $count_selected_published_course = 0;
            $selected_published_course_array = array();
            $coursetype_for_selected_published_course_array = array();
            if (!empty($this->request->data['SectionSplitForPublishedCourses'])) {
                foreach ($this->request->data['SectionSplitForPublishedCourses']['selected'] as $spck => $spcv) {
                    if ($spcv != 0) {
                        $selected_published_course_array[] = $this->request->data['SectionSplitForPublishedCourses'][$spck]
                        ['published_course_id'];
                        $coursetype_for_selected_published_course_array[] = $this->request->data['SectionSplitForPublishedCourse']
                        ['type'][$spck];
                    }
                }
                $count_selected_published_course = count($selected_published_course_array);
            }
            if ($count_selected_published_course > 0) {
                if (!in_array(-1, $coursetype_for_selected_published_course_array)) {
                    //save in section_split_for_published_courses table
                    $selected_section_id = $this->request->data['Section'][$this->request->data['SectionSplitForPublishedCourse']
                    ['selectedsection']]['id'];
                    $number_split_sections = $this->request->data['SectionSplitForPublishedCourse']['number_of_section'];
                    unset($this->request->data['SectionSplitForPublishedCourse']);
                    $selected_published_courses_name = null; //use for display purpose only
                    foreach ($selected_published_course_array as $spck => $selected_published_course_id) {
                        $selected_course_id = $this->SectionSplitForPublishedCourse->PublishedCourse->field(
                            'PublishedCourse.course_id',
                            array('PublishedCourse.id' => $selected_published_course_id)
                        );
                        $selected_published_courses_name = $selected_published_courses_name . ', ' .
                            $this->SectionSplitForPublishedCourse->PublishedCourse->Course->field(
                                'Course.course_title',
                                array('Course.id' => $selected_course_id)
                            );
                        $this->request->data['SectionSplitForPublishedCourse']['published_course_id'] = $selected_published_course_id;
                        $this->request->data['SectionSplitForPublishedCourse']['section_id'] = $selected_section_id;
                        $this->request->data['SectionSplitForPublishedCourse']['type'] =
                            $coursetype_for_selected_published_course_array[$spck];
                        $this->SectionSplitForPublishedCourse->create();
                        $this->SectionSplitForPublishedCourse->save(
                            $this->request->data['SectionSplitForPublishedCourse']
                        );
                        //know save each plited selection name and section_split_for_published_course_id in
                        //course_split_sections table
                        $this->request->data['CourseSplitSection']['section_split_for_published_course_id'] =
                            $this->SectionSplitForPublishedCourse->id;
                        //automaticaliy generate section name for splited sections from thery parent section name.
                        $parent_section_name = $this->SectionSplitForPublishedCourse->Section->field(
                            'Section.name',
                            array(
                                'Section.id' => $selected_section_id
                            )
                        );
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
                        $course_split_section_id_array = array();
                        for ($i = 0; $i < $number_split_sections; $i++) {
                            $split_Section_name = $parent_section_name . ' ' . $variable_parent_section_name;
                            $this->request->data['CourseSplitSection']['section_name'] = $split_Section_name;
                            $split_section_name_for_display = $split_section_name_for_display . ', ' . $split_Section_name;
                            $this->SectionSplitForPublishedCourse->CourseSplitSection->create();
                            $this->SectionSplitForPublishedCourse->CourseSplitSection->save(
                                $this->request->data['CourseSplitSection']
                            );
                            $course_split_section_id_array[] = $this->SectionSplitForPublishedCourse->
                            CourseSplitSection->id;
                            if (is_numeric($variable_parent_section_name)) {
                                $variable_parent_section_name = $variable_parent_section_name + 1;
                            } else {
                                $variable_parent_section_name = ord($variable_parent_section_name);
                                $variable_parent_section_name = chr($variable_parent_section_name + 1);
                            }
                        }
                        //newly created children section students is saved in associated database table
                        $studentssections = $this->SectionSplitForPublishedCourse->Section->StudentsSection->find(
                            'all',
                            array('conditions' => array('StudentsSection.section_id' => $selected_section_id))
                        );
                        $k = 0; //first child section index
                        foreach ($studentssections as $ssk => $ssv) {
                            if ($ssv['StudentsSection']['archive'] == 0) {
                                $this->request->data['StudentsCourseSplitSection']['student_id'] =
                                    $ssv['StudentsSection']['student_id'];
                                $this->request->data['StudentsCourseSplitSection']['course_split_section_id'] =
                                    $course_split_section_id_array[$k];
                                $this->SectionSplitForPublishedCourse->CourseSplitSection->StudentsCourseSplitSection->create(
                                );
                                $this->SectionSplitForPublishedCourse->CourseSplitSection->StudentsCourseSplitSection->save(
                                    $this->request->data['StudentsCourseSplitSection']
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
            $sections_curriculum_name = $this->Session->read('sections_curriculum_name');
            $sections = $this->Session->read('sections');
            $this->set(compact('sections', 'isbeforesearch', 'sections_curriculum_name'));
        }
    }
}

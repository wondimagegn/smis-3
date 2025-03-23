<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class MakeupExamsController extends AppController
{

    public $name = 'MakeupExams';
    public $menuOptions = array(
        'parent' => 'grades',
        'exclude' => array(
            'edit',
            'delete',
            'view',
            'deleteFxMakeupAssignment'
        ),
        'alias' => array(
            'index' => 'List Makeup & Supplmentary Exams',
            'add' => 'Assign Makeup Exam',
            'assign_fx' => 'Assign Supplmentary Exam Fx'
        )
    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator');
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        //$this->Auth->Allow('assign_fx','deleteFxMakeupAssignment');
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);

        $current_academicyear = $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        $curr_ac_year_expoded = explode('/', $current_academicyear);
        $previous_academicyear = $current_academicyear;

        if (!empty($curr_ac_year_expoded)) {
            $previous_academicyear = ($curr_ac_year_expoded[0] - 1) . '/' . ($curr_ac_year_expoded[1] - 1);
        }

        $years_to_look_list_for_display = $acyear_array_data = $this->AcademicYear->academicYearInArray(
            APPLICATION_START_YEAR,
            (explode('/', $current_academicyear)[0])
        );

        $programs = ClassRegistry::init('Program')->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
        );
        $program_types = $programTypes = ClassRegistry::init('ProgramType')->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
        );

        if (($this->role_id == ROLE_REGISTRAR && $this->Session->read(
                    'Auth.User'
                )['is_admin'] == 0) || $this->role_id == ROLE_COLLEGE || $this->role_id == ROLE_DEPARTMENT) {
            $academicYearRangeForNonAdminRegistrar = new $this->AcademicYear(new ComponentCollection());
            $years_to_look_list_for_display = $acyear_array_data = $academicYearRangeForNonAdminRegistrar->academicYearInArray(
                ((explode('/', $defaultacademicyear)[0]) - ACY_BACK_FOR_GRADE_CHANGE_APPROVAL),
                (explode('/', $defaultacademicyear)[0])
            );
        }

        if (count($years_to_look_list_for_display) >= 2) {
            $startYr = array_pop($years_to_look_list_for_display);
            $endYr = reset($years_to_look_list_for_display);
            $years_to_look_list_for_display = 'from ' . $startYr . ' up to ' . $endYr;
        } else {
            if (count($years_to_look_list_for_display) == 1) {
                $years_to_look_list_for_display = ' on ' . $defaultacademicyear;
            } else {
                $years_to_look_list_for_display = '';
            }
        }

        debug($years_to_look_list_for_display);

        $this->set(
            compact(
                'acyear_array_data',
                'defaultacademicyear',
                'previous_academicyear',
                'program_types',
                'programTypes',
                'programs',
                'years_to_look_list_for_display'
            )
        );

        unset($this->request->data['User']['password']);
    }

    public function index()
    {

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
            $makeup_exams = array();
            $programs = $this->MakeupExam->PublishedCourse->Program->find(
                'list',
                array('conditions' => array('Program.id' => $this->program_ids))
            );
            $program_types = $this->MakeupExam->PublishedCourse->ProgramType->find(
                'list',
                array('conditions' => array('ProgramType.id' => $this->program_type_ids))
            );

            if (!empty($this->request->data)) {
                $makeup_exams = $this->MakeupExam->getmakeupExams(
                    $this->department_id,
                    $this->request->data['MakeupExam']['acadamic_year'],
                    $this->request->data['MakeupExam']['program_id'],
                    $this->request->data['MakeupExam']['program_type_id'],
                    $this->request->data['MakeupExam']['semester']
                );
            }
            $this->set(compact('makeup_exams', 'programs', 'program_types', 'makeup_exams'));
        } else {
            $this->Flash->error(
                __(
                    'You need to have department role to access makeup exam administration. Please contact your system administrator to get department role.'
                )
            );
            return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
        }
    }

    public function add()
    {

        $grade_history = array();
        $register_or_add = array();

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
            if (!empty($this->request->data)) {
                $save_is_ok = true;
                $makeup_exam['MakeupExam']['minute_number'] = $this->request->data['MakeupExam']['minute_number'];
                $makeup_exam['MakeupExam']['student_id'] = $this->request->data['MakeupExam']['student_id'];

                if (trim($this->request->data['MakeupExam']['exam_published_course_id']) != "0") {
                    $makeup_exam['MakeupExam']['published_course_id'] = $this->request->data['MakeupExam']['exam_published_course_id'];
                } else {
                    $this->Flash->error(
                        __('You are required to select the course that the student is going to take exam.')
                    );
                    $save_is_ok = false;
                }

                if ($this->request->data['MakeupExam']['course_registration_id'] != "0") {
                    $register_or_add = explode('~', $this->request->data['MakeupExam']['course_registration_id']);

                    //Duplication checking by course add
                    if (strcasecmp($register_or_add[1], 'add') == 0) {
                        $published_course_id = $this->MakeupExam->PublishedCourse->CourseAdd->field(
                            'published_course_id',
                            array('id' => $register_or_add[0])
                        );
                        $grade_history = $this->MakeupExam->ExamGradeChange->ExamGrade->CourseAdd->getCourseAddGradeHistory(
                            $register_or_add[0]
                        );
                        $listOfGradesAllowedForRepeat = $this->MakeupExam->PublishedCourse->getRepeatableGradeGivenPublishedCourse(
                            $published_course_id
                        );

                        if (!(!empty($grade_history) && in_array(
                                $grade_history[0]['ExamGrade']['grade'],
                                $listOfGradesAllowedForRepeat
                            ))) {
                            $this->Flash->error(
                                __(
                                    'Makeup exam is allowed for ' . implode(
                                        ',',
                                        $listOfGradesAllowedForRepeat
                                    ) . ' grade.'
                                )
                            );
                            $save_is_ok = false;
                        } else {
                            if ($this->request->data['MakeupExam']['exam_published_course_id'] != "0" && $published_course_id == $this->request->data['MakeupExam']['exam_published_course_id']) {
                                //If a student is assigned for the same course he curentlly taking (circular assignment)
                                $this->Flash->error(
                                    __(
                                        'The student already add the selected course and s/he can take the exam at the class where s/he add.'
                                    )
                                );
                                $save_is_ok = false;
                            } else {
                                //Check if the student is already assigned for makeup exam
                                $not_processed_makeup_exam = $this->MakeupExam->find('count', array(
                                    'conditions' => array(
                                        'MakeupExam.course_add_id' => $register_or_add[0],
                                        //'MakeupExam.published_course_id' => $makeup_exam['MakeupExam']['published_course_id'],
                                        'MakeupExam.id NOT IN (SELECT makeup_exam_id FROM exam_grade_changes WHERE makeup_exam_id IS NOT NULL)'
                                    ),
                                    'contain' => array()
                                ));

                                if (!empty($not_processed_makeup_exam)) {
                                    $this->Flash->error(
                                        __(
                                            'The student is already assigned to take the exam for the exam course you select. If you want to apply changes, please delete and re-enter the record before any exam result/grade is submited. If exam result/grade is already entered, please contact the instructor to apply changes. The instructor will be required to request cancelation of grade if his/her grade submition is proccessed by the department and registrar.'
                                        )
                                    );
                                    $save_is_ok = false;
                                } else {
                                    $garde_status = $this->MakeupExam->CourseAdd->isAnyGradeOnProcess(
                                        $register_or_add[0]
                                    );
                                    if ($garde_status == true) {
                                        $this->Flash->error(
                                            __(
                                                'The course which the student add is either on grade submission or grade change process. Please make sure that student exam grade is submited and processed by the department, college (if there is any grade change on process) and registrar.'
                                            )
                                        );
                                        $save_is_ok = false;
                                    } else {
                                        $makeup_exam['MakeupExam']['course_add_id'] = $register_or_add[0];
                                    }
                                }
                            }
                        }
                        //debug($published_course_id);
                    } else {
                        //Duplication checking by course registration
                        $published_course_id = $this->MakeupExam->PublishedCourse->CourseRegistration->field(
                            'published_course_id',
                            array('id' => $register_or_add[0])
                        );
                        $grade_history = $this->MakeupExam->PublishedCourse->CourseRegistration->getCourseRegistrationGradeHistory(
                            $register_or_add[0]
                        );
                        $listOfGradesAllowedForRepeat = $this->MakeupExam->PublishedCourse->Course->getRepeatableGradeGivenPublishedCourse(
                            $published_course_id
                        );

                        if (!(!empty($grade_history) && in_array(
                                $grade_history[0]['ExamGrade']['grade'],
                                $listOfGradesAllowedForRepeat
                            ))) {
                            $this->Flash->error(
                                __(
                                    'Makeup exam is allowed for only ' . implode(
                                        ',',
                                        $listOfGradesAllowedForRepeat
                                    ) . ' grade.'
                                )
                            );
                            $save_is_ok = false;
                        } else {
                            if ($this->request->data['MakeupExam']['exam_published_course_id'] != "0" && $published_course_id == $this->request->data['MakeupExam']['exam_published_course_id']) {
                                $this->Flash->error(
                                    __(
                                        'The student already registered for the selected course and s/he can take the exam at her/his registered class.'
                                    )
                                );
                                $save_is_ok = false;
                            } else {
                                //Check if the student is already assigned for makeup exam
                                if ($this->request->data['MakeupExam']['exam_published_course_id'] != "0") {
                                    $not_processed_makeup_exam = $this->MakeupExam->find('all', array(
                                        'conditions' => array(
                                            'MakeupExam.course_registration_id' => $register_or_add[0],
                                            //'MakeupExam.published_course_id' => $makeup_exam['MakeupExam']['published_course_id'],
                                            'MakeupExam.id NOT IN (SELECT makeup_exam_id FROM exam_grade_changes WHERE makeup_exam_id IS NOT NULL)'
                                        ),
                                        'contain' => array()
                                    ));

                                    if (!empty($not_processed_makeup_exam)) {
                                        $this->Flash->error(
                                            __(
                                                'The student is already assigned to take the exam for the exam course you select. If you want to apply changes, please delete and re-enter the record before any exam result/grade is submited. If exam result/grade is already entered, please contact the instructor to apply changes. The instructor will be required to request cancelation of grade if his/her grade submition is proccessed by the department and registrar.'
                                            )
                                        );
                                        $save_is_ok = false;
                                    } else {
                                        $garde_status = $this->MakeupExam->CourseRegistration->isAnyGradeOnProcess(
                                            $register_or_add[0]
                                        );
                                        if ($garde_status == true) {
                                            $this->Flash->error(
                                                __(
                                                    'The course for which the student registered is either on grade submission or grade change process. Please make sure that student exam grade is submited and processed by the department, college (if there is any grade change on process) and registrar.'
                                                )
                                            );
                                            $save_is_ok = false;
                                        } else {
                                            $makeup_exam['MakeupExam']['course_registration_id'] = $register_or_add[0];
                                        }
                                    }
                                }
                            }
                        }
                        //debug($published_course_id);
                    }
                } else {
                    $this->Flash->error(
                        __('You are required to select the course for which the student is going to take makeup exam.')
                    );
                    $save_is_ok = false;
                }
                //exit();
                //debug($register_or_add);
                debug($makeup_exam);

                $this->MakeupExam->create();

                if ($save_is_ok) {
                    if ($this->MakeupExam->save($makeup_exam)) {
                        $this->Flash->success(__('The makeup exam has been saved.'));
                        return $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Flash->error(__('The makeup exam could not be saved. Please, try again.'));
                    }
                }

                //redisplay
                //$programs = $this->MakeupExam->PublishedCourse->Section->Program->find('list');
                $programs = $this->MakeupExam->PublishedCourse->Section->Program->find(
                    'list',
                    array('conditions' => array('Program.id' => $this->program_ids))
                );
                $program_id = $this->request->data['MakeupExam']['program_id'];

                $student_sections = $this->MakeupExam->PublishedCourse->Section->allDepartmentSectionsOrganizedByProgramTypeSuppExam(
                    $this->department_id,
                    1,
                    $this->request->data['MakeupExam']['program_id'],
                    3
                );
                $student_section_id = $this->request->data['MakeupExam']['student_section_id'];

                $students = $this->MakeupExam->PublishedCourse->Section->allStudents($student_section_id);
                $student_id = $this->request->data['MakeupExam']['student_id'];
                //debug($students);

                //$student_registered_courses = $this->MakeupExam->PublishedCourse->CourseRegistration->Student->getStudentRegisteredAndAddCourses($student_id);
                $student_registered_courses = $this->MakeupExam->PublishedCourse->CourseRegistration->Student->getPossibleStudentRegisteredAndAddCoursesForSup(
                    $student_id
                );
                $registered_course_id = $this->request->data['MakeupExam']['course_registration_id'];

                //$departments = $this->MakeupExam->PublishedCourse->Department->allDepartmentsByCollege(0, 1);
                $departments = $this->MakeupExam->PublishedCourse->Department->allDepartmentsByCampus(
                    $this->department_id,
                    $include_freshman_program = 0,
                    $only_active = 1
                );
                $department_id = $this->request->data['MakeupExam']['department_id'];

                $exam_sections = $this->MakeupExam->PublishedCourse->Section->allDepartmentSectionsOrganizedByProgramTypeSuppExam(
                    $department_id,
                    1,
                    $program_id,
                    3
                );
                $exam_section_id = $this->request->data['MakeupExam']['exam_section_id'];

                $exam_published_courses = $this->MakeupExam->PublishedCourse->lastPublishedCoursesForSection(
                    $exam_section_id
                );
                $exam_published_course_id = $this->request->data['MakeupExam']['exam_published_course_id'];
            } else {
                //$programs = $this->MakeupExam->PublishedCourse->Section->Program->find('list');
                $programs = $this->MakeupExam->PublishedCourse->Section->Program->find(
                    'list',
                    array('conditions' => array('Program.id' => $this->program_ids))
                );
                $program_id = '';

                $student_sections = $this->MakeupExam->PublishedCourse->Section->allDepartmentSectionsOrganizedByProgramTypeSuppExam(
                    $this->department_id,
                    1,
                    $program_id,
                    3
                );
                $student_section_id = "";

                $students = array();
                $student_id = "";

                $student_registered_courses = array();
                $registered_course_id = "";

                //$departments = $this->MakeupExam->PublishedCourse->Department->allDepartmentsByCollege(0, 1);
                $departments = $this->MakeupExam->PublishedCourse->Department->allDepartmentsByCampus(
                    $this->department_id,
                    $include_freshman_program = 0,
                    $only_active = 1
                );
                $department_id = '';

                $exam_sections = array();
                $exam_section_id = "";

                $exam_published_courses = array();
                $exam_published_course_id = "";

                $grade_history = "";
            }

            $programs = array('0' => '[ Select Program ]') + $programs;
            $student_sections = array('0' => '[ Select Section ]') + $student_sections;
            $departments = array('0' => '[ Select Department ]') + $departments;
            $students = array('0' => '[ Select Student ]') + $students;
            $exam_sections = array('0' => '[ Select Section ]') + $exam_sections;
            $exam_published_courses = array('0' => '[ Select Course ]') + $exam_published_courses;
            $student_registered_courses = array('0' => '[ Select Course ]') + $student_registered_courses;

            $this->set(
                compact(
                    'student_sections',
                    'student_section_id',
                    'programs',
                    'program_id',
                    'students',
                    'student_id',
                    'student_registered_courses',
                    'registered_course_id',
                    'departments',
                    'department_id',
                    'exam_sections',
                    'exam_section_id',
                    'exam_published_courses',
                    'exam_published_course_id',
                    'grade_history',
                    'register_or_add'
                )
            );
        } else {
            $this->Flash->warning(
                __(
                    'You need to have department role to access makeup exam administration. Please contact your system administrator to get department role.'
                )
            );
            return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
        }
    }

    public function assign_fx($published_course_id = null)
    {

        //initialization
        if (isset($this->request->data['MakeupExam']) && !empty($this->request->data['MakeupExam'])) {
            $program_id = $this->request->data['MakeupExam']['program_id'];
            $program_type_id = $this->request->data['MakeupExam']['program_type_id'];
            $department_id = $this->request->data['MakeupExam']['department_id'];
            $academic_year_selected = $this->request->data['MakeupExam']['acadamic_year'];
            $semester_selected = $this->request->data['MakeupExam']['semester'];
        }

        $published_course_combo_id = null;
        $department_combo_id = null;
        $publishedCourses = array();
        $students_with_ng = array();
        $have_message = false;

        $departments = array();
        $only_pre_assigned = 0;

        $programs = $this->MakeupExam->PublishedCourse->Program->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids))
        );
        $program_types = $this->MakeupExam->PublishedCourse->ProgramType->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids))
        );

        /* if ((!empty($this->department_ids) || !empty($this->college_ids))) {
            $departments = $this->MakeupExam->CourseRegistration->Student->Department->allDepartmentsByCollege2(0, $this->department_ids, $this->college_ids);
        } else {
            $departments = $this->MakeupExam->CourseRegistration->Student->Department->find('list', array('conditions' => array('Department.id' => $this->department_id), 'recursive' => -1));
        } */

        if (!empty($this->department_ids)) {
            $departments = $this->MakeupExam->CourseRegistration->Student->Department->allDepartmentsByCollege2(
                0,
                $this->department_ids,
                $this->college_ids,
                1
            );
        } else {
            if (!empty($this->college_ids)) {
                if ($this->onlyPre) {
                    $only_pre_assigned = 1;
                    $departments = $this->MakeupExam->CourseRegistration->Student->Department->onlyFreshmanInAllColleges(
                        $this->college_ids,
                        1
                    );
                } else {
                    $departments = $this->MakeupExam->CourseRegistration->Student->Department->allDepartmentsByCollege2(
                        0,
                        $this->department_ids,
                        $this->college_ids,
                        1
                    );
                }
            }
        }

        if ($this->Session->read('Auth.User')['is_admin'] == 1) {
            $departments = $this->MakeupExam->CourseRegistration->Student->Department->allDepartmentInCollegeIncludingPre(
                $this->department_ids,
                $this->college_ids,
                $includePre = 1,
                $only_active = 1
            );
        }

        //List published course button is clicked
        if (isset($this->request->data['listPublishedCourses'])) {
            //There is nothing to do here for the time being
        } else {
            if (isset($this->request->data['assignFxMakeupExam'])) {
                //Change FX Grade button is clicked
                //debug($this->request->data);

                if (trim($this->request->data['MakeupExam']['minute_number']) == "") {
                    $this->Flash->error(__('Please enter minute number.'));
                } else {
                    $makeupExamAssignments = array();
                    $count = 0;
                    debug($this->request->data);

                    foreach ($this->request->data['MakeupExam'] as $key => $makeup) {
                        if (is_numeric($key) && $makeup['gp'] == 1) {
                            if (isset($makeup['course_registration_id'])) {
                                $not_processed_makeup_exam = $this->MakeupExam->find('count', array(
                                    'conditions' => array(
                                        'MakeupExam.course_registration_id' => $makeup['course_registration_id'],
                                        'MakeupExam.id NOT IN (SELECT makeup_exam_id FROM exam_grade_changes WHERE makeup_exam_id IS NOT NULL)'
                                    ),
                                    'contain' => array()
                                ));

                                if ($not_processed_makeup_exam == 0) {
                                    $makeupExamAssignments['MakeupExam'][$count]['course_registration_id'] = $makeup['course_registration_id'];
                                    $makeupExamAssignments['MakeupExam'][$count]['published_course_id'] = $this->request->data['MakeupExam']['exam_published_course_id'];
                                    $makeupExamAssignments['MakeupExam'][$count]['minute_number'] = $this->request->data['MakeupExam']['minute_number'];
                                    $makeupExamAssignments['MakeupExam'][$count]['student_id'] = $makeup['student_id'];
                                }
                            } else {
                                if (isset($makeup['course_add_id'])) {
                                    debug($makeup);

                                    $not_processed_makeup_exam = $this->MakeupExam->find('count', array(
                                        'conditions' => array(
                                            'MakeupExam.course_add_id' => $makeup['course_add_id'],
                                            'MakeupExam.id NOT IN (SELECT makeup_exam_id FROM exam_grade_changes WHERE makeup_exam_id IS NOT NULL)'
                                        ),
                                        'contain' => array()
                                    ));

                                    debug($not_processed_makeup_exam);

                                    if ($not_processed_makeup_exam == 0) {
                                        $makeupExamAssignments['MakeupExam'][$count]['course_add_id'] = $makeup['course_add_id'];
                                        $makeupExamAssignments['MakeupExam'][$count]['published_course_id'] = $this->request->data['MakeupExam']['exam_published_course_id'];
                                        $makeupExamAssignments['MakeupExam'][$count]['minute_number'] = $this->request->data['MakeupExam']['minute_number'];
                                        $makeupExamAssignments['MakeupExam'][$count]['student_id'] = $makeup['student_id'];
                                    }
                                }
                            }

                            $count++;
                        }
                    }

                    debug($makeupExamAssignments);

                    if ($this->MakeupExam->saveAll($makeupExamAssignments['MakeupExam'])) {
                        $makeupExamAssignmentsCount = count($makeupExamAssignments['MakeupExam']);
                        $this->Flash->success(
                            __(
                                'Makeup exam for ' . $makeupExamAssignmentsCount . ' ' . ($makeupExamAssignmentsCount > 1 ? 'courses have' : 'course has') . ' been saved.'
                            )
                        );
                        return $this->redirect(array('action' => 'assign_fx'));
                    } else {
                        $this->Flash->error(__('The makeup exam could not be saved. Please, try again.'));
                    }
                }
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['listPublishedCourses'])) {
            $department_id = $this->request->data['MakeupExam']['department_id'];
            $this->request->data['MakeupExam']['published_course_id'] = null;
            $published_course_id = null;
            $department_combo_id = $department_id;
            $college_id = explode('~', $department_id);
            //$registrar = ($this->role_id == ROLE_REGISTRAR) ? 1 : 0;

            if (is_array($college_id) && count($college_id) > 1) {
                $college_id = $college_id[1];
                $publishedCourses = $this->MakeupExam->CourseRegistration->listOfCoursesWithFx(
                    $college_id,
                    $this->request->data['MakeupExam']['acadamic_year'],
                    $this->request->data['MakeupExam']['semester'],
                    $this->request->data['MakeupExam']['program_id'],
                    $this->request->data['MakeupExam']['program_type_id'],
                    1,
                    1
                );
            } else {
                $publishedCourses = $this->MakeupExam->CourseRegistration->listOfCoursesWithFx(
                    $department_id,
                    $this->request->data['MakeupExam']['acadamic_year'],
                    $this->request->data['MakeupExam']['semester'],
                    $this->request->data['MakeupExam']['program_id'],
                    $this->request->data['MakeupExam']['program_type_id'],
                    0,
                    1
                );
                debug($publishedCourses);
            }

            if (empty($publishedCourses)) {
                $this->Flash->error(
                    __(
                        'We could not find courses with selected criteria which is selected by students to retake the FX exam. Only one FX examination retake for single student is allowed and multiple FX application is not allowed and a maximum of 3 FX throughout student stay.'
                    )
                );
                //return $this->redirect(array('action' => 'assign_fx'));
            } else {
                $publishedCourses = array('0' => '[ Select Published Course ]') + $publishedCourses;
            }
        }

        //When published course is selected from the combo box

        if (!empty($published_course_id) || (isset($this->request->data['MakeupExam']['published_course_id']) && $this->request->data['MakeupExam']['published_course_id'] != 0)) {
            if (isset($this->request->data['ExamGrade']['published_course_id'])) {
                $published_course_id = $this->request->data['ExamGrade']['published_course_id'];
            }

            $publishedCourses = array();

            $published_course = $this->MakeupExam->CourseRegistration->PublishedCourse->find(
                'first',
                array(
                    'conditions' => array('PublishedCourse.id' => $published_course_id),
                    'contain' => array('Section', 'YearLevel')
                )
            );

            if (empty($published_course) || (!empty($published_course['PublishedCourse']['department_id']) && $published_course['PublishedCourse']['given_by_department_id'] != $this->department_id && $this->role_id == ROLE_DEPARTMENT) || (!empty($published_course['PublishedCourse']['department_id']) && !in_array(
                        $published_course['PublishedCourse']['department_id'],
                        $this->department_ids
                    ) && $this->role_id == ROLE_REGISTRAR) || (!empty($published_course['PublishedCourse']['college_id']) && !in_array(
                        $published_course['PublishedCourse']['college_id'],
                        $this->college_ids
                    ) && $this->role_id == ROLE_REGISTRAR) || (!empty($published_course['PublishedCourse']['given_by_department_id']) && $this->department_id != $published_course['PublishedCourse']['given_by_department_id'] && $this->role_id != ROLE_REGISTRAR)) {
                $this->Flash->error(__('Please select a valid published course.'));
            } else {
                if (empty($published_course['PublishedCourse']['department_id'])) {
                    if (!empty($published_course['PublishedCourse']['given_by_department_id'])) {
                        $publishedCourses = $this->MakeupExam->CourseRegistration->listOfCoursesWithFx(
                            $published_course['PublishedCourse']['given_by_department_id'],
                            $published_course['PublishedCourse']['academic_year'],
                            $published_course['PublishedCourse']['semester'],
                            $published_course['PublishedCourse']['program_id'],
                            $published_course['PublishedCourse']['program_type_id'],
                            0,
                            1
                        );
                        $department_combo_id = $published_course['PublishedCourse']['given_by_department_id'];
                    } else {
                        $publishedCourses = $this->MakeupExam->CourseRegistration->listOfCoursesWithFx(
                            $published_course['PublishedCourse']['college_id'],
                            $published_course['PublishedCourse']['academic_year'],
                            $published_course['PublishedCourse']['semester'],
                            $published_course['PublishedCourse']['program_id'],
                            $published_course['PublishedCourse']['program_type_id'],
                            0,
                            1
                        );
                        $department_combo_id = 'c~' . $published_course['PublishedCourse']['college_id'];
                    }
                } else {
                    $publishedCourses = $this->MakeupExam->CourseRegistration->listOfCoursesWithFx(
                        $published_course['PublishedCourse']['given_by_department_id'],
                        $published_course['PublishedCourse']['academic_year'],
                        $published_course['PublishedCourse']['semester'],
                        $published_course['PublishedCourse']['program_id'],
                        $published_course['PublishedCourse']['program_type_id'],
                        0,
                        1
                    );
                    $department_combo_id = $published_course['PublishedCourse']['department_id'];
                }
            }

            $published_course_combo_id = $published_course_id;
            // $students_with_fx = $this->MakeupExam->CourseRegistration->ExamGrade->getStudentsWithFXForMakeupAssignment($published_course_id,true);

            $students_with_fx = $this->MakeupExam->CourseRegistration->PublishedCourse->getStudentSelectedFxExamPublishedCourse(
                $published_course_id
            );

            debug($students_with_fx);

            if (empty($students_with_fx)) {
                if ($have_message == false) {
                    $this->Flash->info(__('There is no student with Fx for the selected course.'));
                }
            }

            $program_id = $published_course['PublishedCourse']['program_id'];
            $program_type_id = $published_course['PublishedCourse']['program_type_id'];
            //$department_id = $published_course['PublishedCourse']['department_id'];
            $academic_year_selected = $published_course['PublishedCourse']['academic_year'];
            $semester_selected = $published_course['PublishedCourse']['semester'];

            $department_id = (isset($published_course['PublishedCourse']['college_id']) && !empty($published_course['PublishedCourse']['college_id']) ? 'c~' . $published_course['PublishedCourse']['college_id'] : $published_course['PublishedCourse']['department_id']);

            $sectionsHaveSameCourses = $this->MakeupExam->CourseRegistration->PublishedCourse->listSimilarPublishedCoursesForCombo(
                $published_course['PublishedCourse']['id']
            );
            $selectedPublishedCourseDetail = $this->MakeupExam->CourseRegistration->PublishedCourse->find(
                'first',
                array(
                    'conditions' => array('PublishedCourse.id' => $published_course['PublishedCourse']['id']),
                    'contain' => array('Section', 'YearLevel')
                )
            );
            $this->request->data['MakeupExam']['department_id'] = $department_id;
        }

        $this->set(
            compact(
                'publishedCourses',
                'programs',
                'program_types',
                'selectedPublishedCourseDetail',
                'departments',
                'publishedCourses',
                'published_course_combo_id',
                'department_combo_id',
                'students_with_fx',
                'applicable_grades',
                'program_id',
                'program_type_id',
                'department_id',
                'academic_year_selected',
                'semester_selected',
                'sectionsHaveSameCourses'
            )
        );
    }

    public function delete($id = null)
    {

        if (!$id && $this->MakeupExam->exists($id)) {
            $this->Flash->error(__('Invalid makeup exam!'));
            return $this->redirect(array('action' => 'index'));
        }

        if ($this->MakeupExam->canItBeDeleted($id)) {
            if ($this->MakeupExam->delete($id)) {
                $this->Flash->success(__('Makeup exam deleted'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Flash->error(__('Makeup exam was not deleted'));
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->Flash->warning(__('Result is already recorded for the makeup exam and it can not be deleted.'));
            return $this->redirect(array('action' => 'index'));
        }
    }

    public function deleteFxMakeupAssignment($id = null)
    {

        $publishedCourseId = $this->MakeupExam->field('MakeupExam.published_course_id', array('MakeupExam.id' => $id));

        if (!$id && $this->MakeupExam->exists($id)) {
            $this->Flash->error(__('Invalid makeup exam assignmet!'));
            return $this->redirect(array('action' => 'assign_fx', $publishedCourseId));
        }

        if ($this->MakeupExam->canItBeDeleted($id)) {
            if ($this->MakeupExam->delete($id)) {
                $this->Flash->success(__('Makeup exam assignmet deleted'));
                return $this->redirect(array('action' => 'assign_fx', $publishedCourseId));
            }
            $this->Flash->error(__('Makeup exam was not deleted'));
            return $this->redirect(array('action' => 'assign_fx', $publishedCourseId));
        } else {
            $this->Flash->warning(
                __('Result is already recorded for the makeup exam assignment and it can not be deleted.')
            );
            return $this->redirect(array('action' => 'assign_fx', $publishedCourseId));
        }
    }
}

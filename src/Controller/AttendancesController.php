<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class AttendancesController extends AppController
{

    public $name = 'Attendances';
    public $components = array('AcademicYear');
    public $menuOptions = array(
        'parent' => 'grades',
        'exclude' => array('index', 'edit', 'delete', 'view'),
        'alias' => array(
            'take_attendance' => 'Take Attendance',
            'instructor_view_attendance' => 'View Attendance',
            'department_view_attendance' => 'View Instructor\'s Attendance',
            'freshman_view_attendance' => 'View Freshman Attendance'
        )
    );

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        //$this->Auth->Allow('');
        if ($this->Session->check('Message.auth')) {
            $this->Session->delete('Message.auth');
        }
        if ($this->Auth->user() && in_array($this->request->params['action'], array('login'))) {
            return $this->redirect($this->Auth->logout());
        }
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyearArray();
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
    }

    public function take_attendance($published_course_id = null, $selected_attendance_date = null)
    {

        $selected_acadamic_year = $this->AcademicYear->current_academicyear();
        $selected_semester = 'I';
        $published_course_combo_id = "";
        $publishedCourses = array();

        //Start: Checking if the user is eligible to manage published attendance
        $login_instructor_staff_id = $this->Attendance->PublishedCourse->CourseInstructorAssignment->Staff->field(
            'id',
            array('Staff.user_id' => $this->Auth->user('id'))
        );
        if (!empty($this->request->data)) {
            $published_course_id = $this->request->data['Attendance']['published_course_id'];
        }
        if (!empty($published_course_id)) {
            $student_course_register_and_adds = $this->Attendance->PublishedCourse->getStudentsTakingPublishedCourse(
                $published_course_id
            );
            $grade_submission_status = $this->Attendance->PublishedCourse->CourseAdd->ExamResult->getExamGradeSubmissionStatus(
                $published_course_id,
                $student_course_register_and_adds
            );
            $assigned_instructor_staff_id = $this->Attendance->PublishedCourse->CourseInstructorAssignment->field(
                'staff_id',
                array(
                    'CourseInstructorAssignment.published_course_id' => $published_course_id,
                    'CourseInstructorAssignment.type LIKE \'%Lecture%\'',
                    'isprimary' => 1
                )
            );
            $published_course_department = $this->Attendance->PublishedCourse->find(
                'first',
                array(
                    'conditions' => array('PublishedCourse.id' => $published_course_id),
                    'contain' => array('Section' => array('Department'))
                )
            );
            //Do you have the right to manage attendance
            if ($login_instructor_staff_id != $assigned_instructor_staff_id) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'Sorry the selected course is not assigned to you to take attendance. Please select a valid course.'
                    ),
                    'default',
                    array('class' => 'error-message error-box')
                );
                return $this->redirect(array('action' => 'take_attendance'));
            }
            if (0 && $grade_submission_status['grade_submited'] == 1) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'Sorry grade is already submitted for the selected course. Please use the following view attendance tool to get attendance details.'
                    ),
                    'default',
                    array('class' => 'info-message info-box')
                );
                return $this->redirect(array('action' => 'take_attendance'));
            }
            //End of do you have the right to manage exam result and grade
        }
        //End: Checking if the user is eligible to manage published attendance

        if (!empty($published_course_id)) {
            $section_and_course_detail = $this->Attendance->PublishedCourse->find('first', array(
                'conditions' => array(
                    'PublishedCourse.id' => $published_course_id
                ),
                'contain' => array('Section', 'Course')
            ));
            $section_detail = $section_and_course_detail['Section'];
            $course_detail = $section_and_course_detail['Course'];
            //debug($section_and_course_detail);
            $selected_acadamic_year = $section_and_course_detail['PublishedCourse']['academic_year'];
            $selected_semester = $section_and_course_detail['PublishedCourse']['semester'];
            $publishedCourses = $this->Attendance->PublishedCourse->CourseInstructorAssignment->listOfCoursesInstructorAssignedBySection(
                $section_and_course_detail['PublishedCourse']['academic_year'],
                $section_and_course_detail['PublishedCourse']['semester'],
                $login_instructor_staff_id,
                0
            );
            $attendance_to_be_taken_dates = $this->Attendance->PublishedCourse->CourseSchedule->getDateListToTakeAttendance(
                $published_course_id
            );
            $published_course_combo_id = $published_course_id;
            if (!empty($selected_attendance_date)) {
                $attendance_dates_for_checking = array_keys($attendance_to_be_taken_dates);
                if (!in_array($selected_attendance_date, $attendance_dates_for_checking)) {
                    $this->Session->setFlash(
                        '<span></span>' . __('Please select a valid attendance date.'),
                        'default',
                        array('class' => 'error-message error-box')
                    );
                    return $this->redirect(array('action' => 'take_attendance', $published_course_id));
                } else {
                    $student_registers = $student_course_register_and_adds['register'];
                    $student_adds = $student_course_register_and_adds['add'];
                    foreach ($student_registers as $key => $student_register) {
                        $attendance_detail = $this->Attendance->find(
                            'first',
                            array(
                                'conditions' =>
                                    array(
                                        'Attendance.published_course_id' => $published_course_id,
                                        'Attendance.attendance_date' => $selected_attendance_date,
                                        'Attendance.student_id' => $student_register['Student']['id']
                                    ),
                                'recursive' => -1
                            )
                        );
                        if (!empty($attendance_detail)) {
                            $student_registers[$key]['Attendance'] = $attendance_detail['Attendance'];
                        } else {
                            $student_registers[$key]['Attendance'] = array();
                        }
                    }
                    foreach ($student_adds as $key => $student_add) {
                        $attendance_detail = $this->Attendance->find(
                            'first',
                            array(
                                'conditions' =>
                                    array(
                                        'Attendance.published_course_id' => $published_course_id,
                                        'Attendance.attendance_date' => $selected_attendance_date,
                                        'Attendance.student_id' => $student_add['Student']['id']
                                    ),
                                'recursive' => -1
                            )
                        );
                        if (!empty($attendance_detail)) {
                            $student_adds[$key]['Attendance'] = $attendance_detail['Attendance'];
                        } else {
                            $student_adds[$key]['Attendance'] = array();
                        }
                    }
                }
            }
        }
        if (!empty($this->request->data)) {
            $attendance_to_be_taken_dates = $this->Attendance->PublishedCourse->CourseSchedule->getDateListToTakeAttendance(
                $this->request->data['Attendance']['published_course_id']
            );
            $attendance_dates_for_checking = array_keys($attendance_to_be_taken_dates);
            if ($this->request->data['Attendance']['attendance_date'] == 0) {
                $this->Session->setFlash(
                    '<span></span>' . __('Please select attendance date.'),
                    'default',
                    array('class' => 'error-message error-box')
                );
            } else {
                if (!in_array($this->request->data['Attendance']['attendance_date'], $attendance_dates_for_checking)) {
                    $this->Session->setFlash(
                        '<span></span>' . __('Please select a valid attendance date.'),
                        'default',
                        array('class' => 'error-message error-box')
                    );
                } else {
                    foreach ($this->request->data['Student'] as $key => &$attendance) {
                        $attendance['attendance_date'] = $this->request->data['Attendance']['attendance_date'];
                        $attendance['published_course_id'] = $this->request->data['Attendance']['published_course_id'];
                        $attendance['attendance_type'] = 'lecture';
                    }
                    if ($this->Attendance->saveAll($this->request->data['Student'], array('validate' => false))) {
                        $this->Session->setFlash(
                            '<span></span>' . __('Attendance is successfully taken.'),
                            'default',
                            array('class' => 'success-message success-box')
                        );
                        return $this->redirect(
                            array(
                                'action' => 'take_attendance',
                                $this->request->data['Attendance']['published_course_id'],
                                $this->request->data['Attendance']['attendance_date']
                            )
                        );
                    } else {
                        $this->Session->setFlash(
                            '<span></span>' . __('Attendance is not taken. Please try again.'),
                            'default',
                            array('class' => 'error-message error-box')
                        );
                    }
                }
            }
        }
        if (empty($publishedCourses)) {
            $publishedCourses = $this->Attendance->PublishedCourse->CourseInstructorAssignment->listOfCoursesInstructorAssignedBySection(
                $selected_acadamic_year,
                $selected_semester,
                $login_instructor_staff_id,
                0
            );
            if (empty($publishedCourses)) {
                $publishedCourses = array(0 => 'Select Academic Year & Semester');
            } else {
                $publishedCourses = array(0 => 'Select Course') + $publishedCourses;
            }
        }

        $this->set(
            compact(
                'section_detail',
                'course_detail',
                'student_registers',
                'student_adds',
                'grade_submission_status',
                'attendance_to_be_taken_dates',
                'selected_attendance_date'
            )
        );
        $this->set(
            compact('publishedCourses', 'published_course_combo_id', 'selected_acadamic_year', 'selected_semester')
        );
    }

    public function index()
    {

        if ($this->Acl->check($this->Auth->user(), 'controllers/attendances/take_attendance')) {
            return $this->redirect('take_attendance');
        } else {
            if ($this->Acl->check($this->Auth->user(), 'controllers/attendances/department_view_attendance')) {
                return $this->redirect('department_view_attendance');
            } else {
                if ($this->Acl->check($this->Auth->user(), 'controllers/attendances/freshman_view_attendance')) {
                    return $this->redirect('freshman_view_attendance');
                }
            }
        }
    }

    public function instructor_view_attendance($published_course_id = null)
    {

        $this->__view($published_course_id, 0, 0);
    }

    public function freshman_view_attendance($published_course_id = null)
    {

        $this->__department_and_freshman_view_attendance($published_course_id, 1, 1);
    }

    public function department_view_attendance($published_course_id = null)
    {

        $this->__department_and_freshman_view_attendance($published_course_id, 1, 0);
    }

    private function __department_and_freshman_view_attendance(
        $published_course_id = null,
        $is_department = 1,
        $is_freshman = 0
    ) {

        $department_id = "";
        $selected_acadamic_year = "";
        $selected_semester = "";
        $program_id = "";
        $program_type_id = "";
        $college_id = "";
        $selected_acadamic_year = $this->AcademicYear->current_academicyear();
        $selected_semester = 'I';
        $published_course_combo_id = "";
        $publishedCourses = array();
        $published_course_combo_id = "";

        $programs = $this->Attendance->PublishedCourse->Section->Program->find('list');
        $program_types = $this->Attendance->PublishedCourse->Section->ProgramType->find('list');

        if (isset($this->request->data['listPublishedCourses'])) {
            if ($is_freshman == 1) {
                $publishedCourses = $this->Attendance->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection(
                    $this->college_id,
                    $this->request->data['Attendance']['acadamic_year'],
                    $this->request->data['Attendance']['semester'],
                    $this->request->data['Attendance']['program_id'],
                    $this->request->data['Attendance']['program_type_id']
                );
            } else {
                $publishedCourses = $this->Attendance->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection(
                    $this->department_id,
                    $this->request->data['Attendance']['acadamic_year'],
                    $this->request->data['Attendance']['semester'],
                    $this->request->data['Attendance']['program_id'],
                    $this->request->data['Attendance']['program_type_id']
                );
            }
            if (empty($publishedCourses)) {
                $this->Session->setFlash(
                    '<span></span>' . __('Sorry there is no published courses by the selected criteria.'),
                    'default',
                    array('class' => 'info-message info-box')
                );
                return $this->redirect(
                    array('action' => ($is_freshman == 1 ? 'freshman_view_attendance' : 'department_view_attendance'))
                );
            }
        }

        if (!empty($published_course_id) || isset($this->request->data['viewCourseAttendance'])) {
            $this->__view($published_course_id, 1, $is_freshman);
            return;
        }

        if (empty($publishedCourses)) {
            $publishedCourses = array(0 => 'Select Academic Year & Semester');
        } else {
            $publishedCourses = array(0 => 'Select Course') + $publishedCourses;
        }
        $this->set(
            compact(
                'programs',
                'program_types',
                'program_id',
                'program_type_id',
                'selected_semester',
                'publishedCourses',
                'published_course_combo_id',
                'selected_acadamic_year'
            )
        );
        $this->render('department_view_attendance');
    }


    private function __view($published_course_id = null, $is_department = 0, $is_freshman = 0)
    {

        /*
        1. Display academic year, semester and courses selection form
        2. When the above is selected, display list of courses by section
        3. When a course is selected, retrieve and display attendance date range
        4. When the view attendance button is clicked, display the attendance
        */
        $selected_acadamic_year = $this->AcademicYear->current_academicyear();
        $selected_semester = 'I';
        $published_course_combo_id = "";
        $publishedCourses = array();

        //Start: Checking if the user is eligible to manage published attendance
        $login_instructor_staff_id = $this->Attendance->PublishedCourse->CourseInstructorAssignment->Staff->field(
            'id',
            array('Staff.user_id' => $this->Auth->user('id'))
        );
        if (!empty($this->request->data)) {
            $published_course_id = $this->request->data['Attendance']['published_course_id'];
        }
        if (!empty($published_course_id)) {
            $student_course_register_and_adds = $this->Attendance->PublishedCourse->getStudentsTakingPublishedCourse(
                $published_course_id
            );
            $grade_submission_status = $this->Attendance->PublishedCourse->CourseAdd->ExamResult->getExamGradeSubmissionStatus(
                $published_course_id,
                $student_course_register_and_adds
            );
            $assigned_instructor_staff_id = $this->Attendance->PublishedCourse->CourseInstructorAssignment->field(
                'staff_id',
                array(
                    'CourseInstructorAssignment.published_course_id' => $published_course_id,
                    'CourseInstructorAssignment.type LIKE \'%Lecture%\'',
                    'isprimary' => 1
                )
            );
            $published_course_department = $this->Attendance->PublishedCourse->find(
                'first',
                array(
                    'conditions' => array('PublishedCourse.id' => $published_course_id),
                    'contain' => array('Section' => array('Department'))
                )
            );
            //Do you have the right to manage attendance
            if (!($is_department == 1 || $login_instructor_staff_id == $assigned_instructor_staff_id)) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'Sorry the selected course is not assigned to you to view attendance. Please select a valid course.'
                    ),
                    'default',
                    array('class' => 'error-message error-box')
                );
                return $this->redirect(
                    array('action' => ($is_department == 1 ? ($is_freshman == 1 ? 'freshman_view_attendance' : 'department_view_attendance') : 'instructor_view_attendance'))
                );
            }
            //End of do you have the right to manage exam result and grade
        }
        //End: Checking if the user is eligible to manage published attendance


        if (!empty($published_course_id)) {
            $section_and_course_detail = $this->Attendance->PublishedCourse->find('first', array(
                'conditions' => array(
                    'PublishedCourse.id' => $published_course_id
                ),
                'contain' => array('Section', 'Course')
            ));
            $section_detail = $section_and_course_detail['Section'];
            $course_detail = $section_and_course_detail['Course'];
            $selected_acadamic_year = $section_and_course_detail['PublishedCourse']['academic_year'];
            $selected_semester = $section_and_course_detail['PublishedCourse']['semester'];
            if ($is_department == 1) {
                $programs = $this->Attendance->PublishedCourse->Section->Program->find('list');
                $program_types = $this->Attendance->PublishedCourse->Section->ProgramType->find('list');
                $program_id = $section_and_course_detail['PublishedCourse']['program_id'];
                $program_type_id = $section_and_course_detail['PublishedCourse']['program_type_id'];
                if ($is_freshman == 1) {
                    $publishedCourses = $this->Attendance->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection(
                        $this->college_id,
                        $selected_acadamic_year,
                        $selected_semester,
                        $program_id,
                        $program_type_id
                    );
                } else {
                    $publishedCourses = $this->Attendance->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection(
                        $this->department_id,
                        $selected_acadamic_year,
                        $selected_semester,
                        $program_id,
                        $program_type_id
                    );
                }
            } else {
                $publishedCourses = $this->Attendance->PublishedCourse->CourseInstructorAssignment->listOfCoursesInstructorAssignedBySection(
                    $section_and_course_detail['PublishedCourse']['academic_year'],
                    $section_and_course_detail['PublishedCourse']['semester'],
                    $login_instructor_staff_id,
                    0
                );
            }
            $attendance_taken_date_list = $this->Attendance->getListOfDateAttendanceTaken($published_course_id);
            $published_course_combo_id = $published_course_id;
            if (empty($attendance_taken_date_list)) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'Sorry there is no recorded attendance for the selected course. Please check this course later after an attendance is taken.'
                    ),
                    'default',
                    array('class' => 'info-message info-box')
                );
                //$this->redirect(array('action' => ($is_department == 1 ? 'department_view_attendance' : 'instructor_view_attendance')));
            }
        }

        if (isset($this->request->data['viewCourseAttendance'])) {
            $attendance_taken_date_list = $this->Attendance->getListOfDateAttendanceTaken($published_course_id);
            $attendance_dates_for_checking = array_keys($attendance_taken_date_list);
            if (!in_array(
                    $this->request->data['Attendance']['attendance_start_date'],
                    $attendance_dates_for_checking
                ) || !in_array(
                    $this->request->data['Attendance']['attendance_end_date'],
                    $attendance_dates_for_checking
                )) {
                $this->Session->setFlash(
                    '<span></span>' . __('Please select a valid attendance date.'),
                    'default',
                    array('class' => 'error-message error-box')
                );
                return $this->redirect(
                    array(
                        'action' => ($is_department == 1 ? ($is_freshman == 1 ? 'freshman_view_attendance' : 'department_view_attendance') : 'instructor_view_attendance'),
                        $published_course_id
                    )
                );
            }
            if ($this->request->data['Attendance']['attendance_start_date'] > $this->request->data['Attendance']['attendance_end_date']) {
                $this->Session->setFlash(
                    '<span></span>' . __('Please select a valid attendance date.'),
                    'default',
                    array('class' => 'error-message error-box')
                );
                return $this->redirect(
                    array(
                        'action' => ($is_department == 1 ? ($is_freshman == 1 ? 'freshman_view_attendance' : 'department_view_attendance') : 'instructor_view_attendance'),
                        $published_course_id
                    )
                );
            } else {
                $student_registers = $student_course_register_and_adds['register'];
                $student_adds = $student_course_register_and_adds['add'];
                $student_course_attendance_details = $this->Attendance->getCourseAttendanceDetail(
                    $this->request->data['Attendance']['published_course_id'],
                    $this->request->data['Attendance']['attendance_start_date'],
                    $this->request->data['Attendance']['attendance_end_date'],
                    $student_registers,
                    $student_adds
                );
            }
        }
        if (empty($publishedCourses)) {
            if ($is_department == 1) {
                if ($is_freshman == 1) {
                    $publishedCourses = $this->Attendance->PublishedCourse->CourseInstructorAssignment->listOfCoursesCollegeFreshTakingOrgBySection(
                        $this->college_id,
                        $selected_acadamic_year,
                        $selected_semester,
                        1,
                        1
                    );
                } else {
                    $publishedCourses = $this->Attendance->PublishedCourse->CourseInstructorAssignment->listOfCoursesSectionsTakingOrgBySection(
                        $this->department_id,
                        $selected_acadamic_year,
                        $selected_semester,
                        1,
                        1
                    );
                }
            } else {
                $publishedCourses = $this->Attendance->PublishedCourse->CourseInstructorAssignment->listOfCoursesInstructorAssignedBySection(
                    $selected_acadamic_year,
                    $selected_semester,
                    $login_instructor_staff_id,
                    0
                );
            }
        }
        if (empty($publishedCourses))
            $publishedCourses = array(0 => 'Select Academic Year & Semester');
        else {
            $publishedCourses = array(0 => 'Select Course') + $publishedCourses;
        }

        $this->set(
            compact(
                'student_course_attendance_details',
                'attendance_taken_date_list',
                'section_detail',
                'course_detail',
                'student_registers',
                'student_adds',
                'grade_submission_status',
                'attendance_to_be_taken_dates',
                'selected_attendance_date',
                'programs',
                'program_types',
                'program_id',
                'program_type_id'
            )
        );
        $this->set(
            compact('publishedCourses', 'published_course_combo_id', 'selected_acadamic_year', 'selected_semester')
        );
        if ($is_department == 1) {
            $this->render('department_view_attendance');
        } else {
            $this->render('view');
        }
    }

}

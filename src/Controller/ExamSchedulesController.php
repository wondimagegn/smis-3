<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ExamSchedulesController extends AppController
{

    public $name = 'ExamSchedules';
    public $menuOptions = array(
        'controllerButton' => false,
        'exclude' => array('*'),
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
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acadamicYears = $this->AcademicYear->acyearArray();
        $default_academic_year = $this->AcademicYear->currentAcademicYear();
        $this->set(compact('acadamicYears', 'default_academic_year'));
    }

    public function cancel_invigilator_assignment($invigilator_id = null)
    {

        $invigilatorDetail = $this->ExamSchedule->Invigilator->find(
            'first',
            array(
                'conditions' =>
                    array(
                        'Invigilator.id' => $invigilator_id
                    ),
                'contain' =>
                    array(
                        'Staff',
                        'StaffForExam',
                        'ExamSchedule' =>
                            array(
                                'PublishedCourse' =>
                                    array(
                                        'Course',
                                        'Section',
                                        'Department'
                                    )
                            )
                    )
            )
        );
        if (!empty($invigilatorDetail['Invigilator']['staff_for_exam_id'])) {
            $staff_name = $invigilatorDetail['StaffForExam']['first_name'] . ' ' . $invigilatorDetail['StaffForExam']['middle_name'] . ' ' . $invigilatorDetail['StaffForExam']['last_name'];
        } else {
            $staff_name = $invigilatorDetail['Staff']['first_name'] . ' ' . $invigilatorDetail['Staff']['middle_name'] . ' ' . $invigilatorDetail['Staff']['last_name'];
        }

        if (!($invigilatorDetail['ExamSchedule']['PublishedCourse']['college_id'] == $this->college_id || $invigilatorDetail['ExamSchedule']['PublishedCourse']['Department']['college_id'] == $this->college_id)) {
            $this->Session->setFlash(
                '<span></span>' . __('You are not authorized to manage invigilator assignment for the selected staff.'),
                'default',
                array('class' => 'error-message error-box')
            );
            return $this->redirect(array('action' => 'college_exam_schedule_view'));
        } else {
            if ($this->ExamSchedule->Invigilator->delete($invigilator_id)) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        '<u>' . $staff_name . '</u> is successfully canceled from his/her invigilator assignment for the course <u>' . $invigilatorDetail['ExamSchedule']['PublishedCourse']['Course']['course_title'] . ' (' . $invigilatorDetail['ExamSchedule']['PublishedCourse']['Course']['course_code'] . ')</u> of <u>' . $invigilatorDetail['ExamSchedule']['PublishedCourse']['Section']['name'] . '</u> section.'
                    ),
                    'default',
                    array('class' => 'success-message success-box')
                );
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        '<u>' . $staff_name . '</u> invigilator assignment cancellation is failed. Please try again.'
                    ),
                    'default',
                    array('class' => 'error-message error-box')
                );
            }
            return $this->redirect(array('action' => 'college_exam_schedule_view'));
        }
    }

    public function college_exam_schedule_view()
    {

        $viewExamSchedule = false;

        //When change exam hall is clicked
        for ($i = 1; $i <= $this->request->data['ExamSchedule']['exam_schedule_count']; $i++) {
            if (isset($this->request->data['changeClassRoom_' . $i])) {
                if (!isset($this->request->data['ExamSchedule']['class_room_id_' . $i]) || $this->request->data['ExamSchedule']['class_room_id_' . $i] == 0 || $this->request->data['ExamSchedule']['class_room_block_id_' . $i] == 0) {
                    $this->Session->setFlash(
                        '<span></span>' . __('You are required to select class room to change exam hall.'),
                        'default',
                        array('class' => 'error-message error-box')
                    );
                } else {
                    $examScheduleUpdate['id'] = $this->request->data['ExamSchedule']['exam_schedule_id_' . $i];
                    $examScheduleUpdate['class_room_id'] = $this->request->data['ExamSchedule']['class_room_id_' . $i];
                    if ($this->ExamSchedule->save($examScheduleUpdate)) {
                        $examScheduleDetail = $this->ExamSchedule->find(
                            'first',
                            array(
                                'conditions' =>
                                    array(
                                        'ExamSchedule.id' => $examScheduleUpdate['id']
                                    ),
                                'contain' =>
                                    array(
                                        'ClassRoom' =>
                                            array(
                                                'ClassRoomBlock'
                                            ),
                                        'PublishedCourse' =>
                                            array(
                                                'Course',
                                                'Section'
                                            )
                                    )
                            )
                        );
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                'Exam hall for the course <u>' . $examScheduleDetail['PublishedCourse']['Course']['course_title'] . ' (' . $examScheduleDetail['PublishedCourse']['Course']['course_code'] . ')</u> of <u>' . $examScheduleDetail['PublishedCourse']['Section']['name'] . '</u> section is changed to <u>' . $examScheduleDetail['ClassRoom']['room_code'] . ' (' . $examScheduleDetail['ClassRoom']['ClassRoomBlock']['block_code'] . ')</u> exam hall.'
                            ),
                            'default',
                            array('class' => 'success-message success-box')
                        );
                    }
                }
                $viewExamSchedule = true;
                unset($this->request->data['ExamSchedule']['class_room_id_' . $i]);
                unset($this->request->data['ExamSchedule']['class_room_block_id_' . $i]);
            } else {
                unset($this->request->data['ExamSchedule']['class_room_id_' . $i]);
                unset($this->request->data['ExamSchedule']['class_room_block_id_' . $i]);
            }
        }
        //When change exam schedule date is clicked
        for ($i = 1; $i <= $this->request->data['ExamSchedule']['exam_schedule_count']; $i++) {
            if (isset($this->request->data['changeExamDate_' . $i])) {
                $is_exam_date_valid = checkdate(
                    $this->request->data['ExamSchedule']['exam_date_' . $i]['month'],
                    $this->request->data['ExamSchedule']['exam_date_' . $i]['day'],
                    $this->request->data['ExamSchedule']['exam_date_' . $i]['year']
                );
                if (!$is_exam_date_valid) {
                    $this->Session->setFlash(
                        '<span></span>' . __('Please select a valid date.'),
                        'default',
                        array('class' => 'error-message error-box')
                    );
                } else {
                    $examScheduleUpdate['id'] = $this->request->data['ExamSchedule']['exam_schedule_id_' . $i];
                    $examScheduleUpdate['exam_date'] = $this->request->data['ExamSchedule']['exam_date_' . $i];
                    if ($this->ExamSchedule->save($examScheduleUpdate)) {
                        $examScheduleDetail = $this->ExamSchedule->find(
                            'first',
                            array(
                                'conditions' =>
                                    array(
                                        'ExamSchedule.id' => $examScheduleUpdate['id']
                                    ),
                                'contain' =>
                                    array(
                                        'ClassRoom' =>
                                            array(
                                                'ClassRoomBlock'
                                            ),
                                        'PublishedCourse' =>
                                            array(
                                                'Course',
                                                'Section'
                                            )
                                    )
                            )
                        );
                        $new_exam_schedule = date(
                            "F j, Y",
                            mktime(
                                0,
                                0,
                                0,
                                substr($examScheduleDetail['ExamSchedule']['exam_date'], 5, 2),
                                substr($examScheduleDetail['ExamSchedule']['exam_date'], 8, 2),
                                substr($examScheduleDetail['ExamSchedule']['exam_date'], 0, 4)
                            )
                        );
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                'Exam date for the course <u>' . $examScheduleDetail['PublishedCourse']['Course']['course_title'] . ' (' . $examScheduleDetail['PublishedCourse']['Course']['course_code'] . ')</u> of <u>' . $examScheduleDetail['PublishedCourse']['Section']['name'] . '</u> section is changed to <u>' . $new_exam_schedule . '</u>'
                            ),
                            'default',
                            array('class' => 'success-message success-box')
                        );
                    }
                }
                $viewExamSchedule = true;
                unset($this->request->data['ExamSchedule']['exam_date_' . $i]);
            } else {
                unset($this->request->data['ExamSchedule']['exam_date_' . $i]);
            }
        }
        //When change exam schedule date session is clicked
        for ($i = 1; $i <= $this->request->data['ExamSchedule']['exam_schedule_count']; $i++) {
            if (isset($this->request->data['changeExamSession_' . $i])) {
                $examScheduleUpdate['id'] = $this->request->data['ExamSchedule']['exam_schedule_id_' . $i];
                $examScheduleUpdate['session'] = $this->request->data['ExamSchedule']['session_' . $i];
                if ($this->ExamSchedule->save($examScheduleUpdate)) {
                    $examScheduleDetail = $this->ExamSchedule->find(
                        'first',
                        array(
                            'conditions' =>
                                array(
                                    'ExamSchedule.id' => $examScheduleUpdate['id']
                                ),
                            'contain' =>
                                array(
                                    'ClassRoom' =>
                                        array(
                                            'ClassRoomBlock'
                                        ),
                                    'PublishedCourse' =>
                                        array(
                                            'Course',
                                            'Section'
                                        )
                                )
                        )
                    );
                    if ($examScheduleUpdate['session'] == 1) {
                        $new_session = 'Morning';
                    } else {
                        if ($examScheduleUpdate['session'] == 2) {
                            $new_session = 'Afternoon';
                        } else {
                            $new_session = 'Evening';
                        }
                    }

                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'Exam session for the course <u>' . $examScheduleDetail['PublishedCourse']['Course']['course_title'] . ' (' . $examScheduleDetail['PublishedCourse']['Course']['course_code'] . ')</u> of <u>' . $examScheduleDetail['PublishedCourse']['Section']['name'] . '</u> section is changed to <u>' . $new_session . '</u>'
                        ),
                        'default',
                        array('class' => 'success-message success-box')
                    );
                }
                $viewExamSchedule = true;
                unset($this->request->data['ExamSchedule']['session_' . $i]);
            } else {
                unset($this->request->data['ExamSchedule']['session_' . $i]);
            }
        }
        //When add invigilator is clicked
        for ($i = 1; $i <= $this->request->data['ExamSchedule']['exam_schedule_count']; $i++) {
            if (isset($this->request->data['addInvigilator_' . $i])) {
                if (strcasecmp($this->request->data['ExamSchedule']['department_id_' . $i], 'External') != 0) {
                    $invigilator_count = $this->ExamSchedule->Invigilator->find(
                        'count',
                        array(
                            'conditions' =>
                                array(
                                    'Invigilator.exam_schedule_id' => $this->request->data['ExamSchedule']['exam_schedule_id_' . $i],
                                    'Invigilator.staff_id' => $this->request->data['ExamSchedule']['invigilator_id_' . $i]
                                )
                        )
                    );
                } else {
                    $invigilator_count = $this->ExamSchedule->Invigilator->find(
                        'count',
                        array(
                            'conditions' =>
                                array(
                                    'Invigilator.exam_schedule_id' => $this->request->data['ExamSchedule']['exam_schedule_id_' . $i],
                                    'Invigilator.staff_for_exam_id' => $this->request->data['ExamSchedule']['invigilator_id_' . $i]
                                )
                        )
                    );
                }
                if ($invigilator_count > 0) {
                    $this->Session->setFlash(
                        '<span></span>' . __('The selected invigilator is already assigned as an invigilator.'),
                        'default',
                        array('class' => 'error-message error-box')
                    );
                } else {
                    $invigilator['exam_schedule_id'] = $this->request->data['ExamSchedule']['exam_schedule_id_' . $i];
                    if (strcasecmp($this->request->data['ExamSchedule']['department_id_' . $i], 'External') == 0) {
                        $invigilator['staff_for_exam_id'] = $this->request->data['ExamSchedule']['invigilator_id_' . $i];
                    } else {
                        $invigilator['staff_id'] = $this->request->data['ExamSchedule']['invigilator_id_' . $i];
                    }
                    if ($this->ExamSchedule->Invigilator->save($invigilator)) {
                        $examScheduleDetail = $this->ExamSchedule->find(
                            'first',
                            array(
                                'conditions' =>
                                    array(
                                        'ExamSchedule.id' => $invigilator['exam_schedule_id']
                                    ),
                                'contain' =>
                                    array(
                                        'ClassRoom' =>
                                            array(
                                                'ClassRoomBlock'
                                            ),
                                        'PublishedCourse' =>
                                            array(
                                                'Course',
                                                'Section'
                                            )
                                    )
                            )
                        );

                        $this->Session->setFlash(
                            '<span></span>' . __(
                                'Additional invigilator is added for the course <u>' . $examScheduleDetail['PublishedCourse']['Course']['course_title'] . ' (' . $examScheduleDetail['PublishedCourse']['Course']['course_code'] . ')</u> of <u>' . $examScheduleDetail['PublishedCourse']['Section']['name'] . '</u> section'
                            ),
                            'default',
                            array('class' => 'success-message success-box')
                        );
                    }
                }
                $viewExamSchedule = true;
                unset($this->request->data['ExamSchedule']['invigilator_id_' . $i]);
                unset($this->request->data['ExamSchedule']['department_id_' . $i]);
            } else {
                unset($this->request->data['ExamSchedule']['invigilator_id_' . $i]);
                unset($this->request->data['ExamSchedule']['department_id_' . $i]);
            }
        }

        //When published course cancel button is clicked
        for ($i = 1; $i <= $this->request->data['ExamSchedule']['exam_schedule_count']; $i++) {
            if (isset($this->request->data['cancelExamSchedule_' . $i])) {
                $examScheduleDetail = $this->ExamSchedule->find(
                    'first',
                    array(
                        'conditions' =>
                            array(
                                'ExamSchedule.id' => $this->request->data['ExamSchedule']['exam_schedule_id_' . $i]
                            ),
                        'contain' =>
                            array(
                                'ClassRoom' =>
                                    array(
                                        'ClassRoomBlock'
                                    ),
                                'PublishedCourse' =>
                                    array(
                                        'Course',
                                        'Section'
                                    )
                            )
                    )
                );

                if (empty($examScheduleDetail)) {
                    $this->Session->setFlash(
                        '<span></span>' . __('The selected exam schedule is already canceled.'),
                        'default',
                        array('class' => 'error-message error-box')
                    );
                } else {
                    if ($this->ExamSchedule->delete($this->request->data['ExamSchedule']['exam_schedule_id_' . $i])) {
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                'Exam schedule for the course <u>' . $examScheduleDetail['PublishedCourse']['Course']['course_title'] . ' (' . $examScheduleDetail['PublishedCourse']['Course']['course_code'] . ')</u> of <u>' . $examScheduleDetail['PublishedCourse']['Section']['name'] . '</u> section is successfully canceled'
                            ),
                            'default',
                            array('class' => 'success-message success-box')
                        );
                    } else {
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                'Exam schedule cancellation for the course <u>' . $examScheduleDetail['PublishedCourse']['Course']['course_title'] . ' (' . $examScheduleDetail['PublishedCourse']['Course']['course_code'] . ')</u> of <u>' . $examScheduleDetail['PublishedCourse']['Section']['name'] . '</u> section is failed. Please try again.'
                            ),
                            'default',
                            array('class' => 'error-message error-box')
                        );
                    }
                }
                $viewExamSchedule = true;
            } else {
                //unset can be done here
            }
        }

        if ($viewExamSchedule || isset($this->request->data['viewExamSchedule'])) {
            $examSchedules = array();
            if (empty($this->request->data['ExamSchedule']['program_type_id']) || empty($this->request->data['ExamSchedule']['department_id']) || empty($this->request->data['ExamSchedule']['year_level'])) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'You are required to select at least one program type, department and year level.'
                    ),
                    'default',
                    array('class' => 'error-message error-box')
                );
            } else {
                $program_type_ids = array();
                $department_ids = array();
                $year_level_ids = array();
                if (is_array($this->request->data['ExamSchedule']['program_type_id'])) {
                    $program_type_ids = $this->request->data['ExamSchedule']['program_type_id'];
                } else {
                    $program_type_ids[] = $this->request->data['ExamSchedule']['program_type_id'];
                }

                if (is_array($this->request->data['ExamSchedule']['department_id'])) {
                    $department_ids = $this->request->data['ExamSchedule']['department_id'];
                } else {
                    $department_ids[] = $this->request->data['ExamSchedule']['department_id'];
                }

                if (is_array($this->request->data['ExamSchedule']['year_level'])) {
                    $year_levels = $this->request->data['ExamSchedule']['year_level'];
                } else {
                    $year_levels[] = $this->request->data['ExamSchedule']['year_level'];
                }
                //Organize by department and year level
                if ($this->request->data['ExamSchedule']['organize_by_department'] == 1 && $this->request->data['ExamSchedule']['organize_by_year_level'] == 1) {
                    foreach ($department_ids as $department_id) {
                        $tmp_dpt[0] = $department_id;
                        foreach ($year_levels as $year_level) {
                            $tmp_yl[0] = $year_level;
                            $examSchedulesTmp = $this->ExamSchedule->getExamSchedule(
                                $this->college_id,
                                $this->request->data['ExamSchedule']['acadamic_year'],
                                $this->request->data['ExamSchedule']['semester'],
                                $this->request->data['ExamSchedule']['program_id'],
                                $program_type_ids,
                                $tmp_dpt,
                                $tmp_yl,
                                $this->request->data['ExamSchedule']['organize_by_department'],
                                $this->request->data['ExamSchedule']['organize_by_year_level']
                            );
                            if (!empty($examSchedulesTmp)) {
                                $department = ClassRegistry::init('Department')->find(
                                    'first',
                                    array(
                                        'conditions' =>
                                            array(
                                                'Department.id' => $department_id
                                            ),
                                        'recursive' => -1
                                    )
                                );
                                $examSchedules[$department['Department']['name']][$year_level] = $examSchedulesTmp;
                            }
                        }
                    }
                } //Organize by department
                else {
                    if ($this->request->data['ExamSchedule']['organize_by_department'] == 1) {
                        foreach ($department_ids as $department_id) {
                            $tmp_dpt[0] = $department_id;
                            $examSchedulesTmp = $this->ExamSchedule->getExamSchedule(
                                $this->college_id,
                                $this->request->data['ExamSchedule']['acadamic_year'],
                                $this->request->data['ExamSchedule']['semester'],
                                $this->request->data['ExamSchedule']['program_id'],
                                $program_type_ids,
                                $tmp_dpt,
                                $year_levels,
                                $this->request->data['ExamSchedule']['organize_by_department'],
                                $this->request->data['ExamSchedule']['organize_by_year_level']
                            );
                            if (!empty($examSchedulesTmp)) {
                                $department = ClassRegistry::init('Department')->find(
                                    'first',
                                    array(
                                        'conditions' =>
                                            array(
                                                'Department.id' => $department_id
                                            ),
                                        'recursive' => -1
                                    )
                                );
                                $examSchedules[$department['Department']['name']] = $examSchedulesTmp;
                            }
                        }
                    } //Organize by year level
                    else {
                        if ($this->request->data['ExamSchedule']['organize_by_year_level'] == 1) {
                            foreach ($year_levels as $year_level) {
                                $tmp_yl[0] = $year_level;
                                $examSchedulesTmp = $this->ExamSchedule->getExamSchedule(
                                    $this->college_id,
                                    $this->request->data['ExamSchedule']['acadamic_year'],
                                    $this->request->data['ExamSchedule']['semester'],
                                    $this->request->data['ExamSchedule']['program_id'],
                                    $program_type_ids,
                                    $department_ids,
                                    $tmp_yl,
                                    $this->request->data['ExamSchedule']['organize_by_department'],
                                    $this->request->data['ExamSchedule']['organize_by_year_level']
                                );
                                if (!empty($examSchedulesTmp)) {
                                    $examSchedules[$year_level] = $examSchedulesTmp;
                                }
                            }
                        } //No organization
                        else {
                            $examSchedules = $this->ExamSchedule->getExamSchedule(
                                $this->college_id,
                                $this->request->data['ExamSchedule']['acadamic_year'],
                                $this->request->data['ExamSchedule']['semester'],
                                $this->request->data['ExamSchedule']['program_id'],
                                $program_type_ids,
                                $department_ids,
                                $year_levels,
                                $this->request->data['ExamSchedule']['organize_by_department'],
                                $this->request->data['ExamSchedule']['organize_by_year_level']
                            );
                        }
                    }
                }
                //debug($examSchedules);
                $academic_year = $this->request->data['ExamSchedule']['acadamic_year'];
                $semester = $this->request->data['ExamSchedule']['semester'];
                $program_name = ($this->request->data['ExamSchedule']['program_id'] == 1 ? 'Undergraduate' : 'Postgraguate');
                $program_types_name = array();
                foreach ($program_type_ids as $k => $v) {
                    if ($v == 1) {
                        $program_types_name[] = 'Regular';
                    }
                    if ($v == 2) {
                        $program_types_name[] = 'Summer';
                    }
                    if ($v == 3) {
                        $program_types_name[] = 'Extension';
                    }
                    if ($v == 4) {
                        $program_types_name[] = 'Advanced Standing';
                    }
                    if ($v == 5) {
                        $program_types_name[] = 'In-Service';
                    }
                    if ($v == 6) {
                        $program_types_name[] = 'Distance and Continuing Education';
                    }
                }
                $department_names = array();
                $departments = ClassRegistry::init('Department')->find(
                    'all',
                    array(
                        'conditions' =>
                            array(
                                'Department.id' => $department_ids
                            ),
                        'recursive' => -1
                    )
                );
                foreach ($departments as $k => $v) {
                    $department_names[] = $v['Department']['name'];
                }
                if (empty($examSchedules)) {
                    $this->Session->setFlash(
                        '<span></span>' . __('There is no exam schedule based on the selected criteria.'),
                        'default',
                        array('class' => 'info-message info-box')
                    );
                }
                $class_room_blocks = $this->ExamSchedule->ClassRoom->ClassRoomBlock->find(
                    'list',
                    array(
                        'conditions' =>
                            array(
                                'ClassRoomBlock.id IN (SELECT class_room_block_id FROM class_rooms WHERE available_for_exam = 1 AND exam_capacity > 0)',
                                'ClassRoomBlock.college_id' => $this->college_id
                            ),
                        'fields' =>
                            array(
                                'ClassRoomBlock.block_code'
                            )
                    )
                );
                $departments_for_change = $this->ExamSchedule->ClassRoom->ClassRoomBlock->College->Department->find(
                    'list',
                    array(
                        'conditions' =>
                            array(
                                'Department.college_id' => $this->college_id
                            ),
                        'fields' =>
                            array(
                                'Department.id',
                                'Department.name'
                            )
                    )
                );
                $class_room_blocks = array(0 => '--- Select Class Room Block ---') + $class_room_blocks;
                $departments_for_change = array('External' => 'External Invigilators') + $departments_for_change;
                $departments_for_change = array(0 => '--- Select Department ---') + $departments_for_change;
                $sessions = array(
                    1 => 'Morning',
                    2 => 'Afternoon',
                    3 => 'Evening'
                );
                $class_rooms = array(0 => '--- Select Class Room ---');
                $invigilators = array(0 => '--- Select Invigilator ---');
                $this->set(
                    compact(
                        'examSchedules',
                        'academic_year',
                        'semester',
                        'program_name',
                        'program_types_name',
                        'department_names',
                        'year_levels',
                        'class_room_blocks',
                        'sessions',
                        'class_rooms',
                        'departments_for_change',
                        'invigilators'
                    )
                );
            }
        }
        $departments = ClassRegistry::init('Department')->find(
            'list',
            array(
                'conditions' =>
                    array(
                        'Department.college_id' => $this->college_id
                    )
            )
        );
        $programs = ClassRegistry::init('Program')->find('list');
        $programTypes = ClassRegistry::init('ProgramType')->find('list');
        $semesters = array('I' => 'I', 'II' => 'II', 'III' => 'III');
        $max_year_level = ClassRegistry::init('YearLevel')->get_department_max_year_level($departments);
        $departments['10000'] = 'Pre/(Unassign Freshman)';
        for ($i = 1; $i <= $max_year_level; $i++) {
            $yearLevels[$i] = $i . ($i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')));
        }
        $this->set(compact('departments', 'programs', 'programTypes', 'semesters', 'yearLevels'));
    }

    public function department_exam_schedule_view()
    {
    }

    public function student_exam_schedule_view()
    {
    }

    public function instructor_exam_schedule_view()
    {
    }

    private function _exam_schedule_view()
    {
    }

    public function index()
    {

        $this->ExamSchedule->recursive = 0;
        $this->set('examSchedules', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid exam schedule'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('examSchedule', $this->ExamSchedule->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->ExamSchedule->create();
            if ($this->ExamSchedule->save($this->request->data)) {
                $this->Session->setFlash(__('The exam schedule has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The exam schedule could not be saved. Please, try again.'));
            }
        }
        $classRooms = $this->ExamSchedule->ClassRoom->find('list');
        $publishedCourses = $this->ExamSchedule->PublishedCourse->find('list');
        $this->set(compact('classRooms', 'publishedCourses'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid exam schedule'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->ExamSchedule->save($this->request->data)) {
                $this->Session->setFlash(__('The exam schedule has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The exam schedule could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->ExamSchedule->read(null, $id);
        }
        $classRooms = $this->ExamSchedule->ClassRoom->find('list');
        $publishedCourses = $this->ExamSchedule->PublishedCourse->find('list');
        $this->set(compact('classRooms', 'publishedCourses'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for exam schedule'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->ExamSchedule->delete($id)) {
            $this->Session->setFlash(__('Exam schedule deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Exam schedule was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}

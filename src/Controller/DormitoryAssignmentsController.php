<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class DormitoryAssignmentsController extends AppController
{

    public $name = 'DormitoryAssignments';
    public $menuOptions = array(
        'parent' => 'dormitory',
        'exclude' => array(
            'get_departments',
            'get_year_levels',
            'get_dormitories',
            'add_student_dormitory',
            'add_student_dormitory_update',
            'get_dormitory_blocks',
            'get_section'
        ),
        'alias' => array(
            'index' => 'List of Assigned Students',
            'add' => 'Auto Students Assignment',
            'add_delete' => 'Manual Student Assignment',
            'entry_leave' => 'Students Entry/Leave Management'
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
        $this->Auth->allow(
            'get_departments',
            'get_year_levels',
            'getAssignedStudent',
            'get_dormitories',
            'add_student_dormitory',
            'add_student_dormitory_update',
            'get_dormitory_blocks',
            'get_section'
        );
    }

    public function index()
    {

        //$this->DormitoryAssignment->recursive = 0;
//Check the user whether assigned dormitory block or not
        $user_id = $this->Auth->user('id');
        $assigned_dormitory_block_ids = $this->DormitoryAssignment->get_assigned_dormitory_blocks($user_id);
        $assigned_dormitory_block_count = count($assigned_dormitory_block_ids);
        if (!empty($assigned_dormitory_block_count)) {
            debug($this->request->data);
            $colleges = $this->DormitoryAssignment->Student->College->find('list');
            $programs = $this->DormitoryAssignment->Student->Program->find('list');
            $programTypes = $this->DormitoryAssignment->Student->ProgramType->find('list');
            //$fine_formatted_dormitories = $this->_get_formated_dormitory_block("%",$assigned_dormitory_block_ids);

            $is_atleat_one_parameter_isset = false;
            if (!empty($this->request->data['DormitoryAssignment']['program_id'])) {
                $program_id = $this->request->data['DormitoryAssignment']['program_id'];
                $is_atleat_one_parameter_isset = true;
            } else {
                $program_id = null;
            }
            if (!empty($this->request->data['DormitoryAssignment']['program_type_id'])) {
                $program_type_id = $this->request->data['DormitoryAssignment']['program_type_id'];
                $is_atleat_one_parameter_isset = true;
            } else {
                $program_type_id = null;
            }
            if (!empty($this->request->data['DormitoryAssignment']['gender'])) {
                $gender = $this->request->data['DormitoryAssignment']['gender'];
                $is_atleat_one_parameter_isset = true;
            } else {
                $gender = null;
            }
            if (!empty($this->request->data['DormitoryAssignment']['college_id'])) {
                $college_id = $this->request->data['DormitoryAssignment']['college_id'];
                $is_atleat_one_parameter_isset = true;
            } else {
                $college_id = null;
            }
            if (!empty($this->request->data['DormitoryAssignment']['department_id'])) {
                $department_id = $this->request->data['DormitoryAssignment']['department_id'];
                $is_atleat_one_parameter_isset = true;
            } else {
                $department_id = null;
            }
            if (!empty($this->request->data['DormitoryAssignment']['year_level_id'])) {
                $year_level_id = $this->request->data['DormitoryAssignment']['year_level_id'];
                $is_atleat_one_parameter_isset = true;
            } else {
                $year_level_id = null;
            }
            if (!empty($this->request->data['DormitoryAssignment']['dormitory_block'])) {
                $dormitory_block_id = $this->request->data['DormitoryAssignment']['dormitory_block'];
            } else {
                $dormitory_block_id = null;
            }
            if (!empty($this->request->data['DormitoryAssignment']['dormitory'])) {
                $dormitory_id = $this->request->data['DormitoryAssignment']['dormitory'];
            } else {
                $dormitory_id = null;
            }

            if (!empty($college_id)) {
                $departments = $this->_get_departments_list($college_id);
            } else {
                $departments = null;
            }
            if (!empty($department_id)) {
                $yearLevels = $this->_get_year_levels_list($department_id);
            } else {
                $yearLevels = null;
            }
            if (!empty($dormitory_block_id)) {
                $dormitories = $this->_get_dormitories_list($dormitory_block_id);
            } else {
                $dormitories = null;
            }
            if (!empty($gender)) {
                $fine_formatted_dormitories = $this->_get_formated_dormitory_block(
                    $gender,
                    $assigned_dormitory_block_ids
                );
            } else {
                $fine_formatted_dormitories = $this->_get_formated_dormitory_block("%", $assigned_dormitory_block_ids);
            }
            $dormitoryAssignments = $this->_get_already_assigned_students(
                $program_id,
                $program_type_id,
                $gender,
                $college_id,
                $department_id,
                $year_level_id,
                $dormitory_block_id,
                $dormitory_id,
                $is_atleat_one_parameter_isset,
                $this->request->data['DormitoryAssignment']['limit']
            );

            $this->set(
                compact(
                    'programs',
                    'programTypes',
                    'colleges',
                    'fine_formatted_dormitories',
                    'departments',
                    'yearLevels',
                    'dormitories',
                    'dormitoryAssignments'
                )
            );

            if (isset($this->request->data['exportToExcel'])
                && !empty($this->request->data['exportToExcel'])) {
                $this->autoLayout = false;
                $filename = 'Dormitory Assignment List-' . date(
                        'Ymd H:i:s'
                    ) . '' . $this->request->data['DormitoryAssignment']['dormitory'];
                $this->set(
                    compact(
                        'programs',
                        'programTypes',
                        'colleges',
                        'fine_formatted_dormitories',
                        'departments',
                        'yearLevels',
                        'dormitories',
                        'dormitoryAssignments',
                        'filename'
                    )
                );
                $this->render('/Elements/dormitory_assignment_list_xls');
                return;
            }
        } else {
            $this->Session->setFlash(
                '<span></span>' . __(
                    'Currently you do not have dormitory block to manage. So Please contact dormitory block administrator.'
                ),
                'default',
                array('class' => 'error-box er-message')
            );
        }
    }

    /*function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid dormitory assignment'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('dormitoryAssignment', $this->DormitoryAssignment->read(null, $id));
    }*/

    public function add()
    {

        $beforesearch = 1;
        $this->set(compact('beforesearch'));
        //Check the user whether assigned dormitory block or not
        $user_id = $this->Auth->user('id');
        $assigned_dormitory_block_ids = $this->DormitoryAssignment->get_assigned_dormitory_blocks($user_id);
        $assigned_dormitory_block_count = count($assigned_dormitory_block_ids);
        if (!empty($assigned_dormitory_block_count)) {
            if ($this->Session->read('selected_program')) {
                $this->Session->delete('selected_program');
            }
            if ($this->Session->read('selected_program_type')) {
                $this->Session->delete('selected_program_type');
            }
            if ($this->Session->read('selected_gender')) {
                $this->Session->delete('selected_gender');
            }
            if ($this->Session->read('selected_college')) {
                $this->Session->delete('selected_college');
            }
            if ($this->Session->read('selected_department')) {
                $this->Session->delete('selected_department');
            }
            if ($this->Session->read('selected_year_level')) {
                $this->Session->delete('selected_year_level');
            }

            if (!empty($this->request->data) && isset($this->request->data['search'])) {
                $beforesearch = 0;
                $everythingfine = false;
                switch ($this->request->data) {
                    case empty($this->request->data['DormitoryAssignment']['program_id']) :
                        $this->Session->setFlash(
                            '<span></span> ' . __('Please select the program that you want to assign dormitories. '),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                        break;
                    case empty($this->request->data['DormitoryAssignment']['program_type_id']) :
                        $this->Session->setFlash(
                            '<span></span> ' . __(
                                'Please select the program type that you want to assign dormitories. '
                            ),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                        break;
                    case empty($this->request->data['DormitoryAssignment']['gender']) :
                        $this->Session->setFlash(
                            '<span></span> ' . __('Please select the gender that you want to assign dormitories. '),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                        break;
                    case empty($this->request->data['DormitoryAssignment']['college_id']) :
                        $this->Session->setFlash(
                            '<span></span> ' . __('Please select the department that you want to assign dormitories. '),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                        break;
                    case empty($this->request->data['DormitoryAssignment']['department_id']) :
                        $this->Session->setFlash(
                            '<span></span> ' . __('Please select the department that you want to assign dormitories. '),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                        break;
                    case empty($this->request->data['DormitoryAssignment']['year_level_id']) :
                        $this->Session->setFlash(
                            '<span></span> ' . __('Please select the Year Level that you want to assign dormitories. '),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                        break;
                    default:
                        $everythingfine = true;
                }
                if ($everythingfine) {
                    $selected_program = $this->request->data['DormitoryAssignment']['program_id'];
                    $this->Session->write('selected_program', $selected_program);
                    $selected_program_type = $this->request->data['DormitoryAssignment']['program_type_id'];
                    $this->Session->write('selected_program_type', $selected_program_type);
                    $selected_gender = $this->request->data['DormitoryAssignment']['gender'];
                    $this->Session->write('selected_gender', $selected_gender);
                    $selected_college = $this->request->data['DormitoryAssignment']['college_id'];
                    $this->Session->write('selected_college', $selected_college);
                    $selected_department = $this->request->data['DormitoryAssignment']['department_id'];
                    $this->Session->write('selected_department', $selected_department);
                    $selected_year_level = $this->request->data['DormitoryAssignment']['year_level_id'];
                    $this->Session->write('selected_year_level', $selected_year_level);
                    $selected_section_id = $this->request->data['DormitoryAssignment']['section_id'];
                    $this->Session->write('selected_section_id', $selected_section_id);
                    $departments = $this->_get_departments_list($selected_college);
                    $yearLevels = $this->_get_year_levels_list($selected_department);
                    $sections = $this->__getSection($this->request->data);

                    //Get student_id that have dormitory in there name
                    $student_ids = $this->DormitoryAssignment->get_student_have_dormitory();
                    //Get accepted_student_id that have dormitory in there name
                    $accepted_student_ids = $this->DormitoryAssignment->get_accepted_student_have_dormitory();

                    //Get dormitories unassigned students
                    $unassigned_students = null;
                    if ($selected_department == 10000) {
                        $unassigned_students = $this->DormitoryAssignment->Student->getListOfDepartmentNonAssignedStudents(
                            $selected_college,
                            $selected_program,
                            $selected_program_type,
                            $selected_gender,
                            $student_ids,
                            $accepted_student_ids
                        );
                    } else {
                        $unassigned_students = $this->DormitoryAssignment->Student->getListOfDepartmentStudentsByYearLevelAndSection(
                            $selected_college,
                            $selected_department,
                            $selected_program,
                            $selected_program_type,
                            $selected_year_level,
                            1,
                            $selected_gender,
                            $student_ids,
                            $accepted_student_ids,
                            $this->request->data['DormitoryAssignment']['section_id']
                        );
                    }
                    $admitted_unassigned_students = $unassigned_students['student'];
                    $non_admitted_unassigned_students = $unassigned_students['accepted_student'];


                    $fine_formatted_dormitories = $this->_get_formated_dormitory_block(
                        $selected_gender,
                        $assigned_dormitory_block_ids
                    );

                    $this->set(
                        compact(
                            'departments',
                            'yearLevels',
                            'selected_program',
                            'selected_program_type',
                            'selected_gender',
                            'selected_college',
                            'selected_department',
                            'selected_year_level',
                            'fine_formatted_dormitories',
                            'admitted_unassigned_students',
                            'sections',
                            'non_admitted_unassigned_students',
                            'beforesearch',
                            'selected_section_id'
                        )
                    );
                }
            }
            if (!empty($this->request->data) && isset($this->request->data['assign'])) {
                $selected_students = array();
                $selected_accepted_students = array();
                if (isset($this->request->data['Student'])) {
                    foreach ($this->request->data['Student']['Selected'] as $sk => $sv) {
                        if ($sv != 0) {
                            $selected_students[] = $sk;
                        }
                    }
                }
                if (isset($this->request->data['AcceptedStudent'])) {
                    foreach ($this->request->data['AcceptedStudent']['Selected'] as $ask => $asv) {
                        if ($asv != 0) {
                            $selected_accepted_students[] = $ask;
                        }
                    }
                }

                $count_selected_students = count($selected_students);
                $count_selected_accepted_students = count($selected_accepted_students);

                if (!empty($count_selected_students) || !empty($count_selected_accepted_students)) {
                    $selected_dormitory_block_id = $this->request->data['DormitoryAssignment']['dormitory_block'];
                    if (!empty($selected_dormitory_block_id)) {
                        //Find block dormitories
                        $selected_block_dormitories = $this->DormitoryAssignment->Dormitory->find(
                            'all',
                            array(
                                'conditions' => array(
                                    'Dormitory.available' => 1,
                                    'Dormitory.dormitory_block_id' => $selected_dormitory_block_id
                                ),
                                'fields' => array('Dormitory.id', 'Dormitory.capacity'),
                                'order' => array('Dormitory.floor', 'Dormitory.dorm_number'),
                                'recursive' => -1
                            )
                        );

                        $capacities = $this->DormitoryAssignment->get_block_capacity($selected_block_dormitories);
                        if ($capacities['free_capacity'] != 0) {
                            $totoal_selected_student_count = $count_selected_students + $count_selected_accepted_students;
                            $student_count = 0;
                            $accepted_student_count = 0;
                            $assign_student_count = 0;
                            $isassign = false;
                            //Assign studet to each dormitories starting from ground dorm
                            foreach ($selected_block_dormitories as $selected_dormitory) {
                                $this->request->data['DormitoryAssignments']['dormitory_id'] = $selected_dormitory['Dormitory']['id'];
                                $selected_dormitory_free_space = $this->DormitoryAssignment->get_free_dormitory_space(
                                    $selected_dormitory
                                );
                                for ($i = 1; $i <= $selected_dormitory_free_space; $i++) {
                                    if (!empty($selected_students[$student_count])) {
                                        if (isset($this->request->data['DormitoryAssignments']['accepted_student_id'])) {
                                            unset($this->request->data['DormitoryAssignments']['accepted_student_id']);
                                        }

                                        $this->request->data['DormitoryAssignments']['student_id'] = $selected_students[$student_count];
                                        $this->DormitoryAssignment->create();
                                        $this->DormitoryAssignment->save($this->request->data['DormitoryAssignments']);

                                        $student_count++;
                                        $assign_student_count++;
                                        $isassign = true;
                                    } else {
                                        if (!empty($selected_accepted_students[$accepted_student_count])) {
                                            if (isset($this->request->data['DormitoryAssignments']['student_id'])) {
                                                unset($this->request->data['DormitoryAssignments']['student_id']);
                                            }
                                            $this->request->data['DormitoryAssignments']['accepted_student_id'] = $selected_accepted_students[$accepted_student_count];


                                            $this->DormitoryAssignment->create();
                                            $this->DormitoryAssignment->save(
                                                $this->request->data['DormitoryAssignments']
                                            );
                                            $accepted_student_count++;
                                            $assign_student_count++;
                                            $isassign = true;
                                        } else {
                                            break 2;
                                        }
                                    }
                                }
                            }

                            if ($isassign == true) {
                                if ($totoal_selected_student_count == $assign_student_count) {
                                    $this->Session->setFlash(
                                        '<span></span>' . __('All selected students have been assigned successfully.'),
                                        'default',
                                        array('class' => 'success-box success-message')
                                    );
                                } else {
                                    $this->Session->setFlash(
                                        '<span></span>' . __(
                                            'Out of ' . $totoal_selected_student_count . ' selected students ' . $assign_student_count . ' of them have been assigned successfully. You may assign the remaining students with other dormitory block.'
                                        ),
                                        'default',
                                        array('class' => 'success-box success-message')
                                    );
                                }
                            } else {
                                $this->Session->setFlash(
                                    '<span></span>' . __('The dormitories could not be assigned. Please, try again.'),
                                    'default',
                                    array('class' => 'error-box error-message')
                                );
                            }
                        } else {
                            $this->Session->setFlash(
                                '<span></span>' . __(
                                    'You selected full dormitory block. Please select dormitory block that have free space dormitories to assign students.'
                                ),
                                'default',
                                array('class' => 'error-box error-message')
                            );
                        }
                    } else {
                        $this->Session->setFlash(
                            '<span></span>' . __('Please select dormitory block.'),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __('Please select at least one student.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
                $this->request->data['search'] = true;
                $beforesearch = 0;

                //Get student_id that have dormitory in there name
                $student_ids = $this->DormitoryAssignment->get_student_have_dormitory();
                //Get accepted_student_id that have dormitory in there name
                $accepted_student_ids = $this->DormitoryAssignment->get_accepted_student_have_dormitory();

                //Get dormitories unassigned students
                $unassigned_students = null;
                if ($this->request->data['DormitoryAssignment']['department_id'] == 10000) {
                    $unassigned_students = $this->DormitoryAssignment->Student->getListOfDepartmentNonAssignedStudents(
                        $this->request->data['DormitoryAssignment']['college_id'],
                        $this->request->data['DormitoryAssignment']['program_id'],
                        $this->request->data['DormitoryAssignment']['program_type_id'],
                        $this->request->data['DormitoryAssignment']['gender'],
                        $student_ids,
                        $accepted_student_ids
                    );
                } else {
                    $unassigned_students = $this->DormitoryAssignment->Student->getListOfDepartmentStudentsByYearLevel(
                        $this->request->data['DormitoryAssignment']['college_id'],
                        $this->request->data['DormitoryAssignment']['department_id'],
                        $this->request->data['DormitoryAssignment']['program_id'],
                        $this->request->data['DormitoryAssignment']['program_type_id'],
                        $this->request->data['DormitoryAssignment']['year_level_id'],
                        1,
                        $this->request->data['DormitoryAssignment']['gender'],
                        $student_ids,
                        $accepted_student_ids
                    );
                }

                $admitted_unassigned_students = $unassigned_students['student'];
                $non_admitted_unassigned_students = $unassigned_students['accepted_student'];

                $fine_formatted_dormitories = $this->_get_formated_dormitory_block(
                    $this->request->data['DormitoryAssignment']['gender'],
                    $assigned_dormitory_block_ids
                );

                $this->set(
                    compact(
                        'fine_formatted_dormitories',
                        'admitted_unassigned_students',
                        'non_admitted_unassigned_students',
                        'beforesearch'
                    )
                );
            }

            if ($this->Session->read('selected_program')) {
                $selected_program = $this->Session->read('selected_program');
            } else {
                if (!empty($this->request->data['DormitoryAssignment']['program_id'])) {
                    $selected_program = $this->request->data['DormitoryAssignment']['program_id'];
                }
            }
            if ($this->Session->read('selected_program_type')) {
                $selected_program_type = $this->Session->read('selected_program_type');
            } else {
                if (!empty($this->request->data['DormitoryAssignment']['program_type_id'])) {
                    $selected_program_type = $this->request->data['DormitoryAssignment']['program_type_id'];
                }
            }
            if ($this->Session->read('selected_gender')) {
                $selected_gender = $this->Session->read('selected_gender');
            } else {
                if (!empty($this->request->data['DormitoryAssignment']['gender'])) {
                    $selected_gender = $this->request->data['DormitoryAssignment']['gender'];
                }
            }
            if ($this->Session->read('selected_college')) {
                $selected_college = $this->Session->read('selected_college');
            } else {
                if (!empty($this->request->data['DormitoryAssignment']['college_id'])) {
                    $selected_college = $this->request->data['DormitoryAssignment']['college_id'];
                }
            }
            if ($this->Session->read('selected_department')) {
                $selected_department = $this->Session->read('selected_department');
            } else {
                if (!empty($this->request->data['DormitoryAssignment']['department_id'])) {
                    $selected_department = $this->request->data['DormitoryAssignment']['department_id'];
                }
            }
            if ($this->Session->read('selected_year_level')) {
                $selected_year_level = $this->Session->read('selected_year_level');
            } else {
                if (!empty($this->request->data['DormitoryAssignment']['year_level_id'])) {
                    $selected_year_level = $this->request->data['DormitoryAssignment']['year_level_id'];
                }
            }

            if (!empty($selected_college)) {
                $departments = $this->_get_departments_list($selected_college);
            } else {
                $departments = null;
            }
            if (!empty($selected_department)) {
                $yearLevels = $this->_get_year_levels_list($selected_department);
            } else {
                $yearLevels = null;
            }
            $colleges = $this->DormitoryAssignment->AcceptedStudent->College->find('list');
            $programs = $this->DormitoryAssignment->AcceptedStudent->Program->find('list');
            $programTypes = $this->DormitoryAssignment->AcceptedStudent->ProgramType->find('list');
            $this->set(
                compact(
                    'colleges',
                    'programs',
                    'programTypes',
                    'departments',
                    'yearLevels',
                    'selected_program',
                    'selected_program_type',
                    'selected_gender',
                    'selected_college',
                    'selected_department',
                    'selected_year_level'
                )
            );
        } else {
            $this->Session->setFlash(
                '<span></span>' . __(
                    'Currently you do not have dormitory block to manage. So Please contact dormitory block administrator.'
                ),
                'default',
                array('class' => 'error-box er-message')
            );
        }
    }

    public function add_delete()
    {

        $this->paginate = array('order' => 'DormitoryAssignment.created DESC', 'limit' => 200);
        //Check the user whether assigned dormitory block or not
        $user_id = $this->Auth->user('id');
        $assigned_dormitory_block_ids = $this->DormitoryAssignment->get_assigned_dormitory_blocks($user_id);
        $assigned_dormitory_block_count = count($assigned_dormitory_block_ids);
        if (!empty($assigned_dormitory_block_count)) {
            $from_delete = $this->Session->read('from_delete');
            $from_manually_add = $this->Session->read('from_manually_add');
            if ($from_delete != 1 && $from_manually_add != 1) {
                if ($this->Session->read('selected_program_id')) {
                    $this->Session->delete('selected_program_id');
                }
                if ($this->Session->read('selected_program_type_id')) {
                    $this->Session->delete('selected_program_type_id');
                }
                if ($this->Session->read('selected_gender')) {
                    $this->Session->delete('selected_gender');
                }
                if ($this->Session->read('selected_college_id')) {
                    $this->Session->delete('selected_college_id');
                }
                if ($this->Session->read('selected_department_id')) {
                    $this->Session->delete('selected_department_id');
                }
                if ($this->Session->read('selected_year_level_id')) {
                    $this->Session->delete('selected_year_level_id');
                }
                if ($this->Session->read('dormitory_block_id')) {
                    $this->Session->delete('dormitory_block_id');
                }
                if ($this->Session->read('dormitory_id')) {
                    $this->Session->delete('dormitory_id');
                }
            }
            $colleges = $this->DormitoryAssignment->Student->College->find('list');
            $programs = $this->DormitoryAssignment->Student->Program->find('list');
            $programTypes = $this->DormitoryAssignment->Student->ProgramType->find('list');

            $is_atleat_one_parameter_isset = false;
            if (!empty($this->request->data['DormitoryAssignment']['program_id'])) {
                $selected_program_id = $this->request->data['DormitoryAssignment']['program_id'];
                $is_atleat_one_parameter_isset = true;
                $this->Session->write('selected_program_id', $selected_program_id);
            } else {
                if ($this->Session->read('selected_program_id')) {
                    $selected_program_id = $this->Session->read('selected_program_id');
                    $is_atleat_one_parameter_isset = true;
                } else {
                    $selected_program_id = null;
                }
            }
            if (!empty($this->request->data['DormitoryAssignment']['program_type_id'])) {
                $selected_program_type_id = $this->request->data['DormitoryAssignment']['program_type_id'];
                $is_atleat_one_parameter_isset = true;
                $this->Session->write('selected_program_type_id', $selected_program_type_id);
            } else {
                if ($this->Session->read('selected_program_type_id')) {
                    $selected_program_type_id = $this->Session->read('selected_program_type_id');
                    $is_atleat_one_parameter_isset = true;
                } else {
                    $selected_program_type_id = null;
                }
            }
            if (!empty($this->request->data['DormitoryAssignment']['gender'])) {
                $selected_gender = $this->request->data['DormitoryAssignment']['gender'];
                $is_atleat_one_parameter_isset = true;
                $this->Session->write('selected_gender', $selected_gender);
            } else {
                if ($this->Session->read('selected_gender')) {
                    $selected_gender = $this->Session->read('selected_gender');
                    $is_atleat_one_parameter_isset = true;
                } else {
                    $selected_gender = null;
                }
            }
            if (!empty($this->request->data['DormitoryAssignment']['college_id'])) {
                $selected_college_id = $this->request->data['DormitoryAssignment']['college_id'];
                $is_atleat_one_parameter_isset = true;
                $this->Session->write('selected_college_id', $selected_college_id);
            } else {
                if ($this->Session->read('selected_college_id')) {
                    $selected_college_id = $this->Session->read('selected_college_id');
                    $is_atleat_one_parameter_isset = true;
                } else {
                    $selected_college_id = null;
                }
            }
            if (!empty($this->request->data['DormitoryAssignment']['department_id'])) {
                $selected_department_id = $this->request->data['DormitoryAssignment']['department_id'];
                $is_atleat_one_parameter_isset = true;
                $this->Session->write('selected_department_id', $selected_department_id);
            } else {
                if ($this->Session->read('selected_department_id')) {
                    $selected_department_id = $this->Session->read('selected_department_id');
                    $is_atleat_one_parameter_isset = true;
                } else {
                    $selected_department_id = null;
                }
            }
            if (!empty($this->request->data['DormitoryAssignment']['year_level_id'])) {
                $selected_year_level_id = $this->request->data['DormitoryAssignment']['year_level_id'];
                $is_atleat_one_parameter_isset = true;
                $this->Session->write('selected_year_level_id', $selected_year_level_id);
            } else {
                if ($this->Session->read('selected_year_level_id')) {
                    $selected_year_level_id = $this->Session->read('selected_year_level_id');
                    $is_atleat_one_parameter_isset = true;
                } else {
                    $selected_year_level_id = null;
                }
            }
            if (!empty($this->request->data['DormitoryAssignment']['dormitory_block'])) {
                $dormitory_block_id = $this->request->data['DormitoryAssignment']['dormitory_block'];
                $this->Session->write('dormitory_block_id', $dormitory_block_id);
            } else {
                if ($this->Session->read('dormitory_block_id')) {
                    $dormitory_block_id = $this->Session->read('dormitory_block_id');
                } else {
                    $dormitory_block_id = null;
                    //$this->Session->write('dormitory_block_id',$dormitory_block_id);
                }
            }
            if (!empty($this->request->data['DormitoryAssignment']['dormitory'])) {
                $dormitory_id = $this->request->data['DormitoryAssignment']['dormitory'];
                $this->Session->write('dormitory_id', $dormitory_id);
            } else {
                if ($this->Session->read('dormitory_id')) {
                    $dormitory_id = $this->Session->read('dormitory_id');
                } else {
                    $dormitory_id = null;
                }
            }

            if (!empty($selected_college_id)) {
                $departments = $this->_get_departments_list($selected_college_id);
            } else {
                $departments = null;
            }
            if (!empty($selected_department_id)) {
                $yearLevels = $this->_get_year_levels_list($selected_department_id);
            } else {
                $yearLevels = null;
            }
            if (!empty($dormitory_block_id)) {
                $dormitories = $this->_get_dormitories_list($dormitory_block_id);
            } else {
                $dormitories = null;
            }
            if (!empty($selected_gender)) {
                $fine_formatted_dormitories = $this->_get_formated_dormitory_block(
                    $selected_gender,
                    $assigned_dormitory_block_ids
                );
            } else {
                $fine_formatted_dormitories = $this->_get_formated_dormitory_block("%", $assigned_dormitory_block_ids);
            }
            if (empty($dormitory_block_id)) {
                $dormitoryAssignments = $this->_get_already_assigned_students(
                    $selected_program_id,
                    $selected_program_type_id,
                    $selected_gender,
                    $selected_college_id,
                    $selected_department_id,
                    $selected_year_level_id,
                    $assigned_dormitory_block_ids,
                    $dormitory_id,
                    $is_atleat_one_parameter_isset
                );
            } else {
                $dormitoryAssignments = $this->_get_already_assigned_students(
                    $selected_program_id,
                    $selected_program_type_id,
                    $selected_gender,
                    $selected_college_id,
                    $selected_department_id,
                    $selected_year_level_id,
                    $dormitory_block_id,
                    $dormitory_id,
                    $is_atleat_one_parameter_isset
                );
            }
            $this->set(
                compact(
                    'programs',
                    'programTypes',
                    'colleges',
                    'fine_formatted_dormitories',
                    'departments',
                    'yearLevels',
                    'dormitories',
                    'dormitoryAssignments',
                    'selected_program_id',
                    'selected_program_type_id',
                    'selected_gender',
                    'selected_college_id',
                    'selected_department_id',
                    'selected_year_level_id',
                    'dormitory_block_id',
                    'dormitory_id'
                )
            );

            if ($this->Session->read('from_delete')) {
                $this->Session->delete('from_delete');
            }
            if ($this->Session->read('from_manually_add')) {
                $this->Session->delete('from_manually_add');
            }
        } else {
            $this->Session->setFlash(
                '<span></span>' . __(
                    'Currently you do not have dormitory block to manage. So Please contact dormitory block administrator.'
                ),
                'default',
                array('class' => 'error-box er-message')
            );
        }
    }

    public function add_student_dormitory($data = null)
    {

        $this->layout = 'ajax';
        $explode_data = explode('~', $data);
        $dormitory_id = $explode_data[0];
        $selected_program_id = $explode_data[1];
        $selected_program_type_id = $explode_data[2];
        $selected_gender = $explode_data[3];
        $selected_college_id = $explode_data[4];
        $selected_department_id = $explode_data[5];
        $selected_year_level_id = $explode_data[6];
        $dormitory_block_id = $explode_data[7];

        //Get student_id that have dormitory in there name
        $student_ids = $this->DormitoryAssignment->get_student_have_dormitory();
        //Get accepted_student_id that have dormitory in there name
        $accepted_student_ids = $this->DormitoryAssignment->get_accepted_student_have_dormitory();

        //Get dormitories unassigned students
        $unassigned_students = null;
        //department unassign students
        if ($selected_department_id == 10000) {
            $unassigned_students = $this->DormitoryAssignment->Student->getListOfDepartmentNonAssignedStudents(
                $selected_college_id,
                $selected_program_id,
                $selected_program_type_id,
                $selected_gender,
                $student_ids,
                $accepted_student_ids
            );
            //All department including department unassigned students
        } else {
            if (empty($selected_department_id)) {
                $unassigned_students_array_department_non_assigned = $this->DormitoryAssignment->Student->getListOfDepartmentNonAssignedStudents(
                    $selected_college_id,
                    $selected_program_id,
                    $selected_program_type_id,
                    $selected_gender,
                    $student_ids,
                    $accepted_student_ids
                );

                $unassigned_students_array_department_assigned = $this->DormitoryAssignment->Student->getListOfDepartmentStudentsByYearLevel(
                    $selected_college_id,
                    $selected_department_id,
                    $selected_program_id,
                    $selected_program_type_id,
                    $selected_year_level_id,
                    1,
                    $selected_gender,
                    $student_ids,
                    $accepted_student_ids
                );
                //Merge both

                $unassigned_students = array_merge(
                    $unassigned_students_array_department_non_assigned,
                    $unassigned_students_array_department_assigned
                );
                //Only department assign students
            } else {
                $unassigned_students = $this->DormitoryAssignment->Student->getListOfDepartmentStudentsByYearLevel(
                    $selected_college_id,
                    $selected_department_id,
                    $selected_program_id,
                    $selected_program_type_id,
                    $selected_year_level_id,
                    1,
                    $selected_gender,
                    $student_ids,
                    $accepted_student_ids
                );
            }
        }
        $admitted_unassigned_students = $unassigned_students['student'];
        $non_admitted_unassigned_students = $unassigned_students['accepted_student'];

        $unassigned_students_array = array();
        if (!empty($admitted_unassigned_students)) {
            foreach ($admitted_unassigned_students as $student) {
                $unassigned_students_array[$student['Student']['id'] . '~' . 'S'] = $student['Student']['studentnumber'] . ' ' . $student['Student']['full_name'];
            }
        }
        if (!empty($non_admitted_unassigned_students)) {
            foreach ($non_admitted_unassigned_students as $acceptedStudent) {
                $unassigned_students_array[$acceptedStudent['AcceptedStudent']['id'] . '~' . 'AS'] = $acceptedStudent['AcceptedStudent']['studentnumber'] . ' ' . $acceptedStudent['AcceptedStudent']['full_name'];
            }
        }

        $this->set(compact('unassigned_students_array', 'dormitory_id'));
    }

    public function add_student_dormitory_update()
    {

        if (!empty($this->request->data)) {
            $from_manually_add = 1;
            $this->Session->write('from_manually_add', $from_manually_add);
            $selected_id = $this->request->data['DormitoryAssignment']['Selected_student_id'];
            $explode_selected_id = explode('~', $selected_id);
            $student_name = null;
            $selected_dormitory_data = $this->DormitoryAssignment->Dormitory->find(
                'first',
                array(
                    'fields' => array('Dormitory.id', 'Dormitory.capacity'),
                    'conditions' => array('Dormitory.id' => $this->request->data['DormitoryAssignment']['dormitory_id'])
                )
            );
            $selected_dormitory_free_space = $this->DormitoryAssignment->get_free_dormitory_space(
                $selected_dormitory_data
            );
            $dormitory_number = $this->DormitoryAssignment->Dormitory->field(
                'Dormitory.dorm_number',
                array('Dormitory.id' => $this->request->data['DormitoryAssignment']['dormitory_id'])
            );
            if ($selected_dormitory_free_space != 0) {
                if (strcasecmp($explode_selected_id[1], "S") == 0) {
                    $this->request->data['DormitoryAssignments']['student_id'] = $explode_selected_id[0];
                    $student_name = $this->DormitoryAssignment->Student->field(
                        'Student.full_name',
                        array('Student.id' => $explode_selected_id[0])
                    );
                } else {
                    $this->request->data['DormitoryAssignments']['accepted_student_id'] = $explode_selected_id[0];
                    $student_name = $this->DormitoryAssignment->AcceptedStudent->field(
                        'AcceptedStudent.full_name',
                        array('AcceptedStudent.id' => $explode_selected_id[0])
                    );
                }
                $this->request->data['DormitoryAssignments']['dormitory_id'] = $this->request->data['DormitoryAssignment']['dormitory_id'];
                $this->DormitoryAssignment->create();
                $this->DormitoryAssignment->save($this->request->data['DormitoryAssignments']);
                $this->Session->setFlash(
                    '<span></span>' . __(
                        ' Student ' . $student_name . ' is manually assigned to dormitory ' . $dormitory_number
                    ),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'add_delete'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        ' You can not add student in dormitory ' . $dormitory_number . ' since the dormitory was full.'
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
                return $this->redirect(array('action' => 'add_delete'));
            }
        }
    }

    public function entry_leave()
    {

        $this->paginate = array('order' => 'DormitoryAssignment.created DESC', 'limit' => 200);
        $user_id = $this->Auth->user('id');
        $assigned_dormitory_block_ids = $this->DormitoryAssignment->get_assigned_dormitory_blocks($user_id);
        $assigned_dormitory_block_count = count($assigned_dormitory_block_ids);
        $this->__init_search();
        if ($this->Session->read('search_data')) {
            $this->request->data['Search'] = $this->Session->read('search_data');
            $this->request->data['search'] = true;
        }

        if (!empty($assigned_dormitory_block_count)) {
            $fine_formatted_dormitories = $this->_get_formated_dormitory_block("%", $assigned_dormitory_block_ids);
            if (!empty($dormitory_block_id)) {
                if (!empty($dormitory_id)) {
                    $dormitories_ids = $dormitory_id;
                } else {
                    $dormitories_ids = $this->DormitoryAssignment->Dormitory->find(
                        'list',
                        array(
                            'fields' => array('Dormitory.id'),
                            'conditions' => array('Dormitory.dormitory_block_id' => $dormitory_block_id)
                        )
                    );
                }
                $dormitories = $this->_get_dormitories_list($dormitory_block_id);
                //$options[] = array('DormitoryAssignment.dormitory_id'=>$dormitories_ids);
            } else {
                $dormitories = array();
            }
            $this->set(compact('fine_formatted_dormitories', 'dormitories'));
            if (!empty($this->request->data) && isset($this->request->data['search'])) {
                $options = array('DormitoryAssignment.leave_date is null');
                if (!empty($this->request->data['Search']['dormitory_block'])) {
                    $dormitory_block_id = $this->request->data['Search']['dormitory_block'];
                } else {
                    $dormitory_block_id = null;
                }
                if (!empty($this->request->data['Search']['dormitory'])) {
                    $dormitory_id = $this->request->data['Search']['dormitory'];
                } else {
                    $dormitory_id = null;
                }

                if (!empty($dormitory_block_id)) {
                    if (!empty($dormitory_id)) {
                        $dormitories_ids = $dormitory_id;
                    } else {
                        $dormitories_ids = $this->DormitoryAssignment->Dormitory->find(
                            'list',
                            array(
                                'fields' => array('Dormitory.id'),
                                'conditions' => array('Dormitory.dormitory_block_id' => $dormitory_block_id)
                            )
                        );
                    }
                    $dormitories = $this->_get_dormitories_list($dormitory_block_id);
                    $options[] = array('DormitoryAssignment.dormitory_id' => $dormitories_ids);
                } else {
                    $dormitories = null;
                    $dormitories_ids = $this->DormitoryAssignment->Dormitory->find(
                        'list',
                        array(
                            'fields' => array('Dormitory.id'),
                            'conditions' => array('Dormitory.dormitory_block_id' => $assigned_dormitory_block_ids)
                        )
                    );
                    $options[] = array('DormitoryAssignment.dormitory_id' => $dormitories_ids);
                }

                $this->paginate = array(
                    'conditions' => $options,
                    'contain' => array(
                        'Dormitory' => array(
                            'fields' => array(
                                'Dormitory.id',
                                'Dormitory.dorm_number',
                                'Dormitory.floor'
                            ),
                            'DormitoryBlock' => array(
                                'fields' => array(
                                    'DormitoryBlock.id',
                                    'DormitoryBlock.block_name'
                                ),
                                'Campus' => array('fields' => array('Campus.name'))
                            )
                        ),
                        'Student' => array(
                            'fields' => array(
                                'Student.id',
                                'Student.full_name',
                                'Student.studentnumber'
                            )
                        ),
                        'AcceptedStudent' => array(
                            'fields' => array(
                                'AcceptedStudent.id',
                                'AcceptedStudent.full_name',
                                'AcceptedStudent.studentnumber'
                            )
                        )
                    )
                );
                $dormitoryAssignments = $this->paginate();

                $this->set(compact('fine_formatted_dormitories', 'dormitories', 'dormitoryAssignments'));
            }
            if (!empty($this->request->data) && isset($this->request->data['update'])) {
                //$selected_for_received = array();
                $update_received_date = array();

                if (isset($this->request->data['DormitoryAssignment']['Is_received']) && !empty($this->request->data['DormitoryAssignment']['Is_received'])) {
                    $index = 0;
                    foreach ($this->request->data['DormitoryAssignment']['Is_received'] as $irk => $irv) {
                        if ($irv != 0) {
                            $update_received_date['DormitoryAssignment'][$index]['id'] = $irk;
                            $update_received_date['DormitoryAssignment'][$index]['received_date'] = $this->request->data['DormitoryAssignment']['update_date'];
                            $update_received_date['DormitoryAssignment'][$index]['received'] = 1;
                            $index++;
                        }
                    }
                }
                $update_return_date = array();
                if (isset($this->request->data['DormitoryAssignment']['Is_return']) && !empty($this->request->data['DormitoryAssignment']['Is_return'])) {
                    $index = 0;
                    foreach ($this->request->data['DormitoryAssignment']['Is_return'] as $ilk => $ilv) {
                        if ($ilv != 0) {
                            $update_return_date['DormitoryAssignment'][$index]['id'] = $ilk;
                            $update_return_date['DormitoryAssignment'][$index]['leave_date'] = $this->request->data['DormitoryAssignment']['update_date'];
                            $index++;
                        }
                    }
                }
                if (!empty($update_received_date) || !empty($update_return_date)) {
                    //for received_date
                    $who_is_update = null;
                    if (!empty($update_received_date)) {
                        $this->DormitoryAssignment->saveAll(
                            $update_received_date['DormitoryAssignment'],
                            array('validate' => false)
                        );
                        $who_is_update = "received date";
                    }
                    //for return (leave_date)
                    if (!empty($update_return_date)) {
                        $this->DormitoryAssignment->saveAll(
                            $update_return_date['DormitoryAssignment'],
                            array('validate' => false)
                        );
                        if (empty($who_is_update)) {
                            $who_is_update = "leave date";
                        } else {
                            $who_is_update = $who_is_update . ' and ' . 'leave date';
                        }
                    }

                    $this->Session->setFlash(
                        '<span></span>' . __('Selected ' . $who_is_update . ' updated successfully.'),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __('Please check at least one received date or leave date.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
                $fine_formatted_dormitories = $this->_get_formated_dormitory_block("%", $assigned_dormitory_block_ids);
                $this->request->data['search'] = true;
                $options = array('DormitoryAssignment.leave_date' => null);
                if (!empty($this->request->data['Search']['dormitory_block'])) {
                    $dormitory_block_id = $this->request->data['Search']['dormitory_block'];
                } else {
                    $dormitory_block_id = null;
                }
                if (!empty($this->request->data['Search']['dormitory'])) {
                    $dormitory_id = $this->request->data['Search']['dormitory'];
                } else {
                    $dormitory_id = null;
                }

                if (!empty($dormitory_block_id)) {
                    if (!empty($dormitory_id)) {
                        $dormitories_ids = $dormitory_id;
                    } else {
                        $dormitories_ids = $this->DormitoryAssignment->Dormitory->find(
                            'list',
                            array(
                                'fields' => array('Dormitory.id'),
                                'conditions' => array('Dormitory.dormitory_block_id' => $dormitory_block_id)
                            )
                        );
                    }
                    $dormitories = $this->_get_dormitories_list($dormitory_block_id);
                    $options[] = array('DormitoryAssignment.dormitory_id' => $dormitories_ids);
                } else {
                    $dormitories = null;
                    $dormitories_ids = $this->DormitoryAssignment->Dormitory->find(
                        'list',
                        array(
                            'fields' => array('Dormitory.id'),
                            'conditions' => array('Dormitory.dormitory_block_id' => $assigned_dormitory_block_ids)
                        )
                    );
                    $options[] = array('DormitoryAssignment.dormitory_id' => $dormitories_ids);
                }

                $this->paginate = array(
                    'conditions' => $options,
                    'contain' => array(
                        'Dormitory' => array(
                            'fields' => array(
                                'Dormitory.id',
                                'Dormitory.dorm_number',
                                'Dormitory.floor'
                            ),
                            'DormitoryBlock' => array(
                                'fields' => array(
                                    'DormitoryBlock.id',
                                    'DormitoryBlock.block_name'
                                ),
                                'Campus' => array('fields' => array('Campus.name'))
                            )
                        ),
                        'Student' => array(
                            'fields' => array(
                                'Student.id',
                                'Student.full_name',
                                'Student.studentnumber'
                            )
                        ),
                        'AcceptedStudent' => array(
                            'fields' => array(
                                'AcceptedStudent.id',
                                'AcceptedStudent.full_name',
                                'AcceptedStudent.studentnumber'
                            )
                        )
                    )
                );

                $dormitoryAssignments = $this->paginate();

                $this->set(compact('fine_formatted_dormitories', 'dormitories', 'dormitoryAssignments'));
            }
        } else {
            $this->Session->setFlash(
                '<span></span>' . __(
                    'Currently you do not have dormitory block to manage. So Please contact dormitory block administrator.'
                ),
                'default',
                array('class' => 'error-box er-message')
            );
        }
    }

    /*function edit($id = null) {
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid dormitory assignment'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->DormitoryAssignment->save($this->request->data)) {
                $this->Session->setFlash(__('The dormitory assignment has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The dormitory assignment could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->DormitoryAssignment->read(null, $id);
        }
        $dormitories = $this->DormitoryAssignment->Dormitory->find('list');
        $students = $this->DormitoryAssignment->Student->find('list');
        $acceptedStudents = $this->DormitoryAssignment->AcceptedStudent->find('list');
        $this->set(compact('dormitories', 'students', 'acceptedStudents'));
    }*/

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid id for dormitory assignment'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'add_delete'));
        }
        $from_delete = 1;
        $this->Session->write('from_delete', $from_delete);
        $is_received = $this->DormitoryAssignment->field(
            'DormitoryAssignment.received',
            array('DormitoryAssignment.id' => $id)
        );
        if ($is_received == 0) {
            if ($this->DormitoryAssignment->delete($id)) {
                $this->Session->setFlash(
                    '<span></span>' . __('Dormitory assignment deleted'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'add_delete'));
            }
        } else {
            $this->Session->setFlash(
                '<span></span>' . __(
                    'You can not deleted this dormitory assignment since the student already received assigned dormitory.'
                ),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'add_delete'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Dormitory assignment was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'add_delete'));
    }

    public function get_departments($college_id = null)
    {

        if (!empty($college_id)) {
            $this->layout = 'ajax';
            $departments = $this->_get_departments_list($college_id);
            $this->set(compact('departments'));
        }
    }

    public function get_year_levels($department_id = null)
    {

        if (!empty($department_id)) {
            $this->layout = 'ajax';
            $yearLevels = $this->_get_year_levels_list($department_id);
            $this->set(compact('yearLevels'));
        }
    }

    public function get_section()
    {

        $this->layout = 'ajax';
        $sections = $this->__getSection($this->request->data);
        $this->set(compact('sections'));
    }

    public function get_dormitory_blocks($gender = null)
    {

        $this->layout = 'ajax';
        $user_id = $this->Auth->user('id');
        $assigned_dormitory_block_ids = $this->DormitoryAssignment->get_assigned_dormitory_blocks($user_id);
        if (empty($gender)) {
            $fine_formatted_dormitories = $this->_get_formated_dormitory_block("%", $assigned_dormitory_block_ids);
        } else {
            $fine_formatted_dormitories = $this->_get_formated_dormitory_block($gender, $assigned_dormitory_block_ids);
        }
        $this->set(compact('fine_formatted_dormitories'));
    }

    public function get_dormitories($dormitory_block_id = null)
    {

        if (!empty($dormitory_block_id)) {
            $this->layout = 'ajax';
            $dormitories = $this->_get_dormitories_list($dormitory_block_id);
            $this->set(compact('dormitories'));
        }
    }

    public function _get_departments_list($college_id = null)
    {

        $departments = $this->DormitoryAssignment->AcceptedStudent->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $college_id))
        );
        $departments['10000'] = 'Pre/(Unassign Freshman)';

        return $departments;
    }

    public function _get_year_levels_list($department_id = null)
    {

        if ($department_id == 10000) {
            $yearLevels[10000] = '1st';
        } else {
            $yearLevels = ClassRegistry::init('YearLevel')->find(
                'list',
                array('conditions' => array('YearLevel.department_id' => $department_id))
            );
        }

        return $yearLevels;
    }

    public function _get_dormitories_list($dormitory_block_id = null)
    {

        if (!empty($dormitory_block_id)) {
            $dormitories = $this->DormitoryAssignment->Dormitory->find(
                'list',
                array(
                    'conditions' => array('Dormitory.dormitory_block_id' => $dormitory_block_id),
                    'order' => array('Dormitory.floor', 'Dormitory.dorm_number')
                )
            );

            return $dormitories;
        }
    }

    public function _get_formated_dormitory_block($gender = null, $assigned_dormitory_block_ids = null)
    {

        $dormitory_block_ids = $this->DormitoryAssignment->Dormitory->DormitoryBlock->find(
            'list',
            array(
                'conditions' => array(
                    'DormitoryBlock.type LIKE' => $gender,
                    'DormitoryBlock.id' => $assigned_dormitory_block_ids
                )
            )
        );

        $dormitories = $this->DormitoryAssignment->Dormitory->find(
            'all',
            array(
                'conditions' => array(
                    'Dormitory.available' => 1,
                    'Dormitory.dormitory_block_id' => $dormitory_block_ids
                ),
                'fields' => array('Dormitory.id', 'Dormitory.capacity', 'Dormitory.dormitory_block_id'),
                'contain' => array(
                    'DormitoryBlock' => array(
                        'fields' => array(
                            'DormitoryBlock.id',
                            'DormitoryBlock.block_name'
                        ),
                        'Campus' => array('fields' => array('Campus.name'))
                    )
                )
            )
        );
        //formated dormitories in it is block
        $formatted_dormitories = array();
        foreach ($dormitories as $dormitory) {
            $formatted_dormitories[$dormitory['DormitoryBlock']['Campus']['name']][$dormitory['DormitoryBlock']['id']]['name'] = $dormitory['DormitoryBlock']['block_name'];
            $formatted_dormitories[$dormitory['DormitoryBlock']['Campus']['name']][$dormitory['DormitoryBlock']['id']][] = $dormitory;
        }
        $fine_formatted_dormitories = array();
        foreach ($formatted_dormitories as $ck => $cv) {
            foreach ($cv as $bk => $bv) {
                $block_name = $bv['name'];
                unset($bv['name']);
                $capacities = $this->DormitoryAssignment->get_block_capacity($bv);
                if ($capacities['free_capacity'] == 0) {
                    $fine_formatted_dormitories[$ck][$bk] = $block_name . ' (Full with:' . $capacities['total_capacity'] . ' students)';
                } else {
                    $fine_formatted_dormitories[$ck][$bk] = $block_name . ' (Free Space:' . $capacities['free_capacity'] . ' out of ' . $capacities['total_capacity'] . ')';
                }
            }
        }

        return $fine_formatted_dormitories;
    }

    public function _get_already_assigned_students(
        $program_id = null,
        $program_type_id = null,
        $gender = null,
        $college_id = null,
        $department_id = null,
        $year_level_id = null,
        $dormitory_block_id = null,
        $dormitory_id = null,
        $is_atleat_one_parameter_isset = null,
        $limit = 100
    ) {

        $options = array('DormitoryAssignment.leave_date' => null);
        if ($is_atleat_one_parameter_isset == true) {
            $students_array = null;
            //department unassign students
            if ($department_id == 10000) {
                $students_array = $this->DormitoryAssignment->Student->getListOfDepartmentNonAssignedStudents(
                    $college_id,
                    $program_id,
                    $program_type_id,
                    $gender,
                    null,
                    null,
                    $limit
                );
                //All department including department unassigned students
            } else {
                if (empty($department_id)) {
                    $students_array_department_non_assigned = $this->DormitoryAssignment->Student->getListOfDepartmentNonAssignedStudents(
                        $college_id,
                        $program_id,
                        $program_type_id,
                        $gender,
                        null,
                        null,
                        $limit
                    );

                    $students_array_department_assigned = $this->DormitoryAssignment->Student->getListOfDepartmentStudentsByYearLevel(
                        $college_id,
                        $department_id,
                        $program_id,
                        $program_type_id,
                        $year_level_id,
                        0,
                        $gender,
                        null,
                        null,
                        $limit
                    );
                    //Merge both

                    $students_array = array_merge(
                        $students_array_department_non_assigned,
                        $students_array_department_assigned
                    );
                    //Only department assign students
                } else {
                    $students_array = $this->DormitoryAssignment->Student->getListOfDepartmentStudentsByYearLevel(
                        $college_id,
                        $department_id,
                        $program_id,
                        $program_type_id,
                        $year_level_id,
                        0,
                        $gender,
                        null,
                        null,
                        $limit
                    );
                }
            }

            $admitted_students = $students_array['student'];
            $non_admitted_students = $students_array['accepted_student'];
            $student_ids = array();
            if (!empty($admitted_students)) {
                foreach ($admitted_students as $admitted_student) {
                    $student_ids[$admitted_student['Student']['id']] = $admitted_student['Student']['id'];
                }
            }
            $accepted_student_ids = array();
            if (!empty($non_admitted_students)) {
                foreach ($non_admitted_students as $non_admitted_student) {
                    $accepted_student_ids[$non_admitted_student['AcceptedStudent']['id']] = $non_admitted_student['AcceptedStudent']['id'];
                }
            }
            if (!empty($student_ids) && !empty($accepted_student_ids)) {
                $options[] = array(
                    "OR" => array(
                        'DormitoryAssignment.accepted_student_id' => $accepted_student_ids,
                        'DormitoryAssignment.student_id' => $student_ids
                    )
                );
            } else {
                if (!empty($student_ids)) {
                    $options[] = array('DormitoryAssignment.student_id' => $student_ids);
                } else {
                    if (!empty($accepted_student_ids)) {
                        $options[] = array('DormitoryAssignment.accepted_student_id' => $accepted_student_ids);
                    } else {
                        $options[] = array(
                            'DormitoryAssignment.accepted_student_id' => $accepted_student_ids,
                            'DormitoryAssignment.student_id' => $student_ids
                        );
                    }
                }
            }
        }

        if (!empty($dormitory_block_id)) {
            if (!empty($dormitory_id)) {
                $dormitories_ids = $dormitory_id;
            } else {
                $dormitories_ids = $this->DormitoryAssignment->Dormitory->find(
                    'list',
                    array(
                        'fields' => array('Dormitory.id'),
                        'conditions' => array('Dormitory.dormitory_block_id' => $dormitory_block_id)
                    )
                );
            }
            $options[] = array('DormitoryAssignment.dormitory_id' => $dormitories_ids);
        }

        $this->paginate = array(
            'conditions' => $options,
            'limit' => $limit,
            'maxLimit' => $limit,
            'contain' => array(
                'Dormitory' => array(
                    'fields' => array('Dormitory.id', 'Dormitory.dorm_number', 'Dormitory.floor'),
                    'DormitoryBlock' => array(
                        'fields' => array('DormitoryBlock.id', 'DormitoryBlock.block_name'),
                        'Campus' => array('fields' => array('Campus.name'))
                    )
                ),
                'Student' => array('fields' => array('Student.id', 'Student.full_name', 'Student.studentnumber')),
                'AcceptedStudent' => array(
                    'fields' => array(
                        'AcceptedStudent.id',
                        'AcceptedStudent.full_name',
                        'AcceptedStudent.studentnumber'
                    )
                )
            )
        );

        $this->Paginator->settings = $this->paginate;

        return $this->Paginator->paginate('DormitoryAssignment');
        //return $this->paginate();
    }

    public function __init_search()
    {

        // We create a search_data session variable when we fill any criteria
        // in the search form.
        if (!empty($this->request->data['Search'])) {
            $search_session = $this->request->data['Search'];
            // Session variable 'search_data'
            $this->Session->write('search_data', $search_session);
        } else {
            $search_session = $this->Session->read('search_data');
            $this->request->data['Search'] = $search_session;
        }
    }

    public function getAssignedStudent($dorm_id = null)
    {

        $this->layout = 'ajax';
        if (!empty($dorm_id)) {
            $assignedStudents = $this->DormitoryAssignment->find(
                'all',
                array(
                    'conditions' => array('DormitoryAssignment.dormitory_id' => $dorm_id),
                    'contain' => array('Student' => array('Department'))
                )
            );

            $this->set(compact('assignedStudents'));
        }
    }

    public function __getSection($data)
    {

        $options = array();
        $options['conditions']['Section.archive'] = 0;
        $options['fields'] = array(
            'Section.id',
            'Section.name'
        );
        if (!empty($data['DormitoryAssignment']['year_level_id'])) {
            $options['conditions']['Section.year_level_id'] = $data['DormitoryAssignment']['year_level_id'];
        }
        if (!empty($data['DormitoryAssignment']['program_type_id'])) {
            $options['conditions']['Section.program_type_id'] = $data['DormitoryAssignment']['program_type_id'];
        }
        if (!empty($data['DormitoryAssignment']['program_id'])) {
            $options['conditions']['Section.program_id'] = $data['DormitoryAssignment']['program_id'];
        }
        if (!empty($data['DormitoryAssignment']['department_id'])) {
            $options['conditions']['Section.department_id'] = $data['DormitoryAssignment']['department_id'];
        }
        return ClassRegistry::init('Section')->find('list', $options);
    }

}

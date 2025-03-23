<?php

namespace App\Controller;

use Cake\Event\Event;


class MealHallAssignmentsController extends AppController
{

    public $name = 'MealHallAssignments';

    public $menuOptions = array(
        'parent' => 'mealService',
        'exclude' => array(
            'get_colleges',
            'get_departments',
            'get_year_levels',
            'get_department_year_levels',
            'add_student_meal_hall',
            'add_student_meal_hall_update'
        ),
        'alias' => array(
            'index' => 'List Meal Hall Assigned Students',
            'add' => 'Auto Students Meal Hall Assignment',
            'add_delete' => 'Manual Student Assignment and Cancellation'
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
            'get_colleges',
            'get_departments',
            'get_year_levels',
            'get_department_year_levels',
            'add_student_meal_hall',
            'add_student_meal_hall_update'
        );
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyearArray();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        //To get current and next academic year
        $firstpartacademicyear = substr($defaultacademicyear, 0, 4);
        $secondpartacademicyear = substr($defaultacademicyear, -2);

        $next_acyear = ($firstpartacademicyear + 1) . '/' . ($secondpartacademicyear + 1);

        $current_and_next_acyear = array();
        $current_and_next_acyear[$defaultacademicyear] = $defaultacademicyear;
        $current_and_next_acyear[$next_acyear] = $next_acyear;

        $this->set(compact('acyear_array_data', 'defaultacademicyear', 'current_and_next_acyear'));
        unset($this->request->data['User']['password']);
    }

    public function index()
    {

        $colleges = $this->MealHallAssignment->Student->College->find('list');
        $programs = $this->MealHallAssignment->Student->Program->find('list');
        $programTypes = $this->MealHallAssignment->Student->ProgramType->find('list');
        $mealHalls = $this->MealHallAssignment->get_formatted_mealhall_for_view();
        $is_atleat_one_parameter_isset = false;
        if (!empty($this->request->data['MealHallAssignment']['program_type_id'])) {
            $program_type_id = $this->request->data['MealHallAssignment']['program_type_id'];
            $is_atleat_one_parameter_isset = true;
        } else {
            $program_type_id = null;
        }
        if (!empty($this->request->data['MealHallAssignment']['college_id'])) {
            $college_id = $this->request->data['MealHallAssignment']['college_id'];
            $is_atleat_one_parameter_isset = true;
        } else {
            $college_id = null;
        }
        if (!empty($this->request->data['MealHallAssignment']['department_id'])) {
            $department_id = $this->request->data['MealHallAssignment']['department_id'];
            $is_atleat_one_parameter_isset = true;
        } else {
            $department_id = null;
        }
        if (!empty($this->request->data['MealHallAssignment']['year_level_id'])) {
            $year_level_id = $this->request->data['MealHallAssignment']['year_level_id'];
            $is_atleat_one_parameter_isset = true;
        } else {
            $year_level_id = null;
        }
        if (!empty($this->request->data['MealHallAssignment']['academic_year'])) {
            $academic_year = $this->request->data['MealHallAssignment']['academic_year'];
        } else {
            $academic_year = null;
        }
        if (!empty($this->request->data['MealHallAssignment']['meal_hall_id'])) {
            $meal_hall_id = $this->request->data['MealHallAssignment']['meal_hall_id'];
        } else {
            $meal_hall_id = null;
        }

        if (!empty($college_id)) {
            $departments = $this->_get_departments_list($college_id);
        } else {
            $departments = null;
        }
        if (!empty($department_id)) {
            $yearLevels = $this->_get_department_year_levels_list($department_id);
        } else {
            if (!empty($college_id)) {
                $yearLevels = $this->_get_year_levels_list($college_id);
            } else {
                $yearLevels = null;
            }
        }
        $mealHallAssignments = $this->_get_already_assigned_students(
            $program_type_id,
            $college_id,
            $department_id,
            $year_level_id,
            $academic_year,
            $meal_hall_id,
            $is_atleat_one_parameter_isset
        );

        $this->set(
            compact(
                'mealHallAssignments',
                'colleges',
                'programs',
                'programTypes',
                'departments',
                'yearLevels',
                'mealHalls'
            )
        );
    }

    /*function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid meal hall assignment'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('mealHallAssignment', $this->MealHallAssignment->read(null, $id));
    }*/

    public function add()
    {

        if ($this->Session->read('selected_program_type')) {
            $this->Session->delete('selected_program_type');
        }
        if ($this->Session->read('selected_campus')) {
            $this->Session->delete('selected_campus');
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
        if ($this->Session->read('selected_academicyear')) {
            $this->Session->delete('selected_academicyear');
        }
        $beforesearch = 1;
        $this->set(compact('beforesearch'));
        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $beforesearch = 0;
            $everythingfine = false;

            switch ($this->request->data) {
                case empty($this->request->data['MealHallAssignment']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select the program type that you want to assign meal hall. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['MealHallAssignment']['campus_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select campus that you want to assign meal hall. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['MealHallAssignment']['college_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select college that you want to assign meal hall. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['MealHallAssignment']['year_level_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select Year Level that you want to assign meal hall. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['MealHallAssignment']['academic_year']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select academic year that you want to assign meal hall. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                $selected_program_type = $this->request->data['MealHallAssignment']['program_type_id'];
                $this->Session->write('selected_program_type', $selected_program_type);
                $selected_campus = $this->request->data['MealHallAssignment']['campus_id'];
                $this->Session->write('selected_campus', $selected_campus);
                $selected_college = $this->request->data['MealHallAssignment']['college_id'];
                $this->Session->write('selected_college', $selected_college);
                $selected_department = $this->request->data['MealHallAssignment']['department_id'];
                $this->Session->write('selected_department', $selected_department);
                $selected_year_level = $this->request->data['MealHallAssignment']['year_level_id'];
                $this->Session->write('selected_year_level', $selected_year_level);
                $selected_academicyear = $this->request->data['MealHallAssignment']['academic_year'];
                $this->Session->write('selected_academicyear', $selected_academicyear);

                $colleges = $this->_get_colleges_list($selected_campus);
                $departments = $this->_get_departments_list($selected_college);
                if (!empty($selected_department)) {
                    $yearLevels = $this->_get_department_year_levels_list($selected_department);
                } else {
                    $yearLevels = $this->_get_year_levels_list($selected_college);
                }

                //Get student_id that have dormitory in there name
                $student_ids = $this->MealHallAssignment->get_student_have_mealhall($selected_academicyear);
                //Get accepted_student_id that have dormitory in there name
                $accepted_student_ids = $this->MealHallAssignment->get_accepted_student_have_mealhall(
                    $selected_academicyear
                );

                //Get Meal halls unassigned students
                $unassigned_students = $this->_get_meal_hall_unassigned_students(
                    $selected_program_type,
                    $selected_college,
                    $selected_department,
                    $selected_year_level,
                    $student_ids,
                    $accepted_student_ids
                );
                $admitted_unassigned_students = $unassigned_students['student'];
                $non_admitted_unassigned_students = $unassigned_students['accepted_student'];

                $mealHalls = $this->MealHallAssignment->get_formatted_mealhall($selected_academicyear);

                $this->set(
                    compact(
                        'colleges',
                        'departments',
                        'yearLevels',
                        'selected_program_type',
                        'selected_campus',
                        'selected_college',
                        'selected_department',
                        'selected_year_level',
                        'beforesearch',
                        'selected_academicyear',
                        'admitted_unassigned_students',
                        'non_admitted_unassigned_students',
                        'mealHalls'
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
                $selected_meal_hall_id = $this->request->data['MealHallAssignment']['meal_hall'];
                $selected_academicyear = $this->request->data['MealHallAssignment']['academic_year'];
                if (!empty($selected_meal_hall_id)) {
                    $totoal_selected_student_count = $count_selected_students + $count_selected_accepted_students;

                    $isassign = false;
                    //Assign students to selected meal halls
                    if (!empty($selected_students)) {
                        $assign_students = array();
                        $index = 0;
                        foreach ($selected_students as $selected_student) {
                            $assign_students['MealHallAssignment'][$index]['meal_hall_id'] = $selected_meal_hall_id;
                            $assign_students['MealHallAssignment'][$index]['student_id'] = $selected_student;
                            $assign_students['MealHallAssignment'][$index]['academic_year'] = $selected_academicyear;
                            $index++;
                        }
                        $this->MealHallAssignment->create();
                        if ($this->MealHallAssignment->saveAll(
                            $assign_students['MealHallAssignment'],
                            array('validate' => 'first')
                        )) {
                            $isassign = true;
                        }
                    }
                    if (!empty($selected_accepted_students)) {
                        $assign_accepted_students = array();
                        $index = 0;
                        foreach ($selected_accepted_students as $selected_accepted_student) {
                            $assign_accepted_students['MealHallAssignment'][$index]['meal_hall_id'] = $selected_meal_hall_id;
                            $assign_accepted_students['MealHallAssignment'][$index]['accepted_student_id'] = $selected_accepted_student;
                            $assign_accepted_students['MealHallAssignment'][$index]['academic_year'] = $selected_academicyear;
                            $index++;
                        }
                        $this->MealHallAssignment->create();
                        if ($this->MealHallAssignment->saveAll(
                            $assign_accepted_students['MealHallAssignment'],
                            array('validate' => 'first')
                        )) {
                            $isassign = true;
                        }
                    }

                    if ($isassign == true) {
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                'All selected ' . $totoal_selected_student_count . ' students have been assigned successfully.'
                            ),
                            'default',
                            array('class' => 'success-box success-message')
                        );
                    } else {
                        $this->Session->setFlash(
                            '<span></span>' . __('The meal hall assignment could not be saved. Please, try again.'),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __('Please select meal hall.'),
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
            $student_ids = $this->MealHallAssignment->get_student_have_mealhall($selected_academicyear);
            //Get accepted_student_id that have dormitory in there name
            $accepted_student_ids = $this->MealHallAssignment->get_accepted_student_have_mealhall(
                $selected_academicyear
            );
            //Get Meal halls unassigned students
            $unassigned_students = $this->_get_meal_hall_unassigned_students(
                $this->request->data['MealHallAssignment']['program_type_id'],
                $this->request->data['MealHallAssignment']['college_id'],
                $this->request->data['MealHallAssignment']['department_id'],
                $this->request->data['MealHallAssignment']['year_level_id'],
                $student_ids,
                $accepted_student_ids
            );

            $admitted_unassigned_students = $unassigned_students['student'];
            $non_admitted_unassigned_students = $unassigned_students['accepted_student'];
            $mealHalls = $this->MealHallAssignment->get_formatted_mealhall($selected_academicyear);

            $this->set(
                compact('beforesearch', 'admitted_unassigned_students', 'non_admitted_unassigned_students', 'mealHalls')
            );
        }

        if ($this->Session->read('selected_program_type')) {
            $selected_program_type = $this->Session->read('selected_program_type');
        } else {
            $selected_program_type = $this->request->data['MealHallAssignment']['program_type_id'];
        }
        if ($this->Session->read('selected_campus')) {
            $selected_campus = $this->Session->read('selected_campus');
        } else {
            $selected_campus = $this->request->data['MealHallAssignment']['campus_id'];
        }
        if ($this->Session->read('selected_college')) {
            $selected_college = $this->Session->read('selected_college');
        } else {
            $selected_college = $this->request->data['MealHallAssignment']['college_id'];
        }
        if ($this->Session->read('selected_department')) {
            $selected_department = $this->Session->read('selected_department');
        } else {
            $selected_department = $this->request->data['MealHallAssignment']['department_id'];
        }
        if ($this->Session->read('selected_year_level')) {
            $selected_year_level = $this->Session->read('selected_year_level');
        } else {
            $selected_year_level = $this->request->data['MealHallAssignment']['year_level_id'];
        }
        if ($this->Session->read('selected_academicyear')) {
            $selected_academicyear = $this->Session->read('selected_academicyear');
        } else {
            $selected_academicyear = $this->request->data['MealHallAssignment']['academic_year'];
        }

        if (!empty($selected_campus)) {
            $colleges = $this->_get_colleges_list($selected_campus);
        } else {
            $colleges = null;
        }
        if (!empty($selected_college)) {
            $departments = $this->_get_departments_list($selected_college);
        } else {
            $departments = null;
        }
        if (!empty($selected_department)) {
            $yearLevels = $this->_get_department_year_levels_list($selected_department);
        } else {
            if (!empty($selected_college)) {
                $yearLevels = $this->_get_year_levels_list($selected_college);
            } else {
                $yearLevels = null;
            }
        }

        $campuses = $this->MealHallAssignment->MealHall->Campus->find('list');
        $programs = $this->MealHallAssignment->Student->Program->find('list');
        $programTypes = $this->MealHallAssignment->AcceptedStudent->ProgramType->find('list');
        $this->set(
            compact(
                'campuses',
                'programs',
                'programTypes',
                'colleges',
                'departments',
                'yearLevels',
                'selected_program_type',
                'selected_campus',
                'selected_college',
                'selected_department',
                'selected_year_level',
                'selected_academicyear'
            )
        );
    }

    public function add_delete()
    {

        $from_delete = $this->Session->read('from_delete');
        $from_manually_add = $this->Session->read('from_manually_add');
        if ($from_delete != 1 && $from_manually_add != 1) {
            if ($this->Session->read('selected_program_type_id')) {
                $this->Session->delete('selected_program_type_id');
            }
            if ($this->Session->read('selected_campus')) {
                $this->Session->delete('selected_campus');
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
            if ($this->Session->read('selected_academicyear')) {
                $this->Session->delete('selected_academicyear');
            }
            if ($this->Session->read('selected_meal_hall_id')) {
                $this->Session->delete('selected_meal_hall_id');
            }
        }
        $colleges = $this->MealHallAssignment->Student->College->find('list');
        $programs = $this->MealHallAssignment->Student->Program->find('list');
        $programTypes = $this->MealHallAssignment->Student->ProgramType->find('list');
        $mealHalls = $this->MealHallAssignment->get_formatted_mealhall_for_view();

        $is_atleat_one_parameter_isset = false;
        if (!empty($this->request->data['MealHallAssignment']['program_type_id'])) {
            $selected_program_type_id = $this->request->data['MealHallAssignment']['program_type_id'];
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
        if (!empty($this->request->data['MealHallAssignment']['college_id'])) {
            $selected_college_id = $this->request->data['MealHallAssignment']['college_id'];
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
        if (!empty($this->request->data['MealHallAssignment']['department_id'])) {
            $selected_department_id = $this->request->data['MealHallAssignment']['department_id'];
            $this->Session->write('selected_department_id', $selected_department_id);
            $is_atleat_one_parameter_isset = true;
        } else {
            if ($this->Session->read('selected_department_id')) {
                $selected_department_id = $this->Session->read('selected_department_id');
                $is_atleat_one_parameter_isset = true;
            } else {
                $selected_department_id = null;
            }
        }
        if (!empty($this->request->data['MealHallAssignment']['year_level_id'])) {
            $selected_year_level_id = $this->request->data['MealHallAssignment']['year_level_id'];
            $this->Session->write('selected_year_level_id', $selected_year_level_id);
            $is_atleat_one_parameter_isset = true;
        } else {
            if ($this->Session->read('selected_year_level_id')) {
                $selected_year_level_id = $this->Session->read('selected_year_level_id');
                $is_atleat_one_parameter_isset = true;
            } else {
                $selected_year_level_id = null;
            }
        }
        if (!empty($this->request->data['MealHallAssignment']['academic_year'])) {
            $selected_academicyear = $this->request->data['MealHallAssignment']['academic_year'];
            $this->Session->write('selected_academicyear', $selected_academicyear);
        } else {
            if ($this->Session->read('selected_academicyear')) {
                $selected_academicyear = $this->Session->read('selected_academicyear');
            } else {
                $selected_academicyear = null;
            }
        }
        if (!empty($this->request->data['MealHallAssignment']['meal_hall_id'])) {
            $selected_meal_hall_id = $this->request->data['MealHallAssignment']['meal_hall_id'];
            $this->Session->write('selected_meal_hall_id', $selected_meal_hall_id);
        } else {
            if ($this->Session->read('selected_meal_hall_id')) {
                $selected_meal_hall_id = $this->Session->read('selected_meal_hall_id');
            } else {
                $selected_meal_hall_id = null;
            }
        }

        if (!empty($selected_college_id)) {
            $departments = $this->_get_departments_list($selected_college_id);
        } else {
            $departments = null;
        }
        if (!empty($selected_department_id)) {
            $yearLevels = $this->_get_department_year_levels_list($selected_department_id);
        } else {
            if (!empty($selected_college_id)) {
                $yearLevels = $this->_get_year_levels_list($selected_college_id);
            } else {
                $yearLevels = null;
            }
        }

        $mealHallAssignments = $this->_get_already_assigned_students(
            $selected_program_type_id,
            $selected_college_id,
            $selected_department_id,
            $selected_department_id,
            $selected_academicyear,
            $selected_meal_hall_id,
            $is_atleat_one_parameter_isset
        );

        //$this->set(compact('mealHallAssignments','colleges','programs','programTypes','departments', 'yearLevels','mealHalls'));


        $this->set(
            compact(
                'programs',
                'programTypes',
                'colleges',
                'mealHallAssignments',
                'departments',
                'yearLevels',
                'mealHalls',
                'selected_program_type_id',
                'selected_college_id',
                'selected_department_id',
                'selected_year_level_id',
                'selected_meal_hall_id',
                'selected_academicyear'
            )
        );

        if ($this->Session->read('from_delete')) {
            $this->Session->delete('from_delete');
        }
        if ($this->Session->read('from_manually_add')) {
            $this->Session->delete('from_manually_add');
        }
    }

    public function add_student_meal_hall($data = null)
    {

        $this->layout = 'ajax';
        $explode_data = explode('~', $data);
        $meal_hall_id = $explode_data[0];
        $selected_program_type_id = $explode_data[1];
        $selected_college_id = $explode_data[2];
        $selected_department_id = $explode_data[3];
        $selected_year_level_id = $explode_data[4];
        $selected_academicyear = $explode_data[5];
        $selected_academicyear = str_replace('-', '/', $selected_academicyear);

        //Get student_id that already assigned meal hall in the selected academic year
        $student_ids = $this->MealHallAssignment->get_student_have_mealhall($selected_academicyear);
        //Get accepted_student_id that already assigned meal hall in the selected academic year
        $accepted_student_ids = $this->MealHallAssignment->get_accepted_student_have_mealhall($selected_academicyear);

        //Get dormitories unassigned students
        $unassigned_students = null;
        $program_id = 1; //here only concidered undergraduate students
        //department unassign students
        if ($selected_department_id == 10000) {
            $unassigned_students = $this->MealHallAssignment->Student->getListOfDepartmentNonAssignedStudents(
                $selected_college_id,
                $program_id,
                $selected_program_type_id,
                null,
                $student_ids,
                $accepted_student_ids
            );
            //All department including department unassigned students
        } else {
            if (empty($selected_department_id)) {
                $unassigned_students_array_department_non_assigned = $this->MealHallAssignment->Student->getListOfDepartmentNonAssignedStudents(
                    $selected_college_id,
                    $program_id,
                    $selected_program_type_id,
                    null,
                    $student_ids,
                    $accepted_student_ids
                );

                $unassigned_students_array_department_assigned = $this->MealHallAssignment->Student->getListOfDepartmentStudentsByYearLevel(
                    $selected_college_id,
                    $selected_department_id,
                    $program_id,
                    $selected_program_type_id,
                    $selected_year_level_id,
                    1,
                    null,
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
                $unassigned_students = $this->MealHallAssignment->Student->getListOfDepartmentStudentsByYearLevel(
                    $selected_college_id,
                    $selected_department_id,
                    $program_id,
                    $selected_program_type_id,
                    $selected_year_level_id,
                    1,
                    null,
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

        $this->set(compact('unassigned_students_array', 'meal_hall_id', 'selected_academicyear'));
    }

    public function add_student_meal_hall_update()
    {

        if (!empty($this->request->data)) {
            $from_manually_add = 1;
            $this->Session->write('from_manually_add', $from_manually_add);
            $selected_id = $this->request->data['MealHallAssignment']['Selected_student_id'];
            $explode_selected_id = explode('~', $selected_id);
            $student_name = null;

            if (strcasecmp($explode_selected_id[1], "S") == 0) {
                $this->request->data['MealHallAssignments']['student_id'] = $explode_selected_id[0];
                $student_name = $this->MealHallAssignment->Student->field(
                    'Student.full_name',
                    array('Student.id' => $explode_selected_id[0])
                );
            } else {
                $this->request->data['MealHallAssignments']['accepted_student_id'] = $explode_selected_id[0];
                $student_name = $this->MealHallAssignment->AcceptedStudent->field(
                    'AcceptedStudent.full_name',
                    array('AcceptedStudent.id' => $explode_selected_id[0])
                );
            }
            $this->request->data['MealHallAssignments']['meal_hall_id'] = $this->request->data['MealHallAssignment']['meal_hall_id'];
            $this->request->data['MealHallAssignments']['academic_year'] = $this->request->data['MealHallAssignment']['selected_academicyear'];
            $meal_hall_name = $this->MealHallAssignment->MealHall->field(
                'MealHall.name',
                array('MealHall.id' => $this->request->data['MealHallAssignment']['meal_hall_id'])
            );
            $this->MealHallAssignment->create();
            if ($this->MealHallAssignment->save($this->request->data['MealHallAssignments'])) {
                $this->Session->setFlash(
                    '<span></span>' . __(' Student ' . $student_name . ' is manually assigned to ' . $meal_hall_name),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('action' => 'add_delete'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        ' Student ' . $student_name . ' could not have been assign to ' . $meal_hall_name . ' Please try again.'
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
                return $this->redirect(array('action' => 'add_delete'));
            }
        }
    }

    /*function edit($id = null) {
        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid meal hall assignment'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->MealHallAssignment->save($this->request->data)) {
                $this->Session->setFlash(__('The meal hall assignment has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The meal hall assignment could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->MealHallAssignment->read(null, $id);
        }
        $mealHalls = $this->MealHallAssignment->MealHall->find('list');
        $students = $this->MealHallAssignment->Student->find('list');
        $acceptedStudents = $this->MealHallAssignment->AcceptedStudent->find('list');
        $this->set(compact('mealHalls', 'students', 'acceptedStudents'));
    }*/

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span>' . __('Invalid id for meal hall assignment'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'add_delete'));
        }
        $from_delete = 1;
        $this->Session->write('from_delete', $from_delete);
        $student_id = $this->MealHallAssignment->field(
            'MealHallAssignment.student_id',
            array('MealHallAssignment.id' => $id)
        );
        $accepted_student_id = $this->MealHallAssignment->field(
            'MealHallAssignment.accepted_student_id',
            array('MealHallAssignment.id' => $id)
        );
        $student_name = null;
        if (!empty($student_id)) {
            $student_name = $this->MealHallAssignment->Student->field(
                'Student.full_name',
                array('Student.id' => $student_id)
            );
        } else {
            if (!empty($accepted_student_id)) {
                $student_name = $this->MealHallAssignment->AcceptedStudent->field(
                    'AcceptedStudent.full_name',
                    array('AcceptedStudent.id' => $accepted_student_id)
                );
            }
        }

        $meal_hall_id = $this->MealHallAssignment->field(
            'MealHallAssignment.meal_hall_id',
            array('MealHallAssignment.id' => $id)
        );
        $meal_hall_name = $this->MealHallAssignment->MealHall->field(
            'MealHall.name',
            array('MealHall.id' => $meal_hall_id)
        );

        if ($this->MealHallAssignment->delete($id)) {
            $this->Session->setFlash(
                '<span></span>' . __('Student ' . $student_name . ' deleted from ' . $meal_hall_name . ' Meal hall.'),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'add_delete'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Meal hall assignment was not deleted. Please try again.'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'add_delete'));
    }

    public function get_colleges($campus_id = null)
    {

        if (!empty($campus_id)) {
            $this->layout = 'ajax';
            $colleges = $this->_get_colleges_list($campus_id);
            $this->set(compact('colleges'));
        }
    }

    public function get_departments($college_id = null)
    {

        if (!empty($college_id)) {
            $this->layout = 'ajax';
            $departments = $this->_get_departments_list($college_id);
            $this->set(compact('departments'));
        }
    }

    public function get_year_levels($college_id = null)
    {

        if (!empty($college_id)) {
            $this->layout = 'ajax';
            $yearLevels = $this->_get_year_levels_list($college_id);
            $this->set(compact('yearLevels'));
        }
    }

    public function get_department_year_levels($data = null)
    {

        if (!empty($data)) {
            $this->layout = 'ajax';
            $explode_data = explode('~', $data);
            $department_id = $explode_data[0];
            $college_id = $explode_data[1];

            if (!empty($department_id)) {
                $yearLevels = $this->_get_department_year_levels_list($department_id);
            } else {
                $yearLevels = $this->MealHallAssignment->get_maximum_year_levels_of_college($college_id);
            }
            $this->set(compact('yearLevels'));
        }
    }

    public function _get_colleges_list($campus_id)
    {

        $colleges = $this->MealHallAssignment->Student->College->find(
            'list',
            array('conditions' => array('College.campus_id' => $campus_id))
        );

        return $colleges;
    }

    public function _get_departments_list($college_id = null)
    {

        $departments = $this->MealHallAssignment->Student->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $college_id))
        );
        $departments['10000'] = 'Pre/(Unassign Freshman)';

        return $departments;
    }

    public function _get_year_levels_list($college_id = null)
    {

        $yearLevels = $this->MealHallAssignment->get_maximum_year_levels_of_college($college_id);
        return $yearLevels;
    }

    public function _get_department_year_levels_list($department_id = null)
    {

        if ($department_id == 10000) {
            $yearLevels[10000] = '1st';
        } else {
            $yearLevels = ClassRegistry::init('YearLevel')->find(
                'list',
                array(
                    'conditions' => array('YearLevel.department_id' => $department_id),
                    'fields' => array('name', 'name')
                )
            );
            //$yearLevels = $this->MealHallAssignment->Student->Department->YearLevel->find('list',array('conditions'=>array('YearLevel.department_id'=>$deparment_id),'fields'=>array('name','name')));
        }
        return $yearLevels;
    }

    public function _get_meal_hall_unassigned_students(
        $selected_program_type = null,
        $selected_college = null,
        $selected_department = null,
        $selected_year_level = null,
        $student_ids = null,
        $accepted_student_ids = null
    ) {

        $unassigned_students = null;

        //assign program_id 1 fro undergraduate students
        $program_id = 1;
        //department unassign students
        if ($selected_department == 10000) {
            $unassigned_students = $this->MealHallAssignment->Student->getListOfDepartmentNonAssignedStudents(
                $selected_college,
                $program_id,
                $selected_program_type,
                null,
                $student_ids,
                $accepted_student_ids
            );
            //All department including department unassigned students
        } else {
            if (empty($selected_department)) {
                $students_array_department_non_assigned = $this->MealHallAssignment->Student->getListOfDepartmentNonAssignedStudents(
                    $selected_college,
                    $program_id,
                    $selected_program_type,
                    null,
                    $student_ids,
                    $accepted_student_ids
                );

                $students_array_department_assigned = $this->MealHallAssignment->Student->getListOfDepartmentStudentsByYearLevel(
                    $selected_college,
                    $selected_department,
                    $program_id,
                    $selected_program_type,
                    $selected_year_level,
                    1,
                    null,
                    $student_ids,
                    $accepted_student_ids
                );

                //Merge both

                $unassigned_students = array_merge(
                    $students_array_department_non_assigned,
                    $students_array_department_assigned
                );
                //Only department assign students
            } else {
                $unassigned_students = $this->MealHallAssignment->Student->getListOfDepartmentStudentsByYearLevel(
                    $selected_college,
                    $selected_department,
                    $program_id,
                    $selected_program_type,
                    $selected_year_level,
                    1,
                    null,
                    $student_ids,
                    $accepted_student_ids
                );
                //debug($unassigned_students);

            }
        }
        //debug($unassigned_students);
        return $unassigned_students;
    }

    public function _get_already_assigned_students(
        $program_type_id = null,
        $college_id = null,
        $department_id = null,
        $year_level_id = null,
        $academic_year = null,
        $meal_hall_id = null,
        $is_atleat_one_parameter_isset = null
    ) {

        $options = array();
        if ($is_atleat_one_parameter_isset == true) {
            $students_array = null;
            $program_id = 1;
            //department unassign students
            if ($department_id == 10000) {
                $students_array = $this->MealHallAssignment->Student->getListOfDepartmentNonAssignedStudents(
                    $college_id,
                    $program_id,
                    $program_type_id,
                    null,
                    null,
                    null
                );
                //All department including department unassigned students
            } else {
                if (empty($department_id)) {
                    $students_array_department_non_assigned = $this->MealHallAssignment->Student->getListOfDepartmentNonAssignedStudents(
                        $college_id,
                        $program_id,
                        $program_type_id,
                        null,
                        null,
                        null
                    );

                    $students_array_department_assigned = $this->MealHallAssignment->Student->getListOfDepartmentStudentsByYearLevel(
                        $college_id,
                        $department_id,
                        $program_id,
                        $program_type_id,
                        $year_level_id,
                        0,
                        null,
                        null,
                        null
                    );
                    //Merge both
                    $students_array = array_merge(
                        $students_array_department_non_assigned,
                        $students_array_department_assigned
                    );
                    //Only department assign students
                } else {
                    $students_array = $this->MealHallAssignment->Student->getListOfDepartmentStudentsByYearLevel(
                        $college_id,
                        $department_id,
                        $program_id,
                        $program_type_id,
                        $year_level_id,
                        0,
                        null,
                        null,
                        null
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
                        'MealHallAssignment.accepted_student_id' => $accepted_student_ids,
                        'MealHallAssignment.student_id' => $student_ids
                    )
                );
            } else {
                if (!empty($student_ids)) {
                    $options[] = array('MealHallAssignment.student_id' => $student_ids);
                } else {
                    if (!empty($accepted_student_ids)) {
                        $options[] = array('MealHallAssignment.accepted_student_id' => $accepted_student_ids);
                    } else {
                        $options[] = array(
                            'MealHallAssignment.accepted_student_id' => $accepted_student_ids,
                            'MealHallAssignment.student_id' => $student_ids
                        );
                    }
                }
            }
        }

        if (!empty($meal_hall_id)) {
            $options[] = array('MealHallAssignment.meal_hall_id' => $meal_hall_id);
        }

        if (!empty($academic_year)) {
            $options[] = array('MealHallAssignment.academic_year' => $academic_year);
        }

        $this->paginate = array(
            'conditions' => $options,
            'contain' => array(
                'MealHall' => array(
                    'fields' => array('MealHall.id', 'MealHall.name'),
                    'Campus' => array('fields' => array('Campus.name'))
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
        return $this->paginate();
    }
}

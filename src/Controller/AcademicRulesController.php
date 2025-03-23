<?php

namespace App\Controller;

use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class AcademicRulesController extends AppController
{

    public $name = 'AcademicRules';
    public $menuOptions = array(
        'parent' => 'dashboard',
        'exclude' => array(
            'index',
            'delete_other_ar',
            'edit_other_academic_rules'
        ),
        'alias' => array(
            'index' => 'View All Rules',
            'add' => 'Set Academic Rule',
            'add_other_academic_rules' => 'Add Other Academic Rules',
            'view_other_academic_rules' => 'View Other Academic Rules',

        )
    );

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow('search', 'view_other_academic_rules', 'add_other_academic_rules');
    }

    public function index()
    {

        $this->AcademicRule->recursive = 1;
        $this->set('academicRules', $this->paginate());
    }

    public function view_other_academic_rules()
    {

        ClassRegistry::init('OtherAcademicRule')->recursive = 1;
        $this->set('otherAcademicRules', ClassRegistry::init('OtherAcademicRule')->find('all'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error(__('Invalid academic rule'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('academicRule', $this->AcademicRule->read(null, $id));
    }

    public function add_other_academic_rules()
    {

        if (!empty($this->request->data)) {
            if (ClassRegistry::init('OtherAcademicRule')->check_duplicate_entry($this->request->data)) {
                ClassRegistry::init('OtherAcademicRule')->create();
                if (ClassRegistry::init('OtherAcademicRule')->save($this->request->data)) {
                    $this->Flash->success(__('The other academic rule has been saved.'));
                } else {
                    $this->Flash->error(__('The other academic rule could not be saved. Please, try again.'));
                }
            } else {
                $error = ClassRegistry::init('OtherAcademicRule')->invalidFields();
                if (isset($error['duplicate'])) {
                    $this->Flash->error(__($error['duplicate'][0]));
                }
            }
        }

        $programs = ClassRegistry::init('Program')->find('list');
        $program_types = ClassRegistry::init('ProgramType')->find('list');

        $academicStatuses = ClassRegistry::init('AcademicStatus')->find('list', array(
            'conditions' => array('AcademicStatus.computable' => 1),
            'order' => array('AcademicStatus.order' => 'DESC')
        ));

        if (!empty($this->department_ids) || !empty($this->college_ids)) {
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                1,
                $this->department_ids,
                $this->college_ids,
                1
            );
        } else {
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                1,
                $this->department_id,
                $this->college_id,
                1
            );
        }

        $yearLevels = ClassRegistry::init('YearLevel')->distinct_year_level();
        $programs = array(0 => 'All Programs') + $programs;
        $program_types = array(0 => 'All Program Types') + $program_types;

        if ($this->role_id == ROLE_DEPARTMENT) {
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(0, $this->department_id, array()
            );
        } else {
            if ($this->role_id == ROLE_COLLEGE) {
                $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                    1,
                    array(),
                    $this->college_id,
                    1
                );
            } else {
                $departments = array(0 => 'All Department') + $departments;
            }
        }

        $yearLevels = array(0 => 'All Year Level') + $yearLevels;

        $default_department_id = null;
        $default_program_id = null;
        $default_program_type_id = null;
        $default_year_level_id = null;
        $curriculums = null;

        $gradess = ClassRegistry::init('Grade')->find(
            'all',
            array('fields' => array('DISTINCT Grade.grade'), 'recursive' => -1)
        );
        $gradesDistinct = array();

        if (!empty($gradess)) {
            foreach ($gradess as $key => $value) {
                $gradesDistinct[$value['Grade']['grade']] = $value['Grade']['grade'];
            }
        }

        $grades = $gradesDistinct;

        $this->set(
            compact(
                'curriculums',
                'grades',
                'academicStand',
                'yearLevels',
                'departments',
                'programs',
                'program_types',
                'default_department_id',
                'default_program_id',
                'default_program_type_id',
                'academicStatuses',
                'default_year_level_id'
            )
        );
    }

    public function edit_other_academic_rules($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__('Invalid academic rule'));
            return $this->redirect(array('action' => 'view_other_academic_rules'));
        }

        if (!empty($this->request->data)) {
            if (ClassRegistry::init('OtherAcademicRule')->save($this->request->data)) {
                $this->Flash->success(__('The other academic rule has been saved.'));
            } else {
                $this->Flash->error(__('The other academic rule could not be saved. Please, try again.'));
            }
        }

        $programs = ClassRegistry::init('Program')->find('list');
        $program_types = ClassRegistry::init('ProgramType')->find('list');

        $academicStatuses = ClassRegistry::init('AcademicStatus')->find('list', array(
            'conditions' => array('AcademicStatus.computable' => 1),
            'order' => array('AcademicStatus.order' => 'DESC')
        ));

        if (!empty($this->department_ids) || !empty($this->college_ids)) {
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                1,
                $this->department_ids,
                $this->college_ids,
                1
            );
        } else {
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                1,
                $this->department_id,
                $this->college_id,
                1
            );
        }

        $yearLevels = ClassRegistry::init('YearLevel')->distinct_year_level();
        $programs = array(0 => 'All Programs') + $programs;
        $program_types = array(0 => 'All Program Types') + $program_types;

        if ($this->role_id == ROLE_DEPARTMENT) {
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(0, $this->department_id, array()
            );
        } else {
            if ($this->role_id == ROLE_COLLEGE) {
                $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                    1,
                    array(),
                    $this->college_id,
                    1
                );
            } else {
                $departments = array(0 => 'All Department') + $departments;
            }
        }

        $yearLevels = array(0 => 'All Year Level') + $yearLevels;

        $default_department_id = null;
        $default_program_id = null;
        $default_program_type_id = null;
        $default_year_level_id = null;
        $curriculums = null;

        $gradess = ClassRegistry::init('Grade')->find(
            'all',
            array('fields' => array('DISTINCT Grade.grade'), 'recursive' => -1)
        );
        $gradesDistinct = array();

        if (!empty($gradess)) {
            foreach ($gradess as $key => $value) {
                $gradesDistinct[$value['Grade']['grade']] = $value['Grade']['grade'];
            }
        }

        $grades = $gradesDistinct;

        if (empty($this->request->data)) {
            $this->request->data = ClassRegistry::init('OtherAcademicRule')->read(null, $id);
            if (isset($this->request->data['OtherAcademicRule']['department_id']) && !empty($this->request->data['OtherAcademicRule']['department_id'])) {
                $curriculums = ClassRegistry::init('Curriculum')->find(
                    'list',
                    array('conditions' => array('Curriculum.department_id' => $this->request->data['OtherAcademicRule']['department_id']))
                );
                $this->set(compact('curriculums'));
            }
            if (isset($this->request->data['OtherAcademicRule']['curriculum_id']) && !empty($this->request->data['OtherAcademicRule']['curriculum_id'])) {
                $courseCategories = ClassRegistry::init('CourseCategory')->find(
                    'list',
                    array('conditions' => array('CourseCategory.curriculum_id' => $this->request->data['OtherAcademicRule']['curriculum_id']))
                );
                $this->set(compact('courseCategories'));
            }
        }

        $this->set(
            compact(
                'curriculums',
                'grades',
                'academicStand',
                'yearLevels',
                'departments',
                'programs',
                'program_types',
                'default_department_id',
                'default_program_id',
                'default_program_type_id',
                'academicStatuses',
                'default_year_level_id'
            )
        );
    }

    public function add($id = null)
    {

        debug($this->AcademicRule->checkExeclusiveNessOFGradeRule('2011/12'));

        if (!empty($this->request->data)) {
            $this->AcademicRule->create();
            if (!empty($this->request->data['AcademicStand'])) {
                $reformatData = $this->request->data['AcademicStand']['approve'];
                unset($this->request->data['AcademicStand']['approve']);
                if (!empty($reformatData)) {
                    $count = 0;
                    foreach ($reformatData as $id => $id_value) {
                        if ($id_value) {
                            $this->request->data['AcademicStand']['AcademicStand'][$count]['academic_stand_id'] = $id;
                            $this->request->data['AcademicStand']['AcademicStand'][$count]['delete'] = 1;
                            $count++;
                        }
                    }
                }
                //save it
                //bind to hasMany to save extra field
                $this->AcademicRule->bindModel(array('hasMany' => array('AcademicStandsAcademicRule')));
                debug($this->request->data);
                if ($this->AcademicRule->saveAll($this->request->data)) {
                    $this->Flash->success(__('The academic rule has been saved'));
                    // $this->redirect(array('action' => 'index'));
                } else {
                    $this->Flash->error(__('The academic rule could not be saved. Please, try again.'));
                }
            } else {
                $this->Flash->error(__('Please select atleast one academic stands to map'));
            }
        }

        if ($id) {
            $academicStands = $this->AcademicRule->AcademicStand->find(
                'list',
                array('conditions' => array('AcademicStand.id' => $id))
            );
        } else {
            $academicStands = $this->AcademicRule->AcademicStand->find('list');
        }

        if (empty($academicStands)) {
            $this->Flash->info('<span></span>' . __('Please set academic stands before defining academic rule'));
            return $this->redirect(array('controller' => 'academicStands', 'action' => 'add'));
        }

        $academicStandsDetail = $this->AcademicRule->AcademicStand->find('all');
        $this->set(compact('academicStands', 'academicStandsDetail'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__('Invalid academic rule'));
            return $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data)) {
            if ($this->AcademicRule->save($this->request->data)) {
                $this->Flash->success(__('The academic rule has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('The academic rule could not be saved. Please, try again.'));
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->AcademicRule->read(null, $id);
        }

        $academicStands = $this->AcademicRule->AcademicStand->find('list');
        $this->set(compact('academicStands'));
    }

    public function delete($id = null, $action_model_id = null)
    {

        if (!$id) {
            $this->Flash->error(__('Invalid id for academic rule'));
            return $this->redirect(array('action' => 'index'));
        }

        if (!empty($action_model_id)) {
            $academic_rule = explode('~', $action_model_id);

            if ($this->AcademicRule->delete($id)) {
                $this->Flash->success(__('Academic rule deleted.'));
            } else {
                $this->Flash->error(__(' Could not be deleted'));
            }
            $this->redirect(array('controller' => $academic_rule[1], 'action' => $academic_rule[0], $academic_rule[2]));
        }

        if ($this->AcademicRule->delete($id)) {
            $this->Flash->success(__('Academic rule deleted'));
            return $this->redirect(array('action' => 'index'));
        }

        $this->Flash->error(__('Academic rule was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }

    public function delete_other_ar($id = null)
    {

        if (!$id) {
            $this->Flash->error(__('Invalid id for academic rule'));
            return $this->redirect(array('action' => 'view_other_academic_rules'));
        }

        if (ClassRegistry::init('OtherAcademicRule')->delete($id)) {
            $this->Flash->success(__('Academic rule deleted.'));
            return $this->redirect(array('action' => 'view_other_academic_rules'));
        }

        $this->Flash->error(__('Academic rule was not deleted.'));
        return $this->redirect(array('action' => 'view_other_academic_rules'));
    }
}

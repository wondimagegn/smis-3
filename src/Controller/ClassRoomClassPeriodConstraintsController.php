<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ClassRoomClassPeriodConstraintsController extends AppController
{

    public $name = 'ClassRoomClassPeriodConstraints';
    public $components = array('AcademicYear');

    public $menuOptions = array(
        'exclude' => array('*'),
        'controllerButton' => false,
    );

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow('get_class_rooms', 'get_periods', 'get_already_recorded_data', '_get_unrecorded_periods');
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

        //$this->ClassRoomClassPeriodConstraint->recursive = 0;
        $classPeriods_id_array = $this->ClassRoomClassPeriodConstraint->ClassPeriod->find(
            'list',
            array(
                'fields' => array('ClassPeriod.id'),
                'conditions' => array('ClassPeriod.college_id' => $this->college_id)
            )
        );
        $this->paginate = array(
            'conditions' => array('ClassRoomClassPeriodConstraint.class_period_id' => $classPeriods_id_array),
            'contain' => array(
                'ClassRoom' => array(
                    'fields' => array('ClassRoom.room_code', 'ClassRoom.id'),
                    'ClassRoomBlock' => array(
                        'fields' => array('ClassRoomBlock.block_code'),
                        'Campus' => array('fields' => array('Campus.name'))
                    )
                ),
                'ClassPeriod' => array(
                    'fields' => array('ClassPeriod.week_day'),
                    'PeriodSetting' => array(
                        'fields' => array(
                            'PeriodSetting.id',
                            'PeriodSetting.period',
                            'PeriodSetting.hour'
                        )
                    )
                )
            )
        );

        $this->set('classRoomClassPeriodConstraints', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid class room class period constraint'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('classRoomClassPeriodConstraint', $this->ClassRoomClassPeriodConstraint->read(null, $id));
    }

    public function add()
    {

        $from_delete = $this->Session->read('from_delete');
        if ($from_delete != 1) {
            if ($this->Session->read('selected_academicyear')) {
                $this->Session->delete('selected_academicyear');
            }
            if ($this->Session->read('selected_semester')) {
                $this->Session->delete('selected_semester');
            }
            if ($this->Session->read('selected_class_room_block')) {
                $this->Session->delete('selected_class_room_block');
            }
            if ($this->Session->read('classRooms')) {
                $this->Session->delete('classRooms');
            }
            if ($this->Session->read('selected_week_day')) {
                $this->Session->delete('selected_week_day');
            }
            if ($this->Session->read('selected_program')) {
                $this->Session->delete('selected_program');
            }
            if ($this->Session->read('selected_program_type')) {
                $this->Session->delete('selected_program_type');
            }
        }
        if (!empty($this->request->data)) {
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['ClassRoomClassPeriodConstraint']['academicyear']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select academic year that you want to add class room class period constraints.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ClassRoomClassPeriodConstraint']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select semester that you want to add class room class period constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ClassRoomClassPeriodConstraint']['program_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the program that you want to add class room class period constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ClassRoomClassPeriodConstraint']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the program type that you want to add class room class period constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ClassRoomClassPeriodConstraint']['class_room_blocks']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select class room block that you want to add class room class period constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ClassRoomClassPeriodConstraint']['class_room_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select class room that you want to add class room class period constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ClassRoomClassPeriodConstraint']['week_day']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select week day that you want to add class room class period constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                $selected_class_period_id_array = array();
                if (!empty($this->request->data['ClassRoomClassPeriodConstraint']['Selected'])) {
                    foreach ($this->request->data['ClassRoomClassPeriodConstraint']['Selected'] as $crcpcsk => $crcpcsv) {
                        if ($crcpcsv != 0) {
                            $selected_class_period_id_array[] = $crcpcsv;
                        }
                    }
                    if (count($selected_class_period_id_array) != 0) {
                        $this->request->data['ClassRoomClassPeriodConstraints']['academic_year'] =
                            $this->request->data['ClassRoomClassPeriodConstraint']['academicyear'];
                        $this->request->data['ClassRoomClassPeriodConstraints']['semester'] =
                            $this->request->data['ClassRoomClassPeriodConstraint']['semester'];
                        $this->request->data['ClassRoomClassPeriodConstraints']['class_room_id'] =
                            $this->request->data['ClassRoomClassPeriodConstraint']['class_room_id'];
                        $this->request->data['ClassRoomClassPeriodConstraints']['active'] =
                            $this->request->data['ClassRoomClassPeriodConstraint']['active'];
                        foreach ($selected_class_period_id_array as $class_period_id) {
                            $this->request->data['ClassRoomClassPeriodConstraints']['class_period_id'] = $class_period_id;
                            $this->ClassRoomClassPeriodConstraint->create();
                            $this->ClassRoomClassPeriodConstraint->save(
                                $this->request->data['ClassRoomClassPeriodConstraints']
                            );
                        }
                        $this->Session->setFlash(
                            '<span></span>' . __('The class room class period constraint has been saved'),
                            'default',
                            array('class' => 'success-box success-message')
                        );
                    } else {
                        $this->Session->setFlash(
                            '<span></span>' . __(' Please select at least 1 period'),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                } else {
                    $this->Session->setFlash(
                        '<span></span> Please set class period first in ',
                        "session_flash_link",
                        array(
                            "class" => 'info-box info-message',
                            "link_text" => " this page",
                            "link_url" => array(
                                "controller" => "classPeriods",
                                "action" => "add",
                                "admin" => false
                            )
                        )
                    );
                }

                $selected_academicyear = $this->request->data['ClassRoomClassPeriodConstraint']['academicyear'];
                $selected_semester = $this->request->data['ClassRoomClassPeriodConstraint']['semester'];
                $selected_program = $this->request->data['ClassRoomClassPeriodConstraint']['program_id'];
                $this->Session->write('selected_program', $selected_program);
                $selected_program_type = $this->request->data['ClassRoomClassPeriodConstraint']['program_type_id'];
                $this->Session->write('selected_program_type', $selected_program_type);
                $selected_class_room_block = $this->request->data['ClassRoomClassPeriodConstraint']['class_room_blocks'];
                $classRooms = $this->ClassRoomClassPeriodConstraint->ClassRoom->find(
                    'list',
                    array(
                        'fields' => array('ClassRoom.room_code'),
                        'conditions' => array(
                            'ClassRoom.class_room_block_id' => $selected_class_room_block,
                            'ClassRoom.available_for_lecture' => 1
                        )
                    )
                );
                $selected_class_rooms = $this->request->data['ClassRoomClassPeriodConstraint']['class_room_id'];
                $selected_week_day = $this->request->data['ClassRoomClassPeriodConstraint']['week_day'];

                $this->Session->write('selected_week_day', $selected_week_day);
                $this->Session->write('classRooms', $classRooms);
                $this->Session->write('selected_academicyear', $selected_academicyear);
                $this->Session->write('selected_semester', $selected_semester);
                $this->Session->write('selected_class_room_block', $selected_class_room_block);
                $this->Session->write('selected_class_rooms', $selected_class_rooms);

                $fromadd_already_recorded_class_room_class_period_constraints = $this->_get_class_room_class_period_constraints_data(
                    $this->request->data['ClassRoomClassPeriodConstraint']['class_room_id'],
                    $this->request->data['ClassRoomClassPeriodConstraint']['academicyear'],
                    $selected_semester = $this->request->data['ClassRoomClassPeriodConstraint']['semester']
                );
                $fromadd_periods = $this->_get_unrecorded_periods(
                    $selected_week_day,
                    $selected_program,
                    $selected_program_type
                );

                $this->set(
                    compact(
                        'fromadd_already_recorded_class_room_class_period_constraints',
                        'fromadd_periods',
                        'selected_class_rooms'
                    )
                );
            }
        }
        if ($this->Session->read('selected_week_day')) {
            $selected_week_day = $this->Session->read('selected_week_day');
        } else {
            //$selected_week_day = $this->request->data['ClassRoomClassPeriodConstraint']['week_day'];
        }
        if ($this->Session->read('selected_academicyear')) {
            $selected_academicyear = $this->Session->read('selected_academicyear');
        } else {
            $selected_academicyear = $this->request->data['ClassRoomClassPeriodConstraint']['academicyear'];
        }
        if ($this->Session->read('selected_semester')) {
            $selected_semester = $this->Session->read('selected_semester');
        } else {
            $selected_semester = $this->request->data['ClassRoomClassPeriodConstraint']['semester'];
        }
        if ($this->Session->read('selected_program')) {
            $selected_program = $this->Session->read('selected_program');
        } else {
            $selected_program = $this->request->data['ClassRoomClassPeriodConstraint']['program_id'];
        }
        if ($this->Session->read('selected_program_type')) {
            $selected_program_type = $this->Session->read('selected_program_type');
        } else {
            $selected_program_type = $this->request->data['ClassRoomClassPeriodConstraint']['program_type_id'];
        }
        if ($this->Session->read('selected_class_room_block')) {
            $selected_class_room_block = $this->Session->read('selected_class_room_block');
        } else {
            $selected_class_room_block = $this->request->data['ClassRoomClassPeriodConstraint']['class_room_blocks'];
        }
        if ($this->Session->read('selected_class_rooms')) {
            $selected_class_rooms = $this->Session->read('selected_class_rooms');
        } else {
            //$selected_class_rooms = $this->request->data['ClassRoomClassPeriodConstraint']['class_room_id'];
        }
        $classRoomBlocks = $this->ClassRoomClassPeriodConstraint->ClassRoom->ClassRoomBlock->find(
            'all',
            array(
                'fields' => array('ClassRoomBlock.id', 'ClassRoomBlock.block_code'),
                'conditions' => array('ClassRoomBlock.college_id' => $this->college_id),
                'contain' => array('Campus' => array('fields' => array('Campus.name')))
            )
        );
        //debug($classRoomBlocks);
        //format class room blocks by campus
        $formatted_class_room_blocks = array();
        if (!empty($classRoomBlocks)) {
            foreach ($classRoomBlocks as $classRoomBlock) {
                $formatted_class_room_blocks[$classRoomBlock['Campus']['name']][$classRoomBlock['ClassRoomBlock']['id']] = $classRoomBlock['ClassRoomBlock']['block_code'];
            }
        }
        //debug($formatted_class_room_blocks);
        if ($this->Session->read('classRooms')) {
            $classRooms = $this->Session->read('classRooms');
        } else {
            if (!empty($selected_class_room_block)) {
                $classRooms = $this->ClassRoomClassPeriodConstraint->ClassRoom->find(
                    'list',
                    array(
                        'fields' => array('ClassRoom.room_code'),
                        'conditions' => array(
                            'ClassRoom.class_room_block_id' => $selected_class_room_block,
                            'ClassRoom.available_for_lecture' => 1
                        )
                    )
                );
            } else {
                $classRooms = null;
            }
        }
        $week_days = $this->ClassRoomClassPeriodConstraint->ClassPeriod->find('all', array(
            'fields' => array(
                'DISTINCT ClassPeriod.week_day'
            ),
            'conditions' => array('ClassPeriod.college_id' => $this->college_id)
        ));
        $programs = $this->ClassRoomClassPeriodConstraint->ClassPeriod->Program->find('list');
        $programTypes = $this->ClassRoomClassPeriodConstraint->ClassPeriod->ProgramType->find('list');
        $classPeriods = $this->ClassRoomClassPeriodConstraint->ClassPeriod->find('list');
        $this->set(
            compact(
                'classPeriods',
                'formatted_class_room_blocks',
                'week_days',
                'classRooms',
                'selected_academicyear',
                'selected_semester',
                'selected_class_room_block',
                'selected_week_day',
                'programs',
                'programTypes',
                'selected_program',
                'selected_program_type'
            )
        );
        //If it come from delete
        if ($this->Session->read('from_delete')) {
            $selected_week_day = $this->Session->read('selected_week_day');
            $fromadd_already_recorded_class_room_class_period_constraints = $this->_get_class_room_class_period_constraints_data(
                $selected_class_rooms,
                $selected_academicyear,
                $selected_semester
            );
            $fromadd_periods = $this->_get_unrecorded_periods(
                $selected_week_day,
                $selected_program,
                $selected_program_type
            );

            $classRooms = $this->ClassRoomClassPeriodConstraint->ClassRoom->find(
                'list',
                array(
                    'fields' => array('ClassRoom.room_code'),
                    'conditions' => array(
                        'ClassRoom.class_room_block_id' => $selected_class_room_block,
                        'ClassRoom.available_for_lecture' => 1
                    )
                )
            );
            $this->set(
                compact(
                    'selected_week_day',
                    'classRooms',
                    'selected_class_rooms',
                    'fromadd_already_recorded_class_room_class_period_constraints',
                    'fromadd_periods'
                )
            );
            $this->Session->delete('from_delete');
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid class room class period constraint'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->ClassRoomClassPeriodConstraint->save($this->request->data)) {
                $this->Session->setFlash(__('The class room class period constraint has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('The class room class period constraint could not be saved. Please, try again.')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->ClassRoomClassPeriodConstraint->read(null, $id);
        }
        $classRooms = $this->ClassRoomClassPeriodConstraint->ClassRoom->find('list');
        $classPeriods = $this->ClassRoomClassPeriodConstraint->ClassPeriod->find('list');
        $this->set(compact('classRooms', 'classPeriods'));
    }

    public function delete($id = null, $from = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span> ' . __('Invalid id for class room class period constraint.'),
                'default',
                array('class' => 'error-box error-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        }
        $from_delete = 1;
        $this->Session->write('from_delete', $from_delete);
        $areyou_eligible_to_delete = $this->ClassRoomClassPeriodConstraint->beforeDeleteCheckEligibility(
            $id,
            $this->college_id
        );
        if ($areyou_eligible_to_delete == true) {
            $deleted_constraint_data = $this->ClassRoomClassPeriodConstraint->find(
                'first',
                array('conditions' => array('ClassRoomClassPeriodConstraint.id' => $id))
            );

            $this->Session->write(
                'selected_academicyear',
                $deleted_constraint_data['ClassRoomClassPeriodConstraint']['academic_year']
            );
            $this->Session->write(
                'selected_semester',
                $deleted_constraint_data['ClassRoomClassPeriodConstraint']['semester']
            );
            $this->Session->write(
                'selected_class_rooms',
                $deleted_constraint_data['ClassRoomClassPeriodConstraint']['class_room_id']
            );
            $this->Session->write('selected_week_day', $deleted_constraint_data['ClassPeriod']['week_day']);
            $this->Session->write(
                'selected_class_room_block',
                $deleted_constraint_data['ClassRoom']['class_room_block_id']
            );
            if ($this->ClassRoomClassPeriodConstraint->delete($id)) {
                $this->Session->setFlash(
                    '<span></span> ' . __('Class room class period constraint deleted'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                if (empty($from)) {
                    return $this->redirect(array('action' => 'index'));
                } else {
                    return $this->redirect(array('action' => 'add'));
                }
            }
            $this->Session->setFlash(
                '<span></span> ' . __('Class room class period constraint was not deleted.'),
                'default',
                array('class' => 'error-box error-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        } else {
            $this->Session->setFlash(
                '<span></span> ' . __('You are not eligible to delete this class room class period constraint.'),
                'default',
                array('class' => 'error-box error-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        }
    }

    public function getClassRooms($class_room_block_id = null)
    {

        if (!empty($class_room_block_id)) {
            $this->layout = 'ajax';
            $classRooms = $this->ClassRoomClassPeriodConstraint->ClassRoom->find(
                'list',
                array(
                    'fields' => array('ClassRoom.room_code'),
                    'conditions' => array(
                        'ClassRoom.class_room_block_id' => $class_room_block_id,
                        "OR" => array(
                            'ClassRoom.available_for_lecture' => 1,
                            "AND" => array('ClassRoom.available_for_lecture' => 0, 'ClassRoom.available_for_exam' => 0)
                        )
                    )
                )
            );
            $this->set(compact('classRooms'));
        }
    }

    public function getPeriods($data = null)
    {

        $explode_data = explode('~', $data);
        $week_id = $explode_data[0];
        $program_id = $explode_data[1];
        $program_type_id = $explode_data[2];
        if (!empty($week_id) && !empty($program_id) && !empty($program_type_id)) {
            $this->layout = 'ajax';
            $periods = $this->_get_unrecorded_periods($week_id, $program_id, $program_type_id);
            $this->set(compact('periods'));
        }
    }

    public function getAlreadyRecordedData($data = null)
    {

        if ($this->Session->read('class_room_id')) {
            $this->Session->delete('class_room_id');
        }
        if ($this->Session->read('academicyear')) {
            $this->Session->delete('academicyear');
        }
        if ($this->Session->read('semester')) {
            $this->Session->delete('semester');
        }
        $this->layout = 'ajax';
        $explode_data = explode("~", $data);
        $class_room_id = $explode_data[0];
        $academicyear = $explode_data[1];
        $academicyear = str_replace('-', '/', $academicyear);
        $semester = $explode_data[2];

        $this->Session->write('class_room_id', $class_room_id);
        $this->Session->write('academicyear', $academicyear);
        $this->Session->write('semester', $semester);

        if (!empty($class_room_id) && !empty($academicyear) && !empty($semester)) {
            $already_recorded_class_room_class_period_constraints = $this->_get_class_room_class_period_constraints_data(
                $class_room_id,
                $academicyear,
                $semester
            );

            $this->set(compact('already_recorded_class_room_class_period_constraints'));
        }
    }

    public function _get_class_room_class_period_constraints_data(
        $class_room_id = null,
        $academicyear = null,
        $semester = null
    ) {

        $already_recorded_class_room_class_period_constraints = $this->ClassRoomClassPeriodConstraint->find(
            'all',
            array(
                'conditions' => array(
                    'ClassRoomClassPeriodConstraint.class_room_id' => $class_room_id,
                    'ClassRoomClassPeriodConstraint.academic_year' => $academicyear,
                    'ClassRoomClassPeriodConstraint.semester' => $semester
                ),
                'contain' => array(
                    'ClassRoom' => array(
                        'fields' => array('ClassRoom.room_code'),
                        'ClassRoomBlock' => array(
                            'fields' => array('ClassRoomBlock.block_code'),
                            'Campus' => array('fields' => array('Campus.name'))
                        )
                    ),
                    'ClassPeriod' => array(
                        'fields' => array('ClassPeriod.week_day'),
                        'PeriodSetting' => array(
                            'fields' => array(
                                'PeriodSetting.id',
                                'PeriodSetting.period',
                                'PeriodSetting.hour'
                            )
                        )
                    )
                )
            )
        );

        return $already_recorded_class_room_class_period_constraints;
    }

    public function _get_unrecorded_periods($week_id = null, $program_id = null, $program_type_id = null)
    {

        if (!empty($week_id) && !empty($program_id) && !empty($program_type_id)) {
            $class_room_id = null;
            $academicyear = null;
            $semester = null;
            if ($this->Session->read('class_room_id')) {
                $class_room_id = $this->Session->read('class_room_id');
            }
            if ($this->Session->read('academicyear')) {
                $academicyear = $this->Session->read('academicyear');
            }
            if ($this->Session->read('semester')) {
                $semester = $this->Session->read('semester');
            }

            $already_recorded_periods = $this->ClassRoomClassPeriodConstraint->find(
                'list',
                array(
                    'conditions' => array(
                        'ClassRoomClassPeriodConstraint.class_room_id' => $class_room_id,
                        'ClassRoomClassPeriodConstraint.academic_year' => $academicyear,
                        'ClassRoomClassPeriodConstraint.semester' => $semester
                    ),
                    'fields' => array('ClassRoomClassPeriodConstraint.class_period_id')
                )
            );


            $periods = $this->ClassRoomClassPeriodConstraint->ClassPeriod->find(
                'all',
                array(
                    'fields' => array('ClassPeriod.id', 'ClassPeriod.period_setting_id', 'ClassPeriod.week_day'),
                    'conditions' => array(
                        'ClassPeriod.week_day' => $week_id,
                        'ClassPeriod.program_id' => $program_id,
                        'ClassPeriod.program_type_id' => $program_type_id,
                        'ClassPeriod.college_id' => $this->college_id,
                        'NOT' => array('ClassPeriod.id' => $already_recorded_periods)
                    ),
                    'contain' => array(
                        'PeriodSetting' => array(
                            'fields' => array(
                                'PeriodSetting.id',
                                'PeriodSetting.period',
                                'PeriodSetting.hour'
                            )
                        )
                    )
                )
            );

            return $periods;
        }
    }
}

<?php

namespace App\Controller;

use Cake\Event\Event;

class ClassPeriodsController extends AppController
{

    public $name = 'ClassPeriods';
    public $menuOptions = array(
        'parent' => 'scheduleSetting',
        'exclude' => array('getAlreadyRecordedPeriods'),
        'alias' => array(
            'index' => 'List Class Period',
            'add' => 'Add Class Period '
        )
    );

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        //$this->Auth->allow(array('*'));
        $this->Auth->allow('get_already_recorded_periods');
    }

    public function index()
    {

        $this->ClassPeriod->recursive = 0;
        $programTypes = $this->ClassPeriod->ProgramType->find('list');
        $programs = $this->ClassPeriod->Program->find('list');
        $this->set(compact('programTypes', 'programs'));
        $conditions = array('ClassPeriod.college_id' => $this->college_id);
        if (!empty($this->request->data['ClassPeriod']['programs'])) {
            $program_id = $this->request->data['ClassPeriod']['programs'];
            $conditions[] = array('ClassPeriod.program_id' => $program_id);
        }
        if (!empty($this->request->data['ClassPeriod']['programTypes'])) {
            $program_type_id = $this->request->data['ClassPeriod']['programTypes'];
            $conditions[] = array('ClassPeriod.program_type_id' => $program_type_id);
        }
        $this->paginate = array(
            'conditions' => $conditions,
            'order' => array(
                'ClassPeriod.period_setting_id'
            )
        );
        $this->set('classPeriods', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid class period'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('classPeriod', $this->ClassPeriod->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $selected_periods_array = array();
            if (!empty($this->request->data['ClassPeriod']['Selected'])) {
                foreach ($this->request->data['ClassPeriod']['Selected'] as $csk => $csv) {
                    if ($csv != 0) {
                        $selected_periods_array[] = $csk;
                    }
                }
            }
            if (count($selected_periods_array) > 0) {
                unset($this->request->data['ClassPeriod']['Selected']);
                foreach ($selected_periods_array as $period_setting_id) {
                    $this->request->data['ClassPeriod']['period_setting_id'] = $period_setting_id;
                    $this->ClassPeriod->create();
                    $this->ClassPeriod->save($this->request->data);
                }
                $this->Session->setFlash('<span></span>' . __('The class period has been saved'), 'default', array(
                    'class' => 'success-box success-message'
                ));
                //$this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('<span></span>' . __(' Please select at least 1 period'), 'default', array(
                    'class' => 'error-box error-message'
                ));
            }
            if ($this->Session->read('week_day')) {
                $this->Session->delete('week_day');
            }
            $merged_get_record_array = $this->_get_record(
                $this->request->data['ClassPeriod']['program_id'],
                $this->request->data['ClassPeriod']['program_type_id'],
                $this->request->data['ClassPeriod']['week_day']
            );
            $already_recorded_periods_array_fromadd = $merged_get_record_array[0];
            $unrecorded_periods_array_fromadd = $merged_get_record_array[1];
            //$periodSettings =	$merged_get_record_array[2];
            $selected_week_day = $merged_get_record_array[3];
            $this->set(
                compact(
                    'already_recorded_periods_array_fromadd',
                    'unrecorded_periods_array_fromadd',
                    'selected_week_day'
                )
            );
        }
        if ($this->Session->read('week_day')) {
            $merged_get_record_array = $this->_get_record(
                $this->Session->read('program_id'),
                $this->Session->read('program_type_id'),
                $this->Session->read('week_day')
            );

            $already_recorded_periods_array_fromadd = $merged_get_record_array[0];
            $unrecorded_periods_array_fromadd = $merged_get_record_array[1];
            //$periodSettings =	$merged_get_record_array[2];
            $selected_week_day = $merged_get_record_array[3];

            $this->set(
                compact(
                    'already_recorded_periods_array_fromadd',
                    'unrecorded_periods_array_fromadd',
                    'selected_week_day'
                )
            );
        }
        $colleges = $this->ClassPeriod->College->find('list');
        $programTypes = $this->ClassPeriod->ProgramType->find('list');
        $programs = $this->ClassPeriod->Program->find('list');
        $this->set(compact('periodSettings', 'colleges', 'programTypes', 'programs'));
    }

    public function getAlreadyRecordedPeriods($data = null)
    {

        //$this->layout = 'ajax';
        $explode_data = explode(" ", $data);
        $selected_week_day = $explode_data[0];
        $selected_program_id = $explode_data[1];
        $selected_program_type_id = $explode_data[2];
        $this->layout = 'ajax';

        if ($this->Session->read('program_id')) {
            $this->Session->delete('program_id');
        }
        if ($this->Session->read('program_type_id')) {
            $this->Session->delete('program_type_id');
        }
        if ($this->Session->read('week_day')) {
            $this->Session->delete('week_day');
        }

        $merged_get_record_array = $this->_get_record(
            $selected_program_id,
            $selected_program_type_id,
            $selected_week_day
        );
        $already_recorded_periods_array = $merged_get_record_array[0];
        $unrecorded_periods_array = $merged_get_record_array[1];
        $periodSettings = $merged_get_record_array[2];
        //$selected_week_day = $merged_get_record_array[3];
        $this->set(
            compact('already_recorded_periods_array', 'unrecorded_periods_array', 'periodSettings', 'selected_week_day')
        );
    }

    public function _get_record($program_id = null, $program_type_id = null, $week_day = null)
    {

        $already_recorded_setting = $this->ClassPeriod->find('list', array(
            'fields' => array('ClassPeriod.period_setting_id'),
            'conditions' => array(
                'ClassPeriod.college_id' => $this->college_id,
                'ClassPeriod.program_id' => $program_id,
                'ClassPeriod.program_type_id' => $program_type_id,
                'ClassPeriod.week_day' => $week_day
            )
        ));
        //debug($already_recorded_setting);
        $this->ClassPeriod->PeriodSetting->recursive = -1;
        $periodSettings = $this->ClassPeriod->PeriodSetting->find('all', array(
            'fields' => array(
                'PeriodSetting.id',
                'PeriodSetting.period',
                'PeriodSetting.hour'
            ),
            'conditions' => array('PeriodSetting.college_id' => $this->college_id)
        ));
        $already_recorded_periods_array = array();
        $unrecorded_periods_array = array();
        foreach ($periodSettings as $psk => $psv) {
            if (in_array($psv['PeriodSetting']['id'], $already_recorded_setting)) {
                $already_recorded_periods_array[array_search($psv['PeriodSetting']['id'], $already_recorded_setting)]
                    = $psv;
            } else {
                $unrecorded_periods_array[] = $psv;
            }
        }
        $merged_return_get_record_array = array();
        $merged_return_get_record_array[0] = $already_recorded_periods_array;
        $merged_return_get_record_array[1] = $unrecorded_periods_array;
        $merged_return_get_record_array[2] = $periodSettings;
        $merged_return_get_record_array[3] = $week_day;

        return $merged_return_get_record_array;
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid class period'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->ClassPeriod->save($this->request->data)) {
                $this->Session->setFlash(__('The class period has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The class period could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->ClassPeriod->read(null, $id);
        }
        $periodSettings = $this->ClassPeriod->PeriodSetting->find('list');
        $colleges = $this->ClassPeriod->College->find('list');
        $programTypes = $this->ClassPeriod->ProgramType->find('list');
        $programs = $this->ClassPeriod->Program->find('list');
        $this->set(compact('periodSettings', 'colleges', 'programTypes', 'programs'));
    }

    public function delete($id = null, $from = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span> ' . __('Invalid id for class period.'),
                'default',
                array('class' => 'error-box error-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        }
        $program_id = $this->ClassPeriod->field('ClassPeriod.program_id', array('ClassPeriod.id' => $id));
        $program_type_id = $this->ClassPeriod->field('ClassPeriod.program_type_id', array('ClassPeriod.id' => $id));
        $week_day = $this->ClassPeriod->field('ClassPeriod.week_day', array('ClassPeriod.id' => $id));
        $this->Session->write('program_id', $program_id);
        $this->Session->write('program_type_id', $program_type_id);
        $this->Session->write('week_day', $week_day);

        if ($this->ClassPeriod->delete($id)) {
            $this->Session->setFlash(
                '<span></span> ' . __('Class period deleted.'),
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
            '<span></span> ' . __('Class period was not deleted.'),
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

?>

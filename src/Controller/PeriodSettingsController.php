<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class PeriodSettingsController extends AppController
{

    public $name = 'PeriodSettings';
    public $menuOptions = array(
        'parent' => 'scheduleSetting',
        'exclude' => array(),
        'alias' => array(
            'index' => 'List Period Setting',
            'add' => 'Add Period Setting '
        )
    );
    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }

    public function index()
    {

        $this->PeriodSetting->recursive = 0;
        $this->paginate = array(
            'conditions' => array('PeriodSetting.college_id' => $this->college_id),
            'order' => array('PeriodSetting.period')
        );
        $this->set('periodSettings', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid period setting'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('periodSetting', $this->PeriodSetting->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $is_this_pariod_already_exist = $this->PeriodSetting->find('count', array(
                'conditions' => array(
                    'PeriodSetting.period' => $this->request->data['PeriodSetting']['period'],
                    'PeriodSetting.college_id' => $this->college_id
                )
            ));
            if ($is_this_pariod_already_exist == 0) {
                $hour = null;
                if ($this->request->data['PeriodSetting']['hour']['meridian'] == "am") {
                    $hour = $this->request->data['PeriodSetting']['hour']['hour'];
                } else {
                    $hour = ($this->request->data['PeriodSetting']['hour']['hour'] + 12);
                }
                $is_this_startingTime_already_exist = $this->PeriodSetting->field(
                    'PeriodSetting.period',
                    array('PeriodSetting.hour LIKE' => $hour . '%', 'PeriodSetting.college_id' => $this->college_id)
                );
                if (empty($is_this_startingTime_already_exist)) {
                    $this->PeriodSetting->create();
                    if ($this->PeriodSetting->save($this->request->data)) {
                        $this->Session->setFlash(
                            '<span></span> ' . __(
                                'Period ' . $this->request->data['PeriodSetting']['period'] . ' has been saved.'
                            ),
                            'default',
                            array('class' => 'success-box success-message')
                        );
                        //$this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(
                            '<span></span> ' . __(
                                'The period ' . $this->request->data['PeriodSetting']['period'] . ' could not be saved. Please, try again.'
                            ),
                            'default',
                            array('class' => 'error-box error-box')
                        );
                    }
                } else {
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'The period starting time is overlap with period ' . $is_this_startingTime_already_exist . ', Please proved unique starting time for this period or edit period ' . $is_this_startingTime_already_exist . ' starting time.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span> ' . __(
                        'Period ' . $this->request->data['PeriodSetting']['period'] . ' is already exist, Please proved unique period or edit period ' . $this->request->data['PeriodSetting']['period'] . ' starting time.'
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        $colleges = $this->PeriodSetting->College->find('list');
        $periodSettings = $this->PeriodSetting->find('all', array(
            'conditions' => array(
                'PeriodSetting.college_id' =>
                    $this->college_id
            ),
            'order' => array('PeriodSetting.period')
        ));
        //debug($periodSettings);
        $this->set(compact('colleges', 'periodSettings'));
    }

    public function edit($id = null, $from = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span> ' . __('Invalid period setting.'),
                'default',
                array('class' => 'error-box error-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        }
        if (!empty($this->request->data)) {
            $hour = null;
            if ($this->request->data['PeriodSetting']['hour']['meridian'] == "am") {
                $hour = $this->request->data['PeriodSetting']['hour']['hour'];
            } else {
                $hour = ($this->request->data['PeriodSetting']['hour']['hour'] + 12);
            }
            $is_this_startingTime_already_exist = $this->PeriodSetting->field(
                'PeriodSetting.period',
                array('PeriodSetting.hour LIKE' => $hour . '%')
            );
            if (empty($is_this_startingTime_already_exist)) {
                if ($this->PeriodSetting->save($this->request->data)) {
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Period ' . $this->request->data['PeriodSetting']['period']
                            . ' starting time has been updated.',
                            true
                        ),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    if (empty($from)) {
                        return $this->redirect(array('action' => 'index'));
                    } else {
                        return $this->redirect(array('action' => 'add'));
                    }
                } else {
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'The period ' . $this->request->data['PeriodSetting']['period']
                            . ' could not be saved. Please, try again.',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-box')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span> ' . __(
                        'The period starting time is overlap with period
					' . $is_this_startingTime_already_exist . ', Please proved unique starting time for this period.'
                        ,
                        true
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->PeriodSetting->read(null, $id);
        }
        //debug($this->request->data);
        $colleges = $this->PeriodSetting->College->find('list');
        $this->set(compact('colleges'));
    }

    public function delete($id = null, $from = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span> ' . __('Invalid id for period setting.'),
                'default',
                array('class' => 'error-box error-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        }
        $is_period_used_in_class_period = $this->PeriodSetting->ClassPeriod->find(
            'count',
            array(
                'conditions' => array(
                    'ClassPeriod.period_setting_id' => $id,
                    'ClassPeriod.college_id' => $this->college_id
                )
            )
        );
        if (empty($is_period_used_in_class_period)) {
            $Dperiod = $this->PeriodSetting->field('PeriodSetting.period', array('PeriodSetting.id' => $id));
            if ($this->PeriodSetting->delete($id)) {
                $this->Session->setFlash(
                    '<span></span> ' . __('Period ' . $Dperiod . ' has been deleted.'),
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
                '<span></span> ' . __('Period was not deleted.'),
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
                '<span></span> ' . __(
                    'You can not delete this period since it used in class period. So please delete the class period first.'
                ),
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
}

?>

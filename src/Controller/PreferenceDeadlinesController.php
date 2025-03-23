<?php

namespace App\Controller;
use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class PreferenceDeadlinesController extends AppController
{

    public $name = 'PreferenceDeadlines';
    public $menuOptions = array(
        'parent' => 'preferences',
        'exclude' => array('index'),
        'alias' => array(
            'index' => 'View Preference Deadline',
            'add' => 'Add Preference Deadline',
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
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyearArray();
        //To diplay current academic year as default in drop down list
        $selected = $this->AcademicYear->currentAcademicYear();
        foreach ($acyear_array_data as $k => $v) {
            if ($v == $selected) {
                $selected = $k;
                break;
            }
        }
        $this->set(compact('acyear_array_data', 'selected'));
    }

    public function index()
    {

        $this->PreferenceDeadline->recursive = 0;
        $conditions = array(
            "PreferenceDeadline.college_id" => $this->college_id,
        );
        $this->set('preferenceDeadlines', $this->paginate($conditions));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid preference deadline'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('preferenceDeadline', $this->PreferenceDeadline->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->PreferenceDeadline->create();
            $check_already_recorded_preference_deadline = $this->PreferenceDeadline->find(
                'count',
                array(
                    'conditions' => array(
                        'PreferenceDeadline.college_id' => $this->college_id,
                        'PreferenceDeadline.academicyear' => $this->request->data['PreferenceDeadline']['academicyear']
                    )
                )
            );
            if ($check_already_recorded_preference_deadline == 0) {
                if ($this->PreferenceDeadline->save($this->request->data)) {
                    $this->Session->setFlash(
                        '<span></span>' . __('The preference deadline has been saved'),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __('The preference deadline could not be saved. Please, try again.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'The preference deadline has already recored
			    for the academic year  selected.',
                        true
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
                $this->redirect(array('action' => 'index'));
            }
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash('<span></span>' . __('Invalid preference deadline'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            $recordown = $this->PreferenceDeadline->find('first', array(
                'conditions' =>
                    array('college_id' => $this->college_id)
            ));

            if ($this->Auth->user('id') == $recordown['PreferenceDeadline']['user_id']) {
                if ($this->PreferenceDeadline->save($this->request->data)) {
                    $this->Session->setFlash(
                        '<span></span>' . __('The preference deadline has been saved'),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __('The preference deadline could not be saved. Please, try again.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'You dont own this preference deadline.
			    This action will be reported. ',
                        true
                    ),
                    'default',
                    array('class' => 'warning-box warning-message')
                );
                $this->redirect(array('action' => 'index'));
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->PreferenceDeadline->read(null, $id);
            $this->set('recordedacademicyear', $this->request->data['PreferenceDeadline']['academicyear']);
        }
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for preference deadline'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->PreferenceDeadline->delete($id)) {
            $this->Session->setFlash(__('Preference deadline deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Preference deadline was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}

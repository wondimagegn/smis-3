<?php

namespace App\Controller;

use Cake\Event\Event;

class QuotasController extends AppController
{

    public $name = 'Quotas';
    public $menuOptions = array(
        'parent' => 'placement',
        'exclude' => array('index', 'add'),

    );


    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded
        $this->viewBuilder()->setHelpers(['Js']);
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
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        foreach ($acyear_array_data as $k => $v) {
            if ($v == $defaultacademicyear) {
                $defaultacademicyear = $k;
                break;
            }
        }
        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
    }

    public function index()
    {

        //$this->Quota->recursive = 0;
        //$this->set('quotas', $this->paginate());
        $this->redirect(array(
            'controller' => 'participatingDepartments',
            'action' => 'index'
        ));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid quota'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('quota', $this->Quota->read(null, $id));
    }

    public function add()
    {

        if ($college_data = $this->Session->read('users_relation')) {
            $college_id = $college_data[0]['Staff'][0]['college_id'];
        } else {
            $college_id = null;
        }
        if (!empty($this->request->data)) {
            $this->Quota->create();
            /*****Manipulationg  developing regions id to save as commad separted
             * if there are regions which deserve quota******/
            //debug($this->request->data);
            if (isset($this->request->data['Quota']['developing_regions_id']) && !empty($this->request->data['Quota']['developing_regions_id'])) {
                $developing_regions_id = null;
                $count = count($this->request->data['Quota']['developing_regions_id']);

                foreach ($this->request->data['Quota']['developing_regions_id'] as $key => $value) {
                    if (--$count) {
                        $developing_regions_id .= $value . ',';
                    } else {
                        $developing_regions_id .= $value;
                    }
                }

                $this->request->data['Quota']['developing_regions_id'] = $developing_regions_id;
            } else {
                $this->request->data['Quota']['regions'] = 0;
            }
            $this->set($this->request->data);
            if ($this->Quota->validates()) {
                if ($this->Quota->checkAvailableFemaleInTheGivenAcademicYear(
                    $this->request->data['Quota'],
                    $this->college_id,
                    $this->AcademicYear->current_academicyear()
                )) {
                    if ($this->Quota->checkAvailableRegionStudentInTheGivenAcademicYear(
                        $this->request->data['Quota'],
                        $this->college_id,
                        $this->request->data['Quota']['developing_regions_id'],
                        $this->AcademicYear->current_academicyear()
                    )) {
                        if ($this->Quota->save($this->request->data)) {
                            $this->Session->setFlash(__('The quota has been saved'));
                            $this->redirect(array('action' => 'index'));
                        } else {
                            $this->Session->setFlash(__('The quota could not be saved. Please, try again.'));
                        }
                    } else {
                        $this->Session->setFlash(
                            __(
                                'The region quota should be less than
			        or equal to the number of student in the given regions. Please, adjust the number again.',
                                true
                            )
                        );
                    }
                } else {
                    $this->Session->setFlash(
                        __(
                            'The female quota should be less than
			        or equal to the number of female students. Please, adjust the number again.',
                            true
                        )
                    );
                }
            } else {
                // didnt validate
                $this->Session->setFlash(__('Validation Error. Please, try again.'));
            }
        }


        $developingRegions = $this->Quota->College->AcceptedStudent->Region->find('list');

        $this->set(compact('developingRegions'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid quota'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            //is any region checked if manipulate the region id else set region quota zero
            if (isset($this->request->data['Quota']['developing_regions_id']) && !empty($this->request->data['Quota']['developing_regions_id'])) {
                $developing_regions_id = null;
                $count = count($this->request->data['Quota']['developing_regions_id']);

                foreach ($this->request->data['Quota']['developing_regions_id'] as $key => $value) {
                    if (--$count) {
                        $developing_regions_id .= $value . ',';
                    } else {
                        $developing_regions_id .= $value;
                    }
                }

                $this->request->data['Quota']['developing_regions_id'] = $developing_regions_id;
            } else {
                $this->request->data['Quota']['regions'] = 0;
            }
            if ($this->Quota->save($this->request->data)) {
                $this->Session->setFlash(__('The quota has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The quota could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Quota->read(null, $id);
            //debug($this->request->data);
        }
        if ($college_data = $this->Session->read('users_relation')) {
            $college_id = $college_data[0]['Staff'][0]['college_id'];
        } else {
            $college_id = null;
        }
        $colleges = $this->Quota->College->find('list', array(
            'conditions' =>
                array('College.id' => $college_id)
        ));
        $college_name = $colleges[$college_id];
        $developingRegions = $this->Quota->College->AcceptedStudent->Region->find('list');
        if (!empty($this->request->data['Quota']['developing_regions_id'])) {
            $selected = explode(",", $this->request->data['Quota']['developing_regions_id']);
            $this->set(compact('selected'));
        }

        $this->set(compact('college_name', 'college_id', 'developingRegions'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for quota'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->Quota->delete($id)) {
            $this->Session->setFlash(__('Quota deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Quota was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}

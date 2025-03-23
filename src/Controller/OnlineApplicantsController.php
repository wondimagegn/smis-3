<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class OnlineApplicantsController extends AppController
{

    public $name = 'OnlineApplicants';
    public $menuOptions = array(
        'parent' => 'placement',
        'exclude' => array('search'),
        'alias' => array(
            'index' => 'View Online Request',
            'process_selected' => 'Process Selected'

        )
    );

    public $paginate = array();

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

        $this->viewBuilder()->setHelpers(['DatePicker', 'Media.Media']);
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        //$acyear_array_data = $this->AcademicYear->acyear_array();
        $acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 1, date('Y'));
        $acYearMinuSeparated = $this->AcademicYear->acYearMinuSeparated();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        $defaultacademicyearMinusSeparted = str_replace('/', '-', $defaultacademicyear);

        if (!empty($this->program_type_id)) {
            $program_types = $programTypes = $this->OnlineApplicant->ProgramType->find('list', array(
                'conditions' =>
                    array('ProgramType.id' => $this->program_type_id)
            ));
        } else {
            $program_types = $programTypes = $this->OnlineApplicant->ProgramType->find('list');
        }
        if (!empty($this->program_id)) {
            $programs = $this->OnlineApplicant->Program->find('list', array(
                'conditions' =>
                    array('Program.id' => $this->program_id)
            ));
        } else {
            $programs = $this->OnlineApplicant->Program->find('list');
        }

        $this->set(
            compact(
                'acyear_array_data',
                'acYearMinuSeparated',
                'defaultacademicyear',
                'program_types',
                'programs',
                'programTypes',
                'defaultacademicyearMinusSeparted'
            )
        );
    }

    /*
    *Generic search for returned items
    */
    public function search()
    {

        // the page we will redirect to
        $url['action'] = 'index';

        // build a URL will all the search elements in it
        // the resulting URL will be
        // domain.com/returned_items/index/Search.keywords:mykeyword/Search.tag_id:3
        //debug($this->request->data);
        foreach ($this->request->data as $k => $v) {
            foreach ($v as $kk => $vv) {
                if (is_array($vv)) {
                    foreach ($vv as $kkk => $vvv) {
                        $url[$k . '.' . $kk . '.' . $kkk] = $vvv;
                    }
                } else {
                    $url[$k . '.' . $kk] = $vv;
                }
            }
        }
        // redirect the user to the url
        return $this->redirect($url, null, true);
    }

    public function index()
    {

        $this->paginate = array(
            'order' => array('OnlineApplicant.created' => ' DESC'),
            'maxLimit' => 100,
            'limit' => 100,
            'contain' => array(
                'College',
                'Program',
                'ProgramType',
                'Department',
                'OnlineApplicantStatus' => array('order' => array('OnlineApplicantStatus.created DESC'))
            )
        );


        // filter by tracking number
        if (
            isset($this->passedArgs['OnlineApplicant.trackingnumber'])
        ) {
            $trackingnumber = $this->passedArgs['OnlineApplicant.trackingnumber'];
            if (!empty($trackingnumber)) {
                $this->paginate['conditions'][]['OnlineApplicant.trackingnumber'] = $trackingnumber;
            }
            $this->request->data['OnlineApplicant']['trackingnumber'] = $this->passedArgs['OnlineApplicant.trackingnumber'];
        }
        // filter by name
        if (isset($this->passedArgs['OnlineApplicant.name'])) {
            $name = $this->passedArgs['OnlineApplicant.name'];
            if (!empty($name)) {
                $this->paginate['conditions'][]['OnlineApplicant.first_name like'] = '%' . $name . '%';
            }
            $this->request->data['OnlineApplicant']['name'] = $this->passedArgs['OnlineApplicant.name'];
        }
        // filter by period
        if (isset($this->passedArgs['OnlineApplicant.request_to.year'])) {
            $this->paginate['conditions'][] = array(
                'OnlineApplicant.created <= \'' . $this->passedArgs['OnlineApplicant.request_to.year']
                . '-' . $this->passedArgs['OnlineApplicant.request_to.month'] . '-' . $this->passedArgs['OnlineApplicant.request_to.day'] . '\''
            );

            $this->paginate['conditions'][] = array(
                'OnlineApplicant.created >= \'' . $this->passedArgs['OnlineApplicant.request_from.year']
                . '-' . $this->passedArgs['OnlineApplicant.request_from.month'] . '-' . $this->passedArgs['OnlineApplicant.request_from.day'] . '\''
            );
            $this->request->data['OnlineApplicant']['request_from'] = $this->passedArgs['OnlineApplicant.request_from.year']
                . '-' . $this->passedArgs['OnlineApplicant.request_from.month'] . '-' . $this->passedArgs['OnlineApplicant.request_from.day'];

            $this->request->data['OnlineApplicant']['request_to'] = $this->passedArgs['OnlineApplicant.request_to.year']
                . '-' . $this->passedArgs['OnlineApplicant.request_to.month'] . '-' . $this->passedArgs['OnlineApplicant.request_to.day'];
        }

        debug($this->request->data);

        $this->Paginator->settings = $this->paginate;
        $onlineApplicants = $this->Paginator->paginate('OnlineApplicant');

        if (empty($onlineApplicants) && isset($this->request->data) && !empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('There is no online admission request based on the given criteria.'),
                'default',
                array('class' => 'info-box info-message')
            );
        }


        $this->set(compact('onlineApplicants'));
    }

    public function process_selected()
    {

        if (
            !empty($this->request->data)
            && !empty($this->request->data['processSelected'])
        ) {
            $atleast_select_one = array_sum($this->request->data['OnlineApplicant']['approve']);
            if ($atleast_select_one > 0) {
                unset($this->request->data['OnlineApplicant']['SelectAll']);
                $admittedStudentsLists = array();
                $selectedAdmittedCount = 0;
                foreach (
                    $this->request->data['OnlineApplicant']['approve']
                    as $id => $selected
                ) {
                    if ($selected == 1) {
                        $selected_students[] = $id;
                        $basicData = $this->OnlineApplicant->find(
                            'first',
                            array('conditions' => array('OnlineApplicant.id' => $id))
                        );
                        if (!empty($basicData)) {
                            $admittedStudentsLists['AcceptedStudent'][$selectedAdmittedCount]['first_name'] = $basicData['OnlineApplicant']['first_name'];
                            $admittedStudentsLists['AcceptedStudent'][$selectedAdmittedCount]['middle_name'] = $basicData['OnlineApplicant']['father_name'];
                            $admittedStudentsLists['AcceptedStudent'][$selectedAdmittedCount]['last_name'] = $basicData['OnlineApplicant']['grand_father_name'];

                            $admittedStudentsLists['AcceptedStudent'][$selectedAdmittedCount]['sex'] = $basicData['OnlineApplicant']['gender'];

                            $admittedStudentsLists['AcceptedStudent'][$selectedAdmittedCount]['department_id'] = $basicData['OnlineApplicant']['department_id'];
                            $admittedStudentsLists['AcceptedStudent'][$selectedAdmittedCount]['program_id'] = $basicData['OnlineApplicant']['program_id'];
                            $admittedStudentsLists['AcceptedStudent'][$selectedAdmittedCount]['program_type_id'] = $basicData['OnlineApplicant']['program_type_id'];
                            $admittedStudentsLists['AcceptedStudent'][$selectedAdmittedCount]['college_id'] = $basicData['OnlineApplicant']['college_id'];
                            $admittedStudentsLists['AcceptedStudent'][$selectedAdmittedCount]['online_applicant_id'] = $basicData['OnlineApplicant']['id'];
                        }
                        $selectedAdmittedCount++;
                    }
                }

                if (ClassRegistry::init('AcceptedStudent')->saveAll(
                    $admittedStudentsLists['AcceptedStudent'],
                    array('validate' => 'first')
                )) {
                    $this->Session->setFlash(
                        __(
                            '<span></span>All selected students has been ready for in the registrar accepted student list for further processing.'
                        ),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(
                        __('<span></span>The student could not be saved. Please, try again.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('Please select atleast one student to process.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
                $this->request->data['getacceptedstudent'] = true;

                $this->request->data['OnlineApplicant'] = $this->Session->read('search_data');
            }
        }
        if (!empty($this->request->data) && !empty($this->request->data['getonlineapplicant'])) {
            if (!empty($this->request->data['OnlineApplicant']['academicyear'])) {
                $conditions = null;
                $ssacdemicyear = $this->request->data['OnlineApplicant']['academicyear'];
                $pprogram_id = $this->request->data['OnlineApplicant']['program_id'];
                $pprogram_type_id = $this->request->data['OnlineApplicant']['program_type_id'];
                $name = $this->request->data['OnlineApplicant']['name'];
                $college_ids = array();
                $department_ids = array();
                if (!empty($this->college_ids)) {
                    $college_ids = $this->college_ids;
                } elseif (!empty($this->department_ids)) {
                    $department_ids = $this->department_ids;
                }
                // retrive list of students based on registrar clerk assigned responsibility
                if (!empty($college_ids)) {
                    if (!empty($this->request->data['OnlineApplicant']['college_id'])) {
                        $conditions = array(
                            "OnlineApplicant.application_status" => 1,
                            "OnlineApplicant.academic_year LIKE" => "$ssacdemicyear%",
                            "OnlineApplicant.first_name LIKE" => "$name%",
                            "OnlineApplicant.college_id" => $this->request->data['OnlineApplicant']['college_id'],
                            "OnlineApplicant.program_id" => $pprogram_id,
                            "OnlineApplicant.program_type_id" => $pprogram_type_id,
                            "OnlineApplicant.id NOT IN (select online_applicant_id from accepted_students where online_applicant_id is not null )",
                        );
                    } else {
                        if (isset($this->request->data['OnlineApplicant']['department_id']) && !empty($this->request->data['OnlineApplicant']['department_id'])) {
                            $conditions = array(
                                "OnlineApplicant.application_status" => 1,
                                "OnlineApplicant.academic_year LIKE" => "$ssacdemicyear%",
                                "OnlineApplicant.first_name LIKE" => "$name%",
                                "OnlineApplicant.college_id" => $college_ids,
                                "OnlineApplicant.program_id" => $pprogram_id,
                                "OnlineApplicant.program_type_id" => $pprogram_type_id,
                                //"OnlineApplicant.department_id" => $this->request->data['OnlineApplicant']['department_id'],
                                "OnlineApplicant.id NOT IN (select online_applicant_id from accepted_students where online_applicant_id is not null )",
                            );
                        }
                    }
                } elseif (!empty($department_ids)) {
                    if (!empty($this->request->data['OnlineApplicant']['department_id'])) {
                        $conditions = array(
                            "OnlineApplicant.application_status" => 1,
                            "OnlineApplicant.academic_year" => $ssacdemicyear,
                            "OnlineApplicant.department_id" => $this->request->data['OnlineApplicant']['department_id'],
                            "OnlineApplicant.program_id" => $pprogram_id,
                            "OnlineApplicant.program_type_id" => $pprogram_type_id,
                        );
                        debug($conditions);
                    } else {
                        $conditions = array(
                            "OnlineApplicant.application_status" => 1,
                            "OnlineApplicant.academic_year LIKE" => "$ssacdemicyear%",
                            "OnlineApplicant.first_name LIKE" => "$name%",
                            "OnlineApplicant.department_id" => $department_ids,
                            "OnlineApplicant.program_id" => $pprogram_id,
                            "OnlineApplicant.program_type_id" => $pprogram_type_id,
                            "OnlineApplicant.id NOT IN (select online_applicant_id from accepted_students where online_applicant_id is not null )",
                        );
                    }
                }
                //
                if (!empty($conditions)) {
                    if (isset($this->request->data['OnlineApplicant']['limit'])) {
                        $limit = $this->request->data['OnlineApplicant']['limit'];
                    } else {
                        $limit = 1800;
                    }

                    $this->paginate = array(
                        'limit' => $limit,
                        'maxLimit' => $limit,
                    );
                    $this->paginate['conditions'] = $conditions;
                    $this->Paginator->settings = $this->paginate;
                    $onlineApplicants = $this->Paginator->paginate('OnlineApplicant');
                    $this->set('onlineApplicants', $onlineApplicants);
                    if (!empty($onlineApplicants)) {
                        $this->set('admitsearch', true);
                    } else {
                        $this->Session->setFlash(
                            __(
                                '<span></span>No data is found with your search criteria that needs admission, either all students has been process or no student is applied online in the given criteria.'
                            ),
                            'default',
                            array('class' => 'info-box info-message')
                        );
                    }
                    $admitsearch = true;
                    $this->request->data['getonlineapplicant'] = true;
                } else {
                    $this->Session->setFlash(
                        __('<span></span>You dont have privilage to admit students in the given criteria.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    __('<span></span>Please select academic  year'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        // display the right department and college based on the privilage of registrar users
        if ($this->role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read(
                'Auth.User'
            )['Role']['parent_id']) {
            $college_ids = array();
            $department_ids = array();

            if (!empty($this->college_ids)) {
                $college_ids = $this->college_ids;
                $this->set(
                    'colleges',
                    $this->OnlineApplicant->College->find(
                        'list',
                        array('conditions' => array('College.id' => $college_ids))
                    )
                );
                $this->set(
                    'departments',
                    $this->OnlineApplicant->Department->find(
                        'list',
                        array('conditions' => array('Department.college_id' => $college_ids))
                    )
                );
                $this->set('college_level', true);
            } elseif (!empty($this->department_ids)) {
                $department_ids = $this->department_ids;
                $this->set(
                    'departments',
                    $this->OnlineApplicant->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $department_ids))
                    )
                );
                $this->set(
                    'colleges',
                    $this->OnlineApplicant->College->find(
                        'list',
                        array('conditions' => array('College.id' => $college_ids))
                    )
                );
                $this->set('department_level', true);
            }
            $this->set(compact('colleges'));
        } else {
            $colleges = $this->OnlineApplicant->College->find('list');
            $departments = $this->OnlineApplicant->Department->find('list');
            $this->set(compact('colleges', 'departments'));
        }

        $programs = $this->OnlineApplicant->Program->find('list');
        $programTypes = $this->OnlineApplicant->ProgramType->find('list');
        $this->set(
            compact(
                'programs',
                'programTypes',
                'departments'
            )
        );
    }


    public function view($id = null)
    {

        if (!$this->OnlineApplicant->exists($id)) {
            throw new NotFoundException(__('Invalid online applicant  request'));
        }
        $options = array(
            'conditions' => array('OnlineApplicant.' . $this->OnlineApplicant->primaryKey => $id),
            'contain' => array('Attachment', 'OnlineApplicantStatus')
        );
        $onlineApplicant = $this->OnlineApplicant->find('first', $options);

        $this->set('onlineApplicant', $onlineApplicant);
        //$this->set(compact('statuses'));
    }

    public function delete($id = null)
    {

        $this->OnlineApplicant->id = $id;
        if (!$this->OnlineApplicant->exists()) {
            throw new NotFoundException(__('Invalid online applicant request'));
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->OnlineApplicant->delete()) {
            $this->Session->setFlash(
                '<span></span>' . __('The online applicant request has been deleted.'),
                'default',
                array('class' => 'success-box	success-message')
            );
        } else {
            $this->Flash->error(__(''));
            $this->Session->setFlash(
                '<span></span>' . __('The online applicant request could not be deleted. Please, try again.'),
                'default',
                array('class' => 'error-box error-message')
            );
        }
        return $this->redirect(array('action' => 'index'));
	}
}

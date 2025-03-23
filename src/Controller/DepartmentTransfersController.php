<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Core\Configure;

class DepartmentTransfersController extends AppController
{

    public $name = 'DepartmentTransfers';

    public $menuOptions = array(
        'parent' => 'transfers',
        'alias' => array(
            'index' => 'View Department Transfer',
            'add' => 'Request Department Transfer',
        ),
        'exclude' => array(
            'get_department_combo',
            'search'
        )
    );

    public $paginate = array();

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->Allow('search', 'get_department_combo');
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
    }

    public function __init_search_department_transfer()
    {

        if (!empty($this->request->data['Search'])) {
            $this->Session->write('search_data_department_transfer', $this->request->data['Search']);
        } else {
            if ($this->Session->check('search_data_department_transfer')) {
                $this->request->data['Search'] = $this->Session->read('search_data_department_transfer');
            }
        }
    }

    public function __init_clear_session_filters()
    {

        if ($this->Session->check('search_data_department_transfer')) {
            $this->Session->delete('search_data_department_transfer');
        }
    }

    public function search()
    {

        $this->__init_search_department_transfer();

        $url['action'] = 'index';

        if (!empty($this->request->data)) {
            foreach ($this->request->data as $k => $v) {
                foreach ($v as $kk => $vv) {
                    if (is_array($vv)) {
                        foreach ($vv as $kkk => $vvv) {
                            $url[$k . '.' . $kk . '.' . $kkk] = str_replace('/', '-', trim($vvv));
                        }
                    } else {
                        $url[$k . '.' . $kk] = str_replace('/', '-', trim($vv));
                    }
                }
            }
        }

        return $this->redirect($url, null, true);
    }

    public function index()
    {

        //$this->DepartmentTransfer->recursive = 0;
        //$this->paginate = array('order' => array('DepartmentTransfer.created' => 'DESC'));

        $this->__init_search_department_transfer();

        $selectedLimit = 100;
        $options = array();

        if (!empty($this->passedArgs)) {
            debug($this->passedArgs);

            if (isset($this->passedArgs['Search.department_id'])) {
                $this->request->data['Search']['department_id'] = $this->passedArgs['Search.department_id'];
            }

            if (isset($this->passedArgs['Search.limit']) && !empty($this->passedArgs['Search.limit'])) {
                $this->request->data['Search']['limit'] = $selectedLimit = $this->passedArgs['Search.limit'];
            } else {
                $this->request->data['Search']['limit'] = $selectedLimit = '';
            }

            if (isset($this->passedArgs['Search.transfer_request_date_from.year']) && !empty($this->passedArgs['Search.transfer_request_date_from.year'])) {
                $this->request->data['Search']['transfer_request_date_from']['year'] = $this->passedArgs['Search.transfer_request_date_from.year'];
                $this->request->data['Search']['transfer_request_date_from']['month'] = $this->passedArgs['Search.transfer_request_date_from.month'];
                $this->request->data['Search']['transfer_request_date_from']['day'] = $this->passedArgs['Search.transfer_request_date_from.day'];
            }


            if (isset($this->passedArgs['Search.transfer_request_date_to.year']) && !empty($this->passedArgs['Search.transfer_request_date_to.year'])) {
                $this->request->data['Search']['transfer_request_date_to']['year'] = $this->passedArgs['Search.transfer_request_date_to.year'];
                $this->request->data['Search']['transfer_request_date_to']['month'] = $this->passedArgs['Search.transfer_request_date_to.month'];
                $this->request->data['Search']['transfer_request_date_to']['day'] = $this->passedArgs['Search.transfer_request_date_to.day'];
            }

            ////////////////////

            if (isset($this->passedArgs['page'])) {
                $page = $this->request->data['Search']['page'] = $this->passedArgs['page'];
            }

            if (isset($this->passedArgs['sort'])) {
                $this->request->data['Search']['sort'] = $this->passedArgs['sort'];
            }

            if (isset($this->passedArgs['direction'])) {
                $this->request->data['Search']['direction'] = $this->passedArgs['direction'];
            }

            ////////////////////

            $this->__init_search_department_transfer();
        }

        if (isset($this->request->data['viewTransferApplication'])) {
            unset($this->passedArgs);
            $this->__init_clear_session_filters();
            $this->__init_search_department_transfer();
        }

        debug($this->request->data);

        if (!empty($this->request->data) && $this->role_id != ROLE_STUDENT /* && isset($this->request->data['viewTransferApplication']) */) {
            $options['conditions'][] = array('Student.graduated' => 0);

            if ($this->role_id == ROLE_DEPARTMENT) {
                $options['conditions'][] = array(
                    'OR' => array(
                        'DepartmentTransfer.department_id' => $this->department_id,
                        'DepartmentTransfer.from_department_id' => $this->department_id
                    )
                );

                if ($this->request->data['Search']['status'] == 1) {
                    $options['conditions'][] = array(
                        'OR' => array(
                            "DepartmentTransfer.sender_department_approval" => 1,
                            "DepartmentTransfer.receiver_department_approval" => 1,
                        )
                    );
                } else {
                    if ($this->request->data['Search']['status'] == -1) {
                        $options['conditions'][] = array(
                            'OR' => array(
                                "DepartmentTransfer.sender_department_approval" => -1,
                                "DepartmentTransfer.receiver_department_approval" => -1,
                            )
                        );
                    } else {
                        if ($this->request->data['Search']['status'] == 0) {
                            $options['conditions'][] = array(
                                'OR' => array(
                                    "DepartmentTransfer.sender_department_approval IS NULL",
                                    "DepartmentTransfer.receiver_department_approval IS NULL",
                                )
                            );
                        }
                    }
                }
            } else {
                if (!empty($this->request->data['Search']['department_id'])) {
                    $options['conditions'][] = array('DepartmentTransfer.department_id' => $this->request->data['Search']['department_id']);
                } else {
                    $options['conditions'][] = array(
                        'OR' => array(
                            'DepartmentTransfer.department_id' => $this->department_ids,
                            'DepartmentTransfer.from_department_id' => $this->department_ids
                        )
                    );
                }

                if ($this->request->data['Search']['status'] == 1) {
                    $options['conditions'][] = array(
                        'OR' => array(
                            "DepartmentTransfer.sender_college_approval" => 1,
                            "DepartmentTransfer.receiver_college_approval" => 1,
                        )
                    );
                } else {
                    if ($this->request->data['Search']['status'] == -1) {
                        $options['conditions'][] = array(
                            'OR' => array(
                                "DepartmentTransfer.sender_college_approval" => -1,
                                "DepartmentTransfer.receiver_college_approval" => -1,
                            )
                        );
                    } else {
                        if ($this->request->data['Search']['status'] == 0) {
                            $options['conditions'][] = array(
                                'OR' => array(
                                    "DepartmentTransfer.sender_college_approval IS NULL",
                                    "DepartmentTransfer.receiver_college_approval IS NULL",
                                )
                            );
                        }
                    }
                }
            }

            $options['conditions'][] = array('DepartmentTransfer.transfer_request_date >= \'' . $this->request->data['Search']['transfer_request_date_from']['year'] . '-' . $this->request->data['Search']['transfer_request_date_from']['month'] . '-' . $this->request->data['Search']['transfer_request_date_from']['day'] . '\'');
            $options['conditions'][] = array('DepartmentTransfer.transfer_request_date <= \'' . $this->request->data['Search']['transfer_request_date_to']['year'] . '-' . $this->request->data['Search']['transfer_request_date_to']['month'] . '-' . $this->request->data['Search']['transfer_request_date_to']['day'] . '\'');
        } else {
            $options['conditions'][] = array('Student.graduated' => 0);

            $defaltDaysAgoForRequests = (new DateTime())->modify(
                '-' . DEFAULT_DAYS_FOR_DEPARTMENT_TRANSFER_REQUEST_CHECK . ' days'
            )->format('Y-m-d');

            if ($this->role_id == ROLE_STUDENT) {
                $options['conditions'][] = array('DepartmentTransfer.student_id' => $this->student_id);
            } else {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $options['conditions'][] = array(
                        'OR' => array(
                            'DepartmentTransfer.department_id' => $this->department_id,
                            'DepartmentTransfer.from_department_id' => $this->department_id
                        )
                    );

                    //$options['conditions'][] = array('DepartmentTransfer.transfer_request_date >= \'' . $defaltDaysAgoForRequests . '\'');

                    $options['conditions'][] = array(
                        'DepartmentTransfer.transfer_request_date >= \'' . (date(
                                'Y'
                            ) - 1) . '-' . '01-01' . '\''
                    );
                    $options['conditions'][] = array(
                        'DepartmentTransfer.transfer_request_date <= \'' . (date(
                            'Y-m-d'
                        )) . '\''
                    );
                } else {
                    if (!empty($this->department_ids)) {
                        $options['conditions'][] = array(
                            'OR' => array(
                                'DepartmentTransfer.department_id' => $this->department_ids,
                                'DepartmentTransfer.from_department_id' => $this->department_ids
                            )
                        );

                        //$options['conditions'][] = array('DepartmentTransfer.transfer_request_date >= \'' . $defaltDaysAgoForRequests . '\'');
                        $options['conditions'][] = array(
                            'DepartmentTransfer.transfer_request_date >= \'' . (date(
                                    'Y'
                                ) - 1) . '-' . '01-01' . '\''
                        );
                        $options['conditions'][] = array(
                            'DepartmentTransfer.transfer_request_date <= \'' . (date(
                                'Y-m-d'
                            )) . '\''
                        );
                    }
                }
            }
        }


        $departmentTransfers = array();

        debug($options['conditions']);

        if (!empty($options['conditions'])) {
            // allow the student to cancel his request if he made a department request to his current department by mistake, all exclude requests made to and from the same department for college and department roles. Neway

            if ($this->role_id != ROLE_STUDENT) {
                $options['conditions'][] = array('DepartmentTransfer.department_id <> DepartmentTransfer.from_department_id');
            }

            $this->Paginator->settings = array(
                'conditions' => $options['conditions'],
                'order' => array(
                    'DepartmentTransfer.transfer_request_date' => 'DESC',
                    'DepartmentTransfer.modified' => 'DESC'
                ),
                'limit' => (!empty($selectedLimit) ? $selectedLimit : 100),
                'maxLimit' => (!empty($selectedLimit) ? $selectedLimit : 100),
                'page' => (isset($page) && $page > 0 ? $page : 1),
                'recursive' => 0
            );

            try {
                $departmentTransfers = $this->Paginator->paginate($this->modelClass);
                $this->set(compact('DepartmentTransfers'));
            } catch (NotFoundException $e) {
                if (!empty($this->request->data['Search'])) {
                    unset($this->request->data['Search']['page']);
                    unset($this->request->data['Search']['sort']);
                    unset($this->request->data['Search']['direction']);
                }
                unset($this->passedArgs);
                //$this->Session->write('search_data_department_transfer', $this->request->data['Search']);
                return $this->redirect(array('action' => 'index'));
            } catch (Exception $e) {
                if (!empty($this->request->data['Search'])) {
                    unset($this->request->data['Search']['page']);
                    unset($this->request->data['Search']['sort']);
                    unset($this->request->data['Search']['direction']);
                }
                unset($this->passedArgs);
                //$this->Session->write('search_data_department_transfer', $this->request->data['Search']);
                //return $this->redirect(array('action' => 'index'));
            }
        }

        //debug($departmentTransfers);

        if (empty($departmentTransfers)) {
            $this->Flash->info(__('There is no department transfer applicant in the given criteria.'));
        }

        if ($this->role_id == ROLE_DEPARTMENT) {
            $departments = $this->DepartmentTransfer->Department->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_id))
            );
            $this->request->data['Search']['department_id'] = $department_id = $this->department_id;
        } else {
            if ($this->role_id == ROLE_REGISTRAR || $this->role_id == ROLE_SYSADMIN || !empty($this->department_ids) || !empty($this->college_ids)) {
                $departments = $this->DepartmentTransfer->Department->find('list', array(
                    'conditions' => array(
                        'OR' => array(
                            'Department.college_id' => $this->college_ids,
                            'Department.id' => $this->department_ids
                        ),
                        'Department.active' => 1
                    )
                ));

                $departments = array('' => '[ Select Department ]') + $departments;

                if (isset($this->request->data['Search']['department_id'])) {
                    $this->request->data['Search']['department_id'] = '';
                }
            } else {
                if ($this->role_id == ROLE_COLLEGE) {
                    $departments = $this->DepartmentTransfer->Department->find('list', array(
                        'conditions' => array(
                            'OR' => array(
                                'Department.college_id' => $this->college_id,
                                'Department.id' => $this->department_ids
                            ),
                            'Department.active' => 1
                        )
                    ));

                    $departments = array('' => '[ Select Department ]') + $departments;

                    if (isset($this->request->data['Search']['department_id'])) {
                        $this->request->data['Search']['department_id'] = '';
                    }
                }
            }
        }

        $departmentsss = $this->DepartmentTransfer->Department->find('list');

        $this->set(compact('departmentTransfers', 'departments', 'departmentsss', 'selectedLimit'));
    }

    public function request_transfer()
    {

        $check_id_is_valid = ClassRegistry::init('Student')->find(
            'first',
            array('conditions' => array('Student.id' => $this->student_id), 'recursive' => -1)
        );

        $current_department_id = null;

        debug($this->student_id);

        if (empty($check_id_is_valid['Student']['department_id'])) {
            $this->Flash->error(
                __('You can not request department transfer since you don\'t have department currently.')
            );
            return $this->redirect(array('action' => 'index'));
        }

        $current_department_id = $check_id_is_valid['Student']['department_id'];

        if (!empty($this->request->data) && isset($this->request->data['saveTransfer'])) {
            debug($this->request->data);

            $college_id = $this->DepartmentTransfer->Department->find(
                'first',
                array('conditions' => array('Department.id' => $this->request->data['DepartmentTransfer']['department_id']))
            );

            $this->request->data['DepartmentTransfer']['to_college_id'] = $college_id['Department']['college_id'];
            $this->request->data['DepartmentTransfer']['from_department_id'] = $check_id_is_valid['Student']['department_id'];

            $this->request->data['DepartmentTransfer']['student_id'] = $check_id_is_valid['Student']['id'];

            $this->DepartmentTransfer->create();

            $checkPendingRequest = $this->DepartmentTransfer->find('count', array(
                'conditions' => array(
                    'DepartmentTransfer.student_id' => $this->student_id,
                    'OR' => array(
                        'DepartmentTransfer.sender_department_approval IS NULL',
                        'DepartmentTransfer.receiver_department_approval IS NULL',
                        'DepartmentTransfer.sender_college_approval IS NULL',
                        'DepartmentTransfer.receiver_college_approval IS NULL',
                    )
                )
            ));

            if (!$checkPendingRequest) {
                if ($this->DepartmentTransfer->save($this->request->data)) {
                    $this->Flash->success(
                        __('The department transfer request has been send to your current department')
                    );
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Flash->error(__('The department transfer could not be saved. Please, try again.'));
                }
            } else {
                $this->Flash->error(
                    __(
                        'There is department transfer request submitted previously waiting for decision. Please check the status of the previous request or cancel your previous request if not approved and you can submit your request again.'
                    )
                );
                return $this->redirect(array('action' => 'index'));
            }
        }

        //debug($check_id_is_valid);

        $currentCollegeSteam = $this->DepartmentTransfer->Student->College->field(
            'stream',
            array('College.id' => $check_id_is_valid['Student']['college_id'])
        );

        $student_section_exam_status = $this->DepartmentTransfer->Student->get_student_section($this->student_id);

        $colleges = $this->DepartmentTransfer->Student->College->find(
            'list',
            array(
                'conditions' => array(
                    'College.active' => 1,
                    'College.id <>' => Configure::read('only_stream_based_colleges_pre_social_natural'),
                    'College.stream' => $currentCollegeSteam
                )
            )
        );

        $departments = array();

        if (!empty($this->request->data['DepartmentTransfer']['college_id'])) {
            $departments = $this->DepartmentTransfer->Department->find(
                'list',
                array(
                    'conditions' => array(
                        'Department.college_id' => $this->request->data['DepartmentTransfer']['college_id'],
                        'Department.active' => 1
                    )
                )
            );
        } /* else {
			$temp = array_keys($colleges);
			$collegeIds = $temp[0];
			$departments = $this->DepartmentTransfer->Department->find('list', array('conditions' => array('Department.college_id' => $collegeIds, 'Department.active' => 1)));
		} */

        if (isset($current_department_id)) {
            if (isset($departments[$current_department_id])) {
                unset($departments[$current_department_id]);
            }
        }

        $error_message = '';

        $checkEligibility = $this->DepartmentTransfer->chceckStudentForDepartmentTransfer($this->student_id, 'Student');

        //debug($checkEligibility);

        if (isset($checkEligibility[0][1]['disqualification']) && !empty($checkEligibility[0][1]['disqualification'])) {
            $error_message = $checkEligibility[0][1]['disqualification'][0];
        } else {
            if (isset($checkEligibility) && !empty($checkEligibility) && !is_array($checkEligibility)) {
                $error_message = $checkEligibility;
            }
        }

        $attended_semester = ClassRegistry::init('ExamGrade')->getListOfAyAndSemester($this->student_id);

        $this->set(
            compact(
                'student_section_exam_status',
                'colleges',
                'departments',
                'attended_semester',
                'check_id_is_valid',
                'error_message'
            )
        );
    }

    public function department_approve_transfer()
    {

        if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
            $this->DepartmentTransfer->create();

            foreach ($this->request->data['DepartmentTransfer'] as $in => &$va) {
                if (isset($va['id'])) {
                    if (isset($va['sender_department_approval']) && $va['sender_department_approval'] != "") {
                        $va['sender_department_approval_by'] = $this->Auth->user('id');
                        $va['sender_department_approval_date'] = date('Y-m-d h:i:s');
                    } else {
                        if (isset($va['sender_college_approval']) && $va['sender_college_approval'] != "") {
                            $va['sender_college_approval_by'] = $this->Auth->user('id');
                            $va['sender_college_approval_date'] = date('Y-m-d h:i:s');
                        } else {
                            if (isset($va['receiver_department_approval']) && $va['receiver_department_approval'] != "") {
                                // is transfer intra
                                $student_orginal_department_college = $this->DepartmentTransfer->Student->field(
                                    'college_id',
                                    array('Student.id' => $va['student_id'])
                                );
                                $student_requested_department_college = $this->DepartmentTransfer->Department->field(
                                    'college_id',
                                    array('Department.id' => $va['department_id'])
                                );

                                if ($student_orginal_department_college == $student_requested_department_college) {
                                    $va['receiver_college_approval'] = $va['receiver_department_approval'];
                                    $va['receiver_college_approval_date'] = date('Y-m-d h:i:s');
                                    $va['receiver_college_approval_by'] = 'Automatic';

                                    // accepted
                                    if ($va['receiver_department_approval'] == 1) {
                                        // update student department id in student table
                                        $acceptedStudentId = $this->DepartmentTransfer->Student->field(
                                            'Student.accepted_student_id',
                                            array('Student.id' => $va['student_id'])
                                        );

                                        $this->DepartmentTransfer->Student->id = $va['student_id'];
                                        $this->DepartmentTransfer->Student->saveField(
                                            'department_id',
                                            $va['department_id']
                                        );
                                        $this->DepartmentTransfer->Student->saveField('curriculum_id', null);

                                        ClassRegistry::init('AcceptedStudent')->id = $acceptedStudentId;
                                        ClassRegistry::init('AcceptedStudent')->saveField(
                                            'department_id',
                                            $va['department_id']
                                        );
                                        ClassRegistry::init('AcceptedStudent')->saveField('curriculum_id', null);

                                        // archive the section
                                        $this->DepartmentTransfer->Student->StudentsSection->id = $this->DepartmentTransfer->Student->StudentsSection->field(
                                            'StudentsSection.id',
                                            array(
                                                'StudentsSection.student_id' => $va['student_id'],
                                                'StudentsSection.archive' => 0
                                            )
                                        );
                                        $this->DepartmentTransfer->Student->StudentsSection->saveField('archive', '1');
                                    }
                                }

                                $va['receiver_department_approval_by'] = $this->Auth->user('id');
                                $va['receiver_department_approval_date'] = date('Y-m-d h:i:s');
                            } else {
                                if (isset($va['receiver_college_approval']) && $va['receiver_college_approval'] != "") {
                                    $va['receiver_college_approval_by'] = $this->Auth->user('id');
                                    $va['receiver_college_approval_date'] = date('Y-m-d h:i:s');
                                } else {
                                    unset($this->request->data['DepartmentTransfer'][$in]);
                                }
                            }
                        }
                    }
                } else {
                    unset($this->request->data['DepartmentTransfer'][$in]);
                }
            }

            debug($this->request->data['DepartmentTransfer']);

            if (!empty($this->request->data['DepartmentTransfer'])) {
                if ($this->DepartmentTransfer->saveAll(
                    $this->request->data['DepartmentTransfer'],
                    array('validate' => 'first')
                )) {
                    $this->Flash->success(
                        __(
                            'The selected transfer applicantions have been approved, and the applicant students will be notified.'
                        )
                    );
                    //$this->request->data['Search']['status'] = 1;
                    //$this->Session->write('search_data_department_transfer', $this->request->data['Search']);
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Flash->error(__('The transfer could not be saved. Please, try again.'));
                }
            }
        }

        $defaltDaysAgoForRequests = (new DateTime())->modify(
            '-' . DEFAULT_DAYS_FOR_DEPARTMENT_TRANSFER_REQUEST_CHECK . ' days'
        )->format('Y-m-d');

        $optionsleaver['conditions'][] = array(
            "DepartmentTransfer.sender_department_approval is null",
            "DepartmentTransfer.from_department_id" => $this->department_id,
            'DepartmentTransfer.department_id <> DepartmentTransfer.from_department_id',
            'DepartmentTransfer.transfer_request_date >= \'' . $defaltDaysAgoForRequests . '\'',
            "Student.graduated" => 0
        );

        $optionscoming['conditions'][] = array(
            "DepartmentTransfer.sender_department_approval = 1",
            "DepartmentTransfer.sender_college_approval = 1",
            "DepartmentTransfer.receiver_college_approval is null",
            "DepartmentTransfer.receiver_department_approval is null",
            "DepartmentTransfer.department_id" => $this->department_id,
            'DepartmentTransfer.department_id <> DepartmentTransfer.from_department_id',
            'DepartmentTransfer.transfer_request_date >= \'' . $defaltDaysAgoForRequests . '\'',
            "Student.graduated" => 0
        );

        $departmentTransfersLeaverRequest = $this->DepartmentTransfer->find('all', $optionsleaver);

        $departmentTransfersIncomingToYourDepartment = $this->DepartmentTransfer->find('all', $optionscoming);

        debug($departmentTransfersLeaverRequest);

        debug($departmentTransfersIncomingToYourDepartment);


        if (empty($departmentTransfersLeaverRequest) && empty($departmentTransfersIncomingToYourDepartment)) {
            $this->Flash->info(
                'There is no department transfer request in the system that needs your approval for now.'
            );
            //$this->redirect(array('action' => 'index'));
        } else {
            $departmentTransfersLeaverRequest = $this->DepartmentTransfer->attachSemesterAttended(
                $departmentTransfersLeaverRequest
            );
            $departmentTransfersIncomingToYourDepartment = $this->DepartmentTransfer->attachSemesterAttended(
                $departmentTransfersIncomingToYourDepartment
            );
        }

        $this->set(compact('departmentTransfersIncomingToYourDepartment', 'departmentTransfersLeaverRequest'));
    }

    public function college_approve_transfer()
    {

        if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
            $this->DepartmentTransfer->create();


            foreach ($this->request->data['DepartmentTransfer'] as $in => &$va) {
                $designationDepartmentId = $this->DepartmentTransfer->field(
                    'DepartmentTransfer.department_id',
                    array('DepartmentTransfer.id' => $va['id'])
                );

                if (isset($va['sender_college_approval']) && $va['sender_college_approval'] != "") {
                    $va['sender_college_approval_by'] = $this->Auth->user('id');
                    $va['sender_college_approval_date'] = date('Y-m-d h:i:s');
                } else {
                    if (isset($va['receiver_college_approval']) && $va['receiver_college_approval'] != "") {
                        $va['receiver_college_approval_by'] = $this->Auth->user('id');
                        $va['receiver_college_approval_date'] = date('Y-m-d h:i:s');
                    } else {
                        unset($this->request->data['DepartmentTransfer'][$in]);
                    }
                }

                if ($va['receiver_college_approval'] == 1) {
                    // update student department id in student table
                    if (isset($designationDepartmentId) && !empty($designationDepartmentId)) {
                        $this->DepartmentTransfer->Student->id = $va['student_id'];
                        $this->DepartmentTransfer->Student->saveField('department_id', $designationDepartmentId);
                        $this->DepartmentTransfer->Student->saveField('college_id', $this->college_id);
                        $this->DepartmentTransfer->Student->saveField('original_college_id', $this->college_id);
                        $this->DepartmentTransfer->Student->saveField('curriculum_id', null);

                        // archive the section
                        $this->DepartmentTransfer->Student->StudentsSection->id = $this->DepartmentTransfer->Student->StudentsSection->field(
                            'StudentsSection.id',
                            array('StudentsSection.student_id' => $va['student_id'], 'StudentsSection.archive' => 0)
                        );
                        $this->DepartmentTransfer->Student->StudentsSection->saveField('archive', '1');
                    }
                }
            }

            if (!empty($this->request->data['DepartmentTransfer'])) {
                if ($this->DepartmentTransfer->saveAll(
                    $this->request->data['DepartmentTransfer'],
                    array('validate' => 'first')
                )) {
                    $this->Flash->success(
                        __(
                            'The selected transfer applicantins have been approved, and the applicant students will be notified.'
                        )
                    );
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Flash->error(__('The transfer could not be saved. Please, try again.'));
                }
            }
        }

        $defaltDaysAgoForRequests = (new DateTime())->modify(
            '-' . DEFAULT_DAYS_FOR_DEPARTMENT_TRANSFER_REQUEST_CHECK . ' days'
        )->format('Y-m-d');

        $optionsleaver['conditions'][] = array(
            "DepartmentTransfer.sender_college_approval is null",
            "DepartmentTransfer.sender_department_approval = 1",
            "Student.college_id " => $this->college_id,
            'DepartmentTransfer.department_id <> DepartmentTransfer.from_department_id',
            'DepartmentTransfer.transfer_request_date >= \'' . $defaltDaysAgoForRequests . '\'',
            "Student.graduated" => 0
        );

        $optionscoming['conditions'][] = array(
            "DepartmentTransfer.sender_department_approval = 1",
            "DepartmentTransfer.sender_college_approval = 1",
            "DepartmentTransfer.receiver_department_approval = 1",
            "DepartmentTransfer.receiver_college_approval is null",
            "DepartmentTransfer.to_college_id " => $this->college_id,
            'DepartmentTransfer.department_id <> DepartmentTransfer.from_department_id',
            'DepartmentTransfer.transfer_request_date >= \'' . $defaltDaysAgoForRequests . '\'',
            "Student.graduated" => 0
        );

        $departmentTransfersLeaverRequest = $this->DepartmentTransfer->find('all', $optionsleaver);
        $departmentTransfersIncomingToYourDepartment = $this->DepartmentTransfer->find('all', $optionscoming);


        if (empty($departmentTransfersLeaverRequest) && empty($departmentTransfersIncomingToYourDepartment)) {
            $this->Flash->info(
                __('There is no department transfer request in the system that needs your approval for now.')
            );
            $this->redirect(array('action' => 'index'));
        } else {
            $departmentTransfersLeaverRequest = $this->DepartmentTransfer->attachSemesterAttended(
                $departmentTransfersLeaverRequest
            );
            $departmentTransfersIncomingToYourDepartment = $this->DepartmentTransfer->attachSemesterAttended(
                $departmentTransfersIncomingToYourDepartment
            );
        }

        $this->set(compact('departmentTransfersIncomingToYourDepartment', 'departmentTransfersLeaverRequest'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Flash->error(__('Invalid department transfer'));
            return $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data)) {
            if ($this->DepartmentTransfer->save($this->request->data)) {
                $this->Flash->success(__('The department transfer has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('The department transfer could not be saved. Please, try again.'));
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->DepartmentTransfer->read(null, $id);
        }

        $departments = $this->DepartmentTransfer->Department->find('list');
        $students = $this->DepartmentTransfer->Student->find('list');

        $this->set(compact('departments', 'students'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Flash->error(__('Invalid id for department transfer'));
            return $this->redirect(array('action' => 'index'));
        }

        $check = $this->DepartmentTransfer->find(
            'count',
            array(
                'conditions' => array(
                    'DepartmentTransfer.sender_department_approval is null',
                    'DepartmentTransfer.student_id' => $this->student_id,
                    'DepartmentTransfer.id' => $id
                )
            )
        );

        if ($check) {
            if ($this->DepartmentTransfer->delete($id)) {
                $this->Flash->success(__('Department transfer request cancelled.'));
                return $this->redirect(array('action' => 'index'));
            }
        }

        $this->Flash->error(__('Department transfer was not deleted.'));

        return $this->redirect(array('action' => 'index'));
    }

    public function apply_department_transfer_for_student()
    {

        if (!empty($this->request->data) && isset($this->request->data['applyTransfer'])) {
            $everythingfine = true;

            if (empty($this->request->data)) {
                $this->Flash->error(__('Please provide transfer details.'));
                $everythingfine = false;
            }

            $department_id = null;
            $college_id = null;

            if (!empty($this->department_ids)) {
                $department_id = $this->department_ids;
            } else {
                if (!empty($this->department_id)) {
                    $department_id = $this->department_id;
                } else {
                    if ($this->role_id == ROLE_REGISTRAR) {
                        if (!empty($this->department_ids)) {
                            $department_id = $this->department_ids;
                        } else {
                            if (!empty($this->college_ids)) {
                                $college_id = $this->college_ids;
                            }
                        }
                    }
                }
            }

            if ($everythingfine) {
                if (!empty($department_id)) {
                    $check_id_is_valid = ClassRegistry::init('Student')->find(
                        'first',
                        array(
                            'conditions' => array(
                                'Student.studentnumber' => trim(
                                    $this->request->data['Student']['studentnumber']
                                ),
                                'Student.department_id' => $department_id
                            )
                        )
                    );
                } else {
                    if (!empty($college_id)) {
                        $check_id_is_valid = ClassRegistry::init('Student')->find(
                            'count',
                            array(
                                'conditions' => array(
                                    'Student.studentnumber' => trim(
                                        $this->request->data['Student']['studentnumber']
                                    ),
                                    'Student.college_id' => $college_id,
                                    'Student.department_id is null'
                                )
                            )
                        );
                    }
                }

                if ($check_id_is_valid) {
                    $everythingfine = true;
                    $student_section_exam_status = $this->DepartmentTransfer->Student->get_student_section(
                        $check_id_is_valid['Student']['id']
                    );
                    $attended_semester = ClassRegistry::init('ExamGrade')->getListOfAyAndSemester(
                        $check_id_is_valid['Student']['id']
                    );

                    $colleges = $this->DepartmentTransfer->Student->College->find('list');

                    $temp = array_keys($colleges);
                    $collegeIds = $temp[0];

                    $departments = $this->DepartmentTransfer->Department->find(
                        'list',
                        array('conditions' => array('Department.college_id' => $collegeIds))
                    );

                    if (empty($check_id_is_valid['Student']['department_id'])) {
                        $this->Flash->error(
                            __(
                                'You can not request department transfer for this student. The student don\'t have department currently.'
                            )
                        );
                        return $this->redirect(array('action' => 'index'));
                    }

                    $this->set(
                        compact(
                            'student_section_exam_status',
                            'check_id_is_valid',
                            'departments',
                            'colleges',
                            'attended_semester'
                        )
                    );
                } else {
                    $everythingfine = false;
                    $this->Session->setFlash(
                        '<span></span> ' . __('The provided student number  is not valid.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['saveTransfer'])) {
            $college_id = $this->DepartmentTransfer->Department->find(
                'first',
                array('conditions' => array('Department.id' => $this->request->data['DepartmentTransfer']['department_id']))
            );

            $this->request->data['DepartmentTransfer']['to_college_id'] = $college_id['Department']['college_id'];
            $this->DepartmentTransfer->create();

            // find the college where the department located

            if ($this->DepartmentTransfer->save($this->request->data)) {
                $this->Flash->success(__('The department transfer request has been send to your current department'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('The department transfer could not be saved. Please, try again.'));
            }
        }
    }

    public function __auto_department_transfer_update()
    {

        $optionscoming['conditions'][] = array(
            "DepartmentTransfer.to_college_id is null",
            "DepartmentTransfer.from_department_id is null",
        );

        $optionscoming['contain'] = array('Student' => array('AcceptedStudent'));

        $departmentTransfersLeaverRequest = $this->DepartmentTransfer->find('all', $optionscoming);
        //debug($departmentTransfersLeaverRequest);
        $transferUpdate = array();
        $count = 0;

        if (!empty($departmentTransfersLeaverRequest)) {
            foreach ($departmentTransfersLeaverRequest as $k => $v) {
                if (isset($v['Student']['AcceptedStudent']['department_id'])) {
                    $transferUpdate['DepartmentTransfer'][$count]['id'] = $v['DepartmentTransfer']['id'];
                    $transferUpdate['DepartmentTransfer'][$count]['from_department_id'] = $v['Student']['AcceptedStudent']['department_id'];
                    $transferUpdate['DepartmentTransfer'][$count]['to_college_id'] = $this->DepartmentTransfer->Department->field(
                        'college_id',
                        array('Department.id' => $v['DepartmentTransfer']['department_id'])
                    );
                    $count++;
                }
            }
        }

        // debug($transferUpdate);

        if (isset($transferUpdate['DepartmentTransfer'])) {
            if ($this->DepartmentTransfer->saveAll($transferUpdate['DepartmentTransfer'], array('validate' => false))) {
                echo 'Done';
            } else {
                echo 'Something went wrong';
            }
        }
    }

    public function get_department_combo($college_id = null, $current_department_id = null, $student_program_id = null)
    {

        $this->layout = 'ajax';

        $departments = array();
        $availableDepartmentsBasedOnProgram = array();

        if (!empty($student_program_id)) {
            $availableDepartmentsBasedOnProgram = ClassRegistry::init('Curriculum')->find('list', array(
                'fields' => array('Curriculum.department_id', 'Curriculum.department_id'),
                'conditions' => array(
                    'Curriculum.program_id' => $student_program_id,
                    'Curriculum.active' => 1
                ),
                'group' => 'Curriculum.department_id'
            ));
        }

        if (!empty($college_id)) {
            $departments = $this->DepartmentTransfer->Department->find('list', array(
                'conditions' => array(
                    'Department.college_id' => $college_id,
                    'Department.id' => $availableDepartmentsBasedOnProgram,
                    'Department.active' => 1
                )
            ));
        }

        if (!empty($current_department_id)) {
            if (isset($departments[$current_department_id])) {
                unset($departments[$current_department_id]);
            }
        }

        //debug($departments);

        $this->set(compact('departments'));
    }
}

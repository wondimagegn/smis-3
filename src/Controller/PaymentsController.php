<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class PaymentsController extends AppController
{

    public $name = 'Payments';
    public $menuOptions = array(
        'parent' => 'costShares',
        'alias' => array(
            'index' => 'View Payment',
            'add' => 'Add Payment',

        )
    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded


    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyearArray();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
    }

    public function __init_search()
    {

        // We create a search_data session variable when we fill any criteria
        // in the search form.
        if (!empty($this->request->data['Payment'])) {
            $search_session = $this->request->data['Payment'];
            // Session variable 'search_data'
            $this->Session->write('search_data', $search_session);
        } else {
            $search_session = $this->Session->read('search_data');
            $this->request->data['Payment'] = $search_session;
        }
    }

    public function index()
    {

        $this->paginate = array('order' => array('Payment.created DESC '));
        $this->__init_search();
        if ($this->Session->read('search_data')) {
            $this->request->data['viewPayment'] = true;
        }
        if (!empty($this->request->data) && isset($this->request->data['viewPayment'])) {
            $options = array();
            if (!empty($this->request->data['Payment']['department_id'])) {
                $options [] = array(
                    'Student.department_id' => $this->request->data['Payment']['department_id']

                );
            }

            if (!empty($this->request->data['Payment']['college_id'])) {
                $options [] = array(
                    'Student.college_id' => $this->request->data['Payment']['college_id']

                );
            }
            if (!empty($this->request->data['Payment']['paid_date_to'])) {
                $options [] = array(
                    'Payment.payment_date >= \'' .
                    $this->request->data['Payment']['paid_date_from']['year'] . '-' .
                    $this->request->data['Payment']['paid_date_from']['month'] . '-' .
                    $this->request->data['Payment']['paid_date_from']['day'] . '\'',

                    'Payment.payment_date <= \'' .
                    $this->request->data['Payment']['paid_date_to']['year'] . '-' .
                    $this->request->data['Payment']['paid_date_to']['month'] . '-' .
                    $this->request->data['Payment']['paid_date_to']['day'] . '\'',

                );
            }

            if (!empty($this->request->data['Payment']['reference_number'])) {
                unset($options);
                $options [] = array(
                    'Payment.reference_number like ' => $this->request->data['Payment']['reference_number'] . '%'
                );
            }


            $payments = $this->paginate($options);

            if (empty($payments)) {
                $this->Session->setFlash(
                    '<span></span>' .
                    __('There is no student in the system that  paid payment in the given criteria.'),
                    'default',
                    array('class' => 'info-box info-message')
                );
            }
        }
        if (!empty($this->request->data['Payment']['college_id'])) {
            if (!empty($this->department_ids)) {
                $departments = $this->Payment->Student->Department->find(
                    'list',
                    array(
                        'conditions' => array(
                            'Department.college_id' =>
                                $this->request->data['Payment']['college_id'],
                            'Department.id' => $this->department_ids
                        )
                    )
                );
            }

            $this->set(compact('departments'));
        }
        $colleges = $this->Payment->Student->College->find('list');

        $this->set(compact('payments'));
        $this->set(compact('colleges'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash('<span></span>' . __('Invalid payment'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('payment', $this->Payment->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data) && isset($this->request->data['saveApplicablePayment'])) {
            $this->Payment->create();
            if ($this->Payment->duplication($this->request->data) == 0) {
                if ($this->Payment->save($this->request->data)) {
                    $this->Session->setFlash(
                        '<span></span>' . __('The payment has been saved'),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __('The payment could not be saved. Please, try again.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );

                    $this->request->data['continue'] = true;
                    $student_number = $this->Payment->Student->field(
                        'studentnumber',
                        array('id' => trim($this->request->data['Payment']['student_id']))
                    );
                    $this->request->data['Payment']['studentID'] = $student_number;
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'You have already recorded  payment for the selected student for ' . $this->request->data['Payment']['academic_year'] . ' of semester ' . $this->request->data['Payment']['semester'] . '.'
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
                $this->request->data['continue'] = true;
                $student_number = $this->Payment->Student->field(
                    'studentnumber',
                    array('id' => trim($this->request->data['Payment']['student_id']))
                );
                $this->request->data['Payment']['studentID'] = $student_number;
            }
        }
        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            $everythingfine = false;
            if (!empty($this->request->data['Payment']['studentID'])) {
                $check_id_is_valid = $this->Payment->Student->
                find(
                    'count',
                    array(
                        'conditions' => array(
                            'Student.studentnumber' =>
                                trim($this->request->data['Payment']['studentID'])
                        )
                    )
                );
                $studentIDs = 1;

                if ($check_id_is_valid > 0) {
                    $everythingfine = true;
                    $student_id = $this->Payment->Student->field(
                        'id',
                        array('studentnumber' => trim($this->request->data['Payment']['studentID']))
                    );
                    $applicable_payments = ClassRegistry::init('ApplicablePayment')->find(
                        'first',
                        array(
                            'conditions' => array(
                                'ApplicablePayment.academic_year like ' =>
                                    $this->request->data['Payment']['academic_year'],
                                'ApplicablePayment.semester' =>
                                    $this->request->data['Payment']['semester']
                            ),
                            'recursive' => -1
                        )
                    );
                    if (!empty($applicable_payments)) {
                        $student_section_exam_status = $this->Payment->Student->
                        get_student_section($student_id);
                        $this->set(compact('student_section_exam_status'));


                        $this->set(compact('studentIDs', 'applicable_payments'));
                    } else {
                        $this->Session->setFlash(
                            '<span></span> ' . __(
                                'You can not maintain payment for the given student,before maintaining appliable payment for ' . $this->request->data['Payment']['academic_year'] . ' academic year of semester ' . $this->request->data['Payment']['semester'] . ' . First maintain the appliable payment for student here.'
                            ),
                            'default',
                            array('class' => 'info-box info-message')
                        );
                        $this->redirect(array('controller' => 'applicablePayments', 'action' => 'add'));
                    }
                } else {
                    $this->Session->setFlash(
                        '<span></span> ' . __('The provided student number is not valid.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span> ' . __('Please provide student number to maintain student applicable payment.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid payment'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Payment->save($this->request->data)) {
                $this->Session->setFlash(__('The payment has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The payment could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Payment->read(null, $id);
        }
        $students = $this->Payment->Student->find('list');
        $this->set(compact('students'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for payment'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->Payment->delete($id)) {
            $this->Session->setFlash(
                '<span></span>' . __('Payment deleted'),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            '<span></span>' . __('Payment was not deleted'),
            'default',
            array('class' => 'errro-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }
}

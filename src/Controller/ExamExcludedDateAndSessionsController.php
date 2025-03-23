<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ExamExcludedDateAndSessionsController extends AppController
{

    public $name = 'ExamExcludedDateAndSessions';
    public $menuOptions = array(
        'parent' => 'examSchedule',
        'exclude' => array('get_exam_periods_details'),
        'alias' => array(
            'index' => 'List of Exams Excluded Date and Sessions',
            'add' => 'Add Exams Excluded Date and Sessions'
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
        $this->Auth->allow('get_exam_periods_details');
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

        //$this->ExamExcludedDateAndSession->recursive = 0;
        $from_delete = $this->Session->read('from_delete');
        //To display exam excluded date and session data after delection of one recod
        if ($from_delete == 1) {
            $exam_period_id = $this->Session->read('exam_period_id');
            $exam_period_data = $this->ExamExcludedDateAndSession->ExamPeriod->find(
                'first',
                array(
                    'conditions' => array('ExamPeriod.id' => $exam_period_id),
                    'fields' => array(
                        'ExamPeriod.academic_year',
                        'ExamPeriod.semester',
                        'ExamPeriod.program_id',
                        'ExamPeriod.program_type_id'
                    ),
                    'recursive' => -1
                )
            );
            $this->request->data['ExamExcludedDateAndSession']['academic_year'] = $exam_period_data['ExamPeriod']['academic_year'];
            $this->request->data['ExamExcludedDateAndSession']['semester'] = $exam_period_data['ExamPeriod']['semester'];
            $this->request->data['ExamExcludedDateAndSession']['program_id'] = $exam_period_data['ExamPeriod']['program_id'];
            $this->request->data['ExamExcludedDateAndSession']['program_type_id'] = $exam_period_data['ExamPeriod']['program_type_id'];
        }
        $programs = $this->ExamExcludedDateAndSession->ExamPeriod->Program->find('list');
        $programTypes = $this->ExamExcludedDateAndSession->ExamPeriod->ProgramType->find('list');
        $isbeforesearch = 1;
        $this->set(compact('programs', 'programTypes', 'isbeforesearch'));
        if (!empty($this->request->data)) {
            if (!empty($this->request->data['ExamExcludedDateAndSession']['academic_year'])) {
                $examPeriods = $this->ExamExcludedDateAndSession->ExamPeriod->find(
                    'all',
                    array(
                        'conditions' => array(
                            'ExamPeriod.college_id' => $this->college_id,
                            'ExamPeriod.academic_year' => $this->request->data['ExamExcludedDateAndSession']['academic_year'],
                            'ExamPeriod.semester' => $this->request->data['ExamExcludedDateAndSession']['semester'],
                            'ExamPeriod.program_id' => $this->request->data['ExamExcludedDateAndSession']['program_id'],
                            'ExamPeriod.program_type_id' => $this->request->data['ExamExcludedDateAndSession']['program_type_id']
                        ),
                        'contain' => array('YearLevel' => array('fields' => array('YearLevel.name')))
                    )
                );
                $examExcludedDateAndSession_array = array();
                foreach ($examPeriods as $ek => $examPeriod) {
                    $examExcludedDateAndSession_array[$ek] = $this->ExamExcludedDateAndSession->find(
                        'all',
                        array(
                            'conditions' => array('ExamExcludedDateAndSession.exam_period_id' => $examPeriod['ExamPeriod']['id']),
                            'order' => array(
                                'ExamExcludedDateAndSession.excluded_date',
                                'ExamExcludedDateAndSession.session'
                            )
                        )
                    );
                }
                $isbeforesearch = 0;
                $this->set(compact('examExcludedDateAndSession_array', 'examPeriods', 'isbeforesearch'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'Please select the academic year of the exam period that you want to see exam excluded date and sessions.'
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid exam excluded date and session'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('examExcludedDateAndSession', $this->ExamExcludedDateAndSession->read(null, $id));
    }

    public function add()
    {

        $from_delete = $this->Session->read('from_delete');
        if ($from_delete != 1) {
            if ($this->Session->read('exam_period_id')) {
                $this->Session->delete('exam_period_id');
            }
            if ($this->Session->read('selected_academicyear')) {
                $this->Session->delete('selected_academicyear');
            }
            if ($this->Session->read('selected_semester')) {
                $this->Session->delete('selected_semester');
            }
        }
        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            if ($this->Session->read('examPeriods')) {
                $this->Session->delete('examPeriods');
            }
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['ExamExcludedDateAndSession']['academic_year']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please provide final exam period academic year, it is required.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ExamExcludedDateAndSession']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please provide final exam period semester, it is required.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                $examPeriods = $this->ExamExcludedDateAndSession->ExamPeriod->find(
                    'all',
                    array(
                        'conditions' => array(
                            'ExamPeriod.college_id' => $this->college_id,
                            'ExamPeriod.academic_year' => $this->request->data['ExamExcludedDateAndSession']['academic_year'],
                            'ExamPeriod.semester' => $this->request->data['ExamExcludedDateAndSession']['semester']
                        ),
                        'contain' => array(
                            'College' => array('fields' => array('College.name')),
                            'Program' => array('fields' => array('Program.name')),
                            'ProgramType' => array('fields' => array('ProgramType.name')),
                            'YearLevel' => array('fields' => array('YearLevel.name'))
                        )
                    )
                );
                if (empty($examPeriods)) {
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'There is no exam periods to exclude date and sessions in the selected criteria.'
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                } else {
                    $this->Session->write('examPeriods', $examPeriods);
                    $this->set(compact('examPeriods', 'selected_academicyear'));
                }
            }
            $selected_academicyear = $this->request->data['ExamExcludedDateAndSession']['academic_year'];
            $this->Session->write('selected_academicyear', $selected_academicyear);
            $selected_semester = $this->request->data['ExamExcludedDateAndSession']['semester'];
            $this->Session->write('selected_semester', $selected_semester);
            $this->set(compact('selected_academicyear', 'selected_semester'));
        }
        if (!empty($this->request->data) && isset($this->request->data['submit'])) {
            $date_array = $this->ExamExcludedDateAndSession->get_list_of_exam_period_dates(
                $this->request->data['ExamExcludedDateAndSession']['Exam_Period']
            );
            $selected_excluded_session_array = array();
            if (!empty($this->request->data['ExamPeriod']['Selected'])) {
                foreach ($this->request->data['ExamPeriod']['Selected'] as $epsk => $epsv) {
                    if ($epsv != '0') {
                        $explode_data = explode("-", $epsv);
                        $selected_excluded_session_array[$date_array[$explode_data[0]]][] = $explode_data[1];
                    }
                }
            }
            $this->request->data['ExamExcludedDateAndSessions']['exam_period_id'] = $this->request->data['ExamExcludedDateAndSession']['Exam_Period'];
            $count_selected_excluded_session = count($selected_excluded_session_array);
            if ($count_selected_excluded_session != 0) {
                $issave = false;
                foreach ($selected_excluded_session_array as $date_key => $date_value) {
                    $this->request->data['ExamExcludedDateAndSessions']['excluded_date'] = $date_key;
                    foreach ($date_value as $session_key => $session_value) {
                        $this->request->data['ExamExcludedDateAndSessions']['session'] = $session_value;
                        $this->ExamExcludedDateAndSession->create();
                        if ($this->ExamExcludedDateAndSession->save(
                            $this->request->data['ExamExcludedDateAndSessions']
                        )) {
                            $issave = true;
                        }
                    }
                }
                if ($issave == true) {
                    $this->Session->setFlash(
                        '<span></span>' . __('The exam excluded date and session has been saved'),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    //$this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'The exam excluded date and session could not be saved. Please, try again.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('Please check at least 1 session.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
            $this->request->data['search'] = true;
            $selected_academicyear = $this->request->data['ExamExcludedDateAndSession']['academic_year'];
            $this->Session->write('selected_academicyear', $selected_academicyear);
            $selected_semester = $this->request->data['ExamExcludedDateAndSession']['semester'];
            $this->Session->write('selected_semester', $selected_semester);
            $examPeriods = $this->Session->read('examPeriods');
            $selected_exam_period = $this->request->data['ExamExcludedDateAndSession']['Exam_Period'];
            $already_excluded_date_and_session_array = $this->ExamExcludedDateAndSession->get_already_excluded_date_and_session(
                $this->request->data['ExamExcludedDateAndSession']['Exam_Period']
            );
            $examExcludedDateAndSessions = $already_excluded_date_and_session_array[0];
            $excluded_session_by_date = $already_excluded_date_and_session_array[1];
            $this->set(
                compact(
                    'examExcludedDateAndSessions',
                    'excluded_session_by_date',
                    'date_array',
                    'examPeriods',
                    'selected_academicyear',
                    'selected_exam_period',
                    'selected_semester'
                )
            );
        }
        if ($this->Session->read('from_delete')) {
            $examPeriods = $this->Session->read('examPeriods');
            $selected_academicyear = $this->Session->read('selected_academicyear');
            $selected_semester = $this->Session->read('selected_semester');
            $exam_period_id = $this->Session->read('exam_period_id');
            $selected_exam_period = $exam_period_id;
            $date_array = $this->ExamExcludedDateAndSession->get_list_of_exam_period_dates($exam_period_id);
            $already_excluded_date_and_session_array = $this->ExamExcludedDateAndSession->get_already_excluded_date_and_session(
                $exam_period_id
            );
            $examExcludedDateAndSessions = $already_excluded_date_and_session_array[0];
            $excluded_session_by_date = $already_excluded_date_and_session_array[1];
            $this->set(
                compact(
                    'examExcludedDateAndSessions',
                    'excluded_session_by_date',
                    'date_array',
                    'examPeriods',
                    'selected_academicyear',
                    'selected_exam_period',
                    'selected_semester'
                )
            );
            $this->Session->delete('from_delete');
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid exam excluded date and session'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->ExamExcludedDateAndSession->save($this->request->data)) {
                $this->Session->setFlash(__('The exam excluded date and session has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    __('The exam excluded date and session could not be saved. Please, try again.')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->ExamExcludedDateAndSession->read(null, $id);
        }
        $examPeriods = $this->ExamExcludedDateAndSession->ExamPeriod->find('list');
        $this->set(compact('examPeriods'));
    }

    public function delete($id = null, $from = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span> ' . __('Invalid id for exam excluded date and session.'),
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
        $exam_period_id = $this->ExamExcludedDateAndSession->field(
            'ExamExcludedDateAndSession.exam_period_id',
            array('ExamExcludedDateAndSession.id' => $id)
        );
        $this->Session->write('exam_period_id', $exam_period_id);
        //TODO: Before deleting exam excluded date and session first check whether there is any exam schedule on this period or not. If there is exam schedule deny deletion of this deleting exam excluded date and session. promote to delete exam schedule before deletion of this deleting exam excluded date and session.
        if ($this->ExamExcludedDateAndSession->delete($id)) {
            $this->Session->setFlash(
                '<span></span> ' . __('Exam excluded date and session deleted.'),
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
            '<span></span> ' . __('Exam excluded date and session was not deleted.'),
            'default',
            array('class' => 'error-box error-message')
        );
        if (empty($from)) {
            return $this->redirect(array('action' => 'index'));
        } else {
            return $this->redirect(array('action' => 'add'));
        }
    }

    public function get_exam_periods_details($exam_period_id = null)
    {

        if (!empty($exam_period_id)) {
            $this->layout = 'ajax';
            $date_array = $this->ExamExcludedDateAndSession->get_list_of_exam_period_dates($exam_period_id);

            $already_excluded_date_and_session_array = $this->ExamExcludedDateAndSession->get_already_excluded_date_and_session(
                $exam_period_id
            );
            $examExcludedDateAndSessions = $already_excluded_date_and_session_array[0];
            $excluded_session_by_date = $already_excluded_date_and_session_array[1];
            $this->set(compact('date_array', 'examExcludedDateAndSessions', 'excluded_session_by_date'));
        }
    }
}

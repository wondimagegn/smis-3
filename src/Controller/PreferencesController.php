<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Utility\Hash;
use Cake\Collection\Collection;

class PreferencesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->loadComponent('AcademicYear');
        // Configure pagination
        $this->paginate = [
            'Preferences' => [
                'limit' => 20,
                'order' => ['Preferences.id' => 'ASC']
            ]
        ];
    }

    public function beforeRender(\Cake\Event\EventInterface $event): void
    {
        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 12, date('Y'));
        $this->set(compact('acyear_array_data'));
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Allow public actions
        $this->Auth->allow(['get_preference', 'getStudentPreference']);
    }

    protected function __init_search()
    {
        $session = $this->request->getSession();
        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('Preference'))) {
            $searchSession = $this->request->getData('Preference');
            $session->write('search_data', $searchSession);
        } else {
            $searchSession = $session->read('search_data');
            $this->request = $this->request->withData('Preference', $searchSession);
        }
    }

    protected function __init_search_feed()
    {
        $session = $this->request->getSession();
        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('Search'))) {
            $searchSession = $this->request->getData('Search');
            $session->write('search_data_feed', $searchSession);
        } else {
            $searchSession = $session->read('search_data_feed');
            $this->request = $this->request->withData('Search', $searchSession);
        }
    }

    public function index($academic_year = null, $suffix = null)
    {
        $session = $this->request->getSession();
        $conditions = [];

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('Preference.viewPDF'))) {
            $searchSession = $session->read('search_data');
            $this->request = $this->request->withData('Preference', $searchSession);
        }

        if ($this->request->getQuery('page')) {
            $this->__init_search();
            $this->request = $this->request->withData('Preference.page', $this->request->getQuery('page'));
            $this->__init_search();
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('Preference.listStudentsPreference'))) {
            $this->__init_search();
        }

        // Build conditions
        if (!empty($this->request->getData('Preference.academicyear'))) {
            $conditions['Preferences.academicyear'] = $this->request->getData('Preference.academicyear');
        }

        if (!empty($this->request->getData('Preference.limit'))) {
            $this->paginate['Preferences']['limit'] = $this->request->getData('Preference.limit');
        }

        if (!empty($this->request->getData('Preference.preferences_order'))) {
            $conditions['Preferences.preferences_order'] = $this->request->getData('Preference.preferences_order');
        }

        if (!empty($this->request->getData('Preference.department_id'))) {
            $conditions['Preferences.department_id'] = $this->request->getData('Preference.department_id');
        }

        // Student-specific logic
        if ($this->Auth->user('role_id') === ROLE_STUDENT) {
            $acceptedStudent = $this->Preferences->AcceptedStudents->find()
                ->where(['AcceptedStudents.user_id' => $this->Auth->user('id')])
                ->contain(['Students'])
                ->first();

            if ($acceptedStudent) {
                $conditions['Preferences.accepted_student_id'] = $acceptedStudent->id;
                $preferenceDeadline = TableRegistry::getTableLocator()->get('PreferenceDeadlines')->find()
                    ->where([
                        'PreferenceDeadlines.college_id' => $acceptedStudent->college_id,
                        'PreferenceDeadlines.academicyear LIKE' => $acceptedStudent->academicyear . '%'
                    ])
                    ->first();
                $prefCount = $this->Preferences->find()
                    ->where(['Preferences.user_id' => $acceptedStudent->user_id])
                    ->count();
                $this->set(compact('preferenceDeadline', 'prefCount'));
            }
        }

        $this->paginate['Preferences']['conditions'] = $conditions;
        $preferences = $conditions ? $this->paginate('Preferences') : [];

        if (empty($preferences) && $this->request->is(['post', 'put']) && !empty($this->request->getData())) {
            $this->Flash->info(__('No result found in the given criteria.'), ['element' => 'info']);
        }

        $programs = $this->Preferences->AcceptedStudents->Programs->find('list')->toArray();
        $programTypes = $this->Preferences->AcceptedStudents->ProgramTypes->find('list')->toArray();
        $departments = !empty($this->request->getData('Preference.academicyear'))
            ? $this->_getParticipatingDepartment($this->college_id, $this->request->getData('Preference.academicyear'))
            : $this->_getParticipatingDepartment($this->college_id, $this->AcademicYear->currentAademicYear());

        $this->set(compact('programs', 'programTypes', 'departments', 'preferences'));
    }

    public function view($id = null)
    {
        if (!$id) {
            $this->Flash->error(__('Invalid preference.'), ['element' => 'error']);
            return $this->redirect(['action' => 'index']);
        }

        $preference = $this->Preferences->findById($id)->first();
        if (!$preference) {
            $this->Flash->error(__('Invalid preference.'), ['element' => 'error']);
            return $this->redirect(['action' => 'index']);
        }

        $this->set(compact('preference'));
    }

    public function add($accepted_student_id = null)
    {
        $session = $this->request->getSession();
        $user = $this->Auth->user();
        $logged_user_detail = TableRegistry::getTableLocator()->get('Users')->find()
            ->where(['Users.id' => $user['id']])
            ->contain(['Staffs', 'Students'])
            ->first();

        if ($session->check('search_data')) {
            $this->request = $this->request->withData('searchacademicyear', true);
            $this->request = $this->request->withData('Search', $session->read('search_data'));
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('searchacademicyear'))) {
            if (!empty($this->request->getData('Search.academicyear'))) {
                $this->__init_search_feed();
                $selectedAcademicYear = $this->request->getData('Search.academicyear');
                $conditions = [
                    'AcceptedStudents.academicyear' => $selectedAcademicYear,
                    'AcceptedStudents.college_id' => $this->college_id,
                    'AcceptedStudents.department_id IS' => null,
                    'AcceptedStudents.program_type_id' => PROGRAM_TYPE_REGULAR,
                    'AcceptedStudents.program_id' => PROGRAM_UNDEGRADUATE,
                    'OR' => [
                        'AcceptedStudents.placementtype IS' => null,
                        'AcceptedStudents.placementtype' => CANCELLED_PLACEMENT
                    ]
                ];

                if (!empty($accepted_student_id)) {
                    $conditions['AcceptedStudents.id !='] = $accepted_student_id;
                }

                $acceptedStudents = $this->Preferences->AcceptedStudents->find()
                    ->where($conditions)
                    ->order(['AcceptedStudents.full_name' => 'ASC'])
                    ->contain(['Colleges', 'Programs', 'ProgramTypes', 'Preferences'])
                    ->all()
                    ->toArray();

                $preference_not_completed = array_filter($acceptedStudents, function ($student) {
                    return empty($student->preferences);
                });

                if (empty($preference_not_completed) && empty($accepted_student_id)) {
                    $this->Flash->error(__('No student in the selected academic year needs preference feeding.'), ['element' => 'error']);
                }

                $this->set(compact('selectedAcademicYear', 'preference_not_completed'));
            } else {
                $this->Flash->error(__('Please select academic year to add department preference.'), ['element' => 'error']);
            }
        }

        if (!empty($accepted_student_id)) {
            if ($user['role_id'] !== ROLE_STUDENT) {
                $valid = $this->Preferences->AcceptedStudents->find()
                    ->where(['AcceptedStudents.id' => $accepted_student_id])
                    ->count();

                $field_academic_year = $this->Preferences->AcceptedStudents->field('academicyear', ['AcceptedStudents.id' => $accepted_student_id]);

                if ($valid) {
                    $eligible_user = $this->Preferences->AcceptedStudents->find()
                        ->where([
                            'AcceptedStudents.id' => $accepted_student_id,
                            'AcceptedStudents.college_id' => $this->college_id,
                            'AcceptedStudents.department_id IS' => null
                        ])
                        ->count();

                    if ($eligible_user === 0) {
                        $details = '';
                        if (!empty($logged_user_detail->staffs)) {
                            $details .= $logged_user_detail->staffs[0]->first_name . ' ' .
                                $logged_user_detail->staffs[0]->middle_name . ' ' .
                                $logged_user_detail->staffs[0]->last_name . ' (' .
                                $logged_user_detail->username . ')';
                        } elseif (!empty($logged_user_detail->students)) {
                            $details .= $logged_user_detail->students[0]->first_name . ' ' .
                                $logged_user_detail->students[0]->middle_name . ' ' .
                                $logged_user_detail->students[0]->last_name . ' (' .
                                $logged_user_detail->username . ')';
                        }

                        $autoMessageTable = TableRegistry::getTableLocator()->get('AutoMessages');
                        $autoMessageTable->sendPermissionManagementBreakAttempt(
                            Configure::read('User.user'),
                            '<u>' . $details . '</u> is trying to feed student placement preference without assigned privilege.'
                        );

                        $this->Flash->error(__('You do not have the privilege to feed the selected student preference.'), ['element' => 'error']);
                        return $this->redirect(['action' => 'add']);
                    } else {
                        $accepted_student_detail = $this->Preferences->AcceptedStudents->find()
                            ->where(['AcceptedStudents.id' => $accepted_student_id])
                            ->first();
                        $this->set(compact('accepted_student_detail', 'accepted_student_id'));
                    }
                }
            }

            $departments = $this->_getParticipatingDepartment($this->college_id, $field_academic_year);
            if ($departments) {
                $departmentcount = count($departments);
                $this->set(compact('departments', 'departmentcount'));
            } else {
                $this->Flash->error(__('Please first add placement participating departments.'), ['element' => 'error']);
                return $this->redirect(['controller' => 'ParticipatingDepartments', 'action' => 'add']);
            }
        }

        if ($this->request->is(['post', 'put']) && !empty($this->request->getData('submitpreference'))) {
            $academicyear = $this->request->getData('Preference.academicyear');
            $accepted_student_id = $this->request->getData('Preference.accepted_student_id');

            // Check if auto placement is running
            $placementLockTable = TableRegistry::getTableLocator()->get('PlacementLocks');
            $auto_run = $placementLockTable->find()
                ->where([
                    'PlacementLocks.college_id' => $this->college_id,
                    'PlacementLocks.academic_year' => $academicyear,
                    'PlacementLocks.process_start' => 1
                ])
                ->count();

            if ($auto_run) {
                $this->Flash->error(__('The auto placement is running. You cannot modify preferences now.'), ['element' => 'error']);
                return $this->redirect(['action' => 'add']);
            }

            // Reformat data
            $preferencesData = $this->request->getData('Preference');
            foreach ($preferencesData as &$value) {
                if (!isset($value['academicyear'])) {
                    $value['academicyear'] = $academicyear ?: $this->AcademicYear->current_academicyear();
                }
                if (!isset($value['college_id'])) {
                    $value['college_id'] = $this->college_id ?: TableRegistry::getTableLocator()->get('ParticipatingDepartments')
                        ->field('college_id', ['department_id' => $value['department_id']]);
                }
                if (!isset($value['user_id'])) {
                    $value['user_id'] = $user['id'];
                }
            }

            // Remove unnecessary data
            $this->request = $this->request->withData('Preference.accepted_student_id', null);
            $this->request = $this->request->withData('Preference.academicyear', null);
            $this->request = $this->request->withData('Preference.college_id', null);

            // Check preference deadline
            $preferenceDeadlineTable = TableRegistry::getTableLocator()->get('PreferenceDeadlines');
            $isPreferenceDeadlineRecorded = $preferenceDeadlineTable->find()
                ->where([
                    'PreferenceDeadlines.college_id' => $this->college_id,
                    'PreferenceDeadlines.academicyear LIKE' => $academicyear . '%'
                ])
                ->count();

            if ($isPreferenceDeadlineRecorded) {
                $is_deadline_passed = $preferenceDeadlineTable->find()
                    ->where([
                        'PreferenceDeadlines.college_id' => $this->college_id,
                        'PreferenceDeadlines.academicyear LIKE' => $academicyear . '%',
                        'PreferenceDeadlines.deadline <' => date('Y-m-d H:i:s')
                    ])
                    ->count();

                if (!$is_deadline_passed) {
                    if (!$this->Preferences->isAlreadyEnteredPreference($accepted_student_id) &&
                        $this->Preferences->isAllPreferenceDepartmentSelectedDifferent($preferencesData)) {
                        if (!empty($preferencesData)) {
                            if ($this->Preferences->saveMany($this->Preferences->newEntities($preferencesData), ['validate' => true])) {
                                $this->Flash->success(__('The preference has been saved.'), ['element' => 'success']);
                            } else {
                                $this->Flash->error(__('The preference could not be saved. Please try again.'), ['element' => 'error']);
                            }
                        }
                    } else {
                        $errors = $this->Preferences->getErrors();
                        if (!empty($errors['preference'])) {
                            $this->Flash->error($errors['preference'][0], ['element' => 'error']);
                        } elseif (!empty($errors['alreadypreferencerecorded'])) {
                            $this->Flash->error($errors['alreadypreferencerecorded'][0], ['element' => 'error']);
                        } elseif (!empty($errors['department'])) {
                            $this->Flash->error($errors['department'][0], ['element' => 'error']);
                        } else {
                            $this->Flash->error(__('Please fill the input fields.'), ['element' => 'error']);
                        }
                    }
                } else {
                    $this->Flash->error(__('Preference deadline has passed. You cannot record preferences.'), ['element' => 'error']);
                }
            } else {
                $this->Flash->error(__('Please set preference deadline first.'), ['element' => 'error']);
                return $this->redirect(['controller' => 'PreferenceDeadlines', 'action' => 'add']);
            }
        }

        $this->set(['user_id' => $user['id']]);
    }

    public function student_record_preference()
    {
        $user = $this->Auth->user();
        $acceptedStudent = $this->Preferences->AcceptedStudents->find()
            ->where(['AcceptedStudents.user_id' => $user['id']])
            ->contain(['Colleges'])
            ->first();

        if (!$acceptedStudent) {
            $this->Flash->error(__('Student record not found.'), ['element' => 'error']);
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['post', 'put'])) {
            $preferenceDeadlineTable = TableRegistry::getTableLocator()->get('PreferenceDeadlines');
            $is_preference_deadline = $preferenceDeadlineTable->find()
                ->where([
                    'PreferenceDeadlines.college_id' => $acceptedStudent->college_id,
                    'PreferenceDeadlines.academicyear LIKE' => $this->AcademicYear->current_academicyear() . '%',
                    'PreferenceDeadlines.deadline >' => date('Y-m-d H:i:s')
                ])
                ->count();

            if ($is_preference_deadline) {
                $preferencesData = $this->request->getData('Preference');
                $this->set($this->request->getData());

                if ($this->Preferences->validate($preferencesData)) {
                    if (!$this->Preferences->isAlreadyEnteredPreference($preferencesData[1]['accepted_student_id'])) {
                        if ($this->Preferences->isAllPreferenceDepartmentSelectedDifferent($preferencesData)) {
                            if ($this->Preferences->saveMany($this->Preferences->newEntities($preferencesData), ['validate' => true])) {
                                $this->Flash->success(__('The preference has been saved.'), ['element' => 'success']);
                                return $this->redirect(['action' => 'index']);
                            } else {
                                $this->Flash->error(__('The preference could not be saved. Please try again.'), ['element' => 'error']);
                            }
                        } else {
                            $this->Flash->error(__('Please select different department preferences for each order.'), ['element' => 'error']);
                        }
                    } else {
                        $this->Flash->error(__('You have already entered your preference. Please edit before the deadline.'), ['element' => 'error']);
                        return $this->redirect(['action' => 'index']);
                    }
                } else {
                    $this->Flash->error(__('Please enter the input correctly.'), ['element' => 'error']);
                }
            } else {
                $this->Flash->error(__('Preference deadline has passed. Contact the college dean.'), ['element' => 'error']);
                return $this->redirect(['action' => 'index']);
            }
        }

        if ($this->Preferences->isAlreadyEnteredPreference($user['id'])) {
            $this->Flash->error(__('You have already entered your preference.'), ['element' => 'error']);
            return $this->redirect(['action' => 'index']);
        }

        $departments = $this->_participating_department_name($acceptedStudent->college_id, null);
        if ($departments) {
            $departmentcount = count($departments);
            $this->set(compact('departments', 'departmentcount'));
        } else {
            $this->Flash->info(__('Please come back when the college announces preference filling.'), ['element' => 'info']);
            return $this->redirect(['action' => 'index']);
        }

        $studentData = [
            'studentname' => $acceptedStudent->full_name,
            'studentnumber' => $acceptedStudent->studentnumber,
            'acyear' => $acceptedStudent->academicyear,
            'collegename' => $acceptedStudent->college->name,
            'college_id' => $acceptedStudent->college_id,
            'accepted_student_id' => $acceptedStudent->id
        ];
        $this->set($studentData);
    }

    public function edit($id = null)
    {
        $user = $this->Auth->user();

        if (!$id && !$this->request->is(['post', 'put'])) {
            $this->Flash->error(__('Invalid preference.'), ['element' => 'error']);
            return $this->redirect(['action' => 'index']);
        }

        if (!$this->request->is(['post', 'put'])) {
            $preferences = $this->Preferences->find()
                ->where(['Preferences.user_id' => $user['id']])
                ->all()
                ->toArray();
            $this->request = $this->request->withData('Preference', $preferences);
        }

        $departments = $this->_participating_department_name($this->college_id, null);
        if ($departments) {
            $departmentcount = count($departments);
            $this->set(compact('departments', 'departmentcount'));
        } else {
            $this->Flash->info(__('Please first add placement participating departments.'), ['element' => 'info']);
            return $this->redirect(['controller' => 'ParticipatingDepartments', 'action' => 'add']);
        }

        $this->set(['user_id' => $user['id']]);
    }

    public function delete($id = null)
    {
        if (!$id) {
            $this->Flash->error(__('Invalid id for preference.'), ['element' => 'error']);
            return $this->redirect(['action' => 'index']);
        }

        $preference = $this->Preferences->findById($id)->first();
        if (!$preference) {
            $this->Flash->error(__('Invalid preference.'), ['element' => 'error']);
            return $this->redirect(['action' => 'index']);
        }

        $autoPlacementCount = $this->Preferences->AcceptedStudents->find()
            ->where([
                'AcceptedStudents.placementtype' => AUTO_PLACEMENT,
                'AcceptedStudents.id' => $preference->accepted_student_id
            ])
            ->count();

        if ($autoPlacementCount > 0) {
            $this->Flash->error(__('Preference cannot be deleted. The student has been placed based on this preference.'), ['element' => 'error']);
        } else {
            $preferenceIds = $this->Preferences->find()
                ->where(['Preferences.accepted_student_id' => $preference->accepted_student_id])
                ->extract('id')
                ->toArray();

            if ($this->Preferences->deleteAll(['Preferences.id IN' => $preferenceIds])) {
                $this->Flash->success(__('Preferences deleted.'), ['element' => 'success']);
            } else {
                $this->Flash->error(__('Preferences could not be deleted.'), ['element' => 'error']);
            }
        }

        return $this->redirect(['action' => 'index']);
    }

    public function get_preference($participationg_department_id = null)
    {
        $this->viewBuilder()->setLayout('ajax');
        $remaining_departments = $this->_participating_department_name($this->college_id, $participationg_department_id);
        $this->set(compact('remaining_departments'));
        $this->set('_serialize', ['remaining_departments']);
    }

    public function getStudentPreference($acceptedStudentId = null)
    {
        if (!$acceptedStudentId || !is_numeric($acceptedStudentId)) {
            throw new BadRequestException('Invalid student ID');
        }

        $this->viewBuilder()->setLayout('ajax');

        $studentBasic = $this->Preferences->AcceptedStudents->find()
            ->where(['AcceptedStudents.id' => $acceptedStudentId])
            ->first()
            ->toArray();


        debug($studentBasic);

        $studentsPreference = $this->Preferences->find()
            ->where(['Preferences.accepted_student_id' => $acceptedStudentId])
            ->contain(['AcceptedStudents', 'Departments'])
            ->all()
            ->toArray();


        debug($studentsPreference);

        if (!$studentBasic && empty($studentsPreference)) {
            throw new NotFoundException('No data found for student ID: ' . $acceptedStudentId);
        }

        $this->set(compact('studentsPreference', 'studentBasic'));
        $this->set('_serialize', ['studentsPreference', 'studentBasic']);
    }

    protected function _participating_department_name($college_id = null, $department_id = null)
    {
        $participatingDepartmentTable = TableRegistry::getTableLocator()->get('ParticipatingDepartments');
        $conditions = [
            'ParticipatingDepartments.college_id' => $college_id,
            'ParticipatingDepartments.academic_year LIKE' => $this->AcademicYear->current_academicyear() . '%'
        ];

        if ($department_id) {
            $conditions['ParticipatingDepartments.department_id'] = $department_id;
        }

        $departments = $participatingDepartmentTable->find()
            ->where($conditions)
            ->contain(['Departments'])
            ->all()
            ->toArray();

        if (!empty($departments)) {
            $participatingdepartmentname = [];
            foreach ($departments as $department) {
                if (!empty($department['department'])) {
                    $participatingdepartmentname[$department['department']['id']] = $department['department']['name'];
                }
            }
            return $participatingdepartmentname;
        }

        return false;
    }

    protected function _getParticipatingDepartment($college_id = null, $academic_year = null)
    {
        $participatingDepartmentTable = TableRegistry::getTableLocator()->get('ParticipatingDepartments');
        $departments = $participatingDepartmentTable->find()
            ->where([
                'ParticipatingDepartments.college_id' => $college_id,
                'ParticipatingDepartments.academic_year LIKE' => $academic_year . '%'
            ])
            ->contain(['Colleges', 'Departments'])
            ->all()
            ->toArray();

        $department_lists = [];
        foreach ($departments as $department) {
            $department_lists[$department['department']['id']] = $department['department']['name'];
        }

        return $department_lists;
    }

    public function edit_preference($accepted_student_id = null)
    {
        $user = $this->Auth->user();

        if ($this->request->is(['post', 'put'])) {
            $this->set($this->request->getData());
            if ($this->Preferences->validate($this->request->getData())) {
                $userCanEdit = null;
                $college_id = null;
                $preferenceDetails = $this->Preferences->find()
                    ->where(['Preferences.user_id' => $user['id']])
                    ->first();

                if ($user['role_id'] != ROLE_STUDENT) {
                    $userCanEdit = $this->Preferences->find()
                        ->where([
                            'OR' => [
                                'Preferences.college_id' => $this->college_id,
                                'Preferences.user_id' => $user['id']
                            ]
                        ])
                        ->first();
                    $college_id = $this->college_id;
                } else {
                    $accepted_student_college = $this->Preferences->AcceptedStudents->find()
                        ->where(['AcceptedStudents.user_id' => $user['id']])
                        ->contain(['Colleges'])
                        ->first();
                    $college_id = $accepted_student_college->college->id;
                    $userCanEdit = $this->Preferences->find()
                        ->where(['Preferences.user_id' => $user['id']])
                        ->first();
                }

                $accepted_student_id = !empty($userCanEdit) ? $userCanEdit->accepted_student_id : '';

                if ($this->Preferences->isAllPreferenceDepartmentSelectedDifferent($this->request->getData('Preference'))) {
                    if ($userCanEdit) {
                        $preferenceDeadlineTable = TableRegistry::getTableLocator()->get('PreferenceDeadlines');
                        $is_preference_deadline = $preferenceDeadlineTable->find()
                            ->where([
                                'PreferenceDeadlines.college_id' => $college_id,
                                'PreferenceDeadlines.academicyear LIKE' => $this->AcademicYear->current_academicyear() . '%',
                                'PreferenceDeadlines.deadline >' => date('Y-m-d H:i:s')
                            ])
                            ->count();

                        if ($is_preference_deadline) {
                            if ($this->Preferences->saveMany($this->Preferences->newEntities($this->request->getData('Preference')))) {
                                $this->Flash->success(__('The preference has been updated.'), ['element' => 'success']);
                            } else {
                                $this->Flash->error(__('The preference could not be updated. Please try again.'), ['element' => 'error']);
                                return $this->redirect(['action' => 'edit_preference', $accepted_student_id]);
                            }
                        } else {
                            $this->Flash->error(__('Preference deadline has passed. Contact the college dean.'), ['element' => 'error']);
                        }
                    } else {
                        $this->Flash->warning(__('You are not allowed to edit someone\'s preference. This action will be reported.'), ['element' => 'warning']);
                    }
                } else {
                    $this->Flash->error(__('Please select different department preferences for each order.'), ['element' => 'error']);
                    return $this->redirect(['action' => 'edit_preference', $accepted_student_id]);
                }
            } else {
                $this->Flash->error(__('Please select department.'), ['element' => 'error']);
                return $this->redirect(['action' => 'edit_preference', $accepted_student_id]);
            }
            return $this->redirect(['action' => 'index']);
        } else {
            $college_id = $user['role_id'] != ROLE_STUDENT
                ? $this->college_id
                : ($this->Preferences->AcceptedStudents->find()
                    ->where(['AcceptedStudents.user_id' => $user['id']])
                    ->contain(['Colleges'])
                    ->first()->college->id ?? null);

            $preferences = $this->Preferences->find()
                ->where([
                    'Preferences.accepted_student_id' => $accepted_student_id,
                    'Preferences.college_id' => $college_id
                ])
                ->contain(['Colleges', 'AcceptedStudents', 'Departments'])
                ->all()
                ->toArray();

            $this->request = $this->request->withData('Preference', (new Collection($this->Preferences->find()
                ->where(['Preferences.accepted_student_id' => $accepted_student_id])
                ->order(['Preferences.preferences_order' => 'ASC'])
                ->all()))->combine('{n}.id', '{n}')->toArray());

            foreach ($preferences as $value) {
                foreach ($this->request->getData('Preference') as &$data) {
                    if ($data['department_id'] == $value['department_id']) {
                        $data['department_name'] = $value['department']['name'];
                        break;
                    }
                }
                if (!empty($value['college']['name'])) {
                    $this->set('college_name', $value['college']['name']);
                }
                if (!empty($value['accepted_student']['full_name'])) {
                    $this->set('student_full_name', $value['accepted_student']['full_name']);
                }
            }

            $departments = $this->_participating_department_name($college_id, null);
            if ($departments) {
                $departmentcount = count($departments);
                $this->set(compact('departments', 'departmentcount'));
            } else {
                $this->Flash->info(__('Please first add placement participating departments.'), ['element' => 'info']);
                return $this->redirect(['controller' => 'ParticipatingDepartments', 'action' => 'add']);
            }
        }
    }
}

<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\I18n\FrozenTime;
use Cake\Mailer\Email;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\Routing\Router;
use Cake\View\Exception\MissingViewException;
use Cake\Http\Exception\NotFoundException;

class PagesController extends AppController
{
    public $menuOptions = [
        'parent' => 'dashboard',
        'exclude' => [
            'academicCalender',
            'announcement',
            'officialTranscriptRequest',
            'officialRequestTracking',
            'onlineAdmissionTracking',
            'admission',
            'checkGraduate',
            'checkRemedialResult',
            'checkCampusPlacement',
            'getDepartmentCombo'
        ]
    ];

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('Paginator');
        $this->loadComponent('AcademicYear');
        $this->loadComponent('MathCaptcha');
        $this->Auth->allow([
            'academicCalender',
            'announcement',
            'officialTranscriptRequest',
            'officialRequestTracking',
            'onlineAdmissionTracking',
            'admission',
            'checkGraduate',
            'checkRemedialResult',
            'checkCampusPlacement',
            'getDepartmentCombo'
        ]);
    }

    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        parent::beforeRender($event);

        $acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 1, date('Y'));
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->viewBuilder()->setLayout('page-alternative');
    }

    public function display()
    {
        $this->viewBuilder()->setLayout('default-e');

        $path = $this->request->getParam('pass');
        $count = count($path);

        if (!$count) {
            return $this->redirect('/');
        }

        $page = $subpage = $title_for_layout = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        if (!empty($path[$count - 1])) {
            $title_for_layout = Inflector::humanize($path[$count - 1]);
        }

        $this->set(compact('page', 'subpage', 'title_for_layout'));

        try {
            $this->render(implode('/', $path));
        } catch (MissingViewException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }

    public function academicCalender()
    {
        $academicCalendars = [];
        $programs = TableRegistry::getTableLocator()->get('Programs')->find('list')->toArray();
        $programTypes = TableRegistry::getTableLocator()->get('ProgramTypes')->find('list')->toArray();

        debug($this->request->getData());

        if ($this->request->is('post') && !empty($this->request->getData('viewAcademicCalendar'))) {
            $options = [];

            $data = $this->request->getData('Search');

            debug($data);
            if (!empty($data['program_id'])) {
                $options['AcademicCalendars.program_id'] = $data['program_id'];
            }
            if (!empty($data['program_type_id'])) {
                $options['AcademicCalendars.program_type_id'] = $data['program_type_id'];
            }
            if (!empty($data['department_id'])) {
                $options['AcademicCalendars.department_id LIKE'] = '%s:_:"' . $data['department_id'] . '"%';
            }
            if (!empty($data['academic_year'])) {
                $options['AcademicCalendars.academic_year'] = $data['academic_year'];
            }
            if (!empty($data['semester'])) {
                $options['AcademicCalendars.semester'] = $data['semester'];
            }

            $academicCalendars = TableRegistry::getTableLocator()->get('AcademicCalendars')
                ->find('all')
                ->where($options)
                ->contain(['Programs', 'ProgramTypes'])
                ->toArray();

            debug($academicCalendars);

            if (empty($academicCalendars)) {
                $this->Flash->info('There is no academic calendar defined in the system in the given criteria.');
            } else {
                foreach ($academicCalendars as $calendar) {
                    $department_ids = unserialize($calendar->department_id);
                    $year_level_ids = unserialize($calendar->year_level_id);
                    debug($year_level_ids);
                    $college_ids_found = [];

                    if (!empty($department_ids)) {
                        foreach ($department_ids as $dpt) {
                            $parts = explode('pre_', $dpt);
                            if (count($parts) > 1) {
                                $college_ids_found[] = $parts[1];
                            }
                        }

                        if (!empty($college_ids_found)) {
                            $collegeNames = TableRegistry::getTableLocator()->get('Colleges')
                                ->find('list')
                                ->where(['Colleges.id IN' => $college_ids_found])
                                ->toArray();
                            $calendar->department_name = implode(', ', $collegeNames);
                            $calendar->year_name = 'Pre/Freshman';
                        } else {
                            $deptNames = TableRegistry::getTableLocator()->get('Departments')
                                ->find('list')
                                ->where(['Departments.id IN' => $department_ids])
                                ->toArray();
                            $calendar->department_name = implode(', ', $deptNames);
                            $calendar->year_name = implode(', ', $year_level_ids);

                        }
                    }
                }
            }
            debug($academicCalendars);
        }

        $this->set(compact('academicCalendars', 'programs', 'programTypes'));
    }

    public function announcement()
    {
        $announcements = TableRegistry::getTableLocator()->get('Announcements')
            ->getNotExpiredAnnouncements();
        $this->set(compact('announcements'));
    }

    public function officialTranscriptRequest()
    {
        $this->viewBuilder()->setLayout('login');

        $officialTranscriptRequestTable = TableRegistry::getTableLocator()->get('OfficialTranscriptRequests');
        $trackingnumber = $officialTranscriptRequestTable->nextTrackingNumber();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['OfficialTranscriptRequest']['trackingnumber'] = $trackingnumber;

            $officialTranscriptRequestTable->patchEntity($this->OfficialTranscriptRequest, $data, ['validate' => true]);

            if ($officialTranscriptRequestTable->save($this->OfficialTranscriptRequest)) {
                $this->Flash->success("The official transcript request has been forwarded. Your tracking number is <strong>{$trackingnumber}</strong>.");
                return $this->redirect(['action' => 'official_request_tracking']);
            } else {
                $this->Flash->error('The official transcript request could not be saved.');
            }
        }

        $admissiontypes = TableRegistry::getTableLocator()->get('ProgramTypes')
            ->find('list', ['keyField' => 'name', 'valueField' => 'name'])
            ->toArray();

        $degreetypes = [
            'Bachelor of Arts' => 'Bachelor of Arts',
            'Bachelor of Science' => 'Bachelor of Science',
            'Doctor of Medicine' => 'Doctor of Medicine',
            'Master of Science' => 'Master of Science',
            'Master of Arts' => 'Master of Arts',
            'Doctor of Philosophy' => 'Doctor of Philosophy'
        ];

        $this->set(compact('admissiontypes', 'degreetypes', 'trackingnumber'));
    }

    public function officialRequestTracking()
    {
        $request = null;
        $statuses = [
            'request_verified' => 'Request Verified',
            'request_cancelled' => 'Request Cancelled',
            'document_sent' => 'Document Sent To Destination'
        ];

        if ($this->request->is('post') && !empty($this->request->getData('OfficialTranscriptRequest.trackingnumber'))) {
            $trackingnumber = trim($this->request->getData('OfficialTranscriptRequest.trackingnumber'));

            $request = TableRegistry::getTableLocator()->get('OfficialTranscriptRequests')
                ->find('first')
                ->where(['OfficialTranscriptRequests.trackingnumber' => $trackingnumber])
                ->contain(['OfficialRequestStatuses'])
                ->first();

            if (!$request) {
                $this->Flash->warning('The tracking number provided is not valid or request cancelled.');
            }
        }

        $this->set(compact('request', 'statuses'));
    }

    public function onlineAdmissionTracking()
    {
        $request = null;
        $statuses = ['0' => 'Pending', '1' => 'Approved', '-1' => 'Rejected'];

        if ($this->request->is('post') && !empty($this->request->getData('OnlineApplicant.trackingnumber'))) {
            $trackingnumber = trim($this->request->getData('OnlineApplicant.trackingnumber'));
            $onlineApplicantTable = TableRegistry::getTableLocator()->get('OnlineApplicants');

            $request = $onlineApplicantTable->find()
                ->where(['OnlineApplicants.applicationnumber' => $trackingnumber])
                ->contain(['OnlineApplicantStatuses', 'Programs', 'ProgramTypes', 'Departments', 'Colleges'])
                ->first();

            // Handle file upload update
            if ($request && !empty($this->request->getData('Attachment.1.file.name'))) {
                $data = $onlineApplicantTable->preparedAttachment($this->request->getData());
                $data['OnlineApplicant'] = $request->toArray();

                if (!empty($request->attachments[1])) {
                    $data['Attachment'][1]['id'] = $request->attachments[1]->id;
                    $data['Attachment'][1]['foreign_key'] = $request->attachments[1]->foreign_key;
                }

                if ($onlineApplicantTable->saveAssociated($data)) {
                    $this->Flash->success("Payment slip updated for application #{$request->applicationnumber}.");
                }
            }

            if (!$request) {
                $this->Flash->info('The application number is invalid or request cancelled.');
            }
        }

        $this->set(compact('request', 'statuses'));
    }

    public function admission()
    {
        $onlineApplicantTable = TableRegistry::getTableLocator()->get('OnlineApplicants');
        $applicationnumber = $onlineApplicantTable->nextTrackingNumber();

        $academicCalendar = TableRegistry::getTableLocator()->get('AcademicCalendars')
            ->find()
            ->where(['AcademicCalendars.online_admission_end_date >=' => FrozenTime::now()])
            ->first();

        $departments = $colleges = $programs = $programTypes = $acyeardatas = $semester = [];

        if ($academicCalendar) {
            $departmentIds = unserialize($academicCalendar->department_id);
            $programIds = [$academicCalendar->program_id];
            $programTypesIds = [$academicCalendar->program_type_id];

            $departments = TableRegistry::getTableLocator()->get('Departments')
                ->find('list')
                ->where(['Departments.id IN' => $departmentIds])
                ->toArray();

             $college_ids = TableRegistry::getTableLocator()->get('Departments')
                ->find('list', ['keyField' => 'id', 'valueField' => 'college_id'])
                ->where(['Departments.id IN' => $departmentIds])
                ->toArray();

            $colleges = TableRegistry::getTableLocator()->get('Colleges')
                ->find('list')
                ->where(['Colleges.id IN' => array_values($college_ids)])
                ->toArray();

            $programs = TableRegistry::getTableLocator()->get('Programs')
                ->find('list')
                ->where(['Programs.id IN' => $programIds])
                ->toArray();

            $programTypes = TableRegistry::getTableLocator()->get('ProgramTypes')
                ->find('list')
                ->where(['ProgramTypes.id IN' => $programTypesIds])
                ->toArray();

            $acyeardatas[$academicCalendar->academic_year] = $academicCalendar->academic_year;
            $semester[$academicCalendar->semester] = $academicCalendar->semester;
        }

        if ($this->request->is('post') && !empty($this->request->getData('applyOnline'))) {
            $data = $this->request->getData();
            $data['OnlineApplicant']['applicationnumber'] = $applicationnumber;

            $isAdmitted = $onlineApplicantTable->isAppliedFordmittion($data);

            if ($isAdmitted == 0) {
                $data = $onlineApplicantTable->preparedAttachment($data);

                if ($onlineApplicantTable->saveAssociated($data, ['validate' => false])) {
                    $this->sendAdmissionEmail($data, $applicationnumber);
                    $this->Flash->success("Application submitted. Your number is <strong>{$applicationnumber}</strong>.");
                    return $this->redirect(['action' => 'online_admission_tracking']);
                } else {
                    $this->Flash->error('Application could not be saved.');
                }
            } else {
                $this->Flash->success("Already applied. Your number: <strong>{$isAdmitted}</strong>.");
                return $this->redirect(['action' => 'online_admission_tracking']);
            }
        }

        $this->set(compact('departments', 'colleges', 'programs', 'programTypes', 'acyeardatas', 'semester', 'applicationnumber'));
    }

    private function sendAdmissionEmail($data, $applicationnumber)
    {
        $departmentName = TableRegistry::getTableLocator()->get('Departments')
            ->get($data['OnlineApplicant']['department_id'])->name ?? '';
        $collegeName = TableRegistry::getTableLocator()->get('Colleges')
            ->get($data['OnlineApplicant']['college_id'])->name ?? '';
        $programName = TableRegistry::getTableLocator()->get('Programs')
            ->get($data['OnlineApplicant']['program_id'])->name ?? '';
        $programTypeName = TableRegistry::getTableLocator()->get('ProgramTypes')
            ->get($data['OnlineApplicant']['program_type_id'])->name ?? '';

        $message = "Online admission application received.<br><strong>Application #: {$applicationnumber}</strong><br><br>";
        $message .= "<table width='100%' style='font-family:Arial,sans-serif;'>";
        $message .= "<tr><td>Name:</td><td>{$data['OnlineApplicant']['first_name']} {$data['OnlineApplicant']['father_name']}</td></tr>";
        $message .= "<tr><td>Level:</td><td>{$programName}</td></tr>";
        $message .= "<tr><td>Type:</td><td>{$programTypeName}</td></tr>";
        $message .= "<tr><td>College:</td><td>{$collegeName}</td></tr>";
        $message .= "<tr><td>Department:</td><td>{$departmentName}</td></tr>";
        $message .= "</table>";

        $email = new Email('default');
        $email->setViewVars(['message' => $message])
            ->setTemplate('onlineapplication')
            ->setEmailFormat('html')
            ->setTo($data['OnlineApplicant']['email'])
            ->setSubject('Admission Application - ' . $data['OnlineApplicant']['first_name']);

        try {
            $email->send();
            $this->Flash->success('Application submitted and email sent!');
        } catch (\Exception $e) {
            $this->Flash->warning('Application saved, but email failed.');
        }
    }

    public function checkGraduate($studentID = null)
    {
        $students = null;
        $studentIDNotFound = 0;

        if (($this->request->is('post') && !empty($this->request->getData('continue'))) || $studentID) {
            if ($this->MathCaptcha->validates($this->request->getData('Page.security_code')) || $studentID) {
                $id = $studentID ? str_replace('-', '/', $studentID) : trim($this->request->getData('Page.studentID'));

                $count = TableRegistry::getTableLocator()->get('Students')
                    ->find()
                    ->where(['Students.studentnumber' => $id])
                    ->count();

                if ($count > 0) {
                    $students = TableRegistry::getTableLocator()->get('GraduateLists')
                        ->find()
                        ->where(['Students.studentnumber' => $id])
                        ->contain([
                            'Students' => [
                                'Programs', 'Departments', 'Colleges', 'ProgramTypes',
                                'Curriculums' => ['fields' => ['english_degree_nomenclature']],
                                'StudentExamStatuses' => [
                                    'order' => ['StudentExamStatuses.id DESC', 'StudentExamStatuses.academic_year DESC', 'StudentExamStatuses.semester DESC']
                                ],
                                'ExitExams' => ['order' => ['ExitExams.exam_date DESC', 'ExitExams.id DESC']]
                            ],
                            'Attachments'
                        ])
                        ->first();
                } else {
                    $this->Flash->info('Student ID not found. Please check and try again.');
                    $studentIDNotFound = 1;
                }
            } else {
                $this->Flash->error('Incorrect math answer.');
            }

            $this->set('studentID', $id);
        }

        $this->set(compact('students', 'studentIDNotFound'));
        $this->set('mathCaptcha', $this->MathCaptcha->generateEquation());
    }

    public function getDepartmentCombo($college_id)
    {
        $this->viewBuilder()->setLayout('ajax');
        $departments = [];

        $academicCalendar = TableRegistry::getTableLocator()->get('AcademicCalendars')
            ->find()
            ->where(['AcademicCalendars.online_admission_end_date >=' => FrozenTime::now()])
            ->first();

        if ($academicCalendar) {
            $departmentIds = unserialize($academicCalendar->department_id);
            $collegeIds = TableRegistry::getTableLocator()->get('Departments')
                ->find('list', ['valueField' => 'college_id'])
                ->where(['Departments.id IN' => $departmentIds])
                ->toArray();

            if (in_array($college_id, $collegeIds)) {
                $departments = TableRegistry::getTableLocator()->get('Departments')
                    ->find('list')
                    ->where(['Departments.college_id' => $college_id, 'Departments.id IN' => $departmentIds])
                    ->toArray();
            }
        }

        $this->set(compact('departments'));
    }

    public function checkRemedialResult()
    {
        if (Configure::read('SHOW_REMEDIAL_RESULT_CHECK_LINK') != 1) {
            $this->Flash->info('Remedial result checking is currently unavailable.');
            return $this->redirect('/');
        }

        $resultFound = null;
        $firstNameProvided = $searchKeyProvided = '';

        if ($this->request->is('post') && !empty($this->request->getData('continue'))) {
            $firstNameProvided = $this->request->getData('Page.first_name');
            $searchKeyProvided = $this->request->getData('Page.search_key');

            if ($this->MathCaptcha->validates($this->request->getData('Page.security_code'))) {
                $resultFound = TableRegistry::getTableLocator()->get('RemedialResults')
                    ->findRemedialResult($this->request->getData());

                if (!$resultFound) {
                    $this->Flash->info('No result found. Please check your name and ID.');
                }
            } else {
                $this->Flash->error('Incorrect math answer.');
            }
        }

        $this->set(compact('resultFound', 'firstNameProvided', 'searchKeyProvided'));
        $this->set('mathCaptcha', $this->MathCaptcha->generateEquation());
    }

    public function checkCampusPlacement()
    {
        if (Configure::read('SHOW_CAMPUS_PLACEMENT_CHECK_LINK') != 1) {
            return $this->redirect('/');
        }

        $resultFound = null;
        $firstNameProvided = $searchKeyProvided = '';

        if ($this->request->is('post') && !empty($this->request->getData('continue'))) {
            $firstNameProvided = $this->request->getData('Page.first_name');
            $searchKeyProvided = $this->request->getData('Page.search_key');

            if ($this->MathCaptcha->validates($this->request->getData('Page.security_code'))) {
                $resultFound = TableRegistry::getTableLocator()->get('CampusPlacements')
                    ->checkCampusPlacement($this->request->getData());

                if (!$resultFound) {
                    $this->Flash->info('No placement found. Please verify your details.');
                }
            } else {
                $this->Flash->error('Incorrect math answer.');
            }
        }

        $this->set(compact('resultFound', 'firstNameProvided', 'searchKeyProvided'));
        $this->set('mathCaptcha', $this->MathCaptcha->generateEquation());
    }
}

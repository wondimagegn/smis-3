<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\I18n\FrozenTime;
use Cake\Network\Exception\NotFoundException;
use Cake\Mailer\Email;

class AlumniController extends AppController
{

    public $menuOptions = [
        'weight' => 2,
        'alias' => [
            'checkAlumniSurvey' => 'Check Alumni Survey',
            'alumniSurveyView' => 'Baseline Survey View',
            'addBaselinesurveyOnbehalf' => 'Fill Baseline Survey On Behalf of Student'
        ]
    ];

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator');

        $this->loadModel('Alumni');
        $this->loadModel('Students');
        $this->loadModel('AlumniResponses');
        $this->loadModel('SurveyQuestions');
        $this->loadModel('AlumniMembers');
    }

    public function beforeRender(): void
    {
        parent::beforeRender();
        $acyear_array_data = $this->AcademicYear->acyearArray();
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
    }

    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['member_registration']);
    }

    public function alumniSurveyView()
    {
        $this->paginate = [
            'contain' => [
                'Students' => [
                    'SenateLists',
                    'Departments' => ['fields' => ['id', 'name']]
                ],
                'AlumniResponses'
            ]
        ];

        if (!empty($this->request->getData('Search.gradution_academic_year'))) {
            $this->paginate['conditions'][]['Alumni.gradution_academic_year'] = $this->request->getData('Search.gradution_academic_year');
        }
        if (!empty($this->request->getData('Search.program_id'))) {
            $this->paginate['conditions'][]['Students.program_id'] = $this->request->getData('Search.program_id');
        }
        if (!empty($this->request->getData('Search.program_type_id'))) {
            $this->paginate['conditions'][]['Students.program_type_id'] = $this->request->getData('Search.program_type_id');
        }
        if (!empty($this->request->getData('Search.department_id'))) {
            $department_id = $this->request->getData('Search.department_id');
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $this->paginate['conditions'][]['Students.college_id'] = $college_id[1];
            } else {
                $this->paginate['conditions'][]['Students.department_id'] = $department_id;
            }
            $default_department_id = $department_id;
        }
        if (!empty($this->request->getData('Search.name'))) {
            $this->paginate['conditions'][]['Alumnus.full_name LIKE'] = '%' . $this->request->getData('Search.name') . '%';
        }
        if (!empty($this->request->getData('Search.limit'))) {
            $this->paginate['limit'] = $this->request->getData('Search.limit');
            $this->paginate['maxLimit'] = $this->request->getData('Search.limit');
        }

        $this->Paginator->settings = $this->paginate;
        $alumni = $this->Paginator->paginate('Alumni');

        if (empty($alumni) && $this->request->getData()) {
            $this->Flash->info(__('There is no student based on your search criteria.'));
        }

        if ($this->request->getData('getAlumniQuestionnaireInExcel')) {
            $surveyQuestions = $this->Alumni->AlumniResponses->SurveyQuestions->find('list', [
                'keyField' => 'id',
                'valueField' => 'question_english'
            ])->toArray();

            $student_ids = [];
            if (!empty($this->request->getData('Alumnus'))) {
                foreach ($this->request->getData('Alumnus') as $key => $student) {
                    if (is_numeric($key) && !empty($student['student_id']) && !empty($student['gp'])) {
                        $student_ids[] = $student['student_id'];
                    }
                }
            }

            if (empty($student_ids)) {
                $this->Flash->error(__('You are required to select at least one student.'));
            } else {
                $alumniSurvey = $this->Alumni->getCompletedSurvey($student_ids);
                if (empty($alumniSurvey)) {
                    $this->Flash->info(__('Baseline Survey Questionnaire generation has experienced a problem for the selected students. Please try again.'));
                } else {
                    $this->autoRender = false;
                    $filename = 'baselinequestionnaire' . date('Ymd His');
                    $this->set(compact('alumniSurvey', 'surveyQuestions', 'filename'));
                    $this->viewBuilder()->setTemplate('/Elements/baseline_survey_questionnaire_xls');
                    return;
                }
            }
        }

        if ($this->request->getData('deleteAlumniQuestionnaireInExcel')) {
            $student_ids = [];
            if (!empty($this->request->getData('Alumnus'))) {
                foreach ($this->request->getData('Alumnus') as $key => $student) {
                    if (is_numeric($key) && !empty($student['student_id']) && !empty($student['gp'])) {
                        $student_ids[] = $student['student_id'];
                    }
                }
            }

            if (empty($student_ids)) {
                $this->Flash->error(__('You are required to select at least one student.'));
            } else {
                $alumniSurvey = $this->Alumni->getSelectedAlumniSurvey($student_ids);
                $deletedCount = 0;
                if (!empty($alumniSurvey)) {
                    foreach ($alumniSurvey as $alv) {
                        if ($this->Alumni->delete($alv['Alumni'])) {
                            $deletedCount++;
                        }
                    }
                }
                if ($deletedCount) {
                    $this->Flash->success(__('You have deleted ' . $deletedCount . ' alumni baseline survey, please ask them to fill again.'));
                    return $this->redirect(['action' => 'alumni_survey_view']);
                }
            }
        }

        $programs = $this->Alumni->Students->Programs->find('list', [
            'conditions' => ['Programs.id IN' => $this->program_ids, 'Programs.active' => 1]
        ])->toArray();

        $programTypes = $this->Alumni->Students->ProgramTypes->find('list', [
            'conditions' => ['ProgramTypes.id IN' => $this->program_type_ids, 'ProgramTypes.active' => 1]
        ])->toArray();

        if ($this->request->getSession()->read('Auth.User.is_admin') == 1 && $this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR) {
            $departments = $this->Alumni->Students->Departments->allDepartmentsByCollege2(1, $this->department_ids, $this->college_ids);
            $departments = [0 => 'All University Students'] + $departments;
        } else {
            $departments = $this->Alumni->Students->Departments->allDepartmentsByCollege2(1, $this->department_ids, $this->college_ids);
        }

        $this->set(compact('programs', 'programTypes', 'alumni', 'departments', 'default_department_id'));
    }

    public function index()
    {
    }

    public function view($id = null)
    {
        if (!$this->Alumni->exists($id)) {
            throw new NotFoundException(__('Invalid Alumnus'));
        }
        $alumnus = $this->Alumni->get($id);
        $this->set(compact('alumnus'));
    }

    public function add()
    {
        $student = $this->Alumni->Students->find('first', [
            'conditions' => ['Students.studentnumber' => $this->Auth->user('username')],
            'contain' => ['GraduateLists', 'Users']
        ])->first();

        if (empty($student)) {
            $this->Flash->warning(__('You are not eligible to fill the survey. The Alumni baseline survey is only available for graduating students.'));
            return $this->redirect('/');
        }

        $student_id = $student->id;

        if (!$this->Alumni->checkIfStudentGradutingClass($student_id)) {
            $this->Flash->warning(__('Either you are not graduating class student or your department did not define a final year project as thesis or project work. You cannot fill alumni baseline survey at this time.'));
            return $this->redirect('/');
        }

        if ($this->Alumni->completedRoundOneQuestionner($student_id)) {
            $this->Flash->success(__('Thank you for completing alumni baseline questionnaire. Congratulations for your graduation and part of our alumni.'));
            return $this->redirect(['controller' => 'exam_grades', 'action' => 'student_grade_view']);
        }

        if ($this->request->is('post')) {
            $this->request->data = $this->Alumni->formatResponse($this->request->getData());
            $alumnus = $this->Alumni->newEntity($this->request->getData(), ['associated' => ['AlumniResponses']]);
            if ($this->Alumni->save($alumnus)) {
                $this->Flash->success(__('THANK YOU FOR YOUR PARTICIPATION!'));
                return $this->redirect(['controller' => 'exam_grades', 'action' => 'student_grade_view']);
            } else {
                $this->Flash->error(__('Your response could not be saved. Please try again.'));
            }
        }

        $surveyQuestions = $this->Alumni->AlumniResponses->SurveyQuestions->find('all', [
            'contain' => ['SurveyQuestionAnswers']
        ])->toArray();

        $student = $this->Alumni->Students->find('first', [
            'conditions' => ['Students.id' => $student_id],
            'contain' => ['Regions', 'Curricula']
        ])->first();

        $student->age = $this->Alumni->Students->getAge($student->birthdate);
        $regions = $this->Alumni->Students->Regions->find('list')->toArray();
        $sexes = ['female' => 'Female', 'male' => 'Male'];
        $university = TableRegistry::getTableLocator()->get('Universities')->getStudentUnivrsity($student_id);

        $this->set(compact('surveyQuestions', 'sexes', 'university', 'student', 'regions', 'student_id'));
    }

    public function add_baselinesurvey_onbehalf()
    {
        $student_id = 0;
        $everythingfine = false;

        if ($this->request->is('post') && !empty($this->request->getData('continue'))) {
            if (!empty($this->request->getData('Alumnus.studentID'))) {
                $student_id_valid = $this->Alumni->Students->find()->where(['Students.studentnumber' => trim($this->request->getData('Alumnus.studentID'))])->count();
                if ($student_id_valid > 0) {
                    $everythingfine = true;
                    $student_id = $this->Alumni->Students->find()->select(['id'])->where(['Students.studentnumber' => trim($this->request->getData('Alumnus.studentID'))])->first()->id;
                } else {
                    $this->Flash->error(__('The provided student number is not valid.'));
                }
            } else {
                $this->Flash->error(__('Please provide student number or ID to continue.'));
            }
        }

        if ($everythingfine) {
            $student = $this->Alumni->Students->find('first', [
                'conditions' => ['Students.id' => $student_id],
                'contain' => ['GraduateLists', 'Users']
            ])->first();

            if (empty($student)) {
                $this->Flash->warning(__('You are not eligible to fill the survey. The Alumni baseline survey is only available for graduating students.'));
                return $this->redirect('/');
            }

            if (!$this->Alumni->checkIfStudentGradutingClass($student_id)) {
                $this->Flash->warning(__('Either you are not graduating class student or your department did not define a final year project.'));
                return $this->redirect('/');
            }

            if ($this->Alumni->completedRoundOneQuestionner($student_id)) {
                $this->Flash->success(__('Thank you for completing alumni baseline questionnaire.'));
                return $this->redirect('/');
            }

            $surveyQuestions = $this->Alumni->AlumniResponses->SurveyQuestions->find('all', [
                'contain' => ['SurveyQuestionAnswers']
            ])->toArray();

            $student = $this->Alumni->Students->find('first', [
                'conditions' => ['Students.id' => $student_id],
                'contain' => ['Regions', 'Curricula']
            ])->first();

            $student->age = $this->Alumni->Students->getAge($student->birthdate);
            $regions = $this->Alumni->Students->Regions->find('list')->toArray();
            $sexes = ['female' => 'Female', 'male' => 'Male'];
            $university = TableRegistry::getTableLocator()->get('Universities')->getStudentUnivrsity($student_id);

            $this->set(compact('surveyQuestions', 'sexes', 'university', 'student', 'regions', 'student_id'));
        }

        if ($this->request->is('post') && !empty($this->request->getData('fillAlumnus'))) {
            $this->request->data = $this->Alumni->formatResponse($this->request->getData());
            $alumnus = $this->Alumni->newEntity($this->request->getData(), ['associated' => ['AlumniResponses']]);
            if ($this->Alumni->save($alumnus)) {
                $this->Flash->success(__('THANK YOU FOR YOUR PARTICIPATION!'));
                return $this->redirect('/');
            } else {
                $this->Flash->error(__('Your response could not be saved. Please try again.'));
            }
        }

        $this->set(compact('everythingfine'));
    }

    public function checkAlumniSurvey()
    {
        if ($this->request->is('post') && !empty($this->request->getData('check'))) {
            $studentValid = $this->Alumni->Students->find()->where(['Students.studentnumber' => trim($this->request->getData('Alumnus.studentID'))])->first();
            if ($studentValid) {
                $alumniDetail = $this->Alumni->find('first', [
                    'conditions' => ['Alumni.student_id' => $studentValid->id],
                    'contain' => [
                        'AlumniResponses' => [
                            'SurveyQuestions',
                            'SurveyQuestionAnswers'
                        ],
                        'Students' => ['GraduateLists']
                    ]
                ])->first();

                if (empty($alumniDetail)) {
                    $this->Flash->info(__('The student is not an alumni member and has not completed alumni survey.'));
                }

                $surveyQuestions = $this->Alumni->AlumniResponses->SurveyQuestions->find('all', [
                    'contain' => ['SurveyQuestionAnswers']
                ])->toArray();

                $student = $this->Alumni->Students->find('first', [
                    'conditions' => ['Students.id' => $studentValid->id],
                    'contain' => ['Regions', 'Curricula']
                ])->first();

                $student->age = $this->Alumni->Students->getAge($studentValid->birthdate);
                $regions = $this->Alumni->Students->Regions->find('list')->toArray();
                $sexes = ['female' => 'Female', 'male' => 'Male'];
                $university = TableRegistry::getTableLocator()->get('Universities')->getStudentUnivrsity($studentValid->id);

                $this->set(compact('alumniDetail', 'student', 'regions', 'university', 'surveyQuestions'));
            } else {
                $this->Flash->error(__('The provided student number is not valid.'));
            }
        }
    }

    public function memberRegistration()
    {
        $this->viewBuilder()->setLayout('login');

        if ($this->request->is('post') && !empty($this->request->getData('applyOnline'))) {
            $applicationnumber = $this->AlumniMembers->nextTrackingNumber();
            $data = $this->request->getData('Alumnus');
            $data['trackingnumber'] = $applicationnumber;

            if (!empty($data['date_of_birth']) && is_array($data['date_of_birth'])) {
                $data['date_of_birth'] = sprintf('%04d-%02d-%02d', $data['date_of_birth']['year'], $data['date_of_birth']['month'], $data['date_of_birth']['day']);
            }
            if (!empty($data['gradution']) && is_array($data['gradution'])) {
                $data['gradution'] = $data['gradution']['year'];
            }

            $member = $this->AlumniMembers->newEntity(['AlumniMember' => $data]);
            if ($this->AlumniMembers->save($member)) {
                $fullname = $data['title'] . '. ' . $data['first_name'] . ' ' . $data['last_name'];
                $autoMessage = "$fullname registered to our alumni membership database with an alumni number " . $data['trackingnumber'];
                TableRegistry::getTableLocator()->get('AutoMessages')->alumniRegistrationMessage($autoMessage);

                $message = "<p>Dear $fullname,</p> Now you are officially Arbaminch University alumni member. Your alumni number is " . $data['trackingnumber'] . ".<br /> <p>Be our follower on the following social medias: </p> <ul> <li>https://www.facebook.com/arbaminchuniversityalumni</li> <li>https://www.twitter.com/alumniarbaminch</li> <li>https://www.linkedin.com/arba-minch-university-alumni</li> </ul> <br/> Your faithfully<br/> Alumni team!";

                if (!empty($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $email = new Email('default');
                    $email->setTemplate('onlineapplication')
                        ->setEmailFormat('html')
                        ->setTo($data['email'])
                        ->setSubject('Congratulations! You\'ve successfully joined our alumni network.')
                        ->setViewVars(['message' => $message])
                        ->send();
                }

                $this->request->getSession()->write('Flash.flash', [
                    'message' => 'Now you are officially Arbaminch University alumni member.',
                    'element' => 'default',
                    'params' => ['class' => 'success', 'delay' => 15000]
                ]);
                unset($this->request->data);
                return $this->redirect('/');
            } else {
                $this->Flash->error(__('Your Alumni membership request could not be saved, please try again.'));
                $errors = $this->AlumniMembers->validationErrors;
                $this->set(compact('errors'));
            }
        }

        $titles = ['Mr' => 'Mr', 'Ms' => 'Ms', 'Mrs' => 'Mrs', 'Dr.' => 'Dr.', 'Professor' => 'Professor'];
        $programs = ['Degree' => 'Bachelor\'s Degree', 'Master' => 'Master\'s Degree', 'PhD' => 'PhD', 'Diploma' => 'Diploma'];
        $institute_colleges = $this->Alumni->Students->Colleges->find('list', [
            'conditions' => [
                'NOT' => ['Colleges.id IN' => \Cake\Core\Configure::read('only_stream_based_freshman_college_ids')],
                'Colleges.active' => 1
            ],
            'order' => ['Colleges.name' => 'ASC']
        ])->toArray();

        $countries = $this->Alumni->Students->Countries->find('list')->toArray();

        $this->set(compact('titles', 'institute_colleges', 'programs', 'countries'));
    }
}

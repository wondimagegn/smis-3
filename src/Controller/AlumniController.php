<?php
namespace App\Controller;


use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class AlumniController extends AppController
{

    public $name = 'Alumni';

    public $menuOptions = array(

        //'parent' => 'graduation',
        'exclude' => array('index', 'edit', 'add'),
        'weight' => 2,

        'alias' => array(
            'checkAlumniSurvey' => 'Check Alumni Survey',
            'alumniSurveyView' => 'Baseline Survey View',
            'addBaselinesurveyOnbehalf' => 'Fill Baseline Survey On Behalf of Student'
        )
    );

    public $paginate = array();

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('EthiopicDateTime');

        $this->loadComponent('Paginator');
        $this->loadComponent('AcademicYear');

        $this->loadComponent('Email');
        $this->viewBuilder()->setHelpers(['Xls', 'Media.Media']);
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyearArray();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->currentAcademicyear();

        $this->set(
            compact(
                'acyear_array_data',
                'defaultacademicyear'
            )
        );
    }


    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        //$this->Security->unlockedActions();
        $this->Auth->Allow(
            'add',
            'alumni_survey_view',
            'member_registration',
            'check_alumni_survey'
        );
    }

    public function alumniSurveyView()
    {

        $this->paginate = array('contain' => array('Student' => array('SenateList'), 'AlumniResponse'));


        // filter by graduation year
        if (isset($this->request->data['Search']['gradution_academic_year']) && !empty($this->request->data['Search']['gradution_academic_year'])) {
            $this->paginate['conditions'][]['Alumnus.gradution_academic_year'] = $this->request->data['Search']['gradution_academic_year'];
        }


        // filter by program
        if (isset($this->request->data['Search']['program_id']) && !empty($this->request->data['Search']['program_id'])) {
            $this->paginate['conditions'][]['Student.program_id'] = $this->request->data['Search']['program_id'];
        }

        // filter by program type
        if (isset($this->request->data['Search']['program_type_id']) && !empty($this->request->data['Search']['program_type_id'])) {
            $this->paginate['conditions'][]['Student.program_type_id'] = $this->request->data['Search']['program_type_id'];
        }

        // filter by department
        /*
        if (isset($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['department_id'])) {
            $this->paginate['conditions'][]['Student.department_id'] = $this->request->data['Search']['department_id'];
        }
        */

        // filter by department or college

        if (isset($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['department_id'])) {
            $department_id = $this->request->data['Search']['department_id'];
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $this->paginate['conditions'][]['Student.college_id'] = $college_id[1];
            } else {
                $this->paginate['conditions'][]['Student.department_id'] = $department_id;
            }
            $default_department_id = $this->request->data['Search']['department_id'];
        }

        // filter by name

        if (isset($this->request->data['Search']['name']) && !empty($this->request->data['Search']['name'])) {
            $this->paginate['conditions'][]['Alumnus.full_name like '] = $this->request->data['SenateList']['minute_number'] . '%';
        }

        if (isset($this->request->data['Search']['limit'])) {
            $this->paginate['limit'] = $this->request->data['Search']['limit'];
            $this->paginate['maxLimit'] = $this->request->data['Search']['limit'];
            $this->request->data['Search']['limit'] = $this->request->data['Search']['limit'];
        }
        debug($this->paginate);
        $this->Paginator->settings = $this->paginate;

        if (isset($this->Paginator->settings['conditions'])) {
            $alumni =
                $this->Paginator->paginate('Alumnus');
        } else {
            $alumni = array();
        }

        if (empty($alumni) && isset($this->request->data) && !empty($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('There is no student  based on the given criteria.'),
                'default',
                array('class' => 'info-box info-message')
            );
        }

        //Issue Student Password button is clicked
        if (isset($this->request->data['getAlumniQuestionnaireInExcel'])) {
            $surveyQuestions = $this->Alumnus->AlumniResponse->SurveyQuestion->find(
                'list',
                array('fields' => array('SurveyQuestion.id', 'SurveyQuestion.question_english'))
            );
            $student_ids = array();
            foreach (
                $this->request->data['Alumnus'] as
                $key => $student
            ) {
                if (is_numeric($key)
                    && !empty($student['student_id'])) {
                    if (isset($student['gp'])
                        && $student['gp'] == 1) {
                        $student_ids[] = $student['student_id'];
                    }
                }
            }

            if (empty($student_ids)) {
                $this->Session->setFlash(
                    '<span></span>' . __('You are required to select at least one student.', true),
                    'default',
                    array('class' => 'error-box error-message')
                );
            } else {
                $alumniSurvey = $this->Alumnus->getCompletedSurvey($student_ids);

                if (empty($alumniSurvey)) {
                    $this->Session->setFlash(
                        '<span></span>' .
                        __(
                            'Baseline Survey Questionnaire generation has experiance problem  for the selected students. Please try again.'
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                } else {
                    $this->autoLayout = false;
                    $filename = 'baselinequestionnaire' . date('Ymd H:i:s');

                    $this->set(compact('alumniSurvey', 'surveyQuestions', 'filename'));
                    $this->render('/Elements/baseline_survey_questionnaire_xls');

                    return;
                }
            }
        }

        if (isset($this->request->data['deleteAlumniQuestionnaireInExcel'])) {
            $surveyQuestions = $this->Alumnus->AlumniResponse->SurveyQuestion->find('list', array(
                'fields' => array(
                    'SurveyQuestion.id',
                    'SurveyQuestion.question_english'
                )
            ));
            $student_ids = array();
            foreach (
                $this->request->data['Alumnus'] as
                $key => $student
            ) {
                if (is_numeric($key) &&
                    !empty($student['student_id'])) {
                    if (isset($student['gp'])
                        && $student['gp'] == 1) {
                        $student_ids[] = $student['student_id'];
                    }
                }
            }

            if (empty($student_ids)) {
                $this->Session->setFlash(
                    '<span></span>' . __('You are required to select at least one student.', true),
                    'default',
                    array('class' => 'error-box error-message')
                );
            } else {
                $alumniSurvey = $this->Alumnus->getSelectedAlumniSurvey($student_ids);
                $deletedCount = 0;

                foreach ($alumniSurvey as $alk => $alv) {
                    if ($this->Alumnus->delete($alv['Alumnus']['id'])) {
                        $deletedCount++;
                    }
                }
                if ($deletedCount) {
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'You have deleted ' . $deletedCount . ' alumni baseline survey, please ask them to fill again.',
                            true
                        ),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    return $this->redirect(array(
                            'controller' => 'alumni',
                            'action' => "alumni_survey_view"
                        )
                    );
                }
            }
        }

        $programs = $this->Alumnus->Student->Program->find('list');
        $programTypes = $this->Alumnus->Student->ProgramType->find('list');
        if (isset($this->department_ids) && !empty($this->department_ids)) {
            //$departments = $this->Alumnus->Student->Department->find('list',array('conditions'=>array('Department.id'=>$this->department_ids)));
            $departments = $this->Alumnus->Student->Department->allDepartmentsByCollege2(
                1,
                $this->department_ids,
                $this->college_ids
            );
        } else {
            //$departments = $this->Alumnus->Student->Department->find('list');
            $departments = $this->Alumnus->Student->Department->allDepartmentsByCollege2(
                1,
                $this->department_ids,
                $this->college_ids
            );
        }
        $departments = array(0 => 'All University Students') + $departments;
        $default_department_id = null;


        $this->set(
            compact(
                'programs',
                'programTypes',
                'alumni',
                'departments',
                'default_department_id'
            )
        );
    }

    public function index()
    {
    }

    public function view($id = null)
    {

        if (!$this->Alumnus->exists($id)) {
            throw new NotFoundException(__('Invalid alumnus'));
        }
        $options = array('conditions' => array('Alumnus.' . $this->Alumnus->primaryKey => $id));
        $this->set('alumnus', $this->Alumnus->find('first', $options));
    }

    public function add()
    {

        /*
        if (!$this->Alumnus->Student->exists($id)) {
            throw new NotFoundException(__('Invalid alumnus'));
        }
        */
        $student = ClassRegistry::init('Student')->find(
            'first',
            array(
                'conditions' => array('Student.studentnumber' => $this->Auth->user('username')),
                'contain' => array('GraduateList', 'User')
            )
        );
        if (isset($student['Student']['id']) && !empty($student['Student']['id'])) {
            //check if it exists
            $student_id = $student['Student']['id'];
        } else {
            $this->Session->setFlash(
                '<span></span>' . __(
                    'You are not elegible to fill survey. The baseline survey is only available for graduting students.'
                ),
                'default',
                array('class' => 'warning-box warning-message')
            );
            return $this->redirect('/');
        }



        if ($this->Alumnus->checkIfStudentGradutingClass($student_id) == false) {
            $this->Session->setFlash(
                '<span></span>' . __(
                    'You can not fill alumni baseline survey due to either you are not graduting class students or department did not define the final year project as thesis or project work.'
                ),
                'default',
                array('class' => 'warning-box warning-message')
            );
            return $this->redirect('/');
        }

        //$student_id=2020;
        $filled1stLevelQuestionair = $this->Alumnus->completedRoundOneQuestionner($student_id);

        if ($filled1stLevelQuestionair == true) {
            $this->Session->setFlash(
                '<span></span>' . __(
                    'Thank you for completing alumni baseline questionnair. Congratulations for your graduation and part of our alumni.'
                ),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('controller' => 'exam_grades', 'action' => "student_grade_view"));
        } else {
            if ($this->Alumnus->checkIfStudentGradutingClass($student_id) == false && false) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'You are not elegible to fill the survey. The survey is intended only for graduating  class.'
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
                return $this->redirect(array('controller' => 'exam_grades', 'action' => "student_grade_view"));
            }
        }
        debug($this->Auth->user('username'));
        if ($this->request->is('post')) {
            debug($this->request);
            $this->request->data = $this->Alumnus->formatResponse($this->request->data);
            $this->Alumnus->create();
            if ($this->Alumnus->saveAll($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('THANK YOU FOR YOUR PARTICIPATION!'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect(array('controller' => 'exam_grades', 'action' => "student_grade_view"));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('Your response could not be saved. Please try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }

            debug($this->Alumnus->invalidFields());
        }


        $surveyQuestions = $this->Alumnus->AlumniResponse->SurveyQuestion->find(
            'all',
            array('contain' => array('SurveyQuestionAnswer'))
        );
        $student = $this->Alumnus->Student->find(
            'first',
            array('conditions' => array('Student.id' => $student_id), 'contain' => array('Region', 'Curriculum'))
        );
        $student['Student']['age'] = $this->Alumnus->Student->getAge($student['Student']['birthdate']);

        $regions = $this->Alumnus->Student->Region->find(
            'list',
            array('fields' => array('Region.name', 'Region.name'))
        );
        $sexes = array('female' => 'Female', 'male' => 'Male');

        $university = ClassRegistry::init('University')->getStudentUnivrsity($student_id);

        $this->set(compact('surveyQuestions', 'sexes', 'university', 'student', 'regions', 'student_id'));
    }

    public function addBaselinesurveyOnbehalf()
    {

        $student_id = 0;
        $everythingfine = false;

        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            if (!empty($this->request->data['Alumnus']['studentID'])) {
                $student_id_valid = $this->Alumnus->Student->find(
                    'count',
                    array(
                        'conditions' => array(
                            'Student.studentnumber' => trim(
                                $this->request->data['Alumnus']['studentID']
                            )
                        )
                    )
                );
                debug($student_id_valid);
                $studentIDs = 1;
                if ($student_id_valid > 0) {
                    $everythingfine = true;
                    $student_id = $this->Alumnus->Student->field(
                        'id',
                        array(
                            'studentnumber' =>
                                trim($this->request->data['Alumnus']['studentID'])
                        )
                    );
                } else {
                    if ($check_id_is_valid == 0) {
                        $this->Session->setFlash(
                            '<span></span> ' . __('You dont have the privilage to view the selected students profile.'),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    } else {
                        $this->Session->setFlash(
                            '<span></span> ' . __('The provided student number is not valid.'),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                }
            } else {
                $this->Session->setFlash(
                    '<span></span> ' .
                    __('Please provide student number to  view profile.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        if ($everythingfine) {
            $student = ClassRegistry::init('Student')->find(
                'first',
                array('conditions' => array('Student.id' => $student_id), 'contain' => array('GraduateList', 'User'))
            );

            if (isset($student['Student']['id']) && !empty($student['Student']['id'])) {
                //check if it exists
                $student_id = $student['Student']['id'];
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'You are not elegible to fill survey. The baseline survey is only available for graduting students.'
                    ),
                    'default',
                    array('class' => 'warning-box warning-message')
                );
                return $this->redirect('/');
            }

            if ($this->Alumnus->checkIfStudentGradutingClass($student_id) == false) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'You can not fill alumni baseline survey due to either you are not graduting class students or department did not define the final year project as thesis or project work.'
                    ),
                    'default',
                    array('class' => 'warning-box warning-message')
                );
                return $this->redirect('/');
            }

            //$student_id=2020;
            $filled1stLevelQuestionair = $this->Alumnus->completedRoundOneQuestionner($student_id);

            if ($filled1stLevelQuestionair == true) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'Thank you for completing alumni baseline questionnair. Congratulations for your graduation and part of our alumni.'
                    ),
                    'default',
                    array('class' => 'success-box success-message')
                );
                return $this->redirect('/');
            } else {
                if ($this->Alumnus->checkIfStudentGradutingClass($student_id) == false && false) {
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'You are not elegible to fill the survey. The survey is intended only for graduating  class.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    return $this->redirect('/');
                }
            }


            $surveyQuestions = $this->Alumnus->AlumniResponse->SurveyQuestion->find(
                'all',
                array('contain' => array('SurveyQuestionAnswer'))
            );
            $student = $this->Alumnus->Student->find(
                'first',
                array('conditions' => array('Student.id' => $student_id), 'contain' => array('Region', 'Curriculum'))
            );
            $student['Student']['age'] = $this->Alumnus->Student->getAge($student['Student']['birthdate']);

            $regions = $this->Alumnus->Student->Region->find(
                'list',
                array('fields' => array('Region.name', 'Region.name'))
            );
            $sexes = array('female' => 'Female', 'male' => 'Male');

            $university = ClassRegistry::init('University')->getStudentUnivrsity($student_id);

            $this->set(
                compact(
                    'surveyQuestions',
                    'sexes',
                    'university',
                    'student',
                    'regions',
                    'student_id'
                )
            );
        }

        if ($this->request->is('post') && isset($this->request->data['fillAlumnus'])) {
            $this->request->data = $this->Alumnus->formatResponse($this->request->data);
            $this->Alumnus->create();
            if ($this->Alumnus->saveAll($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('THANK YOU FOR YOUR PARTICIPATION!'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                //return $this->redirect(array('controller'=>'exam_grades','action' => "student_grade_view"));
                $this->redirect('/');
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('Your response could not be saved. Please try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        $this->set(compact('everythingfine'));
    }


    public function checkAlumniSurvey()
    {

        debug($this->request->data);
        if (!empty($this->request->data) && isset($this->request->data['check'])) {
            $studentValid = $this->Alumnus->Student->find('first', array(
                'conditions' => array('Student.studentnumber' => trim($this->request->data['Alumnus']['studentID'])),
                'recursive' => -1
            ));
            if (isset($studentValid) && !empty($studentValid)) {
                $alumniDetail = $this->Alumnus->find('first', array(
                    'conditions' => array('Alumnus.student_id' => $studentValid['Student']['id']),
                    'contain' => array(
                        'AlumniResponse' => array('SurveyQuestion', 'SurveyQuestionAnswer'),
                        'Student' => array('GraduateList')
                    )
                ));
                if (!isset($alumniDetail) && empty($alumniDetail)) {
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'The student is not an alumni member and has not completed alumni survey.'
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                }
                debug($alumniDetail);

                $surveyQuestions = $this->Alumnus->AlumniResponse->SurveyQuestion->find(
                    'all',
                    array('contain' => array('SurveyQuestionAnswer'))
                );
                $student = $this->Alumnus->Student->find(
                    'first',
                    array(
                        'conditions' => array('Student.id' => $studentValid['Student']['id']),
                        'contain' => array('Region', 'Curriculum')
                    )
                );
                $student['Student']['age'] = $this->Alumnus->Student->getAge($studentValid['Student']['birthdate']);

                $regions = $this->Alumnus->Student->Region->find(
                    'list',
                    array('fields' => array('Region.name', 'Region.name'))
                );
                $sexes = array('female' => 'Female', 'male' => 'Male');

                $university = ClassRegistry::init('University')->getStudentUnivrsity($studentValid['Student']['id']);


                $this->set(
                    compact(
                        'alumniDetail',
                        'student',
                        'regions',
                        'university',
                        'surveyQuestions'
                    )
                );
            } else {
                $this->Session->setFlash(
                    '<span></span> ' . __('The provided student number is not valid.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
    }

    public function memberRegistration()
    {

        $this->layout = 'login';

        if ($this->request->is('post') &&
            isset($this->request->data['applyOnline']) && !empty($this->request->data['applyOnline'])) {
            $applicationnumber = ClassRegistry::init('AlumniMember')->nextTrackingNumber();


            $data['AlumniMember'] = $this->request->data['Alumnus'];
            $data['AlumniMember']['trackingnumber'] = $applicationnumber;


            ClassRegistry::init('AlumniMember')->create();
            ClassRegistry::init('AlumniMember')->set($data);
            if (ClassRegistry::init('AlumniMember')->save($data)) {
                $fullname = $data['AlumniMember']['title'] . ' ' . $data['AlumniMember']['first_name'];
                $autoMessage = "$fullname registered to our alumni membership database with an alumni number " . $data['AlumniMember']['trackingnumber'];


                $message = "Dear $fullname, <br/>
			    Now you are officially Arbaminch University alumni member. You will recieve information related to alumni events through your email. Your alumni number is " . $data['AlumniMember']['trackingnumber'] . " and provide this number whenever you need service from AMU.
			     <br /> Be our followers on the following social medias: <br/> <ul>
			     <li>https://www.facebook.com/arbaminchuniversityalumni</li>
			      <li>https://www.twitter.com/alumniarbaminch</li>
			       <li>https://www.linkedin.com/arba-minch-university-alumni</li>

			     </ul>
			     <br/>
			     Your faithfully
			     Alumni team!
			      ";

                $Email = new CakeEmail('default');
                $Email->template('onlineapplication');
                $Email->emailFormat('html');
                $Email->from(array('wondetask@gmail.com' => 'AMU Alumni Portal'));
                $Email->to($data['AlumniMember']['email']);
                $Email->subject('Congratulation!');
                $Email->viewVars(array('message' => $message));


                try {
                    if ($Email->send()) {
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                " Now you are officially Arbaminch University alumni member. You will recieve information related to alumni events through your email. "
                            ),
                            'default',
                            array('class' => 'success-box success-message')
                        );

                        ClassRegistry::init('AutoMessage')->alumniRegistrationMessage($autoMessage);
                    } else {
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                " Now you are officially Arbaminch University alumni member. You will recieve information related to alumni events through your email. "
                            ),
                            'default',
                            array('class' => 'success-box success-message')
                        );
                        ClassRegistry::init('AutoMessage')->alumniRegistrationMessage($autoMessage);
                    }
                } catch (Exception $e) {
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            "Now you are officially Arbaminch University alumni member. You will recieve information related to alumni events through your email."
                        ),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    ClassRegistry::init('AutoMessage')->alumniRegistrationMessage($autoMessage);
                    //return $this->redirect('/');
                }
                //return $this->redirect('/');

            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The membership  could not be saved, please try again.'),
                    'default',
                    array('class' => 'error-box	error-message')
                );

                $errors = ClassRegistry::init('AlumniMember')->validationErrors;
                //$errors=ClassRegistry::init('AlumniMember')->invalidFields();
                $this->set(compact('errors'));
            }
        }

        //$titles = ClassRegistry::init('Title')->find('list',array('fields'=>array('title','title')));
        $titles = array(
            'Professor' => 'Professor',
            'Dr.' => 'Dr.',
            'Mrs' => 'Mrs',
            'Mr' => 'Mr',
            'Ms' => 'Ms'
        );
        $programs = array(
            'Degree' => 'Degree',
            'Master' => 'Master',
            'PhD' => 'PhD',
            'Diploma' => 'Diploma'
        );
        $institute_colleges = ClassRegistry::init('College')->find(
            'list',
            array('fields' => array('name', 'name'), 'order' => array('College.name ASC'))
        );
        $this->set(compact('titles', 'institute_colleges', 'programs'));

	}

}

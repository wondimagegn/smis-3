<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ColleagueEvalutionRatesController extends AppController
{

    public $menuOptions = array(
        'parent' => 'evalution',
        'exclude' => array('index'),
        'alias' => array(
            'colleague_evaluate_instructor' => 'Evaluate Your Colleagues',
            'head_evaluate_instructor' => 'Evaluate Instructor as Head',
            'instructor_evaluation_report' => 'Instructor Evaluation Reports',
        )
    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->Allow(
            'checkAndFixEvaluationErrors'
        );
    }

    public function __init_search()
    {

        if (!empty($this->request->data['Search'])) {
            $this->Session->write('search_data_evaluation', $this->request->data['Search']);
        } else {
            if ($this->Session->check('search_data_evaluation')) {
                $this->request->data['Search'] = $this->Session->read('search_data_evaluation');
            }
        }
    }


    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        //$acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 2, date('Y'));
        $current_academicyear = $this->AcademicYear->current_academicyear();
        $acyear_array_data = $this->AcademicYear->academicYearInArray(
            ((explode('/', $current_academicyear)[0]) - ACY_BACK_FOR_STAFF_EVALUATION_LIST_PRINT_AND_ARCHIEVE),
            (explode('/', $current_academicyear)[0])
        );

        // $academic_year_selected = $defaultacademicyear = $this->AcademicYear->current_academicyear();
        $acYearAndSemester = $this->AcademicYear->current_acy_and_semester();
        $defaultacademicyear = $current_academicyear = $acYearAndSemester['academic_year'];
        $current_semester = $acYearAndSemester['semester'];

        $this->set(
            compact(
                'acyear_array_data',
                'defaultacademicyear',
                /* 'academic_year_selected', */
                'current_academicyear',
                'current_semester'
            )
        );
    }

    public function index()
    {

        $this->__init_search();
        if ($this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR) {
            return $this->redirect(array('action' => 'colleague_evaluate_instructor'));
        } else {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                return $this->redirect(array('action' => 'head_evaluate_instructor'));
            } else {
                return $this->redirect('/');
            }
        }
    }

    public function colleague_evaluate_instructor($staff_id = null)
    {

        $this->layout = 'default_nobackrefresh';
        $this->__init_search();
        $this->__colleague_evaluate_instructor($staff_id);
    }

    private function __colleague_evaluate_instructor($staff_id = null)
    {

        $default_staff_id = null;
        $this->__init_search();

        if (isset($this->request->data['getInstructorList'])) {
            $colleagueLists = $this->ColleagueEvalutionRate->getNotEvaluatedColleagues(
                $this->request->data,
                $this->Auth->user('id')
            );

            if (empty($colleagueLists)) {
                if (!empty($this->request->data['Search']['name'])) {
                    $this->Flash->info(
                        'No result found. This is could happen if either you evaluated staff named/starts with "' . $this->request->data['Search']['name'] . '" earlier or no staff is found named/name starting with "' . $this->request->data['Search']['name'] . '" to evaluate for ' . ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year'] . ' academic year.'
                    );
                } else {
                    $this->Flash->info(
                        'No result found. This is could happen if you evaluated all colleagues earlier for ' . ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year'] . ' academic year or all your colleagues evaluations are already printed.'
                    );
                }
            } else {
                $default_staff_id = array_keys($colleagueLists)[0];
            }
        }

        //get list of instructors
        if (isset($this->request->data['submitEvaluationResult'])) {
            $colleagueList = array();
            $count = 0;

            $instructor = $this->ColleagueEvalutionRate->Staff->find(
                'first',
                array(
                    'conditions' => array('Staff.id' => $this->request->data['Search']['staff_id']),
                    'contain' => array('Title', 'Position')
                )
            );
            $evaluatorStaff = $this->ColleagueEvalutionRate->Staff->find(
                'first',
                array(
                    'conditions' => array('Staff.user_id' => $this->Auth->user('id')),
                    'contain' => array('Title', 'Position')
                )
            );

            $isInstructorEvaluated = $this->ColleagueEvalutionRate->find('count', array(
                'conditions' => array(
                    'ColleagueEvalutionRate.semester' => $this->request->data['Search']['semester'],
                    'ColleagueEvalutionRate.academic_year' => $this->request->data['Search']['acadamic_year'],
                    'ColleagueEvalutionRate.staff_id' => $this->request->data['Search']['staff_id'],
                    'ColleagueEvalutionRate.evaluator_id' => $evaluatorStaff['Staff']['id'],
                    'ColleagueEvalutionRate.dept_head = 0'
                )
            ));

            if ($isInstructorEvaluated == 0) {
                if (isset($this->request->data['ColleagueEvalutionRate']) && !empty($this->request->data['ColleagueEvalutionRate'])) {
                    foreach ($this->request->data['ColleagueEvalutionRate'] as $key => $value) {
                        $colleagueList['ColleagueEvalutionRate'][$count]['academic_year'] = $this->request->data['Search']['acadamic_year'];
                        $colleagueList['ColleagueEvalutionRate'][$count]['semester'] = $this->request->data['Search']['semester'];
                        $colleagueList['ColleagueEvalutionRate'][$count]['instructor_evalution_question_id'] = $value['instructor_evalution_question_id'];
                        $colleagueList['ColleagueEvalutionRate'][$count]['staff_id'] = $this->request->data['Search']['staff_id'];
                        $colleagueList['ColleagueEvalutionRate'][$count]['evaluator_id'] = $evaluatorStaff['Staff']['id'];
                        $colleagueList['ColleagueEvalutionRate'][$count]['rating'] = $value['rating'];
                        $count++;
                    }
                }

                if (!empty($colleagueList['ColleagueEvalutionRate'])) {
                    if ($this->ColleagueEvalutionRate->saveAll($colleagueList['ColleagueEvalutionRate'])) {
                        $this->Flash->success(
                            'Thank you! You have evaluated ' . $instructor['Title']['title'] . '. ' . $instructor['Staff']['full_name'] . ' (' . $instructor['Position']['position'] . ') for ' . ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year'] . ' academic year. Please fill the next instructor evaluation.'
                        );
                        unset($this->request->data['ColleagueEvalutionRate']);
                        unset($this->request->data['submitEvaluationResult']);

                        if (!empty($this->request->data['Search']['name'])) {
                            $this->request->data['Search']['name'] = '';
                        }

                        $this->request->data['getInstructorList'] = true;

                        $colleagueLists = $this->ColleagueEvalutionRate->getNotEvaluatedColleagues(
                            $this->request->data,
                            $this->Auth->user('id')
                        );

                        if (empty($colleagueLists)) {
                            $this->Flash->info(
                                'No result found. This is could happen if you evaluated all your colleagues earlier for ' . ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year'] . ' academic year or all your colleagues evaluations are already printed.'
                            );
                        } else {
                            $default_staff_id = array_keys($colleagueLists)[0];
                        }
                    } else {
                        $this->Flash->error(
                            'The evalution of ' . $instructor['Title']['title'] . '. ' . $instructor['Staff']['full_name'] . ' (' . $instructor['Position']['position'] . ') for ' . ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year'] . ' academic year could not be saved. Please, try again.'
                        );
                    }
                } else {
                    $this->Flash->error('Empty Evaluation. Please, try again.');
                }
            } else {
                $this->Flash->warning(
                    'You have already evaluated ' . $instructor['Title']['title'] . '. ' . $instructor['Staff']['full_name'] . ' (' . $instructor['Position']['position'] . ')  for ' . ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year'] . ' academic year.'
                );
            }
        }

        $instructorEvalutionQuestionsObjective = $this->ColleagueEvalutionRate->InstructorEvalutionQuestion->find(
            'all',
            array(
                'conditions' => array(
                    'InstructorEvalutionQuestion.type' => 'objective',
                    'InstructorEvalutionQuestion.for' => 'colleague',
                    'InstructorEvalutionQuestion.active' => 1
                ),
                'fields' => array(
                    'InstructorEvalutionQuestion.id',
                    'InstructorEvalutionQuestion.question',
                    'InstructorEvalutionQuestion.question_amharic'
                )
            )
        );

        $this->__init_search();

        $this->set(compact('default_staff_id', 'colleagueLists', 'instructorEvalutionQuestionsObjective'));
        $this->render('colleague_evaluate_instructor');
    }

    public function head_evaluate_instructor($staff_id = null)
    {

        $this->__init_search();
        $this->layout = 'default_nobackrefresh';
        $this->__head_evaluate_instructor($staff_id);
    }

    public function instructor_evaluation_report($staff_id = null)
    {

        $this->__init_search();
        $this->__instructor_evaluation_report($staff_id);
    }

    private function __instructor_evaluation_report($staff_id = null)
    {

        $this->__init_search();

        if (isset($this->request->data['getInstructorList'])) {
            $colleagueLists = $this->ColleagueEvalutionRate->getEvaluatedColleaguesListForHeadReport(
                $this->request->data,
                $this->Auth->user('id')
            );
            if (empty($colleagueLists)) {
                $this->Flash->info('No result found.');
            } else {
                $this->set(compact('colleagueLists'));
            }
        }

        //get list of instructors
        if (isset($this->request->data['generateEvaluationReport'])) {
            $evaluationAggregateds = $this->ColleagueEvalutionRate->getInstructorEvaluationResult(
                $this->request->data,
                $this->department_id
            );

            if (empty($evaluationAggregateds)) {
                $this->Flash->warning('There is no evaluation report for selected instructor.');
            } else {
                $first_staff_id = 0;

                //debug($evaluationAggregateds);

                foreach ($evaluationAggregateds as $key => $value) {
                    if (!empty($value['EvaluatedStaffDetail']['Staff']['id'])) {
                        $first_staff_id = $value['EvaluatedStaffDetail']['Staff']['id'];
                        debug($value['EvaluatedStaffDetail']['academic_year']);
                        debug($value['EvaluatedStaffDetail']['semester']);

                        $printedEvaluationUpdate = ClassRegistry::init('CourseInstructorAssignment')->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'CourseInstructorAssignment.staff_id' => $value['EvaluatedStaffDetail']['Staff']['id'],
                                    'CourseInstructorAssignment.academic_year' => $value['EvaluatedStaffDetail']['academic_year'],
                                    'CourseInstructorAssignment.semester' => $value['EvaluatedStaffDetail']['semester'],
                                    'CourseInstructorAssignment.evaluation_printed' => 0
                                ),
                                'fields' => array('CourseInstructorAssignment.id', 'CourseInstructorAssignment.id')
                            )
                        );

                        if (!empty($printedEvaluationUpdate)) {
                            debug($printedEvaluationUpdate);
                            debug(
                                ClassRegistry::init('CourseInstructorAssignment')->updateAll(
                                    array('CourseInstructorAssignment.evaluation_printed' => 1),
                                    array('CourseInstructorAssignment.id' => $printedEvaluationUpdate)
                                )
                            );
                        }
                    }
                }

                $this->set(compact('evaluationAggregateds', 'first_staff_id'));
                $this->response->type('application/pdf');
                $this->layout = '/pdf/default';
                $this->render('instructor_evaluation_report_pdf');

                unset($this->request->data['ColleagueEvalutionRate']['select_all']);
                unset($this->request->data);
                return;
            }
        }

        $this->set(compact('colleagueLists'));
        $this->render('instructor_evaluation_report');
    }

    private function __head_evaluate_instructor($staff_id = null)
    {

        $default_staff_id = null;
        $this->__init_search();

        //debug($this->request->data);

        if (isset($this->request->data['getInstructorList'])) {
            $colleagueLists = $this->ColleagueEvalutionRate->getNotEvaluatedColleaguesListForHead(
                $this->request->data,
                $this->Auth->user('id')
            );

            if (empty($colleagueLists)) {
                if (!empty($this->request->data['Search']['name'])) {
                    $this->Flash->info(
                        'No result found. This is could happen if either you evaluated staff named/starts with "' . $this->request->data['Search']['name'] . '" earlier or no staff is found named/name starting with "' . $this->request->data['Search']['name'] . '" who is evaluated by colleague and waiting your evaluation for ' . ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year'] . ' academic year.'
                    );
                } else {
                    $this->Flash->info(
                        'No result found. This is could happen if you evaluated all colleagues earlier for ' . ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year'] . ' academic year or no instructor is found who is evaluated by colleagues and waiting your evaluation or all colleague evaluations are already printed.'
                    );
                }
            } else {
                $default_staff_id = array_keys($colleagueLists)[0];
            }
        }

        /* if (isset($this->request->data['getInstructorList'])) {
            $colleagueLists = $this->ColleagueEvalutionRate->getNotEvaluatedColleaguesListForHead($this->request->data, $this->Auth->user('id'));
        } else {

            $acYearAndSemester = $this->AcademicYear->current_acy_and_semester();

            if (!isset($this->request->data['Search']['semester']) && !isset($this->request->data['Search']['acadamic_year'])) {
                $this->request->data['Search']['acadamic_year'] = $acYearAndSemester['academic_year'];
                if ($acYearAndSemester['semester'] == 'II') {
                    $this->request->data['Search']['semester'] = 'I';
                } else if ($acYearAndSemester['semester'] == 'III') {
                    $this->request->data['Search']['semester'] = 'II';
                } else {
                    $this->request->data['Search']['semester'] = $acYearAndSemester['semester'];
                }
            }

            $colleagueLists = $this->ColleagueEvalutionRate->getNotEvaluatedColleaguesListForHead($this->request->data, $this->Auth->user('id'));
        } */

        if (isset($this->request->data['submitEvaluationResult'])) {
            $count = 0;

            $instructor = $this->ColleagueEvalutionRate->Staff->find(
                'first',
                array(
                    'conditions' => array('Staff.id' => $this->request->data['Search']['staff_id']),
                    'contain' => array('Title', 'Position')
                )
            );
            $evaluatorStaff = $this->ColleagueEvalutionRate->Staff->find(
                'first',
                array(
                    'conditions' => array('Staff.user_id' => $this->Auth->user('id')),
                    'contain' => array('Title', 'Position')
                )
            );

            $isInstructorEvaluated = $this->ColleagueEvalutionRate->find('count', array(
                'conditions' => array(
                    'ColleagueEvalutionRate.semester' => $this->request->data['Search']['semester'],
                    'ColleagueEvalutionRate.academic_year' => $this->request->data['Search']['acadamic_year'],
                    'ColleagueEvalutionRate.staff_id' => $this->request->data['Search']['staff_id'],
                    //'ColleagueEvalutionRate.evaluator_id' => $evaluatorStaff['Staff']['id'], //different department accounts can be used
                    'ColleagueEvalutionRate.dept_head = 1'
                )
            ));

            if ($isInstructorEvaluated == 0) {
                if (isset($this->request->data['ColleagueEvalutionRate']) && !empty($this->request->data['ColleagueEvalutionRate'])) {
                    foreach ($this->request->data['ColleagueEvalutionRate'] as $key => $value) {
                        $colleagueList['ColleagueEvalutionRate'][$count]['academic_year'] = $this->request->data['Search']['acadamic_year'];
                        $colleagueList['ColleagueEvalutionRate'][$count]['semester'] = $this->request->data['Search']['semester'];
                        $colleagueList['ColleagueEvalutionRate'][$count]['instructor_evalution_question_id'] = $value['instructor_evalution_question_id'];
                        $colleagueList['ColleagueEvalutionRate'][$count]['staff_id'] = $this->request->data['Search']['staff_id'];
                        $colleagueList['ColleagueEvalutionRate'][$count]['evaluator_id'] = $evaluatorStaff['Staff']['id'];
                        $colleagueList['ColleagueEvalutionRate'][$count]['rating'] = $value['rating'];
                        $colleagueList['ColleagueEvalutionRate'][$count]['dept_head'] = 1;
                        $count++;
                    }
                }

                if (!empty($colleagueList['ColleagueEvalutionRate'])) {
                    if ($this->ColleagueEvalutionRate->saveAll($colleagueList['ColleagueEvalutionRate'])) {
                        $this->Flash->success(
                            'Thank you!, You have evaluated ' . $instructor['Title']['title'] . '. ' . $instructor['Staff']['full_name'] . ' (' . $instructor['Position']['position'] . ')  for ' . ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year'] . ' academic year. Please fill the next instructor evaluation.'
                        );
                        //$this->redirect(array('action'=> 'head_evaluate_instructor'));

                        unset($this->request->data['ColleagueEvalutionRate']);
                        unset($this->request->data['submitEvaluationResult']);

                        if (!empty($this->request->data['Search']['name'])) {
                            $this->request->data['Search']['name'] = '';
                        }

                        $this->request->data['getInstructorList'] = true;

                        $colleagueLists = $this->ColleagueEvalutionRate->getNotEvaluatedColleaguesListForHead(
                            $this->request->data,
                            $this->Auth->user('id')
                        );

                        if (empty($colleagueLists)) {
                            $this->Flash->info(
                                'No result found. This is could happen if you evaluated all colleagues earlier for ' . ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year'] . ' academic year or no instructor is found who is evaluated by colleague and waiting your evaluation or all colleague evaluations are already printed.'
                            );
                        } else {
                            $default_staff_id = array_keys($colleagueLists)[0];
                        }
                    } else {
                        $this->Flash->error(
                            'The evalution of ' . $instructor['Title']['title'] . '. ' . $instructor['Staff']['full_name'] . ' (' . $instructor['Position']['position'] . ') for ' . ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year'] . ' academic year could not be saved. Please, try again.'
                        );
                    }
                } else {
                    $this->Flash->error('Empty Evaluation. Please, try again.');
                }
            } else {
                $this->Flash->warning(
                    'You have already evaluated ' . $instructor['Title']['title'] . '. ' . $instructor['Staff']['full_name'] . ' (' . $instructor['Position']['position'] . ') for ' . ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year'] . ' academic year.'
                );
            }
        }

        $instructorEvalutionQuestionsObjective = $this->ColleagueEvalutionRate->InstructorEvalutionQuestion->find(
            'all',
            array(
                'conditions' => array(
                    'InstructorEvalutionQuestion.type' => 'objective',
                    'InstructorEvalutionQuestion.for' => 'dep-head',
                    'InstructorEvalutionQuestion.active' => 1
                ),
                'fields' => array(
                    'InstructorEvalutionQuestion.id',
                    'InstructorEvalutionQuestion.question',
                    'InstructorEvalutionQuestion.question_amharic',
                )
            )
        );


        $this->__init_search();

        $this->set(compact('default_staff_id', 'colleagueLists', 'instructorEvalutionQuestionsObjective'));
        $this->render('head_evaluate_instructor');
    }
}

<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class PlacementsResultsCriteriasController extends AppController
{

    public $name = 'PlacementsResultsCriterias';
    public $menuOptions = array(
        'parent' => 'placement',
        'exclude' => array('result_category_graph'),
        'alias' => array(
            'index' => 'List Placement Result Criteria',
            'add' => 'Add Placement Result Criteria',
        ),
        'exclude' => array('result_category_graph')
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
        $this->Auth->Allow('getPlacementResultCriteria');
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyearArray();
        //To diplay current academic year as default in drop down list
        $selected = $this->AcademicYear->currentAcademicYear();
        foreach ($acyear_array_data as $k => $v) {
            if ($v == $selected) {
                $selected = $k;
                break;
            }
        }
        $this->set(compact('acyear_array_data', 'selected'));
    }

    public function index()
    {

        $this->PlacementsResultsCriteria->recursive = 0;
        $this->paginate['order'] = 'PlacementsResultsCriteria.admissionyear DESC';
        $this->paginate['conditions'][] = array(
            'PlacementsResultsCriteria.college_id' => $this->college_id,
        );
        $this->Paginator->settings = $this->paginate;
        debug($this->Paginator->settings);
        $this->set('placementsResultsCriterias', $this->Paginator->paginate('PlacementsResultsCriteria'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid placements results criteria'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('placementsResultsCriteria', $this->PlacementsResultsCriteria->read(null, $id));
    }

    public function add($academicyear_set = null, $result_type = null)
    {

        if (!empty($academicyear_set)) {
            $this->request->data['PlacementsResultsCriteria']['admissionyear'] = str_replace(
                '-',
                '/',
                $academicyear_set
            );
            $this->request->data['PlacementsResultsCriteria']['prepartory_result'] = $result_type;
        }

        if (!empty($this->request->data)) {
            $selected_academicyear = $this->request->data['PlacementsResultsCriteria']['admissionyear'];
            $check_auto_placement_already_run_not_allow_adding_or_edit = ClassRegistry::init('AcceptedStudent')->find(
                'count',
                array(
                    'conditions' => array(
                        'AcceptedStudent.academicyear' => $selected_academicyear,
                        'AcceptedStudent.college_id' => $this->college_id,
                        'AcceptedStudent.placementtype' =>
                            AUTO_PLACEMENT
                    )
                )
            );

            $result_type = $this->request->data['PlacementsResultsCriteria']['prepartory_result'];
            $this->Session->write('result_criteria_ac', $selected_academicyear);
            $this->Session->write('result_type', $result_type);
            $is_preparatory = null;
            $result_category = null;
            $average = 0;
            $min = 0;
            $max = 0;
            if ($result_type) {
                $is_preparatory = 'EHEECE_total_results';
            } else {
                $is_preparatory = 'freshman_result';

                ClassRegistry::init('AcceptedStudent')->copyFreshmanResult(
                    $this->request->data['PlacementsResultsCriteria']['admissionyear'],
                    $this->college_id
                );
            }
            //Copy freshman semester I result to accepted_students table here


            $previous_result_category = $this->PlacementsResultsCriteria->find(
                'all',

                array(
                    'conditions' => array(
                        'PlacementsResultsCriteria.admissionyear'
                        => $selected_academicyear,
                        'PlacementsResultsCriteria.college_id' =>
                            $this->college_id
                    ),
                    'fields' => array(
                        'PlacementsResultsCriteria.name',
                        'PlacementsResultsCriteria.id',
                        'PlacementsResultsCriteria.result_from',
                        'PlacementsResultsCriteria.result_to'
                    ),
                    'order' => 'PlacementsResultsCriteria.result_to DESC',
                    'recursive' => -1
                )
            );

            $max = ClassRegistry::init('AcceptedStudent')->find(
                'first',
                array(
                    'fields' => array("MAX(" . $is_preparatory . ")"),
                    'conditions' => array(
                        'AcceptedStudent.college_id' => $this->college_id,
                        "OR" => array(
                            'AcceptedStudent.department_id is null',
                            'AcceptedStudent.department_id' => array(0, '')
                        ),
                        'AcceptedStudent.academicyear' => $this->request->data['PlacementsResultsCriteria']['admissionyear']
                    )
                )
            );
            $min = ClassRegistry::init('AcceptedStudent')->find(
                'first',
                array(
                    'fields' => array("MIN(" . $is_preparatory . ")"),
                    'conditions' => array(
                        'AcceptedStudent.college_id' => $this->college_id,
                        "OR" => array(
                            'AcceptedStudent.department_id is null',
                            'AcceptedStudent.department_id' => array(0, '')
                        ),
                        'AcceptedStudent.academicyear' => $this->request->data['PlacementsResultsCriteria']['admissionyear']
                    )
                )
            );
            $max = $max[0]['MAX(' . $is_preparatory . ')'];
            $min = $min[0]['MIN(' . $is_preparatory . ')'];

            /*$result_category=ClassRegistry::init('AcceptedStudent')->find('all',
            array('conditions'=>array(
        'AcceptedStudent.college_id'=>$this->college_id,
        'AcceptedStudent.academicyear'=>$this->request->data['PlacementsResultsCriteria']['admissionyear'])));
        */

            $average = ClassRegistry::init('AcceptedStudent')->find(
                'first',
                array(
                    'fields' => array("AVG(" . $is_preparatory . ")"),
                    'conditions' => array(
                        'AcceptedStudent.college_id' => $this->college_id,
                        "OR" => array(
                            'AcceptedStudent.department_id is null',
                            'AcceptedStudent.department_id' => array(0, '')
                        ),
                        'AcceptedStudent.academicyear' => $this->request->data['PlacementsResultsCriteria']['admissionyear']
                    )
                )
            );

            $average = $average[0]['AVG(' . $is_preparatory . ')'];
            $this->set(compact('selected_academicyear', 'result_type', 'previous_result_category'));
            $this->set(compact('max', 'min', 'average', 'previous_result_category'));
            if (isset($this->request->data['prepandacademicyear'])) {
                //$this->set($this->request->data);
                if (!empty($this->request->data['PlacementsResultsCriteria']['admissionyear'])) {
                } else {
                    $this->Session->setFlash(
                        __(
                            '<span></span>
			             The placements results criteria needs academic year.
			             Please, select academic year.',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    $this->redirect(array('action' => 'add'));
                }
            }
            if (isset($this->request->data['addresultcategory'])) {
                $isPrepartory = $this->PlacementsResultsCriteria->isPrepartoryResult(
                    $this->request->data['PlacementsResultsCriteria']['admissionyear'],
                    $this->college_id
                );
                $isPrepartory2 = $this->PlacementsResultsCriteria->isPrepartoryResult2(
                    $this->request->data['PlacementsResultsCriteria']['admissionyear'],
                    $this->college_id
                );
                if ($isPrepartory2 !== -1 && $isPrepartory != $this->request->data['PlacementsResultsCriteria']['prepartory_result']) {
                    $this->Session->setFlash(
                        __(
                            '<span></span>Placement results criteria with <u>' . ($isPrepartory == 1 ? 'preparatory' : 'freshman') . '</u> result is already recorded for ' . $this->request->data['PlacementsResultsCriteria']['admissionyear'] . ' academic year. Please, delete all recorded result category in-order to record by <u>' . ($isPrepartory == 0 ? 'preparatory' : 'freshman') . '</u> result so that you can avoid conflicts.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                } else {
                    $result_count = ClassRegistry::init('AcceptedStudent')->find(
                        'count',
                        array(
                            'conditions' =>
                                array(
                                    'AcceptedStudent.freshman_result IS NOT NULL',
                                    'AcceptedStudent.college_id' => $this->college_id


                                )
                        )
                    );
                    if ($isPrepartory2 !== -1 && $isPrepartory == 0 && $result_count == 0) {
                        $this->Session->setFlash(
                            __(
                                '<span></span>There is no freshman result to record result category. Please wait till student grade is submitted and their semester status is generated.'
                            ),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    } else {
                        $this->PlacementsResultsCriteria->create();
                        $this->set($this->request->data);
                        if ($this->PlacementsResultsCriteria->validates()) {
                            $is_already_recorded = $this->PlacementsResultsCriteria->find(
                                'count',
                                array(
                                    'conditions' => array(
                                        'PlacementsResultsCriteria.college_id' => $this->college_id,
                                        'PlacementsResultsCriteria.admissionyear' =>
                                            $this->request->data['PlacementsResultsCriteria']['admissionyear'],
                                        'PlacementsResultsCriteria.result_from' =>
                                            $this->request->data['PlacementsResultsCriteria']['result_from'],
                                        'PlacementsResultsCriteria.result_to' =>
                                            $this->request->data['PlacementsResultsCriteria']['result_to'],
                                        'PlacementsResultsCriteria.prepartory_result' =>
                                            $this->request->data['PlacementsResultsCriteria']['prepartory_result']
                                    )
                                )
                            );

                            if ($is_already_recorded == 0) {
                                //result should not exceed the maximum and
                                //lower than the minimum result
                                if ($this->PlacementsResultsCriteria->
                                resultCategoryInput(
                                    $this->request->data['PlacementsResultsCriteria'],
                                    $max,
                                    $min
                                )) {
                                    //check continutiy of grade range
                                    //$this->PlacementsResultsCriteria->gradeRangeContinuty($this->request->data['PlacementsResultsCriteria'])
                                    if (true) {
                                        if ($this->PlacementsResultsCriteria->save($this->request->data)) {
                                            $this->Session->setFlash(
                                                __(
                                                    '<span></span>The placements
				                        results criteria has been saved',
                                                    true
                                                ),
                                                'default',
                                                array('class' => 'success-box success-message')
                                            );
                                            $previous_result_category = $this->PlacementsResultsCriteria->find(
                                                'all',

                                                array(
                                                    'conditions' => array(
                                                        'PlacementsResultsCriteria.admissionyear'
                                                        => $selected_academicyear,
                                                        'PlacementsResultsCriteria.college_id' =>
                                                            $this->college_id
                                                    ),
                                                    'fields' => array(
                                                        'PlacementsResultsCriteria.name',
                                                        'PlacementsResultsCriteria.id',
                                                        'PlacementsResultsCriteria.result_from',
                                                        'PlacementsResultsCriteria.result_to'
                                                    ),
                                                    'order' => 'PlacementsResultsCriteria.result_to DESC',
                                                    'recursive' => -1
                                                )
                                            );

                                            unset($this->request->data['PlacementsResultsCriteria']['name']);
                                            unset($this->request->data['PlacementsResultsCriteria']['result_to']);
                                            unset($this->request->data['PlacementsResultsCriteria']['result_from']);
                                            // $this->redirect(array('action' => 'index'));
                                        } else {
                                            $this->Session->setFlash(
                                                __(
                                                    '<span></span>The
				                        placements results criteria could not be saved.
				                        Please, try again.',
                                                    true
                                                ),
                                                'default',
                                                array('class' => 'error-box error-message')
                                            );
                                        }
                                    } else {
                                        $error = $this->PlacementsResultsCriteria->invalidFields();

                                        if (isset($error['grade_range_continuty'])) {
                                            $this->Session->setFlash(
                                                __(
                                                    '<span></span>' .
                                                    $error['grade_range_continuty'][0],
                                                    true
                                                ),
                                                'default',
                                                array('class' => 'error-box error-message')
                                            );
                                        }
                                    }
                                } else {
                                    $error = $this->PlacementsResultsCriteria->invalidFields();


                                    if (isset($error['result_criteria_name'])) {
                                        $this->Session->setFlash(
                                            __(
                                                '<span></span>' .
                                                $error['result_criteria_name'][0],
                                                true
                                            ),
                                            'default',
                                            array('class' => 'error-box error-message')
                                        );
                                    } elseif (isset($error['result_from'])) {
                                        $this->Session->setFlash(
                                            __('<span></span>' . $error['result_from'][0]),
                                            'default',
                                            array('class' => 'error-box error-message')
                                        );
                                    } elseif (isset($error['result_to'])) {
                                        $this->Session->setFlash(
                                            __('<span></span>' . $error['result_to'][0]),
                                            'default',
                                            array('class' => 'error-box error-message')
                                        );
                                    } elseif (isset($error['result_from_problem'])) {
                                        $this->Session->setFlash(
                                            __('<span></span>' . $error['result_from_problem'][0]),
                                            'default',
                                            array('class' => 'error-box error-message')
                                        );
                                    } elseif (isset($error['result_to_problem'])) {
                                        $this->Session->setFlash(
                                            __('<span></span>' . $error['result_to_problem'][0]),
                                            'default',
                                            array('class' => 'error-box error-message')
                                        );
                                    } elseif (isset($error['result_from_to'])) {
                                        $this->Session->setFlash(
                                            __('<span></span>' . $error['result_from_to'][0]),
                                            'default',
                                            array('class' => 'error-box error-message')
                                        );
                                    }
                                }
                            } else {
                                $this->Session->setFlash(
                                    __(
                                        '<span></span>The
				                placements results range has already recorded. Please,
				                change the range.',
                                        true
                                    ),
                                    'default',
                                    array('class' => 'error-box error-message')
                                );
                            }
                        }
                    }
                }
            }

            $this->set('prepartory_academic_year', true);

            $this->set(
                compact(
                    'selected_academicyear',
                    'check_auto_placement_already_run_not_allow_adding_or_edit',
                    'result_type',
                    'previous_result_category'
                )
            );
            $this->set(compact('max', 'min', 'average', 'previous_result_category'));
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(
                __('<span></span>Invalid placements results criteria'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            $this->PlacementsResultsCriteria->create();
            $this->set($this->request->data);
            if ($this->PlacementsResultsCriteria->validates()) {
                //result should not exceed the maximum and
                //lower than the minimum result
                if ($this->PlacementsResultsCriteria->
                resultCategoryInput(
                    $this->request->data['PlacementsResultsCriteria'],
                    null,
                    null
                )) {
                    if ($this->PlacementsResultsCriteria->save($this->request->data)) {
                        $this->Session->setFlash(
                            __(
                                '<span></span>The placements
	                        results criteria has been saved',
                                true
                            ),
                            'default',
                            array('class' => 'success-box success-message')
                        );
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(
                            __(
                                '<span></span>The
	                        placements results criteria could not be saved.
	                        Please, try again.',
                                true
                            ),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                } else {
                    $error = $this->PlacementsResultsCriteria->invalidFields();
                    debug($error);

                    if (isset($error['result_criteria_name'])) {
                        $this->Session->setFlash(
                            __('<span></span>' . $error['result_criteria_name'], true),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    } elseif (isset($error['result_from'])) {
                        $this->Session->setFlash(
                            __('<span></span>' . $error['result_from']),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    } elseif (isset($error['result_to'])) {
                        $this->Session->setFlash(
                            __('<span></span>' . $error['result_to']),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    } elseif (isset($error['result_from_problem'])) {
                        $this->Session->setFlash(
                            __('<span></span>' . $error['result_from_problem']),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    } elseif (isset($error['result_to_problem'])) {
                        $this->Session->setFlash(
                            __('<span></span>' . $error['result_to_problem']),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    } elseif (isset($error['result_from_to'])) {
                        $this->Session->setFlash(
                            __('<span></span>' . $error['result_from_to']),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                }
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->PlacementsResultsCriteria->read(null, $id);
        }
        $colleges = $this->PlacementsResultsCriteria->College->find('list');
        $this->set(compact('colleges'));
    }

    public function delete($id = null, $academicyear = null, $result_type = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                __('<span></span>Invalid id for placements results criteria'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }


        $reserved_place = $this->PlacementsResultsCriteria->find(
            'first',
            array('conditions' => array('PlacementsResultsCriteria.id' => $id))
        );
        $this->loadModel('PlacementLock');
        $auto_run = $this->PlacementLock->find('count', array(
            'conditions' => array(
                'PlacementLock.college_id' => $this->college_id,
                'PlacementLock.academic_year' => $reserved_place['PlacementsResultsCriteria']['admissionyear'],
                'PlacementLock.process_start' => 1
            )
        ));
        if ($auto_run) {
            $this->Session->setFlash(
                '<span></span>' . __(
                    'The auto placement is up and running you can not delete the participationg department right now, please come back after you cancelled the auto placement.'
                ),
                'default',
                array('class' => 'error-box error-message')
            );
            $this->redirect(array('action' => 'index'));
        }

        if (count($reserved_place['ReservedPlace']) == 0) {
            if ($this->PlacementsResultsCriteria->delete($id)) {
                $this->Session->setFlash(
                    __('<span></span>Placements results criteria deleted'),
                    'default',
                    array('class' => 'success-box success-message')
                );

                $this->redirect(array('action' => 'add', $academicyear, $result_type));
            }
        } else {
            // delete the related reserved place of the result criteria
            $this->loadModel('ReservedPlace');
            $is_used_reserve = $this->ReservedPlace->find(
                'count',
                array('conditions' => array('ReservedPlace.placements_results_criteria_id' => $id))
            );
            if ($is_used_reserve > 0) {
                $this->redirect(array('action' => 'add', $academicyear, $result_type));
            } else {
                if ($this->PlacementsResultsCriteria->delete($id)) {
                    /*$this->loadModel('ReservedPlace');
                    $this->ReservedPlace->deleteAll(array('ReservedPlace.placements_results_criteria_id'=>$id));
                    $this->Session->setFlash(__('<span></span>Placements results criteria and its associated reserved place has been deleted'),
                    'default', array('class' => 'success-box success-message'));
                    */
                    $this->redirect(array('action' => 'add', $academicyear, $result_type));
                }
            }
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            __('<span></span>Placements results criteria was not deleted.'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }

    /**
     *This function will set an aggregate report
     */
    public function result_category_graph()
    {

        $this->layout = 'ajax';
        $selected_academicyear = $this->Session->read('result_criteria_ac');
        $result_type = $this->Session->read('result_type');
        $average = 0;
        $min = 0;
        $max = 0;
        if ($result_type) {
            $is_preparatory = 'EHEECE_total_results';
        } else {
            $is_preparatory = 'freshman_result';
        }
        $max = ClassRegistry::init('AcceptedStudent')->find(
            'first',
            array(
                'fields' => array("MAX(" . $is_preparatory . ")"),
                'conditions' => array(
                    'AcceptedStudent.college_id' => $this->college_id,
                    'AcceptedStudent.academicyear' => $this->request->data['PlacementsResultsCriteria']['admissionyear']
                )
            )
        );
        $min = ClassRegistry::init('AcceptedStudent')->find(
            'first',
            array(
                'fields' => array("MIN(" . $is_preparatory . ")"),
                'conditions' => array(
                    'AcceptedStudent.college_id' => $this->college_id,
                    'AcceptedStudent.academicyear' => $this->request->data['PlacementsResultsCriteria']['admissionyear']
                )
            )
        );
        $max = $max[0]['MAX(' . $is_preparatory . ')'];
        $min = $min[0]['MIN(' . $is_preparatory . ')'];

        /*$result_category=ClassRegistry::init('AcceptedStudent')->find('all',
        array('conditions'=>array(
    'AcceptedStudent.college_id'=>$this->college_id,
    'AcceptedStudent.academicyear'=>$this->request->data['PlacementsResultsCriteria']['admissionyear'])));
    */

        $average = ClassRegistry::init('AcceptedStudent')->find(
            'first',
            array(
                'fields' => array("AVG(" . $is_preparatory . ")"),
                'conditions' => array(
                    'AcceptedStudent.college_id' => $this->college_id,
                    'AcceptedStudent.academicyear' => $this->request->data['PlacementsResultsCriteria']['admissionyear']
                )
            )
        );

        $average = $average[0]['AVG(' . $is_preparatory . ')'];
        $graph = true;
        $this->set(compact('max', 'min', 'average', 'previous_result_category', 'graph'));
    }

    public function getPlacementResultCriteria($academicYear)
    {

        $this->layout = 'ajax';

        $resultList = $this->PlacementsResultsCriteria->getPlacementResultCriteria(
            $this->college_id,
            str_replace('-', '/', $academicYear)
        );

        $this->set(compact('resultList'));
    }
}

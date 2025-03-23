<?php

namespace App\Controller;


use App\Controller\AppController;


use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class AcademicStandsController extends AppController
{

    public $name = 'AcademicStands';
    public $menuOptions = array(

        'parent' => 'dashboard',
        'exclude' => array('search'),
        'alias' => array(
            'index' => 'View All Stands',
            'add' => 'Set Academic Stands',

        )
    );

    public $paginate = array();

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('AcademicYear'); // Ensure Paginator is loaded


    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow('search');
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
        foreach ($this->request->data as $k => $v) {
            foreach ($v as $kk => $vv) {
                $url[$k . '.' . $kk] = $vv;
            }
        }

        // redirect the user to the url
        return $this->redirect($url, null, true);
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyear_array();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->current_academicyear();
        foreach ($acyear_array_data as $k => $v) {
            if ($v == $defaultacademicyear) {
                $defaultacademicyear = $k;
                break;
            }
        }
        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
    }

    public function index()
    {

        //$this->AcademicStand->recursive = 0;
        $this->paginate = array(
            'contain' => array(
                'Program',
                'AcademicStatus',
                'AcademicRule'
            ),
            'order' => 'AcademicStand.sort_order ASC'
        );

        if (!empty($this->request->data) && isset($this->request->data['viewAcademicStand'])) {
            $options = array();

            if (!empty($this->request->data['Search']['program_id'])) {
                $options [] = array(
                    'AcademicStand.program_id' => $this->request->data['Search']['program_id']

                );
            }

            if (!empty($this->request->data['Search']['academic_status_id'])) {
                $options [] = array(
                    'AcademicStand.academic_status_id' => $this->request->data['Search']['academic_status_id']

                );
            }


            // filter by curriculum
            if (!empty($this->request->data['Search.academic_year_from'])) {
                $options [] = array(
                    'AcademicStand.academic_year_from like ' => $this->request->data['Search']['academic_year_from'] . '%'

                );
            }
            $this->paginate['conditions'] = $options;
            $this->Paginator->settings = $this->paginate;
            //debug($this->Paginator->settings);
            $academicStands = $this->Paginator->paginate('AcademicStand');
            //debug($academicStands);
            if (empty($academicStands)) {
                $this->Session->setFlash(
                    '<span></span>' .
                    __('There is no academic stand defined in the system in the given criteria.'),
                    'default',
                    array('class' => 'info-box info-message')
                );
            }
        }


        if (!empty($academicStands)) {
            //		debug($academicStands);
            foreach ($academicStands as $ack => &$ackv) {
                $semester = unserialize($ackv['AcademicStand']['semester']);
                $year_level_ids = unserialize($ackv['AcademicStand']['year_level_id']);
                if (!empty($year_level_ids) && is_array($year_level_ids)) {
                    $ackv['AcademicStand']['year_level_id'] = implode(",", $year_level_ids);
                }
                if (!empty($semester) && is_array($semester)) {
                    $ackv['AcademicStand']['semester'] = implode(",", $semester);
                }
            }
            $this->set('academicStands', $academicStands);
        }

        $programs = $this->AcademicStand->Program->find('list');
        $academicStatuses = $this->AcademicStand->AcademicStatus->find(
            'list',
            array('order' => 'AcademicStatus.order ASC')
        );
        $prevAcademicStatuses = $this->AcademicStand->AcademicStatus->find(
            'list',
            array('conditions' => array('AcademicStatus.id' => array(2, 3, 6)))
        );

        $this->set(compact('programs', 'academicStatuses', 'prevAcademicStatuses'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid academic stand'));
            return $this->redirect(array('action' => 'index'));
        }
        $academicStand = $this->AcademicStand->read(null, $id);

        $year_level_ids = array();
        $semester = array();
        if (!empty($academicStand['AcademicStand']['year_level_id'])) {
            $year_level_ids = unserialize($academicStand['AcademicStand']['year_level_id']);
            $academicStand['AcademicStand']['year_level_id'] = implode(',', $year_level_ids);
        }
        if (!empty($academicStand['AcademicStand']['semester'])) {
            $semester = unserialize($academicStand['AcademicStand']['semester']);
            if (!empty($semester)) {
                $academicStand['AcademicStand']['semester'] = implode(',', $semester);
            } else {
                $academicStand['AcademicStand']['semester'] = null;
            }
        }


        $this->set('academicStand', $academicStand);
    }


    public function add()
    {

        if (!empty($this->request->data)) {
            $this->AcademicStand->create();
            if (!empty($this->request->data['AcademicStand']['academic_year_from'])) {
                $reformat_save_update = array();

                $given_new_year = explode('/', $this->request->data['AcademicStand']['academic_year_from']);
                $latestDefinedAcademicRuleDate = $this->AcademicStand->find(
                    'first',
                    array('fields' => array("Max(AcademicStand.academic_year_from) as max_year"), 'recursive' => -1)
                );

                // Needs to close the previous academic rule
                // year when adding a new rule.
                if ($given_new_year[0] > $latestDefinedAcademicRuleDate[0]['max_year']) {
                    if (!empty($latestDefinedAcademicRuleDate[0]['max_year'])) {
                        $update_academic_year_to = $this->AcademicStand->find(
                            'all',
                            array(
                                'fields' => array(
                                    "AcademicStand.id",
                                    "AcademicStand.academic_year_from",
                                    "AcademicStand.academic_year_to"
                                ),
                                'conditions' => $latestDefinedAcademicRuleDate[0]['max_year'],
                                'recursive' => -1
                            )
                        );

                        if (!empty($update_academic_year_to)) {
                            $count = 0;
                            foreach ($update_academic_year_to as $k => $v) {
                                $reformat_save_update['AcademicStand'][$count] = $v['AcademicStand'];
                                $reformat_save_update['AcademicStand'][$count]['academic_year_to'] = $this->request->data['AcademicStand']['academic_year_from'];
                                $count++;
                            }
                        }
                    }
                } else {
                    // allow to add the rule without closing the academic_year_to
                }

                if ($this->AcademicStand->check_duplicate_entry($this->request->data)) {
                    // debug($this->request->data);

                    //validate the
                    $this->set($this->request->data);

                    // dont validate the operator if cgpa is not given
                    if (!empty($this->request->data['AcademicRule'])) {
                        foreach ($this->request->data['AcademicRule'] as $k => &$v) {
                            if (empty($v['cgpa'])) {
                                unset($v['operatorI']);
                                unset($v['cgpa']);
                            }
                            if (empty($v['tcw'])) {
                                unset($v['operatorII']);
                            }

                            if (!empty($v['cgpa']) && empty($v['sgpa']) && empty($v['operatorI'])) {
                                unset($v['sgpa']);
                                unset($v['operatorI']);
                            }
                            if ($v['tcw'] == 1) {
                                //unset($v['operatorI']);
                                unset($v['sgpa']);
                                unset($v['pfw']);
                                //unset($v['cgpa']);
                            }
                            if ($v['pfw'] == 1) {
                                unset($v['sgpa']);
                                unset($v['tcw']);
                            }
                        }
                    }

                    if (!empty($this->request->data['AcademicStand']['semester'])) {
                        $this->request->data['AcademicStand']['semester'] = serialize(
                            $this->request->data['AcademicStand']['semester']
                        );
                    }

                    if (!empty($this->request->data['AcademicStand']['year_level_id'])) {
                        $this->request->data['AcademicStand']['year_level_id'] = serialize(
                            $this->request->data['AcademicStand']['year_level_id']
                        );
                    }


                    if ($this->AcademicStand->saveAll($this->request->data, array('validate' => 'first'))) {
                        $this->Session->setFlash(
                            '<span></span>' . __('The academic rule has been saved.'),
                            'default',
                            array('class' => 'success-box success-message')
                        );

                        //close the academic year to of the previous defined when new rule is defined
                        if (!empty($reformat_save_update)) {
                            $this->AcademicStand->saveAll($reformat_save_update['AcademicStand']);
                        }
                        $this->request->data['AcademicRule'] = null;
                        //$this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(
                            '<span></span>' . __('The academic stand could not be saved. Please, try again.'),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                        $this->request->data['AcademicStand']['semester'] = unserialize(
                            $this->request->data['AcademicStand']['semester']
                        );
                        $this->request->data['AcademicStand']['year_level_id'] = unserialize(
                            $this->request->data['AcademicStand']['year_level_id']
                        );
                    }
                } else {
                    $error = $this->AcademicStand->invalidFields();

                    if (isset($error['duplicate'])) {
                        $this->Session->setFlash(
                            '<span></span>' . __($error['duplicate'][0]),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('Select academic year.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
            //	    return;
        }

        if ($this->role_id == ROLE_REGISTRAR) {
            $yearLevels = $yearLevels = ClassRegistry::init('YearLevel')->distinct_year_level();

            $this->set(compact('yearLevels'));
        }

        $programs = $this->AcademicStand->Program->find('list');
        $academicStatuses = $this->AcademicStand->AcademicStatus->find(
            'list',
            array('order' => 'AcademicStatus.order ASC')
        );
        $prevAcademicStatuses = $this->AcademicStand->AcademicStatus->find(
            'list',
            array('conditions' => array('AcademicStatus.id' => array(2, 3, 6)))
        );
        $this->set(
            compact(
                'programs',
                'academicStatuses',
                'prevAcademicStatuses'
            )
        );
    }

    public function edit($id = null)
    {

        $data = $this->AcademicStand->find('first', array('conditions' => array('AcademicStand.id' => $id)));

        $is_edit_possible = $this->AcademicStand->canEditDeleteAcademicRule($data);
        if (!$is_edit_possible) {
            $error = $this->AcademicStand->invalidFields();

            if (isset($error['used_academic_rule'])) {
                $this->Session->setFlash(
                    '<span></span>' . __($error['used_academic_rule'][0]),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
            $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data)) {
            $this->AcademicStand->create();

            //validate the
            $this->set($this->request->data);

            // dont validate the operator if cgpa is not given
            if (!empty($this->request->data['AcademicRule'])) {
                foreach ($this->request->data['AcademicRule'] as $k => &$v) {
                    if ($v['cgpa'] == "" ||
                        $v['cgpa'] == 0) {
                        unset($v['operatorI']);
                        unset($v['cgpa']);
                    }

                    if ($v['sgpa'] == "" ||
                        $v['sgpa'] == 0) {
                        unset($v['operatorI']);
                        unset($v['sgpa']);
                    }

                    if (empty($v['tcw'])) {
                        unset($v['operatorII']);
                    }

                    if (empty($v['pfw'])) {
                        unset($v['operatorIII']);
                    }
                    if ($v['tcw'] == 1) {
                        if (isset($v['id']) && $v['id'] != "") {
                            $this->AcademicStand->AcademicRule->delete($v['id']);
                        }
                        unset($v['sgpa']);
                        unset($v['pfw']);
                        //unset($v['cgpa']);
                    }
                    if ($v['pfw'] == 1) {
                        if (isset($v['id']) && $v['id'] != "") {
                            $this->AcademicStand->AcademicRule->delete($v['id']);
                        }
                        unset($v['sgpa']);
                        unset($v['tcw']);
                    }
                }
            }

            $this->request->data['AcademicStand']['semester'] = serialize(
                $this->request->data['AcademicStand']['semester']
            );
            $this->request->data['AcademicStand']['year_level_id'] = serialize(
                $this->request->data['AcademicStand']['year_level_id']
            );
            // debug($this->request->data);
            if ($this->AcademicStand->saveAll($this->request->data, array('validate' => 'first'))) {
                $this->Session->setFlash(
                    '<span></span>' . __('The academic rule has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );

                $params = 'Search.academic_year_from:' . $this->request->data['AcademicStand']['academic_year_from'] . '&' . 'Search.program_id:' . $this->request->data['AcademicStand']['program_id'] . '';
                //			return $this->redirect(array('action' => 'index',$params));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The academic stand could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
                $this->request->data['AcademicStand']['semester'] = unserialize(
                    $this->request->data['AcademicStand']['semester']
                );
                $this->request->data['AcademicStand']['year_level_id'] = unserialize(
                    $this->request->data['AcademicStand']['year_level_id']
                );
            }
        }
        if ($this->role_id == ROLE_REGISTRAR) {
            $yearLevels = ClassRegistry::init('YearLevel')->find(
                'all',
                array('fields' => array('DISTINCT YearLevel.name'), 'recursive' => -1)
            );

            $yearleveldistinct = array();
            foreach ($yearLevels as $key => $value) {
                $yearleveldistinct[$value['YearLevel']['name']] = $value['YearLevel']['name'];
            }
            $yearLevels = $yearleveldistinct;

            $this->set(compact('yearLevels'));
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->AcademicStand->read(null, $id);
            $year_level_ids = array();
            $semester = array();
            if (!empty($this->request->data['AcademicStand']['year_level_id'])) {
                $year_level_ids = unserialize($this->request->data['AcademicStand']['year_level_id']);
                $this->request->data['AcademicStand']['year_level_id'] = $year_level_ids;
            }
            if (!empty($this->request->data['AcademicStand']['semester'])) {
                $semester = unserialize($this->request->data['AcademicStand']['semester']);
                if (!empty($semester)) {
                    $this->request->data['AcademicStand']['semester'] = $semester;
                } else {
                    $this->request->data['AcademicStand']['semester'] = null;
                }
            }
        }
        $programs = $this->AcademicStand->Program->find('list');
        $academicStatuses = $this->AcademicStand->AcademicStatus->find('list');
        $prevAcademicStatuses = $this->AcademicStand->AcademicStatus->find(
            'list',
            array('conditions' => array('AcademicStatus.id' => array(2, 3, 6)))
        );
        $this->set(
            compact(
                'programs',
                'academicStatuses',
                'prevAcademicStatuses'
            )
        );
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                __('Invalid id for academic stand'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $data = $this->AcademicStand->find('first', array('conditions' => array('AcademicStand.id' => $id)));
        $is_edit_possible = $this->AcademicStand->canEditDeleteAcademicRule($data);
        if (!$is_edit_possible) {
            $error = $this->AcademicStand->invalidFields();

            if (isset($error['used_academic_rule'])) {
                $this->Session->setFlash(
                    '<span></span>' . __($error['used_academic_rule'][0]),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
            $this->redirect(array('action' => 'index'));
        }
        //check other students has used the academic stands previously then protect user from deleting
        if ($this->AcademicStand->delete($id)) {
            $this->Session->setFlash(
                '<span></span>' . __('Academic stand deleted'),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            __('Academic stand was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }
}

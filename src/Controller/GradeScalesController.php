<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class GradeScalesController extends AppController
{

    public $name = 'GradeScales';

    public $menuOptions = array(
        'parent' => 'gradeSettings',
        'exclude' => array('get_grade_scale'),
        'alias' => array(
            'add' => 'Set Grade Scale',
            'index' => 'View Grade Scales',
            'view' => 'View Grade Scale Details'
        )

    );

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow('get_grade_scale');
    }

    public function index()
    {

        $this->paginate = array(
            'contain' => array(
                'Program',
                'GradeScaleDetail' => array('Grade' => array('GradeType'))
            ),
            'limit' => 100,
            'maxLimit' => 100,
            'order' => array(
                'GradeScale.program_id',
                'GradeScale.grade_type_id',
                'GradeScale.active' => 'DESC',
                'GradeScale.created' => 'DESC'
            ),
            'recursive' => -1
        );

        if ($this->role_id == ROLE_COLLEGE) {
            $conditions = array('GradeScale.model' => 'College', 'GradeScale.foreign_key' => $this->college_id);
        } else {
            if ($this->role_id == ROLE_REGISTRAR) {
                $conditions = array('GradeScale.model' => 'College');
            } else {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $conditions = array(
                        'GradeScale.model' => 'Department',
                        'GradeScale.foreign_key' => $this->department_id
                    );
                }
            }
        }

        $this->set('gradeScales', $this->paginate($conditions));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid grade scale');
            return $this->redirect(array('action' => 'index'));
        }

        $conditions = array();
        $conditions['GradeScale.id'] = $id;

        if ($this->role_id == ROLE_COLLEGE) {
            $conditions['model'] = 'College';
            $conditions['foreign_key'] = $this->college_id;
            $gradeScale = $this->GradeScale->find(
                'first',
                array(
                    'conditions' => $conditions,
                    'contain' => array(
                        'Department',
                        'College',
                        'Program' => array('fields' => array('id', 'name')),
                        'GradeScaleDetail' => array('Grade', 'order' => 'GradeScaleDetail.maximum_result DESC')
                    ),
                    'recursive' => -1
                )
            );
        } elseif ($this->role_id == ROLE_REGISTRAR) {
            $conditions['model'] = 'College';
            $gradeScale = $this->GradeScale->find(
                'first',
                array(
                    'conditions' => $conditions,
                    'contain' => array(
                        'Program' => array('fields' => array('id', 'name')),
                        'GradeScaleDetail' => array('Grade', 'order' => 'GradeScaleDetail.maximum_result DESC')
                    ),
                    'recursive' => -1
                )
            );
        } elseif ($this->role_id == ROLE_DEPARTMENT) {
            $conditions['model'] = 'Department';
            $conditions['foreign_key'] = $this->department_id;
            $gradeScale = $this->GradeScale->find(
                'first',
                array(
                    'conditions' => $conditions,
                    'contain' => array(
                        'Program' => array('fields' => array('id', 'name')),
                        'GradeScaleDetail' => array('Grade', 'order' => 'GradeScaleDetail.maximum_result DESC')
                    ),
                    'recursive' => -1
                )
            );
        } else {
            $gradeScale = $this->GradeScale->find(
                'first',
                array(
                    'conditions' => $conditions,
                    'contain' => array(
                        'Program' => array('fields' => array('id', 'name')),
                        'GradeScaleDetail' => array('Grade', 'order' => 'GradeScaleDetail.maximum_result DESC')
                    ),
                    'recursive' => -1
                )
            );
        }

        $gradeType = $this->GradeScale->GradeScaleDetail->Grade->GradeType->find(
            'first',
            array(
                'fields' => array('id', 'type'),
                'conditions' => array('GradeType.id' => $gradeScale['GradeScale']['grade_type_id']),
                'contain' => array('Grade'),
                'recursive' => -1
            )
        );

        if (!empty($gradeScale['GradeScale']['model']) && $gradeScale['GradeScale']['model'] == 'College') {
            $college = ClassRegistry::init('College')->find(
                'first',
                array(
                    'conditions' => array('College.id' => $gradeScale['GradeScale']['foreign_key']),
                    'recursive' => -1
                )
            );
        } else {
            if (!empty($gradeScale['GradeScale']['model']) && $gradeScale['GradeScale']['model'] == 'Department') {
                $department = ClassRegistry::init('Department')->find(
                    'first',
                    array(
                        'conditions' => array('Department.id' => $gradeScale['GradeScale']['foreign_key']),
                        'recursive' => -1
                    )
                );
            }
        }

        $this->set(compact('department', 'gradeType', 'college', 'gradeScale'));
    }

    //was used during college delegation time
    public function _add()
    {

        if ($this->role_id == ROLE_DEPARTMENT) {
            $check_undegraduate_delegated = $this->GradeScale->PublishedCourse->College->find('count', array(
                'conditions' => array(
                    'College.id' => $this->college_id,
                    'College.deligate_scale' => 1
                )
            ));

            $check_postgraduate_delegated = $this->GradeScale->PublishedCourse->College->find('count', array(
                'conditions' => array(
                    'College.id' => $this->college_id,
                    'College.deligate_for_graduate_study' => 1
                )
            ));

            if ($check_undegraduate_delegated == 0 && $check_postgraduate_delegated == 0) {
                $this->Flash->info(
                    'The registrar is responsible for setting grade scale to your college. You can ask registrar to delegate the scale setting to department'
                );
                return $this->redirect(array('action' => 'index'));
            } else {
                if ($check_postgraduate_delegated > 0) {
                    /* if ($check_undegraduate_delegated == 0 ) {
                        $this->Flash->info('The college you belong is responsible for scale setting, you are not allowed to set scale.');
                        $this->redirect(array('action' => 'index'));
                       } */
                } else {
                    if ($check_undegraduate_delegated == 0) {
                        $this->Flash->info(
                            'The registrar is responsible for setting grade scale to your college. You can ask registrar to delegate the scale setting to department'
                        );
                        return $this->redirect(array('action' => 'index'));
                    }
                }
            }

            $find_delegation_program_ids = $this->GradeScale->PublishedCourse->College->find('first', array(
                'conditions' => array(
                    'College.id' => $this->college_id
                ),
                'fields' => array('deligate_for_graduate_study', 'deligate_scale')
            ));

            if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 1 && $find_delegation_program_ids['College']['deligate_scale'] == 1) {
                //$this->Flash->info('You have delegated scale setting to departments.');
                //$this->redirect(array('action' => 'index'));
                $programs = $this->GradeScale->Program->find('list');
            } else {
                if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 1) {
                    $programs = $this->GradeScale->Program->find(
                        'list',
                        array('conditions' => array('Program.id' => PROGRAM_POST_GRADUATE))
                    );
                } else {
                    if ($find_delegation_program_ids['College']['deligate_scale'] == 1) {
                        $programs = $this->GradeScale->Program->find(
                            'list',
                            array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE))
                        );
                    }
                }
            }
        }

        if ($this->role_id == ROLE_COLLEGE) {
            $find_delegation_program_ids = $this->GradeScale->PublishedCourse->College->find('first', array(
                'conditions' => array('College.id' => $this->college_id),
                'fields' => array('deligate_for_graduate_study', 'deligate_scale')
            ));

            if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 0 && $find_delegation_program_ids['College']['deligate_scale'] == 0) {
                $programs = $this->GradeScale->Program->find('list');
            } else {
                if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 0) {
                    $programs = $this->GradeScale->Program->find(
                        'list',
                        array('conditions' => array('Program.id' => PROGRAM_POST_GRADUATE))
                    );
                } else {
                    if ($find_delegation_program_ids['College']['deligate_scale'] == 0) {
                        $programs = $this->GradeScale->Program->find(
                            'list',
                            array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE))
                        );
                    } else {
                        if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 1 && $find_delegation_program_ids['College']['deligate_scale'] == 1) {
                            $this->Flash->info(
                                'You have delegated scale setting to departments for both undergraduate and post graduate program but you can define scale for non department assigned or pre/freshman program.'
                            );
                            $programs = $this->GradeScale->Program->find(
                                'list',
                                array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE))
                            );
                            //$this->redirect(array('action' => 'index'));
                            $onlyfresh = true;
                            $this->set(compact('onlyfresh'));
                        }
                    }
                }
            }
        }

        if (!empty($this->request->data)) {
            $this->GradeScale->create();

            if ($this->role_id == ROLE_COLLEGE) {
                $this->request->data['GradeScale']['model'] = "College";
                $this->request->data['GradeScale']['foreign_key'] = $this->college_id;
            } elseif ($this->role_id == ROLE_DEPARTMENT) {
                $this->request->data['GradeScale']['model'] = "Department";
                $this->request->data['GradeScale']['foreign_key'] = $this->department_id;
            }

            if ($this->role_id == ROLE_COLLEGE) {
                $check_scale_execlusiveness = $this->GradeScale->GradeScaleDetail->check_scale_execlusiveness(
                    $this->request->data,
                    $this->role_id
                );
            } elseif ($this->role_id == ROLE_DEPARTMENT) {
                $check_scale_execlusiveness = $this->GradeScale->GradeScaleDetail->check_scale_execlusiveness(
                    $this->request->data,
                    $this->role_id
                );
            }

            if (true) {
                $this->request->data = $this->GradeScale->unset_empty_rows($this->request->data);

                if (isset($this->request->data['GradeScale']['own'])) {
                    $own = $this->request->data['GradeScale']['own'];
                } else {
                    $own = 0;
                }

                $scale_ids = $this->GradeScale->GradeScaleDetail->Grade->GradeType->getGradeScaleDetails(
                    $this->request->data['GradeScale']['grade_type_id'],
                    $this->request->data['GradeScale']['program_id'],
                    $this->request->data['GradeScale']['foreign_key'],
                    1,
                    $own
                );

                if (count($scale_ids['GradeScale']) == 0) {
                    //check grade uniqueness
                    if ($this->GradeScale->GradeScaleDetail->checkGradeIsUnique($this->request->data)) {
                        //check continuty
                        $continues = $this->GradeScale->GradeScaleDetail->gradeRangeContinuty($this->request->data);
                        if ($continues) {
                            if ($this->GradeScale->saveAll($this->request->data, array('validate' => 'first'))) {
                                $this->Flash->success('The grade scale has been saved');
                                $this->redirect(array('action' => 'index'));
                            } else {
                                $this->Flash->error('The grade scale could not be saved. Please, try again.');
                            }
                        } else {
                            $error = $this->GradeScale->GradeScaleDetail->invalidFields();
                            if (isset($error['grade_range_continuty'])) {
                                $this->Flash->error($error['grade_range_continuty'][0]);
                            }
                        }
                    } else {
                        $error = $this->GradeScale->GradeScaleDetail->invalidFields();
                        if (isset($error['checkGradeIsUnique'])) {
                            $this->Flash->error($error['checkGradeIsUnique'][0]);
                        }
                    }
                } else {
                    $grade_type = $this->GradeScale->GradeScaleDetail->Grade->GradeType->field('type', array(
                        'GradeType.id' => $this->request->data['GradeScale']['grade_type_id']
                    ));

                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'You have already setup grade scale for  ' . $grade_type . ' grade type. Inorder to define a new scale with same grade type first deactivate the previous scale defined in this page.'
                        ),
                        "session_flash_link",
                        array(
                            'class' => 'error-box error-message',
                            'link_text' => "this page",
                            'link_url' => array(
                                "controller" => "grade_scales",
                                "action" => "index",
                                "admin" => false
                            )
                        )
                    );
                }
            } else {
                $error = $this->GradeScale->GradeScaleDetail->invalidFields();
                if (isset($error['minimum_maximum_result'][0])) {
                    $this->Flash->error($error['minimum_maximum_result']);
                }
            }
        }

        $gradeTypes = $this->GradeScale->GradeScaleDetail->Grade->GradeType->find(
            'list',
            array('fields' => array('id', 'type'))
        );

        if (empty($this->request->data)) {
            $temp = array_keys($gradeTypes);
            $gradeTypeId = $temp[0];
        } else {
            $gradeTypeId = $this->request->data['GradeScale']['grade_type_id'];
        }

        $grades = $this->GradeScale->GradeScaleDetail->Grade->find(
            'list',
            array('conditions' => array('Grade.grade_type_id' => $gradeTypeId), 'fields' => array('id', 'grade'))
        );

        $this->paginate = array(
            'contain' => array(
                'Program',
                'GradeScaleDetail' => array(
                    'Grade' => array(
                        'GradeType' => array('id', 'type'),
                        'fields' => array('id', 'grade')
                    )
                )
            )
        );

        if ($this->role_id == ROLE_COLLEGE) {
            $conditions = array(
                'GradeScale.model' => 'College',
                'GradeScale.active' => 1,
                'GradeScale.foreign_key' => $this->college_id
            );
        } else {
            if ($this->role_id == ROLE_DEPARTMENT) {
                $conditions = array(
                    'GradeScale.model' => 'Department',
                    'GradeScale.active' => 1,
                    'GradeScale.foreign_key' => $this->department_id
                );
            }
        }

        $gradeScales = $this->paginate($conditions);

        //check if department is allowed to set scale.

        $this->set(compact('gradeTypes', 'grades', 'programs', 'gradeScales'));
    }

    public function set_grade_scale()
    {

        if (!empty($this->request->data) && isset($this->request->data['saveIt'])) {
            $this->GradeScale->create();
            if ($this->role_id == ROLE_REGISTRAR) {
                $this->request->data['GradeScale']['model'] = "College";
                $this->request->data['GradeScale']['foreign_key'] = $this->request->data['Search']['college_id'];
            } elseif ($this->role_id == ROLE_DEPARTMENT) {
                $this->request->data['GradeScale']['model'] = "Department";
                $this->request->data['GradeScale']['foreign_key'] = $this->department_id;
            }

            if (true) {
                $this->request->data = $this->GradeScale->unset_empty_rows($this->request->data);

                if (isset($this->request->data['GradeScale']['own'])) {
                    $own = $this->request->data['GradeScale']['own'];
                } else {
                    $own = 0;
                }

                $scale_ids = $this->GradeScale->GradeScaleDetail->Grade->GradeType->getGradeScaleDetails(
                    $this->request->data['GradeScale']['grade_type_id'],
                    $this->request->data['GradeScale']['program_id'],
                    $this->request->data['GradeScale']['foreign_key'],
                    1,
                    $own
                );

                if (count($scale_ids['GradeScale']) == 0) {
                    //check grade uniqueness
                    if ($this->GradeScale->GradeScaleDetail->checkGradeIsUnique($this->request->data)) {
                        //check continuty
                        $continues = $this->GradeScale->GradeScaleDetail->gradeRangeContinuty($this->request->data);
                        if ($continues) {
                            if ($this->GradeScale->saveAll($this->request->data, array('validate' => 'first'))) {
                                $this->Flash->success('The grade scale has been saved');
                                $this->redirect(array('action' => 'index'));
                            } else {
                                $this->Flash->error('The grade scale could not be saved. Please, try again.');
                            }
                        } else {
                            $error = $this->GradeScale->GradeScaleDetail->invalidFields();
                            if (isset($error['grade_range_continuty'])) {
                                $this->Flash->error($error['grade_range_continuty'][0]);
                            }
                        }
                    } else {
                        $error = $this->GradeScale->GradeScaleDetail->invalidFields();
                        if (isset($error['checkGradeIsUnique'])) {
                            $this->Flash->error($error['checkGradeIsUnique'][0]);
                        }
                    }
                } else {
                    $grade_type = $this->GradeScale->GradeScaleDetail->Grade->GradeType->field(
                        'type',
                        array('GradeType.id' => $this->request->data['GradeScale']['grade_type_id'])
                    );
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'You have already setup grade scale for  ' . $grade_type . ' grade type. Inorder to define a new scale with same grade type first deactivate the previous scale defined in this page.'
                        ),
                        "session_flash_link",
                        array(
                            'class' => 'error-box error-message',
                            'link_text' => "this page",
                            'link_url' => array(
                                "controller" => "grade_scales",
                                "action" => "index",
                                "admin" => false
                            )
                        )
                    );
                }
            } else {
                $error = $this->GradeScale->GradeScaleDetail->invalidFields();
                if (isset($error['minimum_maximum_result'])) {
                    $this->Flash->error($error['minimum_maximum_result'][0]);
                }
            }
        }

        if ($this->role_id == ROLE_DEPARTMENT) {
            $check_undegraduate_delegated = $this->GradeScale->PublishedCourse->College->find('count', array(
                'conditions' => array(
                    'College.id' => $this->college_id,
                    'College.deligate_scale' => 1
                )
            ));

            $check_postgraduate_delegated = $this->GradeScale->PublishedCourse->College->find('count', array(
                'conditions' => array(
                    'College.id' => $this->college_id,
                    'College.deligate_for_graduate_study' => 1
                )
            ));

            if ($check_undegraduate_delegated == 0 && $check_postgraduate_delegated == 0) {
                $this->Flash->info(
                    'The registrar responsible for scale setting, you are not allowed to set scale. Ask registrar to delegate your department for scale settings.'
                );
                return $this->redirect(array('action' => 'index'));
            } elseif ($check_postgraduate_delegated > 0) {
            } elseif ($check_undegraduate_delegated == 0) {
                $this->Flash->info(
                    'The registrar  is responsible for scale setting, you are not allowed to set scale.'
                );
                return $this->redirect(array('action' => 'index'));
            }

            $find_delegation_program_ids = $this->GradeScale->PublishedCourse->College->find('first', array(
                'conditions' => array(
                    'College.id' => $this->college_id
                ),
                'fields' => array('deligate_for_graduate_study', 'deligate_scale')
            ));

            if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 1 && $find_delegation_program_ids['College']['deligate_scale'] == 1) {
                $programs = $this->GradeScale->Program->find('list');
            } elseif ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 1) {
                $programs = $this->GradeScale->Program->find(
                    'list',
                    array('conditions' => array('Program.id' => PROGRAM_POST_GRADUATE))
                );
            } elseif ($find_delegation_program_ids['College']['deligate_scale'] == 1) {
                $programs = $this->GradeScale->Program->find(
                    'list',
                    array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE))
                );
            }

            $turn_off_search = true;
            $this->set(compact('turn_off_search'));
        }

        if ($this->role_id == ROLE_REGISTRAR) {
            if (isset($this->request->data['Search']['college_id']) && !empty($this->request->data['Search']['college_id'])) {
                $this->request->data['continue'] = true;
            }

            if (!empty($this->request->data) && isset($this->request->data['continue'])) {
                if (!empty($this->request->data['Search']['college_id'])) {
                    $find_delegation_program_ids = $this->GradeScale->PublishedCourse->College->find('first', array(
                        'conditions' => array(
                            'College.id' => $this->request->data['Search']['college_id']
                        ),
                        'contain' => array(),
                        'fields' => array('deligate_for_graduate_study', 'deligate_scale')
                    ));

                    if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 0 && $find_delegation_program_ids['College']['deligate_scale'] == 0) {
                        $programs = $this->GradeScale->Program->find('list');
                    } else {
                        if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 0) {
                            $programs = $this->GradeScale->Program->find(
                                'list',
                                array('conditions' => array('Program.id' => PROGRAM_POST_GRADUATE))
                            );
                        } else {
                            if ($find_delegation_program_ids['College']['deligate_scale'] == 0) {
                                $programs = $this->GradeScale->Program->find(
                                    'list',
                                    array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE))
                                );
                            } else {
                                if ($find_delegation_program_ids['College']['deligate_for_graduate_study'] == 1 && $find_delegation_program_ids['College']['deligate_scale'] == 1) {
                                    //$this->Flash->info('You have delegated scale setting to departments for both undergraduate and post graduate program but you can define scale for non department assigned or pre/freshman program.');
                                    $programs = $this->GradeScale->Program->find(
                                        'list',
                                        array('conditions' => array('Program.id' => PROGRAM_UNDEGRADUATE))
                                    );
                                    //$this->redirect(array('action' => 'index'));
                                    $onlyfresh = true;
                                    $this->set(compact('onlyfresh'));
                                }
                            }
                        }
                    }

                    // For Remedial Update

                    if ($this->request->data['Search']['college_id'] == 14 || $this->request->data['Search']['college_id'] == 15) {
                        //$programs = $this->GradeScale->Program->find('list', array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1)));
                        $programs = $this->GradeScale->Program->find(
                            'list',
                            array('conditions' => array('Program.id' => PROGRAM_REMEDIAL))
                        );
                    }


                    $college_id = $this->request->data['Search']['college_id'];
                    $turn_off_search = true;
                    $this->set(compact('turn_off_search', 'college_id'));
                }
            }
        }

        $gradeTypes = $this->GradeScale->GradeScaleDetail->Grade->GradeType->find(
            'list',
            array('fields' => array('id', 'type'))
        );

        if (empty($this->request->data)) {
            $temp = array_keys($gradeTypes);
            $gradeTypeId = $temp[0];
        } else {
            if (!empty($this->request->data['GradeScale']['grade_type_id'])) {
                $gradeTypeId = $this->request->data['GradeScale']['grade_type_id'];
            } else {
                $temp = array_keys($gradeTypes);
                $gradeTypeId = $temp[0];
            }
        }

        $grades = $this->GradeScale->GradeScaleDetail->Grade->find(
            'list',
            array('conditions' => array('Grade.grade_type_id' => $gradeTypeId), 'fields' => array('id', 'grade'))
        );
        $this->paginate = array(
            'contain' => array(
                'Program',
                'GradeScaleDetail' => array(
                    'Grade' => array(
                        'GradeType' => array('id', 'type'),
                        'fields' => array('id', 'grade')
                    )
                )
            )
        );

        if ($this->role_id == ROLE_REGISTRAR) {
            $conditions = array('GradeScale.model' => 'College');
            $gradeScales = $this->paginate($conditions);
        } else {
            if ($this->role_id == ROLE_DEPARTMENT) {
                $conditions = array(
                    'GradeScale.model' => 'Department',
                    'GradeScale.active' => 1,
                    'GradeScale.foreign_key' => $this->department_id
                );
                $gradeScales = $this->paginate($conditions);
            }
        }

        //check if department is allowed to set scale.
        $colleges = ClassRegistry::init('College')->find('list');
        $this->set(compact('gradeTypes', 'grades', 'colleges', 'programs', 'gradeScales'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Flash->error('Invalid grade scale');
            return $this->redirect(array('action' => 'index'));
        }

        $this->GradeScale->id = $id;

        if (!$this->GradeScale->exists()) {
            $this->Flash->error('Invalid grade scale');
            return $this->redirect(array('action' => 'index'));
        }

        //check if grade is submitted in the given scale id
        $is_grade_editing_possible = $this->GradeScale->check_grade_submitted($id);

        if ($is_grade_editing_possible) {
            $this->Flash->error(
                'You can not edit this scale. Student has already get a grade with this scale. Please deactivate this grade scale and define a new grade scale if you want grade scale change.'
            );
            return $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data)) {
            //check grade uniqueness
            if ($this->GradeScale->GradeScaleDetail->checkGradeIsUnique($this->request->data)) {
                //check continuty
                $continues = $this->GradeScale->GradeScaleDetail->gradeRangeContinuty($this->request->data);
                if ($continues) {
                    if ($this->GradeScale->saveAll($this->request->data, array('validate' => 'first'))) {
                        $this->Flash->success('The grade scale has been saved');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Flash->error('The grade scale could not be saved. Please, try again.');
                    }
                } else {
                    $error = $this->GradeScale->GradeScaleDetail->invalidFields();
                    if (isset($error['grade_range_continuty'])) {
                        $this->Flash->error($error['grade_range_continuty'][0]);
                    }
                }
            } else {
                $error = $this->GradeScale->GradeScaleDetail->invalidFields();
                if (isset($error['checkGradeIsUnique'])) {
                    $this->Flash->error($error['checkGradeIsUnique'][0]);
                }
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->GradeScale->read(null, $id);
        }

        $gradeTypes = $this->GradeScale->GradeScaleDetail->Grade->GradeType->find(
            'list',
            array('fields' => array('id', 'type'))
        );

        if (empty($this->request->data)) {
            $temp = array_keys($gradeTypes);
            $gradeTypeId = $temp[0];
        } else {
            if (!empty($this->request->data['GradeScale']['grade_type_id'])) {
                $gradeTypeId = $this->request->data['GradeScale']['grade_type_id'];
            } else {
                $temp = array_keys($gradeTypes);
                $gradeTypeId = $temp[0];
            }
        }

        $grades = $this->GradeScale->GradeScaleDetail->Grade->find(
            'list',
            array('conditions' => array('Grade.grade_type_id' => $gradeTypeId), 'fields' => array('id', 'grade'))
        );

        $this->paginate = array('contain' => array('Program'));
        $conditions = array('GradeScale.active' => 1);
        $gradeScales = $this->paginate($conditions);

        $programs = $this->GradeScale->Program->find('list');
        $this->set(compact('gradeTypes', 'grades', 'programs', 'gradeScales'));
    }

    public function delete($id = null, $action_controller_id = null)
    {

        if (!empty($action_controller_id)) {
            $grade_type = explode('~', $action_controller_id);
        }

        $this->GradeScale->id = $id;

        if (!$this->GradeScale->exists()) {
            $this->Flash->error('Invalid id for grade scale');
            if (!empty($grade_type[0]) && !empty($grade_type[1]) && !empty($grade_type[2])) {
                $this->redirect(array('controller' => $grade_type[1], 'action' => $grade_type[0], $grade_type[2]));
            } elseif (!empty($grade_type[0]) && !empty($grade_type[1])) {
                $this->redirect(array('controller' => $grade_type[1], 'action' => $grade_type[0]));
            }
            return $this->redirect(array('action' => 'index'));
        }

        //TODO: CHeck grade scale is not involved for any grade computing
        // it true, call function in here to return true or false to allow deletion.

        $check_not_involved_in_grade_computing = $this->GradeScale->allowDelete($id);

        if ($check_not_involved_in_grade_computing) {
            if ($this->GradeScale->delete($id)) {
                $this->Flash->success('Grade scale deleted.');
                if (!empty($grade_type[0]) && !empty($grade_type[1]) && !empty($grade_type[2])) {
                    $this->redirect(array('controller' => $grade_type[1], 'action' => $grade_type[0], $grade_type[2]));
                } elseif (!empty($grade_type[0]) && !empty($grade_type[1])) {
                    $this->redirect(array('controller' => $grade_type[1], 'action' => $grade_type[0]));
                }
                $this->redirect(array('action' => 'index'));
            }
        }

        $this->Flash->error('Grade scale was not deleted');
        return $this->redirect(array('action' => 'index'));
    }

    public function deactive_scale()
    {

        if (!empty($this->request->data) && isset($this->request->data['deactivateselected'])) {
            $selected_atleast_one = array_sum($this->request->data['GradeScale']['selected']);

            if ($selected_atleast_one > 0) {
                // reformat for deactivation
                $data = array();
                $count = 0;

                foreach ($this->request->data['GradeScale']['selected'] as $grade_scale_id => $selected) {
                    if ($selected == 1) {
                        $data['GradeScale'][$count]['active'] = 0;
                        $data['GradeScale'][$count]['id'] = $grade_scale_id;
                    }
                }

                if (!empty($data)) {
                    if ($this->GradeScale->saveAll($data['GradeScale'])) {
                        $this->Flash->success('The grade scale has deactivated successfully.');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Flash->error('The grade scale could not be deactived. Please, try again.');
                    }
                }
            } else {
                $this->Flash->error('Select atleast one grade scale you want to deactivate.');
            }
        }

        $this->GradeScale->recursive = 0;
        $this->paginate = array(
            'limit' => 1000,
            'contain' => array('Program', 'GradeScaleDetail' => array('Grade' => array('GradeType')))
        );

        if ($this->role_id == ROLE_COLLEGE) {
            $conditions = array(
                'model' => 'College',
                'GradeScale.active' => 1,
                'GradeScale.foreign_key' => $this->college_id
            );
        } else {
            if ($this->role_id == ROLE_DEPARTMENT) {
                $conditions = array(
                    'model' => 'Department',
                    'GradeScale.active' => 1,
                    'GradeScale.foreign_key' => $this->department_id
                );
            } else {
                if ($this->role_id == ROLE_REGISTRAR) {
                    $conditions = array('model' => 'College');
                }
            }
        }

        $gradeScales = $this->paginate($conditions);

        if (empty($gradeScales)) {
            $this->Flash->error('There is no scale that needs deactivation.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->set('gradeScales', $this->paginate($conditions));
        }

        $this->set('gradeScales', $this->paginate($conditions));
    }

    public function activate_scale()
    {

        if (!empty($this->request->data) && isset($this->request->data['activatescale'])) {
            $selected_atleast_one = array_sum($this->request->data['GradeScale']['selected']);

            if ($selected_atleast_one > 0) {
                // reformat for deactivation
                $data = array();
                $count = 0;

                foreach ($this->request->data['GradeScale']['selected'] as $grade_scale_id => $selected) {
                    if ($selected == 1) {
                        $data['GradeScale'][$count]['active'] = 1;
                        $data['GradeScale'][$count]['id'] = $grade_scale_id;
                    }
                }

                if (!empty($data)) {
                    if ($this->GradeScale->saveAll($data['GradeScale'])) {
                        $this->Flash->success('The grade scale has activated successfully.');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Flash->error('The grade scale could not be activated. Please, try again.');
                    }
                }
            } else {
                $this->Flash->error('Select atleast one grade scale you want to activate.');
            }
        }

        $this->GradeScale->recursive = 0;
        $this->paginate = array(
            'contain' => array(
                'Program',
                'GradeScaleDetail' => array('Grade' => array('GradeType'))
            )
        );

        if ($this->role_id == ROLE_COLLEGE) {
            $conditions = array(
                'model' => 'College',
                'GradeScale.active' => 0,
                'GradeScale.foreign_key' => $this->college_id
            );
        } else {
            if ($this->role_id == ROLE_REGISTRAR) {
                $conditions = array('model' => 'College', 'GradeScale.active' => 0);
            } else {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $conditions = array(
                        'model' => 'Department',
                        'GradeScale.active' => 0,
                        'GradeScale.foreign_key' => $this->department_id
                    );
                }
            }
        }

        $gradeScales = $this->paginate($conditions);

        if (empty($gradeScales)) {
            $this->Flash->error('There is no scale that  needs activatation.');
            $this->redirect(array('action' => 'index'));
        } else {
            $this->set('gradeScales', $this->paginate($conditions));
        }
    }

    public function get_grade_scale()
    {

        //$this->layout = 'ajax';
        $this->autoRender = false;
        $grade = '';
        //debug($this->request->data);
        if (isset($this->request->data['ExamGradeChange']) && !empty($this->request->data['ExamGradeChange'])) {
            $reg = ClassRegistry::init('CourseRegistration')->find('first', array(
                'conditions' => array(
                    'CourseRegistration.id' => $this->request->data['ExamGradeChange']['course_registration_id']
                ),
                'recursive' => -1
            ));

            $grade_scale = ClassRegistry::init('PublishedCourse')->getGradeScaleDetail(
                $reg['CourseRegistration']['published_course_id']
            );

            foreach ($grade_scale['GradeScaleDetail'] as $k => $v) {
                $encodedResult = floatval($this->request->data['ExamGradeChange']['makeup_exam_result']);
                if ($encodedResult >= $v['minimum_result'] && $encodedResult <= $v['maximum_result']) {
                    $grade = $v['grade'];
                    break;
                }
            }
        }
        return $grade;
    }
}

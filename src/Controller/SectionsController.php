<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;

class SectionsController extends AppController
{

    public $name = 'Sections';


    public $paginate = array();

    public $menuOptions = array(
        'parent' => 'placement',
        'exclude' => array(
            'export',
            'view_pdf',
            'deleteStudentforThisSection',
            'archieveUnarchieveStudentSection',
            'move',
            'section_move_update',
            'mass_student_section_add',
            'add_student_section',
            'add_student_section_update',
            'get_sections_by_program',
            'get_sections_by_dept',
            'get_sections_by_academic_year',
            'get_sections_of_college',
            'get_modal_box',
            'get_section_students',
            'un_assigned_summeries',
            'get_sections_by_program_and_dept',
            'get_year_level',
            'deleteStudent',
            'move_selected_student_section',
            'move_student_section_to_new',
            'add_student_to_section',
            'add_student_prev_section',
            'get_sections_by_dept_data_entry',
            'get_sections_by_year_level',
            'get_sup_students',
            'get_sections_by_program_supp_exam',
            'get_sections_by_program_and_dept_supp_exam',
            'upgrade_selected_student_section',
            'get_sections_by_dept_add_drop',
            'restore_student_section',
            'search'
        ),
        'alias' => array(
            'index' => 'List Sections',
            'add' => 'Add New Section',
            'assign' => 'Assign Students to Section',
            'upgrade_sections' => 'Upgrade Year Level',
            'downgrade_sections' => 'Downgrade Year Level',
            'dispaly_section_less_students' => 'Dispaly Sectionless Students'
        )
    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

        $this->viewBuilder()->setHelpers(['DatePicker', 'Media.Media', 'Csv']);
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow(
            'get_year_level',
            'get_modal_box',
            'get_sections_by_dept',
            'get_section_students',
            'get_sections_of_college',
            'get_sections_by_academic_year',
            'get_sections_by_dept_data_entry',
            'get_sections_by_program',
            'get_sections_by_program_supp_exam',
            'get_sections_by_program_and_dept',
            'get_sections_by_program_and_dept_supp_exam',
            'get_sections_by_year_level',
            'get_sup_students',
            'archieveUnarchieveStudentSection',
            'export',
            'view_pdf',
            'get_sections_by_dept_add_drop',
            'restore_student_section'
        );
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        //$acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 12, date('Y'));

        $current_academicyear = $thisacademicyear = $this->AcademicYear->currentAcademicYear();
        $acyear_array_data = $this->AcademicYear->academicYearInArray(
            APPLICATION_START_YEAR,
            (explode('/', $current_academicyear)[0])
        );

        //debug($acyear_array_data);
        //$this->set('thisacademicyear', $thisacademicyear);

        $programs = $this->Section->Program->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
        );
        $program_types = $programTypes = $this->Section->ProgramType->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
        );

        $yearLevels = $this->year_levels;

        if ($this->role_id == ROLE_DEPARTMENT) {
            $yearLevels = $this->Section->YearLevel->find(
                'list',
                array(
                    'conditions' => array(
                        'YearLevel.department_id' => $this->department_id,
                        'YearLevel.name' => $this->year_levels
                    )
                )
            );
        }

        $this->set(
            compact(
                'acyear_array_data',
                'thisacademicyear',
                'current_academicyear',
                'program_types',
                'programTypes',
                'programs',
                'yearLevels'
            )
        );
    }

    public function __init_search_sections()
    {

        if (!empty($this->request->data['Section'])) {
            $this->Session->write('search_sections', $this->request->data['Section']);
        } else {
            if ($this->Session->check('search_sections')) {
                $this->request->data['Section'] = $this->Session->read('search_sections');
            }
        }
    }

    public function __init_clear_session_filters($data = null)
    {

        if ($this->Session->check('search_sections')) {
            $this->Session->delete('search_sections');
        }
        //return $this->redirect(array('action' => 'index', $data));
    }

    public function search()
    {

        $url['action'] = 'index';

        if (isset($this->request->data) && !empty($this->request->data)) {
            foreach ($this->request->data as $k => $v) {
                if (!empty($v)) {
                    foreach ($v as $kk => $vv) {
                        if (!empty($vv) && is_array($vv)) {
                            foreach ($vv as $kkk => $vvv) {
                                $url[$k . '.' . $kk . '.' . $kkk] = str_replace('/', '-', trim($vvv));
                            }
                        } else {
                            $url[$k . '.' . $kk] = str_replace('/', '-', trim($vv));
                        }
                    }
                }
            }
        }

        return $this->redirect($url, null, true);
    }


    public function index($data = null)
    {

        $limit = 100;
        $name = '';
        $turn_off_search = true;

        $options = array();

        //$page = 1;
        $page = '';

        //$archive = 0;
        /* $sort = 'academicyear';
		$direction = 'DESC'; */

        //$this->__init_search_sections();

        if (isset($this->passedArgs)) {
            if (isset($this->passedArgs['page'])) {
                $page = $this->request->data['Section']['page'] = $this->passedArgs['page'];
            }
            /* if (isset($this->passedArgs['sort'])) {
				$sort = $this->request->data['Section']['sort'] = $this->passedArgs['sort'];
			}
			if (isset($this->passedArgs['direction'])) {
				$direction = $this->request->data['Section']['direction'] = $this->passedArgs['direction'];
			} */

            ////////////////////

            if (isset($this->passedArgs['page'])) {
                $page = $this->request->data['Section']['page'] = $this->passedArgs['page'];
            }

            if (isset($this->passedArgs['sort'])) {
                $this->request->data['Section']['sort'] = $this->passedArgs['sort'];
            }

            if (isset($this->passedArgs['direction'])) {
                $this->request->data['Section']['direction'] = $this->passedArgs['direction'];
            }

            ////////////////////

            $this->__init_search_sections();
        }


        if (isset($data) && !empty($data['Section'])) {
            $this->request->data['Section'] = $data['Section'];
            debug($this->request->data);
            $this->__init_search_sections();
        }

        if (isset($this->request->data['search'])) {
            if (isset($this->passedArgs)) {
                unset($this->passedArgs);
            }
            $this->__init_clear_session_filters();
            $this->__init_search_sections();
        }

        if (!empty($this->request->data['Section']['limit'])) {
            $limit = $this->request->data['Section']['limit'];
        } else {
            $this->request->data['Section']['limit'] = $limit;
        }

        if (!empty($this->request->data['Section']['section_name'])) {
            $name = $this->request->data['Section']['section_name'];
        } else {
            $this->request->data['Section']['section_name'] = $name;
        }

        /* if (!empty($this->request->data['Section']['active'])) {
			$archive = $this->request->data['Section']['active'];
		} else {
			$this->request->data['Section']['active'] = $archive;
		} */

        if (empty($this->request->data['Section']['active'])) {
            $this->request->data['Section']['active'] = '';
        }

        if (isset($this->request->data['Section']['academicyearSection'])) {
            $selected_academic_year = $this->request->data['Section']['academicyearSection'];
        } else {
            if (isset($this->request->data['Section']['academicyear'])) {
                $selected_academic_year = $this->request->data['Section']['academicyear'];
            } else {
                $selected_academic_year = $this->request->data['Section']['academicyearSection'] = $this->request->data['Section']['academicyear'] = $this->AcademicYear->current_academicyear(
                );
            }
        }


        if (/* isset($this->request->data) &&  */ !empty($this->request->data)) {
            //$this->__init_search_sections();
            debug($this->request->data);

            if (!empty($page) && !isset($this->request->data['search'])) {
                $this->request->data['Search']['page'] = $page;
            }

            if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                $departments = $this->Section->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_id, 'Department.active' => 1))
                );
                $options['conditions'][] = array('Section.department_id' => $this->department_id);
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                    $departments = array();

                    if ($this->onlyPre == 0) {
                        $departments = $this->Section->Department->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'Department.college_id' => $this->college_ids,
                                    'Department.active' => 1
                                )
                            )
                        );
                    } else {
                        $colleges = $this->Section->College->find(
                            'list',
                            array('conditions' => array('College.id' => $this->college_ids, 'College.active' => 1))
                        );
                        $this->request->data['Section']['college_id'] = $this->college_id;
                        $this->request->data['Section']['year_level_id'] = '0';
                    }

                    $options['conditions'][] = array('Section.college_id' => $this->college_id);

                    /* if ((isset($this->request->data['Section']['department_id']) && !empty($this->request->data['Section']['department_id']) && $this->request->data['Section']['department_id'] == 0) || (isset($this->request->data['Section']['year_level_id']) && !empty($this->request->data['Section']['year_level_id']) && $this->request->data['Section']['year_level_id'] == 0)) {
					$options['conditions'][] = array('Section.department_id IS NULL');
				} else  */

                    if (isset($this->request->data['Section']['department_id']) && !empty($this->request->data['Section']['department_id']) && $this->request->data['Section']['department_id'] > 0) {
                        $options['conditions'][] = array('Section.department_id' => $this->request->data['Section']['department_id']);
                    }
                } else {
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        if (!empty($this->department_ids)) {
                            $colleges = array();
                            $departments = $this->Section->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.id' => $this->department_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );

                            if (!empty($this->request->data['Section']['department_id'])) {
                                $options['conditions'][] = array('Section.department_id' => $this->request->data['Section']['department_id']);
                            } else {
                                $options['conditions'][] = array('Section.department_id' => $this->department_ids);
                            }
                        } else {
                            if (!empty($this->college_ids)) {
                                $departments = array();
                                $colleges = $this->Section->College->find(
                                    'list',
                                    array(
                                        'conditions' => array(
                                            'College.id' => $this->college_ids,
                                            'College.active' => 1
                                        )
                                    )
                                );

                                if (!empty($this->request->data['Section']['college_id'])) {
                                    $options['conditions'][] = array(
                                        'Section.college_id' => $this->request->data['Section']['college_id'],
                                        'Section.department_id IS NULL'
                                    );
                                } else {
                                    $options['conditions'][] = array(
                                        'Section.college_id' => $this->college_ids,
                                        'Section.department_id IS NULL'
                                    );
                                }

                                $this->request->data['Section']['year_level_id'] = '0';
                            }
                        }
                    } else {
                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
                            // nothing
                        } else {
                            $departments = $this->Section->Department->find(
                                'list',
                                array('conditions' => array('Department.active' => 1))
                            );
                            $colleges = $this->Section->College->find(
                                'list',
                                array('conditions' => array('College.active' => 1))
                            );

                            if (!empty($this->request->data['Section']['department_id'])) {
                                $options['conditions'][] = array('Section.department_id' => $this->request->data['Section']['department_id']);
                            } else {
                                if (empty($this->request->data['Section']['department_id']) && !empty($this->request->data['Section']['college_id'])) {
                                    $departments = $this->Section->Department->find(
                                        'list',
                                        array(
                                            'conditions' => array(
                                                'Department.college_id' => $this->request->data['Section']['college_id'],
                                                'Department.active' => 1
                                            )
                                        )
                                    );
                                    $options['conditions'][] = array('Section.college_id' => $this->request->data['Section']['college_id']);
                                } else {
                                    if (!empty($colleges) && !empty($departments)) {
                                        $options['conditions'][] = array(
                                            'OR' => array(
                                                'Section.college_id' => array_keys($colleges),
                                                'Section.department_id' => array_keys($departments)
                                            )
                                        );
                                    } else {
                                        if (!empty($departments)) {
                                            $options['conditions'][] = array(
                                                'Section.department_id' => array_keys(
                                                    $departments
                                                )
                                            );
                                        } else {
                                            if (!empty($colleges)) {
                                                $options['conditions'][] = array(
                                                    'Section.college_id' => array_keys(
                                                        $colleges
                                                    )
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($selected_academic_year)) {
                //debug($selected_academic_year);
                $options['conditions'][] = array('Section.academicyear' => $selected_academic_year);
            }

            if (!empty($this->request->data['Section']['program_id'])) {
                $options['conditions'][] = array('Section.program_id' => $this->request->data['Section']['program_id']);
            } else {
                if (empty($this->request->data['Section']['program_id'])) {
                    $options['conditions'][] = array('Section.program_id' => $this->program_ids);
                }
            }

            if (!empty($this->request->data['Section']['program_type_id'])) {
                $options['conditions'][] = array('Section.program_type_id' => $this->request->data['Section']['program_type_id']);
            } else {
                if (empty($this->request->data['Section']['program_type_id'])) {
                    $options['conditions'][] = array('Section.program_type_id' => $this->program_type_ids);
                }
            }

            if (isset($this->request->data['Section']['college_id']) && $this->request->data['Section']['college_id'] > 0 && $this->Session->read(
                    'Auth.User'
                )['role_id'] != ROLE_REGISTRAR) {
                $departments = $this->Section->Department->find(
                    'list',
                    array(
                        'conditions' => array(
                            'Department.college_id' => $this->request->data['Section']['college_id'],
                            'Department.active' => 1
                        )
                    )
                );
            }

            if (!($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $this->Session->read(
                    'Auth.User'
                )['is_admin'] == 0)) {
                if (isset($this->request->data['Section']['year_level_id']) && $this->request->data['Section']['year_level_id'] == '0') {
                    $options['conditions'][] = array('Section.department_id IS NULL');
                    debug($this->request->data['Section']['year_level_id']);
                } else {
                    if (isset($this->request->data['Section']['year_level_id']) && is_numeric(
                            $this->request->data['Section']['year_level_id']
                        ) && $this->request->data['Section']['year_level_id'] > 0) {
                        $options['conditions'][] = array('Section.year_level_id' => $this->request->data['Section']['year_level_id']);
                        debug($this->request->data['Section']['year_level_id']);
                    } else {
                        isset($this->request->data['Section']['year_level_id']) ? debug(
                            $this->request->data['Section']['year_level_id']
                        ) : '';

                        if (($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $this->Session->read(
                                    'Auth.User'
                                )['is_admin'] == 0) || ($this->Session->read(
                                    'Auth.User'
                                )['role_id'] == ROLE_REGISTRAR && !empty($this->college_ids) && $this->onlyPre == 1)) {
                            $options['conditions'][] = array('Section.department_id IS NULL');
                        } else {
                            if (!empty($this->request->data['Section']['department_id'])) {
                                $options['conditions'][] = array(
                                    'Section.year_level_id' => $this->Section->YearLevel->find(
                                        'list',
                                        array(
                                            'fields' => array('YearLevel.id'),
                                            'conditions' => array(
                                                'YearLevel.department_id' => $this->request->data['Section']['department_id'],
                                                'YearLevel.name' => (isset($this->request->data['Section']['year_level_id']) && !empty($this->request->data['Section']['year_level_id']) && $this->request->data['Section']['year_level_id'] != 0 ? $this->request->data['Section']['year_level_id'] : $this->year_levels)
                                            )
                                        )
                                    )
                                );
                            } else {
                                if (!empty($departments)) {
                                    if ($this->Session->read(
                                            'Auth.User'
                                        )['role_id'] == ROLE_COLLEGE && empty($this->request->data['Section']['department_id']) && empty($this->request->data['Section']['year_level_id'])) {
                                        $options['conditions'][] = array(
                                            'OR' => array(
                                                'Section.year_level_id IS NULL',
                                                'Section.year_level_id = 0',
                                                'Section.year_level_id = ""',
                                                'Section.year_level_id' => $this->Section->YearLevel->find(
                                                    'list',
                                                    array(
                                                        'fields' => array('YearLevel.id'),
                                                        'conditions' => array(
                                                            'YearLevel.department_id' => array_keys(
                                                                $departments
                                                            ),
                                                            'YearLevel.name' => (isset($this->request->data['Section']['year_level_id']) && !empty($this->request->data['Section']['year_level_id']) && $this->request->data['Section']['year_level_id'] != 0 ? $this->request->data['Section']['year_level_id'] : $this->year_levels)
                                                        )
                                                    )
                                                )
                                            )
                                        );
                                    } else {
                                        $options['conditions'][] = array(
                                            'Section.year_level_id' => $this->Section->YearLevel->find(
                                                'list',
                                                array(
                                                    'fields' => array('YearLevel.id'),
                                                    'conditions' => array(
                                                        'YearLevel.department_id' => array_keys($departments),
                                                        'YearLevel.name' => (isset($this->request->data['Section']['year_level_id']) && !empty($this->request->data['Section']['year_level_id']) && $this->request->data['Section']['year_level_id'] != 0 ? $this->request->data['Section']['year_level_id'] : $this->year_levels)
                                                    )
                                                )
                                            )
                                        );
                                    }
                                } else {
                                    if (isset($this->department_ids) && !empty($this->department_ids)) {
                                        if ($this->Session->read(
                                                'Auth.User'
                                            )['role_id'] == ROLE_COLLEGE && empty($this->request->data['Section']['department_id']) && empty($this->request->data['Section']['year_level_id'])) {
                                            $options['conditions'][] = array(
                                                'OR' => array(
                                                    'Section.year_level_id IS NULL',
                                                    'Section.year_level_id = 0',
                                                    'Section.year_level_id = ""',
                                                    'Section.year_level_id' => $this->Section->YearLevel->find(
                                                        'list',
                                                        array(
                                                            'fields' => array('YearLevel.id'),
                                                            'conditions' => array(
                                                                'YearLevel.department_id' => $this->department_ids,
                                                                'YearLevel.name' => (isset($this->request->data['Section']['year_level_id']) && !empty($this->request->data['Section']['year_level_id']) && $this->request->data['Section']['year_level_id'] != 0 ? $this->request->data['Section']['year_level_id'] : $this->year_levels)
                                                            )
                                                        )
                                                    )
                                                )
                                            );
                                        } else {
                                            $options['conditions'][] = array(
                                                'Section.year_level_id' => $this->Section->YearLevel->find(
                                                    'list',
                                                    array(
                                                        'fields' => array('YearLevel.id'),
                                                        'conditions' => array(
                                                            'YearLevel.department_id' => $this->department_ids,
                                                            'YearLevel.name' => (isset($this->request->data['Section']['year_level_id']) && !empty($this->request->data['Section']['year_level_id']) && $this->request->data['Section']['year_level_id'] != 0 ? $this->request->data['Section']['year_level_id'] : $this->year_levels)
                                                        )
                                                    )
                                                )
                                            );
                                        }
                                    } else {
                                        if ($this->Session->read(
                                                'Auth.User'
                                            )['role_id'] == ROLE_COLLEGE || $this->onlyPre || $this->college_ids) {
                                            $options['conditions'][] = array('Section.department_id IS NULL');
                                        } else {
                                            $options['conditions'][] = array('Section.department_id = ""');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $options['conditions'][] = array('Section.department_id IS NULL');
            }

            /* if (!empty($this->request->data['Section']['year_level_id'])) {
				if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
					$options['conditions'][] = array('Section.year_level_id' => $this->request->data['Section']['year_level_id']);
				} else {
					if (empty($this->request->data['Section']['department_id']) && $this->request->data['Section']['year_level_id'] == 0) {
						$options['conditions'][] = array('Section.department_id IS NULL');
					} else if ((isset($this->request->data['Section']['department_id']) && $this->request->data['Section']['department_id'] == 0) || $this->request->data['Section']['year_level_id'] == 0) {
						$options['conditions'][] = array('Section.department_id IS NULL');
					} else if (isset($this->department_ids) && !empty($this->department_ids)) {
						$options['conditions'][] = array('Section.year_level_id' => $this->Section->YearLevel->find('list', array('fields' => array('YearLevel.id'), 'conditions' => array('YearLevel.department_id' => $this->department_ids, 'YearLevel.name' => (isset($this->request->data['Section']['year_level_id']) && !empty($this->request->data['Section']['year_level_id']) && $this->request->data['Section']['year_level_id'] != 0 ? $this->request->data['Section']['year_level_id'] : $this->year_levels)))));
					}
				}
			} else if ($this->request->data['Section']['year_level_id'] == 0) {
				$options['conditions'][] = array('Section.department_id IS NULL');
			} else {
				if (!empty($this->request->data['Section']['department_id']) && $this->request->data['Section']['department_id'] == 0) {
					$options['conditions'][] = array('Section.department_id IS NULL');
				} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
					$options['conditions'][] = array('Section.year_level_id' => $this->Section->YearLevel->find('list', array('fields' => array('YearLevel.id'), 'conditions' => array('YearLevel.department_id' => $this->department_ids, 'YearLevel.name' => (isset($this->request->data['Section']['year_level_id']) && !empty($this->request->data['Section']['year_level_id']) && $this->request->data['Section']['year_level_id'] != 0 ? $this->request->data['Section']['year_level_id'] : $this->year_levels)))));
				} else {
					if (isset($this->department_ids) && !empty($this->department_ids) &&  $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $this->Session->read('Auth.User')['is_admin'] == 1) {
						$options['conditions'][] = array(
							'OR' => array(
								'Section.department_id IS NULL',
								'Section.year_level_id' => $this->Section->YearLevel->find('list', array('fields' => array('YearLevel.id'), 'conditions' => array('YearLevel.department_id' => $this->department_ids, 'YearLevel.name' =>  $this->year_levels))),
							)
						);
					} else if (($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $this->Session->read('Auth.User')['is_admin'] == 0) || ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && !empty($this->college_ids) && $this->onlyPre == 1)) {
						$options['conditions'][] = array('Section.department_id IS NULL');
					} else if (isset($this->department_ids) && !empty($this->department_ids)) {
						$options['conditions'][] = array('Section.year_level_id' => $this->Section->YearLevel->find('list', array('fields' => array('YearLevel.id'), 'conditions' => array('YearLevel.department_id' => $this->department_ids, 'YearLevel.name' => (isset($this->request->data['Section']['year_level_id']) && !empty($this->request->data['Section']['year_level_id']) && $this->request->data['Section']['year_level_id'] != 0 ? $this->request->data['Section']['year_level_id'] : $this->year_levels)))));
					} else {
						$options['conditions'][] = array('Section.department_id IS NULL');
					}
				}
			} */

            if (isset($name) && !empty($name)) {
                //unset($options);
                $options['conditions'][] = array('Section.name LIKE ' => '%' . $name . '%');
            }

            if (is_numeric($this->request->data['Section']['active'])) {
                $options['conditions'][] = array('Section.archive' => $this->request->data['Section']['active']);
            }
        } else {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                $departments = array();

                if ($this->onlyPre != 1) {
                    $departments = $this->Section->Department->find(
                        'list',
                        array(
                            'conditions' => array(
                                'Department.college_id' => $this->college_id,
                                'Department.active' => 1
                            )
                        )
                    );
                } else {
                    $colleges = $this->Section->College->find(
                        'list',
                        array('conditions' => array('College.id' => $this->college_id, 'College.active' => 1))
                    );
                    $this->request->data['Section']['college_id'] = $this->college_id;
                    $this->request->data['Section']['year_level_id'] = '0';
                }

                $options['conditions'][] = array('Section.college_id' => $this->college_id);
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                    $departments = $this->Section->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_id))
                    );
                    $options['conditions'][] = array('Section.department_id' => $this->department_id);
                } else {
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        if (!empty($this->department_ids)) {
                            $colleges = array();
                            $departments = $this->Section->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.id' => $this->department_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );

                            if (!empty($departments)) {
                                $options['conditions'][] = array(
                                    'Section.department_id' => $this->department_ids,
                                    'Section.program_id' => $this->program_ids,
                                    'Section.program_type_id' => $this->program_type_ids
                                );
                            }
                        } else {
                            if (!empty($this->college_ids)) {
                                $departments = array();
                                $colleges = $this->Section->College->find(
                                    'list',
                                    array(
                                        'conditions' => array(
                                            'College.id' => $this->college_ids,
                                            'College.active' => 1
                                        )
                                    )
                                );

                                if (!empty($colleges)) {
                                    $options['conditions'][] = array(
                                        'Section.college_id' => $this->college_ids,
                                        'Section.program_id' => $this->program_ids,
                                        'Section.program_type_id' => $this->program_type_ids
                                    );
                                }

                                $this->request->data['Section']['year_level_id'] = '0';
                            }
                        }
                    } else {
                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
                            // nothing
                        } else {
                            $departments = $this->Section->Department->find(
                                'list',
                                array('conditions' => array('Department.active' => 1))
                            );
                            $colleges = $this->Section->College->find(
                                'list',
                                array('conditions' => array('College.active' => 1))
                            );

                            if (!empty($colleges) && !empty($departments)) {
                                $options['conditions'][] = array(
                                    'OR' => array(
                                        'Section.department_id' => array_keys($departments),
                                        'Section.college_id' => array_keys($colleges)
                                    )
                                );
                            } else {
                                if (!empty($departments)) {
                                    $options['conditions'][] = array(
                                        'Section.department_id' => array_keys(
                                            $departments
                                        )
                                    );
                                } else {
                                    if (!empty($colleges)) {
                                        $options['conditions'][] = array('Section.college_id' => array_keys($colleges));
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($options)) {
                $options['conditions'][] = array('Section.archive' => 0);
                $this->request->data['Section']['active'] = 0;

                if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                    $options['conditions'][] = array('Section.year_level_id' => $this->year_levels);
                } else {
                    if (isset($this->department_ids) && !empty($this->department_ids) && $this->Session->read(
                            'Auth.User'
                        )['role_id'] == ROLE_COLLEGE && $this->Session->read('Auth.User')['is_admin'] == 1) {
                        $options['conditions'][] = array(
                            'OR' => array(
                                'Section.department_id IS NULL',
                                'Section.year_level_id' => $this->Section->YearLevel->find(
                                    'list',
                                    array(
                                        'fields' => array('YearLevel.id'),
                                        'conditions' => array(
                                            'YearLevel.department_id' => $this->department_ids,
                                            'YearLevel.name' => $this->year_levels
                                        )
                                    )
                                ),
                            )
                        );
                    } else {
                        if (($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $this->Session->read(
                                    'Auth.User'
                                )['is_admin'] == 0) || ($this->Session->read(
                                    'Auth.User'
                                )['role_id'] == ROLE_REGISTRAR && !empty($this->college_ids) && $this->onlyPre == 1)) {
                            $options['conditions'][] = array('Section.department_id IS NULL');
                        } else {
                            if (isset($this->department_ids) && !empty($this->department_ids)) {
                                $options['conditions'][] = array(
                                    'Section.year_level_id' => $this->Section->YearLevel->find(
                                        'list',
                                        array(
                                            'fields' => array('YearLevel.id'),
                                            'conditions' => array(
                                                'YearLevel.department_id' => $this->department_ids,
                                                'YearLevel.name' => (isset($this->request->data['Section']['year_level_id']) && !empty($this->request->data['Section']['year_level_id']) && $this->request->data['Section']['year_level_id'] != 0 ? $this->request->data['Section']['year_level_id'] : $this->year_levels)
                                            )
                                        )
                                    )
                                );
                            } else {
                                $options['conditions'][] = array('Section.department_id IS NULL');
                            }
                        }
                    }
                }
            }
        }

        debug($options['conditions']);

        if (!empty($options['conditions'])) {
            $this->Paginator->settings = array(
                'conditions' => $options['conditions'],
                'contain' => array(
                    'Department' => array(
                        'fields' => array(
                            'Department.id',
                            'Department.name',
                            'Department.shortname',
                            'Department.college_id',
                            'Department.institution_code'
                        )
                    ),
                    'College' => array(
                        'fields' => array(
                            'College.id',
                            'College.name',
                            'College.shortname',
                            'College.institution_code',
                            'College.campus_id',
                        ),
                        'Campus' => array('id', 'name', 'campus_code')
                    ),
                    'Program' => array(
                        'fields' => array(
                            'Program.id',
                            'Program.name',
                            'Program.shortname',
                        )
                    ),
                    'ProgramType' => array(
                        'fields' => array(
                            'ProgramType.id',
                            'ProgramType.name',
                            'ProgramType.shortname',
                        )
                    ),
                    'Curriculum' => array(
                        'fields' => array(
                            'Curriculum.id',
                            'Curriculum.name',
                            'Curriculum.year_introduced',
                            'Curriculum.type_credit',
                            'Curriculum.active',
                        )
                    ),
                    'YearLevel' => array('id', 'name'),
                ),
                'order' => /* (isset($sort) && isset($direction) ? array('Section.'.$sort.'' => $direction) :  */
                    array(
                        'Section.academicyear' => 'DESC',
                        'Section.department_id' => 'ASC',
                        'Section.program_id' => 'ASC',
                        'Section.program_type_id' => 'ASC',
                        'Section.year_level_id' => 'ASC',
                        'Section.name' => 'ASC',
                        'Section.id' => 'ASC'
                    )/* ) */,
                'limit' => $limit,
                'maxLimit' => 500,
                'recursive' => -1,
                'page' => $page
            );

            //$sections = $this->paginate($options);

            try {
                $sections = $this->Paginator->paginate($this->modelClass);
                $this->set(compact('sections'));
            } catch (NotFoundException $e) {
                if (!empty($this->request->data['Search'])) {
                    unset($this->request->data['Search']['page']);
                    unset($this->request->data['Search']['sort']);
                    unset($this->request->data['Search']['direction']);
                }
                if (!empty($this->request->data['Section'])) {
                    unset($this->request->data['Section']['page']);
                    unset($this->request->data['Section']['sort']);
                    unset($this->request->data['Section']['direction']);
                }
                unset($this->passedArgs);
                $this->__init_search_sections();
                return $this->redirect(array('action' => 'index'/* , $this->request->data */));
            } catch (Exception $e) {
                if (!empty($this->request->data['Search'])) {
                    unset($this->request->data['Search']['page']);
                    unset($this->request->data['Search']['sort']);
                    unset($this->request->data['Search']['direction']);
                }
                if (!empty($this->request->data['Section'])) {
                    unset($this->request->data['Section']['page']);
                    unset($this->request->data['Section']['sort']);
                    unset($this->request->data['Section']['direction']);
                }
                unset($this->passedArgs);
                $this->__init_search_sections();
                return $this->redirect(array('action' => 'index'/* , $this->request->data */));
            }
        } else {
            $sections = array();
        }

        if (empty($sections) && !empty($options['conditions'])) {
            $this->Flash->info('No Section is found in the given search criteria. Try changing the search criterias.');
            $turn_off_search = false;
        } else {
            $turn_off_search = false;
            //debug($acceptedStudents[0]);
        }

        //$this->__init_search_index();

        $current_acy = $this->AcademicYear->current_academicyear();

        $acyear_array_options = $this->AcademicYear->academicYearInArray(
            ((explode('/', $current_acy)[0]) - ACY_BACK_FOR_ALL),
            (explode('/', $current_acy)[0])
        );

        $applicable_department_ids = $this->department_ids;
        $applicable_college_ids = $this->college_ids;
        $applicable_program_ids = $this->program_ids;
        $applicable_program_type_ids = $this->program_type_ids;

        $onlyFreshman = (isset($this->onlyPre) && $this->onlyPre == 1 ? 1 : 0);

        $this->set(
            compact(
                'acyear_array_options',
                'colleges',
                'departments',
                /* 'sections', */
                'turn_off_search',
                'limit',
                'name',
                'applicable_department_ids',
                'applicable_college_ids',
                'applicable_program_ids',
                'applicable_program_type_ids',
                'onlyFreshman',
                'page',
                //'sort',
                //'direction',
                'selected_academic_year'
            )
        );
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid section');
            return $this->redirect(array('action' => 'index'));
        }

        $section = $this->Section->find('first', array(
            'conditions' => array(
                'Section.id' => $id,
                'OR' => array(
                    'Section.department_id' => $this->department_id,
                    'Section.college_id' => $this->college_id
                )
            ),
            'contain' => array(
                'Department' => array('id', 'name'),
                'YearLevel' => array('id', 'name'),
                'Program' => array('id', 'name', 'shortname'),
                'ProgramType' => array('id', 'name'),
                'College' => array('id', 'name', 'shortname'),
                'Student' => array(
                    'fields' => array(
                        'id',
                        'full_name',
                        'studentnumber',
                        'email',
                        'phone_mobile',
                        'gender',
                        'academicyear'
                    ),
                    'Department' => array('id', 'name'),
                    'College' => array('id', 'name', 'shortname'),
                    'Program' => array('id', 'name', 'shortname'),
                    'ProgramType' => array('id', 'name'),
                    'order' => array(
                        'Student.academicyear' => 'DESC',
                        'Student.studentnumber' => 'ASC',
                        'Student.id' => 'ASC',
                        'Student.full_name' => 'ASC'
                    ),
                )
            )
        ));

        $this->set('section', $section);
    }

    public function add()
    {

        $isEverything = true;

        if (!empty($this->request->data)) {
            $this->request->data['Section']['college_id'] = $this->college_id;

            if (!empty($this->request->data['Section']['number_of_class'])) {
                if (is_numeric($this->request->data['Section']['number_of_class'])) {
                    $numberofclass = $this->request->data['Section']['number_of_class'];
                } else {
                    $this->Flash->error(
                        'The Number of Section must be number, Please provide number of section to create.'
                    );
                    $isEverything = false;
                }
            } else {
                $this->Flash->error(
                    'The number of class should not be empty. Please provide number of section(s) to create.'
                );
                $isEverything = false;
            }

            $programTypeShortName = $this->Section->ProgramType->field(
                'ProgramType.shortname',
                array('ProgramType.id' => $this->request->data['Section']['program_type_id'])
            );

            if (!empty($this->request->data['Section']['fixed_section_name'])) {
                if (strpos(trim($this->request->data['Section']['fixed_section_name']), ' ') === false) {
                    $fixedsectionname = trim($this->request->data['Section']['fixed_section_name']);
                } else {
                    $this->Flash->error('Please provide fixed section name with out space');
                    $isEverything = false;
                }
            } else {
                $this->Flash->error('Fixed section name should not be empty. Please provide Fixed Section Name');
                $isEverything = false;
            }

            if (!empty($this->request->data['Section']['variable_section_name'])) {
                $variable_section_name = trim($this->request->data['Section']['variable_section_name']);
            } else {
                $this->Flash->error('Please select variable section name.');
                $isEverything = false;
            }

            //Save department as well if user role is not college (use role is department)
            if (ROLE_COLLEGE != $this->role_id) {
                $this->request->data['Section']['department_id'] = $this->department_id;

                $yearlevel = $this->request->data['Section']['year_level_id'];
                $yearlevelname = $this->Section->YearLevel->field('YearLevel.name', array('YearLevel.id' => $yearlevel)
                );
                $yearlevelnameshort = substr($yearlevelname, 0, 1);

                if (!empty($this->request->data['Section']['prefix_section_name'])) {
                    if (strpos(trim($this->request->data['Section']['prefix_section_name']), ' ') === false) {
                        $prefixsectionname = trim(
                                $this->request->data['Section']['prefix_section_name']
                            ) . '' . $programTypeShortName;
                    } else {
                        $this->Flash->error('Please provide prefix section name with out space');
                        $isEverything = false;
                    }
                } else {
                    $this->Flash->error('Prefix Section name should not be empty. Please provide prefix section name.');
                    $isEverything = false;
                }

                if (!empty($this->request->data['Section']['additionalprefix_section_name'])) {
                    if (strpos(trim($this->request->data['Section']['additionalprefix_section_name']), ' ') === false) {
                        $additionalprefix_section_name = trim(
                            $this->request->data['Section']['additionalprefix_section_name']
                        );
                    }
                } else {
                    $additionalprefix_section_name = '';
                }
            }

            if ($isEverything) {
                //********** To maintain the uniqueness of section name for the same college and/or department/academicyear/program/program type

                if (ROLE_COLLEGE != $this->role_id) {
                    $front_section_name = $prefixsectionname . $yearlevelnameshort . ' ' . $fixedsectionname . ' ' . $additionalprefix_section_name;
                } else {
                    $front_section_name = $fixedsectionname;
                }

                //Search using by department and year level as well if user role is not college (use role is department)
                if (ROLE_COLLEGE != $this->role_id) {
                    $conditions = array(
                        'Section.academicyear LIKE' => $this->request->data['Section']['academicyear'] . '%',
                        'Section.college_id' => $this->college_id,
                        'Section.department_id' => $this->department_id,
                        'Section.year_level_id' => $this->request->data['Section']['year_level_id'],
                        'Section.program_id' => $this->request->data['Section']['program_id'],
                        'Section.program_type_id' => $this->request->data['Section']['program_type_id'],
                        'Section.name LIKE ' => $front_section_name . ' ' . '%'
                    );
                } else {
                    $conditions = array(
                        'Section.academicyear LIKE' => $this->request->data['Section']['academicyear'] . '%',
                        'Section.college_id' => $this->college_id,
                        'Section.year_level_id' => $this->request->data['Section']['year_level_id'],
                        'Section.program_id' => $this->request->data['Section']['program_id'],
                        'Section.program_type_id' => $this->request->data['Section']['program_type_id'],
                        'Section.name LIKE ' => $front_section_name . ' ' . '%',
                        "OR" => array("Section.department_id is null", "Section.department_id" => array(0, ''))
                    );
                }

                $similar_sections = $this->Section->find(
                    'list',
                    array('fields' => array('Section.name'), 'conditions' => $conditions)
                );
                $similar_section_variablename = array();

                if (!empty($similar_sections)) {
                    foreach ($similar_sections as $ssv) {
                        $similar_section_variablename[] = substr($ssv, strrpos($ssv, " ") + 1);
                    }
                }

                $numeric_variablesectionname = array();
                $alphabet_variablesectionname = array();

                if (!empty($similar_section_variablename)) {
                    foreach ($similar_section_variablename as $ssvnv) {
                        if (is_numeric($ssvnv)) {
                            $numeric_variablesectionname[] = $ssvnv;
                        } else {
                            $alphabet_variablesectionname[] = $ssvnv;
                        }
                    }
                }

                //****************end of maintain...***************//

                if ($variable_section_name == "Alphabet") {
                    if (empty($alphabet_variablesectionname)) {
                        $variablesectionname = "A";
                    } else {
                        rsort($alphabet_variablesectionname);
                        $variablesectionname = ord($alphabet_variablesectionname[0]);
                        $variablesectionname = $variablesectionname + 1;
                        $variablesectionname = chr($variablesectionname);
                    }
                }

                if ($variable_section_name == "Number") {
                    if (empty($numeric_variablesectionname)) {
                        $variablesectionname = 1;
                    } else {
                        rsort($numeric_variablesectionname);
                        $variablesectionname = $numeric_variablesectionname[0] + 1;
                    }
                }

                unset($this->request->data['Section']['number_of_class']);
                unset($this->request->data['Section']['fixed_section_name']);
                unset($this->request->data['Section']['variable_section_name']);

                if (isset($this->request->data['Section']['prefix_section_name'])) {
                    unset($this->request->data['Section']['prefix_section_name']);
                }

                if (isset($this->request->data['Section']['additionalprefix_section_name'])) {
                    unset($this->request->data['Section']['additionalprefix_section_name']);
                }

                $issave = 0;
                $errMsg = '';

                for ($i = 0; $i < $numberofclass; $i++) {
                    if (ROLE_COLLEGE != $this->role_id) {
                        $name = $prefixsectionname . $yearlevelnameshort . ' ' . $fixedsectionname . ' ' . $additionalprefix_section_name . ' ' . $variablesectionname;
                    } else {
                        $name = $fixedsectionname . ' ' . $variablesectionname;
                    }

                    $this->request->data['Section']['name'] = $name;
                    $this->Section->create();

                    if ($this->Section->save($this->request->data)) {
                        $issave = 1;
                    } else {
                        $errMsg = "$name is already taken. Please use another section name.";
                    }

                    if ($variable_section_name == "Alphabet") {
                        $variablesectionname = ord($variablesectionname);
                        $variablesectionname = $variablesectionname + 1;
                        $variablesectionname = chr($variablesectionname);
                    } else {
                        $variablesectionname = $variablesectionname + 1;
                    }
                }

                if ($issave) {
                    $this->Flash->success('The section(s) has been saved');

                    $redirectSearchDataFilters['Section']['academicyear'] = $this->request->data['Section']['academicyear'];
                    $redirectSearchDataFilters['Section']['program_id'] = $this->request->data['Section']['program_id'];
                    $redirectSearchDataFilters['Section']['program_type_id'] = $this->request->data['Section']['program_type_id'];

                    if ($this->role_id == ROLE_DEPARTMENT) {
                        $redirectSearchDataFilters['Section']['year_level_id'] = $this->request->data['Section']['year_level_id'];
                        $redirectSearchDataFilters['Section']['department_id'] = $this->department_id;
                    } else {
                        //$redirectSearchDataFilters['Section']['year_level_id'] = null;
                        $redirectSearchDataFilters['Section']['college_id'] = $this->college_id;

                        if (isset($this->request->data['Section']['department_id'])) {
                            $redirectSearchDataFilters['Section']['department_id'] = $this->request->data['Section']['department_id'];
                        }
                    }

                    $this->__init_clear_session_filters();

                    $this->Session->write('search_sections', $redirectSearchDataFilters['Section']);

                    return $this->redirect(array('action' => 'index'));
                } else {
                    if (!empty($errMsg)) {
                        $this->Flash->error($errMsg . '.');
                    } else {
                        $this->Flash->error('The section could not be saved. Please try again.');
                    }
                }
            }
        }

        $curriculums = array();

        if ($this->role_id != ROLE_COLLEGE) {
            $curriculums = ClassRegistry::init('Curriculum')->find('list', array(
                'conditions' => array(
                    'Curriculum.department_id' => $this->department_ids,
                    'Curriculum.program_id' => array_values($this->program_ids)[0],
                    'Curriculum.registrar_approved' => 1,
                    'Curriculum.active' => 1
                ),
                'fields' => array('Curriculum.id', 'Curriculum.curriculum_detail'),
                'order' => array('Curriculum.program_id' => 'ASC', 'Curriculum.created' => 'DESC'),
            ));

            if (!empty($curriculums)) {
                $this->set(compact('curriculums'));
            }
        }

        if (isset($this->request->data['Section']['academicyear']) && !empty($this->request->data['Section']['academicyear'])) {
            $thisacademicyear = $this->request->data['Section']['academicyear'];
        } else {
            $thisacademicyear = $this->AcademicYear->current_academicyear();
        }

        if (isset($this->request->data['Section']['program_id'])) {
            $selectedProgram = $this->request->data['Section']['program_id'];
        } else {
            $selectedProgram = array_values($this->program_ids)[0];
        }

        if (isset($this->request->data['Section']['program_type_id'])) {
            $selectedProgramType = $this->request->data['Section']['program_type_id'];
        } else {
            $selectedProgramType = array_values($this->program_type_ids)[0];
        }

        if ($this->role_id == ROLE_DEPARTMENT) {
            if (isset($this->request->data['Section']['year_level_id'])) {
                $yearLevelsss = $this->Section->YearLevel->find(
                    'list',
                    array(
                        'conditions' => array(
                            'YearLevel.department_id' => $this->department_ids,
                            'YearLevel.id' => $this->request->data['Section']['year_level_id']
                        )
                    )
                );
                $selectedYearLevelId = array_keys($yearLevelsss)[0];
                $selectedYearLevelName = array_values($yearLevelsss)[0];
            } else {
                $yearLevelsss = $this->Section->YearLevel->find(
                    'list',
                    array(
                        'conditions' => array(
                            'YearLevel.department_id' => $this->department_ids,
                            'YearLevel.name' => $this->year_levels
                        )
                    )
                );
                $selectedYearLevelId = array_keys($yearLevelsss)[0];
                $selectedYearLevelName = array_values($yearLevelsss)[0];
            }

            if (isset($this->request->data['Section']['curriculum_id'])) {
                $selectedCcurriculumId = $this->request->data['Section']['curriculum_id'];
            } elseif (!empty($curriculums)) {
                $selectedCcurriculumId = array_keys($curriculums)[0];
            } else {
                $selectedCcurriculumId = '%';
            }
        } else {
            $selectedYearLevelId = null;
            $selectedCcurriculumId = null;
            $selectedYearLevelName = null;
        }

        debug($selectedYearLevelName);

        //Summery of section un allocated students
        $summary_data = $this->Section->getsectionlessstudentsummary(
            $thisacademicyear,
            $this->college_id,
            $this->department_id,
            $this->role_id
        );
        $curriculum_unattached_student_count = $this->Section->getcurriculumunattachedstudentsummary(
            $thisacademicyear,
            $this->college_id,
            $this->department_id,
            $this->role_id
        );
        //$section_less_total_students = $this->Section->countsectionlessstudents($this->college_id,
        //$this->role_id,$this->department_id,$thisacademicyear);

        $variable_section_name_array = array();
        $variable_section_name_array['Alphabet'] = ("A, B, C ...");
        $variable_section_name_array['Number'] = ("1, 2, 3 ...");

        $collegename = $this->Section->College->field('College.name', array('College.id' => $this->college_id));
        $collegeshortname = $this->Section->College->field('College.shortname', array('College.id' => $this->college_id)
        );

        $departmentname = $this->Section->Department->field(
            'Department.name',
            array('Department.id' => $this->department_id)
        );
        $departmentshortname = $this->Section->Department->field(
            'Department.shortname',
            array('Department.id' => $this->department_id)
        );

        $yearLevels = $this->Section->YearLevel->find(
            'list',
            array('conditions' => array('YearLevel.department_id' => $this->department_id))
        );
        // $programs = $this->Section->Program->find('list');
        // $programTypes = $this->Section->ProgramType->find('list');

        $programTypeShortName = $this->Section->ProgramType->field(
            'ProgramType.shortname',
            array(
                'ProgramType.id' => (isset($this->request->data['Section']['program_type_id']) ? $this->request->data['Section']['program_type_id'] : array_values(
                    $this->program_type_ids
                )[0])
            )
        );
        $programShortName = $this->Section->Program->field(
            'Program.shortname',
            array(
                'Program.id' => (isset($this->request->data['Section']['program_id']) ? $this->request->data['Section']['program_id'] : array_values(
                    $this->program_ids
                )[0])
            )
        );

        $programss = $this->Section->Program->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
        );
        $program_typess = $programTypess = $this->Section->ProgramType->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
        );

        $acyear = $this->AcademicYear->current_academicyear();

        $GCyear = substr(($this->AcademicYear->current_academicyear()), 0, 4);

        $GCmonth = date('n');
        $GCday = date('j');

        if ($GCmonth >= 9) {
            $GCyear = $GCyear;
        } else {
            $GCyear = $GCyear + 1;
        }

        $ETY = $this->EthiopicDateTime->GetEthiopicYear($GCday, $GCmonth, $GCyear);

        if ($GCmonth == 9) {
            //$ETY+=1;
        }

        if (ROLE_COLLEGE == $this->role_id) {
            $FixedSectionName = isset($collegeshortname) ? /* 'PRE-'. */
                $collegeshortname . $ETY : $collegeshortname/* 'PRE-' */ . $ETY;
        } else {
            $FixedSectionName = isset($departmentshortname) ? $departmentshortname . $ETY : $collegeshortname . $ETY;
        }

        for ($i = 1; $i <= 26; $i++) {
            $number_of_class[$i] = $i;
        }

        $prefix_section_name = array();

        $programForPrefix = $this->Section->Program->find(
            'list',
            array(
                'conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1),
                'fields' => array('Program.shortname', 'Program.shortname'),
                'order' => array('Program.id' => 'ASC')
            )
        );

        if (empty($programForPrefix)) {
            $programForPrefix = $this->Section->Program->find(
                'list',
                array(
                    'conditions' => array(/* 'Program.id' => $this->program_ids, */ 'Program.active' => 1),
                    'fields' => array('Program.shortname', 'Program.shortname'),
                    'order' => array('Program.id' => 'ASC')
                )
            );
        }

        if (!empty($programForPrefix)) {
            foreach ($programForPrefix as $key => $shortname) {
                $prefix_section_name[$key] = $shortname;
            }
        }

        $this->set(
            compact(
                'departmentname',
                'yearLevels',
                'prefix_section_name',
                'variable_section_name_array',
                'collegename',
                'number_of_class',
                'programss',
                'FixedSectionName',
                'programTypess',
                'summary_data',
                'thisacademicyear',
                'curriculum_unattached_student_count'
            )
        );
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Flash->error('Invalid section');
            return $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data)) {
            $this->request->data['Section']['name'] = trim($this->request->data['Section']['name']);

            if ($this->Section->save($this->request->data)) {
                $this->Flash->success('The section has been saved');
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error('The section could not be saved. Please, try again.');
            }
        }

        if (empty($this->request->data)) {
            //$this->request->data = $this->Section->read(null, $id);

            $section = $this->request->data = $this->Section->find('first', array(
                'conditions' => array(
                    'Section.id' => $id,
                    'OR' => array(
                        'Section.department_id' => $this->department_id,
                        'Section.college_id' => $this->college_id
                    )
                ),
                'contain' => array(
                    'Department' => array('id', 'name'),
                    'YearLevel' => array('id', 'name'),
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name'),
                    'College' => array('id', 'name'),
                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                )
            ));
        }

        $curriculums = array();

        if ($this->role_id != ROLE_COLLEGE) {
            $curriculums = ClassRegistry::init('Curriculum')->find('list', array(
                'conditions' => array(
                    'Curriculum.department_id' => $this->department_ids,
                    'Curriculum.program_id' => array_values($this->program_ids)[0],
                    'Curriculum.registrar_approved' => 1,
                    'Curriculum.active' => 1
                ),
                'fields' => array('Curriculum.id', 'Curriculum.curriculum_detail'),
                'order' => array('Curriculum.program_id' => 'ASC', 'Curriculum.created' => 'DESC'),
            ));

            if (!empty($curriculums)) {
                $this->set(compact('curriculums'));
            }
        }

        $colleges = $this->Section->College->find(
            'list',
            array('conditions' => array('College.id' => $this->college_id))
        );
        $departments = $this->Section->Department->find(
            'list',
            array('conditions' => array('Department.id' => $this->department_id))
        );
        $yearLevels = $this->Section->YearLevel->find(
            'list',
            array('conditions' => array('YearLevel.department_id' => $this->department_id))
        );

        $programs = $this->Section->Program->find('list');
        $programTypes = $this->Section->ProgramType->find('list');

        $this->set(compact('section', 'colleges', 'departments', 'yearLevels', 'programs', 'programTypes'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid id for section');
            return $this->redirect(array('action' => 'index'));
        }

        if ($this->Section->isSectionEmpty($id)) {
            //If the course empty, then hard deletion can be performed
            // is course published in the name of the section ?
            // debug($this->Section->isCoursePublishedInTheSection($id));
            if ($this->Section->isCoursePublishedInTheSection($id) == false) {
                if ($this->Section->delete($id)) {
                    $this->Flash->success('Section deleted.');
                    $this->redirect(array('action' => 'index'));
                }
            } else {
                $this->Flash->error(
                    'Course has been published in the name of the section. You can not delete the section right now. If you have to delete the section, you need to Unpublish/delete publisehd course first.'
                );
            }
            return $this->redirect(array('action' => 'index'));
        } else {
            //In order to soft delete (archive) section the section must have not active student. If the section have active students, the system shoud notify the user first to move or delete all active student in that section
            $studentssections = $this->Section->StudentsSection->find(
                'all',
                array('conditions' => array('StudentsSection.section_id' => $id))
            );
            $active_students_ofsection_count = 0;

            if (!empty($studentssections)) {
                foreach ($studentssections as $ssk => $ssv) {
                    if ($ssv['StudentsSection']['archive'] == 0) {
                        $active_students_ofsection_count++;
                        //$this->Section->StudentsSection->id = $ssv['StudentsSection']['id'];
                        //$this->Section->StudentsSection->saveField('archive','1');
                    }
                }
            }

            if ($active_students_ofsection_count == 0) {
                //then soft delete (archive) section
                $this->Section->id = $id;
                if ($this->Section->saveField('archive', '1')) {
                    $this->Flash->success('Section is now Archieved(soft deleted)');
                    return $this->redirect(array('action' => 'index'));
                }
                $this->Flash->error('Section was not deleted or archieved');
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(
                    '<span style="margin-right: 15px;"></span>Section have ' . $active_students_ofsection_count . ' active student(s), In order to delete this section, first you have to move or delete all students of the section in ',
                    "session_flash_link",
                    array(
                        "class" => 'error-box error-message',
                        "link_text" => " this page",
                        "link_url" => array(
                            "controller" => "sections",
                            "action" => "display_sections",
                            "admin" => false
                        )
                    )
                );
                return $this->redirect(array('action' => 'index'));
            }
        }
    }

    public function assign()
    {

        if ($this->Session->read('sdata')) {
            $this->request->data['continue'] = true;
            $this->request->data = $this->Session->read('sdata');
        }

        $this->Session->delete('sdata');

        debug($this->request->data);

        if (!empty($this->request->data['Section']['academicyearSearch'])) {
            $academicyear = $this->request->data['Section']['academicyearSearch'];
        } else {
            $academicyear = $this->AcademicYear->current_academicyear();
        }

        if (!empty($this->request->data['Section']['program_id'])) {
            $selected_program = $this->request->data['Section']['program_id'];
        } else {
            $selected_program = 1;
        }

        if (!empty($this->request->data['Section']['program_type_id'])) {
            $selected_program_type = $this->request->data['Section']['program_type_id'];
        } else {
            $selected_program_type = 1;
        }

        //$academicyear = $this->AcademicYear->current_academicyear();
        $summary_data = $this->Section->getsectionlessstudentsummary(
            $academicyear,
            $this->college_id,
            $this->department_id,
            $this->role_id
        );

        $curriculum_unattached_student_count = $this->Section->getcurriculumunattachedstudentsummary(
            $academicyear,
            $this->college_id,
            $this->department_id,
            $this->role_id
        );
        $programs = $this->Section->Program->find('list');
        $programTypes = $this->Section->ProgramType->find('list');

        /* if (count($this->college_ids) == 1 && (in_array(14, $this->college_ids) || in_array(15, $this->college_ids))) {
			$programs = $this->Section->Program->find('list', array('conditions' => array('Program.id'=> PROGRAM_REMEDIAL)));
		} */

        $assignment_type_array = array();
        $assignment_type_array['alphabet'] = "By Alphabet";
        $assignment_type_array['result'] = "Fairly By Result";

        if (ROLE_COLLEGE != $this->role_id) {
            $yearLevels = $this->Section->YearLevel->find('list', array(
                'conditions' => array(
                    'YearLevel.department_id' => $this->department_id,
                    'YearLevel.name' => '1st'
                )
            ));
            $section_less_total_students = $this->Section->countsectionlessstudents(
                $this->college_id,
                $this->role_id,
                $this->department_id,
                $academicyear,
                $selected_program,
                $selected_program_type,
                null
            );
        } else {
            $section_less_total_students = $section_less_total_students = $this->Section->countsectionlessstudents(
                $this->college_id,
                $this->role_id,
                null,
                $academicyear,
                $selected_program,
                $selected_program_type,
                null
            );
        }

        //$section_less_total_students = 0;
        $isbeforesearch = 1;

        $collegename = $this->Section->College->field('College.name', array('College.id' => $this->college_id));
        $departmentname = $this->Section->Department->field(
            'Department.name',
            array('Department.id' => $this->department_id)
        );

        $this->set(
            compact(
                'collegename',
                'departmentname',
                'programs',
                'programTypes',
                'assignment_type_array',
                'academicyear',
                'isbeforesearch',
                'summary_data',
                'curriculum_unattached_student_count',
                'yearLevels',
                'section_less_total_students'
            )
        );

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            if ($this->Session->read('sdata')) {
                $this->Session->delete('sdata');
            }

            $isbeforesearch = 0;
            $academicyear = $this->request->data['Section']['academicyearSearch'];
            $assignmenttype = $this->request->data['Section']['assignment_type'];

            $selected_program = $this->request->data['Section']['program_id'];
            $selected_program_name = $this->Section->Program->field(
                'Program.name',
                array('Program.id' => $selected_program)
            );
            $selected_program_type = $this->request->data['Section']['program_type_id'];

            //$selected_program_type = $this->Section->ProgramType->field('ProgramType.name', array('ProgramType.id'=>$selected_program_type_id));
            if (ROLE_COLLEGE != $this->role_id) {
                $yearlevel = $this->request->data['Section']['year_level_id'];
                $yearlevelname = $this->Section->YearLevel->field('YearLevel.name', array('YearLevel.id' => $yearlevel)
                );
            }

            $summary_data = $this->Section->getsectionlessstudentsummary(
                $academicyear,
                $this->college_id,
                $this->department_id,
                $this->role_id
            );
            $curriculum_unattached_student_count = $this->Section->getcurriculumunattachedstudentsummary(
                $academicyear,
                $this->college_id,
                $this->department_id,
                $this->role_id
            );
            $section_less_total_students = $this->Section->countsectionlessstudents(
                $this->college_id,
                $this->role_id,
                $this->department_id,
                $academicyear,
                $selected_program,
                $selected_program_type
            );

            //*******check number of curriculum among students in selected criteria and if there is more than one curriculum, the user must selected one curriculum
            $sectionlessStudentCurriculum = $this->Section->getSectionlessStudentCurriculum(
                $academicyear,
                $this->college_id,
                $this->department_id,
                $this->role_id,
                $selected_program,
                $selected_program_type
            );

            $curriculum_id = null;
            $curriculum_count = count($sectionlessStudentCurriculum);

            if ($this->Session->read('empty_Curriculum')) {
                $this->Session->delete('empty_Curriculum');
            }

            if ($curriculum_count == 1) {
                if (empty($curriculum_id)) {
                    $empty_Curriculum = 1;
                    $this->Session->write('empty_Curriculum', $empty_Curriculum);
                }

                $curriculum_id = $sectionlessStudentCurriculum[0];
                $this->request->data['Section']['Curriculum'] = $curriculum_id;
                $this->Session->write('selected_curriculum', $curriculum_id);
                $this->request->data['Section']['curriculum_search'] = 1;
                $this->Session->write('curriculum_search', $this->request->data['Section']['curriculum_search']);
                $this->request->data['continue'] = true;
            } else {
                if ($curriculum_count > 1) {
                    $sectionlessStudentCurriculumArray = array();

                    if (!empty($sectionlessStudentCurriculum)) {
                        foreach ($sectionlessStudentCurriculum as $sscv) {
                            $sectionlessStudentCurriculumArray[$sscv] = Classregistry::init('Curriculum')->field(
                                'Curriculum.curriculum_detail',
                                array('Curriculum.id' => $sscv)
                            );
                        }
                    }

                    if ($this->Session->read('curriculum_search')) {
                        $this->Session->delete('curriculum_search');
                    }

                    $isbeforesearch = 1;

                    $this->set(
                        compact(
                            'sectionlessStudentCurriculum',
                            'sectionlessStudentCurriculumArray',
                            'isbeforesearch',
                            'section_less_total_students'
                        )
                    );
                }
            }

            $this->set(compact('isbeforesearch', 'section_less_total_students'));
            debug($this->request->data);
        }

        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            debug($this->request->data);
            $empty_Curriculum = 0;

            if ($this->Session->read('empty_Curriculum')) {
                $empty_Curriculum = $this->Session->read('empty_Curriculum');
            }

            if (!empty($this->request->data['Section']['Curriculum']) || $empty_Curriculum == 1) {
                $isbeforesearch = 0;
                $academicyear = $this->request->data['Section']['academicyearSearch'];
                $selected_program = $this->request->data['Section']['program_id'];
                $selected_program_type = $this->request->data['Section']['program_type_id'];
                $yearlevel = null;

                if (ROLE_DEPARTMENT == $this->role_id) {
                    $yearlevel = $this->request->data['Section']['year_level_id'];
                }

                $assignmenttype = $this->request->data['Section']['assignment_type'];
                $selected_curriculum = $this->request->data['Section']['Curriculum'];
                $selected_program_name = $this->Section->Program->field(
                    'Program.name',
                    array('Program.id' => $selected_program)
                );

                $this->Session->write('academicyear', $academicyear);
                $this->Session->write('selected_program', $selected_program);
                $this->Session->write('selected_program_type', $selected_program_type);
                $this->Session->write('yearlevel', $yearlevel);
                $this->Session->write('assignmenttype', $assignmenttype);
                $this->Session->write('selected_curriculum', $selected_curriculum);

                $sections = $this->Section->getSectionForAssignment(
                    $academicyear,
                    $this->college_id,
                    $this->department_id,
                    $this->role_id,
                    $selected_program,
                    $selected_program_type,
                    $yearlevel,
                    $selected_curriculum
                );
                $current_sections_occupation = $this->Section->currentsectionsoccupation($sections);

                //Find section curriculum for one of section students
                $sections_curriculum_name = $this->Section->sectionscurriculum($sections);
                debug($sections_curriculum_name);

                $section_less_total_students = $this->Section->countsectionlessstudents(
                    $this->college_id,
                    $this->role_id,
                    $this->department_id,
                    $academicyear,
                    $selected_program,
                    $selected_program_type,
                    $selected_curriculum
                );
                debug($section_less_total_students);

                $collegename = $this->Section->College->field('College.name', array('College.id' => $this->college_id));
                $departmentname = $this->Section->Department->field(
                    'Department.name',
                    array('Department.id' => $this->department_id)
                );
                $yearLevels = $this->Section->YearLevel->find(
                    'list',
                    array('conditions' => array('YearLevel.department_id' => $this->department_id))
                );

                if (!isset($this->request->data['Section']['curriculum_search'])) {
                    $sectionlessStudentCurriculum = $this->Section->getSectionlessStudentCurriculum(
                        $academicyear,
                        $this->college_id,
                        $this->department_id,
                        $this->role_id,
                        $selected_program,
                        $selected_program_type
                    );
                    $sectionlessStudentCurriculumArray = array();

                    if (!empty($sectionlessStudentCurriculum)) {
                        foreach ($sectionlessStudentCurriculum as $sscv) {
                            $sectionlessStudentCurriculumArray[$sscv] = Classregistry::init('Curriculum')->field(
                                'Curriculum.curriculum_detail',
                                array('Curriculum.id' => $sscv)
                            );
                        }
                    }

                    $this->set(compact('sectionlessStudentCurriculum', 'sectionlessStudentCurriculumArray'));
                }

                $this->set(
                    compact(
                        'sections',
                        'section_less_total_students',
                        'isbeforesearch',
                        'summary_data',
                        'curriculum_unattached_student_count',
                        'sections_curriculum_name',
                        'collegename',
                        'departmentname',
                        'yearLevels',
                        'students',
                        'academicyear',
                        'selected_program_name',
                        'current_sections_occupation'
                    )
                );
            } else {
                $this->Flash->error('Please select curriculum.');

                $academicyear = $this->request->data['Section']['academicyearSearch'];
                $selected_program = $this->request->data['Section']['program_id'];
                $selected_program_type = $this->request->data['Section']['program_type_id'];

                $section_less_total_students = $this->Section->countsectionlessstudents(
                    $this->college_id,
                    $this->role_id,
                    $this->department_id,
                    $academicyear,
                    $selected_program,
                    $selected_program_type
                );
                $sectionlessStudentCurriculum = $this->Section->getSectionlessStudentCurriculum(
                    $academicyear,
                    $this->college_id,
                    $this->department_id,
                    $this->role_id,
                    $selected_program,
                    $selected_program_type
                );
                $sectionlessStudentCurriculumArray = array();

                if (!empty($sectionlessStudentCurriculum)) {
                    foreach ($sectionlessStudentCurriculum as $sscv) {
                        $sectionlessStudentCurriculumArray[$sscv] = Classregistry::init('Curriculum')->field(
                            'Curriculum.curriculum_detail',
                            array('Curriculum.id' => $sscv)
                        );
                    }
                }

                $this->set(
                    compact(
                        'sectionlessStudentCurriculum',
                        'sectionlessStudentCurriculumArray',
                        'section_less_total_students'
                    )
                );
            }
        }

        $isassign = 0;

        if (isset($this->request->data['assign'])) {
            $academicyear = $this->Session->read('academicyear');
            $assignmenttype = $this->Session->read('assignmenttype');
            $selected_program = $this->Session->read('selected_program');
            $selected_program_type = $this->Session->read('selected_program_type');
            $selected_curriculum = $this->Session->read('selected_curriculum');

            if (ROLE_COLLEGE != $this->role_id) {
                $yearlevel = $this->Session->read('yearlevel');
                $yearlevelname = $this->Section->YearLevel->field('YearLevel.name', array('YearLevel.id' => $yearlevel)
                );
            }

            $sectionlesstotalstudents = $this->Section->countsectionlessstudents(
                $this->college_id,
                $this->role_id,
                $this->department_id,
                $academicyear,
                $selected_program,
                $selected_program_type,
                $selected_curriculum
            );
            $program_type_id = $selected_program_type;
            $find_the_equvilaent_program_type = unserialize(
                $this->Section->Student->ProgramType->field(
                    'ProgramType.equivalent_to_id',
                    array('ProgramType.id' => $selected_program_type)
                )
            );

            if (!empty($find_the_equvilaent_program_type)) {
                $selected_program_type_array = array();
                $selected_program_type_array[] = $selected_program_type;
                $program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
            }

            //Search using by department and year level as well if user role is not college (use role is department)
            if (ROLE_COLLEGE != $this->role_id) {
                $conditions = array(
                    'AcceptedStudent.academicyear' => $academicyear,
                    //'Student.college_id' => $this->college_id,
                    'Student.department_id' => $this->department_id,
                    'Student.program_id' => $selected_program,
                    'Student.program_type_id' => $program_type_id,
                    'Student.curriculum_id' => $selected_curriculum,
                    'Student.graduated' => 0,
                );
            } else {
                $conditions = array(
                    'AcceptedStudent.academicyear' => $academicyear,
                    'Student.college_id' => $this->college_id,
                    'Student.program_id' => $selected_program,
                    'Student.program_type_id' => $program_type_id,
                    //'Student.curriculum_id' => $selected_curriculum,
                    'Student.curriculum_id IS NULL',
                    'Student.department_id is null',
                    'Student.graduated' => 0,
                );
            }

            $selected_assignment_type = $this->request->data['Section']['assignment_type'];

            if ($sectionlesstotalstudents != 0) {
                if ($selected_assignment_type == 'result') {
                    $students = $this->Section->Student->find('all', array(
                        'conditions' => $conditions,
                        'fields' => array(
                            'Student.id',
                            'Student.full_name',
                            'Student.studentnumber',
                            'Student.gender',
                            'Student.academicyear'
                        ),
                        'contain' => array(
                            'Section' => array(
                                'fields' => array('Section.id', 'Section.name')
                            ),
                            'AcceptedStudent' => array(
                                'fields' => array('AcceptedStudent.id')
                            )
                        ),
                        'order' => array(
                            'AcceptedStudent.EHEECE_total_results' => 'DESC',
                            'AcceptedStudent.sex' => 'ASC',
                            'AcceptedStudent.region_id' => 'ASC'
                        )
                    ));

                    $sectionless_student = array();

                    if (!empty($students)) {
                        foreach ($students as $k => $v) {
                            $check_student_section = count($v['Section']);
                            if ($check_student_section == 0) {
                                $sectionless_student[] = $v['Student']['id'];
                            } else {
                                $is_pre_student = 1;
                                foreach ($v['Section'] as $psk => $psv) {
                                    if (isset($psv['department_id']) && is_numeric(
                                            $psv['department_id']
                                        ) && $psv['department_id'] > 0) {
                                        $is_pre_student = 0;
                                        break;
                                    } else {
                                        $last_registration_semester = ClassRegistry::init('CourseRegistration')->field(
                                            'CourseRegistration.semester',
                                            array('CourseRegistration.section_id' => $psv['StudentsSection']['section_id'])
                                        );
                                        debug($last_registration_semester);
                                        if ($psv['StudentsSection']['archive'] == 0) {
                                            $is_pre_student = 0;
                                            break;
                                        } else {
                                            $is_pre_student = 1;
                                        }
                                    }
                                }
                                if ($is_pre_student == 1) {
                                    $sectionless_student[] = $v['Student']['id'];
                                }
                            }
                        }
                    }

                    $sectionless_student_count = count($sectionless_student);
                    $data = $this->request->data['Section']['Sections']; //Selected Section array

                    unset($this->request->data['Section']['Sections']);
                    $selected_section_count = count($data);

                    $j = 0; //index for selected student section

                    if ($sectionless_student_count > 0 && $selected_section_count > 0) {
                        for ($i = 0; $i < $sectionless_student_count; $i++) {
                            if ($j >= $selected_section_count) {
                                $j = $j % $selected_section_count;
                            }
                            //$available_section = $this->request->data['Section'][$data[$j]]['id'];
                            //$student_for_this_section = $sectionless_student[$i];

                            $this->request->data['StudentsSection']['section_id'] = $this->request->data['Section'][$data[$j]]['id'];
                            $this->request->data['StudentsSection']['student_id'] = $sectionless_student[$i];
                            $this->Section->StudentsSection->create();
                            $this->Section->StudentsSection->save($this->request->data['StudentsSection']);
                            //$this->Section->habtmAdd('Student', $available_section, $student_for_this_section);
                            $j = $j + 1;
                            $isassign = 1; //used to flash message
                        }
                    }

                    if ($isassign) {
                        $this->Flash->success('The section(s) assignment has(have) been completed successfully');
                        return $this->redirect(
                            array('action' => 'display_sections', $this->request->data['StudentsSection']['section_id'])
                        );
                    } else {
                        $this->Flash->error('The section(s) assignment could not be completed. Please, try again.');
                    }
                } else {
                    if ($this->Section->isSectionAssignedStudentsEqualToTotalNumberofAvaliableStudents(
                        $this->request->data['Section'],
                        $sectionlesstotalstudents
                    )) {
                        $students = $this->Section->Student->find('all', array(
                            'conditions' => $conditions,
                            'fields' => array(
                                'Student.id',
                                'Student.full_name',
                                'Student.studentnumber',
                                'Student.gender',
                                'Student.academicyear'
                            ),
                            'contain' => array(
                                'Section' => array(
                                    'fields' => array('Section.id', 'Section.name')
                                ),
                                'AcceptedStudent' => array(
                                    'fields' => array('AcceptedStudent.id')
                                )
                            ),
                            // distribute based on name, sex and region
                            'order' => array(
                                'AcceptedStudent.first_name' => 'ASC',
                                'AcceptedStudent.sex' => 'ASC',
                                'AcceptedStudent.region_id' => 'ASC',
                                'AcceptedStudent.EHEECE_total_results' => 'DESC'
                            )
                        ));

                        $sectionless_student = array();

                        if (!empty($students)) {
                            foreach ($students as $k => $v) {
                                $check_student_section = count($v['Section']);
                                if ($check_student_section == 0) {
                                    $sectionless_student[] = $v['Student']['id'];
                                } else {
                                    $is_pre_student = 1;
                                    foreach ($v['Section'] as $psk => $psv) {
                                        if (!empty($psv['department_id'])) {
                                            $is_pre_student = 0;
                                            break;
                                        } else {
                                            if ($psv['StudentsSection']['archive'] == 0) {
                                                $is_pre_student = 0;
                                                break;
                                            } else {
                                                $is_pre_student = 1;
                                            }
                                        }
                                    }
                                    if ($is_pre_student == 1) {
                                        $sectionless_student[] = $v['Student']['id'];
                                    }
                                }
                            }
                        }
                        //call participating sections
                        if ($this->role_id == ROLE_COLLEGE) {
                            $yearlevel = 0;
                        }

                        $sections = $this->Section->getSectionForAssignment(
                            $academicyear,
                            $this->college_id,
                            $this->department_id,
                            $this->role_id,
                            $selected_program,
                            $selected_program_type,
                            $yearlevel,
                            $selected_curriculum
                        );
                        $sections_count = count($sections);
                        $student_index = 0;
                        $isassign = 0;
                        $available_section = null;

                        if ($sections_count > 0) {
                            for ($i = 0; $i < $sections_count; $i++) {
                                $number_per_section = $this->request->data['Section'][$i]['number'];
                                $available_section = $this->request->data['Section'][$i]['id'];
                                for ($j = 0; $j < $number_per_section; $j++) {
                                    //$student_for_this_section = $sectionless_student[$student_index];
                                    debug($student_index);
                                    debug($sectionless_student);
                                    $this->request->data['StudentsSection']['student_id'] = $sectionless_student[$student_index];
                                    $this->request->data['StudentsSection']['section_id'] = $available_section;
                                    $this->Section->StudentsSection->create();
                                    $this->Section->StudentsSection->save($this->request->data['StudentsSection']);
                                    //$this->Section->habtmAdd('Student', $available_section, $student_for_this_section);

                                    $student_index = $student_index + 1;
                                    $isassign = 1; //used to flash message
                                }
                            }
                        } else {
                            $this->Flash->error('The no sections found for assignment.');
                            //return $this->redirect(array('action' => 'display_sections', $available_section));
                        }

                        if ($isassign) {
                            $this->Flash->success('The section(s) assignment has(have) been completed successfully.');
                            return $this->redirect(array('action' => 'display_sections', $available_section));
                        } else {
                            $this->Flash->error('The section(s) assignment could not be complete. Please, try again.');
                        }
                    } else {
                        $error = $this->Section->invalidFields();

                        if (isset($error['section'])) {
                            $this->Flash->error($error['section'][0]);
                        }

                        $this->set('academicyear', $this->request->data['Section']['academicyear']);
                        $this->set('section_less_total_students', $sectionlesstotalstudents);
                        $this->request->data['Section']['Curriculum'] = $selected_curriculum;

                        if ($this->Session->read('curriculum_search')) {
                            $this->request->data['Section']['curriculum_search'] = $this->Session->read(
                                'curriculum_search'
                            );
                        }
                        //$this->Session->write('sdata',$this->request->data);
                        //return $this->redirect(array('action'=>'assign',true));
                    }
                }
            } else {
                $this->Flash->error('There is no Student to assign section in given parameters');
                $this->set('academicyear', $this->request->data['Section']['academicyear']);
                $this->set('section_less_total_students', $sectionlesstotalstudents);
            }
        }

        $this->set(compact('assignmenttype', 'selected_program_type', 'selected_program'));
    }

    public function display_sections($id = null)
    {

        $this->__init_search_sections();

        if (isset($this->request->data['swapStudentSection']) && !empty($this->request->data['swapStudentSection'])) {
            $rearrangePossible = null;
            if ($this->role_id == ROLE_COLLEGE) {
                $rearrangePossible = $this->Section->rearrangeSectionList(
                    $this->request->data['Section']['academicyear'],
                    $this->college_id,
                    $this->request->data['Section']['year_level_id'],
                    $this->request->data['Section']['program_id'],
                    $this->request->data['Section']['program_type_id'],
                    $this->request->data['Section']['swap'],
                    1
                );
            } else {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $rearrangePossible = $this->Section->rearrangeSectionList(
                        $this->request->data['Section']['academicyear'],
                        $this->department_id,
                        $this->request->data['Section']['year_level_id'],
                        $this->request->data['Section']['program_id'],
                        $this->request->data['Section']['program_type_id'],
                        $this->request->data['Section']['swap']
                    );
                }
            }

            if ($rearrangePossible == 3) {
                $this->Flash->error(
                    'You can not swap students once you published courses. Please delete published courses if grade is not submitted.'
                );
            } else {
            }
            $this->request->data['search'] = true;
        }


        if (!empty($id) && !isset($this->request->data['search'])) {
            $selectedSectionDetails = $this->Section->find(
                'first',
                array('conditions' => array('Section.id' => $id), 'recursive' => -1)
            );

            debug($selectedSectionDetails);

            if (!empty($selectedSectionDetails)) {
                $this->request->data['Section'] = $selectedSectionDetails['Section'];
            } else {
                $this->request->data['Section']['program_id'] = $this->Section->field(
                    'Section.program_id',
                    array('Section.id' => $id)
                );
                $this->request->data['Section']['program_type_id'] = $this->Section->field(
                    'Section.program_type_id',
                    array('Section.id' => $id)
                );
                if (empty($this->request->data['Section']['academicyear'])) {
                    $this->request->data['Section']['academicyear'] = $this->AcademicYear->current_academicyear();
                }
                if (ROLE_COLLEGE != $this->role_id) {
                    $this->request->data['Section']['year_level_id'] = $this->Section->field(
                        'Section.year_level_id',
                        array('Section.id' => $id)
                    );
                }
            }

            $this->request->data['search'] = true;

            $this->__init_clear_session_filters();
            $this->__init_search_sections();
        }

        //$programs = $this->Section->Program->find('list');
        //$programTypes = $this->Section->ProgramType->find('list');
        //$yearLevels = $this->Section->YearLevel->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_id)));

        $collegename = $this->Section->College->field('College.name', array('College.id' => $this->college_id));
        $departmentname = $this->Section->Department->field(
            'Department.name',
            array('Department.id' => $this->department_id)
        );
        $isbeforesearch = 1;
        $this->set(
            compact(/* 'programs', 'programTypes', 'yearLevels', */ 'isbeforesearch', 'collegename', 'departmentname')
        );

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            debug($this->request->data);
            $this->__init_clear_session_filters();
            $this->__init_search_sections();
            debug($this->request->data);

            $isbeforesearch = 0;
            $selected_program = $this->request->data['Section']['program_id'];
            $selected_program_type = $this->request->data['Section']['program_type_id'];
            //$program_type_id = $selected_program_type;

            $program_type_id = $this->Section->getEquivalentProgramTypes($selected_program_type);
            //debug($program_type_id);

            if (isset($this->request->data['Section']['academicyear']) && !empty($this->request->data['Section']['academicyear'])) {
                $thisacademicyear = $this->request->data['Section']['academicyear'];
            } else {
                $thisacademicyear = $this->request->data['Section']['academicyear'] = $this->AcademicYear->current_academicyear(
                );
                $this->__init_clear_session_filters();
                $this->__init_search_sections();
            }

            ////To display each section current hosted students:
            //Search using by department and year level as well if user role is not college (use role is department)
            $selected_year_level = null;

            if (ROLE_COLLEGE != $this->role_id) {
                $selected_year_level = $this->request->data['Section']['year_level_id'];
                if (empty($selected_year_level)) {
                    $selected_year_level = '%';
                }

                $conditions = array(
                    'Section.department_id' => $this->department_id,
                    'Section.archive' => 0,
                    'Section.program_id' => $selected_program,
                    'Section.program_type_id' => $program_type_id,
                    'Section.year_level_id' => $selected_year_level,
                    'Section.academicyear' => $this->request->data['Section']['academicyear']
                );

                //Get Student in there sections
                //$this->college_id will be null
                $studentsections = $this->Section->studentsection(
                    $this->college_id,
                    $this->role_id,
                    $this->department_id,
                    $selected_program,
                    $program_type_id,
                    //$this->request->data['Section']['academicyear'],
                    $thisacademicyear,
                    $selected_year_level
                );
            } else {
                $conditions = array(
                    'Section.college_id' => $this->college_id,
                    'Section.archive' => 0,
                    'Section.program_id' => $selected_program,
                    'Section.program_type_id' => $program_type_id,
                    'Section.academicyear' => $thisacademicyear, //$this->request->data['Section']['academicyear'],
                    "OR" => array(
                        "Section.department_id is null",
                        "Section.department_id = ''"
                    )
                );

                //Get Student in there sections
                $studentsections = $this->Section->studentsection(
                    $this->college_id,
                    $this->role_id,
                    $this->department_id,
                    $selected_program,
                    $program_type_id,
                    $thisacademicyear,
                    $selected_year_level
                );
            }

            $sections = $this->Section->find('all', array(
                'conditions' => $conditions,
                'fields' => array(
                    'Section.id',
                    'Section.name',
                    'Section.year_level_id',
                    'Section.program_id',
                    'Section.program_type_id',
                    'Section.academicyear',
                    'Section.department_id',
                    'Section.college_id'
                ),
                'contain' => array(
                    'Student' => array(
                        'fields' => array(
                            'Student.id',
                            'Student.studentnumber',
                            'Student.full_name',
                            'Student.gender',
                            'Student.graduated',
                            'Student.academicyear'
                        ),
                        'order' => array(
                            'Student.academicyear' => 'DESC',
                            'Student.studentnumber' => 'ASC',
                            'Student.id' => 'ASC',
                            'Student.full_name' => 'ASC'
                        ),
                    ),
                    'StudentsSection',
                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                    'Department' => array(
                        'fields' => array('id', 'name', 'type', 'college_id'),
                        'College' => array('id', 'name', 'type', 'campus_id', 'stream'),
                    ),
                    'College' => array('id', 'name', 'type', 'campus_id', 'stream'),
                    'YearLevel' => array('id', 'name'),
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name'),
                )
            ));

            //debug($sections);

            debug($this->Section->updateSectionCurriculumIDFromPublishedCoursesOfTheSection());

            if (!empty($sections)) {
                foreach ($sections as $key => $section) {
                    if (isset($section['Student']) && count($section['Student']) > 0) {
                        //debug($section['Section']['id']);
                        $this->Section->remove_duplicate_student_sections($section['Section']['id']);
                        $this->Section->updateSectionCurriculumIDFromPublishedCoursesOfTheSection(
                            $section['Section']['id']
                        );
                        //$this->Section->updateSectionCurriculumIDFromPublishedCoursesOfTheSection();

                    }
                }
            }

            $current_sections_occupation = $this->Section->currentsectionsoccupation($sections);
            //Find section curriculum for one of section students
            $sections_curriculum_name = $this->Section->sectionscurriculum($studentsections);

            $this->set(
                compact(
                    'studentsections',
                    'collegename',
                    'departmentname',
                    'current_sections_occupation',
                    'sections_curriculum_name',
                    'sections',
                    'isbeforesearch'
                )
            );
        }


        $swapOptions = array(
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'studentnumber' => 'Student ID'
        );
        $this->set(compact('swapOptions'));
    }

    public function merge_sections()
    {

        $programs = $this->Section->Program->find('list');
        $programTypes = $this->Section->ProgramType->find('list');
        $yearLevels = $this->Section->YearLevel->find('list', array(
            'conditions' =>
                array('YearLevel.department_id' => $this->department_id)
        ));
        $isbeforesearch = 1;
        $this->set(compact('programs', 'programTypes', 'isbeforesearch', 'yearLevels'));
        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $isbeforesearch = 0;
            $selected_program = $this->request->data['Section']['program_id'];
            $selected_program_type = $this->request->data['Section']['program_type_id'];
            $selected_academic_year = $this->request->data['Section']['academicyear'];

            $selected_year_level = $this->request->data['Section']['year_level_id'];

            $program_type_id = $this->Section->getEquivalentProgramTypes($selected_program_type);
            debug($program_type_id);

            /* $find_the_equvilaent_program_type = unserialize($this->Section->ProgramType->field('ProgramType.equivalent_to_id', array(
				'ProgramType.id' => $selected_program_type
			)));
			if (!empty($find_the_equvilaent_program_type)) {
				$selected_program_type_array = array();
				$selected_program_type_array[] = $selected_program_type;
				$program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
			} */
            //Search using by department and year level as well if user role is not college (use role is department)
            if (ROLE_COLLEGE != $this->role_id) {
                $selected_year_level = $this->request->data['Section']['year_level_id'];
                $conditions = array(
                    'Section.college_id' => $this->college_id,
                    'Section.department_id' => $this->department_id,
                    'Section.program_id' => $selected_program,
                    'Section.academicyear' => $selected_academic_year,
                    'Section.program_type_id' => $program_type_id,
                    'Section.year_level_id' => $selected_year_level,
                    'Section.archive' => 0
                );
            } else {
                $conditions = array(
                    'Section.college_id' => $this->college_id,
                    'Section.program_id' => $selected_program,
                    'Section.program_type_id' => $program_type_id,
                    'Section.academicyear' => $selected_academic_year,
                    'Section.archive' => 0,
                    "OR" => array("Section.department_id is null", "Section.department_id" => array(0, ''))
                );
            }
            $sections = $this->Section->find('all', array(
                'conditions' => $conditions,
                'fields' => array('Section.id', 'Section.name'),
                'contain' => array(
                    'Student' => array(
                        'fields' => array(
                            'Student.id',
                            'Student.full_name',
                            'Student.studentnumber'
                        )
                    )
                )
            ));
            $current_sections_occupation = $this->Section->currentsectionsoccupation($sections);
            //to display curriculum
            //TODO: repetative query,remove it during optimization period, section curriculum
            // can be find from the above query by containing curriculum
            $studentsections = $this->Section->studentsection(
                $this->college_id,
                $this->role_id,
                $this->department_id,
                $selected_program,
                $program_type_id,
                $selected_academic_year,
                $selected_year_level
            );

            //Find section curriculum for one of section students
            $sections_curriculum_name = $this->Section->sectionscurriculum($studentsections);


            $this->set(
                compact('sections', 'isbeforesearch', 'current_sections_occupation', 'sections_curriculum_name')
            );
        }
        if (!empty($this->request->data) && isset($this->request->data['merge'])) {
            $count_selected_sections = 0;
            $selected_sections = $this->request->data['Section']['Sections'];
            if (!empty($this->request->data['Section']['academicyear'])) {
                $current_academic_year = $this->request->data['Section']['academicyear'];
            } else {
                $current_academic_year = $this->AcademicYear->current_academicyear();
            }
            $merged_section_ids = $this->Section->mergedSectionIds($this->request->data);
            $new_section_id = $this->request->data['Section'][$selected_sections[0]]['id'];
            $count_selected_sections = count($selected_sections);
            //check whether 2 or more sections are selected
            if ($count_selected_sections >= 2) {
                //Is merging possible for the selected section
                $isMergingPossible = $this->Section->PublishedCourse->CourseRegistration->checkMergingIsPossible(
                    $new_section_id,
                    $merged_section_ids,
                    $current_academic_year
                );


                if (is_array($isMergingPossible)) {
                    //check whether sections have the same curriculum or not
                    if ($this->Section->isSectionsHaveTheSameCurriculum($this->request->data)) {
                        $mergedSection = array();
                        $new_section_id = $this->request->data['Section'][$selected_sections[0]]['id'];
                        $merged_section_id = $this->Section->mergedSectionIds($this->request->data);
                        $new_section_name = $this->Section->field('Section.name', array('Section.id' => $new_section_id)
                        );
                        $mergedSection['Section']['id'] = $new_section_id;
                        $mergedSection['Section']['name'] = $new_section_name;
                        $mergedSection['Section']['college_id'] = $this->college_id;
                        $mergedSection['Section']['program_id'] = $this->request->data['Section']['program_id'];
                        $mergedSection['Section']['program_type_id'] = $this->request->data['Section']['program_type_id'];
                        $mergedSection['Section']['academicyear'] = $current_academic_year;
                        if (ROLE_COLLEGE != $this->role_id) {
                            $mergedSection['Section']['department_id'] = $this->department_id;
                            $mergedSection['Section']['year_level_id'] = $this->request->data['Section']['year_level_id'];
                        }
                        // TODO: we need to implement transaction  roll back in here ?????
                        $studentListsNotInMergedSec = array();
                        if (1) {
                            $mergedSectionsName = null;
                            $deleteArchiveSec['Delete'] = array();
                            $deleteArchiveSec['Archive'] = array();
                            foreach ($this->request->data['Section']['Sections'] as $k => $v) {
                                if ($this->request->data['Section'][$v]['id'] != $new_section_id) {
                                    if ($this->Section->CourseRegistration->ExamGrade->isEverGradeSubmitInTheNameOfSection(
                                        $this->request->data['Section'][$v]['id']
                                    )) {
                                        $deleteArchiveSec['Archive'][] = $this->request->data['Section'][$v]['id'];
                                    } else {
                                        $deleteArchiveSec['Delete'][] = $this->request->data['Section'][$v]['id'];
                                    }
                                }
                            }
                            $mergedSection_id = $new_section_id;

                            // update course registration, published course, and course instructor
                            // assignment with the merged section id
                            if (!empty($mergedSection_id)) {
                                $this->Section->PublishedCourse->CourseRegistration->updateCourseRegistrationPublishedCourseInstructorAssignmentAfterSectionMerge(
                                    $isMergingPossible,
                                    $mergedSection_id
                                );
                            }
                            //Hard deletion is possible
                            if (!empty($deleteArchiveSec['Delete'])) {
                                //Dont delete merged section

                                $studentListsNotInMergedSec = ClassRegistry::init('StudentsSection')->find(
                                    'all',
                                    array('conditions' => array('StudentsSection.section_id' => $deleteArchiveSec['Delete']))
                                );
                                //delete All students in that section and section itself
                                ClassRegistry::init('StudentsSection')->deleteAll(
                                    array('StudentsSection.section_id' => $deleteArchiveSec['Delete']),
                                    false
                                );
                                $this->Section->deleteAll(array('Section.id' => $deleteArchiveSec['Delete']), false);
                            }
                            //soft deletion is possible
                            if (!empty($deleteArchiveSec['Archive'])) {
                                //Dont delete merged section

                                $studentListsNotInMergedSec = array_merge(
                                    $studentListsNotInMergedSec,
                                    ClassRegistry::init('StudentsSection')->find(
                                        'all',
                                        array('conditions' => array('StudentsSection.section_id' => $deleteArchiveSec['Archive']))
                                    )
                                );
                                //archive All students in that section and section itself
                                ClassRegistry::init('StudentsSection')->updateAll(array('StudentsSection.archive' => 1),
                                    array('StudentsSection.section_id' => $deleteArchiveSec['Archive']));
                                $this->Section->updateAll(array('Section.archive' => 1),
                                    array('Section.id' => $deleteArchiveSec['Archive']));
                            }
                            $saveToNesSection = false;
                            foreach ($studentListsNotInMergedSec as $sLM => $sLV) {
                                $studentsSections = array();
                                $studentsSections['StudentsSection']['student_id'] = $sLV['StudentsSection']['student_id'];
                                $studentsSections['StudentsSection']['section_id'] = $mergedSection_id;
                                if (!empty($studentsSections)) {
                                    ClassRegistry::init('StudentsSection')->create();
                                    if (ClassRegistry::init('StudentsSection')->save(
                                        $studentsSections['StudentsSection']
                                    )) {
                                        $saveToNesSection = true;
                                    }
                                }
                            }
                            if ($saveToNesSection) {
                                $this->Session->setFlash(
                                    __(
                                        '<span></span> Sections ' . $mergedSectionsName . ' are merged into section ' .
                                        $mergedSection['Section']['name'],
                                        true
                                    ),
                                    'default',
                                    array('class' => 'success-box success-message')
                                );
                                return $this->redirect(array('action' => 'display_sections', $mergedSection_id));
                            } else {
                                $this->Session->setFlash(
                                    __('<span></span> Sections is not merged , please try again ', true),
                                    'default',
                                    array('class' => 'success-box success-message')
                                );
                            }
                        } else {
                            // roll back
                        }
                    } else {
                        $this->Session->setFlash(
                            __(
                                '<span></span> In order to merge sections,
					all selected section students must have
						the same curriculum. Please select sections that have the
						same students curriculum or empty section.',
                                true
                            ),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                } else {
                    if ($isMergingPossible == 2) {
                        $this->Session->setFlash(
                            __(
                                '<span></span> Merging of the selected
			            section is not possible because year level upgrade has been
			            performed and courses published for the sections.',
                                true
                            ),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    } else {
                        if ($isMergingPossible == 3) {
                            $this->Session->setFlash(
                                __(
                                    '<span></span> Merging of the selected
			            section is not possible because courses taken by the
			            selected section is not similar.',
                                    true
                                ),
                                'default',
                                array('class' => 'error-box error-message')
                            );
                        } else {
                            if ($isMergingPossible == 4) {
                                $this->Session->setFlash(
                                    __(
                                        '<span></span> Merging of the selected
			            section is not possible because grade has been submitted to one
			            of the published courses of the ' . $this->request->data['Section']['academicyear'] . '
			            academic year .',
                                        true
                                    ),
                                    'default',
                                    array('class' => 'error-box error-message')
                                );
                            }
                        }
                    }
                }
            } else {
                $this->Session->setFlash(
                    __('<span></span> At least select 2 sections to merge '),
                    'default',
                    array('class' => 'error-box error-message')
                );
                $this->set('selected_program', $this->request->data['Section']['program_id']);
                $this->set('selected_program_type', $this->request->data['Section']['program_type_id']);
            }

            $this->request->data['search'] = true;
            $isbeforesearch = 0;
            $selected_program = $this->request->data['Section']['program_id'];
            $selected_program_type = $this->request->data['Section']['program_type_id'];

            $selected_academic_year = $this->request->data['Section']['academicyear'];

            $selected_year_level = $this->request->data['Section']['year_level_id'];


            $program_type_id = $selected_program_type;
            $find_the_equvilaent_program_type = unserialize(
                $this->Section->ProgramType->field('ProgramType.equivalent_to_id', array(
                    'ProgramType.id' => $selected_program_type
                ))
            );

            if (!empty($find_the_equvilaent_program_type)) {
                $selected_program_type_array = array();
                $selected_program_type_array[] = $selected_program_type;
                $program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
            }
            //Search using by department and year level as well if user role is not college (use role is department)
            if (ROLE_COLLEGE != $this->role_id) {
                $selected_year_level = $this->request->data['Section']['year_level_id'];
                $conditions = array(
                    'Section.college_id' => $this->college_id,
                    'Section.department_id' =>
                        $this->department_id,
                    'Section.program_id' => $selected_program,
                    'Section.academicyear' => $selected_academic_year,
                    'Section.program_type_id' => $program_type_id,
                    'Section.year_level_id' => $selected_year_level,
                    'Section.archive' => 0
                );
            } else {
                $conditions = array(
                    'Section.college_id' => $this->college_id,
                    'Section.program_id' =>
                        $selected_program,
                    'Section.program_type_id' => $program_type_id,
                    'Section.academicyear' => $selected_academic_year,
                    'Section.archive' => 0,
                    "OR" => array("Section.department_id is null", "Section.department_id" => array(0, ''))
                );
            }

            $sections = $this->Section->find('all', array(
                'conditions' => $conditions,
                'fields' => array('Section.id', 'Section.name'),
                'contain' => array(
                    'Student' => array(
                        'fields' => array(
                            'Student.id',
                            'Student.full_name',
                            'Student.studentnumber'
                        )
                    )
                )
            ));
            $current_sections_occupation = $this->Section->currentsectionsoccupation($sections);
            //to display curriculum
            //TODO: repetative query,remove it during optimization period, section curriculum
            // can be find from the above query by containing curriculum
            $studentsections = $this->Section->studentsection(
                $this->college_id,
                $this->role_id,
                $this->department_id,
                $selected_program,
                $program_type_id,
                $selected_academic_year,
                $selected_year_level
            );

            //Find section curriculum for one of section students
            $sections_curriculum_name = $this->Section->sectionscurriculum($studentsections);

            $yearLevels = $this->Section->YearLevel->find('list', array(
                'conditions' =>
                    array('YearLevel.department_id' => $this->department_id)
            ));
            $this->set(
                compact(
                    'sections',
                    'isbeforesearch',
                    'current_sections_occupation',
                    'yearLevels',
                    'sections_curriculum_name'
                )
            );
        }
    }

    public function split_section()
    {

        $programs = $this->Section->Program->find('list');
        $programTypes = $this->Section->ProgramType->find('list');
        $yearLevels = $this->Section->YearLevel->find(
            'list',
            array('conditions' => array('YearLevel.department_id' => $this->department_id))
        );
        $isbeforesearch = 1;

        $this->set(compact('programs', 'programTypes', 'isbeforesearch', 'yearLevels'));

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $isbeforesearch = 0;
            $selected_program = $this->request->data['Section']['program_id'];
            $selected_program_type = $this->request->data['Section']['program_type_id'];
            $selected_academic_year = $this->request->data['Section']['academicyear'];
            $selected_year_level = $this->request->data['Section']['year_level_id'];

            $this->Session->write('selected_program', $selected_program);
            $this->Session->write('selected_program_type', $selected_program_type);
            $this->Session->write('selected_academic_year', $selected_academic_year);
            $this->Session->write('selected_year_level', $selected_year_level);

            ////To display each section current hosted students:
            $program_type_id = $selected_program_type;

            $find_the_equvilaent_program_type = unserialize(
                $this->Section->ProgramType->field(
                    'ProgramType.equivalent_to_id',
                    array('ProgramType.id' => $selected_program_type)
                )
            );

            if (!empty($find_the_equvilaent_program_type)) {
                $selected_program_type_array = array();
                $selected_program_type_array[] = $selected_program_type;
                $program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
            }

            //Search using by department and year level as well if user role is not college (use role is department)

            if (ROLE_COLLEGE != $this->role_id) {
                $selected_year_level = $this->request->data['Section']['year_level_id'];
                $this->Session->write('selected_year_level', $selected_year_level);

                $conditions = array(
                    'Section.college_id' => $this->college_id,
                    'Section.department_id' => $this->department_id,
                    'Section.program_id' => $selected_program,
                    'Section.program_type_id' => $program_type_id,
                    'Section.academicyear' => $selected_academic_year,
                    'Section.year_level_id' => $selected_year_level,
                    'Section.archive' => 0
                );
            } else {
                $conditions = array(
                    'Section.college_id' => $this->college_id,
                    'Section.program_id' => $selected_program,
                    'Section.program_type_id' => $program_type_id,
                    'Section.archive' => 0,
                    'Section.academicyear' => $selected_academic_year,
                    "OR" => array(
                        "Section.department_id is null",
                        "Section.department_id = ''",
                        "Section.department_id = 0"
                    )
                );
            }

            $sections = $this->Section->find('all', array(
                'conditions' => $conditions,
                'fields' => array('Section.id', 'Section.name'),
                'contain' => array(
                    'Student' => array(
                        'fields' => array('Student.id', 'Student.full_name', 'Student.studentnumber', 'Student.gender')
                    )
                )
            ));

            $current_sections_occupation = $this->Section->currentsectionsoccupation($sections);
            $this->Session->write('current_sections_occupation', $current_sections_occupation);
            $this->Session->write('sections', $sections);
            $this->set(compact('sections', 'current_sections_occupation', 'isbeforesearch'));
        }

        if (!empty($this->request->data) && isset($this->request->data['split'])) {
            $is_course_published = 0;
            $selected_section = $this->request->data['Section']['selectedsection'];

            $number_of_section = $this->request->data['Section']['number_of_section'];
            $sections = $this->Session->read('sections');
            debug($sections);

            $current_sections_occupation = $this->Session->read('current_sections_occupation');
            $selected_program = $this->Session->read('selected_program');
            $selected_program_type = $this->Session->read('selected_program_type');
            $selected_academic_year = $this->Session->read('selected_academic_year');


            //
            //////////////////////////////////////////////////////////////////////////

            $selected_section_id = $this->request->data['Section'][$selected_section]['id'];

            if (!empty($selected_academic_year)) {
                $current_academic_year = $selected_academic_year;
            } else {
                $current_academic_year = $this->AcademicYear->current_academicyear();
            }

            $section_semester = ClassRegistry::init('CourseRegistration')->checkCourseIsPublishedForSection(
                $this->request->data['Section'][$selected_section]['id'],
                $current_academic_year
            );


            if ($section_semester != 2) {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $is_course_published = $this->Section->PublishedCourse->find('count', array(
                        'conditions' => array(
                            'PublishedCourse.department_id' => $this->department_id,
                            'PublishedCourse.section_id' => $selected_section_id,
                            'PublishedCourse.semester' => $section_semester,
                            'PublishedCourse.academic_year' => $current_academic_year
                        )
                    ));
                } else {
                    if ($this->role_id == ROLE_COLLEGE) {
                        $is_course_published = $this->Section->PublishedCourse->find('count', array(
                            'conditions' => array(
                                'PublishedCourse.college_id' => $this->college_id,
                                'PublishedCourse.section_id' => $selected_section_id,
                                'PublishedCourse.semester' => $section_semester,
                                'PublishedCourse.academic_year' => $current_academic_year,
                                'PublishedCourse.department_id is null'
                            )
                        ));
                    }
                }
            }


            if ($selected_section == -1) {
                $is_course_published = 0;
            }

            //////////////////////////////////////////////////////////////

            if (!$is_course_published) {
                if (($selected_section != -1) && $current_sections_occupation[$selected_section] >= $number_of_section) {
                    //find selected section id and name
                    $selected_section_id = $sections[$selected_section]['Section']['id'];
                    $selected_section_name = $sections[$selected_section]['Section']['name'];
                    debug($selected_section_name);
                    //retrieve parts of selected section name
                    $variable_selected_sectionname = substr(
                        $selected_section_name,
                        strrpos($selected_section_name, " ") + 1
                    );
                    debug($variable_selected_sectionname);

                    if (ROLE_COLLEGE != $this->role_id) {
                        $first_space = strpos($selected_section_name, " ");
                        $second_space = strrpos($selected_section_name, " ");
                        $prefix_selected_sectionname = substr($selected_section_name, 0, $first_space);
                        $fixed_selected_sectionname = substr(
                            $selected_section_name,
                            ($first_space + 1),
                            ($second_space - ($first_space + 1))
                        );
                        //$name = $prefixsectionname.$yearlevelnameshort.' '.$fixedsectionname.' '.$variablesectionname;
                    } else {
                        $first_space = strpos($selected_section_name, " ");
                        $fixed_selected_sectionname = substr($selected_section_name, 0, $first_space);
                        // $name = $fixedsectionname.' '.$variablesectionname;
                    }

                    $program_type_id = $selected_program_type;
                    $find_the_equvilaent_program_type = unserialize(
                        $this->Section->ProgramType->field(
                            'ProgramType.equivalent_to_id',
                            array('ProgramType.id' => $selected_program_type)
                        )
                    );

                    if (!empty($find_the_equvilaent_program_type)) {
                        $selected_program_type_array = array();
                        $selected_program_type_array[] = $selected_program_type;
                        $program_type_id = array_merge($selected_program_type_array, $find_the_equvilaent_program_type);
                    }

                    //find all section name in selected program/program type and year level

                    if (ROLE_COLLEGE != $this->role_id) {
                        $selected_year_level = $this->Session->read('selected_year_level');
                        $conditions = array(
                            'Section.college_id' => $this->college_id,
                            'Section.department_id' => $this->department_id,
                            'Section.program_id' => $selected_program,
                            'Section.academicyear' => $current_academic_year,
                            'Section.program_type_id' => $program_type_id,
                            'Section.year_level_id' => $selected_year_level,
                            'Section.archive' => 0
                        );
                    } else {
                        $conditions = array(
                            'Section.college_id' => $this->college_id,
                            'Section.program_id' => $selected_program,
                            'Section.program_type_id' => $program_type_id,
                            'Section.academicyear' => $current_academic_year,
                            'Section.archive' => 0,
                            "OR" => array(
                                "Section.department_id is null",
                                "Section.department_id = ''",
                                "Section.department_id = 0"
                            )
                        );
                    }

                    $all_section_name = $this->Section->find('list', array(
                        'conditions' => $conditions,
                        'fields' => array('Section.name'),
                        'order' => array('Section.name')
                    ));

                    $numeric_section_variablename = array();
                    $character_seection_variablename = array();

                    if (!empty($all_section_name)) {
                        foreach ($all_section_name as $sv) {
                            $variable = substr($sv, strrpos($sv, " ") + 1);
                            if (is_numeric($variable)) {
                                $numeric_section_variablename[] = $variable;
                            } else {
                                $character_seection_variablename[] = $variable;
                            }
                        }
                    }

                    //find the last available variable name from database,construct full variable section name in order to find the gap
                    //in varable section name and find unused section name
                    $last_section_variablename = null;
                    $full_variablename_array = array();
                    $gap_section_name_array = array();

                    if (is_numeric($variable_selected_sectionname)) {
                        $section_variablename_count = count($numeric_section_variablename);
                        $last_section_variablename = $numeric_section_variablename[$section_variablename_count - 1];

                        for ($i = $last_section_variablename; $i >= 1; $i--) {
                            $full_variablename_array[] = $i;
                        }

                        $gap_section_name_array = array_diff($full_variablename_array, $numeric_section_variablename);
                    } else {
                        $section_variablename_count = count($character_seection_variablename);
                        $last_section_variablename = $character_seection_variablename[$section_variablename_count - 1];
                        $last_section_variablename = ord($last_section_variablename);

                        for ($i = $last_section_variablename; $i >= 65; $i--) {
                            $full_variablename_array[] = chr($i);
                        }

                        $last_section_variablename = chr($last_section_variablename);
                        $gap_section_name_array = array_diff(
                            $full_variablename_array,
                            $character_seection_variablename
                        );
                    }

                    sort($gap_section_name_array);

                    //check if section name is already taken and find unique section
                    $split_section_names_array = array();
                    $i = 0;
                    $j = 1;

                    while ($i < $number_of_section) {
                        //check if the section name is already taken ?
                        $checkIfSectionNameTaken = $this->Section->find(
                            'count',
                            array('conditions' => array('Section.name' => $selected_section_name . ' ' . $j))
                        );
                        if ($checkIfSectionNameTaken == 0) {
                            $split_section_names_array[$i] = $selected_section_name . ' ' . $j;
                            $i++;
                        }
                        $j++;
                    }

                    //Create newly merged Section and save to Section table database
                    $SplitedSection = array();
                    $SplitedSection['Section']['college_id'] = $this->college_id;
                    //$selectedsection_id = $this->request->data['Section'][$this->request->data['Section']['selectedsection']]['id'];
                    $SplitedSection['Section']['program_id'] = $selected_program;
                    $SplitedSection['Section']['program_type_id'] = $selected_program_type;
                    $SplitedSection['Section']['academicyear'] = $current_academic_year;

                    if (ROLE_COLLEGE != $this->role_id) {
                        $SplitedSection['Section']['department_id'] = $this->department_id;
                        $SplitedSection['Section']['year_level_id'] = $selected_year_level;
                    }

                    //Check split section status whether grade submitted on the name of section or not
                    // for soft delete (archive) or hard Delete
                    //before delete hold names for display
                    $deleteOk = true;
                    $secSave = false;

                    if ($this->Section->CourseRegistration->ExamGrade->isEverGradeSubmitInTheNameOfSection(
                        $selected_section_id
                    )) {
                        $deleteOk = false;
                        $this->Section->id = $selected_section_id;
                        $this->Section->saveField('archive', '1');
                        //archive students of the section in associate table
                        $studentssections = $this->Section->StudentsSection->find('all', array(
                            'conditions' => array(
                                'StudentsSection.section_id' => $selected_section_id,
                                'StudentsSection.archive' => 0
                            )
                        ));
                        /*
						foreach ($studentssections as $ssk => $ssv) {
							$this->Section->StudentsSection->id = $ssv['StudentsSection']['id'];
							$this->Section->StudentsSection->saveField('archive', '1');
						}
						*/
                    } else {
                    }

                    $Split_Section_id_array = array();

                    for ($i = 0; $i < $number_of_section; $i++) {
                        $SplitedSection['Section']['name'] = $split_section_names_array[$i];
                        if (!empty($SplitedSection)) {
                            $this->Section->create();
                            if ($this->Section->save($SplitedSection)) {
                                $Split_Section_id_array[$i] = $this->Section->id;
                                $secSave = true;
                            } else {
                                $error = $this->Section->invalidFields();
                                debug($error);
                            }
                        }
                    }

                    if (!empty($Split_Section_id_array) && $secSave) {
                        //newly created children section students is saved in associated database table
                        $studentssections = ClassRegistry::init('StudentsSection')->find('all', array(
                            'conditions' => array(
                                'StudentsSection.section_id' => $selected_section_id,
                                'StudentsSection.archive' => 0
                            )
                        ));

                        debug($studentssections);
                        $k = 0; //first child section index

                        if (!empty($studentssections)) {
                            foreach ($studentssections as $ssk => $ssv) {
                                $studentsSections = array();

                                if (isset($Split_Section_id_array[$k]) && !empty($Split_Section_id_array[$k]) && isset($ssv['StudentsSection']['student_id']) && !empty($ssv['StudentsSection']['student_id'])) {
                                    $studentsSections['StudentsSection']['student_id'] = $ssv['StudentsSection']['student_id'];
                                    $studentsSections['StudentsSection']['section_id'] = $Split_Section_id_array[$k];
                                    debug($studentsSections);

                                    if (!empty($studentsSections['StudentsSection'])) {
                                        $this->Section->StudentsSection->create();
                                        $this->Section->StudentsSection->save($studentsSections['StudentsSection']);
                                    }
                                }

                                $k = $k + 1;
                                if (($k % $number_of_section) == 0) {
                                    $k = 0;
                                }
                            }
                        }
                    }

                    //delete historical section
                    if ($deleteOk && $secSave) {
                        //delete students of the section in associate table

                        $this->Section->delete($selected_section_id);

                        $studentssections = $this->Section->StudentsSection->find('all', array(
                            'conditions' => array(
                                'StudentsSection.section_id' => $selected_section_id,
                                'StudentsSection.archive' => 0
                            )
                        ));

                        if (!empty($studentssections)) {
                            foreach ($studentssections as $ssk => $ssv) {
                                $this->Section->StudentsSection->delete($ssv['StudentsSection']['id']);
                            }
                        }
                    }

                    if ($secSave) {
                        $split_sections = null;

                        if (!empty($split_section_names_array)) {
                            foreach ($split_section_names_array as $split_section_names) {
                                $split_sections = $split_sections . $split_section_names . ', ';
                            }
                        }
                        $this->Flash->success(
                            'Section ' . $selected_section_name . ' is split into sections ' . $split_sections . ' successfully.'
                        );
                    } else {
                        $this->Flash->error(
                            'Section ' . $selected_section_name . ' not able split into sections, please try again.'
                        );
                    }
                    //	return $this->redirect(array('action' => 'display_sections',$selected_section_id));

                } else {
                    $this->Flash->error(
                        'Please select section which have students greater than or equal to the number of section to split which is: ' . $number_of_section . '.'
                    );
                }
            } else {
                $this->Flash->error(
                    'You can not split the selected section since course has been published for ' . $section_semester['semester'] . '/' . $current_academic_year . '. First unpublish the courses and split the section.'
                );
                return $this->redirect(array('controller' => 'published_courses', 'action' => 'unpublish'));
            }

            $this->request->data['search'] = true;
            $isbeforesearch = 0;
            $this->set(compact('sections', 'current_sections_occupation', 'isbeforesearch'));
        }
    }

    public function export($sectionid = null)
    {

        $students_per_section = $this->Section->studentsSectionById($sectionid);
        //debug($students_per_section);

        $this->set(compact('students_per_section'));
    }

    public function view_pdf($id = null)
    {

        if (!$id) {
            $this->Flash->error('Sorry, Invalid request.');
            $this->redirect(array('action' => 'index'), null, true);
        }

        $colleges = $this->Section->College->find('list', array(
            'conditions' => array(
                'College.id' => $this->college_id,
                'College.active' => 1
            )
        ));
        $collegename = $colleges[$this->college_id];

        if (!empty($this->department_id)) {
            $departments = $this->Section->Department->find('list', array(
                'conditions' => array(
                    'Department.id' => $this->department_id,
                    'Department.active' => 1
                )
            ));

            $departmentname = $departments[$this->department_id];
        }

        $studentsections = $this->Section->studentsSectionById($id);

        $this->set(compact('studentsections', 'collegename', 'departmentname'));
        $this->response->type('application/pdf');
        $this->layout = '/pdf/default';
        $this->render();
    }

    public function deleteStudentforThisSection($section_id = null, $student_number = null)
    {

        if (!$section_id || !$student_number) {
            $this->Flash->error('Invalid ID for Section or/and Student');
            return $this->redirect(array('action' => 'display_sections'));
        }

        $student_number = str_replace('-', '/', $student_number);
        $section_name = $this->Section->field('Section.name', array('Section.id' => $section_id));

        $student_id = $this->Section->Student->field('Student.id', array('Student.studentnumber' => $student_number));

        //now delete this student from this section from associate table check student deletion from section is possible ?

        if ($this->Section->Student->CourseRegistration->ExamResult->isStudenSectionChangePossible(
            $student_id,
            $section_id
        )) {
            $this->Section->StudentsSection->id = $this->Section->StudentsSection->field('StudentsSection.id', array(
                'StudentsSection.student_id' => $student_id,
                'StudentsSection.section_id' => $section_id,
                'StudentsSection.archive' => 0
            ));
            $this->Section->StudentsSection->delete($this->Section->StudentsSection->id);
        } else {
            if ($this->Section->Student->CourseRegistration->ExamResult->isRegistredInNameOfSectionAndSubmittedGrade(
                $student_id,
                $section_id
            )) {
                $this->Flash->error(
                    $student_number . ' can not be deleted from  ' . $section_name . ' section since one or more course registered by the student in this section, his/her grade not full submitted.'
                );
                $this->redirect(array('action' => 'display_sections', $section_id));
            } else {
                $this->Section->StudentsSection->id = $this->Section->StudentsSection->field(
                    'StudentsSection.id',
                    array(
                        'StudentsSection.student_id' => $student_id,
                        'StudentsSection.section_id' => $section_id,
                        'StudentsSection.archive' => 0
                    )
                );
                $this->Section->StudentsSection->saveField('archive', '1');
            }
        }
        //$this->Section->habtmDelete('Student', $section_id,$student_id);
        //find section name from id for the purpose of display

        $this->Flash->success($student_number . ' is now deleted from ' . $section_name . ' Section');
        return $this->redirect(array('action' => 'display_sections', $section_id));
    }

    public function archieveUnarchieveStudentSection($section_id = null, $student_id = null, $archieve = null)
    {

        if (!$section_id || !$student_id) {
            $this->Flash->error('Invalid ID for Section or/and Student');
            //return $this->redirect(array('action' => 'display_sections'));
            $this->redirect($this->referer());
        }

        $section_name = $this->Section->field('Section.name', array('Section.id' => $section_id));
        $student_number = $this->Section->Student->field('Student.studentnumber', array('Student.id' => $student_id));

        if ($archieve) {
            if ($this->Section->Student->CourseRegistration->ExamResult->isStudenSectionChangePossible(
                $student_id,
                $section_id
            )) {
                $this->Section->StudentsSection->id = $this->Section->StudentsSection->field(
                    'StudentsSection.id',
                    array(
                        'StudentsSection.student_id' => $student_id,
                        'StudentsSection.section_id' => $section_id,
                        'StudentsSection.archive' => 0
                    )
                );

                if ($this->Section->StudentsSection->id) {
                    $this->Section->StudentsSection->saveField('archive', '1');
                    $this->Flash->success($student_number . ' is now archieved form ' . $section_name . ' Section');
                } else {
                    $this->Flash->error('Could\'t archieve ' . $student_number . ' form ' . $section_name . ' Section');
                }
            } else {
                /* if ($this->Section->Student->CourseRegistration->ExamResult->isRegistredInNameOfSectionAndSubmittedGrade($student_id, $section_id)) {
					$this->Flash->error($student_number . ' can not be archieved from Section ' . $section_name . ' at this time. One or more courses are registered by the student in this section and grades are not fully submitted. Try again when all of the grades are submitted!');
					//$this->redirect(array('action' => 'display_sections', $section_id));
					$this->redirect($this->referer());
				} else { */

                if ($this->Section->chceck_all_registered_added_courses_are_graded(
                    $student_id,
                    $section_id,
                    $check_for_invalid_grades = 0
                )) {
                    $this->Section->StudentsSection->id = $this->Section->StudentsSection->field(
                        'StudentsSection.id',
                        array(
                            'StudentsSection.student_id' => $student_id,
                            'StudentsSection.section_id' => $section_id,
                            'StudentsSection.archive' => 0
                        )
                    );

                    if ($this->Section->StudentsSection->id) {
                        $this->Section->StudentsSection->saveField('archive', '1');
                        $this->Flash->success($student_number . ' is now archieved form ' . $section_name . ' Section');
                    } else {
                        $this->Flash->error(
                            'Could\'t archieve ' . $student_number . ' form ' . $section_name . ' Section'
                        );
                    }
                } else {
                    $this->Flash->error(
                        $student_number . ' can not be archieved from Section ' . $section_name . ' at this time. One or more courses are registered by the student in this section and grades are not fully submitted. Try again when all of the grades are submitted!'
                    );
                    //$this->redirect(array('action' => 'display_sections', $section_id));
                    //$this->redirect($this->referer());
                }
            }
        } else {
            $this->Section->StudentsSection->id = $this->Section->StudentsSection->field('StudentsSection.id', array(
                'StudentsSection.student_id' => $student_id,
                'StudentsSection.section_id' => $section_id,
                'StudentsSection.archive' => 1
            ));

            if ($this->Section->StudentsSection->id) {
                $this->Section->StudentsSection->saveField('archive', '0');
                $this->Flash->success($student_number . ' is now unarchieved for ' . $section_name . ' Section');
            } else {
                $this->Flash->error('Could\'t unarchieve ' . $student_number . ' for ' . $section_name . ' Section');
            }
        }

        $refererUrl = explode('/', $this->referer());
        //debug($refererUrl);

        if (is_array($refererUrl) && !empty($refererUrl) && in_array('student_academic_profile', $refererUrl)) {
            return $this->redirect(
                array('controller' => 'students', 'action' => 'student_academic_profile', $student_id)
            );
        } else {
            if (is_array($refererUrl) && !empty($refererUrl) && in_array('display_sections', $refererUrl)) {
                return $this->redirect(array('action' => 'display_sections', $section_id));
            } else {
                $this->redirect($this->referer());
            }
        }

        //return $this->redirect(array('action' => 'display_sections', $section_id));
        //$this->redirect($this->referer());
    }


    public function deleteStudent($section_id = null, $student_numberr = null)
    {

        $student_number = str_replace('-', '/', $student_numberr);
        $student_id = $this->Section->Student->field('Student.id', array('Student.studentnumber' => $student_number));

        if (!$section_id || !$student_number) {
            $this->Flash->error('Invalid id for section or/and student.');
            return $this->redirect(array('action' => 'display_sections'));
        }

        $section_name = $this->Section->field('Section.name', array('Section.id' => $section_id));
        //now delete this student from this section from associate table .  check student deletion from section is possible ?

        if ($this->Section->Student->CourseRegistration->ExamResult->isStudenSectionChangePossible(
            $student_id,
            $section_id
        )) {
            $this->Section->StudentsSection->id = $this->Section->StudentsSection->field(
                'StudentsSection.id',
                array('StudentsSection.student_id' => $student_id, 'StudentsSection.section_id' => $section_id)
            );
            $this->Section->StudentsSection->delete($this->Section->StudentsSection->id);
            $this->Flash->success($student_number . ' is deleted from ' . $section_name . ' section.');
            return $this->redirect(
                array('controller' => 'students', 'action' => 'student_academic_profile', $student_id)
            );
        } else {
            if ($this->Section->Student->CourseRegistration->ExamResult->isRegistredInNameOfSectionAndSubmittedGrade(
                $student_id,
                $section_id
            )) {
                $this->Flash->error(
                    $student_number . ' can not be deleted from ' . $section_name . ' section since one or more course registered by the student in this section, his/her grade full submitted.'
                );
                $this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $student_id));
            } else {
                $this->Section->StudentsSection->id = $this->Section->StudentsSection->field(
                    'StudentsSection.id',
                    array('StudentsSection.student_id' => $student_id, 'StudentsSection.section_id' => $section_id)
                );
                $this->Section->StudentsSection->delete($this->Section->StudentsSection->id);
                $this->Flash->success($student_number . ' is deleted from ' . $section_name . ' section.');
                return $this->redirect(
                    array('controller' => 'students', 'action' => 'student_academic_profile', $student_id)
                );
            }
        }

        $this->Flash->error($student_number . ' can not be deleted from ' . $section_name . ' section.');
        $this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $student_id));
    }

    public function move($student_number = null, $previous_section_id = null)
    {

        $this->layout = 'ajax';
        $student_number = str_replace('-', '/', $student_number);

        $student_id = $this->Section->Student->field('Student.id', array('Student.studentnumber' => $student_number));
        //find all participating sections and pass as an array
        //Search using by department and year level as well if user role is not college (use role is department)


        $prevSectionsDetail = $this->Section->find('first', array(
            'conditions' => array(
                'Section.id' => $previous_section_id
            ),
            'contain' => array(
                'YearLevel' => array('id', 'name')
            ),
            'recursive' => 1
        ));

        if ($this->role_id == ROLE_DEPARTMENT) {
            $next_year_level_exists = $this->Section->YearLevel->find('first', array(
                'conditions' => array(
                    'YearLevel.department_id' => $this->department_id,
                    'YearLevel.id >' => $prevSectionsDetail['YearLevel']['id']
                ),
                'order' => array('YearLevel.id' => 'ASC'),
                'limit' => 1,
                'recursive' => -1
            ));

            //debug($next_year_level_exists['YearLevel']['id']);

            $next_year_level = array();

            if (isset($next_year_level_exists['YearLevel']['id']) && !empty($next_year_level_exists['YearLevel']['id'])) {
                $next_year_level = $this->Section->find('first', array(
                    'conditions' => array(
                        'Section.program_id' => $prevSectionsDetail['Section']['program_id'],
                        'Section.program_type_id' => $prevSectionsDetail['Section']['program_type_id'],
                        'Section.department_id' => $this->department_id,
                        'Section.year_level_id' => $next_year_level_exists['YearLevel']['id'],
                        'Section.academicyear <>' => $prevSectionsDetail['Section']['academicyear'],
                        'Section.id >' => $prevSectionsDetail['Section']['id'],
                        'Section.archive' => 0,
                    ),
                    'contain' => array(
                        'YearLevel' => array('id', 'name')
                    ),
                    'recursive' => -1
                ));
            }

            if (isset($next_year_level) && !empty($next_year_level['Section']['year_level_id'])) {
                $conditions = array(
                    //'Section.college_id' => $this->college_id,
                    'Section.id <>' => $prevSectionsDetail['Section']['id'],
                    'Section.program_id' => $prevSectionsDetail['Section']['program_id'],
                    'Section.program_type_id' => $prevSectionsDetail['Section']['program_type_id'],
                    'Section.department_id' => $this->department_id,
                    'Section.year_level_id >= ' => $prevSectionsDetail['YearLevel']['id'],
                    'Section.year_level_id <= ' => $next_year_level['YearLevel']['id'],
                    'OR' => array(
                        'Section.academicyear' => $prevSectionsDetail['Section']['academicyear'],
                        'Section.academicyear' => $next_year_level['Section']['academicyear'],
                    ),
                    'OR' => array(
                        'Section.curriculum_id IS NULL',
                        'Section.curriculum_id' => $prevSectionsDetail['Section']['curriculum_id'],
                    ),
                    'Section.archive' => 0
                );
            } else {
                $conditions = array(
                    //'Section.college_id' => $this->college_id,
                    'Section.id <>' => $prevSectionsDetail['Section']['id'],
                    'Section.program_id' => $prevSectionsDetail['Section']['program_id'],
                    'Section.program_type_id' => $prevSectionsDetail['Section']['program_type_id'],
                    'Section.department_id' => $this->department_id,
                    'Section.year_level_id' => $prevSectionsDetail['YearLevel']['id'],
                    'Section.academicyear' => $prevSectionsDetail['Section']['academicyear'],
                    'OR' => array(
                        'Section.curriculum_id IS NULL',
                        'Section.curriculum_id' => $prevSectionsDetail['Section']['curriculum_id'],
                    ),
                    'Section.archive' => 0
                );
            }
        } else {
            $conditions = array(
                'Section.college_id' => $this->college_id,
                'Section.id <>' => $prevSectionsDetail['Section']['id'],
                'Section.program_id' => $prevSectionsDetail['Section']['program_id'],
                'Section.program_type_id' => $prevSectionsDetail['Section']['program_type_id'],
                'Section.academicyear' => $prevSectionsDetail['Section']['academicyear'],
                'Section.department_id is null',
                'Section.archive' => 0,
            );
        }

        $sections_all = $this->Section->find(
            'all',
            array(
                'conditions' => $conditions,
                'order' => array(
                    'Section.academicyear' => 'ASC',
                    'Section.year_level_id' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                ),
                'contain' => array('YearLevel' => array('id', 'name')),
                'recursive' => -1
            )
        );

        $sections = array();

        if (!empty($sections_all)) {
            foreach ($sections_all as $key => $section) {
                //debug($sections);
                $sections[$section['Section']['id']] = $section['Section']['name'] . ' (' . (isset($section['YearLevel']['name']) ? $section['YearLevel']['name'] : ($section['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $section['Section']['academicyear'] . ')';
            }
        }

        //unset($sections[$previous_section_id]);
        $this->set(compact('previous_section_id', 'student_id', 'sections', 'student_number'));
    }

    public function move_selected_student_section($previous_section_id = null)
    {

        $this->layout = 'ajax';

        $studentsections = $this->Section->getAllActiveStudents($previous_section_id);

        $prevSectionsDetail = $this->Section->find('first', array(
            'conditions' => array(
                'Section.id' => $previous_section_id
            ),
            'contain' => array(
                'YearLevel' => array('id', 'name')
            ),
            'recursive' => 1
        ));

        $sectionCreatedDate = new DateTime($prevSectionsDetail['Section']['created']);
        $sectionCreatedDate->modify('-1 month');
        $sectionCreatedDate = $sectionCreatedDate->format('Y-m-d');

        $student_curriculum_id = null;

        if (count($this->Section->getAllActiveStudents($previous_section_id)['Student'])) {
            $student_curriculum_id = $this->Section->getAllActiveStudents(
                $previous_section_id
            )['Student'][0]['curriculum_id'];
        }

        if ($this->role_id == ROLE_DEPARTMENT) {
            $next_year_level_exists = $this->Section->YearLevel->find('first', array(
                'conditions' => array(
                    'YearLevel.department_id' => $this->department_id,
                    'YearLevel.id > ' => $prevSectionsDetail['YearLevel']['id']
                ),
                'order' => array('YearLevel.id' => 'ASC'),
                'limit' => 1,
                'recursive' => -1
            ));

            $next_year_level = array();

            if (ALLOW_STDENT_SECTION_MOVE_TO_NEXT_YEAR_LEVEL && isset($next_year_level_exists['YearLevel']['id']) && !empty($next_year_level_exists['YearLevel']['id'])) {
                $next_year_level = $this->Section->find('first', array(
                    'conditions' => array(
                        'Section.program_id' => $prevSectionsDetail['Section']['program_id'],
                        'Section.program_type_id' => $prevSectionsDetail['Section']['program_type_id'],
                        'Section.department_id' => $this->department_id,
                        'Section.year_level_id' => $next_year_level_exists['YearLevel']['id'],
                        'Section.academicyear <> ' => $prevSectionsDetail['Section']['academicyear'],
                        'Section.id > ' => $prevSectionsDetail['Section']['id'],
                        'Section.archive' => 0,
                    ),
                    'contain' => array(
                        'YearLevel' => array('id', 'name')
                    ),
                    'recursive' => -1
                ));
            }

            if (isset($next_year_level['Section']['year_level_id']) && !empty($next_year_level['Section']['year_level_id'])) {
                $conditions = array(
                    'Section.id <>' => $prevSectionsDetail['Section']['id'],
                    'Section.program_id' => $prevSectionsDetail['Section']['program_id'],
                    'Section.program_type_id' => $prevSectionsDetail['Section']['program_type_id'],
                    'Section.department_id' => $this->department_id,
                    'Section.year_level_id >= ' => $prevSectionsDetail['YearLevel']['id'],
                    'Section.year_level_id <= ' => $next_year_level['YearLevel']['id'],
                    'Section.created > ' => $sectionCreatedDate,
                    'Section.academicyear' => array(
                        $prevSectionsDetail['Section']['academicyear'],
                        $next_year_level['Section']['academicyear']
                    ),
                    'OR' => array(
                        'Section.curriculum_id IS NULL',
                        'Section.curriculum_id' => $prevSectionsDetail['Section']['curriculum_id']
                    ),
                    'Section.archive' => 0
                );
            } else {
                $conditions = array(
                    'Section.id <> ' => $prevSectionsDetail['Section']['id'],
                    'Section.program_id' => $prevSectionsDetail['Section']['program_id'],
                    'Section.program_type_id' => $prevSectionsDetail['Section']['program_type_id'],
                    'Section.department_id' => $this->department_id,
                    'Section.year_level_id' => $prevSectionsDetail['YearLevel']['id'],
                    'Section.academicyear' => $prevSectionsDetail['Section']['academicyear'],
                    'OR' => array(
                        'Section.curriculum_id IS NULL',
                        'Section.curriculum_id' => array(
                            $student_curriculum_id,
                            $prevSectionsDetail['Section']['curriculum_id']
                        ),
                    ),
                    'Section.archive' => 0
                );
                debug($conditions);
            }
        } else {
            $conditions = array(
                'Section.college_id' => $this->college_id,
                'Section.id <> ' => $prevSectionsDetail['Section']['id'],
                'Section.program_id' => $prevSectionsDetail['Section']['program_id'],
                'Section.program_type_id' => $prevSectionsDetail['Section']['program_type_id'],
                'Section.created > ' => $sectionCreatedDate,
                'Section.academicyear' => $prevSectionsDetail['Section']['academicyear'],
                'Section.department_id is null',
                'Section.archive' => 0,
            );
        }

        $sections_all = $this->Section->find(
            'all',
            array(
                'conditions' => $conditions,
                'order' => array(
                    'Section.year_level_id' => 'ASC',
                    'Section.academicyear' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                ),
                'contain' => array('YearLevel' => array('id', 'name')),
                'recursive' => -1
            )
        );

        $sections = array();

        if (!empty($sections_all)) {
            foreach ($sections_all as $key => $section) {
                $sections[$section['Section']['id']] = $section['Section']['name'] . ' (' . (isset($section['YearLevel']['name']) ? $section['YearLevel']['name'] : ($section['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $section['Section']['academicyear'] . ')  (' . (count(
                        $this->Section->getAllActiveStudents($section['Section']['id'])['Student']
                    )) . ')';
            }
        }

        $previousSectionName = $this->Section->find('first', array(
                'conditions' => array(
                    'Section.id' => $previous_section_id
                ),
                'contain' => array(
                    'Program',
                    'ProgramType',
                    'YearLevel',
                    'Department',
                    'College'
                )
            )
        );

        if (!empty($studentsections['Student'])) {
            foreach ($studentsections['Student'] as $key => $student) {
                //debug($student['id']);
                //check registration and grades are full submitted
                /* $count_registion = ClassRegistry::init('CourseRegistration')->find('count', array('conditions' => array('CourseRegistration.student_id' => $student['id'], 'CourseRegistration.section_id' => $prevSectionsDetail['Section']['id'])));
				//debug($count_registion);
				if ($count_registion > 0) {
					unset($studentsections['Student'][$key]);
				} */

                if (!$this->Section->chceck_all_registered_added_courses_are_graded(
                    $student['id'],
                    $section_id = $prevSectionsDetail['Section']['id'],
                    $check_for_invalid_grades = 1,
                    $from_student = ''
                )) {
                    unset($studentsections['Student'][$key]);
                }
            }
        }

        //unset($sections[$previous_section_id]);
        $this->set(compact('previous_section_id', 'sections', 'previousSectionName', 'studentsections'));
    }


    public function move_student_section_to_new($previous_section_id, $student_id)
    {

        $this->layout = 'ajax';

        $students = $this->Section->Student->find(
            'first',
            array('conditions' => array('Student.id' => $student_id), 'recursive' => -1)
        );
        $previousSection = $this->Section->find(
            'first',
            array('conditions' => array('Section.id' => $previous_section_id), 'recursive' => -1)
        );
        $equivalentProgramTypes = $this->__getEquivalentProgramTypes($students['Student']['program_type_id']);
        $current_academicyear = $this->AcademicYear->current_academicyear();

        $conditions = array();

        /* if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
			$conditions = array(
				//'Section.college_id' => $this->college_id,
				'Section.id <>' => $previous_section_id,
				'Section.department_id' => $this->department_id,
				'Section.archive' => 0,
				'Section.program_id' => $students['Student']['program_id'],
				'Section.program_type_id' => $equivalentProgramTypes,
				'Section.academicyear' => $previousSection['Section']['academicyear'],
				//'Section.year_level_id >=' => $previousSection['Section']['year_level_id'],
				'Section.year_level_id' => $previousSection['Section']['year_level_id']
			);
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
			if (is_null($students['Student']['department_id'])) {
				$conditions = array(
					'Section.college_id' => $this->college_id,
					'Section.id <>' => $previous_section_id,
					'Section.archive' => 0,
					'Section.program_id' => $students['Student']['program_id'],
					'Section.program_type_id' => $equivalentProgramTypes,
					'Section.academicyear' => $previousSection['Section']['academicyear'],
					"OR" => array(
						"Section.department_id is null",
						"Section.department_id = ''"
					)
				);
			}
		} else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
			if (!empty($this->department_ids)) {
				$conditions = array(
					'Section.program_id' => $students['Student']['program_id'],
					'Section.id <>' => $previous_section_id,
					'Section.program_type_id' => $equivalentProgramTypes,
					'Section.academicyear' => $previousSection['Section']['academicyear'],
					'Section.department_id' => $students['Student']['department_id'],
					'Section.archive' => 0
				);
			} else if (!empty($this->college_ids)) {
				if (is_null($students['Student']['department_id'])) {
					$conditions = array(
						'Section.college_id' => $students['Student']['college_id'],
						'Section.id <>' => $previous_section_id,
						'Section.program_id' => $students['Student']['program_id'],
						'Section.program_type_id' => $equivalentProgramTypes,
						'Section.academicyear' => $previousSection['Section']['academicyear'],
						'Section.department_id is null',
						'Section.archive' => 0
					);
				}
			} else {
				$conditions = array(
					'Section.college_id' => $students['Student']['college_id'],
					'Section.id <>' => $previous_section_id,
					'Section.program_id' => $students['Student']['program_id'],
					'Section.program_type_id' => $equivalentProgramTypes,
					'Section.academicyear' => $previousSection['Section']['academicyear'],
					'Section.department_id is null',
					'Section.archive' => 0
				);
			}
		} else {
		} */

        if (!$previousSection['Section']['archive'] || ($previousSection['Section']['archive'] && $previousSection['Section']['academicyear'] == $current_academicyear)) {
            if (is_null($students['Student']['department_id']) || empty($students['Student']['department_id'])) {
                $conditions = array(
                    'Section.college_id' => $students['Student']['college_id'],
                    'Section.id <>' => $previous_section_id,
                    'Section.archive' => 0,
                    'Section.program_id' => $students['Student']['program_id'],
                    'Section.program_type_id' => $equivalentProgramTypes,
                    'Section.academicyear' => $previousSection['Section']['academicyear'],
                    "OR" => array(
                        "Section.department_id is null",
                        "Section.department_id = ''"
                    )
                );
            } else {
                $conditions = array(
                    'Section.department_id' => $students['Student']['department_id'],
                    'Section.id <>' => $previous_section_id,
                    'Section.archive' => 0,
                    'Section.program_id' => $students['Student']['program_id'],
                    'Section.program_type_id' => $equivalentProgramTypes,
                    'Section.academicyear' => $previousSection['Section']['academicyear'],
                    'Section.year_level_id' => $previousSection['Section']['year_level_id']
                );
            }
        } else {
            if ($previousSection['Section']['archive'] && $previousSection['Section']['academicyear'] != $current_academicyear) {
                if (is_null($students['Student']['department_id']) || empty($students['Student']['department_id'])) {
                    $conditions = array(
                        'Section.college_id' => $students['Student']['college_id'],
                        'Section.id <>' => $previous_section_id,
                        'Section.archive' => 0,
                        'Section.program_id' => $students['Student']['program_id'],
                        'Section.program_type_id' => $equivalentProgramTypes,
                        'Section.academicyear' => $current_academicyear,
                        "OR" => array(
                            "Section.department_id is null",
                            "Section.department_id = ''"
                        )
                    );
                } else {
                    $conditions = array(
                        'Section.department_id' => $students['Student']['department_id'],
                        'Section.id <>' => $previous_section_id,
                        'Section.archive' => 0,
                        'Section.program_id' => $students['Student']['program_id'],
                        'Section.program_type_id' => $equivalentProgramTypes,
                        'Section.academicyear' => $current_academicyear,
                        'Section.year_level_id >' => $previousSection['Section']['year_level_id']
                    );
                }
            }
        }

        $sectionsList = array();

        if (!empty($conditions)) {
            $sectionsList = $this->Section->find('all', array(
                'conditions' => $conditions,
                'contain' => array('YearLevel'),
                'order' => array(
                    //'Section.created' => 'DESC',
                    'Section.academicyear' => 'ASC',
                    'Section.year_level_id' => 'ASC',
                    'Section.college_id' => 'ASC',
                    'Section.department_id' => 'ASC',
                    'Section.program_id' => 'ASC',
                    'Section.program_type_id' => 'ASC',
                    'Section.name' => 'ASC',
                    'Section.id' => 'ASC',
                ),
            ));
        }

        $sections = array();

        if (!empty($sectionsList)) {
            foreach ($sectionsList as $k => $v) {
                $sections[$v['Section']['id']] = $v['Section']['name'] . ' (' . (isset ($v['YearLevel']) && !empty($v['YearLevel']['name']) ? $v['YearLevel']['name'] : ($v['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $v['Section']['academicyear'] . ')';
            }
        }

        $previousSectionName = $this->Section->find('first', array(
            'conditions' => array(
                'Section.id' => $previous_section_id
            ),
            'contain' => array(
                'Program',
                'ProgramType',
                'YearLevel',
                'Department',
                'College'
            )
        ));

        $this->set(
            compact(
                'previous_section_id',
                'sections',
                'previousSectionName',
                'students'
            )
        );
    }


    public function __getEquivalentProgramTypes($program_type_id = 0)
    {

        $program_types_to_look = array();

        $equivalentProgramType = unserialize(
            $this->Section->Student->ProgramType->field(
                'ProgramType.equivalent_to_id',
                array('ProgramType.id' => $program_type_id)
            )
        );

        if (!empty($equivalentProgramType)) {
            $selected_program_type_array = array();
            $selected_program_type_array[] = $program_type_id;
            $program_types_to_look = array_merge($selected_program_type_array, $equivalentProgramType);
        } else {
            $program_types_to_look[] = $program_type_id;
        }

        debug($program_types_to_look);

        return $program_types_to_look;
    }


    /*
	public function section_move_update()
	{
		if (!empty($this->request->data)) {
			$selected_section_curriculum = $this->Section->get_section_curriculum($this->request->data['Section']['Selected_section_id']);

			$isSectionCollegeSection = $this->Section->find('count', array(
				'conditions' => array(
					'Section.id' => $this->request->data['Section']['Selected_section_id'],
					'Section.department_id is null',
					'Section.college_id' => $this->college_id
				)
			));

			if (!empty($selected_section_curriculum) || $isSectionCollegeSection > 0) {

				$previous_section_curriculum = $this->Section->get_section_curriculum($this->request->data['Section']['previous_section_id']);

				$similarAcademicYear = false;
				$sameCurriculum = false;

				$college_selected_section = $this->Section->field('academicyear', array(
					'Section.id' => $this->request->data['Section']['Selected_section_id'],
					'Section.department_id is null',
					'Section.college_id' => $this->college_id
				));

				$college_previous_section_selected = $this->Section->field('academicyear', array(
					'Section.id' => $this->request->data['Section']['previous_section_id'],
					'Section.department_id is null',
					'Section.college_id' => $this->college_id
				));

				if ((!empty($previous_section_curriculum) && !empty($selected_section_curriculum)) && ($previous_section_curriculum == $selected_section_curriculum)) {
					$sameCurriculum = true;
				}


				if (strcasecmp($college_selected_section, $college_previous_section_selected) === 0) {
					$similarAcademicYear = true;
				}

				$student_full_name = $this->Section->Student->field('Student.full_name', array('Student.id' => $this->request->data['Section']['student_id']));
				$new_section_name = $this->Section->field('Section.name', array('Section.id' => $this->request->data['Section']['Selected_section_id']));

				if ($sameCurriculum || $similarAcademicYear ) {

					if ($this->Section->isMoveAllowed($this->request->data['Section']['previous_section_id'], $this->request->data['Section']['student_id'], $this->request->data['Section']['Selected_section_id'])) {

						$preparedForSave = $this->Section->isMoveAllowed($this->request->data['Section']['previous_section_id'], $this->request->data['Section']['student_id'], $this->request->data['Section']['Selected_section_id']);

						$this->request->data['StudentsSection']['student_id'] = $this->request->data['Section']['student_id'];
						$this->request->data['StudentsSection']['section_id'] = $this->request->data['Section']['Selected_section_id'];

						$transaction = false;

						if ($preparedForSave !== true) {
							// synchronize the course registration table with published course
							if ($this->Section->Student->CourseRegistration->saveAll($preparedForSave['CourseRegistration'])) {
								// do the move if course registration is succeeded.
								//To check whether the record is already there as archive, if so just turnoff the archive is enough
								$already_recorded_id = $this->_check_the_record_in_archive($this->request->data['StudentsSection']['section_id'], $this->request->data['StudentsSection']['student_id']);

								if (!empty($already_recorded_id)) {
									$this->Section->StudentsSection->id = $already_recorded_id;
									$this->Section->StudentsSection->saveField('archive', '0');
								} else {
									$this->Section->StudentsSection->save($this->request->data['StudentsSection']);
								}
								$transaction = true;

							} else {
								$this->Flash->error('Synchronization problem, students course regisration is not synchronized with published courses.');
								$this->redirect(array('action' => 'display_sections', $this->request->data['Section']['Selected_section_id']));
							}
						}

						if ($preparedForSave == 3) {
							$transaction = true;
							//To check whether the record is already there as archive, if so just turnoff the archive is enough
							$already_recorded_id = $this->_check_the_record_in_archive($this->request->data['StudentsSection']['section_id'], $this->request->data['StudentsSection']['student_id']);

							if (!empty($already_recorded_id)) {
								$this->Section->StudentsSection->id = $already_recorded_id;
								$this->Section->StudentsSection->saveField('archive', '0');
							} else {
								$this->Section->StudentsSection->save($this->request->data['StudentsSection']);
							}
						}

						//$this->Section->StudentsSection->save($this->request->data['StudentsSection']);
						//now delete this student from previous section from associate table
						//check student deletion from section is possible ?

						$previous_section_name = $this->Section->field('Section.name', array('Section.id' => $this->request->data['Section']['previous_section_id']));

						if ($transaction) {
							if (!$this->Section->Student->CourseRegistration->ExamGrade->isCourseGradeSubmitted($this->request->data['Section']['student_id'], $this->request->data['Section']['previous_section_id'])) {

								$this->Section->StudentsSection->id = $this->Section->StudentsSection->field('StudentsSection.id', array(
									'StudentsSection.student_id' => $this->request->data['Section']['student_id'],
									'StudentsSection.section_id' => $this->request->data['Section']['previous_section_id'],
									'StudentsSection.archive' => 0
								));

								$this->Section->StudentsSection->delete($this->Section->StudentsSection->id);
							} else {
								$this->Section->StudentsSection->id = $this->Section->StudentsSection->field('StudentsSection.id', array(
									'StudentsSection.student_id' => $this->request->data['Section']['student_id'],
									'StudentsSection.section_id' => $this->request->data['Section']['previous_section_id'],
									'StudentsSection.archive' => 0
								));
								$this->Section->StudentsSection->saveField('archive', '1');
							}

							$this->Flash->success($student_full_name . ' is moved from  ' . $previous_section_name . ' section to section ' . $new_section_name . '.');
							$this->redirect(array('action' => 'display_sections', $this->request->data['Section']['Selected_section_id']));

						} else {
							$this->Flash->error($student_full_name . ' could\'t be moved from ' .$previous_section_name . ' to Section ' . $new_section_name . ' something went wrong, please try again.');
							$this->redirect(array('action' => 'display_sections', $this->request->data['Section']['Selected_section_id']));
						}
					} else {
						$error = $this->Section->invalidFields();
						if (isset($error['move_not_allowed'])) {
							$this->Flash->error($error['move_not_allowed'][0]);
						}
						$this->redirect(array('action' => 'display_sections', $this->request->data['Section']['Selected_section_id']));
					}
				} else {

					$new_section_curriculum_name = $this->Section->Student->Curriculum->field('Curriculum.curriculum_detail', array('Curriculum.id' => $selected_section_curriculum));
					$student_curriculum_name = $this->Section->Student->Curriculum->field('Curriculum.curriculum_detail', array('Curriculum.id' => $previous_section_curriculum));

					if ($this->role_id == ROLE_DEPARTMENT) {
						$this->Flash->error($student_full_name . ' will not be moved to ' . $new_section_name . ' section. Because The student attached curriculum  "' . $student_curriculum_name . '" is different from ' . $new_section_name . ' attached section curiculum "' . $new_section_curriculum_name. '".');
					} else if ($this->role_id == ROLE_COLLEGE) {
						//$college_selected_section, $college_previous_section_selected
						$this->Flash->error($student_full_name . ' will not beto moved to ' . $new_section_name . ' section. The student is accepted in ' . $college_selected_section . ' academic year, which is different from Section ' . $new_section_name . ' ' . $college_previous_section_selected . ' academic year "' . $new_section_curriculum_name. '".');
					}
					return $this->redirect(array('action' => 'display_sections', $this->request->data['Section']['Selected_section_id']));
				}
			} else {

				$student_full_name = $this->Section->Student->field('Student.full_name', array('Student.id' => $this->request->data['Section']['student_id']));
				$new_section_name = $this->Section->field('Section.name', array('Section.id' => $this->request->data['Section']['Selected_section_id']));

				if ($this->role_id == ROLE_DEPARTMENT) {
					$this->Flash->error($student_full_name . ' will not be moved to ' . $new_section_name . ' section. The target section is empty.');
				} else if ($this->role_id == ROLE_COLLEGE) {
					$this->Flash->error($student_full_name . ' will not be moved to ' . $new_section_name . ' section. The target section batch is different.');
				}

				$this->redirect(array('action' => 'display_sections', $this->request->data['Section']['Selected_section_id']));
			}
		}
	}
	*/


    public function section_move_update()
    {

        if (!empty($this->request->data) && isset($this->request->data['move_to_section'])) {
            debug($this->request->data['Section']);
            $selectedStudents = $this->request->data;

            unset($selectedStudents['Section']['Selected_section_id']);
            unset($selectedStudents['Section']['previous_section_id']);
            unset($selectedStudents['Section']['SelectAll']);

            $selectedStudentsId = array();
            $selected_student_id = '';

            if (!empty($selectedStudents['Section'])) {
                foreach ($selectedStudents['Section'] as $ss => $vv) {
                    if (!empty($vv['selected_id'])) {
                        $selectedStudentsId[] = $vv['student_id'];
                        $selected_student_id = $vv['student_id'];
                    }
                }
            }

            if (empty($selectedStudentsId)) {
                $selectedStudentsId[] = $selectedStudents['Section']['student_id'];
            }

            if ($this->Section->isSectionMoveAllowedM(
                $this->request->data['Section']['previous_section_id'],
                $selectedStudentsId,
                $this->request->data['Section']['Selected_section_id']
            )) {
                $new_section_name = $this->Section->field(
                    'Section.name',
                    array('Section.id' => $this->request->data['Section']['Selected_section_id'])
                );
                $previous_section_name = $this->Section->field(
                    'Section.name',
                    array('Section.id' => $this->request->data['Section']['previous_section_id'])
                );
                $this->Flash->success(
                    'The selected student is moved from ' . $previous_section_name . ' section to ' . $new_section_name . ' section.'
                );
            } else {
                $error = $this->Section->invalidFields();
                if (isset($error['move_not_allowed'])) {
                    $this->Flash->error($error['move_not_allowed'][0]);
                }
            }

            $this->redirect(
                array('controller' => 'students', 'action' => 'student_academic_profile', $selected_student_id)
            );
        } else {
            if (!empty($this->request->data) && !isset($this->request->data['move_to_section'])) {
                $selected_section_curriculum = $this->Section->get_section_curriculum(
                    $this->request->data['Section']['Selected_section_id']
                );

                $isSectionCollegeSection = $this->Section->find('count', array(
                    'conditions' => array(
                        'Section.id' => $this->request->data['Section']['Selected_section_id'],
                        'Section.department_id is null',
                        'Section.college_id' => $this->college_id
                    )
                ));

                if (!empty($selected_section_curriculum) || $isSectionCollegeSection > 0) {
                    $previous_section_curriculum = $this->Section->get_section_curriculum(
                        $this->request->data['Section']['previous_section_id']
                    );
                    $similarAcademicYear = false;
                    $sameCurriculum = false;

                    $college_selected_section = $this->Section->field('academicyear', array(
                        'Section.id' => $this->request->data['Section']['Selected_section_id'],
                        'Section.department_id is null',
                        'Section.college_id' => $this->college_id
                    ));

                    $college_previous_section_selected = $this->Section->field('academicyear', array(
                        'Section.id' => $this->request->data['Section']['previous_section_id'],
                        'Section.department_id is null',
                        'Section.college_id' => $this->college_id
                    ));

                    if ($selected_section_curriculum == "nostudentinsection" && !empty($previous_section_curriculum)) {
                        $selected_section_curriculum = $previous_section_curriculum;
                    }

                    if ((!empty($previous_section_curriculum) && !empty($selected_section_curriculum)) && ($previous_section_curriculum == $selected_section_curriculum)) {
                        $sameCurriculum = true;
                    }

                    if (strcasecmp($college_selected_section, $college_previous_section_selected) === 0) {
                        $similarAcademicYear = true;
                    }

                    if ($sameCurriculum || $similarAcademicYear) {
                        $selectedStudents = $this->request->data;

                        unset($selectedStudents['Section']['Selected_section_id']);
                        unset($selectedStudents['Section']['previous_section_id']);
                        unset($selectedStudents['Section']['SelectAll']);

                        $selectedStudentsId = array();

                        if (!empty($selectedStudents['Section'])) {
                            foreach ($selectedStudents['Section'] as $ss => $vv) {
                                if (!empty($vv['selected_id'])) {
                                    $selectedStudentsId[] = $vv['student_id'];
                                }
                            }
                        }

                        if (empty($selectedStudentsId)) {
                            $selectedStudentsId[] = $selectedStudents['Section']['student_id'];
                        }

                        if ($this->Section->isSectionMoveAllowedM(
                            $this->request->data['Section']['previous_section_id'],
                            $selectedStudentsId,
                            $this->request->data['Section']['Selected_section_id']
                        )) {
                            $new_section_name = $this->Section->field(
                                'Section.name',
                                array('Section.id' => $this->request->data['Section']['Selected_section_id'])
                            );
                            $previous_section_name = $this->Section->field(
                                'Section.name',
                                array('Section.id' => $this->request->data['Section']['previous_section_id'])
                            );
                            $this->Flash->success(
                                'The selected student is moved from ' . $previous_section_name . ' section to ' . $new_section_name . ' section.'
                            );
                            $this->redirect(
                                array(
                                    'action' => 'display_sections',
                                    $this->request->data['Section']['Selected_section_id']
                                )
                            );
                        } else {
                            $error = $this->Section->invalidFields();
                            if (isset($error['move_not_allowed'])) {
                                $this->Flash->error($error['move_not_allowed'][0]);
                            }
                            $this->redirect(
                                array(
                                    'action' => 'display_sections',
                                    $this->request->data['Section']['Selected_section_id']
                                )
                            );
                        }
                    } else {
                        $new_section_name = $this->Section->field(
                            'Section.name',
                            array('Section.id' => $this->request->data['Section']['Selected_section_id'])
                        );
                        $new_section_curriculum_name = $this->Section->Student->Curriculum->field(
                            'Curriculum.curriculum_detail',
                            array('Curriculum.id' => $selected_section_curriculum)
                        );
                        $student_curriculum_name = $this->Section->Student->Curriculum->field(
                            'Curriculum.curriculum_detail',
                            array('Curriculum.id' => $previous_section_curriculum)
                        );

                        if ($this->role_id == ROLE_DEPARTMENT) {
                            $this->Flash->error(
                                'The selected student will not be moved to ' . $new_section_name . ' section. The student attached curriculum "' . $student_curriculum_name . '" curriculum is different from ' . $new_section_name . ' section attached curiculum "' . $new_section_curriculum_name . '".'
                            );
                        } else {
                            if ($this->role_id == ROLE_COLLEGE) {
                                $this->Flash->error(
                                    'The selected student will not be moved to ' . $new_section_name . ' section.' . $college_selected_section . ' sections academic year is different from ' . $new_section_name . ' section academic year,  ' . $college_previous_section_selected . '.'
                                );
                            }
                        }
                        return $this->redirect(
                            array(
                                'action' => 'display_sections',
                                $this->request->data['Section']['Selected_section_id']
                            )
                        );
                    }
                } else {
                    $new_section_name = $this->Section->field(
                        'Section.name',
                        array('Section.id' => $this->request->data['Section']['Selected_section_id'])
                    );

                    if ($this->role_id == ROLE_DEPARTMENT) {
                        $this->Flash->error(
                            'The selected student will not be moved to ' . $new_section_name . ' section . The target section is empty.'
                        );
                    } else {
                        if ($this->role_id == ROLE_COLLEGE) {
                            $this->Flash->error(
                                'The selected student will not be moved to ' . $new_section_name . '. The target section academic year is different.'
                            );
                        }
                    }

                    $this->redirect(
                        array('action' => 'display_sections', $this->request->data['Section']['Selected_section_id'])
                    );
                }
            }
        }
    }

    public function add_student_to_section($student_id)
    {

        $this->layout = 'ajax';

        $is_student_dismissed = 0;
        $is_student_readmitted = 0;
        $sectionOrganized = array();
        $prefreshStudent = 0;
        $studentNeedsSectionAssignment = 0;
        $currentYearLevelID = null;
        $yearLevelQueryOperator = '>=';
        $last_student_status = array();
        $studentMustHaveCurriculum = 1;
        $curriculumYearLevels = array();
        $statusGeneratedForLastRegistration = 0;

        $lastRegisteredYearLevelID = '';
        $lastRegisteredAcademicYear = '';
        $lastRegisteredSemester = '';

        $lastReadmittedAcademicYear = '';
        $lastReadmittedSemester = '';
        $lastReadmittedDate = '';
        $possibleAcademicYears = array();
        $student_attached_curriculum_name = '';
        $student_have_invalid_grade = 0;
        $lastRegisteredYearLevelName = '';

        $checkOnlyRegisteredPassFailGradeType = null;


        //$this->Section->Student->bindModel(array('hasMany' => array('StudentsSection' => array('conditions' => array('StudentsSection.archive' => array(0,1))))));

        $student_detail = $this->Section->Student->find('first', array(
            'conditions' => array(
                'Student.id' => $student_id
            ),
            'contain' => array(
                'AcceptedStudent' => array('id', 'studentnumber', 'academicyear'),
                'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                'Program' => array('id', 'name'),
                'ProgramType' => array('id', 'name'),
                'Section' => array(
                    'fields' => array(
                        'Section.id',
                        'Section.name',
                        'Section.year_level_id',
                        'Section.academicyear',
                        'Section.college_id',
                        'Section.department_id',
                        'Section.curriculum_id',
                        'Section.created',
                        'Section.archive'
                    ),
                    'order' => array(
                        'Section.academicyear' => 'DESC',
                        'Section.year_level_id' => 'ASC',
                        'Section.id' => 'ASC',
                        'Section.name' => 'ASC'
                    ),
                    'YearLevel' => array('fields' => array('id', 'name'))
                ),
                'CourseRegistration' => array(
                    'order' => array(
                        'CourseRegistration.academic_year' => 'DESC',
                        'CourseRegistration.semester' => 'DESC',
                        'CourseRegistration.id' => 'DESC',
                    ),
                    'fields' => array(
                        'id',
                        'year_level_id',
                        'student_id',
                        'section_id',
                        'semester',
                        'academic_year',
                        'published_course_id',
                        'created'
                    ),
                    'limit' => 1
                ),
                'Readmission' => array(
                    'conditions' => array(
                        'Readmission.registrar_approval' => 1,
                        'Readmission.academic_commision_approval' => 1,
                    ),
                    'fields' => array('student_id', 'academic_year', 'semester', 'registrar_approval_date', 'modified'),
                    'order' => array('Readmission.modified' => 'DESC')
                )
            ),
            'fields' => array(
                'Student.studentnumber',
                'Student.full_name',
                'Student.curriculum_id',
                'Student.department_id',
                'Student.college_id',
                'Student.program_id',
                'Student.program_type_id',
                'Student.gender',
                'Student.graduated',
                'Student.academicyear',
                'Student.admissionyear',
            ),
        ));

        //debug($student_detail);

        $program_types_to_look = $this->__getEquivalentProgramTypes($student_detail['Student']['program_type_id']);
        //debug($program_types_to_look);

        if (is_null(
                $student_detail['Student']['department_id']
            ) && ($student_detail['Student']['program_id'] == PROGRAM_UNDEGRADUATE || $student_detail['Student']['program_id'] == PROGRAM_REMEDIAL)) {
            $prefreshStudent = 1;
            $studentMustHaveCurriculum = 0;
        } else {
            if (!is_null(
                    $student_detail['Student']['curriculum_id']
                ) && $student_detail['Student']['curriculum_id'] != 0) {
                $studentMustHaveCurriculum = 0;
            }
        }

        if (isset($student_detail['Readmission'][0])) {
            $lastReadmittedAcademicYear = $student_detail['Readmission'][0]['academic_year'];
            $lastReadmittedSemester = $student_detail['Readmission'][0]['semester'];
            $lastReadmittedDate = $student_detail['Readmission'][0]['registrar_approval_date'];
        }


        $isLastSemesterInCurriculum = ClassRegistry::init('StudentStatusPattern')->isLastSemesterInCurriculum(
            $student_id
        );

        debug($isLastSemesterInCurriculum);

        $error_meaasge = $this->Section->chceck_all_registered_added_courses_are_graded(
            $student_id,
            $section_id = null,
            $check_for_invalid_grades = 1,
            $from_student = '',
            $skip_f_grade = 1,
            $get_error_message = 1
        );

        $msg = '';

        if (!empty($error_meaasge) && is_array($error_meaasge)) {
            $error_meaasge = $error_meaasge[$student_id]['disqualification'];
            $msg = '<ol>';

            foreach ($error_meaasge as $key => $error_msg) {
                $msg .= '<li>' . $error_msg . '</li>';
            }

            $msg .= '</ol>';

            debug($msg);
        }

        if (isset($student_detail['CourseRegistration'][0]['year_level_id']) && !empty($student_detail['CourseRegistration'][0]['year_level_id'])) {
            $lastRegisteredYearLevelName = $this->Section->YearLevel->field(
                'name',
                array("YearLevel.id" => $student_detail['CourseRegistration'][0]['year_level_id'])
            );
        } else {
            if (isset($student_detail['CourseRegistration'][0]['year_level_id']) && empty($student_detail['CourseRegistration'][0]['year_level_id'])) {
                $lastRegisteredYearLevelName = 'Pre/1st';
            }
        }

        debug($lastRegisteredYearLevelName);

        $statusGeneratedForLastRegistration = 1;


        if (empty($student_detail['Section'])) {
            //freshman without section assignment
            $studentNeedsSectionAssignment = 1;
            $statusGeneratedForLastRegistration = 1;
            $possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange(
                $student_detail['AcceptedStudent']['academicyear'],
                $this->AcademicYear->current_academicyear()
            );
        } else {
            if (empty($student_detail['CourseRegistration'])) {
                //freshman with section assignment but no registration
                $statusGeneratedForLastRegistration = 1;
            } else {
                $student_section_exam_status = ClassRegistry::init('Student')->get_student_section(
                    $student_id,
                    null,
                    null
                );
                //debug($student_section_exam_status);

                if (!empty($student_section_exam_status['StudentExamStatus'])) {
                    $last_student_status['StudentExamStatus'] = $student_section_exam_status['StudentExamStatus'];
                    //debug($last_student_status);
                    // have exam status
                    //debug($student_section_exam_status['StudentExamStatus']['academic_status_id']);

                    if (isset($student_section_exam_status['StudentExamStatus']['academic_status_id']) && is_numeric(
                            $student_section_exam_status['StudentExamStatus']['academic_status_id']
                        ) && $student_section_exam_status['StudentExamStatus']['academic_status_id'] != DISMISSED_ACADEMIC_STATUS_ID) {
                        // not dismissed

                        $studentNeedsSectionAssignment = 1;
                        $possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange(
                            (isset($student_detail['CourseRegistration'][0]['academic_year']) && !empty($student_detail['CourseRegistration'][0]['academic_year']) ? $student_detail['CourseRegistration'][0]['academic_year'] : $student_detail['AcceptedStudent']['academicyear']),
                            $this->AcademicYear->current_academicyear()
                        );

                        $generalSetting = ClassRegistry::init(
                            'GeneralSetting'
                        )->getAllGeneralSettingsByStudentByProgramIdOrBySectionID($student_id);
                        //debug($generalSetting['GeneralSetting']['semesterCountForAcademicYear']);

                        //last registration
                        debug($student_detail['CourseRegistration'][0]);
                        debug($student_detail['CourseRegistration'][0]['academic_year']);
                        debug($student_detail['CourseRegistration'][0]['year_level_id']);

                        $lastRegisteredAcademicYear = $student_detail['CourseRegistration'][0]['academic_year'];
                        $lastRegisteredSemester = $student_detail['CourseRegistration'][0]['semester'];

                        if (!$prefreshStudent) {
                            $lastRegisteredYearLevelID = $student_detail['CourseRegistration'][0]['year_level_id'];
                        }


                        $alreadyGeneratedStatus = ClassRegistry::init('StudentExamStatus')->find('first', array(
                            'conditions' => array(
                                'StudentExamStatus.student_id' => $student_id,
                                'StudentExamStatus.academic_year' => $student_detail['CourseRegistration'][0]['academic_year'],
                                'StudentExamStatus.semester' => $student_detail['CourseRegistration'][0]['semester'],
                            ),
                            'contain' => array(
                                'AcademicStatus' => array('id', 'name', 'computable'),
                            )
                        ));

                        if (empty($alreadyGeneratedStatus) && $student_detail['Student']['program_type_id'] == PROGRAM_TYPE_REGULAR) {
                            $statusGeneratedForLastRegistration = 0;
                        }

                        ////// Tweak $check_for_invalid_grades and PROGRAM_TYPE_REGULAR to bypass system checks to change section with out stutus generation checks   ///////

                        if ($this->Section->chceck_all_registered_added_courses_are_graded(
                            $student_id,
                            $section_id = null,
                            $check_for_invalid_grades = 1,
                            $from_student = '',
                            $skip_f_grade = 1
                        )) {
                            $studentNeedsSectionAssignment = 1;
                            if ($student_detail['Student']['program_type_id'] != PROGRAM_TYPE_REGULAR) {
                                $statusGeneratedForLastRegistration = 1;
                            }
                        } else {
                            $student_have_invalid_grade = 1;
                        }


                        // for students failed Exit Exam for the first time
                        if ($student_detail['Student']['program_id'] == PROGRAM_UNDEGRADUATE && $isLastSemesterInCurriculum && !empty($msg) && is_array(
                                $msg
                            ) && count($msg) == 1) {
                            $studentNeedsSectionAssignment = 1;
                            $student_have_invalid_grade = 0; // Exit Exam
                        }

                        // for students only registered pass or fail grade type
                        $checkOnlyRegisteredPassFailGradeType = ClassRegistry::init(
                            'StudentExamStatus'
                        )->onlyRegisteredPassFailGradeType(
                            $student_id,
                            $student_detail['CourseRegistration'][0]['academic_year'],
                            $student_detail['CourseRegistration'][0]['semester']
                        );

                        if ($checkOnlyRegisteredPassFailGradeType) {
                            $studentNeedsSectionAssignment = 1;
                            $statusGeneratedForLastRegistration = 1;
                        }

                        ////// END Tweak $check_for_invalid_grades and PROGRAM_TYPE_REGULAR to bypass system checks to change section with out stutus generation checks   ///////

                        if ($generalSetting['GeneralSetting']['semesterCountForAcademicYear'] == 3) {
                            if ($student_section_exam_status['StudentExamStatus']['semester'] == 'I' || $student_section_exam_status['StudentExamStatus']['semester'] == 'II') {
                                // no need to show next year one remaining semester
                                if (!$prefreshStudent) {
                                    debug($student_detail['Section'][0]['year_level_id']);
                                    $currentYearLevelID = $student_detail['Section'][0]['year_level_id'];
                                    $yearLevelQueryOperator = '=';

                                    if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                        $currentYearLevelID = $lastRegisteredYearLevelID;
                                    }
                                }
                            } else {
                                if ($student_section_exam_status['StudentExamStatus']['semester'] == 'III') {
                                    // show only year ahead
                                    if (!$prefreshStudent) {
                                        debug($student_detail['Section'][0]['year_level_id']);
                                        $currentYearLevelID = $student_detail['Section'][0]['year_level_id'];
                                        $yearLevelQueryOperator = '>';

                                        if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                            $currentYearLevelID = $lastRegisteredYearLevelID;
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($generalSetting['GeneralSetting']['semesterCountForAcademicYear'] == 2) {
                                if ($student_section_exam_status['StudentExamStatus']['semester'] == 'I') {
                                    // no need to show next year one remaining semester
                                    if (!$prefreshStudent) {
                                        debug($student_detail['Section'][0]['year_level_id']);
                                        $currentYearLevelID = $student_detail['Section'][0]['year_level_id'];
                                        $yearLevelQueryOperator = '=';

                                        if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                            $currentYearLevelID = $lastRegisteredYearLevelID;
                                        }
                                    }
                                } else {
                                    if ($student_detail['CourseRegistration'][0]['semester'] == 'II') {
                                        if (!$prefreshStudent) {
                                            debug($student_detail['Section'][0]['year_level_id']);
                                            $currentYearLevelID = $student_detail['Section'][0]['year_level_id'];
                                            $yearLevelQueryOperator = '>';

                                            if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                                $currentYearLevelID = $lastRegisteredYearLevelID;
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($generalSetting['GeneralSetting']['semesterCountForAcademicYear'] == 1) {
                                    if ($student_section_exam_status['StudentExamStatus']['semester'] == 'I' || $student_detail['CourseRegistration'][0]['semester'] == 'II' || $student_detail['CourseRegistration'][0]['semester'] == 'III') {
                                        if (!$prefreshStudent) {
                                            debug($student_detail['Section'][0]['year_level_id']);
                                            $currentYearLevelID = $student_detail['Section'][0]['year_level_id'];
                                            $yearLevelQueryOperator = '>=';

                                            if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                                $currentYearLevelID = $lastRegisteredYearLevelID;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if (isset($student_section_exam_status['StudentExamStatus']['academic_year']) && !empty($student_section_exam_status['StudentExamStatus']['academic_year']) && (is_null(
                                    $student_section_exam_status['StudentExamStatus']['academic_status_id']
                                ) || empty($student_section_exam_status['StudentExamStatus']['academic_status_id']))) {
                            // might not be dismissed but doesnt have status

                            $studentNeedsSectionAssignment = 0;

                            $possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange(
                                (isset($student_detail['CourseRegistration'][0]['academic_year']) && !empty($student_detail['CourseRegistration'][0]['academic_year']) ? $student_detail['CourseRegistration'][0]['academic_year'] : $student_detail['AcceptedStudent']['academicyear']),
                                $this->AcademicYear->current_academicyear()
                            );

                            $generalSetting = ClassRegistry::init(
                                'GeneralSetting'
                            )->getAllGeneralSettingsByStudentByProgramIdOrBySectionID($student_id);
                            //debug($generalSetting['GeneralSetting']['semesterCountForAcademicYear']);

                            //debug($student_detail);
                            //last registration
                            debug($student_detail['CourseRegistration'][0]);
                            debug($student_detail['CourseRegistration'][0]['academic_year']);
                            debug($student_detail['CourseRegistration'][0]['year_level_id']);

                            $lastRegisteredAcademicYear = $student_detail['CourseRegistration'][0]['academic_year'];
                            $lastRegisteredSemester = $student_detail['CourseRegistration'][0]['semester'];

                            if (!$prefreshStudent) {
                                $lastRegisteredYearLevelID = $student_detail['CourseRegistration'][0]['year_level_id'];
                            }

                            $alreadyGeneratedStatus = ClassRegistry::init('StudentExamStatus')->find('first', array(
                                'conditions' => array(
                                    'StudentExamStatus.student_id' => $student_id,
                                    'StudentExamStatus.academic_year' => $student_detail['CourseRegistration'][0]['academic_year'],
                                    'StudentExamStatus.semester' => $student_detail['CourseRegistration'][0]['semester'],
                                ),
                                'contain' => array(
                                    /* 'Student' => array(
								'College' => array('id', 'name'),
								'Department' => array('id', 'name'),
								'Program' => array('id', 'name'),
								'ProgramType' => array('id', 'name'),
								'fields' => array('id', 'full_name', 'gender', 'studentnumber', 'curriculum_id', 'graduated', 'academicyear', 'admissionyear'),
							), */
                                    'AcademicStatus' => array('id', 'name', 'computable'),
                                )
                            ));

                            if (empty($alreadyGeneratedStatus) && $student_detail['Student']['program_type_id'] == PROGRAM_TYPE_REGULAR) {
                                $statusGeneratedForLastRegistration = 0;
                            }


                            ////// Tweak $check_for_invalid_grades and PROGRAM_TYPE_REGULAR to bypass system checks to change section with out stutus generation checks   ///////

                            if ($this->Section->chceck_all_registered_added_courses_are_graded(
                                $student_id,
                                $section_id = null,
                                $check_for_invalid_grades = 1,
                                $from_student = '',
                                $skip_f_grade = 1
                            )) {
                                $studentNeedsSectionAssignment = 1;
                                if ($student_detail['Student']['program_type_id'] != PROGRAM_TYPE_REGULAR) {
                                    $statusGeneratedForLastRegistration = 1;
                                }
                            } else {
                                $student_have_invalid_grade = 1;
                            }


                            // for students failed Exit Exam for the first time

                            if ($student_detail['Student']['program_id'] == PROGRAM_UNDEGRADUATE && $isLastSemesterInCurriculum && !empty($msg) && is_array(
                                    $msg
                                ) && count($msg) == 1) {
                                $studentNeedsSectionAssignment = 1;
                                //$student_have_invalid_grade = 0; // Exit Exam
                            }


                            // for students only registered pass or fail grade type
                            $checkOnlyRegisteredPassFailGradeType = ClassRegistry::init(
                                'StudentExamStatus'
                            )->onlyRegisteredPassFailGradeType(
                                $student_id,
                                $student_detail['CourseRegistration'][0]['academic_year'],
                                $student_detail['CourseRegistration'][0]['semester']
                            );

                            if ($checkOnlyRegisteredPassFailGradeType) {
                                $studentNeedsSectionAssignment = 1;
                                $statusGeneratedForLastRegistration = 1;
                            }

                            ////// END Tweak $check_for_invalid_grades and PROGRAM_TYPE_REGULAR to bypass system checks to change section with out stutus generation checks   ///////


                            if ($generalSetting['GeneralSetting']['semesterCountForAcademicYear'] == 3) {
                                if ($student_section_exam_status['StudentExamStatus']['semester'] == 'I' || $student_section_exam_status['StudentExamStatus']['semester'] == 'II') {
                                    // no need to show next year one remaining semester
                                    if (!$prefreshStudent) {
                                        debug($student_detail['Section'][0]['year_level_id']);
                                        $currentYearLevelID = $student_detail['Section'][0]['year_level_id'];
                                        $yearLevelQueryOperator = '=';

                                        if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                            $currentYearLevelID = $lastRegisteredYearLevelID;
                                        }
                                    }
                                } else {
                                    if ($student_section_exam_status['StudentExamStatus']['semester'] == 'III') {
                                        // show only year ahead
                                        if (!$prefreshStudent) {
                                            debug($student_detail['Section'][0]['year_level_id']);
                                            $currentYearLevelID = $student_detail['Section'][0]['year_level_id'];
                                            $yearLevelQueryOperator = '>';

                                            if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                                $currentYearLevelID = $lastRegisteredYearLevelID;
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($generalSetting['GeneralSetting']['semesterCountForAcademicYear'] == 2) {
                                    if ($student_section_exam_status['StudentExamStatus']['semester'] == 'I') {
                                        // no need to show next year one remaining semester
                                        if (!$prefreshStudent) {
                                            debug($student_detail['Section'][0]['year_level_id']);
                                            $currentYearLevelID = $student_detail['Section'][0]['year_level_id'];
                                            $yearLevelQueryOperator = '=';

                                            if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                                $currentYearLevelID = $lastRegisteredYearLevelID;
                                            }
                                        }
                                    } else {
                                        if ($student_detail['CourseRegistration'][0]['semester'] == 'II') {
                                            if (!$prefreshStudent) {
                                                debug($student_detail['Section'][0]['year_level_id']);
                                                $currentYearLevelID = $student_detail['Section'][0]['year_level_id'];
                                                $yearLevelQueryOperator = '>';

                                                if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                                    $currentYearLevelID = $lastRegisteredYearLevelID;
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if ($generalSetting['GeneralSetting']['semesterCountForAcademicYear'] == 1) {
                                        if ($student_section_exam_status['StudentExamStatus']['semester'] == 'I' || $student_detail['CourseRegistration'][0]['semester'] == 'II' || $student_detail['CourseRegistration'][0]['semester'] == 'III') {
                                            if (!$prefreshStudent) {
                                                debug($student_detail['Section'][0]['year_level_id']);
                                                $currentYearLevelID = $student_detail['Section'][0]['year_level_id'];
                                                $yearLevelQueryOperator = '>=';

                                                if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                                    $currentYearLevelID = $lastRegisteredYearLevelID;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            //check for readmission
                            $is_student_dismissed = 1;
                            $statusGeneratedForLastRegistration = 1;
                            $studentNeedsSectionAssignment = 1;

                            $possibleReadmissionYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange(
                                $student_section_exam_status['StudentExamStatus']['academic_year'],
                                $this->AcademicYear->current_academicyear()
                            );


                            $readmitted = $this->Section->Student->Readmission->find('first', array(
                                'conditions' => array(
                                    'Readmission.student_id' => $student_id,
                                    'Readmission.registrar_approval' => 1,
                                    'Readmission.academic_commision_approval' => 1,
                                    'Readmission.academic_year' => $possibleReadmissionYears,
                                    /* 'OR' => array(
								'Readmission.academic_year' => $student_section_exam_status['StudentExamStatus']['academic_year'],
								'Readmission.semester' => $student_section_exam_status['StudentExamStatus']['semester'],
								'Readmission.registrar_approval_date' > $student_section_exam_status['StudentExamStatus']['modified'],
								'Readmission.modified' > $student_section_exam_status['StudentExamStatus']['modified'],
							) */
                                ),
                                'order' => array(
                                    'Readmission.academic_year' => 'DESC',
                                    'Readmission.semester' => 'DESC',
                                    'Readmission.modified' => 'DESC'
                                ),
                                'recursive' => -1,
                            ));

                            debug($student_section_exam_status['StudentExamStatus']);

                            if (count($readmitted)) {
                                debug($readmitted);
                                $lastReadmittedAcademicYear = $readmitted['Readmission']['academic_year'];
                                $lastReadmittedSemester = $readmitted['Readmission']['semester'];
                                $lastReadmittedDate = $readmitted['Readmission']['registrar_approval_date'];

                                //debug($lastReadmittedAcademicYear);

                                $is_student_readmitted = 1;
                                $possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange(
                                    $lastReadmittedAcademicYear,
                                    $this->AcademicYear->current_academicyear()
                                );
                                $studentNeedsSectionAssignment = 1;
                            }
                        }
                    }
                } else {
                    //have registration but doesnt have exam status
                    //$statusGeneratedForLastRegistration = 0;
                    //debug($student_section_exam_status);
                }
            }
        }

        $acYrStart = !empty($lastReadmittedAcademicYear) ? str_replace(
            '-',
            '/',
            $lastReadmittedAcademicYear
        ) : (!empty($lastRegisteredAcademicYear) ? $lastRegisteredAcademicYear : $student_detail['Student']['academicyear']);

        if (!empty($acYrStart)) {
            $acYrStart = str_replace('-', '/', $acYrStart);
            debug($acYrStart);
            $possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange(
                $acYrStart,
                $this->AcademicYear->current_academicyear()
            );
        }

        if (empty($possibleAcademicYears)) {
            $possibleAcademicYears[$this->AcademicYear->current_academicyear(
            )] = $this->AcademicYear->current_academicyear();
        }

        debug($possibleAcademicYears);

        if (!$prefreshStudent || $studentMustHaveCurriculum) {
            debug($student_detail['Student']['curriculum_id']);

            if (is_numeric(
                    $student_detail['Student']['curriculum_id']
                ) && $student_detail['Student']['curriculum_id'] > 0) {
                $courseYearLevels = $this->Section->Curriculum->Course->find('list', array(
                    'conditions' => array(
                        'Course.curriculum_id' => $student_detail['Student']['curriculum_id']
                    ),
                    'fields' => array('Course.year_level_id', 'Course.year_level_id'),
                    'group' => array('Course.year_level_id', 'Course.year_level_id'),
                    'order' => array('Course.year_level_id' => 'DESC'),
                ));

                if (!empty($courseYearLevels)) {
                    $curriculumYearLevels = array_keys($courseYearLevels);
                }

                $student_attached_curriculum = $this->Section->Curriculum->find(
                    'first',
                    array(
                        'conditions' => array('Curriculum.id' => $student_detail['Student']['curriculum_id']),
                        'recursive' => -1
                    )
                );

                if (!empty($student_attached_curriculum)) {
                    debug($student_attached_curriculum['Curriculum']['curriculum_detail']);
                    $student_attached_curriculum_name = $student_attached_curriculum['Curriculum']['curriculum_detail'];
                }
            }

            debug($curriculumYearLevels);

            if (!empty($curriculumYearLevels) && isset($student_detail['Section'][0]) && (!is_numeric(
                        $student_detail['Section'][0]['year_level_id']
                    ) || empty($student_detail['Section'][0]['year_level_id']))) {
                asort(
                    $curriculumYearLevels
                ); // Ascending Order (by values) (asort) // Descending Order (by values) (arsort)
                $curriculumYearLevelsSortedASC = array_values($curriculumYearLevels);
                debug($curriculumYearLevelsSortedASC);

                if (isset($curriculumYearLevelsSortedASC[0])) {
                    $currentYearLevelID = $curriculumYearLevelsSortedASC[0];
                }

                if ($student_detail['Student']['program_id'] == PROGRAM_UNDEGRADUATE) {
                    $all_pre_freshman_remedial_college_ids = Configure::read('all_pre_freshman_remedial_college_ids');
                    $program_types_available_pre_freshman = Configure::read(
                        'program_types_available_for_registrar_college_level_permissions'
                    );
                    debug($program_types_available_pre_freshman);

                    if (!empty($all_pre_freshman_remedial_college_ids) && isset($student_detail['Section'][0]['college_id']) && empty($student_detail['Section'][0]['department_id']) && in_array(
                            $student_detail['Section'][0]['college_id'],
                            $all_pre_freshman_remedial_college_ids
                        )) {
                        debug($all_pre_freshman_remedial_college_ids);
                        if (isset($curriculumYearLevelsSortedASC[1]) && !empty($curriculumYearLevelsSortedASC[1])) {
                            $yearLevelQueryOperator = '=';
                            $currentYearLevelID = $curriculumYearLevelsSortedASC[1];
                        }
                    } else {
                        if (isset($student_detail['Section'][0]['college_id']) && empty($student_detail['Section'][0]['department_id']) && in_array(
                                $student_detail['Student']['program_type_id'],
                                $program_types_available_pre_freshman
                            )) {
                            if (isset($curriculumYearLevelsSortedASC[1]) && !empty($curriculumYearLevelsSortedASC[1])) {
                                $yearLevelQueryOperator = '=';
                                $currentYearLevelID = $curriculumYearLevelsSortedASC[1];
                            }
                        } else {
                            if (isset($student_detail['Section'][0]['college_id']) && empty($student_detail['Section'][0]['department_id']) && $student_detail['Student']['program_type_id'] == PROGRAM_TYPE_REGULAR) {
                                if (isset($curriculumYearLevelsSortedASC[1]) && !empty($curriculumYearLevelsSortedASC[1])) {
                                    $yearLevelQueryOperator = '=';
                                    $currentYearLevelID = $curriculumYearLevelsSortedASC[1];
                                }
                            } else {
                                $yearLevelQueryOperator = '=';
                            }
                        }
                    }
                }
            } else {
                if (!empty($student_detail['CourseRegistration'][0]['year_level_id']) && !empty($student_detail['CourseRegistration'][0]['year_level_id'])) {
                    $studentNeedsSectionAssignment = 1;
                    $currentYearLevelID = $student_detail['CourseRegistration'][0]['year_level_id'];
                    $yearLevelQueryOperator = '>=';
                }
            }
        }

        debug($yearLevelQueryOperator);
        debug($currentYearLevelID);

        $sectionOrganized = array();
        $nextYearLevelName = '';

        if (!$prefreshStudent && !empty($student_detail['Student']['department_id']) && is_numeric(
                $student_detail['Student']['department_id']
            ) && $student_detail['Student']['department_id'] > 0) {
            $nextYearLevelID = $currentYearLevelID;

            if ($yearLevelQueryOperator === '=') {
                $yearLevelsProfile = $yearLevels = $this->Section->YearLevel->find(
                    'list',
                    array(
                        'conditions' => array(
                            "YearLevel.id" => $currentYearLevelID,
                            'YearLevel.department_id' => $student_detail['Student']['department_id']
                        ),
                        'fields' => array('YearLevel.name', 'YearLevel.name')
                    )
                );
                $nextYearLevelID = $this->Section->YearLevel->field(
                    'YearLevel.id',
                    array(
                        "YearLevel.id" => $currentYearLevelID,
                        'YearLevel.department_id' => $student_detail['Student']['department_id']
                    )
                );
            } else {
                if (isset($curriculumYearLevels[0])) {
                    $yearLevelsProfile = $yearLevels = $this->Section->YearLevel->find(
                        'list',
                        array(
                            'conditions' => array(
                                "YearLevel.id $yearLevelQueryOperator " => $currentYearLevelID,
                                "YearLevel.id <= " => $curriculumYearLevels[0],
                                'YearLevel.department_id' => $student_detail['Student']['department_id']
                            ),
                            'fields' => array('YearLevel.name', 'YearLevel.name'),
                            'order' => array('YearLevel.id' => 'ASC')
                        )
                    );
                    $nextYearLevelID = $this->Section->YearLevel->find(
                        'list',
                        array(
                            'conditions' => array(
                                "YearLevel.id $yearLevelQueryOperator " => $currentYearLevelID,
                                "YearLevel.id <= " => $curriculumYearLevels[0],
                                'YearLevel.department_id' => $student_detail['Student']['department_id']
                            ),
                            'fields' => array('YearLevel.id', 'YearLevel.id'),
                            'order' => array('YearLevel.id' => 'ASC')
                        )
                    );
                    if (!empty($nextYearLevelID)) {
                        $nextYearLevelID = array_values($nextYearLevelID)[0];
                    } else {
                        $nextYearLevelID = $currentYearLevelID;
                    }
                } else {
                    $yearLevelsProfile = $yearLevels = $this->Section->YearLevel->find(
                        'list',
                        array(
                            'conditions' => array(
                                "YearLevel.id $yearLevelQueryOperator " => $currentYearLevelID,
                                'YearLevel.department_id' => $student_detail['Student']['department_id']
                            ),
                            'fields' => array('YearLevel.name', 'YearLevel.name'),
                            'order' => array('YearLevel.id' => 'ASC')
                        )
                    );
                    $nextYearLevelID = $this->Section->YearLevel->find(
                        'list',
                        array(
                            'conditions' => array(
                                "YearLevel.id $yearLevelQueryOperator " => $currentYearLevelID,
                                'YearLevel.department_id' => $student_detail['Student']['department_id']
                            ),
                            'fields' => array('YearLevel.id', 'YearLevel.id'),
                            'order' => array('YearLevel.id' => 'ASC')
                        )
                    );
                    if (!empty($nextYearLevelID)) {
                        $nextYearLevelID = array_values($nextYearLevelID)[0];
                    } else {
                        $nextYearLevelID = $currentYearLevelID;
                    }
                }
            }

            debug($nextYearLevelID);
            $nextYearLevelName = $this->Section->YearLevel->field('name', array('id' => $nextYearLevelID));

            if (count($yearLevelsProfile) == 1) {
                $currentYearLevelIDName = array_values($yearLevelsProfile)[0] . ' year';
            } else {
                $currentYearLevelIDName = array_values($yearLevelsProfile);
                $currentYearLevelIDName = implode("  year, ", $currentYearLevelIDName) . ' year';
            }

            $sectionOrganized = $this->Section->find('all', array(
                'conditions' => array(
                    'Section.academicyear' => $possibleAcademicYears,
                    'Section.program_id' => $student_detail['Student']['program_id'],
                    'Section.program_type_id' => $program_types_to_look,
                    'Section.department_id' => $student_detail['Student']['department_id'],
                    "Section.year_level_id" => $nextYearLevelID,
                    'Section.curriculum_id' => $student_detail['Student']['curriculum_id'],
                    'Section.archive' => 0
                ),
                'contain' => array(
                    'YearLevel' => array('id', 'name'),
                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                ),
                'order' => array(
                    'Section.academicyear' => 'ASC',
                    'Section.year_level_id' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                ),
                'recursive' => -1
            ));
        } else {
            $yearLevelsProfile[0] = $yearLevels[0] = 'Pre/Freshman/Remedial';
            $currentYearLevelIDName = 'Pre/Freshman/Remedial';

            $sectionOrganized = $this->Section->find('all', array(
                'conditions' => array(
                    'Section.academicyear' => $possibleAcademicYears,
                    'Section.program_id' => $student_detail['Student']['program_id'],
                    'Section.program_type_id' => $program_types_to_look,
                    'Section.department_id is null',
                    'Section.college_id' => $student_detail['Student']['college_id'],
                    'Section.archive' => 0
                ),
                'contain' => array('YearLevel'),
                'order' => array(
                    'Section.academicyear' => 'ASC',
                    'Section.year_level_id' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                ),
                'recursive' => -1
            ));
        }

        debug($checkOnlyRegisteredPassFailGradeType);
        debug($sectionOrganized);
        debug($nextYearLevelName);
        //$statusGeneratedForLastRegistration = 1;

        // Set Display Sections Search Paramenters to session variable

        if (empty($sectionOrganized) && ($this->role_id = ROLE_COLLEGE || $this->role_id = ROLE_DEPARTMENT)) {
            $displaySectionsSearchFilter['Section']['academicyear'] = end($possibleAcademicYears);
            $displaySectionsSearchFilter['Section']['program_id'] = $student_detail['Student']['program_id'];
            $displaySectionsSearchFilter['Section']['program_type_id'] = $student_detail['Student']['program_type_id'];

            if (isset($nextYearLevelID) && !empty($nextYearLevelID) && is_array($nextYearLevelID)) {
                $nextYearLevelID = array_values($nextYearLevelID)[0];
            }

            debug($nextYearLevelID);

            if (isset($currentYearLevelID) && !empty($currentYearLevelID)) {
                $displaySectionsSearchFilter['Section']['year_level_id'] = (isset($nextYearLevelID) && !empty($nextYearLevelID) ? $nextYearLevelID : $currentYearLevelID);
            }

            debug($displaySectionsSearchFilter);

            if (!empty($displaySectionsSearchFilter['Section']['academicyear'])) {
                $this->Session->write('search_sections', $displaySectionsSearchFilter['Section']);
            }
        }

        $this->set(
            compact(
                'sectionOrganized',
                'student_detail',
                'yearLevels',
                'yearLevelsProfile',
                'is_student_dismissed',
                'last_student_status',
                'studentNeedsSectionAssignment',
                'is_student_readmitted',
                'studentMustHaveCurriculum',
                'statusGeneratedForLastRegistration',
                'prefreshStudent',
                'lastRegisteredYearLevelID',
                'lastRegisteredAcademicYear',
                'lastRegisteredSemester',
                'lastReadmittedAcademicYear',
                'lastReadmittedSemester',
                'lastReadmittedDate',
                'possibleAcademicYears',
                'student_attached_curriculum_name',
                'currentYearLevelIDName',
                'student_have_invalid_grade',
                'msg',
                'isLastSemesterInCurriculum',
                'lastRegisteredYearLevelName',
                'nextYearLevelName'
            )
        );
    }

    public function add_student_section($section_id = null)
    {

        $this->layout = 'ajax';

        $section_detail = $this->Section->find('first', array(
            'conditions' => array(
                'Section.id' => $section_id
            ),
            'contain' => array(
                'YearLevel' => array('id', 'name'),
                'Program' => array('id', 'name'),
                'ProgramType' => array('id', 'name', 'equivalent_to_id'),
                'Department' => array('id', 'name', 'college_id', 'type'),
                'College' => array('id', 'name', 'shortname', 'campus_id', 'type', 'stream'),
                'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                'Student' => array(
                    'fields' => array(
                        'Student.id',
                        'Student.studentnumber',
                        'Student.full_name',
                        'curriculum_id',
                        'Student.gender',
                        'Student.graduated'
                    ),
                    'conditions' => array(
                        'Student.graduated = 0'
                    ),
                    'limit' => 1,
                ),
            )
        ));

        //debug($section_detail);

        $isSectionEmpty = 1;

        $section_program_id = $section_detail['Section']['program_id'];
        $section_program_type_id = $section_detail['Section']['program_type_id'];
        $section_academic_year = $section_detail['Section']['academicyear'];
        $sectionsCurriculumID = 0;

        //debug($section_academic_year);

        if (isset($section_detail['Curriculum']['id']) && !empty($section_detail['Curriculum']['id']) && $section_detail['Curriculum']['id']) {
            $sectionsCurriculumID = $section_detail['Curriculum']['id'];
            if (!empty($section_detail['Student'])) {
                $isSectionEmpty = 0;
            }
        } else {
            if (empty($section_detail['Student'])) {
                //$isSectionEmpty = 1;
            } else {
                $sectionsCurriculumID = $this->Section->sectionscurriculumID($section_id);
            }
        }

        //debug($section_program_type_id);
        $program_type_id = $this->Section->getEquivalentProgramTypes($section_program_type_id);
        //debug($program_type_id);

        $program_type_id = "'" . implode("', '", $program_type_id) . "'";

        if (!empty(explode('/', $section_detail['Section']['academicyear'])[0])) {
            debug(explode('/', $section_detail['Section']['academicyear'])[0]);
            $admission_years = $this->AcademicYear->academicYearInArray(
                date('Y') - ACY_BACK_FOR_SECTION_LESS,
                explode('/', $section_detail['Section']['academicyear'])[0]
            );
        } else {
            $admission_years = $this->AcademicYear->academicYearInArray(
                date('Y') - ACY_BACK_FOR_SECTION_LESS,
                date('Y')
            );
        }

        $admission_years = array_keys($admission_years);
        debug($admission_years);
        $admission_years_by_comma = "'" . implode("', '", $admission_years) . "'";

        $student_list_ids = array();

        if ($this->role_id == ROLE_DEPARTMENT) {
            //debug($sectionsCurriculumID);

            if ((isset($sectionsCurriculumID[0]) && $sectionsCurriculumID[0] == -2) || $isSectionEmpty || $sectionsCurriculumID == 0) {
                //debug($sectionsCurriculumID[0]);
                //$query = "SELECT id FROM students WHERE id NOT IN (SELECT student_id FROM students_sections WHERE archive = 0 GROUP BY student_id, section_id) AND  program_id = " . $section_program_id . " AND  program_type_id in (" . $program_type_id . ") AND curriculum_id IS NOT NULL AND  department_id = " . $this->department_id . " AND graduated = 0";
                $query = "SELECT id FROM students WHERE id NOT IN (SELECT student_id FROM students_sections WHERE archive = 0 GROUP BY student_id, section_id) AND  program_id = " . $section_program_id . " AND  program_type_id in (" . $program_type_id . ") AND curriculum_id IS NOT NULL AND academicyear in (" . $admission_years_by_comma . ") AND  department_id = " . $this->department_id . " AND graduated = 0";
            } else {
                if (($sectionsCurriculumID != 0 || $sectionsCurriculumID != -1)) {
                    //$query = "SELECT  id, studentnumber FROM  students WHERE id NOT IN (SELECT student_id FROM students_sections WHERE archive = 0 GROUP BY student_id, section_id ) AND  program_type_id in (" . join(',', $program_type_id) . ") AND curriculum_id IS NOT NULL AND  department_id = " . $this->department_id . " AND id NOT IN (SELECT student_id FROM senate_lists)";
                    $query = "SELECT  id  FROM  students WHERE id NOT IN (SELECT student_id FROM students_sections WHERE archive = 0 GROUP BY student_id, section_id) AND  program_id = " . $section_program_id . " AND  program_type_id in (" . $program_type_id . ") AND curriculum_id = " . $sectionsCurriculumID . " AND  department_id = " . $this->department_id . " AND graduated = 0";
                }
            } /* else if ($sectionsCurriculumID == 0 || $isSectionEmpty) {
				$query = "SELECT id FROM students WHERE id NOT IN (SELECT student_id FROM students_sections WHERE archive = 0 GROUP BY student_id, section_id) AND  program_id = " . $section_program_id . " AND  program_type_id in (" . $program_type_id . ") AND  department_id = " . $this->department_id . " AND graduated = 0";
			}  */

            $queryResult = $this->Section->query($query);
            //debug($queryResult);

            if (!empty($queryResult)) {
                foreach ($queryResult as $k => $v) {
                    $student_list_ids[] = $v['students']['id'];
                }
            }

            //debug($student_list_ids);

            if (!empty($student_list_ids)) {
                if ((isset($sectionsCurriculumID[0]) && $sectionsCurriculumID[0] == -2) || $isSectionEmpty) {
                    $conditions = array(
                        'Student.department_id' => $this->department_id,
                        'Student.id' => $student_list_ids,
                        'Student.program_id' => $section_program_id,
                        'Student.program_type_id IN (' . $program_type_id . ')',
                        'OR' => array(
                            'Student.curriculum_id IS NOT NULL',
                            'Student.curriculum_id' => $sectionsCurriculumID,
                        ),
                        'Student.graduated = 0'
                    );
                } else {
                    $conditions = array(
                        'Student.department_id' => $this->department_id,
                        'Student.id' => $student_list_ids,
                        'Student.program_id' => $section_program_id,
                        'Student.program_type_id IN (' . $program_type_id . ')',
                        'Student.curriculum_id' => $sectionsCurriculumID,
                        'Student.graduated = 0'
                    );
                }
            }

            //debug($conditions);

            $this->set(compact('sectionsCurriculumID'));
        } else {
            $sectionsCurriculumID = -1;

            $this->set(compact('sectionsCurriculumID'));

            $query = "SELECT id FROM students WHERE id NOT IN (SELECT student_id FROM students_sections WHERE archive = 0 GROUP BY student_id, section_id) AND  program_id = " . $section_program_id . " AND  program_type_id in (" . $program_type_id . ") AND  academicyear in (" . $admission_years_by_comma . ") AND college_id = " . $this->college_id . " AND department_id IS NULL AND graduated = 0";
            $queryResult = $this->Section->query($query);

            //debug($queryResult);
            $student_list_ids = array();

            if (!empty($queryResult)) {
                foreach ($queryResult as $k => $v) {
                    $student_list_ids[] = $v['students']['id'];
                }
            }

            if (!empty($student_list_ids)) {
                $conditions = array(
                    'Student.college_id' => $this->college_id,
                    'Student.department_id is null',
                    'Student.program_id' => $section_program_id,
                    'Student.id' => $student_list_ids,
                    'Student.program_type_id IN (' . $program_type_id . ')',
                );
            }
        }

        if (isset($conditions) && !empty($conditions)) {
            //$this->Section->Student->bindModel(array('hasMany' => array('StudentsSection' => array('conditions' => array('StudentsSection.archive' => 1)))));

            $students = $this->Section->Student->find('all', array(
                'conditions' => $conditions,
                'fields' => array(
                    'Student.id',
                    'Student.full_name',
                    'Student.studentnumber',
                    'Student.gender',
                    'Student.graduated'
                ),
                'contain' => array(
                    //'StudentsSection',
                    'Section' => array(
                        'conditions' => array(
                            'Section.academicyear' => $section_academic_year,
                            'Section.archive' => 1,
                        ),
                        'fields' => array(
                            'Section.id',
                            'Section.name',
                            'Section.academicyear',
                            'Section.curriculum_id'
                        ),
                        'YearLevel' => array(
                            'fields' => array(
                                'YearLevel.id',
                                'YearLevel.name',
                            ),
                        ),
                        'Department' => array('id', 'name', 'college_id', 'type'),
                        'College' => array('id', 'name', 'shortname', 'campus_id', 'type', 'stream'),
                        'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                    ),
                    'Department' => array('id', 'name', 'college_id', 'type'),
                    'College' => array('id', 'name', 'shortname', 'campus_id', 'type', 'stream'),
                ),
                'order' => array(
                    'Student.admissionyear' => 'ASC',
                    'Student.first_name' => 'ASC',
                    'Student.middle_name' => 'ASC',
                    'Student.last_name' => 'ASC',
                    'Student.program_type_id' => 'ASC'
                ),
                'revursive' => -1
            ));
        }

        //debug($students);
        //debug($conditions);

        $this->set(
            compact(
                'section_id',
                'sectionless_student',
                'students',
                'section_detail'
            )
        );
    }

    public function add_student_section_update()
    {

        if (!empty($this->request->data)) {
            $new_section_detail = $this->Section->find(
                'first',
                array(
                    'conditions' => array('Section.id' => $this->request->data['Section']['section_id']),
                    'contain' => array('YearLevel')
                )
            );
            //Find student name,  section name for display purpose
            $student_full_name = $this->Section->Student->field(
                'Student.full_name',
                array('Student.id' => $this->request->data['Section']['Selected_student_id'])
            );
            $section_name = $this->Section->field(
                'Section.name',
                array('Section.id' => $this->request->data['Section']['section_id'])
            );
            $studentYearLevel = ClassRegistry::init('StudentExamStatus')->studentYearAndSemesterLevel(
                $this->request->data['Section']['Selected_student_id']
            );

            if (!empty($studentYearLevel) && $new_section_detail['YearLevel']['name'] != $studentYearLevel['year']) {
                $this->Flash->error(
                    '' . $student_full_name . ' is not added to ' . $section_name . ' Because the target section is ' . $new_section_detail['YearLevel']['name'] . ' year while the student is ' . $studentYearLevel['year'] . ' year.'
                );
                $this->redirect(array('action' => 'display_sections', $this->request->data['Section']['section_id']));
            }

            //Check student status whether he/she qualify to have section
            if ($this->Section->Student->StudentExamStatus->get_student_exam_status(
                $this->request->data['Section']['Selected_student_id']
            )) {
                //Find this section curriculum
                $section_curriculum = $this->Section->get_section_curriculum(
                    $this->request->data['Section']['section_id']
                );

                if (!empty($section_curriculum)) {
                    //find student curriculum
                    $student_curriculum = $this->Section->Student->field(
                        'Student.curriculum_id',
                        array('Student.id' => $this->request->data['Section']['Selected_student_id'])
                    );

                    if ($section_curriculum == $student_curriculum) {
                        //save student to new section to associate table
                        $this->request->data['StudentsSection']['student_id'] = $this->request->data['Section']['Selected_student_id'];
                        $this->request->data['StudentsSection']['section_id'] = $this->request->data['Section']['section_id'];

                        //To check whether the record is already there as archive, if so just turnoff the archive is enough
                        $already_recorded_id = $this->_check_the_record_in_archive(
                            $this->request->data['StudentsSection']['section_id'],
                            $this->request->data['StudentsSection']['student_id']
                        );

                        if (!empty($already_recorded_id)) {
                            $this->Section->StudentsSection->id = $already_recorded_id;
                            $this->Section->StudentsSection->saveField('archive', '0');
                        } else {
                            $this->Section->StudentsSection->save($this->request->data['StudentsSection']);
                        }

                        $this->Flash->success('' . $student_full_name . ' is Added to Section ' . $section_name . '.');
                        return $this->redirect(
                            array('action' => 'display_sections', $this->request->data['Section']['section_id'])
                        );
                    } else {
                        //Find student name,  section name for display purpose
                        $student_full_name = $this->Section->Student->field(
                            'Student.full_name',
                            array('Student.id' => $this->request->data['Section']['Selected_student_id'])
                        );
                        $section_name = $this->Section->field(
                            'Section.name',
                            array('Section.id' => $this->request->data['Section']['section_id'])
                        );
                        $section_curriculum_name = $this->Section->Student->Curriculum->field(
                            'Curriculum.curriculum_detail',
                            array('Curriculum.id' => $section_curriculum)
                        );
                        $student_curriculum_name = $this->Section->Student->Curriculum->field(
                            'Curriculum.curriculum_detail',
                            array('Curriculum.id' => $student_curriculum)
                        );

                        $this->Flash->error(
                            '' . $student_full_name . ' will not be added to Section ' . $section_name . '. That\'s because, he/she studies by ' . $student_curriculum_name . ' curriculum, which is different from Section ' . $section_name . ' curiculum ' . $section_curriculum_name . '.'
                        );
                        return $this->redirect(
                            array('action' => 'display_sections', $this->request->data['Section']['section_id'])
                        );
                    }
                } else {
                    //save student to new section to associate table

                    $this->request->data['StudentsSection']['student_id'] = $this->request->data['Section']['Selected_student_id'];
                    $this->request->data['StudentsSection']['section_id'] = $this->request->data['Section']['section_id'];
                    //To check whether the record is already there as archive, if so just turnoff the archive is enough
                    $already_recorded_id = $this->_check_the_record_in_archive(
                        $this->request->data['StudentsSection']['section_id'],
                        $this->request->data['StudentsSection']['student_id']
                    );

                    if (!empty($already_recorded_id)) {
                        $this->Section->StudentsSection->id = $already_recorded_id;
                        $this->Section->StudentsSection->saveField('archive', '0');
                    } else {
                        $this->Section->StudentsSection->save($this->request->data['StudentsSection']);
                    }

                    $student_full_name = $this->Section->Student->field(
                        'Student.full_name',
                        array('Student.id' => $this->request->data['Section']['Selected_student_id'])
                    );
                    $section_name = $this->Section->field(
                        'Section.name',
                        array('Section.id' => $this->request->data['Section']['section_id'])
                    );
                    $this->Flash->success('' . $student_full_name . ' is Added to Section ' . $section_name . '.');
                    return $this->redirect(
                        array('action' => 'display_sections', $this->request->data['Section']['section_id'])
                    );
                }
            } else {
                $student_full_name = $this->Section->Student->field(
                    'Student.full_name',
                    array('Student.id' => $this->request->data['Section']['Selected_student_id'])
                );
                $this->Flash->error('' . $student_full_name . ' fails to qualify to be added to a section.');
                $this->redirect(array('action' => 'display_sections', $this->request->data['Section']['section_id']));
            }
        }
    }

    public function mass_student_section_add()
    {

        if (!empty($this->request->data)) {
            $new_section_detail = $this->Section->find('first', array(
                'conditions' => array(
                    'Section.id' => $this->request->data['SectionDetail']['section_id']
                ),
                'contain' => array(
                    'YearLevel' => array('id', 'name'),
                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                )
            ));

            if (isset($new_section_detail['Curriculum']['id']) && is_numeric(
                    $new_section_detail['Curriculum']['id']
                ) && $new_section_detail['Curriculum']['id']) {
                $section_curriculum = $new_section_detail['Curriculum']['id'];
            } else {
                $section_curriculum = $this->Section->get_section_curriculum(
                    $this->request->data['SectionDetail']['section_id']
                );
            }

            $failSuccess['success'] = 0;
            $failSuccess['error'] = 0;
            $fresh = false;
            $failReason = '<ul>';

            //debug($this->request->data);

            if (isset($this->request->data['Section']) && !empty($this->request->data['Section'])) {
                foreach ($this->request->data['Section'] as $k => $v) {
                    if (is_numeric($k)) {
                        if ($v['selected_id'] == 1) {
                            $studentAdds = array();

                            $studentNameAndID = $this->Section->Student->find('first', array(
                                'conditions' => array(
                                    'Student.id' => $v['student_id']
                                ),
                                'fields' => array(
                                    'first_name',
                                    'middle_name',
                                    'last_name',
                                    'studentnumber',
                                    'curriculum_id',
                                    'college_id',
                                    'department_id',
                                    'academicyear',
                                    'graduated'
                                ),
                                'recursive' => -1,
                            ));

                            $studentnumber = $studentNameAndID['Student']['first_name'] . ' ' . $studentNameAndID['Student']['middle_name'] . ' ' . $studentNameAndID['Student']['last_name'] . ' (' . $studentNameAndID['Student']['studentnumber'] . ')';

                            if ($this->role_id == ROLE_DEPARTMENT) {
                                $student_curriculum = $studentNameAndID['Student']['curriculum_id'];
                                $studentYearLevel = ClassRegistry::init(
                                    'StudentExamStatus'
                                )->studentYearAndSemesterLevel($v['student_id']);
                            } else {
                                $studentYearLevel = 'Pre/1st';
                                $section_curriculum = -1;
                                $student_curriculum = -1;
                                $fresh = true;
                            }
                            //debug($fresh);

                            if ((isset($new_section_detail['YearLevel']['name']) && $new_section_detail['YearLevel']['name'] == $studentYearLevel['year'] && $this->Section->Student->StudentExamStatus->get_student_exam_status(
                                        $v['student_id']
                                    ) && ($student_curriculum == $section_curriculum || $section_curriculum == "nostudentinsection")) || $fresh) {
                                //To check whether the record is already there as archive, if so just turnoff the archive is enough
                                $already_recorded_id = $this->_check_the_record_in_archive(
                                    $v['section_id'],
                                    $v['student_id']
                                );

                                if ($already_recorded_id) {
                                    $this->Section->StudentsSection->id = $already_recorded_id;
                                    $this->Section->StudentsSection->saveField('archive', '0');
                                    $failSuccess['success'] = $failSuccess['success'] + 1;
                                } else {
                                    $studentAdds['StudentsSection']['student_id'] = $v['student_id'];
                                    $studentAdds['StudentsSection']['section_id'] = $v['section_id'];
                                    $this->Section->StudentsSection->create();
                                    $this->Section->StudentsSection->save($studentAdds['StudentsSection']);
                                    $failSuccess['success'] = $failSuccess['success'] + 1;
                                }

                                if ((is_null(
                                            $new_section_detail['Curriculum']['id']
                                        ) || empty($new_section_detail['Curriculum']['id'])) && !$fresh) {
                                    if (isset($section_curriculum) && is_numeric(
                                            $section_curriculum
                                        ) && $section_curriculum > 3) {
                                        $this->Section->id = $v['section_id'];
                                        $this->Section->saveField('curriculum_id', $section_curriculum);
                                    } else {
                                        if (isset($student_curriculum) && is_numeric(
                                                $student_curriculum
                                            ) && $student_curriculum > 3) {
                                            $this->Section->id = $v['section_id'];
                                            $this->Section->saveField('curriculum_id', $student_curriculum);
                                        }
                                    }
                                }
                            } else {
                                $failSuccess['error'] = $failSuccess['error'] + 1;
                                $failReason = '<br>';
                                if ($new_section_detail['YearLevel']['name'] != $studentYearLevel['year']) {
                                    $failReason .= '<li> ' . $new_section_detail['Section']['name'] . ' section\'s year level is  ' . $new_section_detail['YearLevel']['name'] . ' but,  ' . $studentnumber . ' is in ' . $studentYearLevel['year'] . ' year. </li>';
                                }
                                if (($student_curriculum != $section_curriculum)) {
                                    $student_curriculum_name = $this->Section->Curriculum->field(
                                        'Curriculum.name',
                                        array('Curriculum.id' => $student_curriculum)
                                    );
                                    $section_curriculum_name = $this->Section->Curriculum->field(
                                        'Curriculum.name',
                                        array('Curriculum.id' => $section_curriculum)
                                    );
                                    $failReason .= '<li> ' . $new_section_detail['Section']['name'] . ' section\'s  curriculum is "' . $section_curriculum_name . '" but, ' . $studentnumber . ' is attached to "' . $student_curriculum_name . '" Curriculum.</li>';
                                }
                            }
                        }
                    }
                }
            }

            $failReason .= '</ul>';

            if ($failSuccess['success']) {
                $this->Flash->success(
                    ($failSuccess['success'] == 1 ? $studentnumber . ' has been' : $failSuccess['success'] . ' Students have been') . ' added to ' . $new_section_detail['Section']['name'] . ' Section Successfully.'
                );
                return $this->redirect(array('action' => 'display_sections', $new_section_detail['Section']['id']));
            } else {
                if ($failSuccess['error']) {
                    $this->Session->setFlash(
                        __(
                            '<span style="margin-right: 15px;"></span> Not able to add to the section with the following reason ' . $failReason . '',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    return $this->redirect(
                        array('action' => 'display_sections', $this->request->data['SectionDetail']['section_id'])
                    );
                }
            }
            return $this->redirect(
                array('action' => 'display_sections', $this->request->data['SectionDetail']['section_id'])
            );
        }
    }

    public function add_student_prev_section()
    {

        if (!empty($this->request->data)) {
            // Update is needed here!
            /* $selectedStudentDetail = ClassRegistry::init('Student')->find('first', array('conditions' => array('Student.id' => $this->request->data['Section']['Selected_student_id']), 'recursive' => -1));
			$selectedSectionForAdd = array();
			$updateStudentsCurriculumBasedOnCurrentSection = array();
			$updateAcceptedStudentsCurriculumBasedOnCurrentSection = array();
			$count = 0;

			$studID = '';
			$sectionID = '';

			$sectionCurriculumAttachedToSudentWhenAdding = 0;

			debug($this->request->data);

			$selected_student_attached_curriculum = $selectedStudentDetail['Student']['curriculum_id'];

			//$this->Section->Student->bindModel(array('hasMany' => array('StudentsSection' => array('conditions' => array('StudentsSection.student_id' => $this->request->data['Section']['Selected_student_id']), 'group' => array('StudentsSection.student_id', 'StudentsSection.section_id')))));

			$selected_student_sections = $this->Section->Student->find('all', array(
				'conditions' => array(
					'Student.id' => $this->request->data['Section']['Selected_student_id'],
				),
				'fields' => array(
					'Student.id',
					'Student.studentnumber',
					'Student.academicyear',
					'Student.graduated',
				),
				'contain' => array(
					'Section' => array(
						'fields' => array(
							'Section.id',
							'Section.name',
							'Section.academicyear',
							'Section.curriculum_id',
							'Section.created',
							'Section.modified',
						),
						'StudentsSection' => array(
							'fields' => array(
								'StudentsSection.id',
								'StudentsSection.student_id',
								'StudentsSection.section_id',
								'StudentsSection.archive',
								'StudentsSection.created',
								'StudentsSection.modified',
							),
							'order' => array('StudentsSection.modified DESC'),
						),
						'YearLevel' => array(
							'fields' => array(
								'YearLevel.id',
								'YearLevel.name',
							),
						),
						'order' => array('Section.created DESC'),
					)
				),
				'revursive' => -1,
			));

			debug($selected_student_sections);

			debug($selected_student_sections[0]['Section'][0]['StudentsSection']['archive']);

			if (!empty($selected_student_sections[0]['Section'][0])) {
				$students_last_section_acy = $selected_student_sections[0]['Section'][0]['academicyear'];
			} else {
				$students_last_section_acy = $this->AcademicYear->current_academicyear();
			}

			debug($students_last_section_acy);

			$previousStudentsSection = $this->Section->StudentsSection->find('list', array(
				'conditions' => array(
					'StudentsSection.student_id' => $this->request->data['Section']['Selected_student_id'],
					'StudentsSection.archive' => 1
				),
				'group' => array(
					'StudentsSection.student_id',
					'StudentsSection.section_id'
				),
				'fields' => array(
					'StudentsSection.section_id',
					'StudentsSection.section_id'
				)
			)); */

            if (!empty($this->request->data['Section']['assigned_section'])) {
                debug(count($this->request->data['Section']['assigned_section']));

                if (count($this->request->data['Section']['assigned_section']) == 1) {
                    debug($this->request->data['Section']['assigned_section']);

                    $student_detail = $this->Section->Student->find('first', array(
                        'conditions' => array(
                            'Student.id' => $this->request->data['Section']['Selected_student_id']
                        ),
                        'contain' => array(
                            'AcceptedStudent' => array('id', 'studentnumber', 'academicyear'),
                            'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                            'Section' => array(
                                'fields' => array(
                                    'Section.id',
                                    'Section.name',
                                    'Section.year_level_id',
                                    'Section.academicyear',
                                    'Section.college_id',
                                    'Section.department_id',
                                    'Section.curriculum_id',
                                    'Section.created',
                                    'Section.archive'
                                ),
                                'order' => array(
                                    'Section.year_level_id' => 'DESC',
                                    'Section.id DESC',
                                ),
                                'YearLevel' => array(
                                    'fields' => array('id', 'name')
                                )
                            ),
                            /* 'CourseRegistration' => array(
								'order' => array(
									'CourseRegistration.academic_year DESC',
									'CourseRegistration.semester DESC',
									'CourseRegistration.id DESC',
								),
								'fields' => array('id', 'year_level_id',  'student_id', 'section_id', 'year_level_id', 'semester', 'academic_year', 'published_course_id', 'created')
							), */
                        ),
                        'fields' => array(
                            'Student.studentnumber',
                            'Student.full_name',
                            'Student.curriculum_id',
                            'Student.department_id',
                            'Student.college_id',
                            'Student.program_id',
                            'Student.program_type_id',
                            'Student.gender',
                            'Student.graduated',
                            'Student.academicyear',
                            'Student.admissionyear',
                        ),
                    ));

                    //debug($student_detail);

                    $freshmanStudent = 0;

                    if (is_null(
                            $student_detail['Student']['department_id']
                        ) && $student_detail['Student']['program_id'] == PROGRAM_UNDEGRADUATE) {
                        $freshmanStudent = 1;
                    }

                    $sectionDetail = $this->Section->find('first', array(
                        'conditions' => array(
                            'Section.id' => $this->request->data['Section']['assigned_section']
                        ),
                        'contain' => array(
                            'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active')
                        ),
                        'recursive' => -1
                    ));

                    $studentsCountInSelectedSection = ClassRegistry::init('StudentsSection')->find(
                        'count',
                        array('conditions' => array('StudentsSection.section_id' => $this->request->data['Section']['assigned_section']))
                    );

                    debug($studentsCountInSelectedSection);

                    if (!$freshmanStudent && (is_null($student_detail['Student']['curriculum_id']))) {
                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                            $this->Flash->error(
                                $student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ') is not attached to a curriculum, Please attach the student to a curriculum before trying to add the student to a section.'
                            );
                        } else {
                            $this->Flash->error(
                                $student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ') is not attached to a curriculum, Communicate his/her department to attach a curriculum to the student before trying to add him/her to a section.'
                            );
                        }
                        $this->redirect(
                            array(
                                'controller' => 'students',
                                'action' => 'student_academic_profile',
                                $student_detail['Student']['id']
                            )
                        );
                    } else {
                        if (!$freshmanStudent && isset($student_detail['Student']['Curriculum']['name']) && isset($this->request->data['Section']['assigned_section']) && is_numeric(
                                $this->request->data['Section']['assigned_section']
                            )) {
                            if (!empty($sectionDetail) && isset($sectionDetail['Section']['curriculum_id']) && is_numeric(
                                    $sectionDetail['Section']['curriculum_id']
                                ) && $sectionDetail['Section']['curriculum_id'] != 0) {
                                $section_curriculum = $sectionDetail['Section']['curriculum_id'];
                                $section_curriculumName = $sectionDetail['Section']['Curriculum']['name'];
                            } else {
                                $section_curriculum = $this->Section->getSectionCurriculum(
                                    $this->request->data['Section']['assigned_section']
                                );
                                if ($section_curriculum) {
                                    $section_curriculumName = $this->Section->Curriculum->field(
                                        'Curriculum.name',
                                        array('Curriculum.id' => $section_curriculum)
                                    );
                                }
                            }

                            if ($student_detail['Student']['curriculum_id'] != $section_curriculum) {
                                $this->Flash->error(
                                    $student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ') is attached to ' . $student_detail['Student']['Curriculum']['name'] . '  curriculum, But students in ' . $sectionDetail['Section']['name'] . ' section are attached to ' . $section_curriculumName . ' curriculum.'
                                );
                                $this->redirect(
                                    array(
                                        'controller' => 'students',
                                        'action' => 'student_academic_profile',
                                        $this->request->data['Section']['Selected_student_id']
                                    )
                                );
                            }
                        }
                    }

                    $already_recorded = ClassRegistry::init('StudentsSection')->find(
                        'count',
                        array(
                            'conditions' => array(
                                'StudentsSection.section_id' => $this->request->data['Section']['assigned_section'],
                                'StudentsSection.student_id' => $student_detail['Student']['id']
                            )
                        )
                    );

                    if (!$already_recorded) {
                        if (!empty($student_detail['Section'])) {
                            $this->Section->StudentsSection->updateAll(array('archive' => 1),
                                array('student_id' => $student_detail['Student']['id']));
                        }

                        $sectionAdd['StudentsSection']['student_id'] = $student_detail['Student']['id'];
                        $sectionAdd['StudentsSection']['section_id'] = $this->request->data['Section']['assigned_section'];

                        $this->Section->StudentsSection->create();

                        if ($this->Section->StudentsSection->save($sectionAdd['StudentsSection'])) {
                            $this->Flash->success(
                                $student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ') is added to ' . $sectionDetail['Section']['name'] . ' section Successfully.'
                            );
                            //$this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $this->request->data['Section']['Selected_student_id']));
                        } else {
                            $this->Flash->error(
                                $student_detail['Student']['full_name'] . ' (' . $student_detail['Student']['studentnumber'] . ') could not be added to ' . $sectionDetail['Section']['name'] . ' section. Please, try again.'
                            );
                        }
                    }

                    if ($studentsCountInSelectedSection == 0 && (!isset($sectionDetail['Section']['curriculum_id']) || empty($sectionDetail['Section']['curriculum_id']))) {
                        $this->Section->id = $sectionDetail['Section']['id'];
                        $this->Section->saveField('curriculum_id', $student_detail['Student']['curriculum_id']);
                    }

                    $this->redirect(
                        array(
                            'controller' => 'students',
                            'action' => 'student_academic_profile',
                            $student_detail['Student']['id']
                        )
                    );
                } else {
                    /* foreach ($this->request->data['Section']['assigned_section'] as $k => $v) {
						if ($v != 0 && !in_array($v, $previousStudentsSection)) {
							debug($v);
							//$section = $this->Section->find('first', array('conditions' => array('Section.id' => $k), 'recursive' => -1));
							$section = $this->Section->find('first', array('conditions' => array('Section.id' => $v), 'recursive' => -1));
							$studID = $selectedSectionForAdd['StudentsSection'][$count]['student_id'] = $this->request->data['Section']['Selected_student_id'];
							$sectionID = $selectedSectionForAdd['StudentsSection'][$count]['section_id'] = $v;

							$section_curriculum_id = $this->Section->getSectionCurriculum($v);

							debug($section_curriculum_id);

							debug($selected_student_attached_curriculum);

							if ($section['Section']['academicyear'] >= $this->AcademicYear->current_academicyear()) {
								$selectedSectionForAdd['StudentsSection'][$count]['archive'] = 0;
								debug($selectedSectionForAdd);
								$this->Section->StudentsSection->updateAll(array('archive' => 1), array('student_id' => $this->request->data['Section']['Selected_student_id']));
							} else {
								$selectedSectionForAdd['StudentsSection'][$count]['archive'] = 1;
							}

							//debug(ClassRegistry::init('Student')->field('Student.curriculum_id', array('Student.id' => $this->request->data['Section']['Selected_student_id'])));
							$selected_student_attached_curriculum = ClassRegistry::init('Student')->field('Student.curriculum_id', array('Student.id' => $this->request->data['Section']['Selected_student_id']));

							if (empty($selected_student_attached_curriculum) || is_null($selected_student_attached_curriculum)) {

								if ($this->role_id == ROLE_DEPARTMENT && $this->department_id == $selectedStudentDetail['Student']['department_id']) {
									$updateStudentsCurriculumBasedOnCurrentSection['Student']['id'] = $this->request->data['Section']['Selected_student_id'];
									$updateStudentsCurriculumBasedOnCurrentSection['Student']['curriculum_id'] = $section_curriculum_id;

									$updateAcceptedStudentsCurriculumBasedOnCurrentSection['AcceptedStudent']['id'] = $selectedStudentDetail['Student']['accepted_student_id'];
									$updateAcceptedStudentsCurriculumBasedOnCurrentSection['AcceptedStudent']['curriculum_id'] = $section_curriculum_id;
									$updateAcceptedStudentsCurriculumBasedOnCurrentSection['AcceptedStudent']['Placement_Approved_By_Department'] = 1;

									$addCurriculumAttachmentsEntry['CurriculumAttachment']['student_id'] = $this->request->data['Section']['Selected_student_id'];
									$addCurriculumAttachmentsEntry['CurriculumAttachment']['curriculum_id'] = $section_curriculum_id;
									$addCurriculumAttachmentsEntry['CurriculumAttachment']['created'] = date('Y-m-d H:i:s');
									$addCurriculumAttachmentsEntry['CurriculumAttachment']['modified'] = date('Y-m-d H:i:s');

									//ClassRegistry::init('User')->saveAll($deactivateAccount['User'], array('validate' => false))
									debug($updateStudentsCurriculumBasedOnCurrentSection['Student']);
									debug($updateAcceptedStudentsCurriculumBasedOnCurrentSection['AcceptedStudent']);
									debug($addCurriculumAttachmentsEntry['CurriculumAttachment']);


									$this->Section->Student->id = $this->request->data['Section']['Selected_student_id'];

									if ($this->Section->Student->saveField('curriculum_id', $section_curriculum_id)) {
										$sectionCurriculumAttachedToSudentWhenAdding = $section_curriculum_id;
									}

									//$this->Section->Student->AcceptedStudent->id = $selectedStudentDetail['Student']['accepted_student_id'];
									//$this->Section->Student->AcceptedStudent->saveField('curriculum_id', $section_curriculum_id);

									$this->Section->Student->AcceptedStudent->updateAll(array('AcceptedStudent.curriculum_id' => $section_curriculum_id, 'AcceptedStudent.Placement_Approved_By_Department' => 1), array('AcceptedStudent.id' => $selectedStudentDetail['Student']['accepted_student_id']));

									//$this->Section->Student->CurriculumAttachment->create();
									//$this->Section->Student->CurriculumAttachment->save($addCurriculumAttachmentsEntry['CurriculumAttachment']);

								} else if ($this->role_id == ROLE_REGISTRAR && in_array($selectedStudentDetail['Student']['department_id'], $this->department_ids)) {
									$this->Flash->error('The selected student is not attached to a curriculum, Communicate his/her department before trying to add the student to a section.');
									$this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $this->request->data['Section']['Selected_student_id']));
								}
							}
						}
						$count++;
					} */
                }
            } else {
                $this->Flash->error('You need to select a section to add for the selected student.');
                $this->redirect(
                    array(
                        'controller' => 'students',
                        'action' => 'student_academic_profile',
                        $this->request->data['Section']['Selected_student_id']
                    )
                );
            }
            /* if (!empty($selectedSectionForAdd)) {
				debug($selectedSectionForAdd);
				$successAdd = 0;
				$successUpdate = 0;
				$archieve = 0;
				$already_recorded_id = ClassRegistry::init('StudentsSection')->find('first', array('conditions' => array('StudentsSection.section_id' => $sectionID, 'StudentsSection.student_id' => $studID), 'recursive' => -1));

				if ($already_recorded_id) {
					$section = $this->Section->find('first', array('conditions' => array('Section.id' => $sectionID), 'recursive' => -1));
					$this->Section->StudentsSection->id = $already_recorded_id;

					if ($section['Section']['academicyear'] >= $this->AcademicYear->current_academicyear()) {
						$this->Section->StudentsSection->saveField('archive', '0');
						$successUpdate =  1;
					} else {
						$this->Section->StudentsSection->saveField('archive', '1');
						$archieve =  $successUpdate =  1;
					}
				} else {
					$sectionAdd['StudentsSection']['student_id'] = $studID;
					$sectionAdd['StudentsSection']['section_id'] = $sectionID;
					$this->Section->StudentsSection->create();
					$this->Section->StudentsSection->save($sectionAdd['StudentsSection']);
					$successAdd = 1;
				}

				if ($successAdd) {
					if ($sectionCurriculumAttachedToSudentWhenAdding) {
						$this->Flash->success('Student Section Added Successfully and Section Curriculum is also attached to his profile.');
					} else {
						$this->Flash->success('Student Section Added Successfully.');
					}

					$this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $this->request->data['Section']['Selected_student_id']));
				} else if ($successUpdate) {
					$this->Flash->success('Student Section ' . $archieve == 0 ? 'updated sucessfully' : 'updated and archieved' . '.');
					$this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $this->request->data['Section']['Selected_student_id']));
				}
			} else {
				$this->Flash->error('The student could not be added to selected section. Please, try again.');
				$this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $this->request->data['Section']['Selected_student_id']));
			} */
        }

        $this->redirect(
            array(
                'controller' => 'students',
                'action' => 'student_academic_profile',
                $this->request->data['Section']['Selected_student_id']
            )
        );
    }

    public function upgrade_sections()
    {

        if ($this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) {
            $this->Flash->warning('You need to have department role to updrade section year levels!');
            $this->redirect('/');
        }

        $programs = $this->Section->Program->find('list');
        $programTypes = $this->Section->ProgramType->find('list');
        $yearLevels = $this->Section->YearLevel->find(
            'list',
            array('conditions' => array('YearLevel.department_id' => $this->department_id))
        );

        $isbeforesearch = 1;

        $this->set(compact('programs', 'programTypes', 'isbeforesearch', 'yearLevels'));

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $isbeforesearch = 0;
            $selected_program = $this->request->data['Section']['program_id'];
            $selected_program_type = $this->request->data['Section']['program_type_id'];
            $selected_academicyear = (!empty($this->request->data['Section']['academicyear']) ? $this->request->data['Section']['academicyear'] : $this->AcademicYear->current_academicyear(
            ));
            $selected_year_level = $this->request->data['Section']['year_level_id'];

            if (empty($selected_year_level)) {
                //$selected_year_level = '%';
                $selected_year_level = array_keys($yearLevels);
            }

            $sections = $this->Section->find('all', array(
                //'fields' => array('Section.id', 'Section.name'),
                'conditions' => array(
                    'Section.department_id' => $this->department_id,
                    'Section.program_id' => (!empty($selected_program) ? $selected_program : $this->program_ids),
                    'Section.program_type_id' => (!empty($selected_program_type) ? $selected_program_type : $this->program_type_ids),
                    'Section.academicyear' => $selected_academicyear,
                    //'Section.year_level_id LIKE ' => $selected_year_level,
                    'Section.year_level_id' => $selected_year_level,
                    'Section.archive' => 0
                ),
                'contain' => array(
                    'YearLevel' => array(
                        'fields' => array(
                            'YearLevel.name'
                        )
                    ),
                    'PublishedCourse' => array(
                        'fields' => array('PublishedCourse.id', 'PublishedCourse.section_id'),
                        'CourseRegistration' => array(
                            'fields' => array('id', 'published_course_id', 'section_id', 'student_id'),
                            'limit' => 1
                        ),
                        'limit' => 1
                    )
                ),
                'order' => array(
                    'Section.year_level_id' => 'ASC',
                    'Section.program_id' => 'ASC',
                    'Section.program_type_id' => 'ASC',
                    'Section.name' => 'ASC'
                )
            ));

            $sections_lastpublishedcourses_list = array();
            $last_year_level_sections_count = 0;

            /* if (!empty($sections)) {
				foreach ($sections as $section) {
					$sections_lastpublishedcourses_list[$section['Section']['id']] = $this->Section->PublishedCourse->lastPublishedCoursesForSection($section['Section']['id']);
				}
			} */

            if (!empty($sections)) {
                foreach ($sections as $sKey => &$section) {
                    if (!empty($section['Section']['department_id']) && $section['Section']['department_id'] > 0 && $section['Section']['department_id'] == $this->department_id) {
                        //isset($section['PublishedCourse'][0]['CourseRegistration'][0]) ? debug($section['PublishedCourse'][0]['CourseRegistration'][0]) : '';

                        if (empty($section['PublishedCourse']) || (isset($section['PublishedCourse'][0]) && !isset($section['PublishedCourse'][0]['CourseRegistration'][0]))) {
                            unset($sections[$sKey]);
                            continue;
                        }

                        if (!empty($section['Section']['curriculum_id']) && $section['Section']['curriculum_id'] > 0 && !empty($section['Section']['year_level_id']) && $section['Section']['year_level_id'] > 0) {
                            $curriculum_year_levels = $this->Section->Curriculum->Course->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Course.curriculum_id' => $section['Section']['curriculum_id'],
                                        'Course.active' => 1
                                    ),
                                    'fields' => array('Course.year_level_id', 'Course.year_level_id'),
                                    'group' => array('Course.year_level_id'),
                                    'order' => array('Course.year_level_id' => 'DESC')
                                )
                            );
                            //debug($curriculum_year_levels);
                            if (!empty($curriculum_year_levels)) {
                                $curriculum_year_levels = array_values($curriculum_year_levels);
                                //debug($curriculum_year_levels);

                                if (!empty($selected_year_level) && !is_array($selected_year_level) && is_numeric(
                                        $selected_year_level
                                    ) && $curriculum_year_levels[0] <= $selected_year_level) {
                                    $section['Section']['last_year_level_section'] = true;
                                    $last_year_level_sections_count++;
                                } else {
                                    if (!empty($selected_year_level) && is_array(
                                            $selected_year_level
                                        ) && ($curriculum_year_levels[0] <= $section['Section']['year_level_id'])) {
                                        $section['Section']['last_year_level_section'] = true;
                                        $last_year_level_sections_count++;
                                    } else {
                                        if ($curriculum_year_levels[0] == $section['Section']['year_level_id']) {
                                            $section['Section']['last_year_level_section'] = true;
                                            $last_year_level_sections_count++;
                                        } else {
                                            $section['Section']['last_year_level_section'] = false;
                                        }
                                    }
                                }
                            } else {
                                unset($sections[$sKey]);
                                continue;
                            }
                        } else {
                            $department_year_levels = $this->Section->YearLevel->find(
                                'list',
                                array(
                                    'conditions' => array('YearLevel.department_id' => $this->department_id),
                                    'fields' => array('YearLevel.id', 'YearLevel.id'),
                                    'group' => array('YearLevel.department_id', 'YearLevel.id'),
                                    'order' => array('YearLevel.id' => 'DESC')
                                )
                            );

                            if (!empty($department_year_levels)) {
                                $department_year_levels = array_values($department_year_levels);
                                //debug($department_year_levels);

                                if (!empty($selected_year_level) && !is_array($selected_year_level) && is_numeric(
                                        $selected_year_level
                                    ) && $department_year_levels[0] == $selected_year_level) {
                                    $section['Section']['last_year_level_section'] = true;
                                    $last_year_level_sections_count++;
                                } else {
                                    if (!empty($selected_year_level) && is_array(
                                            $selected_year_level
                                        ) && $department_year_levels[0] == $section['Section']['year_level_id']) {
                                        $section['Section']['last_year_level_section'] = true;
                                        $last_year_level_sections_count++;
                                    } else {
                                        if ($department_year_levels[0] == $section['Section']['year_level_id']) {
                                            $section['Section']['last_year_level_section'] = true;
                                            $last_year_level_sections_count++;
                                        } else {
                                            $section['Section']['last_year_level_section'] = false;
                                        }
                                    }
                                }
                            } else {
                                unset($sections[$sKey]);
                                continue;
                            }
                        }

                        $sections_lastpublishedcourses_list[$section['Section']['id']] = $this->Section->PublishedCourse->lastPublishedCoursesForSection(
                            $section['Section']['id']
                        );
                        $sections_lastpublishedcourses_list[$section['Section']['id']]['last_year_level_section'] = (isset($section['Section']['last_year_level_section']) && $section['Section']['last_year_level_section'] ? true : false);
                    } else {
                        unset($sections[$sKey]);
                        continue;
                    }
                }
            }

            //debug($sections);
            //debug($sections_lastpublishedcourses_list);

            $upgradable_sections = array();
            $unupgradable_sections = array();


            if (!empty($sections_lastpublishedcourses_list)) {
                foreach ($sections_lastpublishedcourses_list as $sk => $sv) {
                    $is_submited_grade = 1;
                    //debug($sv);
                    //debug($sv['last_year_level_section']);

                    if (!$sv['last_year_level_section']) {
                        foreach ($sv as $pk => $vk) {
                            if (is_numeric($pk)) {
                                $is_submited_grade = $is_submited_grade * $this->Section->PublishedCourse->CourseRegistration->ExamGrade->is_grade_submitted(
                                        $pk
                                    );
                                debug($is_submited_grade);
                            }
                        }
                    }

                    if ($is_submited_grade != 0 && !$sv['last_year_level_section']) {
                        $upgradable_sections[] = $sk;
                    } else {
                        $unupgradable_sections[] = $sk;
                    }
                }
            }

            debug($upgradable_sections);
            debug($unupgradable_sections);

            $formatedSections = array();
            $unqualified_students_count = array();

            if (!empty($sections)) {
                foreach ($sections as $usk => $usv) {
                    if (in_array(
                            $usv['Section']['id'],
                            $upgradable_sections
                        ) && (!isset($usv['Section']['last_year_level_section']) || (isset($usv['Section']['last_year_level_section']) && !$usv['Section']['last_year_level_section']))) {
                        $formatedSections[$usv['YearLevel']['name']]['Upgradable'][$usv['Section']['id']] = $usv['Section']['name'] . ' (' . (isset($usv['YearLevel']['name']) && !empty($usv['YearLevel']['name']) ? $usv['YearLevel']['name'] : 'Pre/1st') . ', ' . $usv['Section']['academicyear'] . ')';
                    } else {
                        $formatedSections[$usv['YearLevel']['name']]['Unupgradable'][$usv['Section']['id']] = $usv['Section']['name'] . ' (' . (isset($usv['YearLevel']['name']) && !empty($usv['YearLevel']['name']) ? $usv['YearLevel']['name'] : 'Pre/1st') . ', ' . $usv['Section']['academicyear'] . ')' . (isset($usv['Section']['last_year_level_section']) && $usv['Section']['last_year_level_section'] ? ' <span class="on-process">(Final Year)</span>' : ' <span class="rejected">   Grade not fully submitted</span>');
                    }
                }
            }

            if (!empty($upgradable_sections)) {
                $unqualified_students = $this->_get_unqualified_students_count(
                    $upgradable_sections,
                    $this->request->data['Section']['academicyear']
                );
            }


            if (isset($unqualified_students) && !empty($unqualified_students)) {
                $unqualified_students_count = $unqualified_students;
            }

            $this->Session->write('unqualified_students_count', $unqualified_students_count);
            $this->Session->write('formatedSections', $formatedSections);
            debug($unqualified_students_count);

            $this->set(
                compact(
                    'formatedSections',
                    'unqualified_students_count',
                    'isbeforesearch',
                    'selected_program',
                    'selected_program_type',
                    'selected_academicyear',
                    'selected_year_level',
                    'last_year_level_sections_count'
                )
            );
        }

        //After Upgrade button is clicked
        if (isset($this->request->data['upgrade']) && !empty($this->request->data['upgrade'])) {
            //get selected sections for upgrade
            $selected_sections = array();

            if (isset($this->request->data['Section']['Upgradbale_Selected']) && !empty($this->request->data['Section']['Upgradbale_Selected'])) {
                foreach ($this->request->data['Section']['Upgradbale_Selected'] as $susk => $susv) {
                    if ($susv != 0) {
                        $selected_sections[] = $susv;
                    }
                }
            }

            $selected_section_count = count($selected_sections);

            if (!empty($selected_section_count)) {
                $upgradeStatus = $this->Section->upgradeSelectedSection($selected_sections);
                //debug($upgradeStatus);

                if (!empty($upgradeStatus)) {
                    $this->Flash->success(implode(" ", $upgradeStatus) . ' section have been upgraded successfully.');
                    // clearing session here will be helpfull to  prevent form resubmission and  not require user logout for some reason
                    if ($this->Session->check('formatedSections')) {
                        $this->Session->delete('formatedSections');
                        if ($this->Session->check('unqualified_students_count')) {
                            $this->Session->delete('unqualified_students_count');
                        }
                    }
                    return $this->redirect(array('action' => 'display_sections'));
                } else {
                    $this->Flash->error(
                        'Unable to upgrde the selected section(s), all section students fail to qualify for year level upgrade, leaving the section(s) un-upgraded.'
                    );
                }
            } else {
                $this->Flash->error('Please select at least one section.');
            }

            $this->request->data['search'] = true;
            $formatedSections = null;
            $unqualified_students_count = null;
            $isbeforesearch = 0;

            if ($this->Session->check('formatedSections')) {
                $formatedSections = $this->Session->read('formatedSections');
            }

            if ($this->Session->check('unqualified_students_count')) {
                $unqualified_students_count = $this->Session->read('unqualified_students_count');
            }

            $this->set(
                compact(
                    'formatedSections',
                    'unqualified_students_count',
                    'isbeforesearch',
                    'last_year_level_sections_count'
                )
            );
        }
    }

    public function upgrade_selected_student_section($section_id, $student_id)
    {

        $this->layout = 'ajax';

        $student_detail = $this->Section->Student->find('first', array(
            'conditions' => array(
                'Student.id' => $student_id
            ),
            'contain' => array(
                'AcceptedStudent'
            )
        ));

        $section = $this->Section->find('first', array(
            'conditions' => array(
                'Section.id' => $section_id
            ),
            'recursive' => -1
        ));

        $nextSection = $this->Section->find('first', array(
            'conditions' => array(
                'Section.year_level_id' => $section['Section']['year_level_id'] + 1,
                'Section.department_id' => $student_detail['Student']['department_id'],
                'Section.program_id' => $student_detail['Student']['program_id'],
                'Section.program_type_id' => $student_detail['Student']['program_type_id']
            ),
            'recursive' => -1
        ));

        $this->set(compact('student_detail', 'nextSection'));
        /*
		$student_detail = $this->Section->Student->find('first', array(
			'conditions' => array(
				'Student.id' => $student_id
			),
			'contain' => array(
				'AcceptedStudent'
			)
		));

		$possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange($student_detail['AcceptedStudent']['academicyear'], $this->AcademicYear->current_academicyear());

		$previousSectionAttended = $this->Section->StudentsSection->find('list', array(
			'conditions' => array(
				'StudentsSection.student_id' => $student_id
			),
			'fields' => array(
				'StudentsSection.section_id',
				'StudentsSection.section_id'
			)
		));

		if (!empty($previousSectionAttended)) {
			$lastYearLevel = null;
			foreach ($previousSectionAttended as $ac => $se) {
				$section = $this->Section->find('first', array('conditions' => array('Section.id' => $se)));
				if (in_array($section['Section']['academicyear'], $possibleAcademicYears)) {
					unset($possibleAcademicYears[$section['Section']['academicyear']]);
					$lastYearLevel = $section['Section']['year_level_id'];
				}
			}

			// do upgrade for the first academic year
			if ($this->StudentsSection->updateAll(array('StudentsSection.archive' => 1), array('StudentsSection.student_id' => $student_id))) {
				// find section for next academic year
				$nextAc = reset($possibleAcademicYears);

				$section = $this->Section->find('first', array(
					'conditions' => array(
						'Section.academicyear' => $nextAc,
						'Section.program_id' => $student_detail['Student']['program_id'],
						'Section.program_type_id' => $student_detail['Student']['program_type_id'],
						'Section.department_id' => $student_detail['Student']['department_id'],
						'Section.year_level_id' => $lastYearLevel + 1
					)
				));

				$createSection['student_id'] = $student_id;
				$createSection['section_id'] = $section['Section']['id'];
				$this->StudentsSection->create();
				$this->StudentsSection->save($createSection);
			}
		} else {
			// not possible to do upgrade
		}
		*/
    }

    public function get_sections_by_program($program_id = "")
    {

        $this->layout = 'ajax';
        $sections = array();
        $student_sections = array();

        $departement = 0;

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
            $departement = 1;
        }

        if (empty($program_id)) {
            $program_id = array_values($this->program_ids)[0];
            if (empty($program_id)) {
                $program_id = 1;
            }
        }

        $student_sections = $this->Section->allDepartmentSectionsOrganizedByProgramType(
            ($departement == 1 ? $this->department_id : $this->college_id),
            $departement,
            $program_id
        );

        $this->set(compact('student_sections'));
    }

    public function get_sections_by_program_supp_exam($program_id = "")
    {

        $this->layout = 'ajax';
        $sections = array();
        $student_sections = array();

        $departement = 0;

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
            $departement = 1;
        }

        if (empty($program_id)) {
            $program_id = array_values($this->program_ids)[0];
            if (empty($program_id)) {
                $program_id = 1;
            }
        }

        $student_sections = $this->Section->allDepartmentSectionsOrganizedByProgramTypeSuppExam(
            ($departement == 1 ? $this->department_id : $this->college_id),
            $departement,
            $program_id,
            3
        );

        $this->set(compact('student_sections'));
    }

    public function get_section_students($section_id = "")
    {

        $this->layout = 'ajax';
        $students = array();

        if (!empty($section_id)) {
            $students = $this->Section->allStudents($section_id);
            // do we really need this?? I mean, if we are using this function in other places other than supp exam, it will hide all students except those who have supp in the section.
            //$students = ClassRegistry::init('ExamGradeChange')->possibleStudentsForSup($section_id);
        }

        $this->set(compact('students'));
    }

    public function get_sup_students($section_id = "")
    {

        $this->layout = 'ajax';
        $students = array();

        if (!empty($section_id)) {
            $students = ClassRegistry::init('ExamGradeChange')->possibleStudentsForSup($section_id);
        }

        $this->set(compact('students'));
    }

    public function get_sections_by_program_and_dept($department_id = "", $program_id = "")
    {

        $this->layout = 'ajax';
        $student_sections = array();

        $departement_role = 0;

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
            $departement_role = 1;
            if (empty($department_id)) {
                $department_id = $this->department_id;
            }
        } else {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                if (empty($department_id)) {
                    $department_id = $this->college_id;
                }
            }
        }

        if (empty($program_id)) {
            $program_id = array_values($this->program_ids)[0];
            if (empty($program_id)) {
                $program_id = 1;
            }
        }

        if (!empty($department_id) && !empty($program_id)) {
            $student_sections = $this->Section->allDepartmentSectionsOrganizedByProgramType(
                $department_id,
                $departement_role,
                $program_id
            );
        }

        $this->set(compact('student_sections'));
    }

    public function get_sections_by_program_and_dept_supp_exam($department_id = "", $program_id = "")
    {

        $this->layout = 'ajax';
        $student_sections = array();

        $departement_role = 0;

        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
            $departement_role = 1;
            if (empty($department_id)) {
                $department_id = $this->department_id;
            }
        } else {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                if (empty($department_id)) {
                    $department_id = $this->college_id;
                }
            }
        }

        if (empty($program_id)) {
            $program_id = array_values($this->program_ids)[0];
            if (empty($program_id)) {
                $program_id = 1;
            }
        }

        if (!empty($department_id) && !empty($program_id)) {
            $student_sections = $this->Section->allDepartmentSectionsOrganizedByProgramTypeSuppExam(
                $department_id,
                $departement_role,
                $program_id,
                3
            );
        }

        $this->set(compact('student_sections'));
    }

    public function get_year_level($department_id = null)
    {

        $yearLevels = array();

        if (!empty($department_id)) {
            $this->layout = 'ajax';
            $yearLevels = $this->Section->YearLevel->find(
                'list',
                array('conditions' => array('YearLevel.department_id' => $department_id))
            );
        }

        $this->set(compact('yearLevels'));
    }

    public function get_sections_of_college($college_id = null)
    {

        $this->layout = 'ajax';
        $sections = array();

        if (!empty($this->student_id)) {
            $student_program_id = $this->Section->Student->field('program_id', array('Student.id' => $this->student_id)
            );
            $student_section_id = $this->Section->StudentsSection->field(
                'section_id',
                array('StudentsSection.student_id' => $this->student_id, 'StudentsSection.archive' => 0)
            );

            $sections_detail = $this->Section->find('all', array(
                'conditions' => array(
                    'Section.department_id is null',
                    'Section.college_id' => $college_id,
                    'Section.program_id' => $student_program_id,
                    'Section.archive' => 0,
                ),
                'contain' => array(
                    'Program' => array('id', 'name'),
                    'YearLevel' => array('id', 'name'),
                    'ProgramType' => array('id', 'name')
                ),
                'fields' => array(
                    'Section.id',
                    'Section.name',
                    'Section.program_id',
                    'Section.year_level_id',
                    'Section.academicyear',
                    'Section.curriculum_id'
                ),
                'order' => array(
                    'Section.academicyear' => 'DESC',
                    'Section.program_id' => 'ASC',
                    'Section.year_level_id' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                ),
            ));
        } else {
            $sections_detail = $this->Section->find('all', array(
                'conditions' => array(
                    'Section.college_id' => $college_id,
                    'Section.archive' => 0,
                    'Section.department_id is null'
                ),
                'contain' => array(
                    'Program' => array('id', 'name'),
                    'YearLevel' => array('id', 'name'),
                    'ProgramType' => array('id', 'name')
                ),
                'fields' => array(
                    'Section.id',
                    'Section.name',
                    'Section.program_id',
                    'Section.year_level_id',
                    'Section.academicyear',
                    'Section.curriculum_id'
                ),
                'order' => array(
                    'Section.academicyear' => 'DESC',
                    'Section.program_id' => 'ASC',
                    'Section.year_level_id' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                ),
            ));
        }

        if (!empty($sections_detail)) {
            foreach ($sections_detail as $seindex => $secvalue) {
                if (empty($secvalue['YearLevel']['id'])) {
                    $sections[$secvalue['Program']['name']][$secvalue['Section']['id']] = $secvalue['Section']['name'] . ' (' . $secvalue['Section']['academicyear'] . ', ' . ($secvalue['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Fresh') . ')';
                } else {
                    $sections[$secvalue['Program']['name']][$secvalue['Section']['id']] = $secvalue['Section']['name'] . ' (' . $secvalue['Section']['academicyear'] . ', ' . $secvalue['YearLevel']['name'] . ')';
                }
            }
        }

        $this->set(compact('sections'));
    }

    public function get_sections_by_dept($department_id = "", $student_id = '', $acYear, $year_level_name = '')
    {

        $this->layout = 'ajax';
        $sections = array();

        if (!empty($this->student_id)) {
            $student_program_id = $this->Section->Student->field('program_id', array('Student.id' => $this->student_id)
            );
            $student_section_id = $this->Section->StudentsSection->field(
                'section_id',
                array('StudentsSection.student_id' => $this->student_id, 'StudentsSection.archive' => 0)
            );

            if (isset($this->request->data['Student']['college_id']) && !empty($this->request->data['Student']['college_id']) && $this->request->data['Student']['department_id'] == -1) {
                debug($this->request->data);
                $conditions = array(
                    'Section.college_id' => $this->request->data['Student']['college_id'],
                    'Section.archive' => 0,
                    'Section.department_id is null',
                    'Section.program_id' => $student_program_id,
                    'OR' => array(
                        'Section.academicyear LIKE ' => $acYear,
                        'Section.created >= ' => date('Y-m-d H:i:s', strtotime('-1 year'))
                    )
                );
                debug($conditions);
            } else {
                if (isset($this->request->data['Student']['college_id']) && !empty($this->request->data['Student']['college_id']) && empty($this->request->data['Student']['department_id'])) {
                    $conditions = array(
                        'Section.college_id' => $this->request->data['Student']['college_id'],
                        'Section.archive' => 0,
                        'Section.department_id is null',
                        'Section.program_id' => $student_program_id,
                        'OR' => array(
                            'Section.academicyear LIKE ' => $acYear,
                            'Section.created >= ' => date('Y-m-d H:i:s', strtotime('-1 year'))
                        )
                    );
                    debug($conditions);
                } else {
                    if (isset($department_id) && !empty($department_id)) {
                        $conditions = array(
                            'Section.department_id' => $department_id,
                            'Section.archive' => 0,
                            'Section.program_id' => $student_program_id,
                            'OR' => array(
                                'Section.academicyear LIKE ' => $acYear,
                                'Section.created >= ' => date('Y-m-d H:i:s', strtotime('-1 year'))
                            )
                        );
                        debug($conditions);
                    }
                }
            }

            $sections_detail = $this->Section->find('all', array(
                'conditions' => $conditions,
                'order' => array('Section.year_level_id'),
                'contain' => array(
                    'Program' => array('id', 'name'),
                    'YearLevel' => array('id', 'name'),
                    'ProgramType' => array('id', 'name')
                ),
                'fields' => array(
                    'Section.id',
                    'Section.name',
                    'Section.program_id',
                    'Section.year_level_id',
                    'Section.academicyear',
                    'Section.curriculum_id'
                ),
                'order' => array(
                    'Section.academicyear' => 'DESC',
                    'Section.program_id' => 'ASC',
                    'Section.year_level_id' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                ),
            ));
        } else {
            if (!empty($student_id) && $student_id != 0) {
                $student_program_id = $this->Section->Student->field('program_id', array('Student.id' => $student_id));
            } else {
                $student_program_id = $this->program_id;
            }

            if (!$year_level_name) {
                $sections_detail = $this->Section->find('all', array(
                    'conditions' => array(
                        'Section.department_id' => $department_id,
                        'Section.department_id is not null',
                        'Section.program_id' => $student_program_id,
                        'Section.archive' => 0,
                        'OR' => array(
                            'Section.academicyear LIKE ' => $acYear,
                            'Section.created >= ' => date('Y-m-d H:i:s', strtotime('-1 year'))
                        )
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'ProgramType' => array('id', 'name')
                    ),
                    'fields' => array(
                        'Section.id',
                        'Section.name',
                        'Section.program_id',
                        'Section.year_level_id',
                        'Section.academicyear',
                        'Section.curriculum_id'
                    ),
                    'order' => array(
                        'Section.academicyear' => 'DESC',
                        'Section.program_id' => 'ASC',
                        'Section.year_level_id' => 'ASC',
                        'Section.id' => 'ASC',
                        'Section.name' => 'ASC'
                    ),
                ));
            } else {
                $sections_detail = $this->Section->find('all', array(
                    'conditions' => array(
                        //'Section.college_id' => $department_id,
                        'Section.department_id is null',
                        'Section.program_id' => $student_program_id,
                        'Section.archive' => 0,
                        'OR' => array(
                            'Section.academicyear LIKE ' => $acYear,
                            'Section.created >= ' => date('Y-m-d H:i:s', strtotime('-1 year'))
                        )
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'ProgramType' => array('id', 'name')
                    ),
                    'fields' => array(
                        'Section.id',
                        'Section.name',
                        'Section.program_id',
                        'Section.year_level_id',
                        'Section.academicyear',
                        'Section.curriculum_id'
                    ),
                    'order' => array(
                        'Section.academicyear' => 'DESC',
                        'Section.program_id' => 'ASC',
                        'Section.year_level_id' => 'ASC',
                        'Section.id' => 'ASC',
                        'Section.name' => 'ASC'
                    ),
                ));
            }
        }

        if (!empty($sections_detail)) {
            foreach ($sections_detail as $seindex => $secvalue) {
                $dataids = ClassRegistry::init('StudentsSection')->find(
                    'list',
                    array(
                        'conditions' => array('StudentsSection.section_id' => $secvalue['Section']['id']),
                        'fields' => array('student_id', 'student_id'),
                        'group' => array('student_id', 'section_id')
                    )
                );
                $gradutingStudent = ClassRegistry::init('GraduateList')->find(
                    'count',
                    array('conditions' => array('GraduateList.student_id' => $dataids))
                );

                if ($gradutingStudent > count($dataids) / 3) {
                    $isGraduate = true;
                } else {
                    $isGraduate = false;
                }

                if (!$isGraduate) {
                    if (isset($secvalue['YearLevel']['name']) && !empty($secvalue['YearLevel']['name'])) {
                        $yn = $secvalue['YearLevel']['name'];
                    } else {
                        $yn = '1st';
                    }
                    $sections[$secvalue['Program']['name']][$secvalue['Section']['id']] = $secvalue['Section']['name'] . ' (' . $secvalue['Section']['academicyear'] . ', ' . $yn . ')';
                }
                //}
            }
        }
        $this->set(compact('sections'));
    }

    public function get_sections_by_dept_add_drop(
        $department_id = "",
        $student_id = '',
        $year_level_name = '',
        $college_id = ''
    ) {

        $this->layout = 'ajax';
        $sections = array();

        if ((!empty($student_id) && $student_id != 0) || !empty($this->student_id)) {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT && !empty($this->student_id)) {
                $student_id = $this->student_id;
            }

            $student_detail = $this->Section->Student->find('first', array(
                'conditions' => array(
                    'Student.id' => $student_id
                ),
                'contain' => array(
                    'AcceptedStudent' => array('id', 'studentnumber', 'academicyear'),
                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name'),
                    'College' => array('id', 'name', 'campus_id', 'stream'),
                    'Section' => array(
                        'fields' => array(
                            'Section.id',
                            'Section.name',
                            'Section.year_level_id',
                            'Section.academicyear',
                            'Section.college_id',
                            'Section.department_id',
                            'Section.curriculum_id',
                            'Section.created',
                            'Section.archive'
                        ),
                        'order' => array(
                            'Section.academicyear' => 'DESC',
                            'Section.program_id' => 'ASC',
                            'Section.year_level_id' => 'ASC',
                            'Section.id' => 'ASC',
                            'Section.name' => 'ASC'
                        ),
                        'YearLevel' => array('id', 'name')
                    ),
                    'CourseRegistration' => array(
                        'order' => array(
                            'CourseRegistration.academic_year' => 'DESC',
                            'CourseRegistration.semester' => 'DESC',
                            'CourseRegistration.id' => 'DESC'
                        ),
                        //'fields' => array('id', 'year_level_id',  'student_id', 'section_id', 'year_level_id', 'semester', 'academic_year', 'published_course_id', 'created'),
                        'limit' => 1,
                    ),
                    'Readmission' => array(
                        'conditions' => array(
                            'Readmission.registrar_approval' => 1,
                            'Readmission.academic_commision_approval' => 1,
                        ),
                        'fields' => array(
                            'student_id',
                            'academic_year',
                            'semester',
                            'registrar_approval_date',
                            'modified'
                        ),
                        'order' => array('Readmission.modified' => 'DESC')
                    )
                ),
                'fields' => array(
                    'Student.studentnumber',
                    'Student.full_name',
                    'Student.curriculum_id',
                    'Student.department_id',
                    'Student.college_id',
                    'Student.program_id',
                    'Student.program_type_id',
                    'Student.gender',
                    'Student.graduated',
                    'Student.academicyear',
                    'Student.admissionyear',
                ),
            ));

            $student_program_id = $student_detail['Student']['program_id'];
            $program_types_to_look = $this->__getEquivalentProgramTypes($student_detail['Student']['program_type_id']);

            $lastRegisteredAcademicYear = $student_detail['CourseRegistration'][0]['academic_year'];
            $lastRegisteredSemester = $student_detail['CourseRegistration'][0]['semester'];
            $lastRegisteredYearLevelID = (isset($student_detail['CourseRegistration'][0]['year_level_id']) && $student_detail['CourseRegistration'][0]['year_level_id'] != 0 ? $student_detail['CourseRegistration'][0]['year_level_id'] : 0);
            $lastRegisteredSectionID = (isset($student_detail['CourseRegistration'][0]['year_level_id']) && $student_detail['CourseRegistration'][0]['year_level_id'] != 0 ? $student_detail['CourseRegistration'][0]['year_level_id'] : 0);

            if ($lastRegisteredYearLevelID) {
                $lastRegisteredYearLevelName = $this->Section->YearLevel->field(
                    'YearLevel.name',
                    array('YearLevel.id' => $lastRegisteredYearLevelID)
                );
            }

            $student_section_exam_status = $this->Section->Student->get_student_section($student_id);

            if (!empty($student_section_exam_status)) {
                $collegess = $this->Section->College->find('list', array(
                    'conditions' => array(
                        'OR' => array(
                            'College.campus_id' => $student_section_exam_status['College']['campus_id'],
                            'College.stream' => $student_section_exam_status['College']['stream'],
                        ),
                        'College.active' => 1
                    ),
                    'order' => array('College.campus_id ASC', 'College.name ASC')
                ));
            } else {
                $collegess = $this->Section->College->find('list', array(
                    'conditions' => array(
                        'OR' => array(
                            'College.campus_id' => $student_detail['College']['campus_id'],
                            'College.stream' => $student_detail['College']['stream'],
                        ),
                        'College.active' => 1
                    ),
                    'order' => array('College.campus_id ASC', 'College.name ASC')
                ));
            }

            $collIdsToLook = array_keys($collegess);

            $departmentss = $this->Section->Department->find('list', array(
                'conditions' => array(
                    'Department.college_id' => $collIdsToLook,
                    'Department.active' => 1
                ),
            ));


            $deptIdsToLook = array_keys($departmentss);

            $conditions = array();
            $sections_detail = array();

            if (!empty($college_id) && !is_null($student_detail['Student']['department_id'])) {
                $conditions[] = array(
                    'Section.college_id' => $collIdsToLook,
                    'Section.program_id' => $student_program_id,
                    'Section.program_type_id' => $program_types_to_look,
                    'Section.archive' => 0,
                    'Section.academicyear LIKE ' => $lastRegisteredAcademicYear,
                );

                $conditions[] = array('Section.college_id' => $college_id);

                if (!empty($lastRegisteredSectionID) && $lastRegisteredSectionID) {
                    $conditions[] = array('Section.id <>' => $lastRegisteredSectionID);
                }

                if ($department_id == -1) {
                    $conditions[] = array('Section.department_id is null');
                } else {
                    if ($department_id == 0) {
                        // No department selected
                        $conditions[] = array('Section.department_id is null');
                    } else {
                        if (!empty($department_id) && $department_id > 0) {
                            $conditions[] = array('Section.department_id' => $department_id);
                        } else {
                            if (empty($department_id) && empty($lastRegisteredYearLevelName) || empty($year_level_name)) {
                                $conditions[] = array('Section.department_id is null');
                            }
                        }
                    }
                }

                if ((!empty($lastRegisteredYearLevelName) || !empty($year_level_name)) && !empty($department_id) && $department_id > 0) {
                    $year_levels_applicable = array();

                    $allYearLevels = ClassRegistry::init('YearLevel')->distinct_year_level();

                    $yl_name = (isset($year_level_name) && !empty($year_level_name) ? $year_level_name : $lastRegisteredYearLevelName);

                    if (!empty($allYearLevels)) {
                        if (in_array($yl_name, $allYearLevels)) {
                            foreach ($allYearLevels as $year_level) {
                                $year_levels_applicable[] = $year_level;
                                if (strcasecmp($yl_name, $year_level) == 0) {
                                    break;
                                }
                            }
                        }
                    }

                    debug($year_levels_applicable);

                    if (!empty($department_id) && $department_id > 0) {
                        if (!empty($year_levels_applicable)) {
                            $yearLevelIDs = $this->Section->YearLevel->find('list', array(
                                'conditions' => array(
                                    'YearLevel.department_id' => $department_id,
                                    'YearLevel.name' => $year_levels_applicable
                                ),
                                'fields' => array('YearLevel.id', 'YearLevel.id')
                            ));
                        } else {
                            $yearLevelIDs = $this->Section->YearLevel->find('list', array(
                                'conditions' => array(
                                    'YearLevel.department_id' => $department_id,
                                    'YearLevel.name LIKE ' => (isset($year_level_name) && !empty($year_level_name) ? $year_level_name : $lastRegisteredYearLevelName)
                                ),
                                'fields' => array('YearLevel.id', 'YearLevel.id')
                            ));
                        }
                    } else {
                        if (!empty($lastRegisteredYearLevelName) || !empty($year_level_name)) {
                            if (!empty($year_levels_applicable)) {
                                $yearLevelIDs = $this->Section->YearLevel->find('list', array(
                                    'conditions' => array(
                                        'YearLevel.department_id' => $deptIdsToLook,
                                        'YearLevel.name' => $year_levels_applicable
                                    ),
                                    'fields' => array('YearLevel.id', 'YearLevel.id')
                                ));
                            } else {
                                $yearLevelIDs = $this->Section->YearLevel->find('list', array(
                                    'conditions' => array(
                                        'YearLevel.department_id' => $deptIdsToLook,
                                        'YearLevel.name LIKE ' => (isset($year_level_name) && !empty($year_level_name) ? $year_level_name : $lastRegisteredYearLevelName)
                                    ),
                                    'fields' => array('YearLevel.id', 'YearLevel.id')
                                ));
                            }
                        }
                    }

                    if (!empty($yearLevelIDs)) {
                        $conditions[] = array('Section.year_level_id' => $yearLevelIDs);
                    }
                } else {
                    if (is_null(
                            $student_detail['Student']['department_id']
                        ) && (empty($year_level_name) || $department_id == -1 || $department_id == 0)) {
                        $conditions[] = array(
                            'Section.college_id' => (!empty($collIdsToLook) ? $collIdsToLook : $college_id),
                            'Section.department_id IS NULL',
                            'Section.academicyear' => (!empty($student_detail['CourseRegistration'][0]['academic_year']) ? $student_detail['CourseRegistration'][0]['academic_year'] : $student_detail['Student']['academicyear']),
                            'Section.archive' => 0,
                            /* 'OR' => array(
							'Section.year_level_id IS NULL',
							'Section.year_level_id = ""',
							'Section.year_level_id = 0'
						) */
                        );
                    }
                }

                //debug($conditions);

                //$sections_detail = array();

                if (!empty($conditions)) {
                    $sections_detail = $this->Section->find('all', array(
                        'conditions' => $conditions,
                        'contain' => array(
                            'Program' => array('id', 'name'),
                            'ProgramType' => array('id', 'name'),
                            'YearLevel' => array('id', 'name'),
                        ),
                        'fields' => array(
                            'Section.id',
                            'Section.name',
                            'Section.program_id',
                            'Section.year_level_id',
                            'Section.academicyear',
                            'Section.curriculum_id'
                        ),
                        'order' => array(
                            'Section.academicyear' => 'DESC',
                            'Section.program_id' => 'ASC',
                            'Section.year_level_id' => 'ASC',
                            'Section.id' => 'ASC',
                            'Section.name' => 'ASC'
                        ),
                    ));
                }
            }
        }

        if (!empty($sections_detail)) {
            foreach ($sections_detail as $seindex => $secvalue) {
                if (isset($secvalue['YearLevel']['name']) && !empty($secvalue['YearLevel']['name'])) {
                    $yn = $secvalue['YearLevel']['name'];
                } else {
                    if ($secvalue['Section']['program_id'] == PROGRAM_REMEDIAL) {
                        $yn = 'Remedial';
                    } else {
                        $yn = 'Pre/1st';
                    }
                }
                $sections[$secvalue['Program']['name']][$secvalue['Section']['id']] = $secvalue['Section']['name'] . ' (' . $yn . ', ' . $secvalue['Section']['academicyear'] . ')';
            }
        }

        $this->set(compact('sections'));

        $this->set('college_id', $college_id);
        $this->set('department_id', $department_id);
        $this->set('year_level_name', $year_level_name);
    }

    public function get_sections_by_dept_data_entry($department_id = "")
    {

        $this->layout = 'ajax';
        $sections = array();

        if (!empty($this->student_id)) {
            $student_program_id = $this->Section->Student->field('program_id', array('Student.id' => $this->student_id)
            );
            $student_section_id = $this->Section->StudentsSection->field(
                'section_id',
                array('StudentsSection.student_id' => $this->student_id, 'StudentsSection.archive' => 0)
            );

            $sections_detail = $this->Section->find('all', array(
                'conditions' => array(
                    'Section.department_id' => $department_id,
                    'Section.program_id' => $student_program_id
                ),
                'contain' => array(
                    'Program' => array('id', 'name'),
                    'YearLevel' => array('id', 'name'),
                    'ProgramType' => array('id', 'name')
                ),
                'fields' => array(
                    'Section.id',
                    'Section.name',
                    'Section.program_id',
                    'Section.year_level_id',
                    'Section.academicyear',
                    'Section.curriculum_id'
                ),
                'order' => array(
                    'Section.academicyear' => 'DESC',
                    'Section.program_id' => 'ASC',
                    'Section.year_level_id' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                ),
            ));
        } else {
            $sections_detail = $this->Section->find('all', array(
                'conditions' => array(
                    'Section.department_id' => $department_id
                ),
                'contain' => array(
                    'Program' => array('id', 'name'),
                    'YearLevel' => array('id', 'name'),
                    'ProgramType' => array('id', 'name')
                ),
                'fields' => array(
                    'Section.id',
                    'Section.name',
                    'Section.program_id',
                    'Section.year_level_id',
                    'Section.academicyear',
                    'Section.curriculum_id'
                ),
                'order' => array(
                    'Section.academicyear' => 'DESC',
                    'Section.program_id' => 'ASC',
                    'Section.year_level_id' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                ),
            ));
        }

        if (!empty($sections_detail)) {
            foreach ($sections_detail as $seindex => $secvalue) {
                $sections[$secvalue['Program']['name']][$secvalue['Section']['id']] = $secvalue['Section']['name'] . ' (' . (isset($secvalue['YearLevel']['name']) && !empty($secvalue['YearLevel']['name']) ? $secvalue['YearLevel']['name'] : ($secvalue['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $secvalue['Section']['academicyear'] . ')';
            }
        }
        $this->set(compact('sections'));
    }


    public function get_sections_by_academic_year($year = null, $ac = null, $department_id = null)
    {

        $this->layout = 'ajax';
        $sections = array();

        $sections_detail = $this->Section->find('all', array(
            'conditions' => array(
                'Section.department_id' => $department_id,
                'Section.academicyear like ' => $year . '/' . $ac,
            ),
            'contain' => array(
                'Program' => array('id', 'name'),
                'YearLevel' => array('id', 'name'),
                'ProgramType' => array('id', 'name')
            ),
            'fields' => array(
                'Section.id',
                'Section.name',
                'Section.program_id',
                'Section.year_level_id',
                'Section.academicyear',
                'Section.curriculum_id'
            ),
            'order' => array(
                'Section.academicyear' => 'DESC',
                'Section.program_id' => 'ASC',
                'Section.year_level_id' => 'ASC',
                'Section.id' => 'ASC',
                'Section.name' => 'ASC'
            ),
        ));

        if (!empty($sections_detail)) {
            foreach ($sections_detail as $seindex => $secvalue) {
                $sections[$secvalue['Program']['name']][$secvalue['Section']['id']] = $secvalue['Section']['name'] . ' (' . (isset($secvalue['YearLevel']['name']) && !empty($secvalue['YearLevel']['name']) ? $secvalue['YearLevel']['name'] : ($secvalue['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $secvalue['Section']['academicyear'] . ')';
            }
        }
        $this->set(compact('sections'));
    }

    public function get_sections_by_year_level($yearLevel = null, $student_id = null, $acYrStart = null)
    {

        $this->layout = 'ajax';
        $sections = array();
        $sections_organized_by_acy = array();


        if (isset($yearLevel) && isset($student_id) && !empty($student_id)) {
            $student_detail = $this->Section->Student->find('first', array(
                'conditions' => array(
                    'Student.id' => $student_id
                ),
                'contain' => array(
                    'AcceptedStudent',
                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                )
            ));

            //$next_ay_and_s = ClassRegistry::init('StudentExamStatus')->getNextSemster($this->AcademicYear->current_academicyear(), null);
            //$possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange($student_detail['AcceptedStudent']['academicyear'], $next_ay_and_s['academic_year']);

            if (isset($acYrStart) && !empty($acYrStart)) {
                $acYrStart = str_replace('-', '/', $acYrStart);
                debug($acYrStart);
                $possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange(
                    $acYrStart,
                    $this->AcademicYear->current_academicyear()
                );
            } else {
                $possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange(
                    $student_detail['AcceptedStudent']['academicyear'],
                    $this->AcademicYear->current_academicyear()
                );
            }

            $currentStudentsSection = $this->Section->StudentsSection->field('StudentsSection.section_id', array(
                'StudentsSection.student_id' => $student_id,
                'StudentsSection.archive' => 0
            ));

            $previousStudentsSection = $this->Section->StudentsSection->find('list', array(
                'conditions' => array(
                    'StudentsSection.student_id' => $student_id,
                    'StudentsSection.archive' => 1
                ),
                'group' => array(
                    'StudentsSection.student_id',
                    'StudentsSection.section_id'
                ),
                'fields' => array(
                    'StudentsSection.section_id',
                    'StudentsSection.section_id'
                ),
                'order' => 'StudentsSection.id DESC',
            ));

            $excludeSections = array();

            if (!empty($previousStudentsSection) || !empty($currentStudentsSection)) {
                if (!empty($previousStudentsSection) && !empty($currentStudentsSection)) {
                    $excludeSections = $previousStudentsSection;
                    array_push($excludeSections, $currentStudentsSection);
                } else {
                    if (!empty($previousStudentsSection)) {
                        $excludeSections = $previousStudentsSection;
                    } else {
                        array_push($excludeSections, $currentStudentsSection);
                    }
                }
            } else {
                array_push($excludeSections, 0);
            }

            $excludeSections = array_values($excludeSections);
            debug($excludeSections);

            $program_types_to_look = $this->__getEquivalentProgramTypes($student_detail['Student']['program_type_id']);
            debug($program_types_to_look);

            if (!is_null($yearLevel) && $yearLevel != 0 && ($this->Session->read(
                        'Auth.User'
                    )['role_id'] != ROLE_DEPARTMENT || !is_numeric($yearLevel))) {
                $yearLevel = $this->Section->YearLevel->field(
                    'id',
                    array(
                        'YearLevel.department_id' => $student_detail['Student']['department_id'],
                        'YearLevel.name' => $yearLevel
                    )
                );
            }

            debug($yearLevel);

            if (!is_null($yearLevel) && !empty($yearLevel) && $yearLevel != 0) {
                $sections = $this->Section->find('all', array(
                    'conditions' => array(
                        'NOT' => array(
                            'Section.id' => $excludeSections,
                        ),
                        'Section.year_level_id' => $yearLevel,
                        'Section.academicyear' => $possibleAcademicYears,
                        'Section.program_id' => $student_detail['Student']['program_id'],
                        'Section.program_type_id' => $program_types_to_look,
                        'Section.department_id' => $student_detail['Student']['department_id'],
                        'Section.curriculum_id' => $student_detail['Student']['curriculum_id'],
                        /* 'OR' => array(
							'Section.curriculum_id IS NULL',
							'Section.curriculum_id' => $student_detail['Student']['curriculum_id'] ,
						), */
                        'Section.archive' => 0
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'ProgramType' => array('id', 'name')
                    ),
                    'fields' => array('id', 'name', 'program_id', 'year_level_id', 'academicyear'),
                    'order' => array(
                        'Section.academicyear' => 'DESC',
                        'Section.program_id' => 'ASC',
                        'Section.year_level_id' => 'ASC',
                        'Section.id' => 'ASC',
                        'Section.name' => 'ASC'
                    ),
                ));
            } else {
                $sections = $this->Section->find('all', array(
                    'conditions' => array(
                        'NOT' => array(
                            'Section.id' => $excludeSections,
                        ),
                        'Section.college_id' => $student_detail['Student']['college_id'],
                        'Section.academicyear' => $possibleAcademicYears,
                        'Section.program_id' => $student_detail['Student']['program_id'],
                        'Section.program_type_id' => $program_types_to_look,
                        'Section.department_id is null',
                        'Section.archive' => 0
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                        'ProgramType' => array('id', 'name')
                    ),
                    'fields' => array('id', 'name', 'program_id', 'year_level_id', 'academicyear'),
                    'order' => array(
                        'Section.academicyear' => 'DESC',
                        'Section.program_id' => 'ASC',
                        'Section.year_level_id' => 'ASC',
                        'Section.id' => 'ASC',
                        'Section.name' => 'ASC'
                    ),
                ));
            }

            if (!empty($sections)) {
                foreach ($sections as $k => $v) {
                    if (!empty($v['YearLevel']['name'])) {
                        $sections_organized_by_acy[$v['Section']['academicyear']][$v['Section']['id']] = $v['Section']['name'] . ' (' . $v['YearLevel']['name'] . ', ' . $v['Section']['academicyear'] . ')';
                    } else {
                        $sections_organized_by_acy[$v['Section']['academicyear']][$v['Section']['id']] = $v['Section']['name'] . '(' . ($v['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial, ' : 'Pre/1st, ') . $v['Section']['academicyear'] . ')';
                    }
                }
            }
        }

        $this->set(compact('sections', 'sections_organized_by_acy'));
    }

    public function get_modal_box($section_id = null)
    {

        $this->layout = 'ajax';
        if (!empty($section_id)) {
            //debug($section_id);

            $selected_sections_students = array();
            $unupgradable_selected_sections_students = array();

            //get section active students
            //$selected_sections_students = $this->Section->getSectionActiveStudents($section_id);
            $selected_sections_students = $this->Section->getSectionActiveStudentsRegistered($section_id);

            //get unupgradable students among section all active students
            $academicyear = $this->Section->field('Section.academicyear', array('Section.id' => $section_id));

            if (!empty($selected_sections_students)) {
                foreach ($selected_sections_students as $sssk => $sssv) {
                    $student_status = $this->Section->Student->StudentExamStatus->isStudentPassed(
                        $sssv['StudentsSection']['student_id'],
                        $academicyear
                    );
                    $all_valid_grades = $this->Section->chceck_all_registered_added_courses_are_graded(
                        $sssv['StudentsSection']['student_id'],
                        $section_id,
                        1,
                        ''
                    );

                    if ($student_status == 4 || $student_status == 2 || !$all_valid_grades) {
                        $unupgradable_selected_sections_students[] = $sssv['StudentsSection']['student_id'];
                    }
                }
            }

            debug($unupgradable_selected_sections_students);

            //find the dessmisal status name
            $status_name = ClassRegistry::init('AcademicStatus')->field(
                'AcademicStatus.name',
                array('AcademicStatus.id' => DISMISSED_ACADEMIC_STATUS_ID)
            );

            $students_details = array();

            if (!empty($unupgradable_selected_sections_students)) {
                foreach ($unupgradable_selected_sections_students as $student_id) {
                    $students_details[$student_id] = $this->Section->Student->get_student_details($student_id);
                }
            }
            //debug($students_details);
            $this->set(compact('students_details', 'status_name'));
        }
    }

    public function _get_unqualified_students_count($selected_sections = null, $academicYear = null)
    {

        $selected_sections_students = array();
        $categorize_selected_sections_students = array();
        $sectionunupgradablestudentscount = array();

        if (isset($selected_sections) && !empty($selected_sections)) {
            foreach ($selected_sections as $ssk => $ssv) {
                //get section active students
                //$selected_sections_students[$ssv] = $this->Section->getSectionActiveStudents($ssv);
                $selected_sections_students[$ssv] = $this->Section->getSectionActiveStudentsRegistered($ssv);

                if (empty($academicYear)) {
                    $academicYear = $this->Section->field('Section.academicyear', array('Section.id' => $ssv));
                }

                //categorize section active students as upgradable and unupgradable students

                if (isset($selected_sections_students[$ssv]) && !empty($selected_sections_students[$ssv])) {
                    //debug($ssv);
                    //debug($selected_sections_students[$ssv]);
                    $start = microtime(true);
                    foreach ($selected_sections_students[$ssv] as $sssk => $sssv) {
                        if (isset($sssv['StudentsSection']['student_id']) && !empty($sssv['StudentsSection']['student_id'])) {
                            /* if ($sssv['StudentsSection']['student_id'] == 81595) {
								debug($sssv['StudentsSection']['student_id']);
							} */
                            $student_status = $this->Section->Student->StudentExamStatus->isStudentPassed(
                                $sssv['StudentsSection']['student_id'],
                                $academicYear
                            );
                            $all_valid_grades = $this->Section->chceck_all_registered_added_courses_are_graded(
                                $sssv['StudentsSection']['student_id'],
                                $ssv,
                                1,
                                ''
                            );
                            //debug($student_status);

                            if ($student_status == 4 || $student_status == 2 || !$all_valid_grades) {
                                $categorize_selected_sections_students[$ssv]['unupgradable'][] = $sssv['StudentsSection']['student_id'];
                            } else {
                                $categorize_selected_sections_students[$ssv]['upgradable'][] = $sssv['StudentsSection']['student_id'];
                                debug($sssv['StudentsSection']['student_id']);
                            }
                        }
                    }

                    //debug($start);
                    $time_elapsed_secs = microtime(true) - $start;
                    echo "Time elapsed = " . $time_elapsed_secs;

                    //Set unupgradable section number
                    if (isset($categorize_selected_sections_students[$ssv]['unupgradable']) && !empty($categorize_selected_sections_students[$ssv]['unupgradable'])) {
                        //$sectionName = $this->Section->field('Section.name',array('Section.id'=>$ssv));
                        $sectionunupgradablestudentscount[$ssv] = count(
                            $categorize_selected_sections_students[$ssv]['unupgradable']
                        );
                    }

                    $time_elapsed_secs = microtime(true) - $start;
                    echo "Time elapsed = " . $time_elapsed_secs;
                    //debug($ssv);
                }
                //debug($ssv);
            }
        }

        if (isset($sectionunupgradablestudentscount) && !empty($sectionunupgradablestudentscount)) {
            return $sectionunupgradablestudentscount;
        }
        return array();
    }

    public function downgrade_sections()
    {

        if ($this->Session->read('Auth.User')['role_id'] != ROLE_DEPARTMENT) {
            $this->Flash->warning('You need to have department role to downgrade section year levels!');
            $this->redirect('/');
        }

        $programs = $this->Section->Program->find('list');
        $programTypes = $this->Section->ProgramType->find('list');
        $yearLevels = $this->Section->YearLevel->find(
            'list',
            array('conditions' => array('YearLevel.department_id' => $this->department_id))
        );
        $isbeforesearch = 1;

        $this->set(compact('programs', 'programTypes', 'isbeforesearch', 'yearLevels'));

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $isbeforesearch = 0;
            $selected_program = $this->request->data['Section']['program_id'];
            $selected_program_type = $this->request->data['Section']['program_type_id'];
            $selected_academicyear = $this->request->data['Section']['academicyear'];
            $selected_year_level = $this->request->data['Section']['year_level_id'];

            $sections = $this->Section->find('all', array(
                'fields' => array('Section.id', 'Section.name'),
                'conditions' => array(
                    'Section.department_id' => $this->department_id,
                    'Section.program_id' => $selected_program,
                    'Section.program_type_id' => $selected_program_type,
                    'Section.academicyear' => $selected_academicyear,
                    'Section.year_level_id LIKE' => $selected_year_level,
                    'Section.archive' => 0,
                    'Section.id NOT IN (select section_id from published_courses)'
                ),
                'recursive' => -1
            ));

            $formateddowngradableSections = array();

            if (!empty($sections)) {
                foreach ($sections as $usk => $usv) {
                    $formateddowngradableSections[$usv['Section']['id']] = $usv['Section']['name'];
                }
            }

            $this->Session->write('formateddowngradableSections', $formateddowngradableSections);
            $this->set(compact('formateddowngradableSections', 'isbeforesearch'));
        }

        if (!empty($this->request->data) && isset($this->request->data['downgrade'])) {
            $selectedSection = array();

            if (!empty($this->request->data['Section']['Downgradable_Selected'])) {
                foreach ($this->request->data['Section']['Downgradable_Selected'] as $k => $v) {
                    if ($v != 0) {
                        $selectedSection[$k] = $k;
                    }
                }
            }

            if (!empty($selectedSection)) {
                $downgradeSection = $this->Section->downgradeSelectedSection($selectedSection);
                if (!empty($downgradeSection['success']) && empty($downgradeSection['unsuccess'])) {
                    $this->Flash->success(
                        'Section ' . count($downgradeSection['success']) . ' have been downgraded successfully.'
                    );
                } else {
                    if (!empty($downgradeSection['success']) && !empty($downgradeSection['unsuccess'])) {
                        $this->Flash->success(
                            'Section ' . count(
                                $downgradeSection['success']
                            ) . ' have been downgraded successfully but ' . count($downgradeSection['unsuccess']) . '.'
                        );
                    }
                }
                return $this->redirect(array('action' => 'display_sections'));
            } else {
                $this->Flash->error('Please select Section.');
            }

            $this->request->data['search'] = true;

            $formateddowngradableSections = null;
            $isbeforesearch = 0;

            if ($this->Session->read('formateddowngradableSections')) {
                $formateddowngradableSections = $this->Session->read('formateddowngradableSections');
            }
            $this->set(compact('formateddowngradableSections', 'isbeforesearch'));
        }
    }

    public function dispaly_section_less_students()
    {

        // $programs = $this->Section->Program->find('list');
        // $programTypes = $this->Section->ProgramType->find('list');
        $this->__init_search_sections();
        $isbeforesearch = 1;

        /* if ($this->role_id == ROLE_DEPARTMENT) {
			$yearLevels = $this->Section->YearLevel->find('list', array('conditions' => array('YearLevel.department_id' => $this->department_id)));
		} else  {
			$yearLevels = $this->Section->YearLevel->distinct_year_level();
		} */

        if (isset($this->request->data['Section']['program_id'])) {
            $selected_program = $this->request->data['Section']['program_id'];
        } else {
            $selected_program = array_values($this->program_ids)[0];
        }

        if (isset($this->request->data['Section']['program_type_id'])) {
            $selected_program_type = $this->request->data['Section']['program_type_id'];
            $program_types_to_look = $this->Section->getEquivalentProgramTypes($selected_program_type);
        } else {
            $selected_program_type = array_values($this->program_type_ids)[0];
            $program_types_to_look = $this->Section->getEquivalentProgramTypes($selected_program_type);
        }

        if (isset($this->request->data['Section']['academicyear'])) {
            $academicyear = $this->request->data['Section']['academicyear'];
        } else {
            $academicyear = $this->AcademicYear->current_academicyear();
        }

        debug($academicyear);
        debug($selected_program);
        debug($selected_program_type);
        debug($program_types_to_look);

        $this->set(compact('programs', 'programTypes', 'isbeforesearch', 'yearLevels'));


        $sectionlessStudents_ids = array();

        $selected_acy_exploded = explode('/', $academicyear);
        $previous_academic_year = ($selected_acy_exploded[0] - 1) . '/' . ($selected_acy_exploded[1] - 1);

        $all_departments_sections_created_after_previous_acy_sections = array();
        $all_freshman_sections_created_after_previous_acy_sections = array();

        debug($previous_academic_year);

        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            $this->__init_clear_session_filters();
            $this->__init_search_sections();

            $isbeforesearch = 0;
            // $selected_program = $this->request->data['Section']['program_id'];
            // $selected_program_type = $this->request->data['Section']['program_type_id'];

            $program_type_ids = "'" . implode("', '", $program_types_to_look) . "'";

            if (!empty($this->department_ids)) {
                $department_ids = "'" . implode("', '", $this->department_ids) . "'";
            } else {
                $department_ids = 0;
            }

            if (!empty($this->college_ids)) {
                $college_ids = "'" . implode("', '", $this->college_ids) . "'";
            } else {
                $college_ids = 0;
            }

            debug($college_ids);
            debug($department_ids);
            debug($program_type_ids);


            if ($this->role_id == ROLE_DEPARTMENT) {
                /* $sections = $this->Section->find('list', array(
					'fields' => array('Section.id'),
					'conditions' => array(
						'Section.department_id' => $this->department_id,
						'Section.program_id' => $selected_program,
						'Section.program_type_id' => $program_types_to_look,
						'Section.academicyear LIKE ' => $academicyear . '%',
					),
					'recursive' => -1
				));

				$sectionlessStudents_ids = $this->Section->StudentsSection->find('all', array(
					'fields' => array('DISTINCT StudentsSection.student_id'),
					'conditions' => array(
						'StudentsSection.archive' => 1,
						'StudentsSection.section_id' => $sections,
						//'StudentsSection.student_id NOT IN (select student_id from graduate_lists)'
						"StudentsSection.student_id IN (select id from students where graduated = 0 AND department_id IN ($department_ids) AND program_id = $selected_program AND program_type_id in ($program_type_ids))"
					),
					'recursive' => -1
				)); */

                $previous_ac_year_sections = $this->Section->find('list', array(
                    'fields' => array('Section.id', 'Section.id'),
                    'conditions' => array(
                        'Section.department_id' => $this->department_id,
                        'Section.program_id' => $selected_program,
                        'Section.program_type_id' => $program_types_to_look,
                        'Section.academicyear LIKE ' => $previous_academic_year . '%',
                    ),
                    'recursive' => -1
                ));

                debug($previous_ac_year_sections);

                $selected_ac_year_sections = $this->Section->find('list', array(
                    'fields' => array('Section.id', 'Section.id'),
                    'conditions' => array(
                        'Section.department_id' => $this->department_id,
                        'Section.program_id' => $selected_program,
                        'Section.program_type_id' => $program_types_to_look,
                        'Section.academicyear LIKE ' => $academicyear . '%',
                    ),
                    'recursive' => -1
                ));

                $last_section_of_selected_ac_year_sections = $this->Section->find('first', array(
                    'conditions' => array(
                        'Section.department_id' => $this->department_id,
                        'Section.program_id' => $selected_program,
                        'Section.program_type_id' => $program_types_to_look,
                        'Section.academicyear LIKE ' => (isset($previous_academic_year) ? $previous_academic_year : $academicyear) . '%',
                    ),
                    'fields' => array('Section.id', 'Section.created'),
                    'order' => array('Section.created' => (isset($previous_academic_year) ? 'DESC' : 'ASC')),
                    'recursive' => -1
                ));

                debug($last_section_of_selected_ac_year_sections);

                if (!empty($last_section_of_selected_ac_year_sections)) {
                    $all_departments_sections_created_after_previous_acy_sections = $this->Section->find('list', array(
                        'conditions' => array(
                            'Section.department_id' => $this->department_id,
                            'Section.program_id' => $selected_program,
                            'Section.program_type_id' => $program_types_to_look,
                            'OR' => array(
                                'Section.academicyear LIKE ' => $academicyear . '%',
                                'Section.id >=' => $last_section_of_selected_ac_year_sections['Section']['id'],
                                'Section.created >= ' => $last_section_of_selected_ac_year_sections['Section']['created'],
                            )
                        ),
                        'fields' => array('Section.id', 'Section.id'),
                        'recursive' => -1
                    ));
                } else {
                    $all_departments_sections_created_after_previous_acy_sections = $this->Section->find('list', array(
                        'conditions' => array(
                            'Section.department_id' => $this->department_id,
                            'Section.program_id' => $selected_program,
                            'Section.program_type_id' => $program_types_to_look,
                            'Section.academicyear LIKE ' => $academicyear . '%',
                        ),
                        'fields' => array('Section.id', 'Section.id'),
                        'recursive' => -1
                    ));
                }

                if (!empty($previous_ac_year_sections)) {
                    $sectionlessStudents_ids = $this->Section->StudentsSection->find('all', array(
                        'fields' => array('DISTINCT StudentsSection.student_id'),
                        'conditions' => array(
                            'StudentsSection.archive' => 1,
                            'StudentsSection.section_id' => $previous_ac_year_sections,
                            //"StudentsSection.student_id IN (select id from students where graduated = 0 AND department_id IN ($department_ids) AND program_id = $selected_program AND program_type_id in ($program_type_ids))",
                            "StudentsSection.student_id IN (select st.id from students st JOIN course_registrations creg ON creg.student_id = st.id where st.graduated = 0 AND st.department_id IN ($department_ids) AND st.program_id = $selected_program AND st.program_type_id in ($program_type_ids) AND creg.academic_year = '$previous_academic_year' GROUP BY creg.academic_year, creg.student_id, creg.semester)",
                        ),
                        'recursive' => -1
                    ));
                }
            } else {
                if ($this->role_id == ROLE_COLLEGE) {
                    /* $college = $this->Section->College->find('first', array('conditions' => array('College.id' => $this->college_id), 'recursive' => -1));
				$campuses = $this->Section->College->Campus->find('first', array('conditions' => array('Campus.id' => $college['College']['campus_id']), 'recursive' => -1));
				debug($campuses);

				$collegesBelongs = $this->Section->College->Campus->find('list', array('conditions' => array('Campus.available_for_college' => $campuses['Campus']['available_for_college']), 'fields' => array('Campus.id', 'Campus.id')));
				$collegeLists = $this->Section->College->find('list', array('conditions' => array('College.campus_id' => $collegesBelongs), 'fields' => array('College.id', 'College.id')));
				debug($collegeLists); */

                    /* $sections = $this->Section->find('list', array(
					'fields' => array('Section.id'),
					'conditions' => array(
						//'Section.college_id' => $collegeLists,
						'Section.college_id' => $this->college_ids,
						'Section.department_id is null',
						'Section.academicyear LIKE ' => $academicyear . '%',
						'Section.program_id' => $selected_program,
						'Section.program_type_id' => $program_types_to_look
					),
					'recursive' => -1
				));

				$sectionlessStudents_ids = $this->Section->StudentsSection->find('all', array(
					'fields' => array('DISTINCT StudentsSection.student_id'),
					'conditions' => array(
						'StudentsSection.archive' => 1,
						'StudentsSection.section_id ' => $sections,
						"StudentsSection.student_id IN (select id from students where college_id IN ($college_ids) and department_id is null and graduated = 0 AND program_id = $selected_program AND program_type_id in ($program_type_ids))",
						'StudentsSection.student_id IN (select student_id from course_registrations where year_level_id is null OR year_level_id = 0 or year_level_id = "")',
						//'StudentsSection.student_id NOT IN (select student_id from graduate_lists)'
					),
					'recursive' => -1
				)); */


                    $previous_ac_year_sections = $this->Section->find('list', array(
                        'fields' => array('Section.id', 'Section.id',),
                        'conditions' => array(
                            'Section.college_id' => $this->college_id,
                            'Section.department_id is null',
                            'Section.academicyear LIKE ' => $previous_academic_year . '%',
                            'Section.program_id' => $selected_program,
                            'Section.program_type_id' => $program_types_to_look
                        ),
                        'recursive' => -1
                    ));

                    $selected_ac_year_sections = $this->Section->find('list', array(
                        'fields' => array('Section.id', 'Section.id'),
                        'conditions' => array(
                            'Section.college_id' => $this->college_id,
                            'Section.department_id is null',
                            'Section.academicyear LIKE ' => $academicyear . '%',
                            'Section.program_id' => $selected_program,
                            'Section.program_type_id' => $program_types_to_look
                        ),
                        'recursive' => -1
                    ));


                    $last_section_of_selected_ac_year_sections = $this->Section->find('first', array(
                        'conditions' => array(
                            'Section.college_id' => $this->college_id,
                            'Section.department_id is null',
                            'Section.program_id' => $selected_program,
                            'Section.program_type_id' => $program_types_to_look,
                            'Section.academicyear LIKE ' => (isset($previous_academic_year) ? $previous_academic_year : $academicyear) . '%',
                        ),
                        'fields' => array('Section.id', 'Section.created'),
                        'order' => array('Section.created' => (isset($previous_academic_year) ? 'DESC' : 'ASC')),
                        'recursive' => -1
                    ));

                    debug($last_section_of_selected_ac_year_sections);

                    if (!empty($last_section_of_selected_ac_year_sections)) {
                        $all_freshman_sections_created_after_previous_acy_sections = $this->Section->find('list', array(
                            'conditions' => array(
                                'Section.college_id' => $this->college_id,
                                'Section.department_id is null',
                                'Section.program_id' => $selected_program,
                                'Section.program_type_id' => $program_types_to_look,
                                'OR' => array(
                                    'Section.academicyear LIKE ' => $academicyear . '%',
                                    'Section.id >=' => $last_section_of_selected_ac_year_sections['Section']['id'],
                                    'Section.created >= ' => $last_section_of_selected_ac_year_sections['Section']['created'],
                                )
                            ),
                            'fields' => array('Section.id', 'Section.id'),
                            'recursive' => -1
                        ));
                    } else {
                        $all_freshman_sections_created_after_previous_acy_sections = $this->Section->find('list', array(
                            'conditions' => array(
                                'Section.college_id' => $this->college_id,
                                'Section.department_id is null',
                                'Section.program_id' => $selected_program,
                                'Section.program_type_id' => $program_types_to_look,
                                'Section.academicyear LIKE ' => $academicyear . '%',
                            ),
                            'fields' => array('Section.id', 'Section.id'),
                            'recursive' => -1
                        ));
                    }

                    if (!empty($previous_ac_year_sections)) {
                        $sectionlessStudents_ids = $this->Section->StudentsSection->find('all', array(
                            'fields' => array('DISTINCT StudentsSection.student_id'),
                            'conditions' => array(
                                'StudentsSection.archive' => 1,
                                'StudentsSection.section_id' => $previous_ac_year_sections,
                                //"StudentsSection.student_id IN (select id from students where college_id IN ($college_ids) and department_id is null and graduated = 0 AND program_id = $selected_program AND program_type_id in ($program_type_ids))",
                                "StudentsSection.student_id IN (select st.id from students st JOIN course_registrations creg ON creg.student_id = st.id where st.college_id IN ($college_ids) and st.department_id is null and st.graduated = 0 AND st.program_id = $selected_program AND st.program_type_id in ($program_type_ids) AND (creg.year_level_id is null OR creg.year_level_id = 0 or creg.year_level_id = '') AND creg.academic_year = '$previous_academic_year' GROUP BY creg.academic_year, creg.student_id, creg.semester)",
                            ),
                            'recursive' => -1
                        ));
                    }
                }
            }

            $sectionless_students_last_sections_details = array();

            if (!empty($sectionlessStudents_ids)) {
                foreach ($sectionlessStudents_ids as $in => &$v) {
                    if (isset($all_freshman_sections_created_after_previous_acy_sections) && !empty($all_freshman_sections_created_after_previous_acy_sections)) {
                        $is_section_less = $this->Section->StudentsSection->find(
                            'count',
                            array(
                                'conditions' => array(
                                    'StudentsSection.archive' => 0,
                                    'StudentsSection.student_id' => $v['StudentsSection']['student_id'],
                                    'StudentsSection.section_id' => $all_freshman_sections_created_after_previous_acy_sections
                                )
                            )
                        );
                    } else {
                        if (isset($all_departments_sections_created_after_previous_acy_sections) && !empty($all_departments_sections_created_after_previous_acy_sections)) {
                            $is_section_less = $this->Section->StudentsSection->find(
                                'count',
                                array(
                                    'conditions' => array(
                                        'StudentsSection.archive' => 0,
                                        'StudentsSection.student_id' => $v['StudentsSection']['student_id'],
                                        'StudentsSection.section_id' => $all_departments_sections_created_after_previous_acy_sections
                                    )
                                )
                            );
                        } else {
                            $is_section_less = $this->Section->StudentsSection->find(
                                'count',
                                array(
                                    'conditions' => array(
                                        'StudentsSection.archive' => 0,
                                        'StudentsSection.student_id' => $v['StudentsSection']['student_id']
                                    )
                                )
                            );
                        }
                    }

                    if ($is_section_less > 0) {
                        unset($sectionlessStudents_ids[$in]);
                    } else {
                        $have_any_section_assignments_in_later_acys = 0;

                        if (!empty($all_departments_sections_created_after_previous_acy_sections) || !empty($all_freshman_sections_created_after_previous_acy_sections)) {
                            $have_any_section_assignments_in_later_acys = $this->Section->StudentsSection->find(
                                'count',
                                array(
                                    'conditions' => array(
                                        'StudentsSection.section_id' => (!empty($all_departments_sections_created_after_previous_acy_sections) ? $all_departments_sections_created_after_previous_acy_sections : $all_freshman_sections_created_after_previous_acy_sections),
                                        'StudentsSection.student_id' => $v['StudentsSection']['student_id']
                                    )
                                )
                            );
                        }

                        if ($have_any_section_assignments_in_later_acys > 0) {
                            unset($sectionlessStudents_ids[$in]);
                        } else {
                            //check
                            //$exclude = $this->Section->dropOutWithDrawAfterLastRegistrationNotReadmittedExcludeFromSectionless($v['StudentsSection']['student_id'], $this->AcademicYear->current_academicyear());
                            $exclude = $this->Section->dropOutWithDrawAfterLastRegistrationNotReadmittedExcludeFromSectionless(
                                $v['StudentsSection']['student_id'],
                                $academicyear
                            );
                            if ($exclude == 1) {
                                unset($sectionlessStudents_ids[$in]);
                            }
                        }
                    }
                }

                $sectionless_students_last_sections_details = $this->Section->get_sectionless_students_last_sections(
                    $sectionlessStudents_ids
                );
            }

            $this->set(compact('sectionless_students_last_sections_details', 'isbeforesearch'));
        }
    }

    public function _check_the_record_in_archive($section_id = null, $student_id = null)
    {

        $studentSection_id = $this->Section->StudentsSection->field('StudentsSection.id', array(
            'StudentsSection.student_id' => $student_id,
            'StudentsSection.section_id' => $section_id,
            'StudentsSection.archive' => 1
        ));
        return $studentSection_id;
    }


    public function un_assigned_summeries($selectedAcademicYear)
    {

        $this->layout = 'ajax';
        //debug($this->request->data);

        if (!empty($selectedAcademicYear)) {
            $academicYear = str_replace("-", "/", $selectedAcademicYear);
            $sselectedAcademicYear = $academicYear;
        } else {
            $academicYear = $sselectedAcademicYear = $this->AcademicYear->current_academicyear();
        }

        if (!isset($selectedProgram)) {
            $selectedProgram = array_values($this->program_ids)[0];
        }

        if (!isset($selectedProgramType)) {
            $selectedProgramType = array_values($this->program_type_ids)[0];
        }

        $curriculums = array();

        if ($this->role_id == ROLE_DEPARTMENT) {
            $curriculums = ClassRegistry::init('Curriculum')->find('list', array(
                'conditions' => array(
                    'Curriculum.department_id' => $this->department_ids,
                    'Curriculum.program_id' => $selectedProgram,
                    'Curriculum.registrar_approved' => 1,
                    'Curriculum.active' => 1
                ),
                'fields' => array('Curriculum.id', 'Curriculum.curriculum_detail'),
                'order' => array('Curriculum.program_id' => 'ASC', 'Curriculum.created' => 'DESC'),
            ));

            if (empty($selectedYearLevelId)) {
                $yearLevelsss = $this->Section->YearLevel->find(
                    'list',
                    array(
                        'conditions' => array(
                            'YearLevel.department_id' => $this->department_ids,
                            'YearLevel.name' => $this->year_levels
                        )
                    )
                );

                if (!empty($yearLevelsss)) {
                    $selectedYearLevelId = array_keys($yearLevelsss)[0];
                    $selectedYearLevelName = array_values($yearLevelsss)[0];
                } else {
                    $selectedYearLevelId = null;
                    $selectedYearLevelName = null;
                }
            } else {
                $yearLevelsss = $this->Section->YearLevel->find(
                    'list',
                    array(
                        'conditions' => array(
                            'YearLevel.department_id' => $this->department_ids,
                            'YearLevel.id' => $selectedYearLevelId
                        )
                    )
                );

                if (!empty($yearLevelsss)) {
                    $selectedYearLevelId = array_keys($yearLevelsss)[0];
                    $selectedYearLevelName = array_values($yearLevelsss)[0];
                } else {
                    $selectedYearLevelId = null;
                    $selectedYearLevelName = null;
                }
            }

            if (!isset($selectedCurriculumID) && !empty($curriculums)) {
                $selectedCurriculumID = array_keys($curriculums)[0];
                $selectedCurriculumName = array_values($curriculums)[0];
            } else {
                $selectedCurriculumID = '%';
                $selectedCurriculumName = null;
            }
        } else {
            $selectedYearLevelId = null;
            $selectedYearLevelName = null;
            $selectedCurriculumID = null;
            $selectedCurriculumName = null;
        }

        $summary_data = $this->Section->getsectionlessstudentsummary(
            $academicYear,
            $this->college_id,
            $this->department_id,
            $this->role_id
        );

        $curriculum_unattached_student_count = $this->Section->getcurriculumunattachedstudentsummary(
            $academicYear,
            $this->college_id,
            $this->department_id,
            $this->role_id
        );
        $collegename = $this->Section->College->field('College.name', array('College.id' => $this->college_id));
        $departmentname = $this->Section->Department->field(
            'Department.name',
            array('Department.id' => $this->department_id)
        );
        $departmentshortname = $this->Section->Department->field(
            'Department.shortname',
            array('Department.id' => $this->department_id)
        );

        $yearLevels = $this->Section->YearLevel->find(
            'list',
            array('conditions' => array('YearLevel.department_id' => $this->department_id))
        );
        // $programs = $this->Section->Program->find('list');
        // $programTypes = $this->Section->ProgramType->find('list');

        $programss = $this->Section->Program->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
        );
        $program_typess = $programTypess = $this->Section->ProgramType->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
        );

        $thisacademicyear = $academicYear;

        $GCyear = substr(($academicYear), 0, 4);
        $GCmonth = date('n');
        $GCday = date('j');

        if ($GCmonth >= 9) {
            $GCyear = $GCyear;
        } else {
            $GCyear = $GCyear + 1;
        }

        $ETY = $this->EthiopicDateTime->GetEthiopicYear($GCday, $GCmonth, $GCyear);
        if ($GCmonth == 9) {
            //$ETY+=1;
        }

        $FixedSectionName = $departmentshortname . $ETY;
        //echo $FixedSectionName;

        $this->set(
            compact(
                'departmentname',
                'yearLevels',
                'variable_section_name_array',
                'collegename',
                'programss',
                'programTypess',
                'summary_data',
                'FixedSectionName',
                'thisacademicyear',
                'sselectedAcademicYear',
                'curriculum_unattached_student_count',
                'selectedProgramName',
                'selectedProgramTypeName',
                'selectedYearLevelName',
                'selectedCurriculumName'
            )
        );
    }

    public function restore_student_section($section_id = null, $student_id = null, $archieve_status = 1)
    {

        if (!empty($student_id)) {
            $student_number = $this->Section->Student->field('Student.studentnumber', array('Student.id' => $student_id)
            );

            /* debug($student_number);
			debug($section_id);
			debug($archieve_status); */


            if ((!$section_id || empty($section_id)) && !empty($student_number)) {
                $this->Flash->error('Invalid id for section or/and student.');
                $this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $student_id));
            }

            $section_name = $this->Section->field('Section.name', array('Section.id' => $section_id));
            //now delete this student from this section from associate table .  check student deletion from section is possible ?

            if (!$archieve_status) {
                $activeStudentSections = ClassRegistry::init('StudentsSection')->find('list', array(
                    'conditions' => array(
                        'StudentsSection.student_id' => $student_id,
                        'StudentsSection.archive' => 0
                    ),
                    'fields' => array('StudentsSection.id', 'StudentsSection.id')
                ));


                if (!empty($activeStudentSections)) {
                    ClassRegistry::init('StudentsSection')->updateAll(array('StudentsSection.archive' => 1),
                        array('StudentsSection.id' => $activeStudentSections));
                }
            }

            if (!empty($section_name)) {
                // section exists

                $updateStudentSectionIfExists = ClassRegistry::init('StudentsSection')->field(
                    'StudentsSection.id',
                    array(
                        'StudentsSection.student_id' => $student_id,
                        'StudentsSection.section_id' => $section_id,
                    )
                );

                if ($updateStudentSectionIfExists) {
                    $restoreSection['StudentsSection']['id'] = $updateStudentSectionIfExists;
                }

                $restoreSection['StudentsSection']['section_id'] = $section_id;
                $restoreSection['StudentsSection']['student_id'] = $student_id;
                $restoreSection['StudentsSection']['archive'] = $archieve_status;

                // TO DO:  check and maintain, distorted section ordering by yearlevel and registration data available, if any, Neway

                if (ClassRegistry::init('StudentsSection')->save($restoreSection, false)) {
                    $this->Flash->success($student_number . ' restored to ' . $section_name . ' section.');
                } else {
                    $this->Flash->error($student_number . ' can not be restored to ' . $section_name . ' section.');
                }

                $this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $student_id));
            }
        }
    }
}

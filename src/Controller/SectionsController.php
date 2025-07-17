<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use DateTime;

/**
 * SectionsController
 */
class SectionsController extends AppController
{
    protected $menuOptions = [
        'parent' => 'placement',
        'exclude' => [
            'export',
            'view_pdf',
            'deleteStudentforThisSection',
            'archieveUnarchieveStudentSection',
            'move',
            'sectionMoveUpdate',
            'massStudentSectionAdd',
            'addStudentSection',
            'addStudentSectionUpdate',
            'getSectionsByProgram',
            'getSectionsByDept',
            'getSectionsByAcademicYear',
            'getSectionsOfCollege',
            'getModalBox',
            'getSectionStudents',
            'unAssignedSummeries',
            'getSectionsByProgramAndDept',
            'getYearLevel',
            'deleteStudent',
            'moveSelectedStudentSection',
            'moveStudentSectionToNew',
            'addStudentToSection',
            'addStudentPrevSection',
            'getSectionsByDeptDataEntry',
            'getSectionsByYearLevel',
            'getSupStudents',
            'getSectionsByProgramSuppExam',
            'getSectionsByProgramAndDeptSuppExam',
            'upgradeSelectedStudentSection',
            'getSectionsByDeptAddDrop',
            'restoreStudentSection',
            'search',
        ],
        'alias' => [
            'index' => 'List Sections',
            'add' => 'Add New Section',
            'assign' => 'Assign Students to Section',
            'upgradeSections' => 'Upgrade Year Level',
            'downgradeSections' => 'Downgrade Year Level',
            'dispalySectionLessStudents' => 'Display Sectionless Students',
        ],
    ];

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth');
        $this->loadComponent('AcademicYear');
        $this->loadComponent('EthiopicDateTime');

        $this->Paginator->settings = [
            'limit' => 100,
            'maxLimit' => 500,
            'order' => [
                'Sections.academicyear' => 'DESC',
                'Sections.department_id' => 'ASC',
                'Sections.program_id' => 'ASC',
                'Sections.program_type_id' => 'ASC',
                'Sections.year_level_id' => 'ASC',
                'Sections.name' => 'ASC',
                'Sections.id' => 'ASC',
            ],
        ];
    }

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Auth->allow([
            'getYearLevel',
            'getModalBox',
            'getSectionsByDept',
            'getSectionStudents',
            'getSectionsOfCollege',
            'getSectionsByAcademicYear',
            'getSectionsByDeptDataEntry',
            'getSectionsByProgram',
            'getSectionsByProgramSuppExam',
            'getSectionsByProgramAndDept',
            'getSectionsByProgramAndDeptSuppExam',
            'getSectionsByYearLevel',
            'getSupStudents',
            'archieveUnarchieveStudentSection',
            'export',
            'viewPdf',
            'getSectionsByDeptAddDrop',
            'restoreStudentSection',
        ]);
    }

    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);

        $currentAcademicYear = $this->AcademicYear->currentAcademicYear();
        $acyearArrayData = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, explode('/', $currentAcademicYear)[0]);

        $programs = $this->Sections->Programs->find('list')
            ->where(['Programs.id IN' => $this->program_ids, 'Programs.active' => 1])
            ->toArray();

        $programTypes = $this->Sections->ProgramTypes->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids, 'ProgramTypes.active' => 1])
            ->toArray();

        $yearLevels = $this->year_levels;
        if ($this->role_id == ROLE_DEPARTMENT) {
            $yearLevels = $this->Sections->YearLevels->find('list')
                ->where(['YearLevels.department_id' => $this->department_id, 'YearLevels.name IN' => $this->year_levels])
                ->toArray();
        }

        $this->set(compact(
            'acyearArrayData',
            'currentAcademicYear',
            'programTypes',
            'programs',
            'yearLevels'
        ));
    }

    protected function initSearchSections(): void
    {
        $session = $this->request->getSession();
        if (!empty($this->request->getData('Section'))) {
            $session->write('search_sections', $this->request->getData('Section'));
        } elseif ($session->check('search_sections')) {
            $this->request = $this->request->withData('Section', $session->read('search_sections'));
        }
    }

    protected function clearSessionFilters(?array $data = null): void
    {
        $session = $this->request->getSession();
        if ($session->check('search_sections')) {
            $session->delete('search_sections');
        }
    }

    public function search()
    {
        $url = ['action' => 'index'];
        if (!empty($this->request->getData())) {
            foreach ($this->request->getData() as $k => $v) {
                if (!empty($v)) {
                    foreach ($v as $kk => $vv) {
                        if (!empty($vv) && is_array($vv)) {
                            foreach ($vv as $kkk => $vvv) {
                                $url["$k.$kk.$kkk"] = str_replace('/', '-', trim($vvv));
                            }
                        } else {
                            $url["$k.$kk"] = str_replace('/', '-', trim($vv));
                        }
                    }
                }
            }
        }

        return $this->redirect($url);
    }

    public function index(?array $data = null)
    {
        $limit = 100;
        $name = '';
        $turnOffSearch = true;
        $options = [];
        $page = '';

        if (!empty($this->request->getQueryParams())) {
            if (!empty($this->request->getQuery('page'))) {
                $page = $this->request->getData('Section.page', $this->request->getQuery('page'));
            }
            if (!empty($this->request->getQuery('sort'))) {
                $this->request->getData('Section.sort', $this->request->getQuery('sort'));
            }
            if (!empty($this->request->getQuery('direction'))) {
                $this->request->getData('Section.direction', $this->request->getQuery('direction'));
            }
            $this->initSearchSections();
        }

        if (!empty($data['Section'])) {
            $this->request = $this->request->withData('Section', $data['Section']);
            $this->initSearchSections();
        }

        if ($this->request->getData('search')) {
            $this->clearSessionFilters();
            $this->initSearchSections();
        }

        $limit = $this->request->getData('Section.limit', $limit);
        $this->request = $this->request->withData('Section.limit', $limit);

        $name = $this->request->getData('Section.section_name', $name);
        $this->request = $this->request->withData('Section.section_name', $name);

        if (empty($this->request->getData('Section.active'))) {
            $this->request = $this->request->withData('Section.active', '');
        }

        $selectedAcademicYear = $this->request->getData('Section.academicyearSection') ??
            $this->request->getData('Section.academicyear') ??
            $this->AcademicYear->currentAcademicYear();
        $this->request = $this->request->withData('Section.academicyearSection', $selectedAcademicYear)
            ->withData('Section.academicyear', $selectedAcademicYear);

        if (!empty($this->request->getData())) {
            $session = $this->request->getSession();
            $user = $session->read('Auth.User');

            if (!empty($page) && !$this->request->getData('search')) {
                $this->request = $this->request->withData('Search.page', $page);
            }

            if ($user['role_id'] == ROLE_DEPARTMENT) {
                $departments = $this->Sections->Departments->find('list')
                    ->where(['Departments.id' => $this->department_id, 'Departments.active' => 1])
                    ->toArray();
                $options['conditions'][] = ['Sections.department_id' => $this->department_id];
            } elseif ($user['role_id'] == ROLE_COLLEGE) {
                $departments = [];
                if ($this->onlyPre == 0) {
                    $departments = $this->Sections->Departments->find('list')
                        ->where(['Departments.college_id IN' => $this->college_ids, 'Departments.active' => 1])
                        ->toArray();
                } else {
                    $colleges = $this->Sections->Colleges->find('list')
                        ->where(['Colleges.id IN' => $this->college_ids, 'Colleges.active' => 1])
                        ->toArray();
                    $this->request = $this->request->withData('Section.college_id', $this->college_id)
                        ->withData('Section.year_level_id', '0');
                }
                $options['conditions'][] = ['Sections.college_id' => $this->college_id];

                if ($this->request->getData('Section.department_id') > 0) {
                    $options['conditions'][] = ['Sections.department_id' => $this->request->getData('Section.department_id')];
                }
            } elseif ($user['role_id'] == ROLE_REGISTRAR) {
                if (!empty($this->department_ids)) {
                    $colleges = [];
                    $departments = $this->Sections->Departments->find('list')
                        ->where(['Departments.id IN' => $this->department_ids, 'Departments.active' => 1])
                        ->toArray();

                    if ($this->request->getData('Section.department_id')) {
                        $options['conditions'][] = ['Sections.department_id' => $this->request->getData('Section.department_id')];
                    } else {
                        $options['conditions'][] = ['Sections.department_id IN' => $this->department_ids];
                    }
                } elseif (!empty($this->college_ids)) {
                    $departments = [];
                    $colleges = $this->Sections->Colleges->find('list')
                        ->where(['Colleges.id IN' => $this->college_ids, 'Colleges.active' => 1])
                        ->toArray();

                    if ($this->request->getData('Section.college_id')) {
                        $options['conditions'][] = [
                            'Sections.college_id' => $this->request->getData('Section.college_id'),
                            'Sections.department_id IS' => null,
                        ];
                    } else {
                        $options['conditions'][] = [
                            'Sections.college_id IN' => $this->college_ids,
                            'Sections.department_id IS' => null,
                        ];
                    }
                    $this->request = $this->request->withData('Section.year_level_id', '0');
                }
            } else {
                $departments = $this->Sections->Departments->find('list')
                    ->where(['Departments.active' => 1])
                    ->toArray();
                $colleges = $this->Sections->Colleges->find('list')
                    ->where(['Colleges.active' => 1])
                    ->toArray();

                if ($this->request->getData('Section.department_id')) {
                    $options['conditions'][] = ['Sections.department_id' => $this->request->getData('Section.department_id')];
                } elseif ($this->request->getData('Section.college_id')) {
                    $departments = $this->Sections->Departments->find('list')
                        ->where(['Departments.college_id' => $this->request->getData('Section.college_id'), 'Departments.active' => 1])
                        ->toArray();
                    $options['conditions'][] = ['Sections.college_id' => $this->request->getData('Section.college_id')];
                } else {
                    if (!empty($colleges) && !empty($departments)) {
                        $options['conditions'][] = [
                            'OR' => [
                                'Sections.college_id IN' => array_keys($colleges),
                                'Sections.department_id IN' => array_keys($departments),
                            ],
                        ];
                    } elseif (!empty($departments)) {
                        $options['conditions'][] = ['Sections.department_id IN' => array_keys($departments)];
                    } elseif (!empty($colleges)) {
                        $options['conditions'][] = ['Sections.college_id IN' => array_keys($colleges)];
                    }
                }
            }

            if (!empty($selectedAcademicYear)) {
                $options['conditions'][] = ['Sections.academicyear' => $selectedAcademicYear];
            }

            if ($this->request->getData('Section.program_id')) {
                $options['conditions'][] = ['Sections.program_id' => $this->request->getData('Section.program_id')];
            } else {
                $options['conditions'][] = ['Sections.program_id IN' => $this->program_ids];
            }

            if ($this->request->getData('Section.program_type_id')) {
                $options['conditions'][] = ['Sections.program_type_id' => $this->request->getData('Section.program_type_id')];
            } else {
                $options['conditions'][] = ['Sections.program_type_id IN' => $this->program_type_ids];
            }

            if ($this->request->getData('Section.college_id') > 0 && $user['role_id'] != ROLE_REGISTRAR) {
                $departments = $this->Sections->Departments->find('list')
                    ->where(['Departments.college_id' => $this->request->getData('Section.college_id'), 'Departments.active' => 1])
                    ->toArray();
            }

            if (!($user['role_id'] == ROLE_COLLEGE && $user['is_admin'] == 0)) {
                if ($this->request->getData('Section.year_level_id') == '0') {
                    $options['conditions'][] = ['Sections.department_id IS' => null];
                } elseif (is_numeric($this->request->getData('Section.year_level_id')) && $this->request->getData('Section.year_level_id') > 0) {
                    $options['conditions'][] = ['Sections.year_level_id' => $this->request->getData('Section.year_level_id')];
                } else {
                    if (($user['role_id'] == ROLE_COLLEGE && $user['is_admin'] == 0) || ($user['role_id'] == ROLE_REGISTRAR && !empty($this->college_ids) && $this->onlyPre == 1)) {
                        $options['conditions'][] = ['Sections.department_id IS' => null];
                    } elseif ($this->request->getData('Section.department_id')) {
                        $yearLevelIds = $this->Sections->YearLevels->find('list', ['keyField' => 'id', 'valueField' => 'id'])
                            ->where([
                                'YearLevels.department_id' => $this->request->getData('Section.department_id'),
                                'YearLevels.name' => $this->request->getData('Section.year_level_id') ?: $this->year_levels,
                            ])
                            ->toArray();
                        $options['conditions'][] = ['Sections.year_level_id IN' => $yearLevelIds];
                    } elseif (!empty($departments)) {
                        if ($user['role_id'] == ROLE_COLLEGE && empty($this->request->getData('Section.department_id')) && empty($this->request->getData('Section.year_level_id'))) {
                            $yearLevelIds = $this->Sections->YearLevels->find('list', ['keyField' => 'id', 'valueField' => 'id'])
                                ->where([
                                    'YearLevels.department_id IN' => array_keys($departments),
                                    'YearLevels.name' => $this->request->getData('Section.year_level_id') ?: $this->year_levels,
                                ])
                                ->toArray();
                            $options['conditions'][] = [
                                'OR' => [
                                    'Sections.year_level_id IS' => null,
                                    'Sections.year_level_id' => 0,
                                    'Sections.year_level_id' => '',
                                    'Sections.year_level_id IN' => $yearLevelIds,
                                ],
                            ];
                        } else {
                            $yearLevelIds = $this->Sections->YearLevels->find('list', ['keyField' => 'id', 'valueField' => 'id'])
                                ->where([
                                    'YearLevels.department_id IN' => array_keys($departments),
                                    'YearLevels.name' => $this->request->getData('Section.year_level_id') ?: $this->year_levels,
                                ])
                                ->toArray();
                            $options['conditions'][] = ['Sections.year_level_id IN' => $yearLevelIds];
                        }
                    } elseif (!empty($this->department_ids)) {
                        if ($user['role_id'] == ROLE_COLLEGE && empty($this->request->getData('Section.department_id')) && empty($this->request->getData('Section.year_level_id'))) {
                            $yearLevelIds = $this->Sections->YearLevels->find('list', ['keyField' => 'id', 'valueField' => 'id'])
                                ->where([
                                    'YearLevels.department_id IN' => $this->department_ids,
                                    'YearLevels.name' => $this->request->getData('Section.year_level_id') ?: $this->year_levels,
                                ])
                                ->toArray();
                            $options['conditions'][] = [
                                'OR' => [
                                    'Sections.year_level_id IS' => null,
                                    'Sections.year_level_id' => 0,
                                    'Sections.year_level_id' => '',
                                    'Sections.year_level_id IN' => $yearLevelIds,
                                ],
                            ];
                        } else {
                            $yearLevelIds = $this->Sections->YearLevels->find('list', ['keyField' => 'id', 'valueField' => 'id'])
                                ->where([
                                    'YearLevels.department_id IN' => $this->department_ids,
                                    'YearLevels.name' => $this->request->getData('Section.year_level_id') ?: $this->year_levels,
                                ])
                                ->toArray();
                            $options['conditions'][] = ['Sections.year_level_id IN' => $yearLevelIds];
                        }
                    } else {
                        if ($user['role_id'] == ROLE_COLLEGE || $this->onlyPre || !empty($this->college_ids)) {
                            $options['conditions'][] = ['Sections.department_id IS' => null];
                        } else {
                            $options['conditions'][] = ['Sections.department_id' => ''];
                        }
                    }
                }
            } else {
                $options['conditions'][] = ['Sections.department_id IS' => null];
            }

            if (!empty($name)) {
                $options['conditions'][] = ['Sections.name LIKE' => "%$name%"];
            }

            if (is_numeric($this->request->getData('Section.active'))) {
                $options['conditions'][] = ['Sections.archive' => $this->request->getData('Section.active')];
            }
        } else {
            $session = $this->request->getSession();
            $user = $session->read('Auth.User');

            if ($user['role_id'] == ROLE_COLLEGE) {
                $departments = [];
                if ($this->onlyPre != 1) {
                    $departments = $this->Sections->Departments->find('list')
                        ->where(['Departments.college_id' => $this->college_id, 'Departments.active' => 1])
                        ->toArray();
                } else {
                    $colleges = $this->Sections->Colleges->find('list')
                        ->where(['Colleges.id' => $this->college_id, 'Colleges.active' => 1])
                        ->toArray();
                    $this->request = $this->request->withData('Section.college_id', $this->college_id)
                        ->withData('Section.year_level_id', '0');
                }
                $options['conditions'][] = ['Sections.college_id' => $this->college_id];
            } elseif ($user['role_id'] == ROLE_DEPARTMENT) {
                $departments = $this->Sections->Departments->find('list')
                    ->where(['Departments.id' => $this->department_id])
                    ->toArray();
                $options['conditions'][] = ['Sections.department_id' => $this->department_id];
            } elseif ($user['role_id'] == ROLE_REGISTRAR) {
                if (!empty($this->department_ids)) {
                    $colleges = [];
                    $departments = $this->Sections->Departments->find('list')
                        ->where(['Departments.id IN' => $this->department_ids, 'Departments.active' => 1])
                        ->toArray();
                    if (!empty($departments)) {
                        $options['conditions'][] = [
                            'Sections.department_id IN' => $this->department_ids,
                            'Sections.program_id IN' => $this->program_ids,
                            'Sections.program_type_id IN' => $this->program_type_ids,
                        ];
                    }
                } elseif (!empty($this->college_ids)) {
                    $departments = [];
                    $colleges = $this->Sections->Colleges->find('list')
                        ->where(['Colleges.id IN' => $this->college_ids, 'Colleges.active' => 1])
                        ->toArray();
                    if (!empty($colleges)) {
                        $options['conditions'][] = [
                            'Sections.college_id IN' => $this->college_ids,
                            'Sections.program_id IN' => $this->program_ids,
                            'Sections.program_type_id IN' => $this->program_type_ids,
                        ];
                    }
                    $this->request = $this->request->withData('Section.year_level_id', '0');
                }
            } else {
                $departments = $this->Sections->Departments->find('list')
                    ->where(['Departments.active' => 1])
                    ->toArray();
                $colleges = $this->Sections->Colleges->find('list')
                    ->where(['Colleges.active' => 1])
                    ->toArray();

                if (!empty($colleges) && !empty($departments)) {
                    $options['conditions'][] = [
                        'OR' => [
                            'Sections.department_id IN' => array_keys($departments),
                            'Sections.college_id IN' => array_keys($colleges),
                        ],
                    ];
                } elseif (!empty($departments)) {
                    $options['conditions'][] = ['Sections.department_id IN' => array_keys($departments)];
                } elseif (!empty($colleges)) {
                    $options['conditions'][] = ['Sections.college_id IN' => array_keys($colleges)];
                }
            }

            if (!empty($options)) {
                $options['conditions'][] = ['Sections.archive' => 0];
                $this->request = $this->request->withData('Section.active', 0);

                if ($user['role_id'] == ROLE_DEPARTMENT) {
                    $options['conditions'][] = ['Sections.year_level_id IN' => $this->year_levels];
                } else {
                    if (!empty($this->department_ids) && $user['role_id'] == ROLE_COLLEGE && $user['is_admin'] == 1) {
                        $yearLevelIds = $this->Sections->YearLevels->find('list', ['keyField' => 'id', 'valueField' => 'id'])
                            ->where([
                                'YearLevels.department_id IN' => $this->department_ids,
                                'YearLevels.name IN' => $this->year_levels,
                            ])
                            ->toArray();
                        $options['conditions'][] = [
                            'OR' => [
                                'Sections.department_id IS' => null,
                                'Sections.year_level_id IN' => $yearLevelIds,
                            ],
                        ];
                    } elseif (($user['role_id'] == ROLE_COLLEGE && $user['is_admin'] == 0) || ($user['role_id'] == ROLE_REGISTRAR && !empty($this->college_ids) && $this->onlyPre == 1)) {
                        $options['conditions'][] = ['Sections.department_id IS' => null];
                    } elseif (!empty($this->department_ids)) {
                        $yearLevelIds = $this->Sections->YearLevels->find('list', ['keyField' => 'id', 'valueField' => 'id'])
                            ->where([
                                'YearLevels.department_id IN' => $this->department_ids,
                                'YearLevels.name' => $this->request->getData('Section.year_level_id') ?: $this->year_levels,
                            ])
                            ->toArray();
                        $options['conditions'][] = ['Sections.year_level_id IN' => $yearLevelIds];
                    } else {
                        $options['conditions'][] = ['Sections.department_id IS' => null];
                    }
                }
            }
        }

        if (!empty($options['conditions'])) {
            $this->Paginator->settings = array_merge($this->Paginator->settings, [
                'conditions' => $options['conditions'],
                'contain' => [
                    'Departments' => [
                        'fields' => ['id', 'name', 'shortname', 'college_id', 'institution_code'],
                    ],
                    'Colleges' => [
                        'fields' => ['id', 'name', 'shortname', 'institution_code', 'campus_id'],
                        'Campuses' => ['fields' => ['id', 'name', 'campus_code']],
                    ],
                    'Programs' => [
                        'fields' => ['id', 'name', 'shortname'],
                    ],
                    'ProgramTypes' => [
                        'fields' => ['id', 'name', 'shortname'],
                    ],
                    'Curriculums' => [
                        'fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active'],
                    ],
                    'YearLevels' => ['fields' => ['id', 'name']],
                ],
                'page' => $page,
            ]);

            try {
                $sections = $this->Paginator->paginate($this->Sections);
                $this->set(compact('sections'));
            } catch (NotFoundException $e) {
                $this->request = $this->request->withData('Section', array_diff_key(
                    $this->request->getData('Section') ?? [],
                    array_flip(['page', 'sort', 'direction'])
                ));
                $this->initSearchSections();
                return $this->redirect(['action' => 'index']);
            } catch (\Exception $e) {
                $this->request = $this->request->withData('Section', array_diff_key(
                    $this->request->getData('Section') ?? [],
                    array_flip(['page', 'sort', 'direction'])
                ));
                $this->initSearchSections();
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $sections = [];
        }

        if (empty($sections) && !empty($options['conditions'])) {
            $this->Flash->info('No Section is found in the given search criteria. Try changing the search criterias.');
            $turnOffSearch = false;
        } else {
            $turnOffSearch = false;
        }

        $currentAcy = $this->AcademicYear->currentAcademicYear();
        $acyearArrayOptions = $this->AcademicYear->academicYearInArray(
            (explode('/', $currentAcy)[0]) - ACY_BACK_FOR_ALL,
            explode('/', $currentAcy)[0]
        );

        $applicableDepartmentIds = $this->department_ids;
        $applicableCollegeIds = $this->college_ids;
        $applicableProgramIds = $this->program_ids;
        $applicableProgramTypeIds = $this->program_type_ids;
        $onlyFreshman = $this->onlyPre ? 1 : 0;

        $this->set(compact(
            'acyearArrayOptions',
            'colleges',
            'departments',
            'turnOffSearch',
            'limit',
            'name',
            'applicableDepartmentIds',
            'applicableCollegeIds',
            'applicableProgramIds',
            'applicableProgramTypeIds',
            'onlyFreshman',
            'page',
            'selectedAcademicYear'
        ));
    }

    public function view(?int $id = null)
    {
        if (!$id) {
            $this->Flash->error('Invalid section');
            return $this->redirect(['action' => 'index']);
        }

        $section = $this->Sections->find('first')
            ->where([
                'Sections.id' => $id,
                'OR' => [
                    'Sections.department_id' => $this->department_id,
                    'Sections.college_id' => $this->college_id,
                ],
            ])
            ->contain([
                'Departments' => ['fields' => ['id', 'name']],
                'YearLevels' => ['fields' => ['id', 'name']],
                'Programs' => ['fields' => ['id', 'name', 'shortname']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Colleges' => ['fields' => ['id', 'name', 'shortname']],
                'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                'Students' => [
                    'fields' => [
                        'id',
                        'full_name',
                        'studentnumber',
                        'email',
                        'phone_mobile',
                        'gender',
                        'academicyear',
                    ],
                    'Departments' => ['fields' => ['id', 'name']],
                    'Colleges' => ['fields' => ['id', 'name', 'shortname']],
                    'Programs' => ['fields' => ['id', 'name', 'shortname']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'order' => [
                        'Students.academicyear' => 'DESC',
                        'Students.studentnumber' => 'ASC',
                        'Students.id' => 'ASC',
                        'Students.full_name' => 'ASC',
                    ],
                ],
            ])
            ->first();

        $this->set(compact('section'));
    }

    public function add()
    {
        $isEverything = true;

        if ($this->request->is('post')) {
            $sectionData = $this->request->getData('Section');
            $sectionData['college_id'] = $this->college_id;

            if (!empty($sectionData['number_of_class'])) {
                if (is_numeric($sectionData['number_of_class'])) {
                    $numberOfClass = $sectionData['number_of_class'];
                } else {
                    $this->Flash->error('The Number of Section must be a number.');
                    $isEverything = false;
                }
            } else {
                $this->Flash->error('The number of classes should not be empty.');
                $isEverything = false;
            }

            $programTypeShortName = $this->Sections->ProgramTypes->find()
                ->select(['shortname'])
                ->where(['ProgramTypes.id' => $sectionData['program_type_id']])
                ->first()
                ->shortname;

            if (!empty($sectionData['fixed_section_name'])) {
                if (strpos(trim($sectionData['fixed_section_name']), ' ') === false) {
                    $fixedSectionName = trim($sectionData['fixed_section_name']);
                } else {
                    $this->Flash->error('Fixed section name should not contain spaces.');
                    $isEverything = false;
                }
            } else {
                $this->Flash->error('Fixed section name should not be empty.');
                $isEverything = false;
            }

            if (!empty($sectionData['variable_section_name'])) {
                $variableSectionName = trim($sectionData['variable_section_name']);
            } else {
                $this->Flash->error('Please select a variable section name.');
                $isEverything = false;
            }

            if ($this->role_id != ROLE_COLLEGE) {
                $sectionData['department_id'] = $this->department_id;

                $yearLevel = $sectionData['year_level_id'];
                $yearLevelEntity = $this->Sections->YearLevels->find()
                    ->select(['name'])
                    ->where(['YearLevels.id' => $yearLevel])
                    ->first();
                $yearLevelName = $yearLevelEntity ? $yearLevelEntity->name : '';
                $yearLevelNameShort = substr($yearLevelName, 0, 1);

                if (!empty($sectionData['prefix_section_name'])) {
                    if (strpos(trim($sectionData['prefix_section_name']), ' ') === false) {
                        $prefixSectionName = trim($sectionData['prefix_section_name']) . $programTypeShortName;
                    } else {
                        $this->Flash->error('Prefix section name should not contain spaces.');
                        $isEverything = false;
                    }
                } else {
                    $this->Flash->error('Prefix section name should not be empty.');
                    $isEverything = false;
                }

                $additionalPrefixSectionName = !empty($sectionData['additionalprefix_section_name'])
                    ? trim($sectionData['additionalprefix_section_name'])
                    : '';
            }

            if ($isEverything) {
                $frontSectionName = ($this->role_id != ROLE_COLLEGE)
                    ? $prefixSectionName . $yearLevelNameShort . ' ' . $fixedSectionName . ' ' . $additionalPrefixSectionName
                    : $fixedSectionName;

                $conditions = [
                    'Sections.academicyear LIKE' => $sectionData['academicyear'] . '%',
                    'Sections.college_id' => $this->college_id,
                    'Sections.program_id' => $sectionData['program_id'],
                    'Sections.program_type_id' => $sectionData['program_type_id'],
                    'Sections.name LIKE' => $frontSectionName . ' %',
                ];

                if ($this->role_id != ROLE_COLLEGE) {
                    $conditions['Sections.department_id'] = $this->department_id;
                    $conditions['Sections.year_level_id'] = $sectionData['year_level_id'];
                } else {
                    $conditions['OR'] = [
                        'Sections.department_id IS' => null,
                        'Sections.department_id IN' => ['0', ''],
                    ];
                }

                $similarSections = $this->Sections->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'name',
                ])
                    ->where($conditions)
                    ->toArray();

                $similarSectionVariableNames = array_map(function ($name) use ($frontSectionName) {
                    return substr($name, strrpos($name, ' ') + 1);
                }, $similarSections);

                $numericVariableSectionNames = array_filter($similarSectionVariableNames, 'is_numeric');
                $alphabetVariableSectionNames = array_diff($similarSectionVariableNames, $numericVariableSectionNames);

                if ($variableSectionName == 'Alphabet') {
                    if (empty($alphabetVariableSectionNames)) {
                        $variableSectionNameValue = 'A';
                    } else {
                        rsort($alphabetVariableSectionNames);
                        $variableSectionNameValue = chr(ord($alphabetVariableSectionNames[0]) + 1);
                    }
                } else {
                    if (empty($numericVariableSectionNames)) {
                        $variableSectionNameValue = 1;
                    } else {
                        rsort($numericVariableSectionNames);
                        $variableSectionNameValue = (int)$numericVariableSectionNames[0] + 1;
                    }
                }

                unset($sectionData['number_of_class'], $sectionData['fixed_section_name'], $sectionData['variable_section_name']);
                unset($sectionData['prefix_section_name'], $sectionData['additionalprefix_section_name']);

                $isSave = false;
                $errMsg = '';

                for ($i = 0; $i < $numberOfClass; $i++) {
                    $name = ($this->role_id != ROLE_COLLEGE)
                        ? $prefixSectionName . $yearLevelNameShort . ' ' . $fixedSectionName . ' ' . $additionalPrefixSectionName . ' ' . $variableSectionNameValue
                        : $fixedSectionName . ' ' . $variableSectionNameValue;

                    $sectionData['name'] = $name;
                    $section = $this->Sections->newEntity($sectionData);

                    if ($this->Sections->save($section)) {
                        $isSave = true;
                    } else {
                        $errMsg = "$name is already taken. Please use another section name.";
                    }

                    $variableSectionNameValue = ($variableSectionName == 'Alphabet')
                        ? chr(ord($variableSectionNameValue) + 1)
                        : $variableSectionNameValue + 1;
                }

                if ($isSave) {
                    $this->Flash->success('The section(s) have been saved');

                    $redirectSearchDataFilters = [
                        'Section' => [
                            'academicyear' => $sectionData['academicyear'],
                            'program_id' => $sectionData['program_id'],
                            'program_type_id' => $sectionData['program_type_id'],
                        ],
                    ];

                    if ($this->role_id == ROLE_DEPARTMENT) {
                        $redirectSearchDataFilters['Section']['year_level_id'] = $sectionData['year_level_id'];
                        $redirectSearchDataFilters['Section']['department_id'] = $this->department_id;
                    } else {
                        $redirectSearchDataFilters['Section']['college_id'] = $this->college_id;
                        if (!empty($sectionData['department_id'])) {
                            $redirectSearchDataFilters['Section']['department_id'] = $sectionData['department_id'];
                        }
                    }

                    $this->clearSessionFilters();
                    $this->request->getSession()->write('search_sections', $redirectSearchDataFilters['Section']);

                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error($errMsg ?: 'The section could not be saved. Please try again.');
                }
            }
        }

        $curriculums = [];
        if ($this->role_id != ROLE_COLLEGE) {
            $curriculums = TableRegistry::getTableLocator()->get('Curriculums')
                ->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'curriculum_detail',
                ])
                ->where([
                    'Curriculums.department_id IN' => $this->department_ids,
                    'Curriculums.program_id' => reset($this->program_ids),
                    'Curriculums.registrar_approved' => 1,
                    'Curriculums.active' => 1,
                ])
                ->order(['Curriculums.program_id' => 'ASC', 'Curriculums.created' => 'DESC'])
                ->toArray();
        }

        $thisAcademicYear = $this->request->getData('Section.academicyear') ?: $this->AcademicYear->currentAcademicYear();
        $selectedProgram = $this->request->getData('Section.program_id') ?: reset($this->program_ids);
        $selectedProgramType = $this->request->getData('Section.program_type_id') ?: reset($this->program_type_ids);

        if ($this->role_id == ROLE_DEPARTMENT) {
            $yearLevelData = $this->Sections->YearLevels->find('list')
                ->where([
                    'YearLevels.department_id IN' => $this->department_ids,
                    'YearLevels.id' => $this->request->getData('Section.year_level_id') ?: array_key_first(
                        $this->Sections->YearLevels->find('list')
                            ->where(['YearLevels.department_id IN' => $this->department_ids, 'YearLevels.name IN' => $this->year_levels])
                            ->toArray()
                    ),
                ])
                ->toArray();
            $selectedYearLevelId = array_key_first($yearLevelData);
            $selectedYearLevelName = reset($yearLevelData);

            $selectedCurriculumId = !empty($curriculums)
                ? ($this->request->getData('Section.curriculum_id') ?: array_key_first($curriculums))
                : '%';
        } else {
            $selectedYearLevelId = null;
            $selectedCurriculumId = null;
            $selectedYearLevelName = null;
        }

        $summaryData = $this->Sections->getSectionLessStudentSummary(
            $thisAcademicYear,
            $this->college_id,
            $this->department_id,
            $this->role_id
        );
        $curriculumUnattachedStudentCount = $this->Sections->getCurriculumUnattachedStudentSummary(
            $thisAcademicYear,
            $this->college_id,
            $this->department_id,
            $this->role_id
        );

        $variableSectionNameArray = [
            'Alphabet' => 'A, B, C ...',
            'Number' => '1, 2, 3 ...',
        ];

        $collegeName = $this->Sections->Colleges->find()
            ->select(['name'])
            ->where(['Colleges.id' => $this->college_id])
            ->first()
            ->name;
        $collegeShortName = $this->Sections->Colleges->find()
            ->select(['shortname'])
            ->where(['Colleges.id' => $this->college_id])
            ->first()
            ->shortname;

        $departmentName = $this->Sections->Departments->find()
            ->select(['name'])
            ->where(['Departments.id' => $this->department_id])
            ->first()
            ->name;
        $departmentShortName = $this->Sections->Departments->find()
            ->select(['shortname'])
            ->where(['Departments.id' => $this->department_id])
            ->first()
            ->shortname;

        $yearLevels = $this->Sections->YearLevels->find('list')
            ->where(['YearLevels.department_id' => $this->department_id])
            ->toArray();

        $programTypeShortName = $this->Sections->ProgramTypes->find()
            ->select(['shortname'])
            ->where(['ProgramTypes.id' => $this->request->getData('Section.program_type_id') ?: reset($this->program_type_ids)])
            ->first()
            ->shortname;
        $programShortName = $this->Sections->Programs->find()
            ->select(['shortname'])
            ->where(['Programs.id' => $this->request->getData('Section.program_id') ?: reset($this->program_ids)])
            ->first()
            ->shortname;

        $programs = $this->Sections->Programs->find('list')
            ->where(['Programs.id IN' => $this->program_ids, 'Programs.active' => 1])
            ->toArray();
        $programTypes = $this->Sections->ProgramTypes->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids, 'ProgramTypes.active' => 1])
            ->toArray();

        $acyear = $this->AcademicYear->currentAcademicYear();
        $gcYear = substr($acyear, 0, 4);
        $gcMonth = date('n');
        $gcDay = date('j');

        if ($gcMonth < 9) {
            $gcYear++;
        }

        $ety = $this->EthiopicDateTime->getEthiopicYear($gcDay, $gcMonth, $gcYear);

        $fixedSectionName = ($this->role_id == ROLE_COLLEGE)
            ? $collegeShortName . $ety
            : ($departmentShortName ?? $collegeShortName) . $ety;

        $numberOfClass = array_combine(range(1, 26), range(1, 26));

        $prefixSectionName = $this->Sections->Programs->find('list', [
            'keyField' => 'shortname',
            'valueField' => 'shortname',
        ])
            ->where(['Programs.id IN' => $this->program_ids, 'Programs.active' => 1])
            ->order(['Programs.id' => 'ASC'])
            ->toArray();

        if (empty($prefixSectionName)) {
            $prefixSectionName = $this->Sections->Programs->find('list', [
                'keyField' => 'shortname',
                'valueField' => 'shortname',
            ])
                ->where(['Programs.active' => 1])
                ->order(['Programs.id' => 'ASC'])
                ->toArray();
        }

        $this->set(compact(
            'departmentName',
            'yearLevels',
            'prefixSectionName',
            'variableSectionNameArray',
            'collegeName',
            'numberOfClass',
            'programs',
            'fixedSectionName',
            'programTypes',
            'summaryData',
            'thisAcademicYear',
            'curriculumUnattachedStudentCount',
            'curriculums',
            'selectedProgram',
            'selectedProgramType',
            'selectedYearLevelId',
            'selectedYearLevelName',
            'selectedCurriculumId'
        ));
    }

    public function edit(?int $id = null)
    {
        if (!$id && empty($this->request->getData())) {
            $this->Flash->error('Invalid section');
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['post', 'put'])) {
            $sectionData = $this->request->getData();
            $sectionData['Section']['name'] = trim($sectionData['Section']['name']);
            $section = $this->Sections->get($id);
            $section = $this->Sections->patchEntity($section, $sectionData['Section']);

            if ($this->Sections->save($section)) {
                $this->Flash->success('The section has been saved');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The section could not be saved. Please try again.');
            }
        }

        $section = $this->Sections->find('first')
            ->where([
                'Sections.id' => $id,
                'OR' => [
                    'Sections.department_id' => $this->department_id,
                    'Sections.college_id' => $this->college_id,
                ],
            ])
            ->contain([
                'Departments' => ['fields' => ['id', 'name']],
                'YearLevels' => ['fields' => ['id', 'name']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Colleges' => ['fields' => ['id', 'name']],
                'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
            ])
            ->first();

        $this->request = $this->request->withData('Section', $section->toArray());

        $curriculums = [];
        if ($this->role_id != ROLE_COLLEGE) {
            $curriculums = TableRegistry::getTableLocator()->get('Curriculums')
                ->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'curriculum_detail',
                ])
                ->where([
                    'Curriculums.department_id IN' => $this->department_ids,
                    'Curriculums.program_id' => reset($this->program_ids),
                    'Curriculums.registrar_approved' => 1,
                    'Curriculums.active' => 1,
                ])
                ->order(['Curriculums.program_id' => 'ASC', 'Curriculums.created' => 'DESC'])
                ->toArray();
        }

        $colleges = $this->Sections->Colleges->find('list')
            ->where(['Colleges.id' => $this->college_id])
            ->toArray();
        $departments = $this->Sections->Departments->find('list')
            ->where(['Departments.id' => $this->department_id])
            ->toArray();
        $yearLevels = $this->Sections->YearLevels->find('list')
            ->where(['YearLevels.department_id' => $this->department_id])
            ->toArray();
        $programs = $this->Sections->Programs->find('list')->toArray();
        $programTypes = $this->Sections->ProgramTypes->find('list')->toArray();

        $this->set(compact('section', 'colleges', 'departments', 'yearLevels', 'programs', 'programTypes', 'curriculums'));
    }

    public function delete(?int $id = null)
    {
        if (!$id) {
            $this->Flash->error('Invalid id for section');
            return $this->redirect(['action' => 'index']);
        }

        $section = $this->Sections->get($id);

        if ($this->Sections->isSectionEmpty($id)) {
            if (!$this->Sections->isCoursePublishedInTheSection($id)) {
                if ($this->Sections->delete($section)) {
                    $this->Flash->success('Section deleted.');
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $this->Flash->error('Course has been published in the name of the section. Unpublish/delete published course first.');
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $studentsSections = $this->Sections->StudentsSections->find()
                ->where(['StudentsSections.section_id' => $id])
                ->toArray();
            $activeStudentsCount = count(array_filter($studentsSections, fn($ss) => $ss->archive == 0));

            if ($activeStudentsCount == 0) {
                $section->archive = 1;
                if ($this->Sections->save($section)) {
                    $this->Flash->success('Section is now archived (soft deleted)');
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error('Section was not deleted or archived');
            } else {

                 $this->Flash->error(
                     __('Section has {0} active student(s). To delete this section, first move or delete all students.', $activeStudentsCount),
                     [
                         'element' => 'error_with_link',
                         'params' => [
                             'link_text' => __('this page'),
                             'link_url' => ['controller' => 'Sections', 'action' => 'display_sections']
                         ]
                     ]
                 );
            }
            return $this->redirect(['action' => 'index']);
        }
    }

    public function assign()
    {
        $session = $this->request->getSession();
        if ($session->check('sdata')) {
            $this->request = $this->request->withData('continue', true)
                ->withData('', $session->read('sdata'));
        }

        $session->delete('sdata');

        // debug($this->request->getData());

        $academicyear = !empty($this->request->getData('Section.academicyearSearch'))
            ? $this->request->getData('Section.academicyearSearch')
            : $this->AcademicYear->currentAcademicYear();

        $selected_program = !empty($this->request->getData('Section.program_id'))
            ? $this->request->getData('Section.program_id')
            : 1;

        $selected_program_type = !empty($this->request->getData('Section.program_type_id'))
            ? $this->request->getData('Section.program_type_id')
            : 1;

        $summary_data = $this->Sections->getSectionLessStudentSummary($academicyear, $this->college_id, $this->department_id, $this->role_id);

        $curriculum_unattached_student_count = $this->Sections->getCurriculumUnattachedStudentSummary($academicyear, $this->college_id, $this->department_id, $this->role_id);
        $programs = $this->Sections->Programs->find('list')->toArray();
        $programTypes = $this->Sections->ProgramTypes->find('list')->toArray();

        $assignment_type_array = [
            'alphabet' => 'By Alphabet',
            'result' => 'Fairly By Result',
        ];

        if ($this->role_id != ROLE_COLLEGE) {
            $yearLevels = $this->Sections->YearLevels->find('list')
                ->where([
                    'YearLevels.department_id' => $this->department_id,
                    'YearLevels.name' => '1st',
                ])
                ->toArray();
            $section_less_total_students = $this->Sections->countSectionLessStudents($this->college_id, $this->role_id, $this->department_id, $academicyear, $selected_program, $selected_program_type, null);
        } else {
            $section_less_total_students = $this->Sections->countSectionLessStudents($this->college_id, $this->role_id, null, $academicyear, $selected_program, $selected_program_type, null);
        }

        $isbeforesearch = 1;

        $collegename = $this->Sections->Colleges->find()
            ->select(['name'])
            ->where(['Colleges.id' => $this->college_id])
            ->first()
            ->name;
        $departmentname = $this->Sections->Departments->find()
            ->select(['name'])
            ->where(['Departments.id' => $this->department_id])
            ->first()
            ->name;

        $this->set(compact(
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
        ));

        if (!empty($this->request->getData()) && $this->request->getData('search')) {
            if ($session->check('sdata')) {
                $session->delete('sdata');
            }

            $isbeforesearch = 0;
            $academicyear = $this->request->getData('Section.academicyearSearch');
            $assignmenttype = $this->request->getData('Section.assignment_type');

            $selected_program = $this->request->getData('Section.program_id');
            $selected_program_name = $this->Sections->Programs->find()
                ->select(['name'])
                ->where(['Programs.id' => $selected_program])
                ->first()
                ->name;
            $selected_program_type = $this->request->getData('Section.program_type_id');

            if ($this->role_id != ROLE_COLLEGE) {
                $yearlevel = $this->request->getData('Section.year_level_id');
                $yearlevelname = $this->Sections->YearLevels->find()
                    ->select(['name'])
                    ->where(['YearLevels.id' => $yearlevel])
                    ->first()
                    ->name;
            }

            $summary_data = $this->Sections->getSectionLessStudentSummary($academicyear, $this->college_id, $this->department_id, $this->role_id);
            $curriculum_unattached_student_count = $this->Sections->getCurriculumUnattachedStudentSummary($academicyear, $this->college_id, $this->department_id, $this->role_id);
            $section_less_total_students = $this->Sections->countSectionLessStudents($this->college_id, $this->role_id, $this->department_id, $academicyear, $selected_program, $selected_program_type);

            $sectionlessStudentCurriculum = $this->Sections->getSectionLessStudentCurriculum($academicyear, $this->college_id, $this->department_id, $this->role_id, $selected_program, $selected_program_type);

            $curriculum_id = null;
            $curriculum_count = count($sectionlessStudentCurriculum);

            if ($session->check('empty_Curriculum')) {
                $session->delete('empty_Curriculum');
            }

            if ($curriculum_count == 1) {
                if (empty($curriculum_id)) {
                    $empty_Curriculum = 1;
                    $session->write('empty_Curriculum', $empty_Curriculum);
                }

                $curriculum_id = $sectionlessStudentCurriculum[0];
                $this->request = $this->request->withData('Section.Curriculum', $curriculum_id);
                $session->write('selected_curriculum', $curriculum_id);
                $this->request = $this->request->withData('Section.curriculum_search', 1);
                $session->write('curriculum_search', $this->request->getData('Section.curriculum_search'));
                $this->request = $this->request->withData('continue', true);
            } elseif ($curriculum_count > 1) {
                $sectionlessStudentCurriculumArray = [];

                if (!empty($sectionlessStudentCurriculum)) {
                    $curriculumsTable = TableRegistry::getTableLocator()->get('Curriculums');
                    foreach ($sectionlessStudentCurriculum as $sscv) {
                        $sectionlessStudentCurriculumArray[$sscv] = $curriculumsTable->find()
                            ->select(['curriculum_detail'])
                            ->where(['Curriculums.id' => $sscv])
                            ->first()
                            ->curriculum_detail;
                    }
                }

                if ($session->check('curriculum_search')) {
                    $session->delete('curriculum_search');
                }

                $isbeforesearch = 1;

                $this->set(compact(
                    'sectionlessStudentCurriculum',
                    'sectionlessStudentCurriculumArray',
                    'isbeforesearch',
                    'section_less_total_students'
                ));
            }

            $this->set(compact('isbeforesearch', 'section_less_total_students'));
            // debug($this->request->getData());
        }

        if (!empty($this->request->getData()) && $this->request->getData('continue')) {
            // debug($this->request->getData());
            $empty_Curriculum = 0;

            if ($session->check('empty_Curriculum')) {
                $empty_Curriculum = $session->read('empty_Curriculum');
            }

            if (!empty($this->request->getData('Section.Curriculum')) || $empty_Curriculum == 1) {
                $isbeforesearch = 0;
                $academicyear = $this->request->getData('Section.academicyearSearch');
                $selected_program = $this->request->getData('Section.program_id');
                $selected_program_type = $this->request->getData('Section.program_type_id');
                $yearlevel = null;

                if ($this->role_id == ROLE_DEPARTMENT) {
                    $yearlevel = $this->request->getData('Section.year_level_id');
                }

                $assignmenttype = $this->request->getData('Section.assignment_type');
                $selected_curriculum = $this->request->getData('Section.Curriculum');
                $selected_program_name = $this->Sections->Programs->find()
                    ->select(['name'])
                    ->where(['Programs.id' => $selected_program])
                    ->first()
                    ->name;

                $session->write('academicyear', $academicyear);
                $session->write('selected_program', $selected_program);
                $session->write('selected_program_type', $selected_program_type);
                $session->write('yearlevel', $yearlevel);
                $session->write('assignmenttype', $assignmenttype);
                $session->write('selected_curriculum', $selected_curriculum);

                $sections = $this->Sections->getSectionForAssignment($academicyear, $this->college_id, $this->department_id, $this->role_id, $selected_program, $selected_program_type, $yearlevel, $selected_curriculum);
                $current_sections_occupation = $this->Sections->currentSectionsOccupation($sections);

                $sections_curriculum_name = $this->Sections->sectionsCurriculum($sections);
                // debug($sections_curriculum_name);

                $section_less_total_students = $this->Sections->countSectionLessStudents($this->college_id, $this->role_id, $this->department_id, $academicyear, $selected_program, $selected_program_type, $selected_curriculum);
                // debug($section_less_total_students);

                $collegename = $this->Sections->Colleges->find()
                    ->select(['name'])
                    ->where(['Colleges.id' => $this->college_id])
                    ->first()
                    ->name;
                $departmentname = $this->Sections->Departments->find()
                    ->select(['name'])
                    ->where(['Departments.id' => $this->department_id])
                    ->first()
                    ->name;
                $yearLevels = $this->Sections->YearLevels->find('list')
                    ->where(['YearLevels.department_id' => $this->department_id])
                    ->toArray();

                if (!$this->request->getData('Section.curriculum_search')) {
                    $sectionlessStudentCurriculum = $this->Sections->getSectionLessStudentCurriculum($academicyear, $this->college_id, $this->department_id, $this->role_id, $selected_program, $selected_program_type);
                    $sectionlessStudentCurriculumArray = [];

                    if (!empty($sectionlessStudentCurriculum)) {
                        $curriculumsTable = TableRegistry::getTableLocator()->get('Curriculums');
                        foreach ($sectionlessStudentCurriculum as $sscv) {
                            $sectionlessStudentCurriculumArray[$sscv] = $curriculumsTable->find()
                                ->select(['curriculum_detail'])
                                ->where(['Curriculums.id' => $sscv])
                                ->first()
                                ->curriculum_detail;
                        }
                    }

                    $this->set(compact('sectionlessStudentCurriculum', 'sectionlessStudentCurriculumArray'));
                }

                $this->set(compact(
                    'sections',
                    'section_less_total_students',
                    'isbeforesearch',
                    'summary_data',
                    'curriculum_unattached_student_count',
                    'sections_curriculum_name',
                    'collegename',
                    'departmentname',
                    'yearLevels',

                    'academicyear',
                    'selected_program_name',
                    'current_sections_occupation'
                ));
            } else {
                $this->Flash->error('Please select curriculum.');

                $academicyear = $this->request->getData('Section.academicyearSearch');
                $selected_program = $this->request->getData('Section.program_id');
                $selected_program_type = $this->request->getData('Section.program_type_id');

                $section_less_total_students = $this->Sections->countSectionLessStudents($this->college_id, $this->role_id, $this->department_id, $academicyear, $selected_program, $selected_program_type);
                $sectionlessStudentCurriculum = $this->Sections->getSectionLessStudentCurriculum($academicyear, $this->college_id, $this->department_id, $this->role_id, $selected_program, $selected_program_type);
                $sectionlessStudentCurriculumArray = [];

                if (!empty($sectionlessStudentCurriculum)) {
                    $curriculumsTable = TableRegistry::getTableLocator()->get('Curriculums');
                    foreach ($sectionlessStudentCurriculum as $sscv) {
                        $sectionlessStudentCurriculumArray[$sscv] = $curriculumsTable->find()
                            ->select(['curriculum_detail'])
                            ->where(['Curriculums.id' => $sscv])
                            ->first()
                            ->curriculum_detail;
                    }
                }

                $this->set(compact('sectionlessStudentCurriculum', 'sectionlessStudentCurriculumArray', 'section_less_total_students'));
            }
        }

        $isassign = 0;

        if ($this->request->getData('assign')) {
            $academicyear = $session->read('academicyear');
            $assignmenttype = $session->read('assignmenttype');
            $selected_program = $session->read('selected_program');
            $selected_program_type = $session->read('selected_program_type');
            $selected_curriculum = $session->read('selected_curriculum');

            if ($this->role_id != ROLE_COLLEGE) {
                $yearlevel = $session->read('yearlevel');
                $yearlevelname = $this->Sections->YearLevels->find()
                    ->select(['name'])
                    ->where(['YearLevels.id' => $yearlevel])
                    ->first()
                    ->name;
            }

            $sectionlesstotalstudents = $this->Sections->countSectionLessStudents($this->college_id, $this->role_id, $this->department_id, $academicyear, $selected_program, $selected_program_type, $selected_curriculum);
            $program_type_id = $selected_program_type;
            $find_the_equivalent_program_type = unserialize($this->Sections->ProgramTypes->find()
                ->select(['equivalent_to_id'])
                ->where(['ProgramTypes.id' => $selected_program_type])
                ->first()
                ->equivalent_to_id);

            if (!empty($find_the_equivalent_program_type)) {
                $selected_program_type_array = [$selected_program_type];
                $program_type_id = array_merge($selected_program_type_array, $find_the_equivalent_program_type);
            }

            $conditions = ($this->role_id != ROLE_COLLEGE) ? [
                'AcceptedStudents.academicyear' => $academicyear,
                'Students.department_id' => $this->department_id,
                'Students.program_id' => $selected_program,
                'Students.program_type_id IN' => $program_type_id,
                'Students.curriculum_id' => $selected_curriculum,
                'Students.graduated' => 0,
            ] : [
                'AcceptedStudents.academicyear' => $academicyear,
                'Students.college_id' => $this->college_id,
                'Students.program_id' => $selected_program,
                'Students.program_type_id IN' => $program_type_id,
                'Students.curriculum_id IS' => null,
                'Students.department_id IS' => null,
                'Students.graduated' => 0,
            ];

            $selected_assignment_type = $this->request->getData('Section.assignment_type');

            if ($sectionlesstotalstudents != 0) {
                if ($selected_assignment_type == 'result') {
                    $students = $this->Sections->Students->find()
                        ->where($conditions)
                        ->select([
                            'Students.id',
                            'Students.full_name',
                            'Students.studentnumber',
                            'Students.gender',
                            'Students.academicyear',
                        ])
                        ->contain([
                            'Sections' => ['fields' => ['Sections.id', 'Sections.name']],
                            'AcceptedStudents' => ['fields' => ['AcceptedStudents.id']],
                        ])
                        ->order([
                            'AcceptedStudents.EHEECE_total_results' => 'DESC',
                            'AcceptedStudents.sex' => 'ASC',
                            'AcceptedStudents.region_id' => 'ASC',
                        ])
                        ->toArray();

                    $sectionless_student = [];

                    if (!empty($students)) {
                        foreach ($students as $v) {
                            $check_student_section = count($v->sections);
                            if ($check_student_section == 0) {
                                $sectionless_student[] = $v->id;
                            } else {
                                $is_pre_student = 1;
                                foreach ($v->sections as $psv) {
                                    if (isset($psv->department_id) && is_numeric($psv->department_id) && $psv->department_id > 0) {
                                        $is_pre_student = 0;
                                        break;
                                    } else {
                                        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
                                        $last_registration_semester = $courseRegistrationsTable->find()
                                            ->select(['semester'])
                                            ->where(['CourseRegistrations.section_id' => $psv->_joinData->section_id])
                                            ->first()
                                            ->semester ?? null;
                                        // debug($last_registration_semester);
                                        if ($psv->_joinData->archive == 0) {
                                            $is_pre_student = 0;
                                            break;
                                        } else {
                                            $is_pre_student = 1;
                                        }
                                    }
                                }
                                if ($is_pre_student == 1) {
                                    $sectionless_student[] = $v->id;
                                }
                            }
                        }
                    }

                    $sectionless_student_count = count($sectionless_student);
                    $data = $this->request->getData('Section.Sections');
                    $this->request = $this->request->withData('Section.Sections', null);
                    $selected_section_count = count($data);

                    $j = 0;

                    if ($sectionless_student_count > 0 && $selected_section_count > 0) {
                        foreach ($sectionless_student as $student_id) {
                            if ($j >= $selected_section_count) {
                                $j = $j % $selected_section_count;
                            }
                            $studentsSection = $this->Sections->StudentsSections->newEntity([
                                'section_id' => $this->request->getData("Section.$data[$j].id"),
                                'student_id' => $student_id,
                            ]);
                            $this->Sections->StudentsSections->save($studentsSection);
                            $j++;
                            $isassign = 1;
                        }
                    }

                    if ($isassign) {
                        $this->Flash->success('The section(s) assignment has(have) been completed successfully');
                        return $this->redirect(['action' => 'displaySections', $this->request->getData('StudentsSection.section_id')]);
                    } else {
                        $this->Flash->error('The section(s) assignment could not be completed. Please, try again.');
                    }
                } else {
                    if ($this->Sections->isSectionAssignedStudentsEqualToTotalNumberOfAvailableStudents($this->request->getData('Section'), $sectionlesstotalstudents)) {
                        $students = $this->Sections->Students->find()
                            ->where($conditions)
                            ->select([
                                'Students.id',
                                'Students.full_name',
                                'Students.studentnumber',
                                'Students.gender',
                                'Students.academicyear',
                            ])
                            ->contain([
                                'Sections' => ['fields' => ['Sections.id', 'Sections.name']],
                                'AcceptedStudents' => ['fields' => ['AcceptedStudents.id']],
                            ])
                            ->order([
                                'AcceptedStudents.first_name' => 'ASC',
                                'AcceptedStudents.sex' => 'ASC',
                                'AcceptedStudents.region_id' => 'ASC',
                                'AcceptedStudents.EHEECE_total_results' => 'DESC',
                            ])
                            ->toArray();

                        $sectionless_student = [];

                        if (!empty($students)) {
                            foreach ($students as $v) {
                                $check_student_section = count($v->sections);
                                if ($check_student_section == 0) {
                                    $sectionless_student[] = $v->id;
                                } else {
                                    $is_pre_student = 1;
                                    foreach ($v->sections as $psv) {
                                        if (!empty($psv->department_id)) {
                                            $is_pre_student = 0;
                                            break;
                                        } else {
                                            if ($psv->_joinData->archive == 0) {
                                                $is_pre_student = 0;
                                                break;
                                            } else {
                                                $is_pre_student = 1;
                                            }
                                        }
                                    }
                                    if ($is_pre_student == 1) {
                                        $sectionless_student[] = $v->id;
                                    }
                                }
                            }
                        }

                        if ($this->role_id == ROLE_COLLEGE) {
                            $yearlevel = 0;
                        }

                        $sections = $this->Sections->getSectionForAssignment($academicyear, $this->college_id, $this->department_id, $this->role_id, $selected_program, $selected_program_type, $yearlevel, $selected_curriculum);
                        $sections_count = count($sections);
                        $student_index = 0;
                        $isassign = 0;
                        $available_section = null;

                        if ($sections_count > 0) {
                            foreach ($sections as $i => $section) {
                                $number_per_section = $this->request->getData("Section.$i.number");
                                $available_section = $this->request->getData("Section.$i.id");
                                for ($j = 0; $j < $number_per_section; $j++) {
                                    // debug($student_index);
                                    // debug($sectionless_student);
                                    $studentsSection = $this->Sections->StudentsSections->newEntity([
                                        'student_id' => $sectionless_student[$student_index],
                                        'section_id' => $available_section,
                                    ]);
                                    $this->Sections->StudentsSections->save($studentsSection);
                                    $student_index++;
                                    $isassign = 1;
                                }
                            }
                        } else {
                            $this->Flash->error('No sections found for assignment.');
                        }

                        if ($isassign) {
                            $this->Flash->success('The section(s) assignment has(have) been completed successfully.');
                            return $this->redirect(['action' => 'displaySections', $available_section]);
                        } else {
                            $this->Flash->error('The section(s) assignment could not be completed. Please, try again.');
                        }
                    } else {
                        $errors = $this->Sections->validationErrors;
                        if (isset($errors['section'])) {
                            $this->Flash->error($errors['section'][0]);
                        }

                        $this->set(compact('academicyear', 'section_less_total_students'));
                        $this->request = $this->request->withData('Section.Curriculum', $selected_curriculum);

                        if ($session->check('curriculum_search')) {
                            $this->request = $this->request->withData('Section.curriculum_search', $session->read('curriculum_search'));
                        }
                    }
                }
            } else {
                $this->Flash->error('There is no Student to assign section in given parameters');
                $this->set(compact('academicyear', 'section_less_total_students'));
            }
        }

        $this->set(compact('assignmenttype', 'selected_program_type', 'selected_program'));
    }

    public function displaySections(?int $id = null)
    {
        $this->initSearchSections();

        if ($this->request->getData('swapStudentSection') && !empty($this->request->getData('swapStudentSection'))) {
            $rearrangePossible = null;
            if ($this->role_id == ROLE_COLLEGE) {
                $rearrangePossible = $this->Sections->rearrangeSectionList(
                    $this->request->getData('Section.academicyear'),
                    $this->college_id,
                    $this->request->getData('Section.year_level_id'),
                    $this->request->getData('Section.program_id'),
                    $this->request->getData('Section.program_type_id'),
                    $this->request->getData('Section.swap'),
                    1
                );
            } elseif ($this->role_id == ROLE_DEPARTMENT) {
                $rearrangePossible = $this->Sections->rearrangeSectionList(
                    $this->request->getData('Section.academicyear'),
                    $this->department_id,
                    $this->request->getData('Section.year_level_id'),
                    $this->request->getData('Section.program_id'),
                    $this->request->getData('Section.program_type_id'),
                    $this->request->getData('Section.swap')
                );
            }

            if ($rearrangePossible == 3) {
                $this->Flash->error('You cannot swap students once you published courses. Please delete published courses if grade is not submitted.');
            }
            $this->request = $this->request->withData('search', true);
        }

        if (!empty($id) && !$this->request->getData('search')) {
            $selectedSectionDetails = $this->Sections->find()
                ->where(['Sections.id' => $id])
                ->first();

            // debug($selectedSectionDetails);

            if (!empty($selectedSectionDetails)) {
                $this->request = $this->request->withData('Section', $selectedSectionDetails->toArray());
            } else {
                $this->request = $this->request->withData('Section.program_id', $this->Sections->find()
                    ->select(['program_id'])
                    ->where(['Sections.id' => $id])
                    ->first()
                    ->program_id);
                $this->request = $this->request->withData('Section.program_type_id', $this->Sections->find()
                    ->select(['program_type_id'])
                    ->where(['Sections.id' => $id])
                    ->first()
                    ->program_type_id);
                if (empty($this->request->getData('Section.academicyear'))) {
                    $this->request = $this->request->withData('Section.academicyear', $this->AcademicYear->currentAcademicYear());
                }
                if ($this->role_id != ROLE_COLLEGE) {
                    $this->request = $this->request->withData('Section.year_level_id', $this->Sections->find()
                        ->select(['year_level_id'])
                        ->where(['Sections.id' => $id])
                        ->first()
                        ->year_level_id);
                }
            }

            $this->request = $this->request->withData('search', true);

            $this->clearSessionFilters();
            $this->initSearchSections();
        }

        $collegename = $this->Sections->Colleges->find()
            ->select(['name'])
            ->where(['Colleges.id' => $this->college_id])
            ->first()
            ->name;
        $departmentname = $this->Sections->Departments->find()
            ->select(['name'])
            ->where(['Departments.id' => $this->department_id])
            ->first()
            ->name;
        $isbeforesearch = 1;
        $this->set(compact('isbeforesearch', 'collegename', 'departmentname'));

        if (!empty($this->request->getData()) && $this->request->getData('search')) {
            // debug($this->request->getData());
            $this->clearSessionFilters();
            $this->initSearchSections();
            // debug($this->request->getData());

            $isbeforesearch = 0;
            $selected_program = $this->request->getData('Section.program_id');
            $selected_program_type = $this->request->getData('Section.program_type_id');

            $program_type_id = $this->Sections->getEquivalentProgramTypes($selected_program_type);
            // debug($program_type_id);

            $thisacademicyear = !empty($this->request->getData('Section.academicyear'))
                ? $this->request->getData('Section.academicyear')
                : $this->AcademicYear->currentAcademicYear();
            $this->request = $this->request->withData('Section.academicyear', $thisacademicyear);
            $this->clearSessionFilters();
            $this->initSearchSections();

            $selected_year_level = null;

            if ($this->role_id != ROLE_COLLEGE) {
                $selected_year_level = $this->request->getData('Section.year_level_id') ?: '%';
                $conditions = [
                    'Sections.department_id' => $this->department_id,
                    'Sections.archive' => 0,
                    'Sections.program_id' => $selected_program,
                    'Sections.program_type_id IN' => $program_type_id,
                    'Sections.year_level_id LIKE' => $selected_year_level,
                    'Sections.academicyear' => $this->request->getData('Section.academicyear'),
                ];

                $studentsections = $this->Sections->getStudentsSectionById(
                    $this->college_id,
                    $this->role_id,
                    $this->department_id,
                    $selected_program,
                    $program_type_id,
                    $thisacademicyear,
                    $selected_year_level
                );
            } else {
                $conditions = [
                    'Sections.college_id' => $this->college_id,
                    'Sections.archive' => 0,
                    'Sections.program_id' => $selected_program,
                    'Sections.program_type_id IN' => $program_type_id,
                    'Sections.academicyear' => $thisacademicyear,
                    'OR' => [
                        'Sections.department_id IS' => null,
                        'Sections.department_id' => '',
                    ],
                ];

                $studentsections = $this->Sections->getStudentsSectionById(
                    $this->college_id,
                    $this->role_id,
                    $this->department_id,
                    $selected_program,
                    $program_type_id,
                    $thisacademicyear,
                    $selected_year_level
                );
            }

            $sections = $this->Sections->find()
                ->where($conditions)
                ->select([
                    'Sections.id',
                    'Sections.name',
                    'Sections.year_level_id',
                    'Sections.program_id',
                    'Sections.program_type_id',
                    'Sections.academicyear',
                    'Sections.department_id',
                    'Sections.college_id',
                ])
                ->contain([
                    'Students' => [
                        'fields' => [
                            'Students.id',
                            'Students.studentnumber',
                            'Students.full_name',
                            'Students.gender',
                            'Students.graduated',
                            'Students.academicyear',
                        ],
                        'order' => [
                            'Students.academicyear' => 'DESC',
                            'Students.studentnumber' => 'ASC',
                            'Students.id' => 'ASC',
                            'Students.full_name' => 'ASC',
                        ],
                    ],
                    'StudentsSections',
                    'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                    'Departments' => [
                        'fields' => ['id', 'name', 'type', 'college_id'],
                        'Colleges' => ['fields' => ['id', 'name', 'type', 'campus_id', 'stream']],
                    ],
                    'Colleges' => ['fields' => ['id', 'name', 'type', 'campus_id', 'stream']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                ])
                ->toArray();

            // debug($this->Sections->updateSectionCurriculumIdFromPublishedCoursesOfTheSection());

            if (!empty($sections)) {
                foreach ($sections as $section) {
                    if (isset($section->students) && count($section->students) > 0) {
                        // debug($section->id);
                        $this->Sections->removeDuplicateStudentSections($section->id);
                        $this->Sections->updateSectionCurriculumIdFromPublishedCoursesOfTheSection($section->id);
                    }
                }
            }

            $current_sections_occupation = $this->Sections->currentSectionsOccupation($sections);
            $sections_curriculum_name = $this->Sections->sectionsCurriculum($studentsections);

            $this->set(compact(
                'studentsections',
                'collegename',
                'departmentname',
                'current_sections_occupation',
                'sections_curriculum_name',
                'sections',
                'isbeforesearch'
            ));
        }

        $swapOptions = [
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'studentnumber' => 'Student ID',
        ];
        $this->set(compact('swapOptions'));
    }

    public function splitSection()
    {
        $programs = $this->Sections->Programs->find('list')
            ->where(['Programs.id IN' => $this->program_ids])
            ->toArray();
        $programTypes = $this->Sections->ProgramTypes->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids])
            ->toArray();

        $yearLevels = [];

        if ($this->role_id == ROLE_DEPARTMENT) {
            $yearLevels = $this->Sections->YearLevels->find('list')
                ->where(['YearLevels.department_id' => $this->department_id])
                ->toArray();
        }

        $isbeforesearch = 1;

        $current_academic_year = $this->AcademicYear->currentAcademicYear();
        $custom_acy_list = [$current_academic_year => $current_academic_year];

        if (is_numeric(ACY_BACK_FOR_SECTION_ADD) && ACY_BACK_FOR_SECTION_ADD > 0) {
            $custom_acy_list = $this->AcademicYear->academicYearInArray(
                (explode('/', $current_academic_year)[0]) - ACY_BACK_FOR_SECTION_ADD,
                explode('/', $current_academic_year)[0]
            );
        }

        $this->set(compact('programs', 'programTypes', 'isbeforesearch', 'yearLevels', 'custom_acy_list'));

        if (!empty($this->request->getData()) && $this->request->getData('search')) {
            $isbeforesearch = 0;
            $selected_program = $this->request->getData('Section.program_id');
            $selected_program_type = $this->request->getData('Section.program_type_id');
            $selected_academic_year = $this->request->getData('Section.academicyear');
            $selected_year_level = $this->request->getData('Section.year_level_id');

            $session = $this->request->getSession();
            $session->write('selected_program', $selected_program);
            $session->write('selected_program_type', $selected_program_type);
            $session->write('selected_academic_year', $selected_academic_year);
            $session->write('selected_year_level', $selected_year_level);

            $program_type_id = $selected_program_type;
            $find_the_equivalent_program_type = unserialize($this->Sections->ProgramTypes->find()
                ->select(['equivalent_to_id'])
                ->where(['ProgramTypes.id' => $selected_program_type])
                ->first()
                ->equivalent_to_id);

            if (!empty($find_the_equivalent_program_type)) {
                $selected_program_type_array = [$selected_program_type];
                $program_type_id = array_merge($selected_program_type_array, $find_the_equivalent_program_type);
            }

            $conditions = ($this->role_id == ROLE_DEPARTMENT) ? [
                'Sections.college_id' => $this->college_id,
                'Sections.department_id' => $this->department_id,
                'Sections.program_id' => $selected_program,
                'Sections.program_type_id IN' => $program_type_id,
                'Sections.academicyear' => $selected_academic_year,
                'Sections.year_level_id' => $selected_year_level,
                'Sections.archive' => 0,
            ] : [
                'Sections.college_id' => $this->college_id,
                'Sections.program_id' => $selected_program,
                'Sections.program_type_id IN' => $program_type_id,
                'Sections.academicyear' => $selected_academic_year,
                'Sections.archive' => 0,
                'OR' => [
                    'Sections.department_id IS' => null,
                    'Sections.department_id' => '',
                    'Sections.department_id' => 0,
                ],
            ];

            $sections = $this->Sections->find()
                ->where($conditions)
                ->select(['Sections.id', 'Sections.name'])
                ->contain([
                    'Students' => [
                        'fields' => ['Students.id', 'Students.full_name', 'Students.studentnumber', 'Students.gender'],
                    ],
                ])
                ->toArray();

            $current_sections_occupation = $this->Sections->currentSectionsOccupation($sections);

            $session->write('current_sections_occupation', $current_sections_occupation);
            $session->write('sections', $sections);
            $this->set(compact('sections', 'current_sections_occupation', 'isbeforesearch'));
        }

        if (!empty($this->request->getData()) && $this->request->getData('split')) {
            $is_course_published = 0;
            $selected_section = $this->request->getData('Section.selectedsection');
            $number_of_section = $this->request->getData('Section.number_of_section');
            $session = $this->request->getSession();
            $sections = $session->read('sections');
            // debug($sections);

            $current_sections_occupation = $session->read('current_sections_occupation');
            $selected_program = $session->read('selected_program');
            $selected_program_type = $session->read('selected_program_type');
            $selected_academic_year = $session->read('selected_academic_year');

            $selected_section_id = $this->request->getData("Section.$selected_section.id");

            $current_academic_year = !empty($selected_academic_year)
                ? $selected_academic_year
                : $this->AcademicYear->currentAcademicYear();

            $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
            $section_semester = $courseRegistrationsTable->checkCourseIsPublishedForSection($this->request->getData("Section.$selected_section.id"), $current_academic_year);

            if ($section_semester != 2) {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $is_course_published = $this->Sections->PublishedCourses->find()
                        ->where([
                            'PublishedCourses.department_id' => $this->department_id,
                            'PublishedCourses.section_id' => $selected_section_id,
                            'PublishedCourses.semester' => $section_semester,
                            'PublishedCourses.academic_year' => $current_academic_year,
                        ])
                        ->count();
                } elseif ($this->role_id == ROLE_COLLEGE) {
                    $is_course_published = $this->Sections->PublishedCourses->find()
                        ->where([
                            'PublishedCourses.college_id' => $this->college_id,
                            'PublishedCourses.section_id' => $selected_section_id,
                            'PublishedCourses.semester' => $section_semester,
                            'PublishedCourses.academic_year' => $current_academic_year,
                            'PublishedCourses.department_id IS' => null,
                        ])
                        ->count();
                }
            }

            if ($selected_section == -1) {
                $is_course_published = 0;
            }

            if (!$is_course_published) {
                if (($selected_section != -1) && $current_sections_occupation[$selected_section] >= $number_of_section) {
                    $selected_section_id = $sections[$selected_section]['id'];
                    $selected_section_name = $sections[$selected_section]['name'];
                    // debug($selected_section_name);
                    $variable_selected_sectionname = substr($selected_section_name, strrpos($selected_section_name, ' ') + 1);
                    // debug($variable_selected_sectionname);

                    if ($this->role_id != ROLE_COLLEGE) {
                        $first_space = strpos($selected_section_name, ' ');
                        $second_space = strrpos($selected_section_name, ' ');
                        $prefix_selected_sectionname = substr($selected_section_name, 0, $first_space);
                        $fixed_selected_sectionname = substr($selected_section_name, ($first_space + 1), ($second_space - ($first_space + 1)));
                    } else {
                        $first_space = strpos($selected_section_name, ' ');
                        $fixed_selected_sectionname = substr($selected_section_name, 0, $first_space);
                    }

                    $program_type_id = $selected_program_type;
                    $find_the_equivalent_program_type = unserialize($this->Sections->ProgramTypes->find()
                        ->select(['equivalent_to_id'])
                        ->where(['ProgramTypes.id' => $selected_program_type])
                        ->first()
                        ->equivalent_to_id);

                    if (!empty($find_the_equivalent_program_type)) {
                        $selected_program_type_array = [$selected_program_type];
                        $program_type_id = array_merge($selected_program_type_array, $find_the_equivalent_program_type);
                    }

                    $conditions = ($this->role_id == ROLE_DEPARTMENT) ? [
                        'Sections.college_id' => $this->college_id,
                        'Sections.department_id' => $this->department_id,
                        'Sections.program_id' => $selected_program,
                        'Sections.program_type_id IN' => $program_type_id,
                        'Sections.academicyear' => $current_academic_year,
                        'Sections.year_level_id' => $session->read('selected_year_level'),
                        'Sections.archive' => 0,
                    ] : [
                        'Sections.college_id' => $this->college_id,
                        'Sections.program_id' => $selected_program,
                        'Sections.program_type_id IN' => $program_type_id,
                        'Sections.academicyear' => $current_academic_year,
                        'Sections.archive' => 0,
                        'OR' => [
                            'Sections.department_id IS' => null,
                            'Sections.department_id' => '',
                            'Sections.department_id' => 0,
                        ],
                    ];

                    $all_section_name = $this->Sections->find('list')
                        ->where($conditions)
                        ->select(['Sections.name'])
                        ->order(['Sections.name'])
                        ->toArray();

                    $numeric_section_variablename = [];
                    $character_section_variablename = [];

                    if (!empty($all_section_name)) {
                        foreach ($all_section_name as $sv) {
                            $variable = substr($sv, strrpos($sv, ' ') + 1);
                            if (is_numeric($variable)) {
                                $numeric_section_variablename[] = $variable;
                            } else {
                                $character_section_variablename[] = $variable;
                            }
                        }
                    }

                    $last_section_variablename = null;
                    $full_variablename_array = [];
                    $gap_section_name_array = [];

                    if (is_numeric($variable_selected_sectionname)) {
                        $section_variablename_count = count($numeric_section_variablename);
                        $last_section_variablename = $numeric_section_variablename[$section_variablename_count - 1];

                        for ($i = $last_section_variablename; $i >= 1; $i--) {
                            $full_variablename_array[] = $i;
                        }

                        $gap_section_name_array = array_diff($full_variablename_array, $numeric_section_variablename);
                    } else {
                        $section_variablename_count = count($character_section_variablename);
                        $last_section_variablename = $character_section_variablename[$section_variablename_count - 1];
                        $last_section_variablename = ord($last_section_variablename);

                        for ($i = $last_section_variablename; $i >= 65; $i--) {
                            $full_variablename_array[] = chr($i);
                        }

                        $last_section_variablename = chr($last_section_variablename);
                        $gap_section_name_array = array_diff($full_variablename_array, $character_section_variablename);
                    }

                    sort($gap_section_name_array);

                    $split_section_names_array = [];
                    $i = 0;
                    $j = 1;

                    while ($i < $number_of_section) {
                        $checkIfSectionNameTaken = $this->Sections->find()
                            ->where(['Sections.name' => $selected_section_name . ' ' . $j])
                            ->count();
                        if ($checkIfSectionNameTaken == 0) {
                            $split_section_names_array[$i] = $selected_section_name . ' ' . $j;
                            $i++;
                        }
                        $j++;
                    }

                    $splitedSection = [
                        'college_id' => $this->college_id,
                        'program_id' => $selected_program,
                        'program_type_id' => $selected_program_type,
                        'academicyear' => $current_academic_year,
                    ];

                    if ($this->role_id != ROLE_COLLEGE) {
                        $splitedSection['department_id'] = $this->department_id;
                        $splitedSection['year_level_id'] = $selected_year_level;
                    }

                    $deleteOk = true;
                    $secSave = false;

                    if ($this->Sections->CourseRegistrations->ExamGrades->isEverGradeSubmitInTheNameOfSection($selected_section_id)) {
                        $deleteOk = false;
                        $section = $this->Sections->get($selected_section_id);
                        $section->archive = 1;
                        $this->Sections->save($section);
                    }

                    $split_section_id_array = [];
                    $section_id_for_redirect = $selected_section_id;

                    foreach ($split_section_names_array as $name) {
                        $splitedSection['name'] = $name;
                        $sectionEntity = $this->Sections->newEntity($splitedSection);
                        if ($this->Sections->save($sectionEntity)) {
                            $split_section_id_array[] = $sectionEntity->id;
                            $secSave = true;
                            $section_id_for_redirect = $sectionEntity->id;
                        } else {
                            $error = $sectionEntity->getErrors();
                            // debug($error);
                        }
                    }

                    if (!empty($split_section_id_array) && $secSave) {
                        $studentssections = $this->Sections->StudentsSections->find()
                            ->where([
                                'StudentsSections.section_id' => $selected_section_id,
                                'StudentsSections.archive' => 0,
                            ])
                            ->toArray();

                        // debug($studentssections);
                        $k = 0;

                        if (!empty($studentssections)) {
                            foreach ($studentssections as $ssv) {
                                if (isset($split_section_id_array[$k]) && !empty($split_section_id_array[$k]) && isset($ssv->student_id) && !empty($ssv->student_id)) {
                                    $studentsSection = $this->Sections->StudentsSections->newEntity([
                                        'student_id' => $ssv->student_id,
                                        'section_id' => $split_section_id_array[$k],
                                    ]);
                                    $this->Sections->StudentsSections->save($studentsSection);
                                }

                                $k++;
                                if (($k % $number_of_section) == 0) {
                                    $k = 0;
                                }
                            }
                        }
                    }

                    if ($deleteOk && $secSave) {
                        $this->Sections->deleteAll(['Sections.id' => $selected_section_id], false);

                        $studentssections = $this->Sections->StudentsSections->find()
                            ->where([
                                'StudentsSections.section_id' => $selected_section_id,
                                'StudentsSections.archive' => 0,
                            ])
                            ->toArray();

                        if (!empty($studentssections)) {
                            foreach ($studentssections as $ssv) {
                                $this->Sections->StudentsSections->delete($ssv);
                            }
                        }
                    }

                    if ($secSave) {
                        $split_sections = implode(', ', $split_section_names_array);
                        $this->Flash->success("Section $selected_section_name is split into $split_sections sections successfully.");
                    } else {
                        $this->Flash->error("Section $selected_section_name is not split, please try again.");
                    }

                    return $this->redirect(['action' => 'displaySections', $section_id_for_redirect]);
                } else {
                    $this->Flash->error("Please select a section with students greater than or equal to the number of sections to split, which is: $number_of_section.");
                }
            } else {
                $this->Flash->error("You cannot split the selected section since a course has been published for $section_semester[semester]/$current_academic_year. First unpublish the courses and split the section.");
                return $this->redirect(['controller' => 'PublishedCourses', 'action' => 'unpublish']);
            }

            $this->request = $this->request->withData('search', true);
            $isbeforesearch = 0;
            $this->set(compact('sections', 'current_sections_occupation', 'isbeforesearch'));
        }
    }

    public function export(?int $sectionid = null)
    {
        $students_per_section = $this->Sections->getStudentsSectionById($sectionid);
        $this->set(compact('students_per_section'));
    }

    public function view_pdf(?int $id = null)
    {
        if (!$id) {
            $this->Flash->error('Sorry, Invalid request.');
            return $this->redirect(['action' => 'index']);
        }

        $colleges = $this->Sections->Colleges->find('list')
            ->where(['Colleges.id' => $this->college_id, 'Colleges.active' => 1])
            ->toArray();
        $collegename = $colleges[$this->college_id] ?? '';

        $departmentname = '';
        if (!empty($this->department_id)) {
            $departments = $this->Sections->Departments->find('list')
                ->where(['Departments.id' => $this->department_id, 'Departments.active' => 1])
                ->toArray();
            $departmentname = $departments[$this->department_id] ?? '';
        }

        $studentsections = $this->Sections->getStudentsSectionById($id);

        $this->set(compact('studentsections', 'collegename', 'departmentname'));
        $this->response = $this->response->withType('application/pdf');
        $this->viewBuilder()->setLayout('pdf/default');
        $this->render();
    }


    public function deleteStudentForThisSection(?int $section_id = null, ?string $student_number = null)
    {
        if (!$section_id || !$student_number) {
            $this->Flash->error('Invalid ID for Section or/and Student');
            return $this->redirect(['action' => 'displaySections']);
        }

        $student_number = str_replace('-', '/', $student_number);
        $section_name = $this->Sections->find()
            ->select(['name'])
            ->where(['Sections.id' => $section_id])
            ->first()
            ->name ?? '';

        $student_id = $this->Sections->Students->find()
            ->select(['id'])
            ->where(['Students.studentnumber' => $student_number])
            ->first()
            ->id ?? null;

        if ($this->Sections->Students->CourseRegistrations->ExamResults->isStudentSectionChangePossible($student_id, $section_id)) {
            $studentsSection = $this->Sections->StudentsSections->find()
                ->where([
                    'StudentsSections.student_id' => $student_id,
                    'StudentsSections.section_id' => $section_id,
                    'StudentsSections.archive' => 0,
                ])
                ->first();
            if ($studentsSection) {
                $this->Sections->StudentsSections->delete($studentsSection);
            }
        } else {
            if ($this->Sections->Students->CourseRegistrations->ExamResults->isRegisteredInNameOfSectionAndSubmittedGrade($student_id, $section_id)) {
                $this->Flash->error("$student_number cannot be removed from $section_name section because the student is registered for one or more courses in this section, and grades have not been fully submitted.");
                return $this->redirect(['action' => 'displaySections', $section_id]);
            } else {
                $studentsSection = $this->Sections->StudentsSections->find()
                    ->where([
                        'StudentsSections.student_id' => $student_id,
                        'StudentsSections.section_id' => $section_id,
                        'StudentsSections.archive' => 0,
                    ])
                    ->first();
                if ($studentsSection) {
                    $studentsSection->archive = 1;
                    $this->Sections->StudentsSections->save($studentsSection);
                }
            }
        }

        $this->Flash->success("$student_number is now removed from $section_name section");
        return $this->redirect(['action' => 'displaySections', $section_id]);
    }

    public function archieveUnarchieveStudentSection($section_id,  $student_id,  $archive)
    {
        if (!$section_id || !$student_id) {
            $this->Flash->error('Invalid ID for Section or/and Student');
            return $this->redirect($this->referer());
        }

        $section_name = $this->Sections->find()
            ->select(['name'])
            ->where(['Sections.id' => $section_id])
            ->first()
            ->name ?? '';
        $student_number = $this->Sections->Students->find()
            ->select(['studentnumber'])
            ->where(['Students.id' => $student_id])
            ->first()
            ->studentnumber ?? '';

        if ($archive) {
            if ($this->Sections->Students->CourseRegistrations->ExamResults->isStudentSectionChangePossible($student_id, $section_id)) {
                $studentsSection = $this->Sections->StudentsSections->find()
                    ->where([
                        'StudentsSections.student_id' => $student_id,
                        'StudentsSections.section_id' => $section_id,
                        'StudentsSections.archive' => 0,
                    ])
                    ->first();
                if ($studentsSection) {
                    $studentsSection->archive = 1;
                    $this->Sections->StudentsSections->save($studentsSection);
                    $this->Flash->success("$student_number has been successfully archived from $section_name section.");
                } else {
                    $this->Flash->error("Could not archive $student_number from $section_name section.");
                }
            } else {

                if ($this->Sections->checkAllRegisteredAddedCoursesAreGraded($student_id, $section_id, 0)) {
                    $studentsSection = $this->Sections->StudentsSections->find()
                        ->where([
                            'StudentsSections.student_id' => $student_id,
                            'StudentsSections.section_id' => $section_id,
                            'StudentsSections.archive' => 0,
                        ])
                        ->first();

                    if ($studentsSection) {
                        $studentsSection->archive = 1;
                        $this->Sections->StudentsSections->save($studentsSection);
                        $this->Flash->success("$student_number has been successfully archived from $section_name section.");
                    } else {
                        $this->Flash->error("Could not archive $student_number from $section_name section.");
                    }
                } else {
                    $this->Flash->error("$student_number cannot be archived from $section_name section at this time.
                    The student is registered for one or more courses in this section,
                    and grades have not been fully submitted. Please try again once all grades are submitted!");
                }
            }
        } else {
            $studentsSection = $this->Sections->StudentsSections->find()
                ->where([
                    'StudentsSections.student_id' => $student_id,
                    'StudentsSections.section_id' => $section_id,
                    'StudentsSections.archive' => 1,
                ])
                ->first();
            if ($studentsSection) {
                $studentsSection->archive = 0;
                $this->Sections->StudentsSections->save($studentsSection);
                $this->Flash->success("$student_number has been successfully unarchived for $section_name section.");
            } else {
                $this->Flash->error("Could not unarchive $student_number for $section_name section.");
            }
        }

        $refererUrl = explode('/', $this->referer());
        // debug($refererUrl);

        if (is_array($refererUrl) && !empty($refererUrl) && in_array('studentAcademicProfile', $refererUrl)) {
            return $this->redirect(['controller' => 'Students', 'action' => 'studentAcademicProfile', $student_id]);
        } elseif (is_array($refererUrl) && !empty($refererUrl) && in_array('display_sections', $refererUrl)) {
            return $this->redirect(['action' => 'displaySections', $section_id]);
        } else {
            return $this->redirect($this->referer());
        }
    }

    public function deleteStudent(?int $section_id = null, ?string $student_number = null)
    {
        $student_number = str_replace('-', '/', $student_number);
        $student_id = $this->Sections->Students->find()
            ->select(['id'])
            ->where(['Students.studentnumber' => $student_number])
            ->first()
            ->id ?? null;

        if (!$section_id || !$student_number) {
            $this->Flash->error('Invalid id for section or/and student.');
            return $this->redirect(['action' => 'displaySections']);
        }

        $section_name = $this->Sections->find()
            ->select(['name'])
            ->where(['Sections.id' => $section_id])
            ->first()
            ->name ?? '';

        if ($this->Sections->Students->CourseRegistrations->ExamResults->isStudentSectionChangePossible($student_id, $section_id)) {
            $studentsSection = $this->Sections->StudentsSections->find()
                ->where([
                    'StudentsSections.student_id' => $student_id,
                    'StudentsSections.section_id' => $section_id,
                ])
                ->first();
            if ($studentsSection) {
                $this->Sections->StudentsSections->delete($studentsSection);
                $this->Flash->success("$student_number has been successfully removed from $section_name section.");
                return $this->redirect(['controller' => 'Students', 'action' => 'studentAcademicProfile', $student_id]);
            }
        } else {
            if ($this->Sections->Students->CourseRegistrations->ExamResults->isRegisteredInNameOfSectionAndSubmittedGrade($student_id, $section_id)) {
                $this->Flash->error("$student_number cannot be removed from $section_name section because the student is registered for one or more courses in this section, and grades have not been fully submitted.");
                return $this->redirect(['controller' => 'Students', 'action' => 'studentAcademicProfile', $student_id]);
            } else {
                $studentsSection = $this->Sections->StudentsSections->find()
                    ->where([
                        'StudentsSections.student_id' => $student_id,
                        'StudentsSections.section_id' => $section_id,
                    ])
                    ->first();
                if ($studentsSection) {
                    $this->Sections->StudentsSections->delete($studentsSection);
                    $this->Flash->success("$student_number has been successfully removed from $section_name section.");
                    return $this->redirect(['controller' => 'Students', 'action' => 'studentAcademicProfile', $student_id]);
                }
            }
        }

        $this->Flash->error("$student_number cannot be removed from $section_name section.");
        return $this->redirect(['controller' => 'Students', 'action' => 'studentAcademicProfile', $student_id]);
    }

    public function move(?string $student_number = null, ?int $previous_section_id = null)
    {
        $this->viewBuilder()->setLayout('ajax');
        $student_number = str_replace('-', '/', $student_number);

        $student_id = $this->Sections->Students->find()
            ->select(['id'])
            ->where(['Students.studentnumber' => $student_number])
            ->first()
            ->id ?? null;

        $prevSectionsDetail = $this->Sections->find()
            ->where(['Sections.id' => $previous_section_id])
            ->contain(['YearLevels' => ['fields' => ['id', 'name']]])
            ->first();

        if ($this->role_id == ROLE_DEPARTMENT) {
            $next_year_level_exists = $this->Sections->YearLevels->find()
                ->where([
                    'YearLevels.department_id' => $this->department_id,
                    'YearLevels.id >' => $prevSectionsDetail->year_level->id,
                ])
                ->order(['YearLevels.id' => 'ASC'])
                ->first();

            $next_year_level = [];

            if (!empty($next_year_level_exists)) {
                $next_year_level = $this->Sections->find()
                    ->where([
                        'Sections.program_id' => $prevSectionsDetail->program_id,
                        'Sections.program_type_id' => $prevSectionsDetail->program_type_id,
                        'Sections.department_id' => $this->department_id,
                        'Sections.year_level_id' => $next_year_level_exists->id,
                        'Sections.academicyear <>' => $prevSectionsDetail->academicyear,
                        'Sections.id >' => $prevSectionsDetail->id,
                        'Sections.archive' => 0,
                    ])
                    ->contain(['YearLevels' => ['fields' => ['id', 'name']]])
                    ->first();
            }

            $conditions = (!empty($next_year_level) && !empty($next_year_level->year_level_id)) ? [
                'Sections.id <>' => $prevSectionsDetail->id,
                'Sections.program_id' => $prevSectionsDetail->program_id,
                'Sections.program_type_id' => $prevSectionsDetail->program_type_id,
                'Sections.department_id' => $this->department_id,
                'Sections.year_level_id >=' => $prevSectionsDetail->year_level->id,
                'Sections.year_level_id <=' => $next_year_level->year_level->id,
                'Sections.academicyear IN' => [$prevSectionsDetail->academicyear, $next_year_level->academicyear],
                'OR' => [
                    'Sections.curriculum_id IS' => null,
                    'Sections.curriculum_id' => $prevSectionsDetail->curriculum_id,
                ],
                'Sections.archive' => 0,
            ] : [
                'Sections.id <>' => $prevSectionsDetail->id,
                'Sections.program_id' => $prevSectionsDetail->program_id,
                'Sections.program_type_id' => $prevSectionsDetail->program_type_id,
                'Sections.department_id' => $this->department_id,
                'Sections.year_level_id' => $prevSectionsDetail->year_level->id,
                'Sections.academicyear' => $prevSectionsDetail->academicyear,
                'OR' => [
                    'Sections.curriculum_id IS' => null,
                    'Sections.curriculum_id' => $prevSectionsDetail->curriculum_id,
                ],
                'Sections.archive' => 0,
            ];
        } else {
            $conditions = [
                'Sections.college_id' => $this->college_id,
                'Sections.id <>' => $prevSectionsDetail->id,
                'Sections.program_id' => $prevSectionsDetail->program_id,
                'Sections.program_type_id' => $prevSectionsDetail->program_type_id,
                'Sections.academicyear' => $prevSectionsDetail->academicyear,
                'Sections.department_id IS' => null,
                'Sections.archive' => 0,
            ];
        }

        $sections_all = $this->Sections->find()
            ->where($conditions)
            ->order([
                'Sections.academicyear' => 'ASC',
                'Sections.year_level_id' => 'ASC',
                'Sections.id' => 'ASC',
                'Sections.name' => 'ASC',
            ])
            ->contain(['YearLevels' => ['fields' => ['id', 'name']]])
            ->toArray();

        $sections = [];

        if (!empty($sections_all)) {
            foreach ($sections_all as $section) {
                $sections[$section->id] = $section->name . ' (' . ($section->year_level->name ?? ($section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $section->academicyear . ')';
            }
        }

        $this->set(compact('previous_section_id', 'student_id', 'sections', 'student_number'));
    }

    public function moveSelectedStudentSection(?int $previous_section_id = null)
    {
        $this->viewBuilder()->setLayout('ajax');

        $studentsections = $this->Sections->getAllActiveStudents($previous_section_id);

        $prevSectionsDetail = $this->Sections->find()
            ->where(['Sections.id' => $previous_section_id])
            ->contain(['YearLevels' => ['fields' => ['id', 'name']]])
            ->first();

        $sectionCreatedDate = new DateTime($prevSectionsDetail->created);
        $sectionCreatedDate->modify('-1 month');
        $sectionCreatedDate = $sectionCreatedDate->format('Y-m-d');

        $student_curriculum_id = !empty($this->Sections->getAllActiveStudents($previous_section_id)['students'])
            ? $this->Sections->getAllActiveStudents($previous_section_id)['students'][0]['curriculum_id']
            : null;

        if ($this->role_id == ROLE_DEPARTMENT) {
            $next_year_level_exists = $this->Sections->YearLevels->find()
                ->where([
                    'YearLevels.department_id' => $this->department_id,
                    'YearLevels.id >' => $prevSectionsDetail->year_level->id,
                ])
                ->order(['YearLevels.id' => 'ASC'])
                ->first();

            $next_year_level = [];

            if (ALLOW_STUDENT_SECTION_MOVE_TO_NEXT_YEAR_LEVEL && !empty($next_year_level_exists)) {
                $next_year_level = $this->Sections->find()
                    ->where([
                        'Sections.program_id' => $prevSectionsDetail->program_id,
                        'Sections.program_type_id' => $prevSectionsDetail->program_type_id,
                        'Sections.department_id' => $this->department_id,
                        'Sections.year_level_id' => $next_year_level_exists->id,
                        'Sections.academicyear <>' => $prevSectionsDetail->academicyear,
                        'Sections.id >' => $prevSectionsDetail->id,
                        'Sections.archive' => 0,
                    ])
                    ->contain(['YearLevels' => ['fields' => ['id', 'name']]])
                    ->first();
            }

            $conditions = (!empty($next_year_level) && !empty($next_year_level->year_level_id)) ? [
                'Sections.id <>' => $prevSectionsDetail->id,
                'Sections.program_id' => $prevSectionsDetail->program_id,
                'Sections.program_type_id' => $prevSectionsDetail->program_type_id,
                'Sections.department_id' => $this->department_id,
                'Sections.year_level_id >=' => $prevSectionsDetail->year_level->id,
                'Sections.year_level_id <=' => $next_year_level->year_level->id,
                'Sections.created >' => $sectionCreatedDate,
                'Sections.academicyear IN' => [$prevSectionsDetail->academicyear, $next_year_level->academicyear],
                'OR' => [
                    'Sections.curriculum_id IS' => null,
                    'Sections.curriculum_id' => $prevSectionsDetail->curriculum_id,
                ],
                'Sections.archive' => 0,
            ] : [
                'Sections.id <>' => $prevSectionsDetail->id,
                'Sections.program_id' => $prevSectionsDetail->program_id,
                'Sections.program_type_id' => $prevSectionsDetail->program_type_id,
                'Sections.department_id' => $this->department_id,
                'Sections.year_level_id' => $prevSectionsDetail->year_level->id,
                'Sections.academicyear' => $prevSectionsDetail->academicyear,
                'OR' => [
                    'Sections.curriculum_id IS' => null,
                    'Sections.curriculum_id IN' => [$student_curriculum_id, $prevSectionsDetail->curriculum_id],
                ],
                'Sections.archive' => 0,
            ];
        } else {
            $conditions = [
                'Sections.college_id' => $this->college_id,
                'Sections.id <>' => $prevSectionsDetail->id,
                'Sections.program_id' => $prevSectionsDetail->program_id,
                'Sections.program_type_id' => $prevSectionsDetail->program_type_id,
                'Sections.created >' => $sectionCreatedDate,
                'Sections.academicyear' => $prevSectionsDetail->academicyear,
                'Sections.department_id IS' => null,
                'Sections.archive' => 0,
            ];
        }

        $sections_all = $this->Sections->find()
            ->where($conditions)
            ->order([
                'Sections.year_level_id' => 'ASC',
                'Sections.academicyear' => 'ASC',
                'Sections.id' => 'ASC',
                'Sections.name' => 'ASC',
            ])
            ->contain(['YearLevels' => ['fields' => ['id', 'name']]])
            ->toArray();

        $sections = [];

        if (!empty($sections_all)) {
            foreach ($sections_all as $section) {
                $sections[$section->id] = $section->name . ' (' . ($section->year_level->name ?? ($section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $section->academicyear . ') (' . count($this->Sections->getAllActiveStudents($section->id)['students']) . ')';
            }
        }

        $previousSectionName = $this->Sections->find()
            ->where(['Sections.id' => $previous_section_id])
            ->contain([
                'Programs',
                'ProgramTypes',
                'YearLevels',
                'Departments',
                'Colleges',
            ])
            ->first();

        if (!empty($studentsections['students'])) {
            foreach ($studentsections['students'] as $key => $student) {
                if (!$this->Sections->checkAllRegisteredAddedCoursesAreGraded($student['id'], $previous_section_id, 1)) {
                    unset($studentsections['students'][$key]);
                }
            }
        }

        $this->set(compact('previous_section_id', 'sections', 'previousSectionName', 'studentsections'));
    }

    public function moveStudentSectionToNew(int $previous_section_id, int $student_id)
    {
        $this->viewBuilder()->setLayout('ajax');

        $student = $this->Sections->Students->find()
            ->where(['Students.id' => $student_id])
            ->first();
        $previousSection = $this->Sections->find()
            ->where(['Sections.id' => $previous_section_id])
            ->first();
        $equivalentProgramTypes = $this->getEquivalentProgramTypes($student->program_type_id);
        $current_academicyear = $this->AcademicYear->currentAcademicYear();

        $conditions = [];

        if (!$previousSection->archive || ($previousSection->archive && $previousSection->academicyear == $current_academicyear)) {
            $conditions = (empty($student->department_id)) ? [
                'Sections.college_id' => $student->college_id,
                'Sections.id <>' => $previous_section_id,
                'Sections.archive' => 0,
                'Sections.program_id' => $student->program_id,
                'Sections.program_type_id IN' => $equivalentProgramTypes,
                'Sections.academicyear' => $previousSection->academicyear,
                'OR' => [
                    'Sections.department_id IS' => null,
                    'Sections.department_id' => '',
                ],
            ] : [
                'Sections.department_id' => $student->department_id,
                'Sections.id <>' => $previous_section_id,
                'Sections.archive' => 0,
                'Sections.program_id' => $student->program_id,
                'Sections.program_type_id IN' => $equivalentProgramTypes,
                'Sections.academicyear' => $previousSection->academicyear,
                'Sections.year_level_id' => $previousSection->year_level_id,
            ];
        } elseif ($previousSection->archive && $previousSection->academicyear != $current_academicyear) {
            $conditions = (empty($student->department_id)) ? [
                'Sections.college_id' => $student->college_id,
                'Sections.id <>' => $previous_section_id,
                'Sections.archive' => 0,
                'Sections.program_id' => $student->program_id,
                'Sections.program_type_id IN' => $equivalentProgramTypes,
                'Sections.academicyear' => $current_academicyear,
                'OR' => [
                    'Sections.department_id IS' => null,
                    'Sections.department_id' => '',
                ],
            ] : [
                'Sections.department_id' => $student->department_id,
                'Sections.id <>' => $previous_section_id,
                'Sections.archive' => 0,
                'Sections.program_id' => $student->program_id,
                'Sections.program_type_id IN' => $equivalentProgramTypes,
                'Sections.academicyear' => $current_academicyear,
                'Sections.year_level_id >' => $previousSection->year_level_id,
            ];
        }

        $sectionsList = [];

        if (!empty($conditions)) {
            $sectionsList = $this->Sections->find()
                ->where($conditions)
                ->contain(['YearLevels'])
                ->order([
                    'Sections.academicyear' => 'ASC',
                    'Sections.year_level_id' => 'ASC',
                    'Sections.college_id' => 'ASC',
                    'Sections.department_id' => 'ASC',
                    'Sections.program_id' => 'ASC',
                    'Sections.program_type_id' => 'ASC',
                    'Sections.name' => 'ASC',
                    'Sections.id' => 'ASC',
                ])
                ->toArray();
        }

        $sections = [];

        if (!empty($sectionsList)) {
            foreach ($sectionsList as $v) {
                $sections[$v->id] = trim($v->name) . ' (' . ($v->year_level->name ?? ($v->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $v->academicyear . ')';
            }
        }

        $previousSectionName = $this->Sections->find()
            ->where(['Sections.id' => $previous_section_id])
            ->contain([
                'Programs',
                'ProgramTypes',
                'YearLevels',
                'Departments',
                'Colleges',
            ])
            ->first();

        $this->set(compact(
            'previous_section_id',
            'sections',
            'previousSectionName',
            'student'
        ));
    }

    private function getEquivalentProgramTypes(int $program_type_id = 0): array
    {
        $program_types_to_look = [];

        $equivalentProgramType = unserialize($this->Sections->ProgramTypes->find()
            ->select(['equivalent_to_id'])
            ->where(['ProgramTypes.id' => $program_type_id])
            ->first()
            ->equivalent_to_id ?? '');

        if (!empty($equivalentProgramType)) {
            $selected_program_type_array = [$program_type_id];
            $program_types_to_look = array_merge($selected_program_type_array, $equivalentProgramType);
        } else {
            $program_types_to_look[] = $program_type_id;
        }

        // debug($program_types_to_look);
        return $program_types_to_look;
    }

    public function sectionMoveUpdate()
    {
        if (!empty($this->request->getData()) && $this->request->getData('move_to_section')) {
            // debug($this->request->getData('Section'));
            $selectedStudents = $this->request->getData();

            $selectedStudentsId = [];
            $selected_student_id = '';

            if (!empty($selectedStudents['Section'])) {
                foreach ($selectedStudents['Section'] as $vv) {
                    if (!empty($vv['selected_id'])) {
                        $selectedStudentsId[] = $vv['student_id'];
                        $selected_student_id = $vv['student_id'];
                    }
                }
            }

            if (empty($selectedStudentsId)) {
                $selectedStudentsId[] = $selectedStudents['Section']['student_id'];
            }

            if ($this->Sections->isSectionMoveAllowed($this->request->getData('Section.previous_section_id'), $selectedStudentsId, $this->request->getData('Section.Selected_section_id'))) {
                $new_section_name = $this->Sections->find()
                    ->select(['name'])
                    ->where(['Sections.id' => $this->request->getData('Section.Selected_section_id')])
                    ->first()
                    ->name;
                $previous_section_name = $this->Sections->find()
                    ->select(['name'])
                    ->where(['Sections.id' => $this->request->getData('Section.previous_section_id')])
                    ->first()
                    ->name;
                $this->Flash->success("The selected student is moved from $previous_section_name section to $new_section_name section.");
            } else {
                $errors = $this->Sections->validationErrors;
                if (isset($errors['move_not_allowed'])) {
                    $this->Flash->error($errors['move_not_allowed'][0]);
                }
            }

            return $this->redirect(['controller' => 'Students', 'action' => 'studentAcademicProfile', $selected_student_id]);
        } elseif (!empty($this->request->getData())) {
            $selected_section_curriculum = $this->Sections->getSectionCurriculum($this->request->getData('Section.Selected_section_id'));

            $isSectionCollegeSection = $this->Sections->find()
                ->where([
                    'Sections.id' => $this->request->getData('Section.Selected_section_id'),
                    'Sections.department_id IS' => null,
                    'Sections.college_id' => $this->college_id,
                ])
                ->count();

            if (!empty($selected_section_curriculum) || $isSectionCollegeSection > 0) {
                $previous_section_curriculum = $this->Sections->getSectionCurriculum($this->request->getData('Section.previous_section_id'));
                $similarAcademicYear = false;
                $sameCurriculum = false;

                $college_selected_section = $this->Sections->find()
                    ->select(['academicyear'])
                    ->where([
                        'Sections.id' => $this->request->getData('Section.Selected_section_id'),
                        'Sections.department_id IS' => null,
                        'Sections.college_id' => $this->college_id,
                    ])
                    ->first()
                    ->academicyear ?? '';

                $college_previous_section_selected = $this->Sections->find()
                    ->select(['academicyear'])
                    ->where([
                        'Sections.id' => $this->request->getData('Section.previous_section_id'),
                        'Sections.department_id IS' => null,
                        'Sections.college_id' => $this->college_id,
                    ])
                    ->first()
                    ->academicyear ?? '';

                if ($selected_section_curriculum == "nostudentinsection" && !empty($previous_section_curriculum)) {
                    $selected_section_curriculum = $previous_section_curriculum;
                }

                if (!empty($previous_section_curriculum) && !empty($selected_section_curriculum) && $previous_section_curriculum == $selected_section_curriculum) {
                    $sameCurriculum = true;
                }

                if (strcasecmp($college_selected_section, $college_previous_section_selected) === 0) {
                    $similarAcademicYear = true;
                }

                if ($sameCurriculum || $similarAcademicYear) {
                    $selectedStudents = $this->request->getData();

                    $selectedStudentsId = [];

                    if (!empty($selectedStudents['Section'])) {
                        foreach ($selectedStudents['Section'] as $vv) {
                            if (!empty($vv['selected_id'])) {
                                $selectedStudentsId[] = $vv['student_id'];
                            }
                        }
                    }

                    if (empty($selectedStudentsId)) {
                        $selectedStudentsId[] = $selectedStudents['Section']['student_id'];
                    }

                    if ($this->Sections->isSectionMoveAllowed($this->request->getData('Section.previous_section_id'), $selectedStudentsId, $this->request->getData('Section.Selected_section_id'))) {
                        $new_section_name = $this->Sections->find()
                            ->select(['name'])
                            ->where(['Sections.id' => $this->request->getData('Section.Selected_section_id')])
                            ->first()
                            ->name;
                        $previous_section_name = $this->Sections->find()
                            ->select(['name'])
                            ->where(['Sections.id' => $this->request->getData('Section.previous_section_id')])
                            ->first()
                            ->name;
                        $this->Flash->success("The selected student is moved from $previous_section_name section to $new_section_name section.");
                        return $this->redirect(['action' => 'displaySections', $this->request->getData('Section.Selected_section_id')]);
                    } else {
                        $errors = $this->Sections->validationErrors;
                        if (isset($errors['move_not_allowed'])) {
                            $this->Flash->error($errors['move_not_allowed'][0]);
                        }
                        return $this->redirect(['action' => 'displaySections', $this->request->getData('Section.Selected_section_id')]);
                    }
                } else {
                    $new_section_name = $this->Sections->find()
                        ->select(['name'])
                        ->where(['Sections.id' => $this->request->getData('Section.Selected_section_id')])
                        ->first()
                        ->name;
                    $new_section_curriculum_name = $this->Sections->Students->Curriculums->find()
                        ->select(['curriculum_detail'])
                        ->where(['Curriculums.id' => $selected_section_curriculum])
                        ->first()
                        ->curriculum_detail ?? '';
                    $student_curriculum_name = $this->Sections->Students->Curriculums->find()
                        ->select(['curriculum_detail'])
                        ->where(['Curriculums.id' => $previous_section_curriculum])
                        ->first()
                        ->curriculum_detail ?? '';

                    if ($this->role_id == ROLE_DEPARTMENT) {
                        $this->Flash->error("The selected student will not be moved to $new_section_name section. The student attached curriculum \"$student_curriculum_name\" is different from $new_section_name section attached curriculum \"$new_section_curriculum_name\".");
                    } elseif ($this->role_id == ROLE_COLLEGE) {
                        $this->Flash->error("The selected student will not be moved to $new_section_name section. $college_selected_section sections academic year is different from $new_section_name section academic year, $college_previous_section_selected.");
                    }
                    return $this->redirect(['action' => 'displaySections', $this->request->getData('Section.Selected_section_id')]);
                }
            } else {
                $new_section_name = $this->Sections->find()
                    ->select(['name'])
                    ->where(['Sections.id' => $this->request->getData('Section.Selected_section_id')])
                    ->first()
                    ->name;

                if ($this->role_id == ROLE_DEPARTMENT) {
                    $this->Flash->error("The selected student will not be moved to $new_section_name section. The target section is empty.");
                } elseif ($this->role_id == ROLE_COLLEGE) {
                    $this->Flash->error("The selected student will not be moved to $new_section_name. The target section academic year is different.");
                }

                return $this->redirect(['action' => 'displaySections', $this->request->getData('Section.Selected_section_id')]);
            }
        }
    }


    public function addStudentToSection(int $student_id)
    {
        $this->viewBuilder()->setLayout('ajax');

        $is_student_dismissed = 0;
        $is_student_readmitted = 0;
        $sectionOrganized = [];
        $prefreshStudent = 0;
        $studentNeedsSectionAssignment = 0;
        $currentYearLevelID = null;
        $yearLevelQueryOperator = '>=';
        $last_student_status = [];
        $studentMustHaveCurriculum = 1;
        $curriculumYearLevels = [];
        $statusGeneratedForLastRegistration = 0;

        $lastRegisteredYearLevelID = '';
        $lastRegisteredAcademicYear = '';
        $lastRegisteredSemester = '';

        $lastReadmittedAcademicYear = '';
        $lastReadmittedSemester = '';
        $lastReadmittedDate = '';
        $possibleAcademicYears = [];
        $student_attached_curriculum_name = '';
        $student_have_invalid_grade = 0;
        $lastRegisteredYearLevelName = '';

        $checkOnlyRegisteredPassFailGradeType = null;

        $curr_academic_year = $this->AcademicYear->currentAcademicYear();

        $student_detail = $this->Sections->Students->find()
            ->where(['Students.id' => $student_id])
            ->contain([
                'AcceptedStudents' => ['fields' => ['id', 'studentnumber', 'academicyear']],
                'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name', 'allow_year_based_curriculums']],
                'Sections' => [
                    'sort' => [
                        'Sections.academicyear' => 'DESC',
                        'Sections.year_level_id' => 'ASC',
                        'Sections.id' => 'ASC',
                        'Sections.name' => 'ASC'
                    ],
                    'YearLevels' => ['fields' => ['id', 'name']],
                ],
                'CourseRegistrations' => function ($q) {
                    return $q
                        ->select([
                            'id',
                            'year_level_id',
                            'student_id',
                            'section_id',
                            'semester',
                            'academic_year',
                            'published_course_id',
                            'created'
                        ])
                        ->order([
                            'CourseRegistrations.academic_year' => 'DESC',
                            'CourseRegistrations.semester' => 'DESC',
                            'CourseRegistrations.id' => 'DESC'
                        ])
                        ->limit(1);
                },
                'CourseExemptions' => function ($q) {
                    return $q
                        ->where(['CourseExemptions.registrar_confirm_deny' => 1])
                        ->select(['id','student_id', 'taken_course_title', 'request_date'])
                        ->limit(1);
                },
                'Readmissions' => function ($q) {
                    return $q
                        ->where(['Readmissions.registrar_approval' => 1,
                            'Readmissions.academic_commision_approval' => 1,])
                        ->select( ['student_id', 'academic_year', 'semester', 'registrar_approval_date', 'modified'])
                        ->limit(1);
                },
            ])
            ->select([
                'Students.id',
                'Students.studentnumber',
                'Students.first_name', // Add for full_name
                'Students.middle_name',
                'Students.last_name',
                'Students.curriculum_id',
                'Students.department_id',
                'Students.college_id',
                'Students.program_id',
                'Students.program_type_id',
                'Students.gender',
                'Students.graduated',
                'Students.academicyear',
                'Students.admissionyear',
            ])->enableAutoFields(true) // Ensures virtual fields are included
            ->first();

        // debug($student_detail);

        $program_types_to_look = $this->getEquivalentProgramTypes($student_detail->program_type_id);
        // debug($program_types_to_look);

        if (is_null($student_detail->department_id) && ($student_detail->program_id == PROGRAM_UNDERGRADUATE
                || $student_detail->program_id == PROGRAM_REMEDIAL)) {
            $prefreshStudent = 1;
            $studentMustHaveCurriculum = 0;
        } else {
            if (!is_null($student_detail->curriculum_id) && $student_detail->curriculum_id != 0) {
                $studentMustHaveCurriculum = 0;
            }
        }

        if (!empty($student_detail->readmissions[0])) {
            $lastReadmittedAcademicYear = $student_detail->readmissions[0]->academic_year;
            $lastReadmittedSemester = $student_detail->readmissions[0]->semester;
            $lastReadmittedDate = $student_detail->readmissions[0]->registrar_approval_date;
        }

        $studentStatusPatternsTable = TableRegistry::getTableLocator()->get('StudentStatusPatterns');
        $isLastSemesterInCurriculum = $studentStatusPatternsTable->isLastSemesterInCurriculum($student_id);

        $error_message = $this->Sections->checkAllRegisteredAddedCoursesAreGraded($student_id, null, 0, '', 0,0,0);


        $msg = '';

        if (!empty($error_message) && is_array($error_message) && isset($error_message['disqualification'])) {
            $msg = '<ol>';
            foreach ($error_message['disqualification'] as $error_msg) {
                $msg .= '<li>' . h($error_msg) . '</li>';
            }
            $msg .= '</ol>';
        } else {
            $msg = '<p>No disqualification errors found.</p>';
        }

        if (!empty($student_detail->course_registrations[0]->year_level_id)) {
            $lastRegisteredYearLevelName = $this->Sections->YearLevels->find()
                ->select(['name'])
                ->where(['YearLevels.id' => $student_detail->course_registrations[0]->year_level_id])
                ->first()
                ->name ?? '';
        } elseif (isset($student_detail->course_registrations[0]->year_level_id)) {
            $lastRegisteredYearLevelName = 'Pre/1st';
        }

        $statusGeneratedForLastRegistration = 1;

        if (empty($student_detail->sections)) {
            $studentNeedsSectionAssignment = 1;
            $statusGeneratedForLastRegistration = 1;
            $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
            $possibleAcademicYears = $studentExamStatusTable->getAcademicYearRange($student_detail->accepted_student->academicyear, $curr_academic_year);
        } elseif (empty($student_detail->course_registrations)) {
            $statusGeneratedForLastRegistration = 1;
        } else {
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $student_section_exam_status = $studentsTable->getStudentSection($student_id, null, null);
            // debug($student_section_exam_status);

            if (!empty($student_section_exam_status['StudentExamStatuses'])) {
                $last_student_status['StudentExamStatus'] = $student_section_exam_status['StudentExamStatuses'];
                // debug($last_student_status);

                if (isset($student_section_exam_status['StudentExamStatus']['academic_status_id']) && is_numeric($student_section_exam_status['StudentExamStatus']['academic_status_id']) && $student_section_exam_status['StudentExamStatus']['academic_status_id'] != DISMISSED_ACADEMIC_STATUS_ID) {
                    $studentNeedsSectionAssignment = 1;
                    $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatus');
                    $possibleAcademicYears = $studentExamStatusTable->getAcademicYearRange(
                        !empty($student_detail->course_registrations[0]->academic_year) ? $student_detail->course_registrations[0]->academic_year : $student_detail->accepted_student->academicyear,
                        $curr_academic_year
                    );

                    $generalSettingTable = TableRegistry::getTableLocator()->get('GeneralSettings');
                    $generalSetting = $generalSettingTable->getAllGeneralSettingsByStudentByProgramIdOrBySectionId($student_id);


                    $lastRegisteredAcademicYear = $student_detail->course_registrations[0]->academic_year;
                    $lastRegisteredSemester = $student_detail->course_registrations[0]->semester;

                    if (!$prefreshStudent) {
                        $lastRegisteredYearLevelID = $student_detail->course_registrations[0]->year_level_id;
                    }

                    $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatus');
                    $alreadyGeneratedStatus = $studentExamStatusTable->find()
                        ->where([
                            'StudentExamStatus.student_id' => $student_id,
                            'StudentExamStatus.academic_year' => $student_detail->course_registrations[0]->academic_year,
                            'StudentExamStatus.semester' => $student_detail->course_registrations[0]->semester,
                        ])
                        ->contain(['AcademicStatuses' => ['fields' => ['id', 'name', 'computable']]])
                        ->first();

                    if (empty($alreadyGeneratedStatus) && $student_detail->program_type_id == PROGRAM_TYPE_REGULAR) {
                        $statusGeneratedForLastRegistration = 0;

                        $student_course_drop_count = $this->Sections->Students->CourseDrops->find()
                            ->where([
                                'CourseDrops.student_id' => $student_id,
                                'CourseDrops.academic_year' => $student_detail->course_registrations[0]->academic_year,
                                'CourseDrops.semester' => $student_detail->course_registrations[0]->semester,
                                'CourseDrops.registrar_confirmation' => 1,
                            ])
                            ->count();

                        // debug($student_course_drop_count);

                        if ($student_course_drop_count) {
                            $student_course_registration_count = $this->Sections->Students->CourseRegistrations->find()
                                ->where([
                                    'CourseRegistrations.student_id' => $student_id,
                                    'CourseRegistrations.academic_year' => $student_detail->course_registrations[0]->academic_year,
                                    'CourseRegistrations.semester' => $student_detail->course_registrations[0]->semester,
                                ])
                                ->count();

                            // debug($student_course_drop_count);

                            if ($student_course_drop_count == $student_course_registration_count) {
                                $statusGeneratedForLastRegistration = 1;
                                $studentNeedsSectionAssignment = 1;
                            }
                        }
                    }

                    if (!empty($error_message) && is_numeric($error_message) && $error_message == 1) {
                        $studentNeedsSectionAssignment = 1;
                        if ($student_detail->program_type_id != PROGRAM_TYPE_REGULAR) {
                            $statusGeneratedForLastRegistration = 1;
                        }
                    } elseif (!empty($msg) || (!empty($error_message) && is_array($error_message))) {
                        $student_have_invalid_grade = 1;
                    } else {
                        $student_have_invalid_grade = 1;
                    }

                    if ($student_detail->program_id == PROGRAM_UNDERGRADUATE && $isLastSemesterInCurriculum && !empty($msg) && is_array($msg) && count($msg) == 1) {
                        $studentNeedsSectionAssignment = 1;
                        $student_have_invalid_grade = 0; // Exit Exam
                    }

                    $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatus');
                    $checkOnlyRegisteredPassFailGradeType = $studentExamStatusTable->onlyRegisteredPassFailGradeType($student_id, $student_detail->course_registrations[0]->academic_year, $student_detail->course_registrations[0]->semester);

                    if ($checkOnlyRegisteredPassFailGradeType) {
                        $studentNeedsSectionAssignment = 1;
                        $statusGeneratedForLastRegistration = 1;
                    }

                    if ($generalSetting['GeneralSetting']['semesterCountForAcademicYear'] == 3) {
                        if (in_array($student_section_exam_status['StudentExamStatus']['semester'], ['I', 'II'])) {
                            if (!$prefreshStudent) {
                                // debug($student_detail->sections[0]->year_level_id);
                                $currentYearLevelID = $student_detail->sections[0]->year_level_id;
                                $yearLevelQueryOperator = '=';

                                if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                    $currentYearLevelID = $lastRegisteredYearLevelID;
                                }
                            }
                        } elseif ($student_section_exam_status['StudentExamStatus']['semester'] == 'III') {
                            if (!$prefreshStudent) {
                                // debug($student_detail->sections[0]->year_level_id);
                                $currentYearLevelID = $student_detail->sections[0]->year_level_id;
                                $yearLevelQueryOperator = '>';

                                if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                    $currentYearLevelID = $lastRegisteredYearLevelID;
                                }
                            }
                        }
                    } elseif ($generalSetting['GeneralSetting']['semesterCountForAcademicYear'] == 2) {
                        if ($student_section_exam_status['StudentExamStatus']['semester'] == 'I') {
                            if (!$prefreshStudent) {
                                // debug($student_detail->sections[0]->year_level_id);
                                $currentYearLevelID = $student_detail->sections[0]->year_level_id;
                                $yearLevelQueryOperator = '=';

                                if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                    $currentYearLevelID = $lastRegisteredYearLevelID;
                                }
                            }
                        } elseif ($student_detail->course_registrations[0]->semester == 'II') {
                            if (!$prefreshStudent) {
                                // debug($student_detail->sections[0]->year_level_id);
                                $currentYearLevelID = $student_detail->sections[0]->year_level_id;
                                $yearLevelQueryOperator = '>';

                                if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                    $currentYearLevelID = $lastRegisteredYearLevelID;
                                }
                            }
                        }
                    } elseif ($generalSetting['GeneralSetting']['semesterCountForAcademicYear'] == 1) {
                        if (in_array($student_section_exam_status['StudentExamStatus']['semester'], ['I', 'II', 'III'])) {
                            if (!$prefreshStudent) {
                                // debug($student_detail->sections[0]->year_level_id);
                                $currentYearLevelID = $student_detail->sections[0]->year_level_id;
                                $yearLevelQueryOperator = '>=';

                                if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                    $currentYearLevelID = $lastRegisteredYearLevelID;
                                }
                            }
                        }
                    }
                } elseif (!empty($student_section_exam_status['StudentExamStatus']['academic_year']) && (is_null($student_section_exam_status['StudentExamStatus']['academic_status_id']) || empty($student_section_exam_status['StudentExamStatus']['academic_status_id']))) {
                    $studentNeedsSectionAssignment = 0;

                    $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatus');
                    $possibleAcademicYears = $studentExamStatusTable->getAcademicYearRange(
                        !empty($student_detail->course_registrations[0]->academic_year) ? $student_detail->course_registrations[0]->academic_year : $student_detail->accepted_student->academicyear,
                        $curr_academic_year
                    );

                    $generalSettingTable = TableRegistry::getTableLocator()->get('GeneralSettings');
                    $generalSetting = $generalSettingTable->getAllGeneralSettingsByStudentByProgramIdOrBySectionId($student_id);
                    $lastRegisteredAcademicYear = $student_detail->course_registrations[0]->academic_year;
                    $lastRegisteredSemester = $student_detail->course_registrations[0]->semester;

                    if (!$prefreshStudent) {
                        $lastRegisteredYearLevelID = $student_detail->course_registrations[0]->year_level_id;
                    }

                    $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatus');
                    $alreadyGeneratedStatus = $studentExamStatusTable->find()
                        ->where([
                            'StudentExamStatus.student_id' => $student_id,
                            'StudentExamStatus.academic_year' => $student_detail->course_registrations[0]->academic_year,
                            'StudentExamStatus.semester' => $student_detail->course_registrations[0]->semester,
                        ])
                        ->contain(['AcademicStatuses' => ['fields' => ['id', 'name', 'computable']]])
                        ->first();

                    if (empty($alreadyGeneratedStatus) && $student_detail->program_type_id == PROGRAM_TYPE_REGULAR) {
                        $statusGeneratedForLastRegistration = 0;

                        $student_course_drop_count = $this->Sections->Students->CourseDrops->find()
                            ->where([
                                'CourseDrops.student_id' => $student_id,
                                'CourseDrops.academic_year' => $student_detail->course_registrations[0]->academic_year,
                                'CourseDrops.semester' => $student_detail->course_registrations[0]->semester,
                                'CourseDrops.registrar_confirmation' => 1,
                            ])
                            ->count();

                        if ($student_course_drop_count) {
                            $student_course_registration_count = $this->Sections->Students->CourseRegistrations->find()
                                ->where([
                                    'CourseRegistrations.student_id' => $student_id,
                                    'CourseRegistrations.academic_year' => $student_detail->course_registrations[0]->academic_year,
                                    'CourseRegistrations.semester' => $student_detail->course_registrations[0]->semester,
                                ])
                                ->count();

                            // debug($student_course_drop_count);

                            if ($student_course_drop_count == $student_course_registration_count) {
                                $statusGeneratedForLastRegistration = 1;
                                $studentNeedsSectionAssignment = 1;
                            }
                        }
                    }

                    if (!empty($error_message) && is_numeric($error_message) && $error_message == 1) {
                        $studentNeedsSectionAssignment = 1;
                        if ($student_detail->program_type_id != PROGRAM_TYPE_REGULAR) {
                            $statusGeneratedForLastRegistration = 1;
                        }
                    } elseif (!empty($msg) || (!empty($error_message) && is_array($error_message))) {
                        $student_have_invalid_grade = 1;
                    } else {
                        $student_have_invalid_grade = 1;
                    }

                    if ($student_detail->program_id == PROGRAM_UNDERGRADUATE && $isLastSemesterInCurriculum && !empty($msg) && is_array($msg) && count($msg) == 1) {
                        $studentNeedsSectionAssignment = 1;
                        // $student_have_invalid_grade = 0; // Exit Exam
                    }

                    $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatus');
                    $checkOnlyRegisteredPassFailGradeType = $studentExamStatusTable->onlyRegisteredPassFailGradeType($student_id, $student_detail->course_registrations[0]->academic_year, $student_detail->course_registrations[0]->semester);

                    if ($checkOnlyRegisteredPassFailGradeType) {
                        $studentNeedsSectionAssignment = 1;
                        $statusGeneratedForLastRegistration = 1;
                    }

                    if ($generalSetting['GeneralSetting']['semesterCountForAcademicYear'] == 3) {
                        if (in_array($student_section_exam_status['StudentExamStatus']['semester'], ['I', 'II'])) {
                            if (!$prefreshStudent) {
                                // debug($student_detail->sections[0]->year_level_id);
                                $currentYearLevelID = $student_detail->sections[0]->year_level_id;
                                $yearLevelQueryOperator = '=';

                                if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                    $currentYearLevelID = $lastRegisteredYearLevelID;
                                }
                            }
                        } elseif ($student_section_exam_status['StudentExamStatus']['semester'] == 'III') {
                            if (!$prefreshStudent) {
                                // debug($student_detail->sections[0]->year_level_id);
                                $currentYearLevelID = $student_detail->sections[0]->year_level_id;
                                $yearLevelQueryOperator = '>';

                                if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                    $currentYearLevelID = $lastRegisteredYearLevelID;
                                }
                            }
                        }
                    } elseif ($generalSetting['GeneralSetting']['semesterCountForAcademicYear'] == 2) {
                        if ($student_section_exam_status['StudentExamStatus']['semester'] == 'I') {
                            if (!$prefreshStudent) {
                                // debug($student_detail->sections[0]->year_level_id);
                                $currentYearLevelID = $student_detail->sections[0]->year_level_id;
                                $yearLevelQueryOperator = '=';

                                if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                    $currentYearLevelID = $lastRegisteredYearLevelID;
                                }
                            }
                        } elseif ($student_detail->course_registrations[0]->semester == 'II') {
                            if (!$prefreshStudent) {
                                // debug($student_detail->sections[0]->year_level_id);
                                $currentYearLevelID = $student_detail->sections[0]->year_level_id;
                                $yearLevelQueryOperator = '>';

                                if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                    $currentYearLevelID = $lastRegisteredYearLevelID;
                                }
                            }
                        }
                    } elseif ($generalSetting['GeneralSetting']['semesterCountForAcademicYear'] == 1) {
                        if (in_array($student_section_exam_status['StudentExamStatus']['semester'], ['I', 'II', 'III'])) {
                            if (!$prefreshStudent) {
                                // debug($student_detail->sections[0]->year_level_id);
                                $currentYearLevelID = $student_detail->sections[0]->year_level_id;
                                $yearLevelQueryOperator = '>=';

                                if ($lastRegisteredYearLevelID > $currentYearLevelID) {
                                    $currentYearLevelID = $lastRegisteredYearLevelID;
                                }
                            }
                        }
                    }
                } else {
                    $is_student_dismissed = 1;
                    $statusGeneratedForLastRegistration = 1;
                    $studentNeedsSectionAssignment = 1;

                    $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatus');
                    $possibleReadmissionYears = $studentExamStatusTable->getAcademicYearRange($student_section_exam_status['StudentExamStatus']['academic_year'], $curr_academic_year);

                    $readmitted = $this->Sections->Students->Readmissions->find()
                        ->where([
                            'Readmissions.student_id' => $student_id,
                            'Readmissions.registrar_approval' => 1,
                            'Readmissions.academic_commision_approval' => 1,
                            'Readmissions.academic_year IN' => $possibleReadmissionYears,
                        ])
                        ->order(['Readmissions.academic_year' => 'DESC', 'Readmissions.semester' => 'DESC', 'Readmissions.modified' => 'DESC'])
                        ->first();

                    // debug($student_section_exam_status['StudentExamStatus']);

                    if (!empty($readmitted)) {
                        // debug($readmitted);
                        $lastReadmittedAcademicYear = $readmitted->academic_year;
                        $lastReadmittedSemester = $readmitted->semester;
                        $lastReadmittedDate = $readmitted->registrar_approval_date;

                        // debug($lastReadmittedAcademicYear);

                        $is_student_readmitted = 1;
                        $possibleAcademicYears = $studentExamStatusTable->getAcademicYearRange($lastReadmittedAcademicYear, $curr_academic_year);
                        $studentNeedsSectionAssignment = 1;
                    }
                }
            }
        }

        $acYrStart = !empty($lastReadmittedAcademicYear) ? str_replace('-', '/', $lastReadmittedAcademicYear) : (!empty($lastRegisteredAcademicYear) ? $lastRegisteredAcademicYear : $student_detail->academicyear);

        if (!empty($acYrStart)) {
            $acYrStart = str_replace('-', '/', $acYrStart);
            // debug($acYrStart);
            $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
            $possibleAcademicYears = $studentExamStatusTable->getAcademicYearRange($acYrStart, $curr_academic_year);
        }

        if (empty($possibleAcademicYears)) {
            $possibleAcademicYears[$curr_academic_year] = $curr_academic_year;
        }


        if (!$prefreshStudent || $studentMustHaveCurriculum) {

            if (is_numeric($student_detail->curriculum_id) && $student_detail->curriculum_id > 0) {
                $courseYearLevels = $this->Sections->Curriculums->Courses->find('list')
                    ->where(['Courses.curriculum_id' => $student_detail->curriculum_id])
                    ->select(['Courses.year_level_id', 'Courses.year_level_id'])
                    ->group(['Courses.year_level_id'])
                    ->order(['Courses.year_level_id' => 'DESC'])
                    ->toArray();

                if (!empty($courseYearLevels)) {
                    $curriculumYearLevels = array_keys($courseYearLevels);
                }

                $student_attached_curriculum = $this->Sections->Curriculums->find()
                    ->where(['Curriculums.id' => $student_detail->curriculum_id])
                    ->first();

                if (!empty($student_attached_curriculum)) {
                    // debug($student_attached_curriculum->curriculum_detail);
                    $student_attached_curriculum_name = $student_attached_curriculum->curriculum_detail;
                }
            }

            if (!empty($curriculumYearLevels) && !empty($student_detail->sections[0]) &&
                (!is_numeric($student_detail->sections[0]->year_level_id) ||
                    empty($student_detail->sections[0]->year_level_id))) {
                asort($curriculumYearLevels);
                $curriculumYearLevelsSortedASC = array_values($curriculumYearLevels);
                // debug($curriculumYearLevelsSortedASC);

                if (isset($curriculumYearLevelsSortedASC[0])) {
                    $currentYearLevelID = $curriculumYearLevelsSortedASC[0];
                }

                if ($student_detail->program_id == PROGRAM_UNDERGRADUATE) {
                    $all_pre_freshman_remedial_college_ids = Configure::read('all_pre_freshman_remedial_college_ids');
                    $program_types_available_pre_freshman = Configure::read('program_types_available_for_registrar_college_level_permissions');
                    // debug($program_types_available_pre_freshman);

                    if (!empty($all_pre_freshman_remedial_college_ids) && !empty($student_detail->sections[0]->college_id) && empty($student_detail->sections[0]->department_id) && in_array($student_detail->sections[0]->college_id, $all_pre_freshman_remedial_college_ids)) {
                        // debug($all_pre_freshman_remedial_college_ids);
                        if (!empty($curriculumYearLevelsSortedASC[1])) {
                            $yearLevelQueryOperator = '=';
                            $currentYearLevelID = $curriculumYearLevelsSortedASC[1];
                        }
                    } elseif (!empty($student_detail->sections[0]->college_id) && empty($student_detail->sections[0]->department_id) && in_array($student_detail->program_type_id, $program_types_available_pre_freshman)) {
                        if (!empty($curriculumYearLevelsSortedASC[1])) {
                            $yearLevelQueryOperator = '=';
                            $currentYearLevelID = $curriculumYearLevelsSortedASC[1];
                        }
                    } elseif (!empty($student_detail->sections[0]->college_id) && empty($student_detail->sections[0]->department_id) && $student_detail->program_type_id == PROGRAM_TYPE_REGULAR) {
                        if (!empty($curriculumYearLevelsSortedASC[1])) {
                            $yearLevelQueryOperator = '=';
                            $currentYearLevelID = $curriculumYearLevelsSortedASC[1];
                        }
                    } else {
                        $yearLevelQueryOperator = '=';
                    }
                }
            } elseif (!empty($student_detail->course_registrations[0]->year_level_id)) {
                $studentNeedsSectionAssignment = 1;
                $currentYearLevelID = $student_detail->course_registrations[0]->year_level_id;
                $yearLevelQueryOperator = '>=';

                if (!$prefreshStudent && empty($msg) && in_array($lastRegisteredSemester, ['II', 'III']) && !empty($student_section_exam_status['StudentExamStatus']['academic_status_id']) && $student_section_exam_status['StudentExamStatus']['academic_status_id'] != DISMISSED_ACADEMIC_STATUS_ID) {
                    $yearLevelQueryOperator = '>';
                } elseif (!$prefreshStudent && $student_detail->program_type_id == PROGRAM_TYPE_REGULAR && $lastRegisteredSemester == 'I' && !empty($student_section_exam_status['StudentExamStatus']['academic_status_id']) && $student_section_exam_status['StudentExamStatus']['academic_status_id'] != DISMISSED_ACADEMIC_STATUS_ID) {
                    $yearLevelQueryOperator = '=';

                    if ($student_detail->college_id == HEALTH_SCIENCES_COLLEGE_ID || (!empty($student_detail->department->allow_year_based_curriculums) && $student_detail->department->allow_year_based_curriculums)) {
                        // debug($student_detail->department);
                        $yearLevelQueryOperator = '>';
                    }
                }

                if ($student_detail->program_type_id == PROGRAM_TYPE_SUMMER) {
                    $yearLevelQueryOperator = '>';
                }

                if ($isLastSemesterInCurriculum || !empty($msg)) {
                    $yearLevelQueryOperator = '=';
                }
            } elseif (!empty($curriculumYearLevels) && empty($student_detail->course_registrations)) {
                $studentNeedsSectionAssignment = 1;
                $curriculumYearLevelsASC1 = $curriculumYearLevels;
                asort($curriculumYearLevelsASC1);
                $curriculumYearLevelsASC1 = array_values($curriculumYearLevelsASC1);
                $currentYearLevelID = $curriculumYearLevelsASC1[0];
                $yearLevelQueryOperator = '=';
            }

            if (!empty($curriculumYearLevels) && !empty($student_detail->course_exemptions[0]) && empty($student_detail->course_registrations)) {
                $studentNeedsSectionAssignment = 1;
                $curriculumYearLevelsASC = $curriculumYearLevels;
                asort($curriculumYearLevelsASC);
                $curriculumYearLevelsASC = array_values($curriculumYearLevelsASC);
                $currentYearLevelID = $curriculumYearLevelsASC[0];
                $yearLevelQueryOperator = '>';
            }
        }

        if (!empty($acYrStart) && count($possibleAcademicYears) > 1) {
            if ($yearLevelQueryOperator === '>' || ($yearLevelQueryOperator === '=' && $isLastSemesterInCurriculum)) {
                $possibleAcademicYearsASC = $possibleAcademicYears;
                asort($possibleAcademicYearsASC);
                array_shift($possibleAcademicYearsASC);
                $possibleAcademicYears = $possibleAcademicYearsASC;
                $acYrStart = array_values($possibleAcademicYears)[0];
            }
        }
        $sectionOrganized = [];
        $nextYearLevelName = '';

        if (!$prefreshStudent && !empty($student_detail->department_id) && is_numeric($student_detail->department_id) && $student_detail->department_id > 0) {
            $nextYearLevelID = $currentYearLevelID;

            if ($yearLevelQueryOperator === '=') {
                $yearLevelsProfile = $yearLevels = $this->Sections->YearLevels->find('list')
                    ->where(['YearLevels.id' => $currentYearLevelID, 'YearLevels.department_id' => $student_detail->department_id])
                    ->select(['YearLevels.name', 'YearLevels.name'])
                    ->toArray();
                $nextYearLevelID = $this->Sections->YearLevels->find()
                    ->select(['YearLevels.id'])
                    ->where(['YearLevels.id' => $currentYearLevelID, 'YearLevels.department_id' => $student_detail->department_id])
                    ->first()
                    ->id;
            } else {
                if (!empty($curriculumYearLevels[0])) {
                    $curriculumYearLevelsSortedASC = $curriculumYearLevels;
                    asort($curriculumYearLevelsSortedASC);
                    $curriculumYearLevelsSortedASC = array_values($curriculumYearLevelsSortedASC);
                    // debug($curriculumYearLevelsSortedASC);
                    $nextYearLevel = $this->getNextYearLevelId($curriculumYearLevelsSortedASC, $currentYearLevelID);

                    if (!empty($nextYearLevel)) {
                        $yearLevelsProfile = $yearLevels = $this->Sections->YearLevels->find('list')
                            ->where([
                                "YearLevels.id $yearLevelQueryOperator" => $currentYearLevelID,
                                'YearLevels.id <=' => $nextYearLevel,
                                'YearLevels.department_id' => $student_detail->department_id,
                            ])
                            ->select(['YearLevels.name', 'YearLevels.name'])
                            ->order(['YearLevels.id' => 'ASC'])
                            ->toArray();
                        $nextYearLevelID = $this->Sections->YearLevels->find('list')
                            ->where([
                                "YearLevels.id $yearLevelQueryOperator" => $currentYearLevelID,
                                'YearLevels.id <=' => $nextYearLevel,
                                'YearLevels.department_id' => $student_detail->department_id,
                            ])
                            ->select(['YearLevels.id', 'YearLevels.id'])
                            ->order(['YearLevels.id' => 'ASC'])
                            ->toArray();
                    } else {
                        $yearLevelsProfile = $yearLevels = $this->Sections->YearLevels->find('list')
                            ->where([
                                "YearLevels.id $yearLevelQueryOperator" => $currentYearLevelID,
                                'YearLevels.id <=' => $curriculumYearLevels[0],
                                'YearLevels.department_id' => $student_detail->department_id,
                            ])
                            ->select(['YearLevels.name', 'YearLevels.name'])
                            ->order(['YearLevels.id' => 'ASC'])
                            ->toArray();
                        $nextYearLevelID = $this->Sections->YearLevels->find('list')
                            ->where([
                                "YearLevels.id $yearLevelQueryOperator" => $currentYearLevelID,
                                'YearLevels.id <=' => $curriculumYearLevels[0],
                                'YearLevels.department_id' => $student_detail->department_id,
                            ])
                            ->select(['YearLevels.id', 'YearLevels.id'])
                            ->order(['YearLevels.id' => 'ASC'])
                            ->toArray();
                    }
                    if (!empty($nextYearLevelID)) {
                        $nextYearLevelID = array_values($nextYearLevelID)[0];
                    } else {
                        $nextYearLevelID = $currentYearLevelID;
                    }
                } else {
                    $yearLevelsProfile = $yearLevels = $this->Sections->YearLevels->find('list')
                        ->where([
                            "YearLevels.id $yearLevelQueryOperator" => $currentYearLevelID,
                            'YearLevels.department_id' => $student_detail->department_id,
                        ])
                        ->select(['YearLevels.name', 'YearLevels.name'])
                        ->order(['YearLevels.id' => 'ASC'])
                        ->toArray();
                    $nextYearLevelID = $this->Sections->YearLevels->find('list')
                        ->where([
                            "YearLevels.id $yearLevelQueryOperator" => $currentYearLevelID,
                            'YearLevels.department_id' => $student_detail->department_id,
                        ])
                        ->select(['YearLevels.id', 'YearLevels.id'])
                        ->order(['YearLevels.id' => 'ASC'])
                        ->toArray();
                    if (!empty($nextYearLevelID)) {
                        $nextYearLevelID = array_values($nextYearLevelID)[0];
                    } else {
                        $nextYearLevelID = $currentYearLevelID;
                    }
                }
            }

            // debug($nextYearLevelID);
            $nextYearLevelName = $this->Sections->YearLevels->find()
                ->select(['name'])
                ->where(['id' => $nextYearLevelID])
                ->first()
                ->name ?? '';

            if (count($yearLevelsProfile) == 1) {
                $currentYearLevelIDName = array_values($yearLevelsProfile)[0] . ' year';
            } else {
                $currentYearLevelIDName = implode(' year, ', array_values($yearLevelsProfile)) . ' year';
            }

            $sectionOrganized = $this->Sections->find()
                ->where([
                    'Sections.academicyear IN' => $possibleAcademicYears,
                    'Sections.program_id' => $student_detail->program_id,
                    'Sections.program_type_id IN' => $program_types_to_look,
                    'Sections.department_id' => $student_detail->department_id,
                    'Sections.year_level_id' => $nextYearLevelID,
                    'Sections.curriculum_id' => $student_detail->curriculum_id,
                    'Sections.archive' => 0,
                ])
                ->contain([
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                ])
                ->order(['Sections.academicyear' => 'ASC', 'Sections.year_level_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC'])
                ->toArray();
        } else {
            $yearLevelsProfile[0] = $yearLevels[0] = 'Pre/Freshman/Remedial';
            $currentYearLevelIDName = 'Pre/Freshman/Remedial';

            $sectionOrganized = $this->Sections->find()
                ->where([
                    'Sections.academicyear IN' => $possibleAcademicYears,
                    'Sections.program_id' => $student_detail->program_id,
                    'Sections.program_type_id IN' => $program_types_to_look,
                    'Sections.department_id IS' => null,
                    'Sections.college_id' => $student_detail->college_id,
                    'Sections.archive' => 0,
                ])
                ->contain(['YearLevels'])
                ->order(['Sections.academicyear' => 'ASC', 'Sections.year_level_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC'])
                ->toArray();
        }


        if (empty($sectionOrganized) && ($this->role_id == ROLE_COLLEGE || $this->role_id == ROLE_DEPARTMENT)) {
            $displaySectionsSearchFilter = [
                'Section' => [
                    'academicyear' => end($possibleAcademicYears),
                    'program_id' => $student_detail->program_id,
                    'program_type_id' => $student_detail->program_type_id,
                ],
            ];

            if (!empty($nextYearLevelID) && is_array($nextYearLevelID)) {
                $nextYearLevelID = array_values($nextYearLevelID)[0];
            }

            // debug($nextYearLevelID);

            if (!empty($currentYearLevelID)) {
                $displaySectionsSearchFilter['Section']['year_level_id'] = !empty($nextYearLevelID) ? $nextYearLevelID : $currentYearLevelID;
            }

            // debug($displaySectionsSearchFilter);

            if (!empty($displaySectionsSearchFilter['Section']['academicyear'])) {
                $this->request->getSession()->write('search_sections', $displaySectionsSearchFilter['Section']);
            }
        }

        $this->set(compact(
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
            'nextYearLevelName',
            'acYrStart'
        ));
    }

    private function getNextYearLevelId(array $sortedYearLevelIDs = [], string $currentYearLevelID = ''): ?string
    {
        if (!empty($sortedYearLevelIDs) && !empty($currentYearLevelID)) {
            $sortedYearLevelIDs = array_values($sortedYearLevelIDs);
            $index = array_search($currentYearLevelID, $sortedYearLevelIDs);

            if ($index !== false && $index + 1 < count($sortedYearLevelIDs)) {
                return $sortedYearLevelIDs[$index + 1];
            }
        }

        return null;
    }



    public function addStudentSection(?int $section_id = null)
    {
        $this->viewBuilder()->setLayout('ajax');

        $section_detail = $this->Sections->find()
            ->where(['Sections.id' => $section_id])
            ->contain([
                'YearLevels' => ['fields' => ['id', 'name']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name', 'equivalent_to_id']],
                'Departments' => ['fields' => ['id', 'name', 'college_id', 'type']],
                'Colleges' => ['fields' => ['id', 'name', 'shortname', 'campus_id', 'type', 'stream']],
                'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                'Students' => [
                    'fields' => [
                        'Students.id',
                        'Students.studentnumber',
                        'Students.curriculum_id',
                        'Students.gender',
                        'Students.graduated',
                        'Students.academicyear',
                        'full_name' => 'TRIM(CONCAT(COALESCE(Students.first_name, ""), " ", COALESCE(Students.middle_name, ""), " ", COALESCE(Students.last_name, "")))'
                    ],
                    'conditions' => ['Students.graduated' => 0],
                    'sort' => [
                        'Students.academicyear' => 'DESC',
                        'Students.studentnumber' => 'ASC',
                        'Students.id' => 'ASC',
                        'full_name' => 'ASC'
                    ],
                    'limit' => 1
                ]
            ])
            ->disableHydration()
            ->first();

        if (!$section_detail) {
            throw new NotFoundException('Section not found for ID: ' . $section_id);
        }

        $isSectionEmpty = 1;
        $section_program_id = $section_detail['program_id'];
        $section_program_type_id = $section_detail['program_type_id'];
        $section_academic_year = $section_detail['academicyear'];
        $sectionsCurriculumID = 0;

        if (!empty($section_detail['curriculum']['id'])) {
            $sectionsCurriculumID = $section_detail['curriculum']['id'];
            if (!empty($section_detail['students'])) {
                $isSectionEmpty = 0;
            }
        } elseif (empty($section_detail['students'])) {
            // $isSectionEmpty = 1;
        } else {
            $sectionsCurriculumID = $this->Sections->sectionsCurriculumId($section_id);
        }

        $program_type_id = $this->Sections->getEquivalentProgramTypes($section_program_type_id);
        $program_type_id_sql = implode(',', array_map('intval', $program_type_id)); // Sanitize for SQL

        $admission_years = $this->AcademicYear->academicYearInArray(
            date('Y') - ACY_BACK_FOR_SECTION_LESS,
            !empty(explode('/', $section_detail['academicyear'])[0]) ? explode('/', $section_detail['academicyear'])[0] : date('Y')
        );
        $admission_years = array_keys($admission_years);
        $admission_years_sql = implode(',', array_map(function ($year) {
            return "'" . str_replace("'", "''", $year) . "'"; // Escape single quotes
        }, $admission_years));

        $student_list_ids = [];

        $connection = $this->Sections->getConnection();
        if ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {
            if ((is_array($sectionsCurriculumID) && $sectionsCurriculumID[0] == -2) || $isSectionEmpty || $sectionsCurriculumID == 0) {
                $query = $connection->newQuery()
                    ->select('id')
                    ->from('students')
                    ->where([
                        'id NOT IN' => function ($q) use ($connection) {
                            return $connection->newQuery()
                                ->select('student_id')
                                ->from('students_sections')
                                ->where(['archive' => 0])
                                ->group(['student_id', 'section_id']);
                        },
                        'program_id' => $section_program_id,
                        'program_type_id IN' => $program_type_id,
                        'curriculum_id IS NOT NULL',
                        'academicyear IN' => $admission_years,
                        'department_id' => $this->department_id,
                        'graduated' => 0
                    ]);
            } else {
                $query = $connection->newQuery()
                    ->select('id')
                    ->from('students')
                    ->where([
                        'id NOT IN' => function ($q) use ($connection) {
                            return $connection->newQuery()
                                ->select('student_id')
                                ->from('students_sections')
                                ->where(['archive' => 0])
                                ->group(['student_id', 'section_id']);
                        },
                        'program_id' => $section_program_id,
                        'program_type_id IN' => $program_type_id,
                        'curriculum_id' => $sectionsCurriculumID,
                        'department_id' => $this->department_id,
                        'graduated' => 0
                    ]);
            }

            $queryResult = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
            \Cake\Log\Log::debug('Raw Query Result: ' . var_export($queryResult, true), ['scope' => ['sections']]);

            if (!empty($queryResult)) {
                $student_list_ids = array_column($queryResult, 'id');
            }

            if (!empty($student_list_ids)) {
                if ((is_array($sectionsCurriculumID) && $sectionsCurriculumID[0] == -2) || $isSectionEmpty) {
                    $conditions = [
                        'Students.department_id' => $this->department_id,
                        'Students.id IN' => $student_list_ids,
                        'Students.program_id' => $section_program_id,
                        'Students.program_type_id IN' => $program_type_id,
                        'OR' => [
                            'Students.curriculum_id IS NOT' => null,
                            'Students.curriculum_id' => $sectionsCurriculumID,
                        ],
                        'Students.graduated' => 0,
                    ];
                } else {
                    $conditions = [
                        'Students.department_id' => $this->department_id,
                        'Students.id IN' => $student_list_ids,
                        'Students.program_id' => $section_program_id,
                        'Students.program_type_id IN' => $program_type_id,
                        'Students.curriculum_id' => $sectionsCurriculumID,
                        'Students.graduated' => 0,
                    ];
                }
            }

            $this->set(compact('sectionsCurriculumID'));
        } else {
            $sectionsCurriculumID = -1;
            $this->set(compact('sectionsCurriculumID'));

            $query = $connection->newQuery()
                ->select('id')
                ->from('students')
                ->where([
                    'id NOT IN' => function ($q) use ($connection) {
                        return $connection->newQuery()
                            ->select('student_id')
                            ->from('students_sections')
                            ->where(['archive' => 0])
                            ->group(['student_id', 'section_id']);
                    },
                    'program_id' => $section_program_id,
                    'program_type_id IN' => $program_type_id,
                    'academicyear IN' => $admission_years,
                    'college_id' => $this->college_id,
                    'department_id IS' => null,
                    'graduated' => 0
                ]);

            $queryResult = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

            if (!empty($queryResult)) {
                $student_list_ids = array_column($queryResult, 'id');
            }

            if (!empty($student_list_ids)) {
                $conditions = [
                    'Students.college_id' => $this->college_id,
                    'Students.department_id IS' => null,
                    'Students.program_id' => $section_program_id,
                    'Students.id IN' => $student_list_ids,
                    'Students.program_type_id IN' => $program_type_id,
                ];
            }
        }

        $students = [];
        if (!empty($conditions)) {
            $students = $this->Sections->Students->find()
                ->where($conditions)
                ->select([
                    'Students.id',
                    'Students.studentnumber',
                    'Students.gender',
                    'Students.graduated',
                    'full_name' => 'TRIM(CONCAT(COALESCE(Students.first_name, ""), " ", COALESCE(Students.middle_name, ""), " ", COALESCE(Students.last_name, "")))'
                ])
                ->contain([
                    'Sections' => [
                        'fields' => [
                            'Sections.id',
                            'Sections.name',
                            'Sections.academicyear',
                            'Sections.curriculum_id'
                        ],
                        'conditions' => [
                            'Sections.academicyear' => $section_academic_year,
                            'Sections.archive' => 1
                        ],
                        'sort' => [
                            'Sections.academicyear' => 'DESC',
                            'Sections.name' => 'ASC'
                        ],
                        'YearLevels' => ['fields' => ['id', 'name']],
                        'Departments' => ['fields' => ['id', 'name', 'college_id', 'type']],
                        'Colleges' => ['fields' => ['id', 'name', 'shortname', 'campus_id', 'type', 'stream']],
                        'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']]
                    ],
                    'Departments' => ['fields' => ['id', 'name', 'college_id', 'type']],
                    'Colleges' => ['fields' => ['id', 'name', 'shortname', 'campus_id', 'type', 'stream']]
                ])
                ->orderBy([
                    'Students.admissionyear' => 'ASC',
                    'full_name' => 'ASC',
                    'Students.program_type_id' => 'ASC'
                ])
                ->disableHydration()
                ->toArray();
        }

        $this->set(compact('section_id', 'students', 'section_detail'));
        $this->set('_serialize', ['section_id', 'students', 'section_detail']);
    }

    public function addStudentSectionUpdate()
    {
        if (!empty($this->request->getData())) {
            $new_section_detail = $this->Sections->find()
                ->where(['Sections.id' => $this->request->getData('Section.section_id')])
                ->contain(['YearLevels'])
                ->first();

            $student_full_name = $this->Sections->Students->find()
                ->select(['full_name'])
                ->where(['Students.id' => $this->request->getData('Section.Selected_student_id')])
                ->first()
                ->full_name;
            $section_name = $this->Sections->find()
                ->select(['name'])
                ->where(['Sections.id' => $this->request->getData('Section.section_id')])
                ->first()
                ->name;

            $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatus');
            $studentYearLevel = $studentExamStatusTable->studentYearAndSemesterLevel($this->request->getData('Section.Selected_student_id'));

            if (!empty($studentYearLevel) && $new_section_detail->year_level->name != $studentYearLevel['year']) {
                $this->Flash->error(
                    "$student_full_name is not added to $section_name Because the target section is {$new_section_detail->year_level->name} year while the student is {$studentYearLevel['year']} year.",
                    ['element' => 'error_with_link', 'params' => ['link_text' => 'this page', 'link_url' => ['action' => 'displaySections', $this->request->getData('Section.section_id')]]]
                );
                return $this->redirect(['action' => 'displaySections', $this->request->getData('Section.section_id')]);
            }

            if ($this->Sections->Students->StudentExamStatuses->getStudentExamStatus($this->request->getData('Section.Selected_student_id'))) {
                $section_curriculum = $this->Sections->getSectionCurriculum($this->request->getData('Section.section_id'));

                if (!empty($section_curriculum)) {
                    $student_curriculum = $this->Sections->Students->find()
                        ->select(['curriculum_id'])
                        ->where(['Students.id' => $this->request->getData('Section.Selected_student_id')])
                        ->first()
                        ->curriculum_id;

                    if ($section_curriculum == $student_curriculum) {
                        $already_recorded_id = $this->checkTheRecordInArchive($this->request->getData('Section.section_id'), $this->request->getData('Section.Selected_student_id'));

                        if (!empty($already_recorded_id)) {
                            $studentsSection = $this->Sections->StudentsSections->get($already_recorded_id);
                            $studentsSection->archive = 0;
                            $this->Sections->StudentsSections->save($studentsSection);
                        } else {
                            $studentsSection = $this->Sections->StudentsSections->newEntity([
                                'student_id' => $this->request->getData('Section.Selected_student_id'),
                                'section_id' => $this->request->getData('Section.section_id'),
                            ]);
                            $this->Sections->StudentsSections->save($studentsSection);
                        }

                        $this->Flash->success("$student_full_name is Added to Section $section_name.");
                        return $this->redirect(['action' => 'displaySections', $this->request->getData('Section.section_id')]);
                    } else {
                        $section_curriculum_name = $this->Sections->Students->Curriculums->find()
                            ->select(['curriculum_detail'])
                            ->where(['Curriculums.id' => $section_curriculum])
                            ->first()
                            ->curriculum_detail;
                        $student_curriculum_name = $this->Sections->Students->Curriculums->find()
                            ->select(['curriculum_detail'])
                            ->where(['Curriculums.id' => $student_curriculum])
                            ->first()
                            ->curriculum_detail;

                        $this->Flash->error(
                            "$student_full_name will not be added to Section $section_name. That's because, he/she studies by $student_curriculum_name curriculum, which is different from Section $section_name curriculum $section_curriculum_name.",
                            ['element' => 'error_with_link', 'params' => ['link_text' => 'this page', 'link_url' => ['action' => 'displaySections', $this->request->getData('Section.section_id')]]]
                        );
                        return $this->redirect(['action' => 'displaySections', $this->request->getData('Section.section_id')]);
                    }
                } else {
                    $already_recorded_id = $this->checkTheRecordInArchive($this->request->getData('Section.section_id'), $this->request->getData('Section.Selected_student_id'));

                    if (!empty($already_recorded_id)) {
                        $studentsSection = $this->Sections->StudentsSections->get($already_recorded_id);
                        $studentsSection->archive = 0;
                        $this->Sections->StudentsSections->save($studentsSection);
                    } else {
                        $studentsSection = $this->Sections->StudentsSections->newEntity([
                            'student_id' => $this->request->getData('Section.Selected_student_id'),
                            'section_id' => $this->request->getData('Section.section_id'),
                        ]);
                        $this->Sections->StudentsSections->save($studentsSection);
                    }

                    $this->Flash->success("$student_full_name is Added to Section $section_name.");
                    return $this->redirect(['action' => 'displaySections', $this->request->getData('Section.section_id')]);
                }
            } else {
                $this->Flash->error(
                    "$student_full_name fails to qualify to be added to a section.",
                    ['element' => 'error_with_link', 'params' => ['link_text' => 'this page', 'link_url' => ['action' => 'displaySections', $this->request->getData('Section.section_id')]]]
                );
                return $this->redirect(['action' => 'displaySections', $this->request->getData('Section.section_id')]);
            }
        }
    }


    public function massStudentSectionAdd()
    {
        if (!empty($this->request->getData())) {
            $new_section_detail = $this->Sections->find()
                ->where(['Sections.id' => $this->request->getData('SectionDetail.section_id')])
                ->contain([
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                ])
                ->first();

            if (!empty($new_section_detail->curriculum->id)) {
                $section_curriculum = $new_section_detail->curriculum->id;
            } else {
                $section_curriculum = $this->Sections->getSectionCurriculum($this->request->getData('SectionDetail.section_id'));
            }

            $failSuccess = ['success' => 0, 'error' => 0];
            $fresh = false;
            $failReason = '<ul>';

            // debug($this->request->getData());

            if (!empty($this->request->getData('Section'))) {
                foreach ($this->request->getData('Section') as $k => $v) {
                    if (is_numeric($k) && $v['selected_id'] == 1) {
                        $studentAdds = [];

                        $studentNameAndID = $this->Sections->Students->find()
                            ->where(['Students.id' => $v['student_id']])
                            ->select(['first_name', 'middle_name', 'last_name', 'studentnumber', 'curriculum_id', 'college_id', 'department_id', 'academicyear', 'graduated'])
                            ->first();

                        $studentnumber = "{$studentNameAndID->first_name} {$studentNameAndID->middle_name} {$studentNameAndID->last_name} ({$studentNameAndID->studentnumber})";

                        if ($this->role_id == ROLE_DEPARTMENT) {
                            $student_curriculum = $studentNameAndID->curriculum_id;
                            $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatus');
                            $studentYearLevel = $studentExamStatusTable->studentYearAndSemesterLevel($v['student_id']);
                        } else {
                            $studentYearLevel = 'Pre/1st';
                            $section_curriculum = -1;
                            $student_curriculum = -1;
                            $fresh = true;
                        }

                        // debug($fresh);

                        if (($new_section_detail->year_level->name == $studentYearLevel['year'] && $this->Sections->Students->StudentExamStatuses->getStudentExamStatus($v['student_id']) && ($student_curriculum == $section_curriculum || $section_curriculum == "nostudentinsection")) || $fresh) {
                            $already_recorded_id = $this->checkTheRecordInArchive($v['section_id'], $v['student_id']);

                            if ($already_recorded_id) {
                                $studentsSection = $this->Sections->StudentsSections->get($already_recorded_id);
                                $studentsSection->archive = 0;
                                $this->Sections->StudentsSections->save($studentsSection);
                                $failSuccess['success']++;
                            } else {
                                $studentAdds = $this->Sections->StudentsSections->newEntity([
                                    'student_id' => $v['student_id'],
                                    'section_id' => $v['section_id'],
                                ]);
                                $this->Sections->StudentsSections->save($studentAdds);
                                $failSuccess['success']++;
                            }

                            if ((is_null($new_section_detail->curriculum->id) || empty($new_section_detail->curriculum->id)) && !$fresh) {
                                if (is_numeric($section_curriculum) && $section_curriculum > 3) {
                                    $section = $this->Sections->get($v['section_id']);
                                    $section->curriculum_id = $section_curriculum;
                                    $this->Sections->save($section);
                                } elseif (is_numeric($student_curriculum) && $student_curriculum > 3) {
                                    $section = $this->Sections->get($v['section_id']);
                                    $section->curriculum_id = $student_curriculum;
                                    $this->Sections->save($section);
                                }
                            }
                        } else {
                            $failSuccess['error']++;
                            $failReason .= '<br>';
                            if ($new_section_detail->year_level->name != $studentYearLevel['year']) {
                                $failReason .= "<li> {$new_section_detail->name} section's year level is {$new_section_detail->year_level->name} but, $studentnumber is in {$studentYearLevel['year']} year. </li>";
                            }
                            if ($student_curriculum != $section_curriculum) {
                                $student_curriculum_name = $this->Sections->Curriculums->find()
                                    ->select(['name'])
                                    ->where(['Curriculums.id' => $student_curriculum])
                                    ->first()
                                    ->name;
                                $section_curriculum_name = $this->Sections->Curriculums->find()
                                    ->select(['name'])
                                    ->where(['Curriculums.id' => $section_curriculum])
                                    ->first()
                                    ->name;
                                $failReason .= "<li> {$new_section_detail->name} section's curriculum is \"$section_curriculum_name\" but, $studentnumber is attached to \"$student_curriculum_name\" Curriculum.</li>";
                            }
                        }
                    }
                }
            }

            $failReason .= '</ul>';

            if ($failSuccess['success']) {
                $this->Flash->success(($failSuccess['success'] == 1 ? "$studentnumber has been" : "{$failSuccess['success']} Students have been") . " added to {$new_section_detail->name} Section Successfully.");
                return $this->redirect(['action' => 'displaySections', $new_section_detail->id]);
            } elseif ($failSuccess['error']) {
                $this->Flash->error(
                    "Not able to add to the section with the following reason $failReason",
                    ['element' => 'error_with_link', 'params' => ['link_text' => 'this page', 'link_url' => ['action' => 'displaySections', $this->request->getData('SectionDetail.section_id')]]]
                );
                return $this->redirect(['action' => 'displaySections', $this->request->getData('SectionDetail.section_id')]);
            }
            return $this->redirect(['action' => 'displaySections', $this->request->getData('SectionDetail.section_id')]);
        }
    }

    public function upgradeSections()
    {
        if ($this->auth_user['role_id'] != ROLE_DEPARTMENT) {
            $this->Flash->warning('You need to have department role to upgrade section year levels!');
            return $this->redirect('/');
        }

        $programs = $this->Sections->Programs->find('list')->toArray();
        $programTypes = $this->Sections->ProgramTypes->find('list')->toArray();
        $yearLevels = $this->Sections->YearLevels->find('list')
            ->where(['YearLevels.department_id' => $this->department_id])
            ->toArray();

        $isbeforesearch = 1;

        $this->set(compact('programs', 'programTypes', 'isbeforesearch', 'yearLevels'));

        if (!empty($this->request->getData()) && $this->request->getData('search')) {
            $isbeforesearch = 0;
            $selected_program = $this->request->getData('Section.program_id');
            $selected_program_type = $this->request->getData('Section.program_type_id');
            $selected_academicyear = !empty($this->request->getData('Section.academicyear')) ? $this->request->getData('Section.academicyear') : $this->AcademicYear->currentAcademicYear();
            $selected_year_level = $this->request->getData('Section.year_level_id');

            if (empty($selected_year_level)) {
                $selected_year_level = array_keys($yearLevels);
            }

            $sections = $this->Sections->find()
                ->where([
                    'Sections.department_id' => $this->department_id,
                    'Sections.program_id' => !empty($selected_program) ? $selected_program : $this->program_ids,
                    'Sections.program_type_id' => !empty($selected_program_type) ? $selected_program_type : $this->program_type_ids,
                    'Sections.academicyear' => $selected_academicyear,
                    'Sections.year_level_id IN' => $selected_year_level,
                    'Sections.archive' => 0,
                ])
                ->contain([
                    'YearLevels' => ['fields' => ['name']],
                    'PublishedCourses' => [
                        'fields' => ['id', 'section_id'],
                        'CourseRegistrations' => ['fields' => ['id', 'published_course_id', 'section_id', 'student_id'], 'limit' => 1],
                        'limit' => 1,
                    ],
                ])
                ->order(['Sections.year_level_id' => 'ASC', 'Sections.program_id' => 'ASC', 'Sections.program_type_id' => 'ASC', 'Sections.name' => 'ASC'])
                ->toArray();

            $sections_lastpublishedcourses_list = [];
            $last_year_level_sections_count = 0;

            if (!empty($sections)) {
                foreach ($sections as $sKey => &$section) {
                    if (!empty($section->department_id) && $section->department_id == $this->department_id) {
                        // debug($section->published_courses[0]->course_registrations[0] ?? '');

                        if (empty($section->published_courses) || (isset($section->published_courses[0]) && !isset($section->published_courses[0]->course_registrations[0]))) {
                            unset($sections[$sKey]);
                            continue;
                        }

                        if (!empty($section->curriculum_id) && $section->curriculum_id > 0 && !empty($section->year_level_id) && $section->year_level_id > 0) {
                            $curriculum_year_levels = $this->Sections->Curriculums->Courses->find('list')
                                ->where(['Courses.curriculum_id' => $section->curriculum_id, 'Courses.active' => 1])
                                ->select(['Courses.year_level_id', 'Courses.year_level_id'])
                                ->group(['Courses.year_level_id'])
                                ->order(['Courses.year_level_id' => 'DESC'])
                                ->toArray();
                            // debug($curriculum_year_levels);

                            if (!empty($curriculum_year_levels)) {
                                $curriculum_year_levels = array_values($curriculum_year_levels);
                                // debug($curriculum_year_levels);

                                if (!empty($selected_year_level) && !is_array($selected_year_level) && is_numeric($selected_year_level) && $curriculum_year_levels[0] <= $selected_year_level) {
                                    $section->last_year_level_section = true;
                                    $last_year_level_sections_count++;
                                } elseif (!empty($selected_year_level) && is_array($selected_year_level) && ($curriculum_year_levels[0] <= $section->year_level_id)) {
                                    $section->last_year_level_section = true;
                                    $last_year_level_sections_count++;
                                } elseif ($curriculum_year_levels[0] == $section->year_level_id) {
                                    $section->last_year_level_section = true;
                                    $last_year_level_sections_count++;
                                } else {
                                    $section->last_year_level_section = false;
                                }
                            } else {
                                unset($sections[$sKey]);
                                continue;
                            }
                        } else {
                            $department_year_levels = $this->Sections->YearLevels->find('list')
                                ->where(['YearLevels.department_id' => $this->department_id])
                                ->select(['YearLevels.id', 'YearLevels.id'])
                                ->group(['YearLevels.department_id', 'YearLevels.id'])
                                ->order(['YearLevels.id' => 'DESC'])
                                ->toArray();

                            if (!empty($department_year_levels)) {
                                $department_year_levels = array_values($department_year_levels);
                                // debug($department_year_levels);

                                if (!empty($selected_year_level) && !is_array($selected_year_level) && is_numeric($selected_year_level) && $department_year_levels[0] == $selected_year_level) {
                                    $section->last_year_level_section = true;
                                    $last_year_level_sections_count++;
                                } elseif (!empty($selected_year_level) && is_array($selected_year_level) && $department_year_levels[0] == $section->year_level_id) {
                                    $section->last_year_level_section = true;
                                    $last_year_level_sections_count++;
                                } elseif ($department_year_levels[0] == $section->year_level_id) {
                                    $section->last_year_level_section = true;
                                    $last_year_level_sections_count++;
                                } else {
                                    $section->last_year_level_section = false;
                                }
                            } else {
                                unset($sections[$sKey]);
                                continue;
                            }
                        }

                        $sections_lastpublishedcourses_list[$section->id] = $this->Sections->PublishedCourses->lastPublishedCoursesForSection($section->id);
                        $sections_lastpublishedcourses_list[$section->id]['last_year_level_section'] = !empty($section->last_year_level_section);
                    } else {
                        unset($sections[$sKey]);
                        continue;
                    }
                }
            }

            // debug($sections);
            // debug($sections_lastpublishedcourses_list);

            $upgradable_sections = [];
            $unupgradable_sections = [];

            if (!empty($sections_lastpublishedcourses_list)) {
                foreach ($sections_lastpublishedcourses_list as $sk => $sv) {
                    $is_submited_grade = 1;
                    // debug($sv);
                    // debug($sv['last_year_level_section']);

                    if (!$sv['last_year_level_section']) {
                        foreach ($sv as $pk => $vk) {
                            if (is_numeric($pk)) {
                                $is_submited_grade *= $this->Sections->PublishedCourses->CourseRegistrations->ExamGrades->isGradeSubmitted($pk);
                                // debug($is_submited_grade);
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

            // debug($upgradable_sections);
            // debug($unupgradable_sections);

            $formatedSections = [];
            $unqualified_students_count = [];

            if (!empty($sections)) {
                foreach ($sections as $usk => $usv) {
                    if (in_array($usv->id, $upgradable_sections) && (empty($usv->last_year_level_section))) {
                        $formatedSections[$usv->year_level->name]['Upgradable'][$usv->id] = $usv->name . ' (' . ($usv->year_level->name ?? 'Pre/1st') . ', ' . $usv->academicyear . ')';
                    } else {
                        $formatedSections[$usv->year_level->name]['Unupgradable'][$usv->id] = $usv->name . ' (' . ($usv->year_level->name ?? 'Pre/1st') . ', ' . $usv->academicyear . ')' . (!empty($usv->last_year_level_section) ? ' <span class="on-process">(Final Year)</span>' : ' <span class="rejected"> Grade not fully submitted</span>');
                    }
                }
            }

            if (!empty($upgradable_sections)) {
                $unqualified_students = $this->getUnqualifiedStudentsCount($upgradable_sections, $this->request->getData('Section.academicyear'));
            }

            if (!empty($unqualified_students)) {
                $unqualified_students_count = $unqualified_students;
            }

            $session = $this->request->getSession();
            $session->write('unqualified_students_count', $unqualified_students_count);
            $session->write('formatedSections', $formatedSections);
            // debug($unqualified_students_count);

            $this->set(compact(
                'formatedSections',
                'unqualified_students_count',
                'isbeforesearch',
                'selected_program',
                'selected_program_type',
                'selected_academicyear',
                'selected_year_level',
                'last_year_level_sections_count'
            ));
        }

        if (!empty($this->request->getData('upgrade'))) {
            $selected_sections = [];

            if (!empty($this->request->getData('Section.Upgradbale_Selected'))) {
                foreach ($this->request->getData('Section.Upgradbale_Selected') as $susk => $susv) {
                    if ($susv != 0) {
                        $selected_sections[] = $susv;
                    }
                }
            }

            $selected_section_count = count($selected_sections);

            if (!empty($selected_section_count)) {
                $upgradeStatus = $this->Sections->upgradeSelectedSection($selected_sections);
                // debug($upgradeStatus);

                if (!empty($upgradeStatus)) {
                    $session = $this->request->getSession();
                    $this->Flash->success(implode(" ", $upgradeStatus) . ' section have been upgraded successfully.');
                    if ($session->check('formatedSections')) {
                        $session->delete('formatedSections');
                        if ($session->check('unqualified_students_count')) {
                            $session->delete('unqualified_students_count');
                        }
                    }
                    return $this->redirect(['action' => 'displaySections']);
                } else {
                    $this->Flash->error(
                        'Unable to upgrade the selected section(s), all section students fail to qualify for year level upgrade, leaving the section(s) un-upgraded.',
                        ['element' => 'error_with_link', 'params' => ['link_text' => 'this page', 'link_url' => ['action' => 'displaySections']]]
                    );
                }
            } else {
                $this->Flash->error(
                    'Please select at least one section.',
                    ['element' => 'error_with_link', 'params' => ['link_text' => 'this page', 'link_url' => ['action' => 'displaySections']]]
                );
            }

            $this->request = $this->request->withData('search', true);
            $formatedSections = null;
            $unqualified_students_count = null;
            $isbeforesearch = 0;

            $session = $this->request->getSession();
            if ($session->check('formatedSections')) {
                $formatedSections = $session->read('formatedSections');
            }

            if ($session->check('unqualified_students_count')) {
                $unqualified_students_count = $session->read('unqualified_students_count');
            }

            $this->set(compact('formatedSections', 'unqualified_students_count', 'isbeforesearch', 'last_year_level_sections_count'));
        }
    }

    public function upgradeSelectedStudentSection(int $section_id, int $student_id)
    {
        $this->viewBuilder()->setLayout('ajax');

        $student_detail = $this->Sections->Students->find()
            ->where(['Students.id' => $student_id])
            ->contain(['AcceptedStudents'])
            ->first();

        $section = $this->Sections->find()
            ->where(['Sections.id' => $section_id])
            ->first();

        $nextSection = $this->Sections->find()
            ->where([
                'Sections.year_level_id' => $section->year_level_id + 1,
                'Sections.department_id' => $student_detail->department_id,
                'Sections.program_id' => $student_detail->program_id,
                'Sections.program_type_id' => $student_detail->program_type_id,
            ])
            ->first();

        $this->set(compact('student_detail', 'nextSection'));
    }

    public function getSectionsByProgram(string $program_id = "")
    {
        $this->viewBuilder()->setLayout('ajax');
        $sections = [];
        $student_sections = [];

        $department = $this->auth_user['role_id'] == ROLE_DEPARTMENT ? 1 : 0;

        if (empty($program_id)) {
            $program_id = !empty($this->program_ids) ? array_values($this->program_ids)[0] : 1;
        }

        $student_sections = $this->Sections->allDepartmentSectionsOrganizedByProgramType(
            $department == 1 ? $this->department_id : $this->college_id,
            $department,
            $program_id
        );

        $this->set(compact('student_sections'));
    }

    public function getSectionsByProgramSuppExam(string $program_id = "")
    {
        $this->viewBuilder()->setLayout('ajax');
        $sections = [];
        $student_sections = [];

        $department = $this->auth_user['role_id'] == ROLE_DEPARTMENT ? 1 : 0;

        if (empty($program_id)) {
            $program_id = !empty($this->program_ids) ? array_values($this->program_ids)[0] : 1;
        }

        $student_sections = $this->Sections->allDepartmentSectionsOrganizedByProgramTypeSuppExam(
            $department == 1 ? $this->department_id : $this->college_id,
            $department,
            $program_id,
            3
        );

        $this->set(compact('student_sections'));
    }

    public function getSectionStudents(string $section_id = "")
    {
        $this->viewBuilder()->setLayout('ajax');
        $students = [];

        if (!empty($section_id)) {
            $students = $this->Sections->allStudents($section_id);
            // $students = TableRegistry::getTableLocator()->get('ExamGradeChanges')->possibleStudentsForSup($section_id);
        }

        $this->set(compact('students'));
    }

    public function getSupStudents(string $section_id = "")
    {
        $this->viewBuilder()->setLayout('ajax');
        $students = [];

        if (!empty($section_id)) {
            $students = TableRegistry::getTableLocator()->get('ExamGradeChanges')->possibleStudentsForSup($section_id);
        }

        $this->set(compact('students'));
    }

    public function getSectionsByProgramAndDept(string $department_id = "", string $program_id = "")
    {
        $this->viewBuilder()->setLayout('ajax');
        $student_sections = [];

        $department_role = 0;

        if ($this->auth_user['role_id'] == ROLE_DEPARTMENT) {
            $department_role = 1;
            $department_id = empty($department_id) ? $this->department_id : $department_id;
        } elseif ($this->auth_user['role_id'] == ROLE_COLLEGE) {
            $department_id = empty($department_id) ? $this->college_id : $department_id;
        }

        if (empty($program_id)) {
            $program_id = !empty($this->program_ids) ? array_values($this->program_ids)[0] : 1;
        }

        if (!empty($department_id) && !empty($program_id)) {
            $student_sections = $this->Sections->allDepartmentSectionsOrganizedByProgramType(
                $department_id,
                $department_role,
                $program_id
            );
        }

        $this->set(compact('student_sections'));
    }

    public function getSectionsByProgramAndDeptSuppExam(string $department_id = "", string $program_id = "")
    {
        $this->viewBuilder()->setLayout('ajax');
        $student_sections = [];

        $department_role = 0;

        if ($this->auth_user['role_id'] == ROLE_DEPARTMENT) {
            $department_role = 1;
            $department_id = empty($department_id) ? $this->department_id : $department_id;
        } elseif ($this->auth_user['role_id'] == ROLE_COLLEGE) {
            $department_id = empty($department_id) ? $this->college_id : $department_id;
        }

        if (empty($program_id)) {
            $program_id = !empty($this->program_ids) ? array_values($this->program_ids)[0] : 1;
        }

        if (!empty($department_id) && !empty($program_id)) {
            $student_sections = $this->Sections->allDepartmentSectionsOrganizedByProgramTypeSuppExam(
                $department_id,
                $department_role,
                $program_id,
                3
            );
        }

        $this->set(compact('student_sections'));
    }

    public function getYearLevel(?int $department_id = null)
    {
        $yearLevels = [];

        if (!empty($department_id)) {
            $this->viewBuilder()->setLayout('ajax');
            $yearLevels = $this->Sections->YearLevels->find('list')
                ->where(['YearLevels.department_id' => $department_id])
                ->toArray();
        }

        $this->set(compact('yearLevels'));
    }

    public function getSectionsOfCollege(?int $college_id = null)
    {
        $this->viewBuilder()->setLayout('ajax');
        $sections = [];

        if (!empty($this->student_id)) {
            $student_program_id = $this->Sections->Students->find()
                ->select(['program_id'])
                ->where(['Students.id' => $this->student_id])
                ->first()
                ->program_id;

            $student_section_id = $this->Sections->StudentsSections->find()
                ->select(['section_id'])
                ->where(['StudentsSections.student_id' => $this->student_id, 'StudentsSections.archive' => 0])
                ->first()
                ->section_id;

            $sections_detail = $this->Sections->find()
                ->where([
                    'Sections.department_id IS' => null,
                    'Sections.college_id' => $college_id,
                    'Sections.program_id' => $student_program_id,
                    'Sections.archive' => 0,
                ])
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                ])
                ->select(['Sections.id', 'Sections.name', 'Sections.program_id', 'Sections.year_level_id', 'Sections.academicyear', 'Sections.curriculum_id'])
                ->order(['Sections.academicyear' => 'DESC', 'Sections.program_id' => 'ASC', 'Sections.year_level_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC'])
                ->toArray();
        } else {
            $sections_detail = $this->Sections->find()
                ->where([
                    'Sections.college_id' => $college_id,
                    'Sections.archive' => 0,
                    'Sections.department_id IS' => null,
                ])
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                ])
                ->select(['Sections.id', 'Sections.name', 'Sections.program_id', 'Sections.year_level_id', 'Sections.academicyear', 'Sections.curriculum_id'])
                ->order(['Sections.academicyear' => 'DESC', 'Sections.program_id' => 'ASC', 'Sections.year_level_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC'])
                ->toArray();
        }

        if (!empty($sections_detail)) {
            foreach ($sections_detail as $secvalue) {
                if (empty($secvalue->year_level->id)) {
                    $sections[$secvalue->program->name][$secvalue->id] = $secvalue->name . ' (' . $secvalue->academicyear . ', ' . ($secvalue->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Fresh') . ')';
                } else {
                    $sections[$secvalue->program->name][$secvalue->id] = $secvalue->name . ' (' . $secvalue->academicyear . ', ' . $secvalue->year_level->name . ')';
                }
            }
        }

        $this->set(compact('sections'));
    }

    public function getSectionsByDept(string $department_id = "", string $student_id = '', string $acYear = '', string $year_level_name = '')
    {
        $this->viewBuilder()->setLayout('ajax');
        $sections = [];

        if (!empty($this->student_id)) {
            $student_program_id = $this->Sections->Students->find()
                ->select(['program_id'])
                ->where(['Students.id' => $this->student_id])
                ->first()
                ->program_id;

            $student_section_id = $this->Sections->StudentsSections->find()
                ->select(['section_id'])
                ->where(['StudentsSections.student_id' => $this->student_id, 'StudentsSections.archive' => 0])
                ->first()
                ->section_id;

            $conditions = [];

            if (!empty($this->request->getData('Student.college_id')) && $this->request->getData('Student.department_id') == -1) {
                // debug($this->request->getData());
                $conditions = [
                    'Sections.college_id' => $this->request->getData('Student.college_id'),
                    'Sections.archive' => 0,
                    'Sections.department_id IS' => null,
                    'Sections.program_id' => $student_program_id,
                    'OR' => [
                        'Sections.academicyear LIKE' => $acYear,
                        'Sections.created >=' => date('Y-m-d H:i:s', strtotime('-1 year')),
                    ],
                ];
                // debug($conditions);
            } elseif (!empty($this->request->getData('Student.college_id')) && empty($this->request->getData('Student.department_id'))) {
                $conditions = [
                    'Sections.college_id' => $this->request->getData('Student.college_id'),
                    'Sections.archive' => 0,
                    'Sections.department_id IS' => null,
                    'Sections.program_id' => $student_program_id,
                    'OR' => [
                        'Sections.academicyear LIKE' => $acYear,
                        'Sections.created >=' => date('Y-m-d H:i:s', strtotime('-1 year')),
                    ],
                ];
                // debug($conditions);
            } elseif (!empty($department_id)) {
                $conditions = [
                    'Sections.department_id' => $department_id,
                    'Sections.archive' => 0,
                    'Sections.program_id' => $student_program_id,
                    'OR' => [
                        'Sections.academicyear LIKE' => $acYear,
                        'Sections.created >=' => date('Y-m-d H:i:s', strtotime('-1 year')),
                    ],
                ];
                // debug($conditions);
            }

            $sections_detail = $this->Sections->find()
                ->where($conditions)
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                ])
                ->select(['Sections.id', 'Sections.name', 'Sections.program_id', 'Sections.year_level_id', 'Sections.academicyear', 'Sections.curriculum_id'])
                ->order(['Sections.year_level_id' => 'ASC', 'Sections.academicyear' => 'DESC', 'Sections.program_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC'])
                ->toArray();
        } else {
            $student_program_id = !empty($student_id) && $student_id != 0
                ? $this->Sections->Students->find()->select(['program_id'])->where(['Students.id' => $student_id])->first()->program_id
                : $this->program_id;

            $conditions = !$year_level_name
                ? [
                    'Sections.department_id' => $department_id,
                    'Sections.department_id IS NOT' => null,
                    'Sections.program_id' => $student_program_id,
                    'Sections.archive' => 0,
                    'OR' => [
                        'Sections.academicyear LIKE' => $acYear,
                        'Sections.created >=' => date('Y-m-d H:i:s', strtotime('-1 year')),
                    ],
                ]
                : [
                    'Sections.department_id IS' => null,
                    'Sections.program_id' => $student_program_id,
                    'Sections.archive' => 0,
                    'OR' => [
                        'Sections.academicyear LIKE' => $acYear,
                        'Sections.created >=' => date('Y-m-d H:i:s', strtotime('-1 year')),
                    ],
                ];

            $sections_detail = $this->Sections->find()
                ->where($conditions)
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                ])
                ->select(['Sections.id', 'Sections.name', 'Sections.program_id', 'Sections.year_level_id', 'Sections.academicyear', 'Sections.curriculum_id'])
                ->order(['Sections.academicyear' => 'DESC', 'Sections.program_id' => 'ASC', 'Sections.year_level_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC'])
                ->toArray();
        }

        if (!empty($sections_detail)) {
            foreach ($sections_detail as $secvalue) {
                $dataids = TableRegistry::getTableLocator()->get('StudentsSections')->find('list')
                    ->where(['StudentsSections.section_id' => $secvalue->id])
                    ->select(['student_id', 'student_id'])
                    ->group(['student_id', 'section_id'])
                    ->toArray();
                $graduatingStudent = TableRegistry::getTableLocator()->get('GraduateLists')->find()
                    ->where(['GraduateLists.student_id IN' => $dataids])
                    ->count();

                $isGraduate = $graduatingStudent > count($dataids) / 3;

                if (!$isGraduate) {
                    $yn = !empty($secvalue->year_level->name) ? $secvalue->year_level->name : '1st';
                    $sections[$secvalue->program->name][$secvalue->id] = $secvalue->name . ' (' . $secvalue->academicyear . ', ' . $yn . ')';
                }
            }
        }

        $this->set(compact('sections'));
    }

    public function getSectionsByDeptAddDrop(string $department_id = "", string $student_id = '', string $year_level_name = '', string $college_id = '')
    {
        $this->viewBuilder()->setLayout('ajax');
        $sections = [];

        if ((!empty($student_id) && $student_id != 0) || !empty($this->student_id)) {
            $student_id = $this->auth_user['role_id'] == ROLE_STUDENT && !empty($this->student_id) ? $this->student_id : $student_id;

            $student_detail = $this->Sections->Students->find()
                ->where(['Students.id' => $student_id])
                ->contain([
                    'AcceptedStudents' => ['fields' => ['id', 'studentnumber', 'academicyear']],
                    'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                    'Programs' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                    'Colleges' => ['fields' => ['id', 'name', 'campus_id', 'stream']],
                    'Sections' => [
                        'fields' => [
                            'Sections.id',
                            'Sections.name',
                            'Sections.year_level_id',
                            'Sections.academicyear',
                            'Sections.college_id',
                            'Sections.department_id',
                            'Sections.curriculum_id',
                            'Sections.created',
                            'Sections.archive'
                        ],
                        'order' => [
                            'Sections.academicyear' => 'DESC',
                            'Sections.program_id' => 'ASC',
                            'Sections.year_level_id' => 'ASC',
                            'Sections.id' => 'ASC',
                            'Sections.name' => 'ASC'
                        ],
                        'YearLevels' => ['fields' => ['id', 'name']],
                    ],
                    'CourseRegistrations' => [
                        'order' => [
                            'CourseRegistrations.academic_year' => 'DESC',
                            'CourseRegistrations.semester' => 'DESC',
                            'CourseRegistrations.id' => 'DESC'
                        ],
                        'limit' => 1,
                    ],
                    'Readmissions' => [
                        'where' => [
                            'Readmissions.registrar_approval' => 1,
                            'Readmissions.academic_commision_approval' => 1
                        ],
                        'fields' => ['student_id', 'academic_year', 'semester', 'registrar_approval_date', 'modified'],
                        'order' => ['Readmissions.modified' => 'DESC'],
                    ],
                ])
                ->select([
                    'Students.studentnumber',
                    'Students.full_name',
                    'Students.curriculum_id',
                    'Students.department_id',
                    'Students.college_id',
                    'Students.program_id',
                    'Students.program_type_id',
                    'Students.gender',
                    'Students.graduated',
                    'Students.academicyear',
                    'Students.admissionyear',
                ])
                ->first();

            $student_program_id = $student_detail->program_id;
            $program_types_to_look = $this->getEquivalentProgramTypes($student_detail->program_type_id);

            $lastRegisteredAcademicYear = $student_detail->course_registrations[0]->academic_year ?? '';
            $lastRegisteredSemester = $student_detail->course_registrations[0]->semester ?? '';
            $lastRegisteredYearLevelID = !empty($student_detail->course_registrations[0]->year_level_id) ? $student_detail->course_registrations[0]->year_level_id : 0;
            $lastRegisteredSectionID = !empty($student_detail->course_registrations[0]->section_id) ? $student_detail->course_registrations[0]->section_id : 0;

            $lastRegisteredYearLevelName = $lastRegisteredYearLevelID
                ? $this->Sections->YearLevels->find()->select(['name'])->where(
                    ['YearLevels.id' => $lastRegisteredYearLevelID]
                )->first()->name
                : '';

            $student_section_exam_status = $this->Sections->Students->getStudentSection($student_id);

            $colleges = !empty($student_section_exam_status)
                ? $this->Sections->Colleges->find('list')
                    ->where([
                        'OR' => [
                            'Colleges.campus_id' => $student_section_exam_status['College']['campus_id'],
                            'Colleges.stream' => $student_section_exam_status['College']['stream'],
                        ],
                        'Colleges.active' => 1,
                    ])
                    ->order(['Colleges.campus_id' => 'ASC', 'Colleges.name' => 'ASC'])
                    ->toArray()
                : $this->Sections->Colleges->find('list')
                    ->where([
                        'OR' => [
                            'Colleges.campus_id' => $student_detail->college->campus_id,
                            'Colleges.stream' => $student_detail->college->stream,
                        ],
                        'Colleges.active' => 1,
                    ])
                    ->order(['Colleges.campus_id' => 'ASC', 'Colleges.name' => 'ASC'])
                    ->toArray();

            $collIdsToLook = array_keys($colleges);

            $departments = $this->Sections->Departments->find('list')
                ->where(['Departments.college_id IN' => $collIdsToLook, 'Departments.active' => 1])
                ->toArray();

            $deptIdsToLook = array_keys($departments);

            $conditions = [];

            if (!empty($college_id) && !is_null($student_detail->department_id)) {
                $conditions = [
                    'Sections.college_id IN' => $collIdsToLook,
                    'Sections.program_id' => $student_program_id,
                    'Sections.program_type_id IN' => $program_types_to_look,
                    'Sections.archive' => 0,
                    'Sections.academicyear LIKE' => $lastRegisteredAcademicYear,
                ];

                $conditions[] = ['Sections.college_id' => $college_id];

                if (!empty($lastRegisteredSectionID)) {
                    $conditions[] = ['Sections.id <>' => $lastRegisteredSectionID];
                }

                if ($department_id == -1 || $department_id == 0) {
                    $conditions[] = ['Sections.department_id IS' => null];
                } elseif (!empty($department_id) && $department_id > 0) {
                    $conditions[] = ['Sections.department_id' => $department_id];
                } elseif (empty($department_id) && (empty($lastRegisteredYearLevelName) || empty($year_level_name))) {
                    $conditions[] = ['Sections.department_id IS' => null];
                }

                if ((!empty($lastRegisteredYearLevelName) || !empty($year_level_name)) && !empty($department_id) && $department_id > 0) {
                    $year_levels_applicable = [];

                    $allYearLevels = TableRegistry::getTableLocator()->get('YearLevels')->distinctYearLevel();

                    $yl_name = !empty($year_level_name) ? $year_level_name : $lastRegisteredYearLevelName;

                    if (!empty($allYearLevels) && in_array($yl_name, $allYearLevels)) {
                        foreach ($allYearLevels as $year_level) {
                            $year_levels_applicable[] = $year_level;
                            if (strcasecmp($yl_name, $year_level) == 0) {
                                break;
                            }
                        }
                    }

                    // debug($year_levels_applicable);

                    if (!empty($department_id) && $department_id > 0) {
                        $yearLevelIDs = !empty($year_levels_applicable)
                            ? $this->Sections->YearLevels->find('list')
                                ->where(
                                    [
                                        'YearLevels.department_id' => $department_id,
                                        'YearLevels.name IN' => $year_levels_applicable
                                    ]
                                )
                                ->select(['YearLevels.id', 'YearLevels.id'])
                                ->toArray()
                            : $this->Sections->YearLevels->find('list')
                                ->where(
                                    ['YearLevels.department_id' => $department_id, 'YearLevels.name LIKE' => $yl_name]
                                )
                                ->select(['YearLevels.id', 'YearLevels.id'])
                                ->toArray();
                    } elseif (!empty($lastRegisteredYearLevelName) || !empty($year_level_name)) {
                        $yearLevelIDs = !empty($year_levels_applicable)
                            ? $this->Sections->YearLevels->find('list')
                                ->where(
                                    [
                                        'YearLevels.department_id IN' => $deptIdsToLook,
                                        'YearLevels.name IN' => $year_levels_applicable
                                    ]
                                )
                                ->select(['YearLevels.id', 'YearLevels.id'])
                                ->toArray()
                            : $this->Sections->YearLevels->find('list')
                                ->where(
                                    [
                                        'YearLevels.department_id IN' => $deptIdsToLook,
                                        'YearLevels.name LIKE' => $yl_name
                                    ]
                                )
                                ->select(['YearLevels.id', 'YearLevels.id'])
                                ->toArray();
                    }

                    if (!empty($yearLevelIDs)) {
                        $conditions[] = ['Sections.year_level_id IN' => $yearLevelIDs];
                    }
                } elseif (is_null(
                        $student_detail->department_id
                    ) && (empty($year_level_name) || $department_id == -1 || $department_id == 0)) {
                    $conditions = [
                        'Sections.college_id IN' => (!empty($collIdsToLook) ? $collIdsToLook : $college_id),
                        'Sections.department_id IS' => null,
                        'Sections.academicyear' => !empty($student_detail->course_registrations[0]->academic_year) ? $student_detail->course_registrations[0]->academic_year : $student_detail->academicyear,
                        'Sections.archive' => 0,
                    ];
                }

                // debug($conditions);

                if (!empty($conditions)) {
                    $sections_detail = $this->Sections->find()
                        ->where($conditions)
                        ->contain([
                            'Programs' => ['fields' => ['id', 'name']],
                            'ProgramTypes' => ['fields' => ['id', 'name']],
                            'YearLevels' => ['fields' => ['id', 'name']],
                        ])
                        ->select(
                            [
                                'Sections.id',
                                'Sections.name',
                                'Sections.program_id',
                                'Sections.year_level_id',
                                'Sections.academicyear',
                                'Sections.curriculum_id'
                            ]
                        )
                        ->order(
                            [
                                'Sections.academicyear' => 'DESC',
                                'Sections.program_id' => 'ASC',
                                'Sections.year_level_id' => 'ASC',
                                'Sections.id' => 'ASC',
                                'Sections.name' => 'ASC'
                            ]
                        )
                        ->toArray();
                }
            }
        }

        if (!empty($sections_detail)) {
            foreach ($sections_detail as $secvalue) {
                $yn = !empty($secvalue->year_level->name)
                    ? $secvalue->year_level->name
                    : ($secvalue->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st');
                $sections[$secvalue->program->name][$secvalue->id] = $secvalue->name . ' (' . $yn . ', ' . $secvalue->academicyear . ')';
            }
        }

        $this->set(compact('sections', 'college_id', 'department_id', 'year_level_name'));
    }
    public function getSectionsByDeptDataEntry(string $department_id = "", string $student_id = '', string $academic_year = '', string $program_id = '', string $program_type_id = '')
    {
        $this->viewBuilder()->setLayout('ajax');

        $sections = [];
        $options = [];
        $student_sections = [];
        $department_id_selected = '';

        if (!empty($this->student_id) && empty($student_id)) {
            $student_id = $this->student_id;
        }

        if (!empty($student_id)) {
            $student = $this->Sections->Students->find()
                ->where(['Students.id' => $student_id])
                ->select(['program_id', 'program_type_id', 'department_id'])
                ->first();
            if (!empty($student)) {
                $program_id = $student->program_id;
                $program_type_id = $student->program_type_id;
                $student_sections = $this->Sections->StudentsSections->find('list')
                    ->where(['StudentsSections.student_id' => $student_id])
                    ->select(['StudentsSections.section_id', 'StudentsSections.section_id'])
                    ->toArray();
            }
        }

        if (!empty($student_sections)) {
            $options['conditions']['NOT']['Sections.id IN'] = $student_sections;
        }

        if (!empty($department_id)) {
            $options['conditions']['Sections.department_id'] = $department_id_selected = $department_id;
        } else {
            $options['conditions']['Sections.department_id'] = 0;
        }

        if (!empty($program_id)) {
            $options['conditions']['Sections.program_id'] = $program_id;
        }

        if (!empty($program_type_id)) {
            $program_types_to_look = $this->getEquivalentProgramTypes($program_type_id);
            $options['conditions']['Sections.program_type_id IN'] = $program_types_to_look;
        }

        if (!empty($academic_year)) {
            $academic_year = str_replace('-', '/', $academic_year);
            $options['conditions']['Sections.academicyear'] = $academic_year;
        }

        if (!empty($options['conditions'])) {
            $sections_detail = $this->Sections->find()
                ->where($options['conditions'])
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                ])
                ->select(['Sections.id', 'Sections.name', 'Sections.program_id', 'Sections.year_level_id', 'Sections.academicyear', 'Sections.curriculum_id'])
                ->order(['Sections.academicyear' => 'DESC', 'Sections.program_id' => 'ASC', 'Sections.year_level_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC'])
                ->toArray();
        }

        if (!empty($sections_detail)) {
            foreach ($sections_detail as $secvalue) {
                $sections[$secvalue->program->name][$secvalue->id] = $secvalue->name . ' (' . (!empty($secvalue->year_level->name) ? $secvalue->year_level->name : ($secvalue->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $secvalue->academicyear . ')';
            }
        }

        $this->set(compact('sections', 'department_id_selected'));
    }


    public function getSectionsByAcademicYear(?string $year = null, ?string $ac = null, ?int $department_id = null)
    {
        $this->viewBuilder()->setLayout('ajax');
        $sections = [];

        $sections_detail = $this->Sections->find()
            ->where([
                'Sections.department_id' => $department_id,
                'Sections.academicyear LIKE' => $year . '/' . $ac,
            ])
            ->contain([
                'Programs' => ['fields' => ['id', 'name']],
                'YearLevels' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
            ])
            ->select(['Sections.id', 'Sections.name', 'Sections.program_id', 'Sections.year_level_id', 'Sections.academicyear', 'Sections.curriculum_id'])
            ->order(['Sections.academicyear' => 'DESC', 'Sections.program_id' => 'ASC', 'Sections.year_level_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC'])
            ->toArray();

        if (!empty($sections_detail)) {
            foreach ($sections_detail as $secvalue) {
                $sections[$secvalue->program->name][$secvalue->id] = $secvalue->name . ' (' . (!empty($secvalue->year_level->name) ? $secvalue->year_level->name : ($secvalue->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $secvalue->academicyear . ')';
            }
        }

        $this->set(compact('sections'));
    }

    public function getSectionsByYearLevel(?string $yearLevel = null, ?int $student_id = null, ?string $acYrStart = null)
    {
        $this->viewBuilder()->setLayout('ajax');

        $sections = [];
        $sections_organized_by_acy = [];

        if (!empty($student_id)) {
            $student_detail = $this->Sections->Students->find()
                ->where(['Students.id' => $student_id])
                ->contain([
                    'AcceptedStudents' => ['fields' => ['id', 'academicyear']],
                    'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                ])
                ->first();

            $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatus');
            $possibleAcademicYears = !empty($acYrStart)
                ? $studentExamStatusTable->getAcademicYearRange(str_replace('-', '/', $acYrStart), $this->AcademicYear->currentAcademicYear())
                : $studentExamStatusTable->getAcademicYearRange($student_detail->accepted_student->academicyear, $this->AcademicYear->currentAcademicYear());

            // debug($acYrStart);

            $currentStudentsSection = $this->Sections->StudentsSections->find()
                ->select(['section_id'])
                ->where(['StudentsSections.student_id' => $student_id, 'StudentsSections.archive' => 0])
                ->first()
                ->section_id ?? null;

            $previousStudentsSection = $this->Sections->StudentsSections->find('list')
                ->where(['StudentsSections.student_id' => $student_id, 'StudentsSections.archive' => 1])
                ->group(['StudentsSections.student_id', 'StudentsSections.section_id'])
                ->select(['StudentsSections.section_id', 'StudentsSections.section_id'])
                ->order(['StudentsSections.id' => 'DESC'])
                ->toArray();

            $excludeSections = !empty($previousStudentsSection) || !empty($currentStudentsSection)
                ? array_merge($previousStudentsSection, !empty($currentStudentsSection) ? [$currentStudentsSection] : [])
                : [0];

            $excludeSections = array_values($excludeSections);
            // debug($excludeSections);

            $program_types_to_look = $this->getEquivalentProgramTypes($student_detail->program_type_id);
            // debug($program_types_to_look);

            if (!is_null($yearLevel) && $yearLevel != 0 && ($this->auth_user['role_id'] != ROLE_DEPARTMENT || !is_numeric($yearLevel))) {
                $yearLevel = $this->Sections->YearLevels->find()
                    ->select(['id'])
                    ->where(['YearLevels.department_id' => $student_detail->department_id, 'YearLevels.name' => $yearLevel])
                    ->first()
                    ->id ?? null;
            }

            // debug($yearLevel);

            $conditions = [
                'NOT' => ['Sections.id IN' => $excludeSections],
                'Sections.academicyear IN' => $possibleAcademicYears,
                'Sections.program_id' => $student_detail->program_id,
                'Sections.program_type_id IN' => $program_types_to_look,
                'Sections.archive' => 0,
            ];

            if (!is_null($yearLevel) && !empty($yearLevel) && $yearLevel != 0) {
                $conditions['Sections.year_level_id'] = $yearLevel;
                $conditions['Sections.department_id'] = $student_detail->department_id;
                $conditions['Sections.curriculum_id'] = $student_detail->curriculum_id;
            } else {
                $conditions['Sections.college_id'] = $student_detail->college_id;
                $conditions['Sections.department_id IS'] = null;
            }

            $sections = $this->Sections->find()
                ->where($conditions)
                ->contain([
                    'Programs' => ['fields' => ['id', 'name']],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'ProgramTypes' => ['fields' => ['id', 'name']],
                ])
                ->select(['Sections.id', 'Sections.name', 'Sections.program_id', 'Sections.year_level_id', 'Sections.academicyear'])
                ->order(['Sections.academicyear' => 'DESC', 'Sections.program_id' => 'ASC', 'Sections.year_level_id' => 'ASC', 'Sections.id' => 'ASC', 'Sections.name' => 'ASC'])
                ->toArray();

            if (!empty($sections)) {
                foreach ($sections as $v) {
                    $sections_organized_by_acy[$v->academicyear][$v->id] = trim($v->name) . ' (' . (!empty($v->year_level->name) ? $v->year_level->name : ($v->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $v->academicyear . ')';
                }
            }
        }

        $this->set(compact('sections', 'sections_organized_by_acy'));
    }

    public function getModalBox(?int $section_id = null)
    {
        $this->viewBuilder()->setLayout('ajax');

        if (!empty($section_id)) {
            // debug($section_id);

            $selected_sections_students = [];
            $unupgradable_selected_sections_students = [];

            $selected_sections_students = $this->Sections->getSectionActiveStudentsRegistered($section_id);

            $academicyear = $this->Sections->find()
                ->select(['academicyear'])
                ->where(['Sections.id' => $section_id])
                ->first()
                ->academicyear;

            if (!empty($selected_sections_students)) {
                foreach ($selected_sections_students as $sssv) {
                    $student_status = $this->Sections->Students->StudentExamStatuses->isStudentPassed($sssv['StudentsSection']['student_id'], $academicyear);
                    $all_valid_grades = $this->Sections->checkAllRegisteredAddedCoursesAreGraded($sssv['StudentsSection']['student_id'], $section_id, 1, '');

                    if ($student_status == 4 || $student_status == 2 || !$all_valid_grades) {
                        $unupgradable_selected_sections_students[] = $sssv['StudentsSection']['student_id'];
                    }
                }
            }

            // debug($unupgradable_selected_sections_students);

            $status_name = TableRegistry::getTableLocator()->get('AcademicStatuses')->find()
                ->select(['name'])
                ->where(['AcademicStatuses.id' => DISMISSED_ACADEMIC_STATUS_ID])
                ->first()
                ->name;

            $students_details = [];

            if (!empty($unupgradable_selected_sections_students)) {
                foreach ($unupgradable_selected_sections_students as $student_id) {
                    $students_details[$student_id] = $this->Sections->Students->getStudentDetails($student_id);
                }
            }

            // debug($students_details);

            $this->set(compact('students_details', 'status_name'));
        }
    }

    protected function getUnqualifiedStudentsCount(?array $selected_sections = null, ?string $academicYear = null): array
    {
        $selected_sections_students = [];
        $categorize_selected_sections_students = [];
        $sectionunupgradablestudentscount = [];

        if (!empty($selected_sections)) {
            foreach ($selected_sections as $ssv) {
                $selected_sections_students[$ssv] = $this->Sections->getSectionActiveStudentsRegistered($ssv);

                if (empty($academicYear)) {
                    $academicYear = $this->Sections->find()
                        ->select(['academicyear'])
                        ->where(['Sections.id' => $ssv])
                        ->first()
                        ->academicyear;
                }

                if (!empty($selected_sections_students[$ssv])) {
                    // debug($ssv);
                    // debug($selected_sections_students[$ssv]);
                    $start = microtime(true);

                    foreach ($selected_sections_students[$ssv] as $sssv) {
                        if (!empty($sssv['StudentsSection']['student_id'])) {
                            $student_status = $this->Sections->Students->StudentExamStatuses->isStudentPassed($sssv['StudentsSection']['student_id'], $academicYear);
                            $all_valid_grades = $this->Sections->checkAllRegisteredAddedCoursesAreGraded($sssv['StudentsSection']['student_id'], $ssv, 1, '');
                            // debug($student_status);

                            if ($student_status == 4 || $student_status == 2 || !$all_valid_grades) {
                                $categorize_selected_sections_students[$ssv]['unupgradable'][] = $sssv['StudentsSection']['student_id'];
                            } else {
                                $categorize_selected_sections_students[$ssv]['upgradable'][] = $sssv['StudentsSection']['student_id'];
                                // debug($sssv['StudentsSection']['student_id']);
                            }
                        }
                    }

                    // debug($start);
                    // $time_elapsed_secs = microtime(true) - $start;
                    // echo "Time elapsed = " . $time_elapsed_secs;

                    if (!empty($categorize_selected_sections_students[$ssv]['unupgradable'])) {
                        $sectionunupgradablestudentscount[$ssv] = count($categorize_selected_sections_students[$ssv]['unupgradable']);
                    }

                    // $time_elapsed_secs = microtime(true) - $start;
                    // echo "Time elapsed = " . $time_elapsed_secs;
                    // debug($ssv);
                }
                // debug($ssv);
            }
        }

        return $sectionunupgradablestudentscount;
    }

    public function downgradeSections()
    {
        if ($this->auth_user['role_id'] != ROLE_DEPARTMENT) {
            $this->Flash->warning('You need to have department role to downgrade section year levels!');
            return $this->redirect('/');
        }

        $programs = $this->Sections->Programs->find('list')->toArray();
        $programTypes = $this->Sections->ProgramTypes->find('list')->toArray();
        $yearLevels = $this->Sections->YearLevels->find('list')
            ->where(['YearLevels.department_id' => $this->department_id])
            ->toArray();
        $isbeforesearch = 1;

        $this->set(compact('programs', 'programTypes', 'isbeforesearch', 'yearLevels'));

        if (!empty($this->request->getData()) && $this->request->getData('search')) {
            $isbeforesearch = 0;
            $selected_program = $this->request->getData('Section.program_id');
            $selected_program_type = $this->request->getData('Section.program_type_id');
            $selected_academicyear = $this->request->getData('Section.academicyear');
            $selected_year_level = $this->request->getData('Section.year_level_id');

            $sections = $this->Sections->find()
                ->select(['Sections.id', 'Sections.name'])
                ->where([
                    'Sections.department_id' => $this->department_id,
                    'Sections.program_id' => $selected_program,
                    'Sections.program_type_id' => $selected_program_type,
                    'Sections.academicyear' => $selected_academicyear,
                    'Sections.year_level_id LIKE' => $selected_year_level,
                    'Sections.archive' => 0,
                    'Sections.id NOT IN' => $this->Sections->PublishedCourses->find()->select(['section_id']),
                ])
                ->toArray();

            $formateddowngradableSections = [];

            if (!empty($sections)) {
                foreach ($sections as $usv) {
                    $formateddowngradableSections[$usv->id] = $usv->name;
                }
            }

            $this->request->getSession()->write('formateddowngradableSections', $formateddowngradableSections);
            $this->set(compact('formateddowngradableSections', 'isbeforesearch'));
        }

        if (!empty($this->request->getData()) && $this->request->getData('downgrade')) {
            $selectedSection = [];

            if (!empty($this->request->getData('Section.Downgradable_Selected'))) {
                foreach ($this->request->getData('Section.Downgradable_Selected') as $k => $v) {
                    if ($v != 0) {
                        $selectedSection[$k] = $k;
                    }
                }
            }

            if (!empty($selectedSection)) {
                $downgradeSection = $this->Sections->downgradeSelectedSection($selectedSection);
                if (!empty($downgradeSection['success']) && empty($downgradeSection['unsuccess'])) {
                    $this->Flash->success('Section ' . count($downgradeSection['success']) . ' have been downgraded successfully.');
                } elseif (!empty($downgradeSection['success']) && !empty($downgradeSection['unsuccess'])) {
                    $this->Flash->success('Section ' . count($downgradeSection['success']) . ' have been downgraded successfully but ' . count($downgradeSection['unsuccess']) . '.');
                }
                return $this->redirect(['action' => 'displaySections']);
            } else {
                $this->Flash->error(
                    'Please select Section.',
                    ['element' => 'error_with_link', 'params' => ['link_text' => 'this page', 'link_url' => ['action' => 'displaySections']]]
                );
            }

            $this->request = $this->request->withData('search', true);
            $formateddowngradableSections = null;
            $isbeforesearch = 0;

            if ($this->request->getSession()->check('formateddowngradableSections')) {
                $formateddowngradableSections = $this->request->getSession()->read('formateddowngradableSections');
            }

            $this->set(compact('formateddowngradableSections', 'isbeforesearch'));
        }
    }

    public function displaySectionLessStudents()
    {
        $this->initSearchSections();
        $isbeforesearch = 1;

        $selected_program = $this->request->getData('Section.program_id') ?? array_values($this->program_ids)[0];
        $selected_program_type = $this->request->getData('Section.program_type_id') ?? array_values($this->program_type_ids)[0];
        $program_types_to_look = $this->Sections->getEquivalentProgramTypes($selected_program_type);
        $academicyear = $this->request->getData('Section.academicyear') ?? $this->AcademicYear->currentAcademicYear();

        $this->set(compact('isbeforesearch'));

        $sectionlessStudents_ids = [];

        $selected_acy_exploded = explode('/', $academicyear);
        $previous_academic_year = ($selected_acy_exploded[0] - 1) . '/' . ($selected_acy_exploded[1] - 1);

        $all_departments_sections_created_after_previous_acy_sections = [];
        $all_freshman_sections_created_after_previous_acy_sections = [];

        // debug($previous_academic_year);

        if (!empty($this->request->getData()) && $this->request->getData('search')) {
            $this->initClearSessionFilters();
            $this->initSearchSections();

            $isbeforesearch = 0;

            $program_type_ids = "'" . implode("', '", $program_types_to_look) . "'";
            $department_ids = !empty($this->department_ids) ? "'" . implode("', '", $this->department_ids) . "'" : 0;
            $college_ids = !empty($this->college_ids) ? "'" . implode("', '", $this->college_ids) . "'" : 0;

            // debug($college_ids);
            // debug($department_ids);
            // debug($program_type_ids);

            if ($this->role_id == ROLE_DEPARTMENT) {
                $previous_ac_year_sections = $this->Sections->find('list')
                    ->where([
                        'Sections.department_id' => $this->department_id,
                        'Sections.program_id' => $selected_program,
                        'Sections.program_type_id IN' => $program_types_to_look,
                        'Sections.academicyear LIKE' => $previous_academic_year . '%',
                    ])
                    ->select(['Sections.id', 'Sections.id'])
                    ->toArray();

                // debug($previous_ac_year_sections);

                $selected_ac_year_sections = $this->Sections->find('list')
                    ->where([
                        'Sections.department_id' => $this->department_id,
                        'Sections.program_id' => $selected_program,
                        'Sections.program_type_id IN' => $program_types_to_look,
                        'Sections.academicyear LIKE' => $academicyear . '%',
                    ])
                    ->select(['Sections.id', 'Sections.id'])
                    ->toArray();

                $last_section_of_selected_ac_year_sections = $this->Sections->find()
                    ->where([
                        'Sections.department_id' => $this->department_id,
                        'Sections.program_id' => $selected_program,
                        'Sections.program_type_id IN' => $program_types_to_look,
                        'Sections.academicyear LIKE' => $previous_academic_year . '%',
                    ])
                    ->select(['Sections.id', 'Sections.created'])
                    ->order(['Sections.created' => 'DESC'])
                    ->first();

                // debug($last_section_of_selected_ac_year_sections);

                $all_departments_sections_created_after_previous_acy_sections = !empty($last_section_of_selected_ac_year_sections)
                    ? $this->Sections->find('list')
                        ->where([
                            'Sections.department_id' => $this->department_id,
                            'Sections.program_id' => $selected_program,
                            'Sections.program_type_id IN' => $program_types_to_look,
                            'OR' => [
                                'Sections.academicyear LIKE' => $academicyear . '%',
                                'Sections.id >=' => $last_section_of_selected_ac_year_sections->id,
                                'Sections.created >=' => $last_section_of_selected_ac_year_sections->created,
                            ],
                        ])
                        ->select(['Sections.id', 'Sections.id'])
                        ->toArray()
                    : $this->Sections->find('list')
                        ->where([
                            'Sections.department_id' => $this->department_id,
                            'Sections.program_id' => $selected_program,
                            'Sections.program_type_id IN' => $program_types_to_look,
                            'Sections.academicyear LIKE' => $academicyear . '%',
                        ])
                        ->select(['Sections.id', 'Sections.id'])
                        ->toArray();

                if (!empty($previous_ac_year_sections)) {
                    $sectionlessStudents_ids = $this->Sections->StudentsSections->find()
                        ->distinct(['StudentsSections.student_id'])
                        ->where([
                            'StudentsSections.archive' => 1,
                            'StudentsSections.section_id IN' => $previous_ac_year_sections,
                            'StudentsSections.student_id IN' => $this->Sections->Students->find()
                                ->join([
                                    'creg' => [
                                        'table' => 'course_registrations',
                                        'type' => 'INNER',
                                        'conditions' => ['creg.student_id = Students.id'],
                                    ],
                                ])
                                ->where([
                                    'Students.graduated' => 0,
                                    'Students.department_id IN' => $this->department_ids,
                                    'Students.program_id' => $selected_program,
                                    'Students.program_type_id IN' => $program_types_to_look,
                                    'creg.academic_year' => $previous_academic_year,
                                ])
                                ->group(['creg.academic_year', 'creg.student_id', 'creg.semester'])
                                ->select(['Students.id']),
                        ])
                        ->toArray();
                }
            } elseif ($this->role_id == ROLE_COLLEGE) {
                $previous_ac_year_sections = $this->Sections->find('list')
                    ->where([
                        'Sections.college_id' => $this->college_id,
                        'Sections.department_id IS' => null,
                        'Sections.academicyear LIKE' => $previous_academic_year . '%',
                        'Sections.program_id' => $selected_program,
                        'Sections.program_type_id IN' => $program_types_to_look,
                    ])
                    ->select(['Sections.id', 'Sections.id'])
                    ->toArray();

                $selected_ac_year_sections = $this->Sections->find('list')
                    ->where([
                        'Sections.college_id' => $this->college_id,
                        'Sections.department_id IS' => null,
                        'Sections.academicyear LIKE' => $academicyear . '%',
                        'Sections.program_id' => $selected_program,
                        'Sections.program_type_id IN' => $program_types_to_look,
                    ])
                    ->select(['Sections.id', 'Sections.id'])
                    ->toArray();

                $last_section_of_selected_ac_year_sections = $this->Sections->find()
                    ->where([
                        'Sections.college_id' => $this->college_id,
                        'Sections.department_id IS' => null,
                        'Sections.program_id' => $selected_program,
                        'Sections.program_type_id IN' => $program_types_to_look,
                        'Sections.academicyear LIKE' => $previous_academic_year . '%',
                    ])
                    ->select(['Sections.id', 'Sections.created'])
                    ->order(['Sections.created' => 'DESC'])
                    ->first();

                // debug($last_section_of_selected_ac_year_sections);

                $all_freshman_sections_created_after_previous_acy_sections = !empty($last_section_of_selected_ac_year_sections)
                    ? $this->Sections->find('list')
                        ->where([
                            'Sections.college_id' => $this->college_id,
                            'Sections.department_id IS' => null,
                            'Sections.program_id' => $selected_program,
                            'Sections.program_type_id IN' => $program_types_to_look,
                            'OR' => [
                                'Sections.academicyear LIKE' => $academicyear . '%',
                                'Sections.id >=' => $last_section_of_selected_ac_year_sections->id,
                                'Sections.created >=' => $last_section_of_selected_ac_year_sections->created,
                            ],
                        ])
                        ->select(['Sections.id', 'Sections.id'])
                        ->toArray()
                    : $this->Sections->find('list')
                        ->where([
                            'Sections.college_id' => $this->college_id,
                            'Sections.department_id IS' => null,
                            'Sections.program_id' => $selected_program,
                            'Sections.program_type_id IN' => $program_types_to_look,
                            'Sections.academicyear LIKE' => $academicyear . '%',
                        ])
                        ->select(['Sections.id', 'Sections.id'])
                        ->toArray();

                if (!empty($previous_ac_year_sections)) {
                    $sectionlessStudents_ids = $this->Sections->StudentsSections->find()
                        ->distinct(['StudentsSections.student_id'])
                        ->where([
                            'StudentsSections.archive' => 1,
                            'StudentsSections.section_id IN' => $previous_ac_year_sections,
                            'StudentsSections.student_id IN' => $this->Sections->Students->find()
                                ->join([
                                    'creg' => [
                                        'table' => 'course_registrations',
                                        'type' => 'INNER',
                                        'conditions' => ['creg.student_id = Students.id'],
                                    ],
                                ])
                                ->where([
                                    'Students.college_id IN' => $this->college_ids,
                                    'Students.department_id IS' => null,
                                    'Students.graduated' => 0,
                                    'Students.program_id' => $selected_program,
                                    'Students.program_type_id IN' => $program_types_to_look,
                                    'OR' => [
                                        'creg.year_level_id IS' => null,
                                        'creg.year_level_id' => 0,
                                        'creg.year_level_id' => '',
                                    ],
                                    'creg.academic_year' => $previous_academic_year,
                                ])
                                ->group(['creg.academic_year', 'creg.student_id', 'creg.semester'])
                                ->select(['Students.id']),
                        ])
                        ->toArray();
                }
            }

            $sectionless_students_last_sections_details = [];

            if (!empty($sectionlessStudents_ids)) {
                foreach ($sectionlessStudents_ids as $in => &$v) {
                    $is_section_less = !empty($all_freshman_sections_created_after_previous_acy_sections)
                        ? $this->Sections->StudentsSections->find()
                            ->where(['StudentsSections.archive' => 0, 'StudentsSections.student_id' => $v->student_id, 'StudentsSections.section_id IN' => $all_freshman_sections_created_after_previous_acy_sections])
                            ->count()
                        : (!empty($all_departments_sections_created_after_previous_acy_sections)
                            ? $this->Sections->StudentsSections->find()
                                ->where(['StudentsSections.archive' => 0, 'StudentsSections.student_id' => $v->student_id, 'StudentsSections.section_id IN' => $all_departments_sections_created_after_previous_acy_sections])
                                ->count()
                            : $this->Sections->StudentsSections->find()
                                ->where(['StudentsSections.archive' => 0, 'StudentsSections.student_id' => $v->student_id])
                                ->count());

                    if ($is_section_less > 0) {
                        unset($sectionlessStudents_ids[$in]);
                    } else {
                        $have_any_section_assignments_in_later_acys = (!empty($all_departments_sections_created_after_previous_acy_sections) || !empty($all_freshman_sections_created_after_previous_acy_sections))
                            ? $this->Sections->StudentsSections->find()
                                ->where(['StudentsSections.section_id IN' => (!empty($all_departments_sections_created_after_previous_acy_sections) ? $all_departments_sections_created_after_previous_acy_sections : $all_freshman_sections_created_after_previous_acy_sections), 'StudentsSections.student_id' => $v->student_id])
                                ->count()
                            : 0;

                        if ($have_any_section_assignments_in_later_acys > 0) {
                            unset($sectionlessStudents_ids[$in]);
                        } else {
                            $exclude = $this->Sections->dropOutWithDrawAfterLastRegistrationNotReadmittedExcludeFromSectionless($v->student_id, $academicyear);
                            if ($exclude == 1) {
                                unset($sectionlessStudents_ids[$in]);
                            }
                        }
                    }
                }

                $sectionless_students_last_sections_details = $this->Sections->getSectionlessStudentsLastSections($sectionlessStudents_ids);
            }

            $this->set(compact('sectionless_students_last_sections_details', 'isbeforesearch'));
        }
    }

    protected function checkTheRecordInArchive(?int $section_id = null, ?int $student_id = null): ?int
    {
        $studentSection_id = $this->Sections->StudentsSections->find()
            ->select(['StudentsSections.id'])
            ->where([
                'StudentsSections.student_id' => $student_id,
                'StudentsSections.section_id' => $section_id,
                'StudentsSections.archive' => 1,
            ])
            ->first()
            ->id ?? null;

        return $studentSection_id;
    }

    public function unAssignedSummeries(string $selectedAcademicYear)
    {
        $this->viewBuilder()->setLayout('ajax');
        // debug($this->request->data);

        $academicYear = !empty($selectedAcademicYear) ? str_replace("-", "/", $selectedAcademicYear) : $this->AcademicYear->currentAcademicYear();
        $sselectedAcademicYear = $academicYear;

        $selectedProgram = array_values($this->program_ids)[0] ?? null;
        $selectedProgramType = array_values($this->program_type_ids)[0] ?? null;

        $curriculums = [];

        if ($this->role_id == ROLE_DEPARTMENT) {
            $curriculums = TableRegistry::getTableLocator()->get('Curriculums')->find('list')
                ->where([
                    'Curriculums.department_id IN' => $this->department_ids,
                    'Curriculums.program_id' => $selectedProgram,
                    'Curriculums.registrar_approved' => 1,
                    'Curriculums.active' => 1,
                ])
                ->select(['Curriculums.id', 'Curriculums.curriculum_detail'])
                ->order(['Curriculums.program_id' => 'ASC', 'Curriculums.created' => 'DESC'])
                ->toArray();

            $yearLevelsss = $this->Sections->YearLevels->find('list')
                ->where(['YearLevels.department_id IN' => $this->department_ids, 'YearLevels.name IN' => $this->year_levels])
                ->toArray();

            if (!empty($yearLevelsss)) {
                $selectedYearLevelId = array_keys($yearLevelsss)[0];
                $selectedYearLevelName = array_values($yearLevelsss)[0];
            } else {
                $selectedYearLevelId = null;
                $selectedYearLevelName = null;
            }

            $selectedCurriculumID = !empty($curriculums) ? array_keys($curriculums)[0] : '%';
            $selectedCurriculumName = !empty($curriculums) ? array_values($curriculums)[0] : null;
        } else {
            $selectedYearLevelId = null;
            $selectedYearLevelName = null;
            $selectedCurriculumID = null;
            $selectedCurriculumName = null;
        }

        $summary_data = $this->Sections->getSectionlessStudentSummary($academicYear, $this->college_id, $this->department_id, $this->role_id);
        $curriculum_unattached_student_count = $this->Sections->getCurriculumUnattachedStudentSummary($academicYear, $this->college_id, $this->department_id, $this->role_id);

        $collegename = $this->Sections->Colleges->find()
            ->select(['name'])
            ->where(['Colleges.id' => $this->college_id])
            ->first()
            ->name;

        $departmentname = $this->Sections->Departments->find()
            ->select(['name'])
            ->where(['Departments.id' => $this->department_id])
            ->first()
            ->name;

        $departmentshortname = $this->Sections->Departments->find()
            ->select(['shortname'])
            ->where(['Departments.id' => $this->department_id])
            ->first()
            ->shortname;

        $yearLevels = $this->Sections->YearLevels->find('list')
            ->where(['YearLevels.department_id' => $this->department_id])
            ->toArray();

        $programss = $this->Sections->Programs->find('list')
            ->where(['Programs.id IN' => $this->program_ids, 'Programs.active' => 1])
            ->toArray();

        $program_typess = $this->Sections->ProgramTypes->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids, 'ProgramTypes.active' => 1])
            ->toArray();

        $thisacademicyear = $academicYear;

        $GCyear = substr($academicYear, 0, 4);
        $GCmonth = date('n');
        $GCday = date('j');

        if ($GCmonth >= 9) {
            $GCyear = $GCyear;
        } else {
            $GCyear = $GCyear + 1;
        }

        $ETY = $this->EthiopicDateTime->GetEthiopicYear($GCday, $GCmonth, $GCyear);

        $FixedSectionName = $departmentshortname . $ETY;

        $this->set(compact(
            'departmentname',
            'yearLevels',
            'collegename',
            'programss',
            'program_typess',
            'summary_data',
            'FixedSectionName',
            'thisacademicyear',
            'sselectedAcademicYear',
            'curriculum_unattached_student_count',
            'selectedYearLevelName',
            'selectedCurriculumName'
        ));
    }

    public function restoreStudentSection(?int $section_id = null, ?int $student_id = null, int $archive_status = 1)
    {
        if (!empty($student_id)) {
            $student_number = $this->Sections->Students->find()
                ->select(['studentnumber'])
                ->where(['Students.id' => $student_id])
                ->first()
                ->studentnumber;

            if (empty($section_id) || empty($student_number)) {
                $this->Flash->error(
                    'Invalid id for section or/and student.',
                    ['element' => 'error_with_link', 'params' => ['link_text' => 'student academic profile',
                        'link_url' => ['controller' => 'Students', 'action' => 'studentAcademicProfile', $student_id]]]
                );
                return $this->redirect(['controller' => 'Students', 'action' => 'studentAcademicProfile', $student_id]);
            }

            $section_name = $this->Sections->find()
                ->select(['name'])
                ->where(['Sections.id' => $section_id])
                ->first()
                ->name;

            if (!empty($section_name)) {
                if (!$archive_status) {
                    $activeStudentSections = TableRegistry::getTableLocator()->get('StudentsSections')->find('list')
                        ->where(['StudentsSections.student_id' => $student_id, 'StudentsSections.archive' => 0])
                        ->select(['StudentsSections.id', 'StudentsSections.id'])
                        ->toArray();

                    if (!empty($activeStudentSections)) {
                        /*
                        TableRegistry::getTableLocator()->get('StudentsSections')->updateAll(
                            ['StudentsSections.archive' => 1],
                            ['StudentsSections.id IN' => $activeStudentSections]
                        );*/
                        TableRegistry::getTableLocator()->get('StudentsSections')->updateAll(
                            ['archive' => 1],
                            ['id IN' => $activeStudentSections]
                        );
                    }
                }

                $updateStudentSectionIfExists = TableRegistry::getTableLocator()->get('StudentsSections')->find()
                    ->select(['StudentsSections.id'])
                    ->where(['StudentsSections.student_id' => $student_id, 'StudentsSections.section_id' => $section_id])
                    ->first()
                    ->id ?? null;

                $restoreSection = $this->Sections->StudentsSections->newEntity([
                    'id' => $updateStudentSectionIfExists,
                    'section_id' => $section_id,
                    'student_id' => $student_id,
                    'archive' => $archive_status,
                ]);

                if ($this->Sections->StudentsSections->save($restoreSection, ['validate' => false])) {
                    $this->Flash->success("$student_number restored to $section_name section.");
                } else {
                    $this->Flash->error(
                        "$student_number can not be restored to $section_name section.",
                        ['element' => 'error_with_link', 'params' => ['link_text' => 'student academic profile',
                            'link_url' => ['controller' => 'Students', 'action' => 'studentAcademicProfile', $student_id]]]
                    );
                }

                return $this->redirect(['controller' => 'Students', 'action' => 'studentAcademicProfile', $student_id]);
            }
        }
    }
}
?>

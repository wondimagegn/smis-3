<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class PlacementEntranceExamResultEntriesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('placement_entrance_exam_result_entries');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('AcceptedStudents', [
            'foreignKey' => 'accepted_student_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('PlacementRoundParticipants', [
            'foreignKey' => 'placement_round_participant_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->numeric('accepted_student_id', 'Accepted student ID must be numeric')
            ->notEmptyString('accepted_student_id')
            ->numeric('student_id', 'Student ID must be numeric')
            ->notEmptyString('student_id')
            ->numeric('result', 'Result must be numeric')
            ->notEmptyString('result')
            ->numeric('placement_round_participant_id', 'Placement round participant ID must be numeric')
            ->notEmptyString('placement_round_participant_id');

        return $validator;
    }

    public function getSelectedSection(array $data): array
    {
        $appliedUnitClg = explode('c~', $data['Search']['applied_for']);
        $appliedUnitDept = isset($appliedUnitClg[1]) ? [] : explode('d~', $data['Search']['applied_for']);
        $currentUnitClg = explode('c~', $data['Search']['current_unit']);
        $currentUnitDept = isset($currentUnitClg[1]) ? [] : explode('d~', $data['Search']['current_unit']);
        $options = [
            'order' => ['Sections.id' => 'ASC', 'Sections.name' => 'ASC'],
            'contain' => ['YearLevels', 'Colleges', 'Departments'],
            'conditions' => []
        ];

        if (!empty($currentUnitClg[1])) {
            $options['conditions'][] = [
                'Sections.college_id' => $currentUnitClg[1],
                'Sections.department_id IS NULL'
            ];
        } elseif (!empty($currentUnitDept[1])) {
            $options['conditions'][] = ['Sections.department_id' => $currentUnitDept[1]];
        } else {
            if (!empty($appliedUnitClg[1])) {
                $options['conditions'][] = [
                    'Sections.college_id' => $appliedUnitClg[1],
                    'Sections.department_id IS NULL'
                ];
            } elseif (!empty($appliedUnitDept[1])) {
                $options['conditions'][] = ['Sections.department_id' => $appliedUnitDept[1]];
            }
        }

        $options['conditions'][] = [
            'Sections.program_id' => $data['Search']['program_id'],
            'Sections.program_type_id' => $data['Search']['program_type_id'],
            'Sections.academicyear' => $data['Search']['academic_year']
        ];

        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $sections = $sectionsTable->find('all', $options)
            ->disableHydration()
            ->all()
            ->toArray();

        $sectionF = [];
        if (!empty($sections)) {
            $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
            foreach ($sections as $v) {
                $studentCount = $studentsSectionsTable->find('count')
                    ->where(['StudentsSections.section_id' => $v['Section']['id'], 'StudentsSections.archive' => 0])
                    ->disableHydration()
                    ->count();

                if ($studentCount) {
                    $yearLevel = $v['YearLevel']['name'] ?? 'Pre/1st';
                    $sectionF[$yearLevel][$v['Section']['id']] = $v['Section']['name'];
                }
            }
        }

        $sec = [];
        foreach ($sectionF as $k => $kv) {
            $sec[$k] = [0 => 'All'] + $kv;
        }

        return $sec;
    }

    public function getSelectedStudent(array $data): array
    {
        $processedStudents = [];
        $appliedUnitClg = explode('c~', $data['Search']['applied_for']);
        $appliedUnitDept = explode('d~', $data['Search']['applied_for']);
        $foreignKey = !empty($appliedUnitClg[1]) ? $appliedUnitClg[1] : (!empty($appliedUnitDept[1]) ? $appliedUnitDept[1] : null);

        if (!empty($data['Search']['placement_round_participant_id'])) {
            $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
            $placementRoundParticipantSelected = $placementRoundParticipantsTable->find('first')
                ->where(['PlacementRoundParticipants.id' => $data['Search']['placement_round_participant_id']])
                ->disableHydration()
                ->first();

            $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
            $isPlacementDone = $placementParticipatingStudentsTable->find('count')
                ->where(['PlacementParticipatingStudents.placement_round_participant_id' => $placementRoundParticipantSelected['PlacementRoundParticipant']['id']])
                ->disableHydration()
                ->count();

            $options = [
                'contain' => [
                    'Students' => ['order' => ['Students.first_name' => 'ASC']],
                    'Sections'
                ],
                'conditions' => [
                    'StudentsSections.section_id' => $data['Search']['section_id'] == 0 ? $this->getAllSectionIds($data) : $data['Search']['section_id'],
                    'StudentsSections.archive' => 0
                ]
            ];

            $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
            $students = $studentsSectionsTable->find('all', $options)
                ->disableHydration()
                ->all()
                ->toArray();

            $count = 0;
            foreach ($students as $v) {
                $resultExisted = $this->find('first')
                    ->where([
                        'PlacementEntranceExamResultEntries.placement_round_participant_id' => $data['Search']['placement_round_participant_id'],
                        'PlacementEntranceExamResultEntries.student_id' => $v['Student']['id'],
                        'PlacementEntranceExamResultEntries.accepted_student_id' => $v['Student']['accepted_student_id']
                    ])
                    ->disableHydration()
                    ->first();

                $processedStudents[$count]['Student'] = $v['Student'];
                $processedStudents[$count]['Student']['placement_round_participant_id'] = $data['Search']['placement_round_participant_id'];

                if (!empty($resultExisted['PlacementEntranceExamResultEntry'])) {
                    $processedStudents[$count]['EntranceResult'] = $resultExisted['PlacementEntranceExamResultEntry'];
                }

                $processedStudents[$count]['PlacementStatus'] = $isPlacementDone;
                $count++;
            }
        }

        return $processedStudents;
    }

    public function getStudentForPreferenceEntry(array $data): array
    {
        $processedStudents = [];
        $appliedUnitClg = explode('c~', $data['Search']['applied_for']);
        $appliedUnitDept = explode('d~', $data['Search']['applied_for']);
        $foreignKey = !empty($appliedUnitClg[1]) ? $appliedUnitClg[1] : (!empty($appliedUnitDept[1]) ? $appliedUnitDept[1] : null);

        if (!empty($data['Search']['applied_for'])) {
            $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
            $placementRoundParticipantSelected = $placementRoundParticipantsTable->find('first')
                ->where([
                    'PlacementRoundParticipants.applied_for' => $data['Search']['applied_for'],
                    'PlacementRoundParticipants.program_id' => $data['Search']['program_id'],
                    'PlacementRoundParticipants.academic_year' => $data['Search']['academic_year'],
                    'PlacementRoundParticipants.placement_round' => $data['Search']['placement_round']
                ])
                ->disableHydration()
                ->first();

            $placementRoundParticipantUnitsList = $placementRoundParticipantsTable->find('list')
                ->where([
                    'PlacementRoundParticipants.applied_for' => $data['Search']['applied_for'],
                    'PlacementRoundParticipants.program_id' => $data['Search']['program_id'],
                    'PlacementRoundParticipants.program_type_id' => $data['Search']['program_type_id'],
                    'PlacementRoundParticipants.academic_year' => $data['Search']['academic_year'],
                    'PlacementRoundParticipants.placement_round' => $data['Search']['placement_round']
                ])
                ->select(['id', 'name'])
                ->disableHydration()
                ->toArray();

            $semester = $placementRoundParticipantSelected['PlacementRoundParticipant']['semester'] ?? (
            $data['Search']['placement_round'] == 1 ? 'I' :
                ($data['Search']['placement_round'] == 2 || $data['Search']['placement_round'] == 3 ? 'II' : 'I')
            );

            if (!empty($placementRoundParticipantUnitsList)) {
                $listIds = array_keys($placementRoundParticipantUnitsList);
                $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
                $isPlacementDone = $placementParticipatingStudentsTable->find('count')
                    ->where([
                        'PlacementParticipatingStudents.placement_round_participant_id IN' => $listIds,
                        'PlacementParticipatingStudents.status' => 1
                    ])
                    ->disableHydration()
                    ->count();

                $placementDeadlinesTable = TableRegistry::getTableLocator()->get('PlacementDeadlines');
                $preferenceDeadline = $placementDeadlinesTable->find('first')
                    ->where([
                        'PlacementDeadlines.program_id' => $data['Search']['program_id'],
                        'PlacementDeadlines.applied_for' => $data['Search']['applied_for'],
                        'PlacementDeadlines.program_type_id' => $data['Search']['program_type_id'],
                        'PlacementDeadlines.academic_year LIKE' => $data['Search']['academic_year'] . '%',
                        'PlacementDeadlines.placement_round' => $data['Search']['placement_round']
                    ])
                    ->disableHydration()
                    ->first();

                $isDeadlinePassed = 0;
                $deadline = '';
                if (!empty($preferenceDeadline)) {
                    $deadline = $preferenceDeadline['PlacementDeadline']['deadline'];
                    $daysAllowed = is_numeric(DAYS_ALLOWED_TO_ADD_PREFERENCE_ON_BEHALF_OF_STUDENTS_AFTER_DEADLINE) && DAYS_ALLOWED_TO_ADD_PREFERENCE_ON_BEHALF_OF_STUDENTS_AFTER_DEADLINE > 0 ?
                        DAYS_ALLOWED_TO_ADD_PREFERENCE_ON_BEHALF_OF_STUDENTS_AFTER_DEADLINE : 0;
                    $date_now = date("Y-m-d H:i:s", strtotime("-{$daysAllowed} day"));
                    if ($deadline < $date_now) {
                        $isDeadlinePassed = 1;
                    }
                }

                $options = [
                    'contain' => [
                        'Students' => [
                            'order' => ['Students.first_name' => 'ASC'],
                            'AcceptedStudents'
                        ],
                        'Sections'
                    ],
                    'conditions' => []
                ];

                if ($data['Search']['section_id'] == 0) {
                    $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
                    if (!empty($appliedUnitClg[1]) && is_numeric($appliedUnitClg[1])) {
                        $options['conditions'][] = ['Students.department_id IS NULL'];
                        $collegeSectionIDs = $sectionsTable->find('list')
                            ->where([
                                'Sections.college_id' => $appliedUnitClg[1],
                                'Sections.department_id IS NULL',
                                'Sections.program_id' => $data['Search']['program_id'],
                                'Sections.program_type_id' => $data['Search']['program_type_id'],
                                'Sections.academicyear' => $data['Search']['academic_year'],
                                'Sections.archive' => 0
                            ])
                            ->select(['id'])
                            ->disableHydration()
                            ->toArray();
                        $options['conditions'][] = ['StudentsSections.section_id IN' => !empty($collegeSectionIDs) ? $collegeSectionIDs : $this->getAllSectionIds($data), 'StudentsSections.archive' => 0];
                    } elseif (!empty($appliedUnitDept[1]) && is_numeric($appliedUnitDept[1])) {
                        $deptSectionIDs = $sectionsTable->find('list')
                            ->where([
                                'Sections.department_id' => $appliedUnitDept[1],
                                'Sections.program_id' => $data['Search']['program_id'],
                                'Sections.program_type_id' => $data['Search']['program_type_id'],
                                'Sections.academicyear' => $data['Search']['academic_year'],
                                'Sections.archive' => 0
                            ])
                            ->select(['id'])
                            ->disableHydration()
                            ->toArray();
                        $options['conditions'][] = ['StudentsSections.section_id IN' => !empty($deptSectionIDs) ? $deptSectionIDs : $this->getAllSectionIds($data), 'StudentsSections.archive' => 0];
                    } else {
                        $options['conditions'][] = ['StudentsSections.section_id IN' => $this->getAllSectionIds($data)];
                    }
                } else {
                    $options['conditions'][] = ['StudentsSections.section_id' => $data['Search']['section_id'], 'StudentsSections.archive' => 0];
                }

                $placementResultSettingsTable = TableRegistry::getTableLocator()->get('PlacementResultSettings');
                $placementSettings = $placementResultSettingsTable->find()
                    ->where([
                        'PlacementResultSettings.applied_for' => $data['Search']['applied_for'],
                        'PlacementResultSettings.round' => $data['Search']['placement_round'],
                        'PlacementResultSettings.academic_year' => $data['Search']['academic_year'],
                        'PlacementResultSettings.program_id' => $data['Search']['program_id'],
                        'PlacementResultSettings.program_type_id' => $data['Search']['program_type_id']
                    ])
                    ->disableHydration()
                    ->all()
                    ->toArray();

                $resultType = [];
                $entranceMax = ENTRANCEMAXIMUM;
                $freshmanMax = FRESHMANMAXIMUM;
                $preparatoryMax = PREPARATORYMAXIMUM;
                $freshmanResultPercent = DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT;
                $preparatoryResultPercent = DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT;
                $entranceResultPercent = DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT;
                $isEntranceSet = false;
                $isFreshmanSet = false;
                $isPreparatorySet = false;

                foreach ($placementSettings as $pv) {
                    if (!empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && $pv['PlacementResultSetting']['percent'] > 0) {
                        $resultType[$pv['PlacementResultSetting']['result_type']] = $pv['PlacementResultSetting']['percent'];

                        if ($pv['PlacementResultSetting']['result_type'] == 'freshman_result') {
                            $freshmanMax = !empty($pv['PlacementResultSetting']['max_result']) && is_numeric($pv['PlacementResultSetting']['max_result']) && $pv['PlacementResultSetting']['max_result'] > 0 ? (int) $pv['PlacementResultSetting']['max_result'] : FRESHMANMAXIMUM;
                            $isFreshmanSet = !empty($pv['PlacementResultSetting']['max_result']) && is_numeric($pv['PlacementResultSetting']['max_result']) && $pv['PlacementResultSetting']['max_result'] > 0;
                            $freshmanResultPercent = !empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && $pv['PlacementResultSetting']['percent'] > 0 ? (int) $pv['PlacementResultSetting']['percent'] : DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT;
                        } elseif ($pv['PlacementResultSetting']['result_type'] == 'EHEECE_total_results') {
                            $preparatoryMax = !empty($pv['PlacementResultSetting']['max_result']) && is_numeric($pv['PlacementResultSetting']['max_result']) && $pv['PlacementResultSetting']['max_result'] > 0 ? (int) $pv['PlacementResultSetting']['max_result'] : PREPARATORYMAXIMUM;
                            $isPreparatorySet = !empty($pv['PlacementResultSetting']['max_result']) && is_numeric($pv['PlacementResultSetting']['max_result']) && $pv['PlacementResultSetting']['max_result'] > 0;
                            $preparatoryResultPercent = !empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && $pv['PlacementResultSetting']['percent'] > 0 ? (int) $pv['PlacementResultSetting']['percent'] : DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT;
                        } elseif ($pv['PlacementResultSetting']['result_type'] == 'entrance_result') {
                            $entranceMax = !empty($pv['PlacementResultSetting']['max_result']) && is_numeric($pv['PlacementResultSetting']['max_result']) && $pv['PlacementResultSetting']['max_result'] > 0 ? (int) $pv['PlacementResultSetting']['max_result'] : ENTRANCEMAXIMUM;
                            $isEntranceSet = !empty($pv['PlacementResultSetting']['max_result']) && is_numeric($pv['PlacementResultSetting']['max_result']) && $pv['PlacementResultSetting']['max_result'] > 0;
                            $entranceResultPercent = !empty($pv['PlacementResultSetting']['percent']) && is_numeric($pv['PlacementResultSetting']['percent']) && $pv['PlacementResultSetting']['percent'] > 0 ? (int) $pv['PlacementResultSetting']['percent'] : DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT;
                        }
                    }
                }

                $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
                $students = $studentsSectionsTable->find('all', $options)
                    ->disableHydration()
                    ->all()
                    ->toArray();

                $count = 0;
                $processedStudents['ParticipantUnit'] = $placementRoundParticipantUnitsList;
                $prfOrder = 1;
                foreach ($placementRoundParticipantUnitsList as $k => $v) {
                    $processedStudents['ParticipantUnitPreferenceOrder'][$prfOrder] = $prfOrder;
                    $prfOrder++;
                }

                if (!empty($students)) {
                    $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                    $placementPreferencesTable = TableRegistry::getTableLocator()->get('PlacementPreferences');
                    foreach ($students as &$v) {
                        $freshManresult = $studentExamStatusesTable->find('first')
                            ->where([
                                'StudentExamStatuses.student_id' => $v['Student']['id'],
                                'StudentExamStatuses.academic_year' => $data['Search']['academic_year'],
                                'StudentExamStatuses.semester' => $semester
                            ])
                            ->select(['StudentExamStatuses.sgpa', 'StudentExamStatuses.cgpa'])
                            ->order(['StudentExamStatuses.academic_year' => 'DESC', 'StudentExamStatuses.semester' => 'DESC', 'StudentExamStatuses.id' => 'DESC'])
                            ->disableHydration()
                            ->first();

                        if ($isFreshmanSet && !empty($freshManresult['StudentExamStatus']['cgpa']) && is_numeric($freshManresult['StudentExamStatus']['cgpa']) && $freshManresult['StudentExamStatus']['cgpa'] > DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT) {
                            $v['Student']['AcceptedStudent']['freshman_result'] = (($freshManresult['StudentExamStatus']['cgpa'] / $freshmanMax) * $freshmanResultPercent);
                        } elseif (!empty($freshManresult['StudentExamStatus']['cgpa']) && is_numeric($freshManresult['StudentExamStatus']['cgpa']) && $freshManresult['StudentExamStatus']['cgpa'] > DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT) {
                            $v['Student']['AcceptedStudent']['freshman_result'] = (($freshManresult['StudentExamStatus']['cgpa'] / $freshmanMax) * $freshmanResultPercent);
                        }

                        if ($isPreparatorySet && !empty($v['Student']['AcceptedStudent']['EHEECE_total_results']) && is_numeric($v['Student']['AcceptedStudent']['EHEECE_total_results'])) {
                            $v['Student']['AcceptedStudent']['EHEECE_total_results'] = (($v['Student']['AcceptedStudent']['EHEECE_total_results'] / $preparatoryMax) * $preparatoryResultPercent);
                        } elseif (is_numeric($v['Student']['AcceptedStudent']['EHEECE_total_results']) && $v['Student']['AcceptedStudent']['EHEECE_total_results'] >= 0) {
                            $v['Student']['AcceptedStudent']['EHEECE_total_results'] = (($v['Student']['AcceptedStudent']['EHEECE_total_results'] / $preparatoryMax) * $preparatoryResultPercent);
                        }

                        $resultExisted = $this->find('first')
                            ->where([
                                'PlacementEntranceExamResultEntries.placement_round_participant_id IN' => $listIds,
                                'PlacementEntranceExamResultEntries.student_id' => $v['Student']['id'],
                                'PlacementEntranceExamResultEntries.accepted_student_id' => $v['Student']['accepted_student_id']
                            ])
                            ->order(['PlacementEntranceExamResultEntries.result' => 'DESC'])
                            ->disableHydration()
                            ->first();

                        if ($isEntranceSet && !empty($resultExisted['PlacementEntranceExamResultEntry']['result']) && is_numeric($resultExisted['PlacementEntranceExamResultEntry']['result']) && $resultExisted['PlacementEntranceExamResultEntry']['result'] >= 0) {
                            $v['Student']['AcceptedStudent']['entrance_result'] = (($resultExisted['PlacementEntranceExamResultEntry']['result'] / $entranceMax) * $entranceResultPercent);
                        } elseif (!empty($resultExisted['PlacementEntranceExamResultEntry']['result']) && is_numeric($resultExisted['PlacementEntranceExamResultEntry']['result']) && $resultExisted['PlacementEntranceExamResultEntry']['result'] >= 0) {
                            $v['Student']['AcceptedStudent']['entrance_result'] = (($resultExisted['PlacementEntranceExamResultEntry']['result'] / $entranceMax) * $entranceResultPercent);
                        }

                        $preferenceDetails = $placementPreferencesTable->find()
                            ->where([
                                'PlacementPreferences.placement_round_participant_id IN' => $listIds,
                                'PlacementPreferences.student_id' => $v['Student']['id'],
                                'PlacementPreferences.accepted_student_id' => $v['Student']['accepted_student_id'],
                                'PlacementPreferences.academic_year' => $data['Search']['academic_year'],
                                'PlacementPreferences.round' => $data['Search']['placement_round']
                            ])
                            ->disableHydration()
                            ->all()
                            ->toArray();

                        if ($data['Search']['include'] == 1 && !empty($resultExisted['PlacementEntranceExamResultEntry'])) {
                            if ($data['Search']['only_with_status'] == 1 && !empty($freshManresult['StudentExamStatus'])) {
                                $processedStudents['Student'][$count] = $this->buildStudentData($v, $preferenceDetails, $freshManresult, $resultExisted, $isPlacementDone, $deadline, $isDeadlinePassed);
                            } else {
                                $processedStudents['Student'][$count] = $this->buildStudentData($v, $preferenceDetails, $freshManresult, $resultExisted, $isPlacementDone, $deadline, $isDeadlinePassed);
                            }
                        } elseif ($data['Search']['include'] == 0) {
                            if ($data['Search']['only_with_status'] == 1 && !empty($freshManresult['StudentExamStatus'])) {
                                $processedStudents['Student'][$count] = $this->buildStudentData($v, $preferenceDetails, $freshManresult, $isEntranceSet ? $resultExisted : [], $isPlacementDone, $deadline, $isDeadlinePassed);
                            } else {
                                $processedStudents['Student'][$count] = $this->buildStudentData($v, $preferenceDetails, $freshManresult, $isEntranceSet ? $resultExisted : [], $isPlacementDone, $deadline, $isDeadlinePassed);
                            }
                        }

                        $count++;
                    }
                }
            }
        }

        return $processedStudents;
    }

    protected function buildStudentData($student, $preferenceDetails, $freshManresult, $resultExisted, $isPlacementDone, $deadline, $isDeadlinePassed): array
    {
        $data = [
            'Student' => $student['Student'],
            'PlacementPreference' => $preferenceDetails,
            'Status' => !empty($freshManresult['StudentExamStatus']) ? $freshManresult['StudentExamStatus'] : [],
            'EntranceResult' => !empty($resultExisted['PlacementEntranceExamResultEntry']) ? $resultExisted['PlacementEntranceExamResultEntry'] : [],
            'PlacementStatus' => $isPlacementDone,
            'Deadline' => $deadline,
            'DeadlinePassed' => $isDeadlinePassed
        ];

        return $data;
    }

    public function getAllSectionIds(array $data): array
    {
        $appliedUnitClg = explode('c~', $data['Search']['applied_for']);
        $appliedUnitDept = isset($appliedUnitClg[1]) ? [] : explode('d~', $data['Search']['applied_for']);
        $currentUnitClg = explode('c~', $data['Search']['current_unit']);
        $currentUnitDept = isset($currentUnitClg[1]) ? [] : explode('d~', $data['Search']['current_unit']);
        $options = [
            'order' => ['Sections.id' => 'ASC', 'Sections.name' => 'ASC'],
            'contain' => ['YearLevels', 'Colleges', 'Departments'],
            'conditions' => [],
            'fields' => ['Sections.id']
        ];

        if (!empty($currentUnitClg[1])) {
            $options['conditions'][] = [
                'Sections.college_id' => $currentUnitClg[1],
                'OR' => [
                    'Sections.department_id IS NULL',
                    'Sections.department_id = 0',
                    'Sections.department_id = ""'
                ]
            ];
        } elseif (!empty($currentUnitDept[1])) {
            $options['conditions'][] = ['Sections.department_id' => $currentUnitDept[1]];
        } else {
            if (!empty($appliedUnitClg[1])) {
                $options['conditions'][] = [
                    'Sections.college_id' => $appliedUnitClg[1],
                    'OR' => [
                        'Sections.department_id IS NULL',
                        'Sections.department_id = 0',
                        'Sections.department_id = ""'
                    ]
                ];
            } elseif (!empty($appliedUnitDept[1])) {
                $options['conditions'][] = ['Sections.department_id' => $appliedUnitDept[1]];
            }
        }

        $options['conditions'][] = [
            'Sections.program_id' => $data['Search']['program_id'],
            'Sections.program_type_id' => $data['Search']['program_type_id'],
            'Sections.academicyear' => $data['Search']['academic_year'],
            'Sections.archive' => 0
        ];

        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $sections = $sectionsTable->find('list', $options)
            ->disableHydration()
            ->toArray();

        return $sections;
    }
}

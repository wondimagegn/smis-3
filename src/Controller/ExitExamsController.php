<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;

class ExitExamsController extends AppController
{

    public $name = 'ExitExams';
    public $menuOptions = array(
        'parent' => 'graduation',
        //'exclude' => array('index'),
        'weight' => 9,
        'alias' => array(
            'add' => 'Add Exit Exam Result',
            'index' => 'List Exit Exam Results',
            'import_exit_exam_results' => 'Import Exit Exam Results'
        )
    );

    public $paginate = array();

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

        $this->viewBuilder()->setHelpers([
            'DatePicker',
            'Media.Media',
            'Xls'
        ]);
    }

    public function __init_search()
    {

        if (!empty($this->request->data['Search'])) {
            $search_session = $this->request->data['Search'];
            $this->Session->write('search_data', $search_session);
        } else {
            $search_session = $this->Session->read('search_data');
            $this->request->data['Search'] = $search_session;
        }
    }

    public function search()
    {

        $this->__init_search();

        $url['action'] = 'index';

        if (!empty($this->request->data)) {
            foreach ($this->request->data as $k => $v) {
                foreach ($v as $kk => $vv) {
                    if (is_array($vv)) {
                        foreach ($vv as $kkk => $vvv) {
                            $url[$k . '.' . $kk . '.' . $kkk] = str_replace('/', '-', trim($vvv));
                        }
                    } else {
                        $url[$k . '.' . $kk] = str_replace('/', '-', trim($vv));
                    }
                }
            }
        }

        return $this->redirect($url, null, true);
    }


    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow(
            'search'
        //'import_exit_exam_results'
        );
    }

    public function index()
    {

        $section_id = null;
        $program_id = 1;
        $program_type_id = 1;
        $sections = array();
        $exam_date_selected = null;
        $page = 1;
        $name_or_id = '';
        $limit = 100;

        $options = array();

        //$this->__init_search();

        if (isset($this->passedArgs)) {
            debug($this->passedArgs);
            if (isset($this->passedArgs['Search.page'])) {
                $page = $this->request->data['Search']['page'] = $this->passedArgs['Search.page'];
            }

            if (isset($this->passedArgs['Search.department_id'])) {
                $this->request->data['Search']['department_id'] = $this->passedArgs['Search.department_id'];
            }

            if (isset($this->passedArgs['Search.section_id'])) {
                /* $section_id =  */
                $this->request->data['Search']['section_id'] = $this->passedArgs['Search.section_id'];
            }

            if (isset($this->passedArgs['Search.name_or_id'])) {
                $name_or_id = $this->request->data['Search']['name_or_id'] = str_replace(
                    '-',
                    '/',
                    trim($this->passedArgs['Search.name_or_id'])
                );
            }

            if (isset($this->passedArgs['Search.program_id'])) {
                $program_id = $this->request->data['Search']['program_id'] = $this->passedArgs['Search.program_id'];
            }

            if (isset($this->passedArgs['Search.program_type_id'])) {
                $program_type_id = $this->request->data['Search']['program_type_id'] = $this->passedArgs['Search.program_type_id'];
            }

            if (isset($this->passedArgs['Search.exam_date'])) {
                $exam_date_selected = $this->request->data['Search']['exam_date'] = $this->passedArgs['Search.exam_date'];
            }

            if (isset($this->passedArgs['Search.limit'])) {
                $limit = $this->request->data['Search']['limit'] = $this->passedArgs['Search.limit'];
            }
        }


        if (!empty($this->request->data) /* && isset($this->request->data['viewExitExams']) */) {
            if (!empty($this->request->data['Search']['department_id'])) {
                $options[] = array(
                    'Student.department_id' => $this->request->data['Search']['department_id']
                );

                $options[] = array(
                    'Student.department_id' => $this->request->data['Search']['department_id']
                );

                $sections_detail = ClassRegistry::init('Section')->find('all', array(
                    'conditions' => array(
                        'Section.department_id' => $this->request->data['Search']['department_id'],
                        'Section.archive' => 0,
                        'Section.program_type_id' => $program_type_id,
                        'Section.program_id' => $program_id,
                        'OR' => array(
                            'Section.academicyear LIKE ' => $this->AcademicYear->current_academicyear(),
                            'Section.created >= ' => date('Y-m-d H:i:s', strtotime('-1 year'))
                        )
                    ),
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
                    }
                }
            }

            // filter by section
            if (!empty($this->request->data['Search']['section_id'])) {
                $section_id = $this->request->data['Search']['section_id'];
                $list_of_students = ClassRegistry::init('StudentsSection')->find('list', array(
                    'conditions' => array(
                        'StudentsSection.section_id' => $section_id,
                        'StudentsSection.archive' => 0
                    ),
                    'group' => array('student_id', 'section_id'),
                    'fields' => array('student_id', 'student_id')
                ));

                if (!empty($list_of_students)) {
                    $options[] = array('Student.id' => $list_of_students);
                }
            }

            if (!empty($this->request->data['Search']['program_id'])) {
                $options[] = array('Student.program_id' => $this->request->data['Search']['program_id']);
            }

            if (!empty($this->request->data['Search']['program_type_id'])) {
                $options[] = array('Student.program_type_id' => $this->request->data['Search']['program_type_id']);
            }

            if (!empty($this->request->data['Search']['exam_date'])) {
                $options[] = array('ExitExam.exam_date >= ' => $this->request->data['Search']['exam_date']);
            }

            if (!empty($this->request->data['Search']['limit'])) {
                $limit = $this->request->data['Search']['limit'];
            }

            if (!empty($this->request->data['Search']['name_or_id'])) {
                $name_or_id = $this->request->data['Search']['name_or_id'];
                unset($options);
                $options[] = array(
                    'OR' => array(
                        'Student.first_name LIKE ' => '%' . trim($name_or_id) . '%',
                        'Student.middle_name LIKE ' => '%' . trim($name_or_id) . '%',
                        'Student.last_name LIKE ' => '%' . trim($name_or_id) . '%',
                        'Student.studentnumber' => trim($name_or_id),
                    ),
                    'Student.department_id' => (isset($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['department_id']) ? $this->request->data['Search']['department_id'] : $this->department_ids)
                );
            }
        }

        debug($this->request->data);
        debug($options);


        $this->Paginator->settings = array(
            'contain' => array(
                'Student' => array(
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
                        'Campus' => array(
                            'id',
                            'name',
                            'campus_code'
                        )
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
                ),
                'Course' => array(
                    'Curriculum' => array('id', 'name', 'year_introduced', 'type_credit', 'active'),
                ),
            ),
            'order' => array(
                'ExitExam.exam_date' => 'DESC',
                'Student.college_id' => 'ASC',
                'Student.department_id' => 'ASC',
                'Student.full_name' => 'ASC',
                'Student.studentnumber' => 'ASC',
                'ExitExam.id' => 'DESC',
                'ExitExam.student_id' => 'ASC',
            ),
            'limit' => $limit,
            'maxLimit' => $limit,
            'recursive' => -1
        );


        $exitExams = $this->paginate($options);

        if (empty($exitExams) && !empty($this->request->data)) {
            $this->Flash->info('No Exit Exam Result found in the given search criteria.');
        }

        $programs = ClassRegistry::init('Program')->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
        );
        $program_types = $programTypes = ClassRegistry::init('ProgramType')->find(
            'list',
            array('conditions' => array('ProgramType.id' => $this->program_type_ids, 'ProgramType.active' => 1))
        );

        $exam_date = $this->ExitExam->find(
            'list',
            array(
                'group' => array('ExitExam.exam_date'),
                'fields' => array('ExitExam.exam_date', 'ExitExam.exam_date'),
                'order' => array('ExitExam.exam_date' => 'DESC')
            )
        );

        $departments = $this->ExitExam->Student->Department->find(
            'list',
            array('conditions' => array('Department.id' => $this->department_ids))
        );
        $this->set(
            compact(
                'departments',
                'exitExams',
                /* 'section_id',  */
                'page',
                'sections',
                'programs',
                'programTypes',
                'exam_date',
                'limit',
                'exam_date_selected',
                'name_or_id'
            )
        );
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid Exit Exam Result');
            return $this->redirect(array('action' => 'index'));
        } else {
            $exitExams = $this->ExitExam->read(null, $id);

            //debug($exitExams);

            if (!empty($exitExams)) {
                $belongs_to_you = $this->ExitExam->Student->find('count', array(
                    'conditions' => array(
                        'Student.id' => $exitExams['ExitExam']['student_id'],
                        'Student.department_id' => $this->department_ids
                    )
                ));

                if ($belongs_to_you > 0) {
                    $student_id = $exitExams['ExitExam']['student_id'];
                    $curriculum_id = $this->ExitExam->Student->field('curriculum_id', array('Student.id' => $student_id)
                    );

                    $curriculum_has_exit_exam = $this->ExitExam->Student->Curriculum->Course->find(
                        'first',
                        array(
                            'conditions' => array(
                                'Course.curriculum_id' => $curriculum_id,
                                'Course.exit_exam' => 1
                            )
                        )
                    );

                    if (!empty($curriculum_has_exit_exam)) {
                        $student_section_exam_status = $this->ExitExam->Student->get_student_section($student_id);
                        $courses = $this->ExitExam->Student->Course->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'Course.curriculum_id' => $curriculum_id,
                                    'Course.exit_exam' => 1
                                ),
                                'fields' => array('id', 'course_title')
                            )
                        );
                        $this->set(compact('student_section_exam_status', 'courses'));
                    } else {
                        $this->Flash->error(
                            'The curriculum attached to the student does not have exit exam course defined or it is changed, please advice the department of the student to define exit exam in their curriculum.'
                        );
                    }
                } else {
                    $this->Flash->error('You are not elegible to maintain the selected student Exit Exam Result.');
                }
            }

            $this->set('exitExams', $exitExams);
            $exit_exam_types = Configure::read('exit_exam_types');
            $this->set(compact('exit_exam_types'));
        }
    }

    public function add()
    {

        if (!empty($this->request->data) && isset($this->request->data['saveExitExam'])) {
            debug($this->request->data);

            if (isset($this->request->data['ExitExam']['id'])) {
            } else {
                $this->ExitExam->create();
            }

            if ($this->ExitExam->save($this->request->data)) {
                $this->Flash->success('The Exit Exam result has been saved.');
                unset($this->request->data);
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error('The exit Exam Result could not be saved. Please, try again..');
                $this->request->data['continue'] = true;
                $student_number = $this->ExitExam->Student->field(
                    'studentnumber',
                    array('id' => trim($this->request->data['ExitExam']['student_id']))
                );
                $this->request->data['Search']['studentID'] = $student_number;
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['continue'])) {
            $everythingfine = false;

            if (!empty($this->request->data['Search']['studentID'])) {
                $check_id_is_valid = $this->ExitExam->Student->find('count', array(
                    'conditions' => array(
                        'Student.studentnumber' => trim($this->request->data['Search']['studentID'])
                    )
                ));

                $studentIDs = 1;

                if ($check_id_is_valid > 0) {
                    $belongs_to_you = $this->ExitExam->Student->find('count', array(
                        'conditions' => array(
                            'Student.studentnumber' => trim($this->request->data['Search']['studentID']),
                            'Student.department_id' => $this->department_ids
                        )
                    ));

                    $studenDetails = $this->ExitExam->Student->find('first', array(
                        'conditions' => array(
                            'studentnumber' => trim($this->request->data['Search']['studentID'])
                        ),
                        'contain' => array(
                            'ExitExam' => array(
                                'Course',
                                'order' => array('ExitExam.id' => 'DESC', 'ExitExam.exam_date' => 'DESC')
                            ),
                            'GraduateList',
                            'Department' => array('id', 'name', 'type'),
                            'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced')
                        ),
                        'recursive' => -1
                    ));

                    debug($studenDetails);

                    if ($belongs_to_you > 0) {
                        $student_id = $studenDetails['Student']['id'];
                        $curriculum_id = $studenDetails['Student']['curriculum_id'];

                        if (isset($curriculum_id) && is_numeric($curriculum_id) && $curriculum_id > 0) {
                            $curriculum_has_exit_exam = $this->ExitExam->Student->Curriculum->Course->find(
                                'first',
                                array(
                                    'conditions' => array(
                                        'Course.curriculum_id' => $curriculum_id,
                                        'Course.exit_exam' => 1
                                    )
                                )
                            );

                            //debug($curriculum_has_exit_exam);
                            //debug($curriculum_id);

                            if (!empty($curriculum_has_exit_exam)) {
                                $everythingfine = true;
                                $student_section_exam_status = $this->ExitExam->Student->get_student_section(
                                    $student_id
                                );
                                $courses = $this->ExitExam->Student->Course->find(
                                    'list',
                                    array(
                                        'conditions' => array(
                                            'Course.curriculum_id' => $curriculum_id,
                                            'Course.exit_exam' => 1
                                        ),
                                        'fields' => array('id', 'course_title')
                                    )
                                );

                                if (isset($studenDetails['ExitExam'][0]['result']) && $studenDetails['Student']['graduated'] == 1) {
                                    //$this->request->data = $this->ExitExam->read(null, $is_already_recored['ExitExam']['id']);
                                    $this->Flash->info(
                                        $studenDetails['Student']['full_name'] . ' (' . $studenDetails['Student']['studentnumber'] . ') is graduated with ' . $studenDetails['ExitExam'][0]['result'] . '% Exit Exam result dated ' . (CakeTime::format(
                                            "F j, Y",
                                            $studenDetails['ExitExam'][0]['exam_date'],
                                            false,
                                            null
                                        )) . '. No further action is needed here.'
                                    );
                                    $this->request->data = null;
                                    //$this->redirect(array('action' => 'index'));
                                    $latest_exit_exam_result = $studenDetails['ExitExam'][0]['result'];
                                    $exit_exam_id = $studenDetails['ExitExam'][0]['id'];
                                    $this->set(compact('exit_exam_id', 'latest_exit_exam_result'));
                                } else {
                                    if (isset($studenDetails['ExitExam'][0]['result']) && $studenDetails['ExitExam'][0]['result'] >= 50) {
                                        //$this->request->data = $this->ExitExam->read(null, $is_already_recored['ExitExam']['id']);
                                        $this->Flash->info(
                                            'There is recorded Exit Exam Result for ' . $studenDetails['Student']['full_name'] . ' (' . $studenDetails['Student']['studentnumber'] . ') with ' . $studenDetails['ExitExam'][0]['result'] . '% dated ' . (CakeTime::format(
                                                "F j, Y",
                                                $studenDetails['ExitExam'][0]['exam_date'],
                                                false,
                                                null
                                            )) . ', which is above 50% and a pass grade. You don\'t need to add it again. You can make futher modifications except Exam Result here.'
                                        );
                                        $this->request->data = null;
                                        //$this->redirect(array('action' => 'index'));
                                        $latest_exit_exam_result = $studenDetails['ExitExam'][0]['result'];
                                        $exit_exam_id = $studenDetails['ExitExam'][0]['id'];
                                        $this->set(compact('exit_exam_id', 'latest_exit_exam_result'));
                                    }
                                }

                                $this->set(compact('student_section_exam_status', 'courses'));
                                $this->set(compact('studentIDs'));
                            } else {
                                $this->Flash->error(
                                    'The curriculum attached to ' . $studenDetails['Student']['full_name'] . ' (' . $studenDetails['Student']['studentnumber'] . ') does not have exit exam course defined. Please advise ' . $studenDetails['Department']['name'] . ' ' . $studenDetails['Department']['type'] . ' to define one or edit existing course with "Exit Exam" Course Type under "' . $studenDetails['Curriculum']['name'] . ' - ' . $studenDetails['Curriculum']['year_introduced'] . '" curriculum.'
                                );
                            }
                        } else {
                            $this->Flash->error(
                                $studenDetails['Student']['full_name'] . ' (' . $studenDetails['Student']['studentnumber'] . ') is not attached to any curriculum.'
                            );
                        }
                    } else {
                        $this->Flash->error(
                            'You are not elegible to maintain Exit Exam Result for ' . $studenDetails['Student']['full_name'] . ' (' . $studenDetails['Student']['studentnumber'] . ').'
                        );
                    }
                } else {
                    $this->Flash->error('The provided Student ID is not valid.');
                }
            } else {
                $this->Flash->error('Please provide Student ID to maintain Exit Exam Result.');
            }
        }

        $exit_exam_types = Configure::read('exit_exam_types');
        $exam_date = $this->ExitExam->find(
            'first',
            array(
                'group' => array('ExitExam.exam_date'),
                'fields' => array('ExitExam.exam_date', 'ExitExam.exam_date'),
                'order' => array('ExitExam.exam_date' => 'DESC')
            )
        );

        $default_exam_date = date('Y-m-d');

        if (isset($exam_date['ExitExam']['exam_date']) && !empty($exam_date['ExitExam']['exam_date'])) {
            $default_exam_date = $exam_date['ExitExam']['exam_date'];
        }

        if (isset($latest_exit_exam_result) && !empty($latest_exit_exam_result)) {
            $default_exam_date = $studenDetails['ExitExam'][0]['exam_date'];
        }

        debug($default_exam_date);

        $this->set(compact('exit_exam_types', 'default_exam_date'));
    }

    public function import_exit_exam_results()
    {

        if (!empty($this->request->data) && is_uploaded_file($this->request->data['ExitExam']['File']['tmp_name'])) {
            //check the file type before doing the fucken manipulations.

            if (strcasecmp($this->request->data['ExitExam']['File']['type'], 'application/vnd.ms-excel')) {
                $this->Flash->error(
                    'Importing Error!!. Please  save your excel file as "Excel 97-2003 Workbook" type and import again. Current file format is: ' . $this->request->data['AcceptedStudent']['File']['type']
                );
                return;
            }

            $data = new Spreadsheet_Excel_Reader();
            $data->setOutputEncoding('CP1251');
            $data->read($this->request->data['ExitExam']['File']['tmp_name']);

            $headings = array();
            $xls_data = array();

            $required_fields = array(
                'studentnumber',
                'result',
                'type'
                //'exam_date'
            );

            $non_existing_field = array();
            $invalid_rows = array();

            if (empty($data->sheets[0]['cells'])) {
                $this->Flash->error('Importing Error!!. The excel file you uploaded is empty.');
                return;
            }

            if (empty($data->sheets[0]['cells'][1])) {
                $this->Flash->error(
                    'Importing Error!!. Please insert your filed name (studentnumber, result) at first row of your excel file.'
                );
                return;
            }

            if (count($required_fields)) {
                for ($k = 0; $k < count($required_fields); $k++) {
                    if (in_array($required_fields[$k], $data->sheets[0]['cells'][1]) === false) {
                        $non_existing_field[] = $required_fields[$k];
                    }
                }
            }

            if (count($non_existing_field) > 0) {
                $field_list = "";
                foreach ($non_existing_field as $k => $v) {
                    $field_list .= ($v . ", ");
                }
                $field_list = substr($field_list, 0, (strlen($field_list) - 2));
                $this->Flash->error(
                    'Importing Error!!. ' . $field_list . ' is/are required in the excel file you imported at first row.'
                );
                return;
            } else {
                $field_names_exit_exam_table = $data->sheets[0]['cells'][1];
                $duplicated_student_number = array();
                $invalid_student_number = array();

                if ($data->sheets[0]['numRows']) {
                    for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                        $studentNumber = '';
                        $row_data = array();

                        //debug($data->sheets[0]['cells'][$i]);

                        for ($j = 1; $j <= count($field_names_exit_exam_table); $j++) {
                            //check student number is given and populate with value

                            if ($field_names_exit_exam_table[$j] == "studentnumber" && !empty($data->sheets[0]['cells'][$i][$j])) {
                                $studentNumber = $currentStudentNumber = trim($data->sheets[0]['cells'][$i][$j]);

                                if (isset($currentStudentNumber) && !empty($currentStudentNumber)) {
                                    $student_number_exists = $this->ExitExam->Student->find(
                                        'first',
                                        array(
                                            'conditions' => array('Student.studentnumber' => $currentStudentNumber),
                                            'fields' => array(
                                                'id',
                                                'studentnumber',
                                                'full_name',
                                                'curriculum_id',
                                                'graduated'
                                            ),
                                            'recursive' => -1
                                        )
                                    );
                                    debug($student_number_exists);

                                    if (empty($student_number_exists)) {
                                        $invalid_rows[] = $currentStudentNumber . " at row " . $i . " does not exist in the system. Please remove it from the excel.";
                                        continue;
                                    }

                                    $row_data['student_id'] = $student_number_exists['Student']['id'];

                                    if (!empty($student_number_exists)) {
                                        $row_data['studentnumber'] = trim($data->sheets[0]['cells'][$i][$j]);
                                        //$row_data['student_id'] = $student_number_exists['Student']['id'];

                                        //debug($student_number_exists['Student']['id']);

                                        if (isset($student_number_exists['Student']['curriculum_id']) && !empty($student_number_exists['Student']['curriculum_id']) && $student_number_exists['Student']['curriculum_id'] > 0) {
                                            $exit_exam_course_id = $this->ExitExam->Course->field(
                                                'Course.id',
                                                array(
                                                    'Course.curriculum_id' => $student_number_exists['Student']['curriculum_id'],
                                                    'Course.exit_exam' => 1
                                                )
                                            );
                                            //debug($exit_exam_course_id);

                                            $row_data['course_id'] = null;

                                            if (isset($exit_exam_course_id) && !empty($exit_exam_course_id) && $exit_exam_course_id > 0) {
                                                $row_data['course_id'] = $exit_exam_course_id;
                                            }

                                            debug($row_data['course_id']);

                                            if (!isset($row_data['course_id']) || empty($row_data['course_id']) || !($row_data['course_id'] > 0)) {
                                                $invalid_rows[] = 'Attached Curriculum for ' . $student_number_exists['Student']['full_name'] . ' (' . $student_number_exists['Student']['studentnumber'] . ') at row ' . $i . ' does\'t have exit exam Course. Advise the department to define exit exam course or you can remove it from the excel for now.';
                                                continue;
                                            }
                                        }
                                    }

                                    if (!empty($student_number_exists) && $student_number_exists['Student']['graduated'] == 1) {
                                        $invalid_rows[] = $student_number_exists['Student']['full_name'] . ' (' . $student_number_exists['Student']['studentnumber'] . ') at row ' . $i . ' is graduated student. Please remove it from the excel.';
                                        continue;
                                    }

                                    $exit_exam_with_pass_mark = $this->ExitExam->find('first', array(
                                        'conditions' => array(
                                            'ExitExam.student_id' => $row_data['student_id'],
                                            'ExitExam.result >=' => 50
                                        ),
                                        'contain' => array(
                                            'Student' => array('id', 'studentnumber', 'full_name', 'curriculum_id')
                                        ),
                                        'order' => array('ExitExam.id' => 'DESC', 'ExitExam.exam_date' => 'DESC'),
                                        'recursive' => -1
                                    ));

                                    debug($exit_exam_with_pass_mark);

                                    if (!empty($exit_exam_with_pass_mark)) {
                                        $invalid_rows[] = $row_data['studentnumber'] . " at row " . $i . " already have exit Exam result recorded in the system, (" . $exit_exam_with_pass_mark['ExitExam']['result'] . "%), dated " . (CakeTime::format(
                                                "F j, Y",
                                                $exit_exam_with_pass_mark['ExitExam']['exam_date'],
                                                false,
                                                null
                                            )) . "  which is above 50% and a pass grade. Please remove it from the excel.";
                                        continue;
                                    }

                                    $duplicated_student_number[$currentStudentNumber] = isset($duplicated_student_number[$currentStudentNumber]) ? $duplicated_student_number[$currentStudentNumber] : 0 + 1;
                                    if (isset($duplicated_student_number[$currentStudentNumber]) && $duplicated_student_number[$currentStudentNumber] > 1) {
                                        $invalid_rows[] = $currentStudentNumber . " is duplicated at row " . $i . ". Please remove it from the excel.";
                                        continue;
                                    }
                                } else {
                                    $duplicated_student_number[$currentStudentNumber] = 0;
                                }
                            }

                            if ($field_names_exit_exam_table[$j] == "result" && (!isset($data->sheets[0]['cells'][$i][$j]) || (trim(
                                            $data->sheets[0]['cells'][$i][$j]
                                        ) == "" || !is_numeric($data->sheets[0]['cells'][$i][$j])) || (is_numeric(
                                            $data->sheets[0]['cells'][$i][$j]
                                        ) && ($data->sheets[0]['cells'][$i][$j] < 0 || $data->sheets[0]['cells'][$i][$j] > 100)))) {
                                $invalid_rows[] = "Please enter a valid exit exam result for " . $studentNumber . " at row " . $i;
                                continue;
                            }

                            if (in_array($field_names_exit_exam_table[$j], $required_fields)) {
                                if (strcasecmp($field_names_exit_exam_table[$j], "result") == 0) {
                                    $row_data['result'] = trim($data->sheets[0]['cells'][$i][$j]);
                                } else {
                                    if ($field_names_exit_exam_table[$j] == "exam_date") {
                                        $row_data['exam_date'] = trim($data->sheets[0]['cells'][$i][$j]);
                                    } else {
                                        if ($field_names_exit_exam_table[$j] == "type") {
                                            $row_data['type'] = trim($data->sheets[0]['cells'][$i][$j]);
                                        } else {
                                            $row_data[$field_names_exit_exam_table[$j]] = isset($data->sheets[0]['cells'][$i][$j]) ? $data->sheets[0]['cells'][$i][$j] : '';
                                        }
                                    }
                                }
                            }
                        }

                        $selected_exam_date = '';

                        if (isset($this->request->data['ExitExam']['exam_date']) && !empty($this->request->data['ExitExam']['exam_date'])) {
                            $selected_exam_date = $row_data['exam_date'] = $this->request->data['ExitExam']['exam_date']['year'] . '-' . $this->request->data['ExitExam']['exam_date']['month'] . '-' . $this->request->data['ExitExam']['exam_date']['day'];
                        }

                        if (empty($selected_exam_date)) {
                            $selected_exam_date = date('Y-m-d');
                        }


                        // to prevent possible duplicated values in the excel file itself
                        $exitExamUniqueFields = array();

                        if (isset($row_data['student_id'])) {
                            $exitExamUniqueFields[0] = trim($row_data['student_id']);
                            $exitExamUniqueFields[1] = trim($row_data['result']);
                            $exitExamUniqueFields[2] = isset($selected_exam_date) ? $selected_exam_date : trim(
                                $row_data['exam_date']
                            );
                        }

                        //debug($row_data);
                        if (isset($row_data['studentnumber']) && !empty($row_data['studentnumber'])) {
                            unset($row_data['studentnumber']);
                        }

                        $is_duplicated = $this->ExitExam->find(
                            'count',
                            array('conditions' => $row_data, 'recursive' => -1)
                        );

                        //debug($is_duplicated);
                        if ($is_duplicated > 0) {
                            $invalid_rows[] = "Exit exam result for " . $studentNumber . " at row " . $i . " is already imported to the system. Please remove it from the excel.";
                            //continue;
                        }

                        if (isset($xls_data) && !empty($xls_data)) {
                            debug($xls_data);

                            $is_duplicated_in_xls = 0;

                            if (isset($xls_data['ExitExam']['student_id']) && isset($xls_data['ExitExam']['result']) && isset($xls_data['ExitExam']['exam_date'])) {
                                $is_duplicated_in_xls = count(
                                    array_filter($xls_data, function ($xlsd) use ($exitExamUniqueFields) {

                                        return ($xlsd['ExitExam']['student_id'] == $exitExamUniqueFields[0] && $xlsd['ExitExam']['result'] == $exitExamUniqueFields[1] && $xlsd['ExitExam']['exam_date'] == $exitExamUniqueFields[2]);
                                    })
                                );
                            }

                            //debug($is_duplicated_in_xls);

                            if ($is_duplicated_in_xls > 0) {
                                $invalid_rows[] = $studentNumber . " at row " . $i . " (" . $studentNumber . " " . $exitExamUniqueFields[1] . " " . $exitExamUniqueFields[2] . ") is duplicated " . $is_duplicated_in_xls . " in the excel file. Please remove it from the excel.";
                                //continue;
                            }
                        }

                        $xls_data[] = array('ExitExam' => $row_data);

                        $data->sheets[0]['cells'][$i] = null;

                        if (count($invalid_rows) == 19) {
                            $invalid_rows[] = "Please check other similar errors in the file you are trying to import.";
                            break;
                        }
                    }
                }

                //invalid rows
                if (count($invalid_rows) > 0) {
                    $row_list = "";
                    $this->Flash->error(
                        'Importing Error!! Please correct the following listed rows in your excel file and try to import the file again.'
                    );
                    $this->set('invalid_rows', $invalid_rows);
                    return;
                }
            }

            if (!empty($xls_data)) {
                $reformat_for_saveAll = array();

                foreach ($xls_data as $xlk => &$xlv) {
                    if (isset($row_data['studentnumber']) && !empty($row_data['studentnumber'])) {
                        unset($row_data['studentnumber']);
                    }

                    if (empty($xlv['ExitExam']['course_id'])) {
                        $xlv['ExitExam']['course_id'] = 155;
                    }

                    if (isset($selected_exam_date) && !empty($selected_exam_date)) {
                        $xlv['ExitExam']['exam_date'] = $selected_exam_date;
                    }

                    if (!isset($xlv['ExitExam']['type']) || empty($xlv['ExitExam']['type'])) {
                        $xlv['ExitExam']['type'] = 'Exit Exam';
                    }

                    $xlv['ExitExam']['created'] = date('Y-m-d H:i:s');
                    $xlv['ExitExam']['modified'] = date('Y-m-d H:i:s');


                    if (isset($xlv['ExitExam']['student_id']) && isset($xlv['ExitExam']['result']) && isset($xlv['ExitExam']['exam_date'])) {
                        $reformat_for_saveAll['ExitExam'][] = $xlv['ExitExam'];
                    }
                }

                debug($reformat_for_saveAll);

                if ($this->ExitExam->saveAll($reformat_for_saveAll['ExitExam'], array('validate' => 'first'))) {
                    $auto_messages = array();

                    if (count($reformat_for_saveAll['ExitExam'])) {
                        $auto_message['AutoMessage']['message'] = (count($reformat_for_saveAll['ExitExam']) > 1 ? count(
                                    $reformat_for_saveAll['ExitExam']
                                ) . ' students exam results' : count(
                                    $reformat_for_saveAll['ExitExam']
                                ) . ' exam result') . ' recently imported using your account for ' . (CakeTime::format(
                                "F j, Y",
                                $selected_exam_date,
                                false,
                                null
                            )) . ' exam date.';
                        $auto_message['AutoMessage']['read'] = 0;
                        $auto_message['AutoMessage']['user_id'] = $this->Session->read('Auth.User')['id'];
                        $auto_messages[] = $auto_message;
                    }

                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        $registrar_admin = ClassRegistry::init('User')->find('first', array(
                            'conditions' => array(
                                'User.role_id' => ROLE_REGISTRAR,
                                'User.is_admin' => 1,
                                'User.active' => 1
                            ),
                            'recursive' => -1
                        ));

                        debug($registrar_admin);

                        if (!empty($registrar_admin) && ($this->Session->read(
                                    'Auth.User'
                                )['is_admin'] != 1 || $this->Session->read(
                                    'Auth.User'
                                )['id'] != $registrar_admin['User']['id'])) {
                            if (count($reformat_for_saveAll['ExitExam'])) {
                                $auto_message['AutoMessage']['message'] = (count(
                                        $reformat_for_saveAll['ExitExam']
                                    ) > 1 ? count(
                                            $reformat_for_saveAll['ExitExam']
                                        ) . ' students Exit Exam Results are' : count(
                                            $reformat_for_saveAll['ExitExam']
                                        ) . ' student Exit Exam Result') . ' is recently imported using other registrar account(with a privilege to import students Exit Exam Result) for ' . (CakeTime::format(
                                        "F j, Y",
                                        $selected_exam_date,
                                        false,
                                        null
                                    )) . ' exam date.';
                                $auto_message['AutoMessage']['read'] = 0;
                                $auto_message['AutoMessage']['user_id'] = $registrar_admin['User']['id'];
                                $auto_messages[] = $auto_message;
                            }
                        }
                    }


                    if (!empty($auto_messages)) {
                        //debug($auto_messages);
                        debug(
                            ClassRegistry::init('AutoMessage')->saveAll($auto_messages, array('validate' => 'first'))
                        );
                    }

                    $this->Flash->success(
                        'Imported ' . count(
                            $reformat_for_saveAll['ExitExam']
                        ) . ' students Exit Exam Results successfully.'
                    );
                    //$this->redirect(array('action'=>'index'));
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Flash->error('Unable to import the results. Please try again.');
                }
            } else {
                $this->Flash->error('Error. Unable to import the results. Please try again.');
            }
        } else {
            // $this->Flash->error('Importing Error. Please try again');
        }
    }
}

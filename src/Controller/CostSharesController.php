<?php
namespace App\Controller;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class CostSharesController extends AppController
{

    public $name = 'CostShares';
    public $menuOptions = array(
        'exclude' => array('search', 'get_cost_share_summery'),
        'alias' => array(
            'index' => 'View Cost Shares',
            'add' => 'Maintain Cost Shares'

        )
    );
    public $paginate = array();

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow(
            'search',
            'get_cost_share_summery',
            'cost_sharing_report'
        );
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyearArray();
        $sharing_cycles = array(
            'one year' => 'One Year',
            'six month' => 'Six Month'
        );
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        foreach ($acyear_array_data as $k => $v) {
            if ($v == $defaultacademicyear) {
                $defaultacademicyear = $k;
                break;
            }
        }
        $this->set(compact('acyear_array_data', 'defaultacademicyear', 'sharing_cycles'));
        unset($this->request->data['User']['password']);
    }

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

        $this->viewBuilder()->setHelpers(['Media.Media']);
    }

    public function search()
    {

        // the page we will redirect to
        $url['action'] = 'index';

        // build a URL will all the search elements in it
        // the resulting URL will be
        // domain.com/returned_items/index/Search.keywords:mykeyword/Search.tag_id:3

        if (isset($this->request->data['Search']['academic_year'])) {
            $ac = str_replace('/', '-', $this->request->data['Search']['academic_year']);
            $this->request->data['Search']['academic_year'] = $ac;
        }

        foreach ($this->request->data as $k => $v) {
            foreach ($v as $kk => $vv) {
                if (is_array($vv)) {
                    foreach ($vv as $kkk => $vvv) {
                        $url[$k . '.' . $kk . '.' . $kkk] = $vvv;
                    }
                } else {
                    $url[$k . '.' . $kk] = $vv;
                }
            }
        }


        // redirect the user to the url
        return $this->redirect($url, null, true);
    }


    public function index()
    {

        $this->paginate = array(
            'contain' => array(
                'Student' => array(
                    'Department' => array('id', 'name'),
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name')
                )
            )
        );
        $academic_year = '';
        $optionsNotCompleted = array();
        $optionsNotCompleted['contain'] = array(
            'ProgramType',
            'Department',
            'Program'
        );
        $queryCS = " student_id is not null ";
        if ($this->role_id == ROLE_STUDENT) {
            $this->paginate['conditions'][]['CostShare.academic_year LIKE '] = $academic_year . '%';
            $this->paginate['conditions'][]['CostShare.student_id'] = $this->student_id;
        }
        // filter by academic year
        if (isset($this->passedArgs['Search.academic_year'])) {
            if (!empty($this->passedArgs['Search.academic_year'])) {
                $ac = explode('-', $this->passedArgs['Search.academic_year']);
                $academic_year = $ac[0];
                $queryCS .= " and academic_year like '$academic_year%'";
                $optionsNotCompleted['conditions']['AcceptedStudent.academicyear >='] = $this->passedArgs['Search.academic_year'];
            }

            if (isset($this->passedArgs['Search.academic_year'])) {
                $ac = str_replace('-', '/', $this->passedArgs['Search.academic_year']);
                $this->request->data['Search']['academic_year'] = $ac;
            }
        }

        // filter by section
        if (isset($this->passedArgs['Search.section_id'])) {
            $section_id = array($this->passedArgs['Search.section_id']);
            $list_of_students = ClassRegistry::init('StudentsSection')->find(
                'list', array(
                    'conditions' => array(
                        'StudentsSection.section_id' => $section_id,
                        'StudentsSection.archive' => 0
                    ),
                    'fields' => array('student_id', 'student_id')
                )
            );
            if (!empty($list_of_students) && $this->role_id != ROLE_STUDENT) {
                $this->paginate['conditions'][]['Student.id'] = $list_of_students;
                $optionsNotCompleted['conditions']['Student.id'] = $list_of_students;
            }
            $this->request->data['Search']['section_id'] = $this->passedArgs['Search.section_id'];
        }


        // filter by department
        if (isset($this->passedArgs['Search.department_id'])) {
            $department_id = array($this->passedArgs['Search.department_id']);
            if (!empty($department_id) && $this->role_id != ROLE_STUDENT) {
                $this->paginate['conditions'][]['Student.department_id'] = $department_id;
                $optionsNotCompleted['conditions']['Student.department_id'] = $department_id;
            }

            if (!empty($this->passedArgs['Search.department_id'])) {
                //$queryCS.=" and department_id=".$this->passedArgs['Search.department_id'];
            }
            // set the Search data, so the form remembers the option
            $this->request->data['Search']['department_id'] = $this->passedArgs['Search.department_id'];
        }

        // filter by department
        if (isset($this->passedArgs['Search.college_id'])) {
            $college_id = array($this->passedArgs['Search.college_id']);

            if (!empty($this->passedArgs['Search.college_id'])) {
                //$queryCS.=" and college_id=".$this->passedArgs['Search.college_id'];
                $this->paginate['conditions'][]['Student.college_id'] = $college_id;
            }
            $this->request->data['Search']['college_id'] = $this->passedArgs['Search.college_id'];
        }
        // filter by name
        if (isset($this->passedArgs['Search.name'])) {
            $name = $this->passedArgs['Search.name'];
            if (!empty($name)) {
                $this->paginate['conditions'][]['Student.first_name like '] = trim(
                        $name
                    ) . '%';
                $optionsNotCompleted['conditions']['Student.first_name like'] = trim(
                        $name
                    ) . '%';
            }

            $this->request->data['Search']['name'] = $this->passedArgs['Search.name'];
        }

        if (isset($this->passedArgs['Search.completion'])) {
            if (isset($this->passedArgs['Search.completion']) && $this->passedArgs['Search.completion'] == "no") {
                unset($this->paginate);
                $this->paginate = array(
                    'AcceptedStudent',
                    'Department' => array('id', 'name'),
                    'Program' => array('id', 'name'),
                    'ProgramType' => array('id', 'name')
                );
            }
            $this->request->data['Search']['completion'] = $this->passedArgs['Search.completion'];
        }
        if (empty($this->request->data['Search']['department_id'])) {
            if (!empty($this->department_ids)) {
                $this->paginate['conditions'][]['Student.department_id'] = $this->department_ids;

                $optionsNotCompleted['conditions']['Student.department_id'] = $this->department_ids;
            }
        } else {
            if (empty($this->request->data['Search']['college_id'])) {
                if (!empty($this->college_ids)) {
                    $this->paginate['conditions'][]['Student.college_id'] = $this->college_ids;
                    $optionsNotCompleted['conditions']['Student.college_id'] = $this->college_ids;
                }
            }
        }

        if (isset($this->paginate['conditions'])) {
            $this->Paginator->settings['conditions'] = $this->paginate['conditions'];
            $costShares = $this->Paginator->paginate('CostShare');
        } else {
            if (isset($optionsNotCompleted) && !empty($optionsNotCompleted)) {
                $optionsNotCompleted['conditions'][] = "Student.id NOT IN (SELECT student_id FROM cost_shares where $queryCS )";
                //$studentsWithoutCostShares=$this->CostShare->Student->find('all',$optionsNotCompleted);
                debug($optionsNotCompleted);
                $this->Paginator->settings['conditions'] = $optionsNotCompleted['conditions'];
                $studentsWithoutCostShares = $this->Paginator->paginate('Student');
            } else {
                if ($this->role_id == ROLE_STUDENT) {
                    $conditions = array('CostShare.student_id' => $this->student_id);
                    $costShares = $this->paginate($conditions);
                    if (empty($costShares)) {
                        $this->Session->setFlash(
                            '<span></span>' . __('No billing/costsharing data is maintain by your name.'),
                            'default',
                            array('class' => 'info-box info-message')
                        );
                    }
                } else {
                    $costShares = array();
                }
            }
        }

        if ((empty($costShares) && isset($this->passedArgs) && !empty($this->passedArgs))) {
            if (!isset($studentsWithoutCostShares)) {
                $this->Session->setFlash(
                    '<span></span>' . __('No result is found for the given search criteria.'),
                    'default',
                    array('class' => 'info-box info-message')
                );
            }
        }

        if (!empty($this->department_ids)) {
            $departments = $this->CostShare->Student->Department->find(
                'list',
                array('conditions' => array('Department.id' => $this->department_ids))
            );
        } else {
            if (!empty($this->college_ids)) {
                $colleges = $this->CostShare->Student->College->find(
                    'list',
                    array('conditions' => array('College.id' => $this->college_ids))
                );
                $this->set(compact('colleges'));
                $this->set('college_ids', $this->college_ids);
            } else {
                if ($this->role_id == ROLE_DEPARTMENT) {
                    $departments = $this->CostShare->Student->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_id))
                    );
                } else {
                    if ($this->role_id == ROLE_COLLEGE) {
                        $departments = $this->CostShare->Student->Department->find(
                            'list',
                            array('conditions' => array('Department.college_id' => $this->college_id))
                        );
                    } else {
                        $departments = $this->CostShare->Student->Department->find('list');
                    }
                }
            }
        }


        if (!empty($this->request->data['Search']['department_id'])) {
            $sections = ClassRegistry::init('Section')->get_sections_by_dept(
                $this->request->data['Search']['department_id']
            );
            $this->set(compact('sections'));
        }
        $this->set(compact('departments', 'costShares', 'studentsWithoutCostShares'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid cost share'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('costShare', $this->CostShare->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $academic_year = explode('/', $this->request->data['CostShare']['academic_year']);

            $check_duplication = $this->CostShare->find('count', array(
                'conditions' => array(
                    'CostShare.student_id' => $this->request->data['CostShare']['student_id'],
                    'CostShare.academic_year' => $academic_year[0],
                    'CostShare.sharing_cycle' => $this->request->data['CostShare']['sharing_cycle']
                )
            ));

            $validACc = $this->CostShare->Student->find('count', array(
                    'conditions' => array(
                        'Student.id' => $this->request->data['CostShare']['student_id'],
                        'AcceptedStudent.academicyear <=' => $academic_year[0],
                    ),
                )
            );

            if ($check_duplication == 0 && $validACc) {
                $this->CostShare->create();
                if ($this->CostShare->saveAll($this->request->data, array('validate' => 'first'))) {
                    $this->Session->setFlash(
                        '<span></span>' . __('The cost share has been saved'),
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __('The cost share could not be saved. Please, try again.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                if ($validACc > 0) {
                    $this->Session->setFlash(
                        '<span></span>Error. You can not maintain cost sharing for students before they are admitted. Cost sharing academic year always greater than  student admission academic year. ',
                        'default',
                        array('class' => 'error-box error-message')
                    );
                } else {
                    if ($check_duplication > 0) {
                        $student_full_name = $this->CostShare->Student->field(
                            'full_name',
                            array('Student.id' => $this->request->data['CostShare']['student_id'])
                        );
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                'You have already recorded cost sharing for ' . $student_full_name . ' for ' . $this->request->data['CostShare']['academic_year'] . '.'
                            ),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                }
            }
        }

        //$students = $this->CostShare->Student->find('list',array('fields'=>array('id','full_name')));
        if (!empty($this->college_ids)) {
            $colleges = $this->CostShare->Student->College->find(
                'list',
                array('conditions' => array('College.id' => $this->college_ids))
            );
            $this->set('college_ids', $this->college_ids);
        }

        if (!empty($this->department_ids)) {
            $college_ids = array();
            $departments = $this->CostShare->Student->Department->find(
                'all',
                array(
                    'conditions' => array('Department.id' => $this->department_ids),
                    'recursive' => -1
                )
            );
            foreach ($departments as $in => $value) {
                $college_ids[] = $value['Department']['college_id'];
            }
            $colleges = $this->CostShare->Student->College->find('list', array(
                'conditions' => array(
                    'College.id' => $college_ids
                )
            ));
            $this->set('department_ids', $this->department_ids);
        }


        $this->set(compact('students', 'colleges', 'deparments'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid cost share'));
            return $this->redirect(array('action' => 'index'));
        }


        if (!empty($this->request->data)) {
            $validACc = $this->CostShare->Student->find('count', array(
                    'conditions' => array(
                        'Student.id' => $this->request->data['CostShare']['student_id'],
                        'AcceptedStudent.academicyear <=' => $this->request->data['CostShare']['academic_year'],

                    ),
                )
            );
            debug($validACc);
            debug($this->request->data);

            if ($validACc && $this->CostShare->saveAll($this->request->data, array('validate' => 'first'))) {
                $this->Session->setFlash(
                    '<span></span>' . __('The cost share has been saved'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                //return $this->redirect(array('action' => 'index'));
            } else {
                if ($validACc == 0) {
                    $this->Session->setFlash(
                        '<span></span>Error. You can not maintain cost sharing for students before they are admitted. Cost sharing academic year always greater than  student admission academic year. ',
                        'default',
                        array('class' => 'error-box error-message')
                    );
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __('The cost share could not be saved. Please, try again.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
                $this->request->data = $this->CostShare->read(null, $id);

                $last_two_digist = substr($this->request->data['CostShare']['academic_year'], -2) + 1;
                $string = $this->request->data['CostShare']['academic_year'] . '/' . $last_two_digist;
                $this->request->data['CostShare']['academic_year'] = $string;
                $students = $this->CostShare->Student->find('list', array(
                    'fields' => array('id', 'full_name'),
                    array('conditions' => array('Student.id' => $this->request->data['CostShare']['student_id']))
                ));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->CostShare->read(null, $id);

            $last_two_digist = substr($this->request->data['CostShare']['academic_year'], -2) + 1;
            $string = $this->request->data['CostShare']['academic_year'] . '/' . $last_two_digist;
            $this->request->data['CostShare']['academic_year'] = $string;
            $students = $this->CostShare->Student->find('list', array(
                'fields' => array('id', 'full_name'),
                array('conditions' => array('Student.id' => $this->request->data['CostShare']['student_id']))
            ));
        }
        if (!empty($this->college_ids)) {
            $colleges = $this->CostShare->Student->College->find(
                'list',
                array('conditions' => array('College.id' => $this->college_ids))
            );
            $this->set('college_ids', $this->college_ids);
        }

        if (!empty($this->department_ids)) {
            $college_ids = array();
            $departments = $this->CostShare->Student->Department->find(
                'all',
                array(
                    'conditions' => array('Department.id' => $this->department_ids),
                    'recursive' => -1
                )
            );
            foreach ($departments as $in => $value) {
                $college_ids[] = $value['Department']['college_id'];
            }
            $colleges = $this->CostShare->Student->College->find(
                'list',
                array('conditions' => array('College.id' => $college_ids))
            );
            $this->set('department_ids', $this->department_ids);
        }

        //$students = $this->CostShare->Student->find('list',array('fields'=>array('id','full_name')));

        $this->set(compact('students', 'colleges', 'deparments'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for cost share'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->CostShare->delete($id)) {
            $this->Session->setFlash(__('Cost share deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Cost share was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }

    public function get_cost_share_summery($student_id = null)
    {

        $this->layout = 'ajax';
        $cost_share_summery = $this->CostShare->find(
            'all',
            array(
                'conditions' => array('CostShare.student_id' => $student_id),
                'contain' => array('Student' => array('id', 'full_name'), 'Attachment')
            )
        );
        $student_full_name = $this->CostShare->Student->field(
            'full_name',
            array('Student.id' => $student_id)
        );
        $this->set(compact('cost_share_summery', 'student_full_name'));
    }


    public function mass_cost_sharing_import()
    {

        $acyear_list = $this->AcademicYear->academicYearInArray(date('Y') - 4, date('Y') - 1);
        $this->set(compact('acyear_list'));
        if (!empty($this->request->data) && is_uploaded_file($this->request->data['CostShare']['File']['tmp_name'])) {
            //check the file type before doing the fucken manipulations.
            if (strcasecmp($this->request->data['CostShare']['File']['type'], 'application/vnd.ms-excel')) {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'Importing Error. Please  save your excel file as "Excel 97-2003 Workbook" type while you saved the file and import again. Try also to use other 97-2003 file types if you are using office 2010 or recent versions. Current file format is: ' . $this->request->data['CostShare']['File']['type']
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
                return;
            }

            $data = new Spreadsheet_Excel_Reader();
            // Set output Encoding.
            $data->setOutputEncoding('CP1251');
            $data->read($this->request->data['CostShare']['File']['tmp_name']);
            $headings = array();
            $xls_data = array();

            //check without department
            //TODO: Remove studentnumber
            $required_fields = array(
                'studentnumber',
                'education_fee',
                'accomodation_fee',
                'cafeteria_fee',
                'medical_fee'
            );

            $non_existing_field = array();
            $non_valide_rows = array();
            if (empty($data->sheets[0]['cells'])) {
                $this->Session->setFlash(
                    '<span></span>' . __('Importing Error. The excel file you uploaded is empty.', true),
                    'default',
                    array('class' => 'error-box error-message')
                );
                return;
            }
            if (empty($data->sheets[0]['cells'][1])) {
                $this->Session->setFlash(
                    '<span></span>' .
                    __(
                        'Importing Error. Please insert your filed name (studentnumber,education_fee,accomodation_fee,cafeteria_fee,medical_fee)  at first row of your excel file.',
                        true
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
                return;
            }

            for ($k = 0; $k < count($required_fields); $k++) {
                if (in_array(
                        $required_fields[$k],
                        $data->sheets[0]['cells'][1]
                    ) === false) {
                    $non_existing_field[] = $required_fields[$k];
                }
            }

            if (count($non_existing_field) > 0) {
                $field_list = "";
                foreach ($non_existing_field as $k => $v) {
                    $field_list .= ($v . ", ");
                }

                $field_list = substr($field_list, 0, (strlen($field_list) - 2));

                $this->Session->setFlash(
                    '<span></span>' . __(
                        'Importing Error. ' . $field_list . ' is/are required in the excel file you imported at first row.',
                        true
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );

                return;
            } else {
                $fields_name_costshare_table = $data->sheets[0]['cells'][1];
                $duplicated_student_number = array();

                for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                    $row_data = array();
                    $name_error_duplicate = false;
                    for ($j = 1; $j <= count($fields_name_costshare_table); $j++) {
                        //check student number is given and populate with value

                        if ($fields_name_costshare_table[$j] == "studentnumber") {
                            //check from the database if the student is valid
                            $validStudent = $this->CostShare->Student->find(
                                'first',
                                array(
                                    'conditions' => array(
                                        'Student.studentnumber' => trim(
                                            $data->sheets[0]['cells'][$i][$j]
                                        )
                                    )
                                )
                            );

                            if (empty($validStudent)) {
                                $non_valide_rows[] = "Student number at row number " . $i . " does not exist on the system";
                                continue;
                            }

                            $currentStudentNumber = trim($data->sheets[0]['cells'][$i][$j]);
                            if (isset($currentStudentNumber) && !empty($currentStudentNumber)) {
                                $duplicated_student_number[$currentStudentNumber] =
                                    isset($duplicated_student_number[$currentStudentNumber]) ?
                                        $duplicated_student_number[$currentStudentNumber] : 0 + 1;

                                if (isset($duplicated_student_number[$currentStudentNumber])
                                    && $duplicated_student_number[$currentStudentNumber] > 1) {
                                    $non_valide_rows[] = "Duplicated student number at row number " . $i;
                                    continue;
                                }
                            } else {
                                $duplicated_student_number[$currentStudentNumber] = 0;
                            }
                        }

                        if ($fields_name_costshare_table[$j] == "education_fee"
                            && isset($data->sheets[0]['cells'][$i][$j]) &&
                            $data->sheets[0]['cells'][$i][$j] != "" &&
                            !is_numeric($data->sheets[0]['cells'][$i][$j]) && $data->sheets[0]['cells'][$i][$j] < 0) {
                            //TODO: Uncomment the following two lines
                            $non_valide_rows[] = "Please enter a valid education fee on row number " . $i;
                            continue;
                        }


                        if ($fields_name_costshare_table[$j] == "accomodation_fee"
                            && isset($data->sheets[0]['cells'][$i][$j]) &&
                            $data->sheets[0]['cells'][$i][$j] != "" &&
                            !is_numeric($data->sheets[0]['cells'][$i][$j])) {
                            //TODO: Uncomment the following two lines
                            $non_valide_rows[] = "Please enter a valid accomodation fee on row number " . $i;
                            continue;
                        }

                        if ($fields_name_costshare_table[$j] == "cafeteria_fee" &&
                            isset($data->sheets[0]['cells'][$i][$j]) && $data->sheets[0]['cells'][$i][$j] != "" &&
                            !is_numeric($data->sheets[0]['cells'][$i][$j])) {
                            //TODO: Uncomment the following two lines
                            $non_valide_rows[] = "Please enter a valid cafeteria fee on row number " . $i;
                            continue;
                        }

                        if ($fields_name_costshare_table[$j] == "medical_fee"
                            && isset($data->sheets[0]['cells'][$i][$j]) &&
                            $data->sheets[0]['cells'][$i][$j] != "" && !is_numeric($data->sheets[0]['cells'][$i][$j])) {
                            //TODO: Uncomment the following two lines
                            $non_valide_rows[] = "Please enter a valid medical fee on row number " . $i;
                            continue;
                        }

                        if (in_array($fields_name_costshare_table[$j], $required_fields)) {
                            $validStudent = $this->CostShare->Student->find(
                                'first',
                                array('conditions' => array('Student.studentnumber like' => $data->sheets[0]['cells'][$i][$j] . '%'))
                            );
                            if ($fields_name_costshare_table[$j] == "studentnumber") {
                                $row_data['student_id'] = $validStudent['Student']['id'];
                            } else {
                                if ($fields_name_costshare_table[$j] == "education_fee") {
                                    $row_data['education_fee'] = abs($data->sheets[0]['cells'][$i][$j]);
                                } else {
                                    if ($fields_name_costshare_table[$j] == "accomodation_fee") {
                                        $row_data['accomodation_fee'] = abs($data->sheets[0]['cells'][$i][$j]);
                                    } else {
                                        if ($fields_name_costshare_table[$j] == "cafeteria_fee") {
                                            $row_data['cafeteria_fee'] = abs($data->sheets[0]['cells'][$i][$j]);
                                        } else {
                                            if ($fields_name_costshare_table[$j] == "medical_fee") {
                                                $row_data['medical_fee'] = abs($data->sheets[0]['cells'][$i][$j]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $row_data['academic_year'] = $this->request->data['CostShare']['academic_year'];
                        $row_data['cost_sharing_sign_date'] = $this->AcademicYear->get_academicYearBegainingDate(
                            $this->request->data['CostShare']['academic_year']
                        );
                    }

                    $is_duplicated = $this->CostShare->find(
                        'count',
                        array('conditions' => $row_data, 'recursive' => -1)
                    );
                    //debug($is_duplicated);
                    if ($is_duplicated > 0) {
                        $non_valide_rows[] = "The  data on row number " . $i . " has already existed or imported.
			                    Please remove it from your excel file.";
                    }


                    $xls_data[] = array('CostShare' => $row_data);
                    $data->sheets[0]['cells'][$i] = null;
                    if (count($non_valide_rows) == 19) {
                        $non_valide_rows[] = "Please check other similar errors in the file you imported.";
                        break;
                    }
                }

                //invalid rows
                if (count($non_valide_rows) > 0) {
                    $row_list = "";
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'Importing Error.
                      Please correct the following listed rows in your excel file.',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    $this->set('non_valide_rows', $non_valide_rows);
                    return;
                }
            }

            if (!empty($xls_data)) {
                $reformat_for_saveAll = array();
                $academic_year = explode('/', $this->request->data['CostShare']['academic_year']);

                $dupExit = array();
                $invalidAC = array();
                foreach ($xls_data as $xlk => &$xlv) {
                    //check if imported earlier
                    $duplicationExist = $this->CostShare->find('count', array(
                        'conditions' => array(
                            'CostShare.student_id' => $xlv['CostShare']['student_id'],
                            'CostShare.academic_year' => $academic_year[0],
                            'CostShare.sharing_cycle' => $this->request->data['CostShare']['sharing_cycle']
                        ),
                    ));
                    $validACc = $this->CostShare->Student->find('count', array(
                        'conditions' => array(
                            'Student.id' => $xlv['CostShare']['student_id'],
                            'AcceptedStudent.academicyear <=' => $this->request->data['CostShare']['academic_year']
                        ),
                    ));

                    if ($duplicationExist == 0 && $validACc) {
                        $reformat_for_saveAll['CostShare'][] = $xlv['CostShare'];
                    } else {
                        if ($validACc == 0) {
                            $invalidAC[] = $xlv['CostShare'];
                        }
                        if ($duplicationExist > 0) {
                            $dupExit[] = $xlv['CostShare'];
                        }
                    }
                }

                if (!empty($reformat_for_saveAll['CostShare']) && $this->CostShare->saveAll(
                        $reformat_for_saveAll['CostShare'],
                        array('validate' => 'first')
                    )) {
                    $this->Session->setFlash(
                        '<span></span>Success. Imported ' . count($reformat_for_saveAll['CostShare'])
                        . ' records.',
                        'default',
                        array('class' => 'success-box success-message')
                    );
                    //$this->redirect(array('action'=>'index'));
                } else {
                    if (!empty($invalidAC)) {
                        $this->Session->setFlash(
                            '<span></span>Error. You can not maintain cost sharing for students before they are admitted. Cost sharing academic year always greater than  student admission academic year. ',
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    } else {
                        if (!empty($dupExit)) {
                            $this->Session->setFlash(
                                '<span></span>Error.The cost sharing you tried to import has already imported. ',
                                'default',
                                array('class' => 'error-box error-message')
                            );
                        } else {
                            $this->Session->setFlash(
                                '<span></span>Error. Unable to import records. Please try again.',
                                'default',
                                array('class' => 'error-box error-message')
                            );
                        }
                    }
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>Error. Unable to import records. Please try again.',
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
    }

    public function cost_sharing_report()
    {

        if (isset($this->request->data['getReport']) ||
            isset($this->request->data['getReportExcel'])) {
            if ($this->request->data['Report']['report_type'] == 'completedCostSharingAgreemnt') {
                $costSharingForMoE = $this->CostShare->getCostSharingGraduated($this->request->data);
                $this->set(compact('costSharingForMoE'));

                if ($this->request->data['Report']['report_type'] == 'completedCostSharingAgreemnt' && isset($this->request->data['getReportExcel'])) {
                    $this->autoLayout = false;
                    $filename = 'Graduated Cost Sharing Details -' . date('Ymd H:i:s');
                    $this->set(compact('costSharingForMoE', 'filename'));
                    $this->render('/Elements/reports/xls/cost_sharing_government_report_xls');
                    return;
                }
            } else {
                if ($this->request->data['Report']['report_type'] == 'incompleteCostSharing') {
                    $costSharingForInternal = $this->CostShare->getCostSharingNotGraduated($this->request->data);
                    $this->set(compact('costSharingForInternal'));

                    if ($this->request->data['Report']['report_type'] == 'incompleteCostSharing' && isset($this->request->data['getReportExcel'])) {
                        $this->autoLayout = false;
                        $filename = 'Cost Sharing Details -' . date('Ymd H:i:s');
                        $this->set(compact('costSharingForInternal', 'filename'));
                        $this->render('/Elements/reports/xls/cost_sharing_internal_report_xls');
                        return;
                    }
                }
            }
        }

        $report_type_options = array(

            'completedCostSharingAgreemnt' => 'Costsharing Debt (Agreement)',
            'incompleteCostSharing' => 'Costsharing student list by admission ',

        );

        $programs = ClassRegistry::init('Program')->find('list');
        $program_types = ClassRegistry::init('ProgramType')->find('list');
        if (!empty($this->department_ids) ||
            !empty($this->college_ids)) {
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                1,
                $this->department_ids,
                $this->college_ids
            );
        } else {
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                1,
                $this->department_id,
                $this->college_id
            );
        }

        if ($this->role_id == ROLE_DEPARTMENT) {
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                0,
                $this->department_id,
                array()
            );
        } else {
            if ($this->role_id == ROLE_COLLEGE) {
                $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                    1,
                    array(),
                    $this->college_id
                );
            }
        }
        $this->set(compact('departments', 'program_types', 'programs', 'report_type_options'));
    }


}

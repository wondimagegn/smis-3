<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ClassRoomCourseConstraintsController extends AppController
{

    public $name = 'ClassRoomCourseConstraints';
    public $components = array('AcademicYear');

    public $menuOptions = array(
        //'parent' => 'courseConstraint',
        'exclude' => array('*'),
        'controllerButton' => false,
    );

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        //$this->Auth->allow(array('*'));
        $this->Auth->allow('get_year_level', 'get_class_rooms', 'get_course_types');
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyearArray();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
        unset($this->request->data['User']['password']);
    }

    public function index()
    {

        //$this->ClassRoomCourseConstraint->recursive = 0;
        $departments = $this->ClassRoomCourseConstraint->PublishedCourse->Department->find(
            'list',
            array(
                'fields' => array('Department.id'),
                'conditions' => array('Department.college_id' => $this->college_id)
            )
        );
        $publishedCourses_id_array = $this->ClassRoomCourseConstraint->PublishedCourse->find(
            'list',
            array(
                'fields' => array('PublishedCourse.id'),
                'conditions' => array(
                    'PublishedCourse.drop' => 0,
                    "OR" => array(
                        array('PublishedCourse.college_id' => $this->college_id),
                        array('PublishedCourse.department_id' => $departments)
                    )
                )
            )
        );
        $this->paginate = array(
            'conditions' => array('ClassRoomCourseConstraint.published_course_id' => $publishedCourses_id_array),
            'contain' => array(
                'PublishedCourse' => array(
                    'fields' => array('PublishedCourse.id'),
                    'Course' => array('fields' => array('Course.id', 'Course.course_code_title'))
                ),
                'ClassRoom' => array(
                    'fields' => array(
                        'ClassRoom.id',
                        'ClassRoom.room_code',
                        'ClassRoom.class_room_block_id'
                    ),
                    'ClassRoomBlock' => array(
                        'fields' => array('ClassRoomBlock.id', 'ClassRoomBlock.block_code'),
                        'Campus' => array('fields' => array('Campus.name'))
                    )
                )
            )
        );

        $this->set('classRoomCourseConstraints', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid class room course constraint'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('classRoomCourseConstraint', $this->ClassRoomCourseConstraint->read(null, $id));
    }

    public function add()
    {

        $from_delete = $this->Session->read('from_delete');
        if ($from_delete != 1) {
            if ($this->Session->read('selected_academicyear')) {
                $this->Session->delete('selected_academicyear');
            }
            if ($this->Session->read('selected_program')) {
                $this->Session->delete('selected_program');
            }
            if ($this->Session->read('selected_program_type')) {
                $this->Session->delete('selected_program_type');
            }
            if ($this->Session->read('selected_semester')) {
                $this->Session->delete('selected_semester');
            }
            if ($this->Session->read('selected_year_level')) {
                $this->Session->delete('selected_year_level');
            }
            if ($this->Session->read('selected_department')) {
                $this->Session->delete('selected_department');
            }
            if ($this->Session->read('selected_class_room_block')) {
                $this->Session->delete('selected_class_room_block');
            }
            if ($this->Session->read('classRooms')) {
                $this->Session->delete('classRooms');
            }
            if ($this->Session->read('selected_class_rooms')) {
                $this->Session->delete('selected_class_rooms');
            }
        }
        $programs = $this->ClassRoomCourseConstraint->PublishedCourse->Program->find('list');
        $programTypes = $this->ClassRoomCourseConstraint->PublishedCourse->ProgramType->find('list');
        $departments = $this->ClassRoomCourseConstraint->PublishedCourse->Department->find(
            'list',
            array('conditions' => array('Department.college_id' => $this->college_id))
        );
        $yearLevels = null;
        $yearLevels = $this->ClassRoomCourseConstraint->PublishedCourse->YearLevel->find(
            'list',
            array('conditions' => array('YearLevel.department_id' => $this->department_id))
        );
        $this->set(compact('programs', 'programTypes', 'departments', 'yearLevels'));
        if (!empty($this->request->data) && isset($this->request->data['search'])) {
            if ($this->Session->read('sections_array')) {
                $this->Session->delete('sections_array');
            }
            if ($this->Session->read('selected_published_course_id')) {
                $this->Session->delete('selected_published_course_id');
            }
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['ClassRoomCourseConstraint']['academicyear']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the academic year of the publish courses that you want to add class room course constraints.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ClassRoomCourseConstraint']['program_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the program of the publish courses that you want to add class room course constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ClassRoomCourseConstraint']['program_type_id']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the program type of the publish courses that you want to add class room course constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['ClassRoomCourseConstraint']['semester']) :
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the semester of the publish courses that you want to add class room course constraints. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                default:
                    $everythingfine = true;
            }
            if ($everythingfine) {
                $selected_academicyear = $this->request->data['ClassRoomCourseConstraint']['academicyear'];
                $this->Session->write('selected_academicyear', $selected_academicyear);
                $selected_program = $this->request->data['ClassRoomCourseConstraint']['program_id'];
                $this->Session->write('selected_program', $selected_program);
                $selected_program_type = $this->request->data['ClassRoomCourseConstraint']['program_type_id'];
                $this->Session->write('selected_program_type', $selected_program_type);
                $selected_semester = $this->request->data['ClassRoomCourseConstraint']['semester'];
                $this->Session->write('selected_semester', $selected_semester);
                //$program_type_id=$this->AcademicYear->equivalent_program_type($selected_program_type);

                if ($this->role_id == ROLE_COLLEGE) {
                    $selected_year_level = $this->request->data['ClassRoomCourseConstraint']['year_level_id'];
                    $this->Session->write('selected_year_level', $selected_year_level);
                    if (empty($selected_year_level) || ($selected_year_level == "All")) {
                        $selected_year_level = '%';
                    }
                    $selected_department = $this->request->data['ClassRoomCourseConstraint']['department_id'];
                    $this->Session->write('selected_department', $selected_department);
                    if (!empty($selected_department)) {
                        $yearLevels = $this->ClassRoomCourseConstraint->PublishedCourse->YearLevel->find(
                            'list',
                            array('conditions' => array('YearLevel.department_id' => $selected_department))
                        );
                        $conditions = array(
                            'PublishedCourse.academic_year' => $selected_academicyear,
                            'PublishedCourse.department_id' => $selected_department,
                            'PublishedCourse.program_id' =>
                                $selected_program,
                            'PublishedCourse.program_type_id' => $selected_program_type,
                            'PublishedCourse.year_level_id LIKE' => $selected_year_level,
                            'PublishedCourse.semester' => $selected_semester,
                            'PublishedCourse.drop' => 0
                        );
                    } else {
                        $conditions = array(
                            'PublishedCourse.academic_year' => $selected_academicyear,
                            'PublishedCourse.college_id' => $this->college_id,
                            'PublishedCourse.program_id' => $selected_program,
                            'PublishedCourse.program_type_id' => $selected_program_type,
                            'PublishedCourse.semester' => $selected_semester,
                            'PublishedCourse.drop' => 0,
                            "OR" => array(
                                "PublishedCourse.department_id is null",
                                "PublishedCourse.department_id" => array(0, '')
                            )
                        );
                    }
                }
                $publishedcourses = $this->ClassRoomCourseConstraint->PublishedCourse->find('all', array(
                    'conditions' => $conditions,
                    'fields' => array('PublishedCourse.id', 'PublishedCourse.section_id'),
                    'contain' => array(
                        'Section' => array('fields' => array('Section.id', 'Section.name')),
                        'Course' => array(
                            'fields' => array(
                                'Course.id',
                                'Course.course_title',
                                'Course.course_code',
                                'Course.credit',
                                'Course.lecture_hours',
                                'Course.tutorial_hours',
                                'Course.laboratory_hours'
                            )
                        )
                    )
                ));

                $sections_array = array();
                foreach ($publishedcourses as $key => $publishedcourse) {
                    $sections_array[$publishedcourse['Section']['name']][$key]['course_title'] = $publishedcourse['Course']['course_title'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['course_id'] = $publishedcourse
                    ['Course']['id'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['course_code'] = $publishedcourse
                    ['Course']['course_code'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['credit'] = $publishedcourse
                    ['Course']['credit'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['credit_detail'] = $publishedcourse
                        ['Course']['lecture_hours'] . ' ' . $publishedcourse['Course']['tutorial_hours'] . ' ' .
                        $publishedcourse['Course']['laboratory_hours'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['section_id'] =
                        $publishedcourse['PublishedCourse']['section_id'];
                    $sections_array[$publishedcourse['Section']['name']][$key]['published_course_id'] =
                        $publishedcourse['PublishedCourse']['id'];
                }

                if (empty($sections_array)) {
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'There is no course to assign class room constraints in the selected criteria.'
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                } else {
                    /*$classRoomBlocks = $this->ClassRoomCourseConstraint->ClassRoom->ClassRoomBlock->find('list',array('conditions'));

                    $classRoomBlocks_ids = $this->ClassRoomCourseConstraint->ClassRoom->ClassRoomBlock->find('list',array('conditions'=>array('ClassRoomBlock.college_id'=>$this->college_id),'fields'=>array('ClassRoomBlock.id')));

                    $classRooms = $this->ClassRoomCourseConstraint->ClassRoom->find('all', array('conditions'=>array('ClassRoom.class_room_block_id'=>$classRoomBlocks_ids, 'ClassRoom.available_for_lecture'=>1), 'fields'=>array('ClassRoom.id','ClassRoom.room_code', 'ClassRoom.class_room_block_id'),'contain'=>array('ProgramProgramTypeClassRoom'=>array('Program'=>array('fields'=>array('Program.name')),'ProgramType'=>array('fields'=>array('ProgramType.name'))), 'ClassRoomBlock'=>array('fields'=>array('ClassRoomBlock.id','ClassRoomBlock.block_code'), 'Campus'=>array('fields'=>array('Campus.name'))))));
                    //formatted class rooms by class room blocks and campus
                    $formated_classRooms = array();
                    foreach($classRooms as $crk=>$crv){
                        $formated_classRooms[$crv['ClassRoomBlock']['Campus']['name'].'-Block:'.$crv['ClassRoomBlock']['block_code']][$crv['ClassRoom']['id']] = $crv['ClassRoom'];
                        if(!empty($crv['ProgramProgramTypeClassRoom'])){
                            foreach($crv['ProgramProgramTypeClassRoom'] as $ppk=>$ppv){
                                $formated_classRooms[$crv['ClassRoomBlock']['Campus']['name'].'-Block:'.$crv['ClassRoomBlock']['block_code']][$crv['ClassRoom']['id']][$ppv['Program']['name']][]=$ppv['ProgramType']['name'];
                            }
                        }
                    }*/
                    $undergraduate_program_name = $this->ClassRoomCourseConstraint->PublishedCourse->Program->field(
                        'Program.name',
                        array('Program.id' => 1)
                    );
                    $postgraduate_program_name = $this->ClassRoomCourseConstraint->PublishedCourse->ProgramType->field(
                        'ProgramType.name',
                        array('ProgramType.id' => 2)
                    );
                    $this->Session->write('sections_array', $sections_array);
                    $this->Session->write('yearLevels', $yearLevels);
                    //$this->Session->write('formated_classRooms',$formated_classRooms);
                    $this->Session->write('undergraduate_program_name', $undergraduate_program_name);
                    $this->Session->write('postgraduate_program_name', $postgraduate_program_name);
                    $this->set(
                        compact(
                            'sections_array',
                            'yearLevels',
                            'undergraduate_program_name',
                            'postgraduate_program_name'
                        )
                    );
                }
            }
        }
        if (!empty($this->request->data) && isset($this->request->data['submit'])) {
            if (!empty($this->request->data['ClassRoomCourseConstraint']['courses'])) {
                $this->Session->write(
                    'selected_published_course_id',
                    $this->request->data['ClassRoomCourseConstraint']['courses']
                );
                if (!empty($this->request->data['ClassRoomCourseConstraint']['class_rooms'])) {
                    $isalready_record_this_constraints = 0;
                    $isalready_record_this_constraints = $this->ClassRoomCourseConstraint->find(
                        'count',
                        array(
                            'conditions' => array(
                                'ClassRoomCourseConstraint.published_course_id' => $this->request->data['ClassRoomCourseConstraint']['courses'],
                                'ClassRoomCourseConstraint.class_room_id' => $this->request->data['ClassRoomCourseConstraint']['class_rooms'],
                                'ClassRoomCourseConstraint.type' => $this->request->data['ClassRoomCourseConstraint']['type']
                            )
                        )
                    );
                    if ($isalready_record_this_constraints == 0) {
                        $this->request->data['ClassRoomCourseConstraints']['published_course_id'] =
                            $this->request->data['ClassRoomCourseConstraint']['courses'];
                        $this->request->data['ClassRoomCourseConstraints']['class_room_id'] =
                            $this->request->data['ClassRoomCourseConstraint']['class_rooms'];
                        $this->request->data['ClassRoomCourseConstraints']['type'] =
                            $this->request->data['ClassRoomCourseConstraint']['type'];
                        $this->request->data['ClassRoomCourseConstraints']['active'] =
                            $this->request->data['ClassRoomCourseConstraint']['active'];

                        $this->ClassRoomCourseConstraint->create();
                        if ($this->ClassRoomCourseConstraint->save(
                            $this->request->data['ClassRoomCourseConstraints']
                        )) {
                            $this->Session->setFlash(
                                '<span></span>' . __('The class room course constraint has been saved'),
                                'default',
                                array('class' => 'success-box success-message')
                            );
                        } else {
                            $this->Session->setFlash(
                                '<span></span>' . __(
                                    'The class room course constraint could not be saved. Please, try again.'
                                ),
                                'default',
                                array('class' => 'error-box error-message')
                            );
                        }
                    } else {
                        $this->Session->setFlash(
                            '<span></span>' . __(
                                'The class room constraint for the seclected course and class room is already recored.'
                            ),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                } else {
                    $this->Session->setFlash(
                        '<span></span>' . __(' Please select class rooms'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(' Please select course'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
            $selected_class_room_block = $this->request->data['ClassRoomCourseConstraint']['class_room_blocks'];
            $classRooms = $this->ClassRoomCourseConstraint->ClassRoom->find(
                'list',
                array(
                    'fields' => array('ClassRoom.room_code'),
                    'conditions' => array(
                        'ClassRoom.class_room_block_id' => $selected_class_room_block,
                        "OR" => array(
                            'ClassRoom.available_for_lecture' => 1,
                            "AND" => array('ClassRoom.available_for_lecture' => 0, 'ClassRoom.available_for_exam' => 0)
                        )
                    )
                )
            );
            $selected_class_rooms = $this->request->data['ClassRoomCourseConstraint']['class_rooms'];
            $this->Session->write('classRooms', $classRooms);
            $this->Session->write('selected_class_room_block', $selected_class_room_block);
            $this->Session->write('selected_class_rooms', $selected_class_rooms);

            $sections_array = $this->Session->read('sections_array');
            $yearLevels = $this->Session->read('yearLevels');
            //$formated_classRooms = $this->Session->read('formated_classRooms');
            $undergraduate_program_name = $this->Session->read('undergraduate_program_name');
            $postgraduate_program_name = $this->Session->read('postgraduate_program_name');
            $this->set(
                compact(
                    'sections_array',
                    'yearLevels',
                    'undergraduate_program_name',
                    'postgraduate_program_name',
                    'selected_class_room_block',
                    'classRooms',
                    'selected_class_room_block'
                )
            );
        }
        $sections_array = $this->Session->read('sections_array');
        $yearLevels = $this->Session->read('yearLevels');
        //$formated_classRooms = $this->Session->read('formated_classRooms');
        $undergraduate_program_name = $this->Session->read('undergraduate_program_name');
        $postgraduate_program_name = $this->Session->read('postgraduate_program_name');
        if ($this->Session->read('selected_academicyear')) {
            $selected_academicyear = $this->Session->read('selected_academicyear');
        } else {
            $selected_academicyear = $this->request->data['ClassRoomCourseConstraint']['academicyear'];
            $this->Session->write('selected_academicyear', $selected_academicyear);
        }
        if ($this->Session->read('selected_program')) {
            $selected_program = $this->Session->read('selected_program');
        } else {
            $selected_program = $this->request->data['ClassRoomCourseConstraint']['program_id'];
            $this->Session->write('selected_program', $selected_program);
        }
        if ($this->Session->read('selected_program_type')) {
            $selected_program_type = $this->Session->read('selected_program_type');
        } else {
            $selected_program_type = $this->request->data['ClassRoomCourseConstraint']['program_type_id'];
            $this->Session->write('selected_program_type', $selected_program_type);
        }
        if ($this->Session->read('selected_semester')) {
            $selected_semester = $this->Session->read('selected_semester');
        } else {
            $selected_semester = $this->request->data['ClassRoomCourseConstraint']['semester'];
            $this->Session->write('selected_semester', $selected_semester);
        }
        $selected_published_course_id = null;
        if ($this->Session->read($selected_published_course_id)) {
            $selected_published_course_id = $this->Session->read('selected_published_course_id');
        } else {
            if (isset($this->request->data['ClassRoomCourseConstraint']['courses'])) {
                $selected_published_course_id = $this->request->data['ClassRoomCourseConstraint']['courses'];
            }
        }
        if (!empty($selected_published_course_id)) {
            $courseTypes = $this->_get_course_types_array($selected_published_course_id);
        } else {
            $courseTypes = null;
        }
        if ($this->role_id == ROLE_COLLEGE) {
            if ($this->Session->read('selected_year_level')) {
                $selected_year_level = $this->Session->read('selected_year_level');
            } else {
                $selected_year_level = $this->request->data['ClassRoomCourseConstraint']['year_level_id'];
                $this->Session->write('selected_year_level', $selected_year_level);
            }
            if (empty($selected_year_level) || ($selected_year_level == "All")) {
                $selected_year_level = '%';
            }
            if ($this->Session->read('selected_department')) {
                $selected_department = $this->Session->read('selected_department');
            } else {
                $selected_department = $this->request->data['ClassRoomCourseConstraint']['department_id'];
                $this->Session->write('selected_department', $selected_department);
            }
            if (empty($selected_department)) {
                $conditions = array(
                    'PublishedCourse.academic_year' => $selected_academicyear,
                    'PublishedCourse.college_id' => $this->college_id,
                    'PublishedCourse.program_id' => $selected_program,
                    'PublishedCourse.program_type_id' => $selected_program_type,
                    'PublishedCourse.semester' => $selected_semester,
                    'PublishedCourse.drop' => 0,
                    "OR" => array(
                        "PublishedCourse.department_id is null",
                        "PublishedCourse.department_id" => array(0, '')
                    )
                );
            } else {
                $conditions = array(
                    'PublishedCourse.academic_year' =>
                        $selected_academicyear,
                    'PublishedCourse.department_id' => $selected_department,
                    'PublishedCourse.program_id' => $selected_program,
                    'PublishedCourse.program_type_id' =>
                        $selected_program_type,
                    'PublishedCourse.year_level_id LIKE' => $selected_year_level,
                    'PublishedCourse.semester' => $selected_semester,
                    'PublishedCourse.drop' => 0
                );
            }
        }
        if ($this->Session->read('selected_class_room_block')) {
            $selected_class_room_block = $this->Session->read('selected_class_room_block');
        } else {
            if (isset($this->request->data['ClassRoomCourseConstraint']['class_room_blocks'])) {
                $selected_class_room_block = $this->request->data['ClassRoomCourseConstraint']['class_room_blocks'];
                $this->Session->write('selected_class_room_block', $selected_class_room_block);
            }
        }
        if ($this->Session->read('selected_class_rooms')) {
            $selected_class_rooms = $this->Session->read('selected_class_rooms');
        } else {
            //$selected_class_rooms = $this->request->data['ClassRoomClassPeriodConstraint']['class_room_id'];
        }
        $classRoomBlocks = $this->ClassRoomCourseConstraint->ClassRoom->ClassRoomBlock->find(
            'all',
            array(
                'fields' => array('ClassRoomBlock.id', 'ClassRoomBlock.block_code'),
                'conditions' => array('ClassRoomBlock.college_id' => $this->college_id),
                'contain' => array('Campus' => array('fields' => array('Campus.name')))
            )
        );

        $formatted_class_room_blocks = array();
        if (!empty($classRoomBlocks)) {
            foreach ($classRoomBlocks as $classRoomBlock) {
                $formatted_class_room_blocks[$classRoomBlock['Campus']['name']][$classRoomBlock['ClassRoomBlock']['id']] = $classRoomBlock['ClassRoomBlock']['block_code'];
            }
        }
        //debug($formatted_class_room_blocks);
        if ($this->Session->read('classRooms')) {
            $classRooms = $this->Session->read('classRooms');
        } else {
            if (!empty($selected_class_room_block)) {
                $classRooms = $this->ClassRoomCourseConstraint->ClassRoom->find(
                    'list',
                    array(
                        'fields' => array('ClassRoom.room_code'),
                        'conditions' => array(
                            'ClassRoom.class_room_block_id' => $selected_class_room_block,
                            "OR" => array(
                                'ClassRoom.available_for_lecture' => 1,
                                "AND" => array(
                                    'ClassRoom.available_for_lecture' => 0,
                                    'ClassRoom.available_for_exam' => 0
                                )
                            )
                        )
                    )
                );
            } else {
                $classRooms = null;
            }
        }
        $publishedCourses_id_array = $this->ClassRoomCourseConstraint->PublishedCourse->find(
            'list',
            array('fields' => array('PublishedCourse.id'), 'conditions' => $conditions)
        );

        $classRoomCourseConstraints = $this->ClassRoomCourseConstraint->find(
            'all',
            array(
                'conditions' => array('ClassRoomCourseConstraint.published_course_id' => $publishedCourses_id_array),
                'contain' => array(
                    'PublishedCourse' => array(
                        'fields' => array('PublishedCourse.id'),
                        'Section' => array('fields' => array('Section.name')),
                        'Course' => array('fields' => array('Course.id', 'Course.course_code_title'))
                    ),
                    'ClassRoom' => array(
                        'fields' => array(
                            'ClassRoom.id',
                            'ClassRoom.room_code',
                            'ClassRoom.class_room_block_id'
                        ),
                        'ClassRoomBlock' => array(
                            'fields' => array('ClassRoomBlock.id', 'ClassRoomBlock.block_code'),
                            'Campus' => array('fields' => array('Campus.name'))
                        )
                    )
                )
            )
        );

        $this->set(
            compact(
                'classRoomCourseConstraints',
                'yearLevels',
                'selected_academicyear',
                'selected_program',
                'selected_program_type',
                'selected_semester',
                'selected_department',
                'selected_year_level',
                'formatted_class_room_blocks',
                'classRooms',
                'selected_class_rooms',
                'selected_class_room_block',
                'selected_published_course_id',
                'courseTypes'
            )
        );
        if ($this->Session->read('from_delete')) {
            $this->set(
                compact('sections_array', 'yearLevels', 'undergraduate_program_name', 'postgraduate_program_name')
            );
            $this->Session->delete('from_delete');
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid class room course constraint'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->ClassRoomCourseConstraint->save($this->request->data)) {
                $this->Session->setFlash(__('The class room course constraint has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The class room course constraint could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->ClassRoomCourseConstraint->read(null, $id);
        }
        $publishedCourses = $this->ClassRoomCourseConstraint->PublishedCourse->find('list');
        $classRooms = $this->ClassRoomCourseConstraint->ClassRoom->find('list');
        $this->set(compact('publishedCourses', 'classRooms'));
    }

    public function delete($id = null, $from = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                '<span></span> ' . __('Invalid id for class room course constraint.'),
                'default',
                array('class' => 'error-box error-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        }

        $from_delete = 1;
        $this->Session->write('from_delete', $from_delete);
        $areyou_eligible_to_delete = $this->ClassRoomCourseConstraint->beforeDeleteCheckEligibility(
            $id,
            $this->college_id
        );
        if ($areyou_eligible_to_delete == true) {
            if ($this->ClassRoomCourseConstraint->delete($id)) {
                $this->Session->setFlash(
                    '<span></span> ' . __('Class room course constraint deleted'),
                    'default',
                    array('class' => 'success-box success-message')
                );
                if (empty($from)) {
                    return $this->redirect(array('action' => 'index'));
                } else {
                    return $this->redirect(array('action' => 'add'));
                }
            }
            $this->Session->setFlash(
                '<span></span> ' . __('Class room course constraint was not deleted.'),
                'default',
                array('class' => 'error-box error-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        } else {
            $this->Session->setFlash(
                '<span></span> ' . __('You are not eligible to delete this Class room course constraint.'),
                'default',
                array('class' => 'error-box error-message')
            );
            if (empty($from)) {
                return $this->redirect(array('action' => 'index'));
            } else {
                return $this->redirect(array('action' => 'add'));
            }
        }
    }

    public function get_class_rooms($class_room_block_id = null)
    {

        if (!empty($class_room_block_id)) {
            $this->layout = 'ajax';
            $classRooms = $this->ClassRoomCourseConstraint->ClassRoom->find(
                'list',
                array(
                    'fields' => array('ClassRoom.room_code'),
                    'conditions' => array(
                        'ClassRoom.class_room_block_id' => $class_room_block_id,
                        "OR" => array(
                            'ClassRoom.available_for_lecture' => 1,
                            "AND" => array('ClassRoom.available_for_lecture' => 0, 'ClassRoom.available_for_exam' => 0)
                        )
                    )
                )
            );
            $this->set(compact('classRooms'));
        }
    }

    public function get_year_level($department_id = null)
    {

        if (!empty($department_id)) {
            $this->layout = 'ajax';
            //debug($this->request->data);
            $yearLevels = $this->ClassRoomCourseConstraint->PublishedCourse->YearLevel->find('list', array(
                'conditions' =>
                    array('YearLevel.department_id' => $department_id)
            ));
            $this->set(compact('yearLevels'));
        }
    }

    public function get_course_types($publishedCourse_id = null)
    {

        debug($publishedCourse_id);
        if (!empty($publishedCourse_id)) {
            //$this->layout = 'ajax';
            $courseTypes = $this->_get_course_types_array($publishedCourse_id);
            $this->set(compact('courseTypes'));
        }
    }

    public function _get_course_types_array($publishedCourse_id = null)
    {

        if (!empty($publishedCourse_id)) {
            $publishedCourses = $this->ClassRoomCourseConstraint->PublishedCourse->find(
                'first',
                array(
                    'fields' => array('PublishedCourse.id'),
                    'conditions' => array('PublishedCourse.id' => $publishedCourse_id),
                    'contain' => array(
                        'Course' => array(
                            'fields' => array(
                                'Course.id',
                                'Course.lecture_hours',
                                'Course.tutorial_hours',
                                'Course.laboratory_hours'
                            )
                        )
                    )
                )
            );

            $courseTypes = array();
            if (!empty($publishedCourses['Course']['lecture_hours'])) {
                $courseTypes['lecture'] = 'Lecture';
            }
            if (!empty($publishedCourses['Course']['tutorial_hours'])) {
                $courseTypes['tutorial'] = 'Tutorial';
            }
            if (!empty($publishedCourses['Course']['laboratory_hours'])) {
                $courseTypes['laboratory'] = 'Laboratory';
            }
            return $courseTypes;
        }
    }
}

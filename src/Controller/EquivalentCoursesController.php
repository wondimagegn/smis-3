<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class EquivalentCoursesController extends AppController
{

    public $name = 'EquivalentCourses';

    public $menuOptions = array(
        'parent' => 'curriculums',
        'alias' => array(
            'add' => 'Map Equivalent Courses',
            'index' => 'View Mapped/Equivalent Courses'
        )
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
    }

    public function index()
    {

        $this->EquivalentCourse->recursive = 1;

        $this->paginate = array(
            'contain' => array(
                'CourseForSubstitued' => array('Department', 'Curriculum'),
                'CourseBeSubstitued' => array('Department', 'Curriculum')
            ),
            'limit' => 100,
            'maxLimit' => 200
        );

        $this->__init_search_index();

        if ($this->Session->read('search_data_index')) {
            $this->request->data['viewCourseMap'] = true;
        }

        debug($this->request->data);

        $this->paginate['conditions'][] = array('CourseForSubstitued.department_id' => $this->department_id);

        if (isset($this->request->data['Search']['curriculum_id']) && !empty($this->request->data['Search']['curriculum_id'])) {
            $this->paginate['conditions'][] = array('CourseForSubstitued.curriculum_id' => $this->request->data['Search']['curriculum_id']);
            $isCurriculumApproved = $this->EquivalentCourse->CourseBeSubstitued->Curriculum->field(
                'registrar_approved',
                array('Curriculum.id' => $this->request->data['Search']['curriculum_id'])
            );
        }

        if (isset($this->request->data['Search']['program_id']) && !empty($this->request->data['Search']['program_id'])) {
            $curriculums = $this->EquivalentCourse->CourseBeSubstitued->Curriculum->find('list', array(
                'conditions' => array(
                    'Curriculum.department_id' => $this->department_id,
                    'Curriculum.program_id' => $this->request->data['Search']['program_id']
                ),
                'fields' => array('id')
            ));

            $this->paginate['conditions'][] = array('CourseForSubstitued.curriculum_id' => $curriculums);
        }

        if (isset($this->request->data['Search']['title']) && !empty($this->request->data['Search']['title'])) {
            $this->paginate['conditions'][] = array(
                'CourseForSubstitued.course_title like ' => '%' . trim($this->request->data['Search']['title']) . '%'
            );
        }

        if (!empty($this->request->data['Search'])) {
            $this->Paginator->settings = $this->paginate;
        }

        if (isset($this->Paginator->settings['conditions'])) {
            $equivalentCourses = $this->Paginator->paginate('EquivalentCourse');
        } else {
            $equivalentCourses = array();
        }

        if (!empty($this->request->data['viewCourseMap'])) {
            $this->__init_search_index();
        }

        if (empty($equivalentCourses) && isset($this->request->data) && !empty($this->request->data['Search'])) {
            $this->Flash->info('There is no equivalent course mapping found in the given criteria.');
        }

        if (!empty($this->request->data['Search']['program_id'])) {
            $curriculums = $this->EquivalentCourse->CourseBeSubstitued->Curriculum->find('list', array(
                'conditions' => array(
                    'Curriculum.department_id' => $this->department_id,
                    'Curriculum.program_id' => $this->request->data['Search']['program_id']
                ),
                'fields' => array('id', 'curriculum_detail')
            ));
        } else {
            $curriculums = array();
        }

        $programs = $this->EquivalentCourse->CourseBeSubstitued->Curriculum->Program->find(
            'list',
            array('conditions' => array('Program.id' => $this->program_ids, 'Program.active' => 1))
        );

        $this->set(compact('programs', 'curriculums', 'equivalentCourses', 'isCurriculumApproved'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid equivalent course id');
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('equivalentCourse', $this->EquivalentCourse->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->EquivalentCourse->create();

            if (empty($this->request->data['EquivalentCourse']['course_for_substitued_id']) || empty($this->request->data['EquivalentCourse']['course_be_substitued_id'])) {
                $check_duplicate = 0;
            } else {
                $check_duplicate = $this->EquivalentCourse->find('count', array(
                    'conditions' => array(
                        'course_for_substitued_id' => $this->request->data['EquivalentCourse']['course_for_substitued_id'],
                        'course_be_substitued_id' => $this->request->data['EquivalentCourse']['course_be_substitued_id']
                    )
                ));
            }

            //TO Do: before saving, Check/detect cyclic course mappings that refer to each other,
            // affects system data integrity for already graduated student documents( like student copy and others files for future, if prepared from the system and if there is no student copy attached to student folder at the time of graduation, Neway

            if ($check_duplicate == 0) {
                if ($this->EquivalentCourse->isSimilarCurriculum($this->request->data)) {
                    if ($this->EquivalentCourse->save($this->request->data)) {
                        $this->Flash->success('The equivalent course has been saved.');
                        //$this->redirect(array('action' => 'index'));
                    } else {
                        $this->Flash->error('The equivalent course could not be saved. Please, try again.');
                    }
                } else {
                    $error = $this->EquivalentCourse->invalidFields();
                    if (isset($error['error'])) {
                        $this->Flash->error($error['error'][0]);
                    }
                }
            } else {
                $this->Flash->warning('The selected courses has already mapped. You dont need to map it again');
                $this->redirect(array('action' => 'index'));
            }
        }


        $departments = $this->EquivalentCourse->CourseBeSubstitued->Department->find('all', array(
            'conditions' => array(
                'Department.active' => 1,
            ),
            'contain' => array(
                'College' => array('id', 'name')
            ),
            'fields' => array('id', 'name'),
        ));

        $return = array();

        if (!empty($departments)) {
            foreach ($departments as $dep_id => $dep_name) {
                $return[$dep_name['College']['name']][$dep_name['Department']['id']] = $dep_name['Department']['name'];
            }
        }

        $departments = $return;

        $curriculum_graduateed = $this->EquivalentCourse->CourseBeSubstitued->Curriculum->Student->find('list', array(
            'conditions' => array(
                'Student.department_id' => $this->department_id,
                'OR' => array(
                    'Student.graduated' => 1,
                    'Student.id in (select student_id from senate_lists)'
                )
            ),
            'fields' => array('Student.curriculum_id', 'Student.curriculum_id')
        ));

        $curriculums = $this->EquivalentCourse->CourseBeSubstitued->Curriculum->find('list', array(
            'conditions' => array(
                'Curriculum.department_id' => $this->department_id,
                'Curriculum.registrar_approved' => 1,
                //"NOT" => array('Curriculum.id'  => $curriculum_graduateed),
            ),
            'fields' => array('id', 'curriculum_detail')
        ));

        debug($curriculums);
        debug($curriculum_graduateed);

        if (empty($this->request->data)) {
            $courseBeSubstitueds = array();
            $otherCurriculums = array();
        }

        if (empty($this->request->data['EquivalentCourse']['other_curriculum_id'])) {
            $otherCurriculums = array();
        }

        if (!empty($this->request->data['EquivalentCourse']['other_curriculum_id'])) {
            $other_department_id = $this->EquivalentCourse->CourseBeSubstitued->Curriculum->field(
                'department_id',
                array(
                    'Curriculum.id' => $this->request->data['EquivalentCourse']['other_curriculum_id'],
                    'Curriculum.registrar_approved' => 1
                )
            );

            $otherCurriculums = $this->EquivalentCourse->CourseBeSubstitued->Curriculum->find('list', array(
                'conditions' => array(
                    'Curriculum.department_id' => $other_department_id,
                    'Curriculum.registrar_approved' => 1
                ),
                'fields' => array('id', 'curriculum_detail')
            ));
        }

        if (!empty($this->request->data['EquivalentCourse']['course_be_substitued_id'])) {
            $curriculum_id = $this->EquivalentCourse->CourseBeSubstitued->field(
                'curriculum_id',
                array('CourseBeSubstitued.id' => $this->request->data['EquivalentCourse']['course_be_substitued_id'])
            );

            $courseBeSubstitueds = $this->EquivalentCourse->CourseBeSubstitued->find('list', array(
                'conditions' => array(
                    'CourseBeSubstitued.curriculum_id' => $curriculum_id
                ),
                'fields' => array('id', 'course_code', 'course_title')
            ));
        }

        if (!empty($this->request->data['EquivalentCourse']['course_for_substitued_id'])) {
            $curriculum_id = $this->EquivalentCourse->CourseBeSubstitued->field(
                'curriculum_id',
                array('CourseBeSubstitued.id' => $this->request->data['EquivalentCourse']['course_for_substitued_id'])
            );

            $courseForSubstitueds = $this->EquivalentCourse->CourseBeSubstitued->find('list', array(
                'conditions' => array(
                    'CourseBeSubstitued.curriculum_id' => $curriculum_id
                ),
                'fields' => array('id', 'course_code', 'course_title')
            ));
        }

        $this->set(
            compact(
                'courseForSubstitueds',
                'departments',
                'curriculums',
                'otherCurriculums',
                'courseBeSubstitueds'
            )
        );
    }


    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Flash->error('Invalid equivalent course');
            return $this->redirect(array('action' => 'index'));
        }

        if (!$this->EquivalentCourse->checkStudentTakeingEquivalentCourseAndDenyDelete($id, $this->department_id)) {
            $this->Flash->error('Equivalent course map could not be edited. It is associated with students.');
            return $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data)) {
            if ($this->EquivalentCourse->save($this->request->data)) {
                $this->Flash->success('The equivalent course mapping has been saved.');
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error('The equivalent course mapping could not be saved. Please, try again.');
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->EquivalentCourse->read(null, $id);
        }

        $courseForSubstitueds = $this->EquivalentCourse->CourseForSubstitued->find('list', array(
            'conditions' => array(
                'CourseForSubstitued.department_id' => $this->department_id
            ),
            'fields' => array('id', 'course_title')
        ));

        $courseBeSubstitueds = $this->EquivalentCourse->CourseBeSubstitued->find(
            'list',
            array('fields' => array('id', 'course_title'))
        );

        $this->set(compact('courseForSubstitueds', 'courseBeSubstitueds'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid id for equivalent course');
            return $this->redirect(array('action' => 'index'));
        }

        //TODO check the taken equivalent course
        //Attach and Deattch, curriculum history for the student should be kept in the tables

        if ($this->EquivalentCourse->checkStudentTakeingEquivalentCourseAndDenyDelete($id, $this->department_id)) {
            if ($this->EquivalentCourse->delete($id)) {
                $this->Flash->success('Equivalent course mapping is deleted.');
                return $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Flash->error('Equivalent course map could not be deleted. It is associated with students.');
        }

        return $this->redirect(array('action' => 'index'));
    }

    public function __init_search_index()
    {

        if (!empty($this->request->data['Search'])) {
            $search_session = $this->request->data['Search'];
            $this->Session->write('search_data_index', $search_session);
        } else {
            $search_session = $this->Session->read('search_data_index');
            $this->request->data['Search'] = $search_session;
        }
    }
}

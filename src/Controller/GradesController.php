<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class GradesController extends AppController
{

    public $name = 'Grades';

    public $menuOptions = array(
        'exclude' => array('index', 'add', 'get_grade_combo'),
    );

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow('get_grade_combo');
    }

    public function beforeRender($event)
    {

        parent::beforeRender($event);
    }

    public function index()
    {

        //College
        if ($this->Acl->check($this->Auth->user(), 'controllers/examGrades/college_grade_view')) {
            return $this->redirect(array('controller' => 'examGrades', 'action' => 'college_grade_view'));
        } //Department
        else {
            if ($this->Acl->check($this->Auth->user(), 'controllers/examResults/submit_grade_for_instructor')) {
                return $this->redirect(array('controller' => 'examResults', 'action' => 'submit_grade_for_instructor'));
            } //Freshman
            else {
                if ($this->Acl->check(
                    $this->Auth->user(),
                    'controllers/examResults/submit_freshman_grade_for_instructor'
                )) {
                    return $this->redirect(
                        array('controller' => 'examResults', 'action' => 'submit_freshman_grade_for_instructor')
                    );
                } //Registrar
                else {
                    if ($this->Acl->check($this->Auth->user(), 'controllers/examGrades/registrar_grade_view')) {
                        return $this->redirect(array('controller' => 'examGrades', 'action' => 'registrar_grade_view'));
                    } //Instructor
                    else {
                        if ($this->Acl->check($this->Auth->user(), 'controllers/examResults/add')) {
                            return $this->redirect(array('controller' => 'examResults', 'action' => 'add'));
                        } //Student
                        else {
                            if ($this->Acl->check($this->Auth->user(), 'controllers/examGrades/student_grade_view')) {
                                return $this->redirect(
                                    array('controller' => 'examGrades', 'action' => 'student_grade_view')
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error('Invalid grade');
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('grade', $this->Grade->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Grade->create();
            debug($this->Grade->checkGradeIsUnique($this->request->data));
            /*
            if ($this->Grade->save($this->request->data)) {
                $this->Flash->success('The grade has been saved');
                return $this->redirect(array('action' => 'add'));
            } else {
                $this->Flash->error('The grade could not be saved. Please, try again.');
            }
            */
        }

        $gradeTypes = $this->Grade->GradeType->find('list', array('fields' => array('id', 'type')));
        $gradesss = $this->Grade->find('all');
        $this->set(compact('gradeTypes', 'gradesss'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Flash->error('Invalid grade');
            return $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data)) {
            if ($this->Grade->save($this->request->data)) {
                $this->Flash->success('The grade has been saved');
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error('The grade could not be saved. Please, try again.');
            }
        }

        if (empty($this->request->data)) {
            $this->request->data = $this->Grade->read(null, $id);
        }

        $gradeTypes = $this->Grade->GradeType->find('list', array('fields' => array('id', 'type')));
        $gradesss = $this->Grade->find('all', array('conditions' => array('Grade.id <> ' => $id)));
        $this->set(compact('gradeTypes', 'gradesss'));
    }


    public function delete($id = null, $action_controller_id = null)
    {

        if (!empty($action_controller_id)) {
            $grade_type = explode('~', $action_controller_id);
        }

        $this->Grade->id = $id;

        if (!$this->Grade->exists()) {
            $this->Flash->error('Invalid id for grade');
            if (!empty($grade_type[0]) && !empty($grade_type[1]) && !empty($grade_type[2])) {
                $this->redirect(array('controller' => $grade_type[1], 'action' => $grade_type[0], $grade_type[2]));
            }
            return $this->redirect(array('action' => 'index'));
        }

        if ($this->Grade->allowDelete($id)) {
            if ($this->Grade->delete($id)) {
                $this->Flash->success('Grade deleted');
                if (!empty($grade_type[0]) && !empty($grade_type[1]) && !empty($grade_type[2])) {
                    $this->redirect(array('controller' => $grade_type[1], 'action' => $grade_type[0], $grade_type[2]));
                }
                $this->redirect(array('action' => 'index'));
            }
        } else {
            $this->Flash->error('Grade represenation was not deleted. Students has already used this grade.');
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function get_grade_combo($grade_type_id = null)
    {

        $this->layout = 'ajax';
        $grades = $this->Grade->find(
            'list',
            array('conditions' => array('Grade.grade_type_id' => $grade_type_id), 'fields' => array('id', 'grade'))
        );
        $this->set(compact('grades'));
    }
}

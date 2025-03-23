<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class CoursesWeblinksController extends AppController
{

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }
    public function index()
    {
        $this->paginate = [
            'contain' => ['Courses', 'Weblinks'],
        ];
        $coursesWeblinks = $this->paginate($this->CoursesWeblinks);

        $this->set(compact('coursesWeblinks'));
    }

    public function view($id = null)
    {
        $coursesWeblink = $this->CoursesWeblinks->get($id, [
            'contain' => ['Courses', 'Weblinks'],
        ]);

        $this->set('coursesWeblink', $coursesWeblink);
    }

    public function add()
    {
        $coursesWeblink = $this->CoursesWeblinks->newEntity();
        if ($this->request->is('post')) {
            $coursesWeblink = $this->CoursesWeblinks->patchEntity($coursesWeblink, $this->request->getData());
            if ($this->CoursesWeblinks->save($coursesWeblink)) {
                $this->Flash->success(__('The courses weblink has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courses weblink could not be saved. Please, try again.'));
        }
         $this->set(compact('coursesWeblink'));
    }


    public function edit($id = null)
    {
        $coursesWeblink = $this->CoursesWeblinks->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $coursesWeblink = $this->CoursesWeblinks->patchEntity($coursesWeblink, $this->request->getData());
            if ($this->CoursesWeblinks->save($coursesWeblink)) {
                $this->Flash->success(__('The courses weblink has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The courses weblink could not be saved. Please, try again.'));
        }
         $this->set(compact('coursesWeblink'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $coursesWeblink = $this->CoursesWeblinks->get($id);
        if ($this->CoursesWeblinks->delete($coursesWeblink)) {
            $this->Flash->success(__('The courses weblink has been deleted.'));
        } else {
            $this->Flash->error(__('The courses weblink could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

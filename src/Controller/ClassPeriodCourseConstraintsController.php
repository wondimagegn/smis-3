<?php
namespace App\Controller;

use App\Controller\AppController;


class ClassPeriodCourseConstraintsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['PublishedCourses', 'ClassPeriods'],
        ];
        $classPeriodCourseConstraints = $this->paginate($this->ClassPeriodCourseConstraints);

        $this->set(compact('classPeriodCourseConstraints'));
    }


    public function view($id = null)
    {
        $classPeriodCourseConstraint = $this->ClassPeriodCourseConstraints->get($id, [
            'contain' => ['PublishedCourses', 'ClassPeriods'],
        ]);

        $this->set('classPeriodCourseConstraint', $classPeriodCourseConstraint);
    }

    public function add()
    {
        $classPeriodCourseConstraint = $this->ClassPeriodCourseConstraints->newEntity();
        if ($this->request->is('post')) {
            $classPeriodCourseConstraint = $this->ClassPeriodCourseConstraints->patchEntity($classPeriodCourseConstraint, $this->request->getData());
            if ($this->ClassPeriodCourseConstraints->save($classPeriodCourseConstraint)) {
                $this->Flash->success(__('The class period course constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class period course constraint could not be saved. Please, try again.'));
        }
        $publishedCourses = $this->ClassPeriodCourseConstraints->PublishedCourses->find('list', ['limit' => 200]);
        $classPeriods = $this->ClassPeriodCourseConstraints->ClassPeriods->find('list', ['limit' => 200]);
        $this->set(compact('classPeriodCourseConstraint', 'publishedCourses', 'classPeriods'));
    }


    public function edit($id = null)
    {
        $classPeriodCourseConstraint = $this->ClassPeriodCourseConstraints->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $classPeriodCourseConstraint = $this->ClassPeriodCourseConstraints->patchEntity($classPeriodCourseConstraint, $this->request->getData());
            if ($this->ClassPeriodCourseConstraints->save($classPeriodCourseConstraint)) {
                $this->Flash->success(__('The class period course constraint has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class period course constraint could not be saved. Please, try again.'));
        }
        $publishedCourses = $this->ClassPeriodCourseConstraints->PublishedCourses->find('list', ['limit' => 200]);
        $classPeriods = $this->ClassPeriodCourseConstraints->ClassPeriods->find('list', ['limit' => 200]);
        $this->set(compact('classPeriodCourseConstraint', 'publishedCourses', 'classPeriods'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $classPeriodCourseConstraint = $this->ClassPeriodCourseConstraints->get($id);
        if ($this->ClassPeriodCourseConstraints->delete($classPeriodCourseConstraint)) {
            $this->Flash->success(__('The class period course constraint has been deleted.'));
        } else {
            $this->Flash->error(__('The class period course constraint could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

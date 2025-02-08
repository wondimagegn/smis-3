<?php
namespace App\Controller;

use App\Controller\AppController;

class ResultEntryAssignmentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'PublishedCourses', 'CourseRegistrations', 'CourseAdds'],
        ];
        $resultEntryAssignments = $this->paginate($this->ResultEntryAssignments);

        $this->set(compact('resultEntryAssignments'));
    }

    public function view($id = null)
    {
        $resultEntryAssignment = $this->ResultEntryAssignments->get($id, [
            'contain' => ['Students', 'PublishedCourses', 'CourseRegistrations', 'CourseAdds'],
        ]);

        $this->set('resultEntryAssignment', $resultEntryAssignment);
    }

    public function add()
    {
        $resultEntryAssignment = $this->ResultEntryAssignments->newEntity();
        if ($this->request->is('post')) {
            $resultEntryAssignment = $this->ResultEntryAssignments->patchEntity($resultEntryAssignment, $this->request->getData());
            if ($this->ResultEntryAssignments->save($resultEntryAssignment)) {
                $this->Flash->success(__('The result entry assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The result entry assignment could not be saved. Please, try again.'));
        }
        $this->set(compact('resultEntryAssignment'));
    }


    public function edit($id = null)
    {
        $resultEntryAssignment = $this->ResultEntryAssignments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $resultEntryAssignment = $this->ResultEntryAssignments->patchEntity($resultEntryAssignment, $this->request->getData());
            if ($this->ResultEntryAssignments->save($resultEntryAssignment)) {
                $this->Flash->success(__('The result entry assignment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The result entry assignment could not be saved. Please, try again.'));
        }
        $this->set(compact('resultEntryAssignment'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $resultEntryAssignment = $this->ResultEntryAssignments->get($id);
        if ($this->ResultEntryAssignments->delete($resultEntryAssignment)) {
            $this->Flash->success(__('The result entry assignment has been deleted.'));
        } else {
            $this->Flash->error(__('The result entry assignment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

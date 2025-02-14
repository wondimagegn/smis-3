<?php
namespace App\Controller;

use App\Controller\AppController;

class CurriculumAttachmentsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'Curriculums'],
        ];
        $curriculumAttachments = $this->paginate($this->CurriculumAttachments);

        $this->set(compact('curriculumAttachments'));
    }


    public function view($id = null)
    {
        $curriculumAttachment = $this->CurriculumAttachments->get($id, [
            'contain' => ['Students', 'Curriculums'],
        ]);

        $this->set('curriculumAttachment', $curriculumAttachment);
    }


    public function add()
    {
        $curriculumAttachment = $this->CurriculumAttachments->newEntity();
        if ($this->request->is('post')) {
            $curriculumAttachment = $this->CurriculumAttachments->patchEntity($curriculumAttachment, $this->request->getData());
            if ($this->CurriculumAttachments->save($curriculumAttachment)) {
                $this->Flash->success(__('The curriculum attachment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The curriculum attachment could not be saved. Please, try again.'));
        }
        $this->set(compact('curriculumAttachment'));
    }


    public function edit($id = null)
    {
        $curriculumAttachment = $this->CurriculumAttachments->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $curriculumAttachment = $this->CurriculumAttachments->patchEntity($curriculumAttachment, $this->request->getData());
            if ($this->CurriculumAttachments->save($curriculumAttachment)) {
                $this->Flash->success(__('The curriculum attachment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The curriculum attachment could not be saved. Please, try again.'));
        }
        $this->set(compact('curriculumAttachment'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $curriculumAttachment = $this->CurriculumAttachments->get($id);
        if ($this->CurriculumAttachments->delete($curriculumAttachment)) {
            $this->Flash->success(__('The curriculum attachment has been deleted.'));
        } else {
            $this->Flash->error(__('The curriculum attachment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

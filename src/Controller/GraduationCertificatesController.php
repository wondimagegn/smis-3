<?php
namespace App\Controller;

use App\Controller\AppController;

class GraduationCertificatesController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs', 'ProgramTypes'],
        ];
        $graduationCertificates = $this->paginate($this->GraduationCertificates);

        $this->set(compact('graduationCertificates'));
    }


    public function view($id = null)
    {
        $graduationCertificate = $this->GraduationCertificates->get($id, [
            'contain' => ['Programs', 'ProgramTypes'],
        ]);

        $this->set('graduationCertificate', $graduationCertificate);
    }


    public function add()
    {
        $graduationCertificate = $this->GraduationCertificates->newEntity();
        if ($this->request->is('post')) {
            $graduationCertificate = $this->GraduationCertificates->patchEntity($graduationCertificate, $this->request->getData());
            if ($this->GraduationCertificates->save($graduationCertificate)) {
                $this->Flash->success(__('The graduation certificate has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The graduation certificate could not be saved. Please, try again.'));
        }
        $this->set(compact('graduationCertificate'));
    }


    public function edit($id = null)
    {
        $graduationCertificate = $this->GraduationCertificates->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $graduationCertificate = $this->GraduationCertificates->patchEntity($graduationCertificate, $this->request->getData());
            if ($this->GraduationCertificates->save($graduationCertificate)) {
                $this->Flash->success(__('The graduation certificate has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The graduation certificate could not be saved. Please, try again.'));
        }
        $this->set(compact('graduationCertificate'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $graduationCertificate = $this->GraduationCertificates->get($id);
        if ($this->GraduationCertificates->delete($graduationCertificate)) {
            $this->Flash->success(__('The graduation certificate has been deleted.'));
        } else {
            $this->Flash->error(__('The graduation certificate could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

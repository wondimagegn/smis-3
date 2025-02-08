<?php
namespace App\Controller;

use App\Controller\AppController;

class HighSchoolEducationBackgroundsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Regions', 'Students'],
        ];
        $highSchoolEducationBackgrounds = $this->paginate($this->HighSchoolEducationBackgrounds);

        $this->set(compact('highSchoolEducationBackgrounds'));
    }


    public function view($id = null)
    {
        $highSchoolEducationBackground = $this->HighSchoolEducationBackgrounds->get($id, [
            'contain' => ['Regions', 'Students'],
        ]);

        $this->set('highSchoolEducationBackground', $highSchoolEducationBackground);
    }

    public function add()
    {
        $highSchoolEducationBackground = $this->HighSchoolEducationBackgrounds->newEntity();
        if ($this->request->is('post')) {
            $highSchoolEducationBackground = $this->HighSchoolEducationBackgrounds->patchEntity($highSchoolEducationBackground, $this->request->getData());
            if ($this->HighSchoolEducationBackgrounds->save($highSchoolEducationBackground)) {
                $this->Flash->success(__('The high school education background has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The high school education background could not be saved. Please, try again.'));
        }
        $this->set(compact('highSchoolEducationBackground'));
    }


    public function edit($id = null)
    {
        $highSchoolEducationBackground = $this->HighSchoolEducationBackgrounds->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $highSchoolEducationBackground = $this->HighSchoolEducationBackgrounds->patchEntity($highSchoolEducationBackground, $this->request->getData());
            if ($this->HighSchoolEducationBackgrounds->save($highSchoolEducationBackground)) {
                $this->Flash->success(__('The high school education background has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The high school education background could not be saved. Please, try again.'));
        }
        $this->set(compact('highSchoolEducationBackground'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $highSchoolEducationBackground = $this->HighSchoolEducationBackgrounds->get($id);
        if ($this->HighSchoolEducationBackgrounds->delete($highSchoolEducationBackground)) {
            $this->Flash->success(__('The high school education background has been deleted.'));
        } else {
            $this->Flash->error(__('The high school education background could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

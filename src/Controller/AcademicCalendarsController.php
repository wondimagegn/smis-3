<?php
namespace App\Controller;

use App\Controller\AppController;

class AcademicCalendarsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs', 'ProgramTypes'],
        ];
        $academicCalendars = $this->paginate($this->AcademicCalendars);

        $this->set(compact('academicCalendars'));
    }

    public function view($id = null)
    {
        $academicCalendar = $this->AcademicCalendars->get($id, [
            'contain' => [ 'Programs', 'ProgramTypes', 'ExtendingAcademicCalendars'],
        ]);

        $this->set('academicCalendar', $academicCalendar);
    }

    public function add()
    {
        $academicCalendar = $this->AcademicCalendars->newEntity();
        if ($this->request->is('post')) {
            $academicCalendar = $this->AcademicCalendars->patchEntity($academicCalendar, $this->request->getData());
            if ($this->AcademicCalendars->save($academicCalendar)) {
                $this->Flash->success(__('The academic calendar has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The academic calendar could not be saved. Please, try again.'));
        }
        $programs = $this->AcademicCalendars->Programs->find('list', ['limit' => 200]);
        $programTypes = $this->AcademicCalendars->ProgramTypes->find('list', ['limit' => 200]);
        $this->set(compact('academicCalendar',
            'programs', 'programTypes'));
    }


    public function edit($id = null)
    {
        $academicCalendar = $this->AcademicCalendars->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $academicCalendar = $this->AcademicCalendars->patchEntity($academicCalendar, $this->request->getData());
            if ($this->AcademicCalendars->save($academicCalendar)) {
                $this->Flash->success(__('The academic calendar has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The academic calendar could not be saved. Please, try again.'));
        }
        $programs = $this->AcademicCalendars->Programs->find('list', ['limit' => 200]);
        $programTypes = $this->AcademicCalendars->ProgramTypes->find('list', ['limit' => 200]);
        $this->set(compact('academicCalendar', 'programs', 'programTypes'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $academicCalendar = $this->AcademicCalendars->get($id);
        if ($this->AcademicCalendars->delete($academicCalendar)) {
            $this->Flash->success(__('The academic calendar has been deleted.'));
        } else {
            $this->Flash->error(__('The academic calendar could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

<?php
namespace App\Controller;

use App\Controller\AppController;

class ExtendingAcademicCalendarsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['AcademicCalendars', 'YearLevels', 'Departments', 'Programs', 'ProgramTypes'],
        ];
        $extendingAcademicCalendars = $this->paginate($this->ExtendingAcademicCalendars);

        $this->set(compact('extendingAcademicCalendars'));
    }

    public function view($id = null)
    {
        $extendingAcademicCalendar = $this->ExtendingAcademicCalendars->get($id, [
            'contain' => ['AcademicCalendars', 'YearLevels', 'Departments', 'Programs', 'ProgramTypes'],
        ]);

        $this->set('extendingAcademicCalendar', $extendingAcademicCalendar);
    }

    public function add()
    {
        $extendingAcademicCalendar = $this->ExtendingAcademicCalendars->newEntity();
        if ($this->request->is('post')) {
            $extendingAcademicCalendar = $this->ExtendingAcademicCalendars->patchEntity($extendingAcademicCalendar, $this->request->getData());
            if ($this->ExtendingAcademicCalendars->save($extendingAcademicCalendar)) {
                $this->Flash->success(__('The extending academic calendar has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The extending academic calendar could not be saved. Please, try again.'));
        }
        $this->set(compact('extendingAcademicCalendar'));
    }


    public function edit($id = null)
    {
        $extendingAcademicCalendar = $this->ExtendingAcademicCalendars->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $extendingAcademicCalendar = $this->ExtendingAcademicCalendars->patchEntity($extendingAcademicCalendar, $this->request->getData());
            if ($this->ExtendingAcademicCalendars->save($extendingAcademicCalendar)) {
                $this->Flash->success(__('The extending academic calendar has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The extending academic calendar could not be saved. Please, try again.'));
        }
       $this->set(compact('extendingAcademicCalendar'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $extendingAcademicCalendar = $this->ExtendingAcademicCalendars->get($id);
        if ($this->ExtendingAcademicCalendars->delete($extendingAcademicCalendar)) {
            $this->Flash->success(__('The extending academic calendar has been deleted.'));
        } else {
            $this->Flash->error(__('The extending academic calendar could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

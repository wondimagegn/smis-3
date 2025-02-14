<?php
namespace App\Controller;

use App\Controller\AppController;

class ProgramProgramTypeClassRoomsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Programs', 'ProgramTypes', 'ClassRooms'],
        ];
        $programProgramTypeClassRooms = $this->paginate($this->ProgramProgramTypeClassRooms);

        $this->set(compact('programProgramTypeClassRooms'));
    }

    public function view($id = null)
    {
        $programProgramTypeClassRoom = $this->ProgramProgramTypeClassRooms->get($id, [
            'contain' => ['Programs', 'ProgramTypes', 'ClassRooms'],
        ]);

        $this->set('programProgramTypeClassRoom', $programProgramTypeClassRoom);
    }

    public function add()
    {
        $programProgramTypeClassRoom = $this->ProgramProgramTypeClassRooms->newEntity();
        if ($this->request->is('post')) {
            $programProgramTypeClassRoom = $this->ProgramProgramTypeClassRooms->patchEntity($programProgramTypeClassRoom, $this->request->getData());
            if ($this->ProgramProgramTypeClassRooms->save($programProgramTypeClassRoom)) {
                $this->Flash->success(__('The program program type class room has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The program program type class room could not be saved. Please, try again.'));
        }
        $this->set(compact('programProgramTypeClassRoom', 'programs', 'programTypes', 'classRooms'));
    }

    public function edit($id = null)
    {
        $programProgramTypeClassRoom = $this->ProgramProgramTypeClassRooms->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $programProgramTypeClassRoom = $this->ProgramProgramTypeClassRooms->patchEntity($programProgramTypeClassRoom, $this->request->getData());
            if ($this->ProgramProgramTypeClassRooms->save($programProgramTypeClassRoom)) {
                $this->Flash->success(__('The program program type class room has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The program program type class room could not be saved. Please, try again.'));
        }

        $this->set(compact('programProgramTypeClassRoom'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $programProgramTypeClassRoom = $this->ProgramProgramTypeClassRooms->get($id);
        if ($this->ProgramProgramTypeClassRooms->delete($programProgramTypeClassRoom)) {
            $this->Flash->success(__('The program program type class room has been deleted.'));
        } else {
            $this->Flash->error(__('The program program type class room could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

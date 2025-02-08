<?php
namespace App\Controller;

use App\Controller\AppController;

class MergedSectionsExamsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['PublishedCourses', 'Sections'],
        ];
        $mergedSectionsExams = $this->paginate($this->MergedSectionsExams);

        $this->set(compact('mergedSectionsExams'));
    }

    public function view($id = null)
    {
        $mergedSectionsExam = $this->MergedSectionsExams->get($id, [
            'contain' => ['PublishedCourses', 'Sections'],
        ]);

        $this->set('mergedSectionsExam', $mergedSectionsExam);
    }

    public function add()
    {
        $mergedSectionsExam = $this->MergedSectionsExams->newEntity();
        if ($this->request->is('post')) {
            $mergedSectionsExam = $this->MergedSectionsExams->patchEntity($mergedSectionsExam, $this->request->getData());
            if ($this->MergedSectionsExams->save($mergedSectionsExam)) {
                $this->Flash->success(__('The merged sections exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The merged sections exam could not be saved. Please, try again.'));
        }
        $this->set(compact('mergedSectionsExam'));
    }


    public function edit($id = null)
    {
        $mergedSectionsExam = $this->MergedSectionsExams->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mergedSectionsExam = $this->MergedSectionsExams->patchEntity($mergedSectionsExam, $this->request->getData());
            if ($this->MergedSectionsExams->save($mergedSectionsExam)) {
                $this->Flash->success(__('The merged sections exam has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The merged sections exam could not be saved. Please, try again.'));
        }
        $this->set(compact('mergedSectionsExam'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mergedSectionsExam = $this->MergedSectionsExams->get($id);
        if ($this->MergedSectionsExams->delete($mergedSectionsExam)) {
            $this->Flash->success(__('The merged sections exam has been deleted.'));
        } else {
            $this->Flash->error(__('The merged sections exam could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

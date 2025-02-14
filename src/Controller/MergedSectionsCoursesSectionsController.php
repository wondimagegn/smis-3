<?php
namespace App\Controller;

use App\Controller\AppController;

class MergedSectionsCoursesSectionsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['MergedSectionsCourses', 'Sections'],
        ];
        $mergedSectionsCoursesSections = $this->paginate($this->MergedSectionsCoursesSections);

        $this->set(compact('mergedSectionsCoursesSections'));
    }


    public function view($id = null)
    {
        $mergedSectionsCoursesSection = $this->MergedSectionsCoursesSections->get($id, [
            'contain' => ['MergedSectionsCourses', 'Sections'],
        ]);

        $this->set('mergedSectionsCoursesSection', $mergedSectionsCoursesSection);
    }

    public function add()
    {
        $mergedSectionsCoursesSection = $this->MergedSectionsCoursesSections->newEntity();
        if ($this->request->is('post')) {
            $mergedSectionsCoursesSection = $this->MergedSectionsCoursesSections->patchEntity($mergedSectionsCoursesSection, $this->request->getData());
            if ($this->MergedSectionsCoursesSections->save($mergedSectionsCoursesSection)) {
                $this->Flash->success(__('The merged sections courses section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The merged sections courses section could not be saved. Please, try again.'));
        }
        $this->set(compact('mergedSectionsCoursesSection'));
    }


    public function edit($id = null)
    {
        $mergedSectionsCoursesSection = $this->MergedSectionsCoursesSections->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $mergedSectionsCoursesSection = $this->MergedSectionsCoursesSections->patchEntity($mergedSectionsCoursesSection, $this->request->getData());
            if ($this->MergedSectionsCoursesSections->save($mergedSectionsCoursesSection)) {
                $this->Flash->success(__('The merged sections courses section has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The merged sections courses section could not be saved. Please, try again.'));
        }
        $this->set(compact('mergedSectionsCoursesSection'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mergedSectionsCoursesSection = $this->MergedSectionsCoursesSections->get($id);
        if ($this->MergedSectionsCoursesSections->delete($mergedSectionsCoursesSection)) {
            $this->Flash->success(__('The merged sections courses section has been deleted.'));
        } else {
            $this->Flash->error(__('The merged sections courses section could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

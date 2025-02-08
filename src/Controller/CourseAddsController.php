<?php
namespace App\Controller;

use App\Controller\AppController;

class CourseAddsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['YearLevels', 'Students', 'PublishedCourses'],
        ];
        $courseAdds = $this->paginate($this->CourseAdds);

        $this->set(compact('courseAdds'));
    }


    public function view($id = null)
    {
        $courseAdd = $this->CourseAdds->get($id, [
            'contain' => ['YearLevels', 'Students', 'PublishedCourses', 'ExamGrades', 'ExamResults', 'FxResitRequest', 'HistoricalStudentCourseGradeExcludes', 'MakeupExams', 'RejectedExamGrades', 'ResultEntryAssignments'],
        ]);

        $this->set('courseAdd', $courseAdd);
    }

    public function add()
    {
        $courseAdd = $this->CourseAdds->newEntity();
        if ($this->request->is('post')) {
            $courseAdd = $this->CourseAdds->patchEntity($courseAdd, $this->request->getData());
            if ($this->CourseAdds->save($courseAdd)) {
                $this->Flash->success(__('The course add has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course add could not be saved. Please, try again.'));
        }

        $this->set(compact('courseAdd'));
    }


    public function edit($id = null)
    {
        $courseAdd = $this->CourseAdds->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseAdd = $this->CourseAdds->patchEntity($courseAdd, $this->request->getData());
            if ($this->CourseAdds->save($courseAdd)) {
                $this->Flash->success(__('The course add has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course add could not be saved. Please, try again.'));
        }

        $this->set(compact('courseAdd'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseAdd = $this->CourseAdds->get($id);
        if ($this->CourseAdds->delete($courseAdd)) {
            $this->Flash->success(__('The course add has been deleted.'));
        } else {
            $this->Flash->error(__('The course add could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

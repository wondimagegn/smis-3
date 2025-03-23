<?php

namespace App\Controller;

use Cake\Event\Event;

class ExamSplitSectionsController extends AppController
{

    public $name = 'ExamSplitSections';

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for exam split section'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->ExamSplitSection->delete($id)) {
            $this->Session->setFlash(
                __('<span></span> Exam split section deleted.', true),
                'default',
                array('class' => 'success-box success-message')
            );

            return $this->redirect(array('controller' => 'sectionSplitForExams', 'action' => 'index'));
        }

        $this->Session->setFlash(
            __('<span></span> Exam split section was not deleted.', true),
            'default',
            array('class' => 'error-box error-message')
        );

        return $this->redirect(array('action' => 'index'));
    }
}

?>

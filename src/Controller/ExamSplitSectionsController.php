<?php
class ExamSplitSectionsController extends AppController {

	var $name = 'ExamSplitSections';
	/*
	function index() {
		$this->ExamSplitSection->recursive = 0;
		$this->set('examSplitSections', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid exam split section'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('examSplitSection', $this->ExamSplitSection->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->ExamSplitSection->create();
			if ($this->ExamSplitSection->save($this->request->data)) {
				$this->Session->setFlash(__('The exam split section has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The exam split section could not be saved. Please, try again.'));
			}
		}
		$sectionSplitForExams = $this->ExamSplitSection->SectionSplitForExam->find('list');
		
		$this->set(compact('sectionSplitForExams', 'students'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid exam split section'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->ExamSplitSection->save($this->request->data)) {
				$this->Session->setFlash(__('The exam split section has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The exam split section could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->ExamSplitSection->read(null, $id);
		}
		$sectionSplitForExams = $this->ExamSplitSection->SectionSplitForExam->find('list');
		$students = $this->ExamSplitSection->Student->find('list');
		$this->set(compact('sectionSplitForExams', 'students'));
	}
  */
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for exam split section'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->ExamSplitSection->delete($id)) {
			
			$this->Session->setFlash(__('<span></span> Exam split section deleted.',true),'default',array('class'=>'success-box success-message'));

			return $this->redirect(array('controller'=>'sectionSplitForExams','action'=>'index'));
		}
		
		$this->Session->setFlash(__('<span></span> Exam split section was not deleted.',true),'default',array('class'=>'error-box error-message'));

		return $this->redirect(array('action' => 'index'));
	}
}
?>
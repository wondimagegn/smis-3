<?php
class GradeScaleDetailsController extends AppController {

	public $name = 'GradeScaleDetails';
    public $menuOptions = array(
                 'controllerButton'=>false,
                 'exclude' =>'*',
                
            );
            
    public function beforeFilter() {
		parent::beforeRender();
		$this->Auth->allow('get_grade_scale_detail');
    
    }
	public function index() {
		$this->GradeScaleDetail->recursive = 0;
		$this->set('gradeScaleDetails', $this->paginate());
	}

	public function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid grade scale detail'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('gradeScaleDetail', $this->GradeScaleDetail->read(null, $id));
	}

	public function add() {
		if (!empty($this->request->data)) {
			$this->GradeScaleDetail->create();
			if ($this->GradeScaleDetail->save($this->request->data)) {
				$this->Session->setFlash(__('The grade scale detail has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The grade scale detail could not be saved. Please, try again.'));
			}
		}
		$gradeScales = $this->GradeScaleDetail->GradeScale->find('list');
		$grades = $this->GradeScaleDetail->Grade->find('list');
		$this->set(compact('gradeScales', 'grades'));
	}

	public function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid grade scale detail'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->GradeScaleDetail->save($this->request->data)) {
				
			$this->Session->setFlash('<span></span>'.
		__('The grade scale detail has been saved.'),
		'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				
			 $this->Session->setFlash('<span></span>'.
		__('The grade scale detail could not be saved. Please, try again.'),
		'default',array('class'=>'error-box error-message'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->GradeScaleDetail->read(null, $id);
		}
		$gradeScales = $this->GradeScaleDetail->GradeScale->find('list');
		$grades = $this->GradeScaleDetail->Grade->find('list');
		$this->set(compact('gradeScales', 'grades'));
	}

	public function delete($id = null) {
		if (!$id) {
		
			$this->Session->setFlash('<span></span>'.
		__('Invalid id for grade scale detail.'),
		'default',array('class'=>'error-box error-message'));

			return $this->redirect(array('action'=>'index'));
		}
		if ($this->GradeScaleDetail->delete($id)) {
			
			$this->Session->setFlash('<span></span>'.
		__('Grade scale detail deleted.'),
		'default',array('class'=>'success-box success-message'));

			return $this->redirect(array('action'=>'index'));
		}
		
		$this->Session->setFlash('<span></span>'.
		__('Grade scale detail was not deleted.'),
		'default',array('class'=>'error-box error-message'));

		return $this->redirect(array('action' => 'index'));
	}
	
	public function get_grade_scale_detail($grade_scale_id=null){
	    $this->layout='ajax';
	    
	    $gradeScaleDetails=$this->GradeScaleDetail->find('all',array('conditions'=>array('GradeScaleDetail.grade_scale_id'=>$grade_scale_id),'fields'=>array('id','minimum_result',
	    'maximum_result'),'contain'=>array('Grade'=>array('fields'=>array('id','grade')))));
	    
	    $this->set(compact('gradeScaleDetails'));
	}
}

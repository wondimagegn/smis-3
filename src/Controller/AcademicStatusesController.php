<?php
class AcademicStatusesController extends AppController {

	public $name = 'AcademicStatuses';
    public $menuOptions = array(
            
             'parent' => 'dashboard',
             'exclude' => array('index'),
             'alias' => array(
                    'index' => 'View All Status',
                    'add'=>'Set Academic Status'
            )
    );
	public $paginate = array();

	public function index() {
		$this->AcademicStatus->recursive = 0;
		 $this->paginate = array('order' => 'AcademicStatus.order DESC');
		$this->set('academicStatuses', $this->paginate());
	}

	public function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid academic status'),'default',
			array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		
	   $academicStatus=$this->AcademicStatus->read(null, $id);
	   
	   foreach ($academicStatus['AcademicStand'] as $k=>&$v) {
	          $year_level_ids=array();
	          $semester=array();
	          if (!empty($v['year_level_id'])) {
	             $year_level_ids=unserialize($v['year_level_id']);
	             $v['year_level_id']=implode (',',$year_level_ids);
	          }
	          if (!empty($v['semester'])) {
	             $semester=unserialize($v['semester']);
	             if (!empty($semester)) {
	                $v['semester']=implode (',',$semester);
	             } else {
	                 $v['semester']=null;
	             }
	          }
	          if (!empty($v['program_id'])) {
	              $v['program_name']=$this->AcademicStatus->AcademicStand->Program->field('Program.name',array('Program.id'=>$v['program_id']));
	          }
	       
	          
	   }
	 
	  $this->set('academicStatus', $academicStatus);
	}

	public function add() {
		if (!empty($this->request->data)) {
			$this->AcademicStatus->create();
			if ($this->AcademicStatus->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The academic status has been saved'),
				'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The academic status could not be saved. Please, try again.'),
				'default',array('class'=>'error-box error-message'));
			}
		}
	}

	public function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid academic status'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->AcademicStatus->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The academic status has been saved'),
				'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The academic status could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->AcademicStatus->read(null, $id);
		}
	}

	public function delete($id = null) {
		if (!$id || !$this->AcademicStatus->exists($id)) {
			$this->Session->setFlash('<span></span>'.__('Invalid id for academic status'),'default',
			array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		
		if($this->AcademicStatus->canItBeDeleted($id)){
			if ($this->AcademicStatus->delete($id)) {
				$this->Session->setFlash('<span></span>'.__('Academic status deleted'),
				'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action'=>'index'));
			}
			else {
				$this->Session->setFlash('<span></span>'.__('Academic status was not deleted'),
				'default',array('class'=>'error-box error-message'));
				return $this->redirect(array('action' => 'index'));
			}
		}
		else {
			$this->Session->setFlash('<span></span>'.__('Academic status is on use and can not be deleted.'),
			'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
	}
}

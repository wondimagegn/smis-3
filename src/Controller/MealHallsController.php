<?php
class MealHallsController extends AppController {

	public $name = 'MealHalls';
	public $menuOptions = array(
		'parent' => 'mealService',
		'exclude'=>array(''),
		'alias' => array(
                    'index' =>'List Meal Halls',
					'add' =>'Add Meal Hall'
		)
	);
	public $paginate=array();

	 public function beforeFilter(){
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
         //$this->Auth->allow('');  
    }
	public function index() {
		$this->MealHall->recursive = 0;
		$campuses = $this->MealHall->Campus->find('list');
		$this->set(compact('campuses'));
		$campus = '%';
		
		if(!empty($this->request->data['MealHall']['campus_id'])){
			$campus = $this->request->data['MealHall']['campus_id'];
		}

		$conditions = array('MealHall.campus_id LIKE'=>$campus);
		$this->paginate = array('conditions'=>$conditions);
		$this->Paginator->settings=$this->paginate;
		$this->set('mealHalls', $this->Paginator->paginate('MealHall'));
	}


	function add() {
		if (!empty($this->request->data)) {
			$this->MealHall->create();
			if ($this->MealHall->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The meal hall has been saved'),'default',array('class'=>'success-box	success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The meal hall could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			}
		}
		$campuses = $this->MealHall->Campus->find('list');
		$this->set(compact('campuses'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash('<span></span>'.__('Invalid meal hall'),'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->MealHall->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The meal hall has been saved'),'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The meal hall could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->MealHall->read(null, $id);
		}
		$campuses = $this->MealHall->Campus->find('list');
		$this->set(compact('campuses'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid id for meal hall'),'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		//Before delete Meal hall check this meal hall is ever been used in meal hall assignment or not
		$is_meal_hall_ever_used = $this->MealHall->MealHallAssignment->is_meal_hall_ever_used($id);
		if($is_meal_hall_ever_used == false){
			if ($this->MealHall->delete($id)) {
				$this->Session->setFlash('<span></span>'.__('Meal hall deleted'),'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action'=>'index'));
			} 
		} else {
			$this->Session->setFlash('<span></span> '.__('The meal hall can not be delete since it used in meal hall assignment.'),'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('<span></span>'.__('Meal hall was not deleted'),'default',array('class'=>'error-box error-message'));
		return $this->redirect(array('action' => 'index'));
	}
}

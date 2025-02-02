<?php
class MealTypesController extends AppController {

	var $name = 'MealTypes';
	
	var $menuOptions = array(
		'parent' => 'mealService',
		'exclude'=>array('add'),
		'alias' => array(
                    'index' =>'List Meal Types',
					'add' =>'Add Meal Type'
		)
	);
	function beforeFilter(){
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
         //$this->Auth->allow('');  
    }
	function index() {
		$this->MealType->recursive = 0;
		$this->set('mealTypes', $this->paginate());
	}

	/*function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid meal type'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('mealType', $this->MealType->read(null, $id));
	}*/

	/*function add() {
		if (!empty($this->request->data)) {
			$this->MealType->create();
			if ($this->MealType->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The meal type has been saved'),'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The meal type could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			}
		}
	}*/

	/*function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash('<span></span>'.__('Invalid meal type'),'default',array('error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->MealType->save($this->request->data)) {
				$this->Session->setFlash('<span></span>'.__('The meal type has been saved'),'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The meal type could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->MealType->read(null, $id);
		}
	}*/

	/*function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid id for meal type'),'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		//TODO: before delete meal type check whether this meal type id every used in meal attendance table or not 
		$is_this_meal_type_ever_used = $this->MealType->MealAttendance->find('count',array('MealAttendance.meal_type_id'=>$id));
		if(empty($is_this_meal_type_ever_used)){
			if ($this->MealType->delete($id)) {
				$this->Session->setFlash('<span></span>'.__('Meal type deleted'),'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action'=>'index'));
			}
			$this->Session->setFlash('<span></span>'.__('Meal type was not deleted'),'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash('<span></span>'.__('You can not able to delete this meal type since it used in meal attendance.'),'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
	}*/
}

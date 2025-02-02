<?php
class ProgramTypesController extends AppController {

	 var $name = 'ProgramTypes';
     var $menuOptions = array(
            
             'parent' => 'dashboard',
             'exclude' => array('index'),
             'alias' => array(
                    'index' => 'View All Program Types',
                    'map_program_types'=>'Map program types',
            )
     );
   
    
    function beforeFilter(){
        parent::beforeFilter();
        
         $this->Auth->allow('get_program_types');
         // delete auth flash message from the session
        
				     
    }
   
	function index() {
		$this->ProgramType->recursive = 0;
		$this->set('programTypes', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid program type'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('programType', $this->ProgramType->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$this->ProgramType->create();
			if ($this->ProgramType->save($this->request->data)) {
				$this->Session->setFlash(__('The program type has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The program type could not be saved. Please, try again.'));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid program type'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->ProgramType->save($this->request->data)) {
				$this->Session->setFlash(__('The program type has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The program type could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->ProgramType->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for program type'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->ProgramType->delete($id)) {
			$this->Session->setFlash(__('Program type deleted'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Program type was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}
	/**
	* Map program types
	*/
	function map_program_types() {
        
        	
        	if (!empty($this->request->data)) {
        	    
        	    if (!empty($this->request->data['ProgramType']['program_type_id'])
        	    && !empty($this->request->data['ProgramType']['equivalent_to_id'])) {
        	    
	             $this->request->data['ProgramType']['id']=$this->request->data['ProgramType']['program_type_id'];
	            
	             $this->request->data['ProgramType']['equivalent_to_id']=serialize($this->request->data['ProgramType']['equivalent_to_id']);
	               unset($this->request->data['ProgramType']['program_type_id']); 
                   if ($this->ProgramType->save($this->request->data)) {
				        $this->Session->setFlash('<span></span>'.__('The program type has been mapped'),
				        'default',array('class'=>'success-box success-message'));
				        //$this->redirect(array('action' => 'index'));
			        } else {
				        $this->Session->setFlash(__('The program type map could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			        }
			        
			    } else {
			       $this->Session->setFlash(__('The program type map could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			    }        
	        }
	        $programTypes=$this->ProgramType->find('list');
	        $this->set(compact('programTypes'));
	}
	
	/**
	*
	*/
	function get_program_types($program_type_id=null) {
	      
	        $this->layout='ajax';
	       
	        $othersprogramTypes=$this->ProgramType->find('list',array('conditions'=>array(
	        'ProgramType.id <> '=>$program_type_id)));
	       
	        $this->set(compact('othersprogramTypes'));
	    
	}
	
}

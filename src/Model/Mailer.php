<?php
class Mailer extends AppModel {

	var $name = 'Mailer';
	
    var $useTable = 'messages';
	
	/**
	* This function takes array of email message and save to the database
	* @return true when saved.
	*/

	function logMessage($message){	
	     if (!$this->save($message)) {
            return false;
         }
         return true;
	}

	  //event management system, observer pattern :) raising and event 
	public function afterSave($created, $options = array()) {
		/*
         parent::afterSave($created, $options);
         if ($created === true) {
            $Event = new CakeEvent('Model.Mailer.created', $this, array(
                'id' => $this->id,
               
                'data' => $this->data[$this->alias]
            ));
          
            $this->getEventManager()->dispatch($Event);
        }
        */
    }
  
	    
 
}
	
?>

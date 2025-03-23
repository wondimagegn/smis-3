<?php 

if(!empty($othersprogramTypes)) {
    echo $this->Form->input('ProgramType.equivalent_to_id',array(
	    'multiple'=>'checkbox',
	    'options'=>$othersprogramTypes,
	    'div'=>false
    ));
   
}

?>

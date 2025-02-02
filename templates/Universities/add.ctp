<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	  <h6 class="box-title">
	     <?php echo __('Add University Name'); ?>
	     </h6>
     </div>
     <div class="box-body">
       <div class="row">
	    <?php echo $this->Html->script('amharictyping'); ?>  
	 <?php echo $this->Form->create('University',array('action' => 'add','enctype' => 'multipart/form-data'));?>
	
	  <div class="large-6 columns">
	  	
	<?php
		
		echo $this->Form->input('name', array('style' => 'width:300px'));
		echo $this->Form->input('amharic_name',array('style' => 'width:300px', 'id'=>'AmharicText','onkeypress'=>"return AmharicPhoneticKeyPress(event,this);"));
		echo $this->Form->input('short_name');
		echo $this->Form->input('amharic_short_name',array('id'=>'AmharicText','onkeypress'=>"return AmharicPhoneticKeyPress(event,this);"));
	?>
	</div>
      <div class="large-6 columns">
	<?php 
		echo $this->Form->input('academic_year', array('style' => 'width:100px', 'type' => 'select', 'options' => $years, 'default' => (isset($this->request->data['University']['academic_year']) ? $this->request->data['University']['academic_year'] : date('Y'))));
		
		echo $this->Form->input('p_o_box',array('style' => 'width:50px'));
		echo $this->Form->input('telephone', array('style' => 'width:200px'));
		echo $this->Form->input('fax', array('style' => 'width:200px'));
	
		echo $this->Form->input('Attachment.0.file',array('type'=>'file','label'=>'Transparent Logo'));
        echo $this->Form->input('Attachment.1.file',array('type'=>'file','label'=>'Small Logo'));
	?>
	
	  </div>

	<div class="large-12 columns">
	
<?php echo $this->Form->end(array('label'=>'Submit','class'=>'tiny radius button bg-blue'));
?>
	  </div>
	</div>
      </div>
</div>

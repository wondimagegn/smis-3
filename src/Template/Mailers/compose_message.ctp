<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h2 class="box-title">
	<?php echo __('Send Mail');?>
	 </h2>
     </div>
     <div class="box-body">
       <div class="row">
	  <?php 
echo $this->Form->create('Mailer',array('action'=>'compose_message','name'=>'sendMail',
'id'=>'sendMail','onsubmit'=>"return confirmSubmit()"));

?>
	   <div class="large-6 columns">
		<?php              
		 echo $this->Form->input('role_id',array('type' => 'select','label' => false,'multiple' => 'checkbox', 'div' => 'checkboxTable'));
		?>
	   </div>

	     <div class="large-6 columns">
               <?php 
		echo $this->Form->input('subject');
		echo $this->Form->input('message',array('type'=>'textarea'));
		echo $this->Form->Submit(__('Submit'),
array('class'=>'tiny radius button bg-blue'));
		
		?>
	   </div>
<?php 
echo $this->Form->end();
?>
	</div>
     </div>
</div>

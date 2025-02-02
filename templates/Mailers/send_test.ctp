
<div class="mailer form">

<?php 
echo $this->Form->create('Mailer',array('action'=>'send_test','name'=>'sendMail',
'id'=>'sendMail','onsubmit'=>"return confirmSubmit()"));

?>
	<fieldset>
 		<legend class="smallheading"><?php echo __('Send Mail');?></legend>
	<?php
	   
	   echo "<table>";
    //   echo "<tr><td>".$this->Form->input('role_id',array('type' => 'select','label' => false,'multiple' => 'checkbox', 'div' => 'checkboxTable')).'</td></tr>';
			echo $this->Form->input('to');
		echo '<tr><td>'.$this->Form->input('subject').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('message',array('type'=>'textarea')).'</td></tr>';
		echo '<tr><td>'.$this->Form->Submit(__('Submit')).'</td></tr>';
		echo "</table>";
		echo '<table><tr><td id="inlineSend">';
		
		/*
		echo $this->Form->input('to');
	
        echo $this->Js->Submit('Send Test', array('controller' => 'mailers',
        'action' => 'sent_test','update' => '#inLineSend'
        ));
        */
        echo '</td></tr></table>';
	?>
	
	</fieldset>
<?php 
echo $this->Form->end();
?>
</div>


<?php ?>
<script type="text/javascript">
   function validatePasswordJs(form) {
       
       if (window.document.getElementById('password').value=='') {
             alert('Please provide a password');
            return false;
       } else if (window.document.getElementById('password').value.length<6) {
                 alert('The minimum password length is 6');
            return false;
       }    
       return true;
       
   }
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
		<div class="large-12 columns">
                <h3><?php echo __('Password issue/reset  to a student'); ?></h3>
 <?php echo $this->Html->script('generatepassword'); ?>      
   <?php 
   echo $this->Form->create('Student',array('action'=>'issue_password'));
   ?> 
<div class="students index">
<?php if (!isset($hide_search)) { ?>
<table cellpadding="0" cellspacing="0"><tbody>
	
	<tr><td>
	
	
	<?php 
			echo $this->Form->input('Student.studentnumber',array('label'=>'Student Number')); 
			
			?>
	</td></tr>
	<tr><td><?php echo $this->Form->Submit('Continue',array('div'=>false,'name'=>'issuestudentidsearch')); ?> </td>	
</tr>
</tbody>
</table>
<?php } ?>
<?php 
if(isset($hide_search)) {
?>
   <table>
    <tr><td style="font-weight:bold">Student Name: <?php echo $students['Student']['full_name']?></td></tr>
    <tr><td style="font-weight:bold">College: <?php echo $students['College']['name']?></td></tr>
    <tr>
    <td><?php 
    debug($students);
    echo $this->Form->hidden('Student.id',array('value'=>$students['Student']['id']));
    echo $this->Form->hidden('User.id',array('value'=>$students['User']['id']));

    echo $this->Form->hidden('User.role_id',array('value'=>$students['User']['role_id']));
    echo $this->Form->input('User.username',array('value'=>$students['Student']['studentnumber'],'readonly'=>'readonly'));  ?></td></tr>
   <tr> <td><?php echo $this->Form->input('User.passwd',array('label' => 'Password', 'type'=>'password','id'=>'password'));  ?></td></tr>
    <tr> <td><?php echo $this->Form->input('Generate Password',
     array('id'=>'text','name'=>'text'));  ?> 
     </td></tr>
     <tr><td style="padding-left:350px;">  <input type="button" id="button_generate_password" value="Generate" onclick="suggestPassword(this.form)"> </td></tr>
   <tr><td><?php echo $this->Form->Submit('Set Password',array('div'=>false,'class'=>'tiny radius button bg-blue',
 'name'=>'issuepasswordtostudent')); 
          
 ?> </td>	
    <tr><td></td></tr>
</table>
<?php 
    
    }
    
?>
</div>
<?php
echo $this->Form->end();
?>
		</div>
        </div>
      </div>
</div>


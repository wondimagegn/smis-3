<?php ?>
             
<?php echo $this->Form->create('MealAttendance',
 array('defaultbutton' => "submit", 
'defaultfocus' => "TextBox1",'id'=>'MealAttForm'));?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-4 columns">

<div class="smallheading"><?php echo 'Add Meal Attendance'; ?></div>
<?php if(isset($mealHalls)) { ?>
<table cellpadding="0" cellspacing="0" border="0">
<?php 	
		echo '<tr><td class="font">'.$this->Form->input('meal_hall_id',array('label' => 'Meal hall', 'id'=>'id_meal_hall','type'=>'select', 'options'=>$mealHalls, 'empty'=>"--Select Meal Hall--")).'</td></tr>';
       echo '<tr><td>'. $this->Form->Submit('Continue',array('name'=>'continue','div'=>false)).'</td></tr>';
?>
</table>
<?php } ?>

<?php if(isset($mealTypes)) { ?>

	<table border="0" cellpadding="0" cellspacing="0">
		
	<?php
		//echo '<tr><td class="font"> Program</td>'; 
		echo '<tr><td class="font" colspan="4">'.$this->Form->input('meal_type_id',array('style'=>'width:200PX', 'tabindex' => '3', 'selected'=>isset($auto_detected_meal_type)?$auto_detected_meal_type:(isset($this->request->data['MealAttendance']['meal_type_id']) ? $this->request->data['MealAttendance']['meal_type_id'] : ""))).'</td></tr>';
		//echo '<tr><td class="font"> Student ID</td>'; 
		echo '<tr><td class="font" colspan="4">'.$this->Form->input('studentnumber',array('label'=>'Student ID', 'id' => 'studentnumber', 'tabindex' => '4','style'=>'width:200PX')).'</td></tr>';
	
		echo '<tr><td colspan="4">'.$this->Form->Submit('Submit', array('name'=>'submit', 'id'=>'submit', 'tabindex' => '5', 'div'=>false)).'</td></tr>';
		echo '</table>';
		}
		?>
	</div>
        <div class="large-8 columns">
       
		<?php 
		if(isset($students) && !empty($students))
		{
			echo "<div class='smallheading'>Student Basic Data</div>";
			echo '<div class="left">';
			echo '<h5>Name: '.$students['Student']['full_name'].'</h5>';
			echo '<h5>ID: '.$students['Student']['studentnumber'].'</h5>';
			echo '<h5>Gender: '.$students['Student']['gender'].'</h5>';
			echo '</div>';
			echo '<div class="right">';
			if(!empty($students['Attachment'])){
	           
                foreach($students['Attachment'] as $ak=>$av){
                   if(!empty($av['dirname']) && !empty($av['basename']) && strcasecmp($av['dirname'],'img')==0){

                      echo '<div valign="top" align="right">'.$this->Media->embedAsObject($av['dirname'].DS.$av['basename'],array('width'=>144,'class'=>'profile-picture'))."</div>";
                        
                   }
                   
			    }
            } else {
               echo '<div valign="top" align="right"><img src="/img/noimage.jpg" width="144" class="profile-picture"></div>';
               
            } 
            echo '</div>';
        }
	?>
	
 </div> <!-- end of row ---->
 </div> <!-- end of box-body -->
</div><!-- end of box -->

<?php
echo $this->Form->end();
?>

<script type="text/javascript">
	function maintainStudentIDFocus() {
		window.document.getElementById("studentnumber").focus();
		window.document.getElementById("studentnumber").select();
	}

maintainStudentIDFocus();
</script>
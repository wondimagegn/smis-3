<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">         
<?php 
 echo $this->Form->create('Staff');
if (!isset($staff_profile) ) {
?>
<table cellpadding="0" cellspacing="0">
<?php 	
  echo '<tr><td class="smallheading">Search Staff Profile</td></tr>';
        
		echo '<tr><td class="font">'.$this->Form->input('staffid',array('label' => 'Staff ID')).'</td></tr>';
       echo '<tr><td>'. $this->Form->Submit('Continue',array('name'=>'continue','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>';
?>
</table>
<?php 
}
if (!empty($staff_profile)) {
        echo $this->element('staffs/staff_profile');
}
?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

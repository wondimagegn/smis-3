<?php
if(!empty($instructors_list)){
?>
<table style='border: #CCC solid 1px'>
<tr><td class="centeralign_smallheading"><?php echo("Select instructors for invigilation of this college final exams.")?></td><tr>
<?php
	foreach($instructors_list as $instructor){
		echo "<tr><td class='font'>".$this->Form->input('StaffForExam.Selected.'.$instructor['Staff']['id'],array('type'=>'checkbox','value'=>$instructor['Staff']['id'], 'label'=>$instructor['Title']['title']. ' '. $instructor['Staff']['full_name'].' ( Position:'.$instructor['Position']['position'].')'))."</td></tr>";
	}
?> </table>
<?php echo $this->Form->Submit('Submit', array('name'=>'submit','div'=>false));
}
?>

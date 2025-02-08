<?php echo $this->Form->create('AcceptedStudent', array('action' => 'move_readmitted_to_freshman',
'novalidate'=>true));?> 

<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            	
<div>
<?php if(!isset($show_list_generated) || empty($acceptedStudents)) { ?>
<div class="smallheading"><?php echo __('Find readmitted applicant for freshman program.')?></div>
<?php if(!isset($show_list_generated) || empty($acceptedStudents)) { ?>
<table cellpadding="0" cellspacing="0"><tr> 
	<?php 
			echo '<td>'.$this->Form->input('Search.academicyear',array('id'=>'academicyear',
                'label' => 'Readmission Academic Year','type'=>'select','options'=>$readmittedAC,
                'empty'=>"--Select Academic Year--",'selected'=>isset($selectedsacdemicyear)?$selectedsacdemicyear:'')).'</td>';
            echo '<td>'. $this->Form->input('Search.college_id',array('empty'=>"--Select College--")).'</td></tr>';
            echo '<tr><td>'. $this->Form->input('Search.program_id',array('empty'=>"--Select Program--")).'</td>'; 
            echo '<td>'. $this->Form->input('Search.program_type_id',array('empty'=>"--Select Program Type--")).'</td></tr>'; 
            ?>
    <tr>
    	
    	<td colspan="2"><?php echo $this->Form->input('Search.name',array('label'=>"Name")); ?> </td>	
    	
    </tr>
	<tr><td><?php echo $this->Form->submit('Find  Readmitted Students',array('name'=>'continue','div'=>'false','class'=>'tiny radius button bg-blue')); ?> </td>	
	<td><?php ?> </td>	
		
</tr>

</table>
<?php } ?>
<?php 
}

echo $this->Form->end();


if(!empty($acceptedStudents)){
?>

<table><tbody><tr><td width="100%">
<table><tbody>

<tr><td colspan=2>
<?php

//echo $this->Form->hidden('AcceptedStudent.id',array('value'=>));

echo $this->Form->create('AcceptedStudent', array('action' => 'move_readmitted_to_freshman'));

echo "<table>";


echo "<tr><td colspan=2>Select the campus and the college you want to readmitted the selected student in freshman program.</td></tr>";

echo "<tr><td>".$this->Form->input('campus_id',array('empty'=>'--select campus--',
'required'=>true,'options'=>$available_campuses,'label'=>'Select the target campus',
'id'=>'ajax_campus_id', 'onchange'=>'getCollege()'
))."</td><td>".$this->Form->input('selected_college_id',array('empty'=>'--select campus--','id'=>'SelectedCollegeId',
'required'=>true,'options'=>$selected_colleges,'label'=>'Select the target college'))."</td></tr>";
echo "</table>";

 $count=0;

?>
<table>
   <tr><th colspan=11 class="smallheading"><?php echo  __('List of student who applied for readmission application.');?></th></tr>
	<tr>
	        
            <th><?php echo ('No.'); ?> </th>
            <th style="padding:0">
            <?php echo 'Select/ Unselect All <br/>'.$this->Form->checkbox("SelectAll", array('id' => 'select-all','checked'=>'')); ?> </th> 
            <th><?php echo ('Full Name');?></th>
			<th><?php echo ('Sex');?></th>
			<th><?php echo ('Student Number');?></th>
		
			<th><?php echo ('College');?></th>
			<th><?php echo ('Department');?></th>
			<th><?php echo ('Academic Year');?></th>
			<th><?php echo ('Campus');?></th>
			
			
	</tr>
	<?php
	$i = 0;
	$serial_number=1;
	
	foreach ($acceptedStudents as $acceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
       
        <td><?php echo $serial_number++;?></td>
         <td ><?php echo $this->Form->checkbox('AcceptedStudent.approve.' . $acceptedStudent['Student']['AcceptedStudent']['id'],array('class'=>'checkbox1')); ?>&nbsp;</td> 
          <?php //echo $this->Form->hidden('AcceptedStudent.'.$count.'.id',array('value'=>$acceptedStudent['Student']['AcceptedStudent']['id']));?>
        <td><?php echo $acceptedStudent['Student']['AcceptedStudent']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Student']['AcceptedStudent']['sex']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Student']['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
		
		<td><?php echo $acceptedStudent['Student']['College']['name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Student']['Department']['name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Student']['AcceptedStudent']['academicyear']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Student']['College']['Campus']['name']; ?>&nbsp;</td>
		
	</tr>
	
<?php 
$count++;

endforeach; 

?>
	</table>

	<?php 
	
 
echo '<tr><td>'.$this->Form->Submit(__('Readmit selected'),array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'readmitted')).'</td></tr>';


 ?>
</td></tr>
    </tbody></table>
   
</td></tr>

</tbody></table>


<?php 
} else if(empty($acceptedStudents) && !($isbeforesearch)){
    echo "<div class='info-box info-message'> <span></span> No  students who applied readmission in selected criteria</div>";
}
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
<?php  
echo $this->Form->end();
?>

<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
 //Get year level
function getCollege() {
   //serialize form data
		var clg = $("#ajax_campus_id").val();
		$("#SelectedCollegeId").attr('disabled', true);
		$("#SelectedCollegeId").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
 var formUrl = '/colleges/get_college_combo/'+clg;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: clg,
                success: function(data,textStatus,xhr){
$("#SelectedCollegeId").attr('disabled', false);
$("#SelectedCollegeId").empty();
$("#SelectedCollegeId").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
 </script>

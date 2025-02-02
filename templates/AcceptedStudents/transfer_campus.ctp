<?php echo $this->Form->create('AcceptedStudent', array('action' => 'transfer_campus'));?> 
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="reservedPlaces form">

		<p class="fs16">
				     <strong> Important Note: </strong> 
				       This tool will help you to change freshman student campus. By providing some criteria you can find the target student for change. After change you need to do the following:
				       <ul>
				       		<li>Using the dean account, place the student to the new section of the campus</li>
				       		<li>Register the students to the courses</li>
				       </ul>
		</p>
		<div onclick="toggleViewFullId('ListPublishedCourse')"><?php 
			if (!empty($auto_approve) ) {
				echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
				?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
			}
			else {
				echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
				?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
			}
		?>
		</div>
		<div id="ListPublishedCourse" style="display:<?php echo (!empty($auto_approve)   ? 'none' : 'display'); ?>">
	 <?php 
	      
	        echo '<table class="fs16 small_padding" >';
			echo '<tr><td>Academic Year</td><td>'.$this->Form->input('AcceptedStudent.academicyear',array('id'=>'academicyear',
            'label' => false,'type'=>'select','options'=>$academicYearLists,
            'empty'=>"--Select Academic Year--",'selected'=>isset($selected_academicyear)?$selected_academicyear:'')).'</td><td>Current College</td><td>'.
$this->Form->input('AcceptedStudent.college_id',array('label'=>false)).'</td></tr>';
            echo '<tr><td>Program</td><td>'.$this->Form->input('AcceptedStudent.program_id',array('label'=>false)).'</td><td>Program Type</td><td>'.$this->Form->input('AcceptedStudent.program_type_id',array('label'=>false)).'</td></tr>';

 echo '<tr><td>Name</td><td>'.
$this->Form->input('AcceptedStudent.name',array('label'=>false)).'</td><td>Current Campus</td><td>'.$this->Form->input('AcceptedStudent.campus_id',array('label'=>false)).'</td></tr>';

            echo '<tr>';
            echo '<td colspan="4">';
            echo $this->Form->Submit(__('Continue'),array('div'=>false,
 'name'=>'searchbutton','class'=>'tiny radius button bg-blue'));
			echo '</td>';
            echo '</tr>';
            echo '</table>';
            ?>

		</div>
        
<table><tbody><tr><td width="100%">
<table><tbody>

<tr><td colspan=2>
<?php

if(!empty($autoplacedstudents)){
echo $this->Form->hidden('AcceptedStudent.academicyear',array('value'=>$selected_academicyear));
if(!isset($turn_of_approve_button)){
echo "<table>";


echo "<tr><td>Select the campus you want to transfer the selected student.</td></tr>";

echo "<tr><td>".$this->Form->input('campus_id',array('empty'=>'--select campus--',
'required'=>true,'options'=>$available_campuses,'label'=>'Select the target campus'))."</td><td>".$this->Form->input('selected_college_id',array('empty'=>'--select campus--',
'required'=>true,'options'=>$selected_colleges,'label'=>'Select the target college'))."</td></tr>";
echo "</table>";
}

 $count=0;

?>
<table>
   <tr><th colspan=11 class="smallheading"><?php echo  __('List of student placed to campus.');?></th></tr>
	<tr>
	        
            <th><?php echo ('No.'); ?> </th>
            <th style="padding:0">
            <?php echo 'Select/ Unselect All <br/>'.$this->Form->checkbox("SelectAll", array('id' => 'select-all','checked'=>'')); ?> </th> 
            <th><?php echo ('Full Name');?></th>
			<th><?php echo ('Sex');?></th>
			<th><?php echo ('Student Number');?></th>
		
			<th><?php echo ('College');?></th>
			<th><?php echo ('Academic Year');?></th>
			<th><?php echo ('Campus');?></th>
			
			
	</tr>
	<?php
	$i = 0;
	$serial_number=1;
	
	foreach ($autoplacedstudents as $acceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
       
        <td><?php echo $serial_number++;?></td>
         <td ><?php echo $this->Form->checkbox('AcceptedStudent.approve.' . $acceptedStudent['AcceptedStudent']['id'],array('class'=>'checkbox1')); ?>&nbsp;</td> 
          <?php echo $this->Form->hidden('AcceptedStudent.'.$count.'.id',array('value'=>$acceptedStudent['AcceptedStudent']['id']));?>
        <td><?php echo $acceptedStudent['AcceptedStudent']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['sex']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
		
		<td><?php echo $acceptedStudent['College']['name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['academicyear']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Campus']['name']; ?>&nbsp;</td>
		
	</tr>
	
<?php 
$count++;

endforeach; 

?>
	</table>

	<?php 
	
 
echo '<tr><td>'.$this->Form->Submit(__('transfer'),array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'transfer')).'</td></tr>';
	
}

 ?>
</td></tr>
    </tbody></table>
   
</td></tr>

</tbody></table>
</div> 
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
<?php echo $this->Form->end();?>

<script type="text/javascript">

function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
}

function toggleViewFullId(id) {
	if($('#'+id).css("display") == 'none') {
		$('#'+id+'Img').attr("src", '/img/minus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Hide Filter');
		}
	else {
		$('#'+id+'Img').attr("src", '/img/plus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Display Filter');
		}
	$('#'+id).toggle("slow");
}
</script>

<?php echo $this->Form->create('Student');?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  		<div class="large-12 columns">
            	 
<p class="fs16">
             <strong> Important Note: </strong> 
               This tool will help you to allow/deny students to use cafe gate. It is process entensive and time consuming since it will update different databases.
</p>
<div onclick="toggleViewFullId('LisAdmittedStudent')"><?php 
	if (!empty($acceptedStudents)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="LisAdmittedStudentTxt">Display Filter</span><?php
	}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'LisAdmittedStudentImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="LisAdmittedStudentTxt">Hide Filter</span><?php
	}
?>
</div>
<div id="LisAdmittedStudent" style="display:<?php echo ((!empty($students))  ? 'none' : 'display'); ?>">
<?php 
 debug($this->request->data);
 ?>
<table cellspacing="0" cellpadding="0" class="fs13" >
	
	<tr>
		<td style="width:10%">Admission Year:</td>
		<td style="width:25%"><?php echo $this->Form->input('Search.academicyear',array('id'=>'academicyear',
            'label' => false,'type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--")) ?></td>
		<td style="width:11%">Program:</td>
		<td style="width:53%"><?php echo $this->Form->input('Search.program_id',array('label'=>false)); ?></td>
	</tr>
	<tr>
		<td style="width:10%">Current Academic Year:</td>
		<td style="width:25%"><?php echo $this->Form->input('Search.currentAcademicYear',array('id'=>'currentAcademicyear',
            'label' => false,'type'=>'select','options'=>$acyear_array_data)); ?></td>
		<td style="width:11%">Semester:</td>
		<td style="width:53%"><?php 
		echo $this->Form->input('Search.semester',array('label'=>false,'options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'))); ?>
          </td>

	</tr>


	<tr>
		<td style="width:15%">Department:</td>
		<td style="width:20%"><?php echo $this->Form->input('Search.department_id', array('id' => 'ProgramType', 'class' => 'fs14','style' => 'width:300px', 'label' => false, 'type' => 'select', 'options' => $departments)); ?></td>

		<td> Program Type:</td>
		<td><?php echo $this->Form->input('Search.program_type_id',array('label'=>false)); ?></td>
		
	</tr>

	<tr>
		<td style="width:10%">Name:</td>
		<td style="width:25%"><?php echo $this->Form->input('Search.name',array('label'=>false));?></td>
		<td style="width:12%">Limit:</td>
		<td style="width:53%"><?php echo $this->Form->input('Search.limit',array('type'=>'number','label'=>false)); ?></td>
	</tr>
	<tr>
		<td style="width:10%">Registered As:</td>
		<td style="width:53%"><?php 
		echo $this->Form->input('Search.cafe',array('label'=>false,'options'=>array('1'=>'Cafe Consumer','0'=>'Non Cafe'))); ?>
          </td>
       
	</tr>


	<tr>
		<td colspan="2">
		<?php echo $this->Form->submit(__('Get Students', true), array('name' => 'getStudent', 'div' => false,'class'=>'tiny radius button bg-blue')); ?>
		</td>

		<td colspan="2" >
		<?php // echo $this->Form->submit(__('Export  Excel', true), array('name' => 'getExcel', 'div' => false,'class'=>'tiny radius button bg-blue','onclick'=>'')); ?>
		</td>

	</tr>
	
</table>
</div>

<?php 
    if (!empty($students)) {
?>
<div class="smallheading"><?php echo  __('Select List of student you want to allow or deny cafe entry.');?></div>
<table  cellspacing="0" cellpadding="0" >
   
	<tr>
			<th>S.No</th>
            <th>
            <?php echo 'Select/ Unselect All'.$this->Form->checkbox("SelectAll", 
            array('id' => 'select-all','checked'=>''));
             ?> </th> 
            <th><?php echo ('Full Name');?></th>
			<th><?php echo ('Gender');?></th>
			<th><?php echo ('Student Number');?></th>
			<th><?php echo ('Ecardnumber');?></th>
			<th><?php echo ('Department');?></th>
	</tr>
	<tr>
	<td colspan="7"><span>What do you like to do ? <?php 
		echo $this->Form->input('Search.allow',array('label'=>false,'options'=>array('1'=>'Allow','0'=>'Deny'))); ?> </span></td>
		
	</tr>
	<?php
	$i = 0;
	$serial_number=1;
	
	foreach ($students as $acceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
       
        <td><?php echo $serial_number++;?></td>
         <td ><?php echo $this->Form->checkbox('Student.approve.' . $acceptedStudent['Student']['id'],array('class'=>'checkbox1')); ?></td> 
       
        <td><?php echo $acceptedStudent['Student']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Student']['gender']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Student']['studentnumber']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Student']['ecardnumber']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Department']['name']; ?>&nbsp;</td>
		
	</tr>
	
<?php 

endforeach; 
           
echo '<tr><td colspan="7">'.$this->Form->Submit('Push To Cafe Gate',array('div'=>false,'name'=>'pushStudentsToCafeGate',
'class'=>'tiny radius button bg-blue')).'</td></tr>';
?>
</table>
<?php 
    }
echo $this->Form->end();
?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
  </div> <!-- end of box-body -->
</div><!-- end of box -->


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

<?php echo $this->Form->create('Student');?>
<script>
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

<div class="box">
     <div class="box-body">
       <div class="row">
	    <div class="large-12 columns">
		  <div onclick="toggleViewFullId('UpdateKoha')">
				  <?php 
				if (!empty($acceptedStudents)) {
					echo $this->Html->image('plus2.gif', array('id' => 'UpdateKohaImg')); 
					?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="UpdateKohaTxt">Display Filter</span><?php
					}
				else {
					echo $this->Html->image('minus2.gif', array('id' => 'UpdateKohaImg')); 
					?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="UpdateKohaTxt">Hide Filter</span><?php
					}
			?>
		   </div>
 <div id="UpdateKoha" style="display:<?php echo (!empty($acceptedStudents) ? 'none' : 'display'); ?>">
 <div class="smallheading">Please select the admission  year, program and program type, you want to update koha database  borrower system.</div>
 	
 <table cellspacing="0" cellpadding="0" class="fs14">
	<tr>
		<td style="width:15%">Admission Year:</td>
		<td style="width:20%"><?php 
		echo $this->Form->input('Search.academicyear',array('id'=>'academicyear',
            'label' => false,'type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Admission Year--",'selected'=>isset($defaultacademicyear)?$defaultacademicyear:''));
		?></td>
		<td style="width:15%">College:</td>
		<td style="width:50%"><?php 
		echo $this->Form->input('Search.college_id',array('label'=>false,'type'=>'select','empty'=>'---Select College --'));
		 ?></td>
	</tr>
	<tr>
		<td>Program:</td>
		<td><?php echo $this->Form->input('Search.program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs, 'default' => (isset($program_id) ? $program_id : false))); ?></td>
		<td>Program Type:</td>
		<td><?php echo $this->Form->input('Search.program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programTypes, 'default' => (isset($program_type_id) ? $program_type_id : false))); ?></td>
	</tr>
	
	<tr>
		<td>Name:</td>
		<td><?php  echo $this->Form->input('Search.name',array('label'=>false)); ?></td>
		<td></td>
		<td></td>
	</tr>
	
	
	<tr>
		<td colspan="6">
		<?php  echo $this->Form->Submit('Continue',array('div'=>false,'name'=>'getacceptedstudent',
'class'=>'tiny radius button bg-blue')); ?>
		</td>
	</tr>
</table>
</div>

<?php 
    if (!empty($acceptedStudents)) {
    echo  __('Select List of student you want to extend and update the koha database of borrower for three months who are promoted.');
?>
<table cellpadding="0" cellspacing="0">
  <tr>
	        
            <th style="width: 30px;"><?php echo ('No.'); ?> </th>
            <th style="width:5%">
            <?php echo 'Select/ Unselect All <br/>'
            .$this->Form->checkbox("SelectAll", 
            array('id' => 'select-all','checked'=>'')); ?> </th> 
            <th><?php echo ('Full Name');?></th>
			<th><?php echo ('Sex');?></th>
			<th><?php echo ('Student Number');?></th>
		
			<th><?php echo ('Department');?></th>
			<th><?php echo ('Academic Year');?></th>
			
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
        <td><?php echo $this->Form->checkbox('AcceptedStudent.approve.' . $acceptedStudent['Student']['id'],array('class'=>'checkbox1')); ?>&nbsp;</td>               
        <td><?php echo $acceptedStudent['AcceptedStudent']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['sex']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Department']['name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['academicyear']; ?>&nbsp;</td>
		
	
	</tr>
	
<?php 

endforeach; 
           
echo '<tr><td colspan=8>'.
$this->Form->Submit('Update',array('div'=>false,'name'=>'updateKohaDB','class'=>'tiny radius button bg-blue')).
'</td></tr>';
?>
</table>
<?php 
  }
   echo $this->Form->end();
?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
   </div> <!-- end of box-body -->
</div><!-- end of box -->

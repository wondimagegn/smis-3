<?php echo $this->Form->create('Student', array('id' => 'moveStudent'));?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="smallheading"> Moving Students To Newly Formed Department. This will happen if the academic commission decide to open a new department after students has attended their current department or department closed and wants the students to have the new department or program. </div>
<table cellpadding="0" cellspacing="0">
	
	<tr>
		<td style="width:15%"> 
			<?php 
			echo $this->Form->input('AcceptedStudent.program_id'); ?>
		</td>


        
		<td style="width:15%"> 
			<?php 
			echo $this->Form->input('AcceptedStudent.program_type_id'); ?>
		</td>


	</tr>
	<tr> 
	
			<td colspan="2"> <?php 
			echo $this->Form->input('AcceptedStudent.academicyear',array('id'=>'academicyear',
			'label' => 'Admission Academic Year','type'=>'select','options'=>$acyear_list,
			'empty'=>"--Select Academic Year--")); ?>
			</td>

			


	</tr>

	<tr> 
		<td style="width:30%"> <?php 
			echo $this->Form->input('AcceptedStudent.department_id',array('label'=>'Current Department',
'style'=>'width:150px')); ?>
			</td>
		<td style="width:30%"> <?php 
			echo $this->Form->input('AcceptedStudent.target_department_id',array('id'=>'TargetDepartmentId',
			'label' => 'Target Department','style'=>'width:150px','options'=>$departments)); ?>
		</td>
	</tr>

	
   
	<tr><td colspan="2"><?php echo $this->Form->Submit('Find The Batch ',array('div'=>false,'name'=>'getacceptedstudent',
'class'=>'tiny radius button bg-blue')); ?> </td>	
</tr></table>
<div id="ProcessingMove">

                </div>
		<div>
		 <?php if(isset($sectionLists) && !empty($sectionLists)){ ?>
			<table cellpadding="0" cellspacing="0">
				
				<tr>
				<td> 
				<?php foreach($sectionLists as $k=>$pv){ ?>
				

					
						<?php 
						echo $this->Form->input('AcceptedStudent.selected_section.'.$k,array('class'=>'upgradableSelectedSection','type'=>'checkbox','value'=>$pv['Section']['id'],'label'=>$pv['Section']['name'] .' '.$pv['YearLevel']['name']));
							

						  ?>
					
				<?php } ?>
</td>
				</tr>
				<tr>
					<td style="width:100%">  <?php echo $this->Form->Submit('Move Selected Section',array('name'=>'moveSelectedSection','class'=>'tiny radius button bg-blue','div'=>false)); ?>
					</td>
				</tr>

			</table>

		<?php } ?>
		</div>

<?php 
    
echo $this->Form->end();
?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->



<script>

$('#moveStudent').submit(function(){
		var image = new Image();
		image.src = '/img/busy.gif';
		//$('#submitBtn').value="Processing ...";
		$('#submitBtn').attr("disabled",true);
		$('#submitBtn').attr("value","Processing...");
		$("#ProcessingMove").empty().html('<img src="/img/busy.gif" class="displayed" >');
		return true;
});

</script>


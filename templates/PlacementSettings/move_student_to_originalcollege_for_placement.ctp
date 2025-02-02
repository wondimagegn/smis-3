<?php echo $this->Form->create('PlacementSetting', array('id' => 'moveStudent'));?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="smallheading">Moving students after attended in their temporary campus college to their orginal college where the actual placement to their department or choice will be conducted.   </div>
<table cellpadding="0" cellspacing="0">
	
	<tr>
		<td style="width:15%"> 
			<?php 
			echo $this->Form->input('PlacementSetting.program_id'); ?>
		</td>


        
		<td style="width:15%"> 
			<?php 
			echo $this->Form->input('PlacementSetting.program_type_id'); ?>
		</td>


	</tr>
	<tr> 
	
			<td colspan="2"> <?php 
			echo $this->Form->input('PlacementSetting.academicyear',array('id'=>'academicyear',
			'label' => 'Admission Academic Year','type'=>'select','options'=>$acyear_list,
			'empty'=>"--Select Academic Year--")); ?>
			</td>

			


	</tr>

	<tr> 
		<td style="width:30%"> <?php 
			echo $this->Form->input('PlacementSetting.college_id',array('label'=>'Current College',
'style'=>'width:150px')); ?>
			</td>
		<td style="width:30%"> <?php 
			echo $this->Form->input('PlacementSetting.target_college_id',array('id'=>'TargetDepartmentId',
			'label' => 'Original College The Student Accepted','style'=>'width:150px','options'=>$originalcolleges)); ?>
		</td>
	</tr>

	
   
	<tr><td colspan="2"><?php echo $this->Form->Submit('Find The Batch ',array('div'=>false,'name'=>'getacceptedstudentsection',
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
						echo $this->Form->input('PlacementSetting.selected_section.'.$k,array('class'=>'upgradableSelectedSection','type'=>'checkbox','value'=>$pv['Section']['id'],'label'=>$pv['Section']['name'] .' '.'1st'.' total hosted student '.count($pv['Student'])));
							

						  ?>
					
				<?php } ?>
</td>
				</tr>
				<tr>
					<td style="width:100%">  <?php echo $this->Form->Submit('Move Student',array('name'=>'moveSelectedStudent','class'=>'tiny radius button bg-blue','div'=>false)); ?>
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


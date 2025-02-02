<?php ?>
<script type="text/javascript">
function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
}
function toggleViewFullId(id, label) {
	if($('.'+id).css("display") == 'none') {
		$('#'+id+'Img').attr("src", '/img/minus2.gif');
		if(label == 1) {
			$('#'+id+'Txt').empty();
			$('#'+id+'Txt').append(' Hide Options');
		}
	}
	else {
		$('#'+id+'Img').attr("src", '/img/plus2.gif');
		if(label == 1) {
			$('#'+id+'Txt').empty();
			$('#'+id+'Txt').append(' Display Options');
		}
	}
	$('.'+id).toggle("slow");
}
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="attrationView index">
<?php //echo $this->Form->create('SenateList');?>
<?php echo $this->Form->Create('Report'); ?>
<div class="smallheading"><?php echo __('Late Grade Submission Stats'); ?></div>

<div style="margin-top:0px" onclick="toggleViewFullId('AttrationRateView', 1)"><?php 
	if (!isset($gradeSubmissionDelay) || empty($gradeSubmissionDelay)) {
		echo $this->Html->image('minus2.gif', array('id' => 'AttrationRateViewImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="AttrationRateViewTxt"> 
		Hide Options</span><?php
		$display = 'display';
		}
	else {
		echo $this->Html->image('plus2.gif', array('id' => 'AttrationRateViewImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="AttrationRateViewTxt">
		Display Options</span><?php
		$display = 'none';
		}
?></div>

<table cellspacing="0" cellpadding="0" class="fs13">
	<tr class="AttrationRateView" style="display:<?php echo $display; ?>">
		<td style="width:11%">Program:</td>
		<td style="width:25%"><?php echo $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs13', 'label' => false, 'type' => 'select', 'options' => $programs, 'default' => $default_program_id)); ?></td>
		<td style="width:11%">Program Type:</td>
		<td style="width:53%"><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs13', 'label' => false, 'type' => 'select', 'options' => $program_types, 'default' => $default_program_type_id)); ?></td>
	</tr>
	<tr class="AttrationRateView" style="display:<?php echo $display; ?>">
		<td>Department:</td>
		<td colspan="3"><?php echo $this->Form->input('department_id', array('id' => 'ProgramType', 
		'class' => 'fs13', 'label' => false, 'type' => 'select', 
		'style'=>'width:200px',
		'options' => $departments, 
		'default' => $default_department_id)); ?></td>
	</tr>
	
	<tr class="AttrationRateView" style="display:<?php echo $display; ?>">
		<td style="width:15%">Academic Year:</td>
		<td style="width:20%"><?php echo $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
		<td style="width:15%">Semester:</td>
		<td style="width:50%"><?php echo $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?></td>
	</tr>
	
	<tr class="AttrationRateView" style="display:<?php echo $display; ?>">
		<td colspan="6">
		<?php echo $this->Form->submit(__('Get Report'), array('name' => 'getReport', 'div' => false)); ?>
		</td>
	</tr>
</table>
<?php echo $this->Form->end();
?>
</div>
<style>
.bordering {
border-left:1px #cccccc solid;
border-right:1px #cccccc solid;
}
.bordering2 {
border-left:1px #000000 solid;
border-right:1px #000000 solid;
border-top:1px #000000 solid;
border-bottom:1px #000000 solid;
}
.courses_table tr td, .courses_table tr th {
padding:1px
}
</style>

<?php 
if (isset($gradeSubmissionDelay) && !empty($gradeSubmissionDelay)) {

    foreach($gradeSubmissionDelay as $program=>$programType) {
       foreach ($programType as $programTypeName=> $statDetail) {
?>
        <p class="fs16">
                List of Instructors who has failed to submit Grade on due date  for 
                <?php echo $this->request->data['Report']['acadamic_year']; ?> AY, Semester
                 <?php  echo $this->request->data['Report']['semester']; ?> <br/>
                <strong> Program : </strong>   <?php 
                      echo $program;
                    ?>
                    <br/>
                <strong> Program Type: </strong>  <?php 
                      echo $programTypeName;
                         
                    ?>
        </p>
       <table style="width:100%">
  
            <tr>
		        <th  class="bordering2" style="vertical-align:bottom; width:3%">S.N<u>o</u>
		        </th>
		        <th class="bordering2" style="vertical-align:bottom; width:20%">Instructor's Name</th>
		        <th class="bordering2" style="vertical-align:bottom; width:20%">College/Institute name</th>
		        <th class="bordering2"  style="vertical-align:bottom; width:20%">Department Name</th>
		        <th class="bordering2"  style="vertical-align:bottom; width:28%">Course</th>
		        <th class="bordering2"  style="vertical-align:bottom; width:9%">Delayed</th>
		    </tr>
		    
		    <?php 
		      $count=0;
		      foreach ($statDetail as $instcollege=>$instdept) {
		        
		       ?>
		      <?php foreach ($instdept as $instdepartment=>$instcourses) { 
		        
		      ?>
		      <?php foreach ($instcourses as $instfullname=>$courses) {
		            foreach($courses as $coursetitle=>$delays) {
		     
		        $count++; ?>
		               <tr>
		                   <td  class="bordering2">
		                       <?php echo $count; ?>
		                    </td>
		                    <td  class="bordering2">
		                       <?php echo $instfullname; ?>
		                    </td>
		                    <td  class="bordering2">
		                      <?php echo $instcollege; ?>
		                    </td>
		                    <td  class="bordering2">
		                        <?php echo $instdepartment; ?>
		                    </td>
		                    
		                      <td  class="bordering2">
		                        <?php echo $coursetitle; ?>
		                    </td>
		                    
		                     <td  class="bordering2">
		                        <?php echo $delays['noDaysDelayed']; ?>
		                    </td>
		                   
		               </tr>
		               
		                <?php } ?>
		            <?php } ?>
		        <?php } ?>
		    <?php } ?>
        </table>
    <?php 
       
    
      }
   }   
} 
?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

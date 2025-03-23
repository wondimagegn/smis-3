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
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="attrationView index">
<?php echo $this->Form->Create('Report'); ?>
<div class="smallheading"><?php echo __('Academically Dismissed Student  View'); ?></div>

<div style="margin-top:0px" onclick="toggleViewFullId('AttrationRateView', 1)"><?php 
	if (!isset($top) || empty($top)) {
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
		<td style="width:15%">Department:</td>
		<td style="width:20%"><?php echo $this->Form->input('department_id', array('id' => 'ProgramType', 
		'class' => 'fs13', 'label' => false, 'type' => 'select', 
		'style'=>'width:200px',
		'options' => $departments, 
		'default' => $default_department_id)); ?></td>
		
		<td style="width:15%">&nbsp;</td>
		<td style="width:50%" colspan="3">&nbsp;</td>
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

if (isset($dismissedStudent) && !empty($dismissedStudent)) {

foreach($dismissedStudent as $program=>$programType) {
    foreach ($programType as $programTypeName=> $statDetail) {
        foreach ($statDetail as $college=> $department) {
           foreach ($department as $departmentname=> $yearLevel) {
?>
    <p class="fs16">
           List of academically Dismissed Students during  <?php echo $this->request->data['Report']['acadamic_year']; ?> A/Y, 
           and Semester    <?php  echo $this->request->data['Report']['semester']; ?>  <br/>
           <strong> Program : </strong>   <?php 
                  echo $program;
                ?>
                <br/>
            <strong> Program Type: </strong>  <?php 
                  echo $programTypeName;
                  
                 
                ?>
                <br/>
              <strong> College: </strong>  <?php 
                  echo $college;
                  
                 
                ?>
                <br/>
               
                <strong> Department: </strong>  <?php 
                  echo $departmentname;
                  
                 
                ?>
                <br/>
                
                
                
    </p>
           
            <?php 
              foreach ($yearLevel as $year=>$studentList) {
                
              ?>
                <table style="width:100%">
                    <tr>
                       <td class="bordering2" colspan="5" >  <strong> Year : </strong>    <?php echo $year; ?> </td> 
                     
                    </tr> 
                    <tr>
                        <td class="bordering2" > S.N<u>o</u> </td> 
                        <td class="bordering2" > ID </td> 
                        <td class="bordering2" > Full Name </td> 
                          <td class="bordering2" > Sex </td> 
                        <td class="bordering2" > CGPA </td> 
                    </tr>     
                 <?php 
                    $count=0;
                    foreach ($studentList as $in=>$val) {
                  ?>
                      <tr>
                        <td class="bordering2" > <?php echo ++$count; ?> </td> 
                        <td class="bordering2" > <?php echo $val['Student']['studentnumber']; ?>  </td> 
                        <td class="bordering2" > <?php echo $val['Student']['full_name']; ?> </td> 
                         <td class="bordering2" > <?php echo $val['Student']['gender']; ?> </td> 
                        <td class="bordering2" > <?php echo $val['StudentExamStatus']['cgpa']; ?> </td> 
                    </tr>     
                  <?php 
                      
                    }
                 ?>
              </table>
             <?php  
            } 
         ?>
        
  <?php 
     }
    }
  }
 }
}   
?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

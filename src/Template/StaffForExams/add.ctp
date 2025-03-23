<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
  //Get Department
function getDepartments() {
            //serialize form data
            var col = $("#ajax_college_id").val();
$("#ajax_department_id").attr('disabled', true);
$("#ajax_department_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
$("#ajax_instructors").empty();
//get form action
            var formUrl = '/staff_for_exams/get_departments/'+col;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: col,
                success: function(data,textStatus,xhr){
$("#ajax_department_id").attr('disabled', false);
$("#ajax_department_id").empty();
$("#ajax_department_id").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
//list of active instructor in a given college
    function updateInstructors() {
            //serialize form data
            var academicyear = $("#academicyear").val().split('/');
			var formatted_academicyear = academicyear[0]+'-'+academicyear[1];
            var formData = $("#ajax_department_id").val()+'~'+formatted_academicyear+'~'+$("#semester").val();
//$("#ajax_class_room").empty();
$("#ajax_instructors").empty().html('<img src="/img/busy.gif" class="displayed" >');
//$("#ajax_class_room").attr('disabled', true);
//$("#ajax_already_recorded_constraints").attr('disabled', true);
//get form action
            var formUrl = '/staff_for_exams/get_instructors/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
//$("#ajax_class_room").attr('disabled', false);
//$("#ajax_class_room").empty();
$("#ajax_instructors").empty();
$("#ajax_instructors").append(data);
//End 
},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
});
return false;
}
</script>

<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="staffForExams form">
<?php echo $this->Form->create('StaffForExam');?>
<div class="smallheading"><?php echo __('Add/Edit Staff For Exam (Invigilators From other colleges)'); ?></div>
<div class="font"><?php echo 'Institute/College: '.$college_name?></div>
<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academicyear',array('label'=>false, 'id'=>'academicyear', 'type'=>'select','options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:$defaultacademicyear, 'style'=>'width:250PX')).'</td>';
		echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label'=>false,'id'=>'semester', 'options'=>array('I'=>'I','II'=>'II', 'III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"", 'style'=>'width:250PX')).'</td></tr>'; 
		unset($colleges[$college_id]);
		echo '<tr><td class="font"> College</td>';
		echo '<td>'.$this->Form->input('college_id',array('label'=>false, 'type'=>'select', 'id'=>'ajax_college_id','onchange'=>'getDepartments()','options'=>$colleges,'empty'=>"--Select College--", 'style'=>'width:250PX')).'</td>';
		echo '<td class="font"> Department</td>'; 
		echo '<td>'. $this->Form->input('department_id',array('label'=>false,'id'=>'ajax_department_id', 'onchange'=>'updateInstructors()','style'=>'width:250px','options'=>$departments,'empty'=>'--Select Department--')).'</td>';
				   
        ?>
        <tr><td colspan="4"><div id="ajax_instructors">

        </div></td></tr>
</table>
<?php echo $this->Form->end(); ?>

</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

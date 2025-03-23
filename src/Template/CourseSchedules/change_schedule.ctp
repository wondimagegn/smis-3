<?php ?>
<script type='text/javascript'>
function getAvailableRoom(id,academic_year, semester,type) {
            //serialize form data
           
            var classPeriods = $("#selected_class_period_id_"+id).val();
            $("#selected_class_period_id_"+id).attr('disabled',true);
            $("#selected_room_id_"+id).empty();
			$("#selected_room_id_"+id).append('<option style="width:100px">loading...</option>');
			$("#selected_room_id_"+id).attr('disabled', true);
			
			
//get form action
            var formUrl = '/course_schedules/get_potential_class_rooms_combo/'
            +classPeriods +"/"+academic_year+"/"+semester+'/'+type;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: classPeriods,
                success: function(data,textStatus,xhr){
                        $("#selected_room_id_"+id).attr('disabled', false);
                        $("#selected_class_period_id_"+id).attr('disabled',false);
                        $("#selected_room_id_"+id).empty();
                        $("#selected_room_id_"+id).append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
 function isInstructorAvailable(id,published_course_id) {
            //serialize form data
           
            var classPeriods = $("#selected_class_period_id_"+id).val();
            $("#selected_class_period_id_"+id).attr('disabled',true);
           
//get form action
          var formUrl = '/course_schedules/instructor_free_occupied/'+
          classPeriods +"/"+published_course_id;
         
            $.ajax({
                type: 'get',
                url: formUrl,
                data: classPeriods,
                success: function(data,textStatus,xhr){
                       
                        $("#instructor_"+id).empty();
                        $("#instructor_"+id).append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
 </script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<?php 
    echo $this->Form->create('CourseSchedule',array('action'=>'schedule_change_update', 'method'=>"GET"));
    echo '<table>';
   
	echo '<tr><td>'.$this->Form->input('class_period_id',array(
	'label'=>false,'style'=>'width:150px','onchange'=>" 

	getAvailableRoom(".$course_schedule_id.",'".$academic_year."','".$semester."','".$type."');

	isInstructorAvailable('".$course_schedule_id."','".$published_course_id."')",'id'=>'selected_class_period_id_'.$course_schedule_id,'type'=>'select',
        'options'=>$get_potential_class_periods_section_is_free['period'],'empty'=>"--Select Period--")).'</tr></td>';
   echo '<tr><td>'.$this->Form->input('class_room_id',array(
	'label'=>false,'style'=>'width:150px','id'=>'selected_room_id_'.$course_schedule_id,'type'=>'select',
        'options'=>$get_potential_class_periods_section_is_free['rooms'],'onchange'=>"this.form.submit();",'empty'=>"--Select Room--")).'</td></tr>';
    echo '<tr><td id="instructor_'.$course_schedule_id.'">';
    echo '</td></tr>';
	echo $this->Form->hidden('id', array('value'=>$course_schedule_id));
	echo '</table>';
	//echo $this->Form->end(__('Change')); 
?>


	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
<a class="close-reveal-modal">&#215;</a>

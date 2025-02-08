<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
//Get Class Period from a given week day
function getexamperiodlist() {
            //serialize form data
            var subCat = $("#ajax_instructor_id").val();
$("#instructor_exam_exclude_date_constraints_details").attr('disabled', true);
$("#instructor_exam_exclude_date_constraints_details").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/instructor_exam_exclude_date_constraints/get_instructor_exam_exclude_date_constraints_details/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
$("#instructor_exam_exclude_date_constraints_details").attr('disabled', false);
$("#instructor_exam_exclude_date_constraints_details").empty();
$("#instructor_exam_exclude_date_constraints_details").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
</script>
<?php
if (isset($instructors_list)) { 
		
		$dropdown_data_array= array();
		if(isset($instructors_list[0]['StaffForExam'])){
			foreach($instructors_list as $ilk=>$ilv){
				$count = 1;
				$dropdown_data_array[$ilv['Staff']['id']]= ($ilv['Staff']['Title']['title'].' '.$ilv['Staff']['full_name'].' ( Position: '.$ilv['Staff']['Position']['position'].' - College: '.$ilv['Staff']['College']['name'].')');
			}
		} else {
			foreach($instructors_list as $ilk=>$ilv){
				$count = 1;
				$dropdown_data_array[$ilv['Staff']['id']]= ($ilv['Title']['title'].' '.$ilv['Staff']['full_name'].' ( Position: '.$ilv['Position']['position'].')');
			}
		}
		echo '<table cellpadding="0" cellspacing="0">';
		echo '<tr><td class="font">'.$this->Form->input('staff_id',array('id'=>'ajax_instructor_id','onchange'=>'getexamperiodlist()','label'=>'Instructors', 'type'=>'select','empty'=>'---Please Instructor---', 'options'=>$dropdown_data_array)).'</td>';
		
		echo '</table>';
	?>
		<div id="instructor_exam_exclude_date_constraints_details">
<?php
	}
?>

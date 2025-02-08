<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
//Get Class Period from a given week day
function getclassperiod() {
            //serialize form data
            var subCat = $("#ajax_weekday_constraints").val()+'~'+$("#ajax_program").val()+'~'+$("#ajax_program_type").val();
$("#ajax_periods_constraints").attr('disabled', true);
$("#ajax_periods_constraints").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/class_room_class_period_constraints/get_periods/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
$("#ajax_periods_constraints").attr('disabled', false);
$("#ajax_periods_constraints").empty();
$("#ajax_periods_constraints").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
        
//Class Room Combo
    function updateclassroomcombo() {
            //serialize form data
            //alert($("#ajax_class_room_block").val());
            var formData = $("#ajax_class_room_block").val();
$("#ajax_class_room").empty();
$("#ajax_class_room").attr('disabled', true);
$("#ajax_already_recorded_constraints").attr('disabled', true);
//get form action
            var formUrl = '/class_room_class_period_constraints/get_class_rooms/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
$("#ajax_class_room").attr('disabled', false);
$("#ajax_class_room").empty();
$("#ajax_class_room").append(data);
//Class Room Class Period Constraints
//var subCat = $("#ajax_department_"+id).val();
		
		var academicyear = $("#academicyear").val().split('/');
		var formatted_academicyear = academicyear[0]+'-'+academicyear[1];

         var subCat = $("#ajax_class_room").val()+'~'+formatted_academicyear+'~'+$("#semester").val();
$("#ajax_already_recorded_constraints").empty();
//get form action
var formUrl = '/class_room_class_period_constraints/get_already_recorded_data/'+subCat;
$.ajax({
type: 'get',
url: formUrl,
data: subCat,
success: function(data,textStatus,xhr){
$("#ajax_already_recorded_constraints").attr('disabled', false);
$("#ajax_already_recorded_constraints").empty();
$("#ajax_already_recorded_constraints").append(data);
},
error: function(xhr,textStatus,error){
alert(textStatus);
}
});
//End 
},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
});
return false;
}
//Class Room Class Period Constraints
function updateconstraints(id,published_course_id,isprimary,course_split_section_id) {
            //serialize form data
			var academicyear = $("#academicyear").val().split('/');
			var formatted_academicyear = academicyear[0]+'-'+academicyear[1];
            var subCat = $("#ajax_class_room").val()+'~'+formatted_academicyear+'~'+$("#semester").val();
$("#ajax_already_recorded_constraints").attr('disabled', true);
$("#ajax_already_recorded_constraints").empty().html('<img src="/img/busy.gif" class="displayed" >');
$("#ajax_periods_constraints").empty();
//get form action
            var formUrl = '/class_room_class_period_constraints/get_already_recorded_data/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
$("#ajax_already_recorded_constraints").attr('disabled', false);
$("#ajax_already_recorded_constraints").empty();
$("#ajax_already_recorded_constraints").append(data);
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
             
<div class="classRoomClassPeriodConstraints form">
<?php echo $this->Form->create('ClassRoomClassPeriodConstraint');?>
<div class="smallheading"><?php echo __('Add Class Room Class Period Constraint'); ?></div>
<div class="info-box info-message"><font color=RED><u>Beware:</u></font><br/> - All college class rooms periods, except you classified as occupied are free by default.</div>
<table cellpadding="0" cellspacing="0">
	<?php 
			$week_days_array = array();
		foreach($week_days as $wdsv){
			$week_day_name = null;
			switch($wdsv['ClassPeriod']['week_day']){
				case 1: $week_day_name ="Sunday"; break;
				case 2: $week_day_name ="Monday"; break;
				case 3: $week_day_name ="Tuesday"; break;
				case 4: $week_day_name ="Wednesday"; break;
				case 5: $week_day_name ="Thursday"; break;
				case 6: $week_day_name ="Friday"; break;
				case 7: $week_day_name ="Saturday"; break;
				default : $week_day_name =null;
			}
			$week_days_array[$wdsv['ClassPeriod']['week_day']] = $week_day_name.'('.$wdsv['ClassPeriod']['week_day'].')';
		}
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('ClassRoomClassPeriodConstraint.academicyear',array('label' => false,'id'=>'academicyear','type'=>'select','options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:"",'empty'=>"--Select Academic Year--",'style'=>'width:150PX')).'</td>';
		echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('ClassRoomClassPeriodConstraint.semester',array('label'=>false, 'id'=>'semester','options'=>array('I'=>'I','II'=>'II', 'III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"",'empty'=>'--select semester--','style'=>'width:150PX')).'</td>'; 
		echo '<td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('ClassRoomClassPeriodConstraint.program_id',array('label'=>false,'id'=>'ajax_program', 'selected'=>isset($selected_program)?$selected_program:"",'empty'=>"--Select Program--", 'style'=>'width:150PX')).'</td></tr>'; 
        echo '<tr><td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('ClassRoomClassPeriodConstraint.program_type_id',array('label'=>false,'id'=>'ajax_program_type', 'selected'=>isset($selected_program_type)? $selected_program_type:"",'empty'=>"--Select Program Type--", 'style'=>'width:150PX')).'</td>'; 
		echo '<td class="font"> Class Room Block</td>';
		echo '<td>'.$this->Form->input('ClassRoomClassPeriodConstraint.class_room_blocks',array('label' =>false,'type'=>'select','id'=>'ajax_class_room_block','onchange'=>'updateclassroomcombo()', 'options'=>$formatted_class_room_blocks,'selected'=>isset($selected_class_room_block)?$selected_class_room_block:"", 'empty'=>"--Select Class Room Blocks--",'style'=>'width:150PX')).'</td>';
		echo '<td class="font"> Class Room</td>';
	   	echo '<td>'. $this->Form->input('ClassRoomClassPeriodConstraint.class_room_id', array('label'=>false,'id'=>'ajax_class_room','onchange'=>'updateconstraints()','type'=>'select','options'=>$classRooms,'selected'=>isset($selected_class_rooms)?$selected_class_rooms:"",'empty'=>'---Select Class Rooms ---','style'=>'width:150PX')).'</td></tr>';
	   	echo '<tr><td class="font"> Option</td>';
		echo '<td>'.$this->Form->input('ClassRoomClassPeriodConstraint.active',array('label'=>false,'type'=>'select','options'=>array(1=>'Occupied',0=>'Free'),'style'=>'width:150PX')).'</td>';
		echo '<td class="font"> Week Day</td>';
		echo '<td>'.$this->Form->input('ClassRoomClassPeriodConstraint.week_day',array('label'=>false,'id'=>'ajax_weekday_constraints', 'onchange'=>'getclassperiod()', 'type'=>'select', 'empty'=>'---Please Select Week Day---','options'=>$week_days_array,'selected'=>isset($selected_week_day)?$selected_week_day:"",'style'=>'width:150PX')).'</td></tr>';
		?>
		<tr><td colspan="6"><div id="ajax_periods_constraints">
		<?php
			if(isset($fromadd_periods) && !empty($fromadd_periods)){
					echo '<table><tr><td colspan="3" class="font"> Select Periods</td></tr><tr>';
					foreach($fromadd_periods as $pk=>$pv){
			
						echo '<td>'.$this->Form->input('ClassRoomClassPeriodConstraint.Selected.'.$pv['PeriodSetting']['period'],array('type'=>
							'checkbox',	'value'=>$pv['ClassPeriod']['id'],'label'=>$pv['PeriodSetting']['period'].' ('.
							$this->Format->humanize_hour($pv['PeriodSetting']['hour']).')')).'</td>';
					}
					echo '</tr></table>';
			}
			?>
		</div></td></tr> 
		
		<?php
        echo '<tr><td colspan="6">'. $this->Form->Submit('Submit',array('name'=>'submit','div'=>false,'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
        ?><tr><td colspan="6"><div id="ajax_already_recorded_constraints">
        <?php 
				if(isset($fromadd_already_recorded_class_room_class_period_constraints)) {
					?> <div class="smallheading">Already Recorded Class Room Class Period Constraints</div>
					<table style='border: #CCC solid 1px'>
					<tr><th style='border-right: #CCC solid 1px'>No.</th><th style='border-right: #CCC solid 1px'>Room
						</th><th style='border-right: #CCC solid 1px'>Block</th><th style='border-right: #CCC solid 1px'>Campus</th><th style='border-right: #CCC solid 1px'>Academic Year</th><th style='border-right: #CCC solid 1px'>Semester</th><th style='border-right: #CCC solid 1px'>Week Day</th><th style='border-right: #CCC solid 1px'>Period</th><th style='border-right: #CCC solid 1px'>Option</th><th style='border-right: #CCC solid 1px'>Action</th></tr>
					<?php
					$count = 1;
					foreach($fromadd_already_recorded_class_room_class_period_constraints as $arck=>$arcv){
		
						$week_day_name = null;
						switch($arcv['ClassPeriod']['week_day']){
							case 1: $week_day_name ="Sunday"; break;
							case 2: $week_day_name ="Monday"; break;
							case 3: $week_day_name ="Tuesday"; break;
							case 4: $week_day_name ="Wednesday"; break;
							case 5: $week_day_name ="Thursday"; break;
							case 6: $week_day_name ="Friday"; break;
							case 7: $week_day_name ="Saturday"; break;
							default : $week_day_name =null;
						}
						$option = null;
						if($arcv['ClassRoomClassPeriodConstraint']['active'] == 0){
							$option= "Free";
						} else {
							$option ="Occupied";
						}
		
						echo "<tr><td style='border-right: #CCC solid 1px'>".$count++. "</td><td style='border-right: #CCC solid 1px'>".
							$arcv['ClassRoom']['room_code']."</td><td style='border-right: #CCC solid 1px'>".
							$arcv['ClassRoom']['ClassRoomBlock']['block_code']."</td><td style='border-right: #CCC solid 1px'>".
							$arcv['ClassRoom']['ClassRoomBlock']['Campus']['name']."<td style='border-right: #CCC solid 1px'>".
							$arcv['ClassRoomClassPeriodConstraint']['academic_year']."</td><td style='border-right: #CCC solid 1px'>".
							$arcv['ClassRoomClassPeriodConstraint']['semester']."</td><td style='border-right: #CCC solid 1px'>".
							$week_day_name.'('.$arcv['ClassPeriod']['week_day'].')'."<td style='border-right: #CCC solid 1px'>".
							$this->Format->humanize_hour($arcv['ClassPeriod']['PeriodSetting']['hour'])."</td><td style='border-right: #CCC solid 1px'>".
							$option."</td><td style='border-right: #CCC solid 1px'>".
						$this->Html->link(__('Delete'), array('action' => 'delete', $arcv['ClassRoomClassPeriodConstraint']['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $arcv['ClassRoomClassPeriodConstraint']['id'],"fromadd")).
						"</td></tr>";
					}
					?></table><?php
				}
			?>
        </div></td></tr>
</table>
<?php echo $this->Form->end(); ?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

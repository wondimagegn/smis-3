<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
//Get departments
function getCollege() {
            //serialize form data
            var campus = $("#ajax_campus_id").val();
$("#ajax_college_id").attr('disabled', true);
$("#ajax_college_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
$("#ajax_department_id").empty();
$("#ajax_year_level_id").empty();
//get form action
            var formUrl = '/meal_hall_assignments/get_colleges/'+campus;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: campus,
                success: function(data,textStatus,xhr){
$("#ajax_college_id").attr('disabled', false);
$("#ajax_college_id").empty();
$("#ajax_college_id").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
//Get departments
function getDepartment() {
            //serialize form data
            var college = $("#ajax_college_id").val();
$("#ajax_department_id").attr('disabled', true);
$("#ajax_department_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
$("#ajax_year_level_id").attr('disabled', true);
$("#ajax_year_level_id").empty();
//get form action
            var formUrl = '/meal_hall_assignments/get_departments/'+college;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: college,
                success: function(data,textStatus,xhr){
$("#ajax_department_id").attr('disabled', false);
$("#ajax_department_id").empty();
$("#ajax_department_id").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });

//get list of year levels from college
            var subUrl = '/meal_hall_assignments/get_year_levels/'+college;
            $.ajax({
                type: 'get',
                url: subUrl,
                data: college,
                success: function(data,textStatus,xhr){
$("#ajax_year_level_id").attr('disabled', false);
$("#ajax_year_level_id").empty();
$("#ajax_year_level_id").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
 //Get year level if department is selected
function getDepartmentYearLevel() {
            //serialize form data
            var dept = $("#ajax_department_id").val()+'~'+$("#ajax_college_id").val();
$("#ajax_year_level_id").attr('disabled', true);
$("#ajax_year_level_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/meal_hall_assignments/get_department_year_levels/'+dept;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: dept,
                success: function(data,textStatus,xhr){
$("#ajax_year_level_id").attr('disabled', false);
$("#ajax_year_level_id").empty();
$("#ajax_year_level_id").append(data);
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

<div class="mealHallAssignments index">
<?php echo $this->Form->create('MealHallAssignment');?>
<div class="smallheading"><?php echo __('Manual Student Assignment and Cancellation'); ?></div>
	<div class="info-box info-message"><font color=RED><u>Beaware:</u></font><br/> -To add student manaually to a given meal hall, you have to selected specific <u>Meal Hall</u>, <u>Academic Year</u> and at least one other parameter from below optional search parameters. </div>
<div class="font"><?php echo __('Optional search parameters');?> </div>
<table cellpadding="0" cellspacing="0">
	<?php
		echo '<tr><td class="font"> Program</td>'; 
        echo '<td>'. $this->Form->input('program_id',array('label'=>false,'disabled'=>true, 'style'=>'width:150px')).'</td>'; 
		echo '<td class="font"> Program Type</td>'; 
        echo '<td>'. $this->Form->input('program_type_id',array('label'=>false, 'selected'=>isset($selected_program_type_id)?$selected_program_type_id:"",'style'=>'width:150px', 'empty'=>"All")).'</td>'; 
        echo '<td class="font"> College</td>'; 
		echo '<td>'. $this->Form->input('college_id',array('label'=>false,'id'=>'ajax_college_id', 'onchange'=>'getDepartment()','selected'=>isset($selected_college_id)?$selected_college_id:"", 'style'=>'width:150px','empty'=>"All")).'</td></tr>'; 
		echo '<tr><td class="font"> Department</td>'; 
		echo '<td>'. $this->Form->input('department_id',array('label'=>false,'id'=>'ajax_department_id', 'onchange'=>'getDepartmentYearLevel()', 'selected'=>isset($selected_department_id)?$selected_department_id:"",'style'=>'width:150px','options'=>$departments,'empty'=>'All')).'</td>';
		echo '<td class="font"> Year Level</td>'; 
        echo '<td>'. $this->Form->input('year_level_id',array('label'=>false, 'id'=>'ajax_year_level_id','options'=>$yearLevels, 'selected'=>isset($selected_year_level_id)?$selected_year_level_id:"",'style'=>'width:150px','empty'=>'All')).'</td>';  
        echo '<td class="font"> Academic Year</td>'; 
        echo '<td>'.$this->Form->input('academic_year',array('label'=>false,'id'=>'academicyear', 'type'=>'select','style'=>'width:150px','options'=>$acyear_array_data, 'selected'=>isset($selected_academicyear)?$selected_academicyear:"",'empty'=>"All")).'</td></tr>';
         echo '<tr><td class="font" > Meal Hall</td>'; 
         echo '<td class="font" >'.$this->Form->input('meal_hall_id',array('label'=>false,'id'=>'mealHall', 'type'=>'select','style'=>'width:150px','options'=>$mealHalls, 'selected'=>isset($selected_meal_hall_id)?$selected_meal_hall_id:"",'empty'=>"All")).'</td></tr>';
        echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>'; 
		
	?> 
</table>
	<table cellpadding="0" cellspacing="0">
	<?php 
		if(!empty($selected_meal_hall_id) && !empty($selected_academicyear)){
			$academicyear = explode('/',$selected_academicyear);
			$formatted_academicyear = $academicyear[0].'-'.$academicyear[1];
			/*
		   echo '<tr><td colspan="12" style="text-align: center;" id="ajax_student">'.$this->Js->link(__('Add'),'/meal_hall_assignments/add_student_meal_hall/'.$selected_meal_hall_id.'~'.$selected_program_type_id.'~'.$selected_college_id.'~'.$selected_department_id.'~'. $selected_year_level_id.'~'.$formatted_academicyear, array('update'=>'#ajax_student','evalScripts'=>true)).'</td></tr>';
		   */
		    echo '<tr><td colspan="12" style="text-align: center;" id="ajax_student">'.$this->Html->link('Add','#',
   array('data-animation'=>"fade",
'data-reveal-id'=>'myModalMealAssignment',
'data-reveal-ajax'=>'/meal_hall_assignments/add_student_meal_hall/'.$selected_meal_hall_id.'~'.$selected_program_type_id.'~'.$selected_college_id.'~'.$selected_department_id.'~'. $selected_year_level_id.'~'.$formatted_academicyear)).'</td></tr>';
		}
	?>
	<tr>
			<th><?php echo $this->Paginator->sort('S.No');?></th>
			<th><?php echo $this->Paginator->sort('Name');?></th>
			<th><?php echo $this->Paginator->sort('ID');?></th>
			<th><?php echo $this->Paginator->sort('meal_hall');?></th>
			<th><?php echo $this->Paginator->sort('Campus');?></th>
			<th><?php echo $this->Paginator->sort('For Academic Year');?></th>
			<th><?php echo $this->Paginator->sort('Assigned Date');?></th>
			<th class="actions"><?php echo __('Actions');?></th> 
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($mealHallAssignments as $mealHallAssignment):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<?php if(!empty($mealHallAssignment['Student']['id'])) {?>
			<td>
				<?php echo $this->Html->link($mealHallAssignment['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $mealHallAssignment['Student']['id'])); ?>
			</td>
			<td>
				<?php echo $this->Html->link($mealHallAssignment['Student']['studentnumber'], array('controller' => 'students', 'action' => 'view', $mealHallAssignment['Student']['id'])); ?>
			</td>
		<?php } else if(!empty($mealHallAssignment['AcceptedStudent']['id'])) {?>
			<td><?php echo $mealHallAssignment['AcceptedStudent']['full_name']; ?>&nbsp;</td>
			<td><?php echo $mealHallAssignment['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
		
		<?php }?>
		<td>
			<?php echo $this->Html->link($mealHallAssignment['MealHall']['name'], array('controller' => 'meal_halls', 'action' => 'view', $mealHallAssignment['MealHall']['id'])); ?>
		</td>
		<td><?php echo $mealHallAssignment['MealHall']['Campus']['name']; ?>&nbsp;</td>
		<td><?php echo $mealHallAssignment['MealHallAssignment']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $this->Format->short_date($mealHallAssignment['MealHallAssignment']['created']); ?>&nbsp;</td>
		 <td class="actions">
			<!-- <?php echo $this->Html->link(__('View'), array('action' => 'view', $mealHallAssignment['MealHallAssignment']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $mealHallAssignment['MealHallAssignment']['id'])); ?> -->
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $mealHallAssignment['MealHallAssignment']['id']), null, sprintf(__('Are you sure you want to delete  %s?'), $mealHallAssignment['Student']['full_name'])); ?>
		</td> 
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>

</div>
</div>
</div>
</div>


<div class="box">
     <div class="box-body">
            <div class="row">
            	<div class="large-12 columns">
            		<div id="myModalMealAssignment" class="reveal-modal" data-reveal>

            		</div>
            	</div>
            </div>
    </div>
</div>

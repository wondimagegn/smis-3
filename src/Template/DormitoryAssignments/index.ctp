<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
//Get departments
function getDepartment() {
            //serialize form data
            var college = $("#ajax_college_id").val();
$("#ajax_department_id").attr('disabled', true);
$("#ajax_department_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
$("#ajax_year_level_id").empty();
//get form action
            var formUrl = '/dormitory_assignments/get_departments/'+college;
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
            return false;
 }
 //Get year level
function getYearLevel() {
            //serialize form data
            var dept = $("#ajax_department_id").val();
$("#ajax_year_level_id").attr('disabled', true);
$("#ajax_year_level_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/dormitory_assignments/get_year_levels/'+dept;
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
  //Get Dormitory Block of a given gender
function getDormitoryBlock() {
            //serialize form data
            var college = $("#ajax_gender").val();
$("#ajax_dormitory_block").attr('disabled', true);
$("#ajax_dormitory_block").empty().html('<img src="/img/busy.gif" class="displayed" >');
$("#ajax_dormitory").empty();
//get form action
            var formUrl = '/dormitory_assignments/get_dormitory_blocks/'+college;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: college,
                success: function(data,textStatus,xhr){
$("#ajax_dormitory_block").attr('disabled', false);
$("#ajax_dormitory_block").empty();
$("#ajax_dormitory_block").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
  //Get dormitory of a given block
function getDormitory() {
            //serialize form data
            var block = $("#ajax_dormitory_block").val();
$("#ajax_dormitory").attr('disabled', true);
$("#ajax_dormitory").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/dormitory_assignments/get_dormitories/'+block;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: block,
                success: function(data,textStatus,xhr){
$("#ajax_dormitory").attr('disabled', false);
$("#ajax_dormitory").empty();
$("#ajax_dormitory").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
</script>
<div class="dormitoryAssignments index">
<?php echo $this->Form->create('DormitoryAssignment');?>
	<div class="smallheading"><?php echo __('List of Dormitory assigned students');?></div>
<?php if(isset($programs)) {?>
	<div class="font"><?php echo __('Optional search parameters');?> </div>
	
	<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Program</td>'; 
        echo '<td>'. $this->Form->input('program_id',array('label'=>false,'empty'=>"All",
        'style'=>'width:150px')).'</td>'; 
		echo '<td class="font"> Program Type</td>'; 
        echo '<td>'. $this->Form->input('program_type_id',array('label'=>false,
        'style'=>'width:150px', 'empty'=>"All")).'</td>'; 
        echo '<td class="font"> Gender</td>'; 
        echo '<td>'. $this->Form->input('gender',array('id'=>'ajax_gender','label'=>false, 'options'=>array('male'=>'Male','female'=>'Female'),'onchange'=>'getDormitoryBlock()', 'style'=>'width:150px','empty'=>"All")).'</td></tr>'; 
        echo '<tr><td class="font"> College</td>'; 
		echo '<td>'. $this->Form->input('college_id',array('label'=>false,'id'=>'ajax_college_id', 'onchange'=>'getDepartment()','style'=>'width:150px','empty'=>"All")).'</td>'; 
		echo '<td class="font"> Department</td>'; 
		echo '<td>'. $this->Form->input('department_id',array('label'=>false,'id'=>'ajax_department_id', 'onchange'=>'getYearLevel()','style'=>'width:150px','options'=>$departments,'empty'=>'All')).'</td>';
		echo '<td class="font"> Year Level</td>'; 
        echo '<td>'. $this->Form->input('year_level_id',array('label'=>false, 'id'=>'ajax_year_level_id','options'=>$yearLevels,'style'=>'width:150px','empty'=>'All')).'</td></tr>'; 
        echo '<tr><td class="font"> Dormitory Block</td>'; 
        echo '<td class="font">'.$this->Form->input('dormitory_block',array('label' => false, 'id'=>'ajax_dormitory_block','style'=>'width:150px','onchange'=>'getDormitory()','type'=>'select', 'options'=>$fine_formatted_dormitories, 'empty'=>"All")).'</td>';
        echo '<td class="font"> Dormitory</td>'; 
        echo '<td class="font">'.$this->Form->input('dormitory',array('label' => false,'id'=>'ajax_dormitory','type'=>'select', 'options'=>$dormitories, 'empty'=>"All")).'</td></tr>';
        echo '<tr><td colspan="6">'.$this->Form->input('limit',array('label' =>'Limit',
        	'size'=>5,'type'=>'number')).'</td></tr>'; 

		echo '<tr><td colspan="3">'.$this->Form->submit('Search',array('class'=>'tiny radius button bg-blue','name' => 'search', 'div' => false,'id'=>'Search')).'</td><td colspan="3">'.$this->Form->submit('Export To Excel',array('class'=>'tiny radius button bg-blue','name' => 'exportToExcel', 'div' => false,'id'=>'Search')).'</td></tr>'; 
	?>
	</table>
	
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('S.No');?></th>
			<th><?php echo $this->Paginator->sort('Name');?></th>
			<th><?php echo $this->Paginator->sort('ID');?></th>
			<th><?php echo $this->Paginator->sort('Dorm');?></th>
			<th><?php echo $this->Paginator->sort('floor');?></th>
			<th><?php echo $this->Paginator->sort('Block');?></th>
			<th><?php echo $this->Paginator->sort('Campus');?></th>
			<th><?php echo $this->Paginator->sort('assignment_date');?></th>
			<th><?php echo $this->Paginator->sort('leave_date');?></th>
			<th><?php echo $this->Paginator->sort('received');?></th>
			<th><?php echo $this->Paginator->sort('received_date');?></th>
			<!-- <th class="actions"><?php echo __('Actions');?></th> -->
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($dormitoryAssignments as $dormitoryAssignment):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		
		$floor = null;
		if($dormitoryAssignment['Dormitory']['floor'] ==1){
			$floor = "Ground Floor";
		} else if($dormitoryAssignment['Dormitory']['floor']==2){
			$floor = ($dormitoryAssignment['Dormitory']['floor']-1)."st Floor";
		} else if($dormitoryAssignment['Dormitory']['floor']==3){
			$floor = ($dormitoryAssignment['Dormitory']['floor']-1)."nd Floor";
		} else if($dormitoryAssignment['Dormitory']['floor']==4){
			$floor = ($dormitoryAssignment['Dormitory']['floor']-1)."rd Floor";
		} else {
			$floor = ($dormitoryAssignment['Dormitory']['floor']-1)."th Floor";
		}
		
		$received = null;
		if($dormitoryAssignment['DormitoryAssignment']['received'] == 0){
			$received = "No";
		} else {
			$received = "Yes";
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<?php if(!empty($dormitoryAssignment['Student']['id'])) {?>
			<td>
				<?php echo $this->Html->link($dormitoryAssignment['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $dormitoryAssignment['Student']['id'])); ?>
			</td>
			<td>
				<?php echo $this->Html->link($dormitoryAssignment['Student']['studentnumber'], array('controller' => 'students', 'action' => 'view', $dormitoryAssignment['Student']['id'])); ?>
			</td>
		<?php } else if(!empty($dormitoryAssignment['AcceptedStudent']['id'])) {?>
			<td><?php echo $dormitoryAssignment['AcceptedStudent']['full_name']; ?>&nbsp;</td>
			<td><?php echo $dormitoryAssignment['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
		
		<?php }?>
		<td>
			<?php echo $this->Html->link($dormitoryAssignment['Dormitory']['dorm_number'], array('controller' => 'dormitories', 'action' => 'view', $dormitoryAssignment['Dormitory']['id'])); ?>
		</td>
		<td><?php echo $floor; ?>&nbsp;</td>
		<td><?php echo $dormitoryAssignment['Dormitory']['DormitoryBlock']['block_name']; ?>&nbsp;</td>
		<td><?php echo $dormitoryAssignment['Dormitory']['DormitoryBlock']['Campus']['name']; ?>&nbsp;</td>
		
		<td><?php echo $this->Format->short_date($dormitoryAssignment['DormitoryAssignment']['created']); ?>&nbsp;</td>
		<td><?php echo $dormitoryAssignment['DormitoryAssignment']['leave_date']; ?>&nbsp;</td>
		<td><?php echo $received; ?>&nbsp;</td>
		<td><?php echo $dormitoryAssignment['DormitoryAssignment']['received_date']; ?>&nbsp;</td>
		<!-- <td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $dormitoryAssignment['DormitoryAssignment']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $dormitoryAssignment['DormitoryAssignment']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $dormitoryAssignment['DormitoryAssignment']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $dormitoryAssignment['DormitoryAssignment']['id'])); ?>
		</td> -->
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
<?php }?>
</div>

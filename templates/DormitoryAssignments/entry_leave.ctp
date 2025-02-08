<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
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
<div class="dormitoryAssignments entry_leave">
<?php echo $this->Form->create('DormitoryAssignment');?>
	<div class="smallheading"><?php echo __('Students Entry/Leave Management');?></div>
	<?php if(isset($fine_formatted_dormitories)) {?>
	<div class="font"><?php echo __('Optional search parameters');?> </div>
	
	<table cellpadding="0" cellspacing="0">
	<?php 
        echo '<tr><td class="font"> Dormitory Block</td>'; 
        echo '<td class="font">'.$this->Form->input('Search.dormitory_block',array('label' => false, 'id'=>'ajax_dormitory_block','onchange'=>'getDormitory()','type'=>'select', 'options'=>$fine_formatted_dormitories, 'style'=>'width:150px','empty'=>"All")).'</td>';
        echo '<td class="font"> Dormitory</td>'; 
        echo '<td class="font">'.$this->Form->input('Search.dormitory',array('label' => false, 'id'=>'ajax_dormitory','type'=>'select', 'style'=>'width:150px','options'=>$dormitories, 'empty'=>"All",'selected'=>isset($this->request->data['Search']['dormitory']) ? $this->request->data['Search']['dormitory'] : '' )).'</td></tr>';
        		
		echo '<tr><td colspan="4">'.$this->Form->Submit('Search',array('name'=>'search','div'=>false,'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
	?>
	</table>
	<?php } ?>
<?php if(isset($dormitoryAssignments)) { 
	if(!empty($dormitoryAssignments)){
?>
	<table cellpadding="0" cellspacing="0">
		<tr><td colspan="11" class="smallheading"><?php echo $this->Form->input('DormitoryAssignment.update_date.',array('type'=>'date','label'=>'Received Or/And Leave Date:')); ?>&nbsp;</td></td>
	
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
		
		$received_date = $dormitoryAssignment['DormitoryAssignment']['received_date'];
		$leave_date = $dormitoryAssignment['DormitoryAssignment']['leave_date'];

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
		<td><?php echo (empty($leave_date) && $received=="Yes")?$this->Form->checkbox('DormitoryAssignment.Is_return.'.$dormitoryAssignment['DormitoryAssignment']['id']):(empty($leave_date)?$leave_date:$this->Format->short_date($leave_date)) ?>&nbsp;</td>
		<td><?php echo $received; ?>&nbsp;</td>
		<td><?php echo !empty($received_date)?$this->Format->short_date($received_date):$this->Form->checkbox('DormitoryAssignment.Is_received.'.$dormitoryAssignment['DormitoryAssignment']['id']); ?>&nbsp;</td>
		<!-- <td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $dormitoryAssignment['DormitoryAssignment']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $dormitoryAssignment['DormitoryAssignment']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $dormitoryAssignment['DormitoryAssignment']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $dormitoryAssignment['DormitoryAssignment']['id'])); ?>
		</td> -->
	</tr>
<?php endforeach; 
 echo '<tr><td colspan="11">'. $this->Form->Submit('Update Received Date/Leave Date',array('name'=>'update','div'=>false,'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
?>
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
	
<?php 
	} else {
	  echo "<div class='info-box info-message'><span></span> The selected Dormitory block or Dormitory is empty. Please select dormitory block or dormitory that contain at least one student.</div>";
	}
} 
echo $this->Form->end(); ?>
</div>

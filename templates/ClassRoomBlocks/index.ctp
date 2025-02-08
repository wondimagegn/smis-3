<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';

//Get class room block
function getClassRoomBlock() {
            //serialize form data
            var campus = $("#ajax_campus_id").val();
$("#ajax_class_room_block_id").attr('disabled', true);
$("#ajax_class_room_block_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/class_room_blocks/get_class_room_blocks/'+campus;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: campus,
                success: function(data,textStatus,xhr){
$("#ajax_class_room_block_id").attr('disabled', false);
$("#ajax_class_room_block_id").empty();
$("#ajax_class_room_block_id").append(data);
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
            
<div class="classRoomBlocks index">
<?php echo $this->Form->create('ClassRoomBlock');?>
<div class="smallheading"><?php echo __('Class Room Blocks');?></div>
<?php echo "<div class='font'>".$college_name."</div>"; ?>
	<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td>'.$this->Form->input('campus_id',array('id'=>'ajax_campus_id','onchange'=>'getClassRoomBlock()','empty'=>'All')).'</td>';
			
		echo '<td >'.$this->Form->input('class_room_block_code',array('id'=>'ajax_class_room_block_id', 'label'=>'Class Room Block', 'type'=>'select','options'=>$campus_classRoomBlocks,'empty'=>'All')).'</td></tr>'; 
		echo '<tr><td colspan=2>'.$this->Form->end(array('label'=>'Search','class'=>'tiny radius button bg-blue')).'</td></tr>'; 
	?>
	</table>
 
	<table cellpadding="0" cellspacing="0"  style="border: #CCC double 2px">
	<tr>
			<th class="font"><?php echo 'S.N<u>o</u>';?></th>
			<th class="font"><?php echo $this->Paginator->sort('college_id');?></th>
			<th class="font"><?php echo $this->Paginator->sort('campus_id');?></th>
			<th class="font"><?php echo $this->Paginator->sort('block_code');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$block_count = $this->Paginator->counter('%start%');
	foreach ($classRoomBlocks as $classRoomBlock):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td class="font"><?php echo $block_count++; ?>&nbsp;</td>
		<td class="font">
			<?php echo $this->Html->link($classRoomBlock['College']['name'], array('controller' => 'colleges', 'action' => 'view', $classRoomBlock['College']['id'])); ?>
		</td>
		<td class="font">
			<?php echo $this->Html->link($classRoomBlock['Campus']['name'], array('controller' => 'campuses', 'action' => 'view', $classRoomBlock['Campus']['id'])); ?>
		</td>
		<td class="font"><?php echo $classRoomBlock['ClassRoomBlock']['block_code']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $classRoomBlock['ClassRoomBlock']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $classRoomBlock['ClassRoomBlock']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $classRoomBlock['ClassRoomBlock']['id']), null, sprintf(__('Are you sure you want to delete block %s?'), $classRoomBlock['ClassRoomBlock']['block_code'])); ?>
		</td>
	</tr>
		<tr><td colspan="5"><table style="border: #CCC dashed 2px"><tr>
		<td colspan="7" class="font" style="border-right: #CCC solid 1px; text-align:center;"><?php (__('List of Class Rooms Under the Block'));?></td></tr>
		<tr><th style="border-right: #CCC solid 1px"><?php echo 'No.';?></th><th style="border-right: #CCC solid 1px"><?php echo 'Class Room';?></th><th style="border-right: #CCC solid 1px"><?php echo 'Available For Lecture';?></th><th style="border-right: #CCC solid 1px"><?php echo 'Available For Exam';?></th><th style="border-right: #CCC solid 1px"><?php echo 'Lecture Capacity';?></th><th style="border-right: #CCC solid 1px"><?php echo 'Exam Capacity';?></th><th style="border-right: #CCC solid 1px"><?php echo 'Action';?></th></tr>
		<?php 
		$count = 1;
		foreach ($classRoomBlock['ClassRoom'] as $classRoom){
			if($classRoom['available_for_lecture']== 1){
				$available_for_lecture = "Yes";
			} else {
			 	$available_for_lecture = "No";
			}
			if($classRoom['available_for_exam']== 1){
				$available_for_exam = "Yes";
			} else {
			 	$available_for_exam = "No";
			}
			echo '<tr><td style="border-right: #CCC solid 1px">'.$count++.'</td>';
			echo '<td style="border-right: #CCC solid 1px">'.$classRoom['room_code'].'</td>';
			echo '<td style="border-right: #CCC solid 1px">'.$available_for_lecture.'</td>';
			echo '<td style="border-right: #CCC solid 1px">'.$available_for_exam.'</td>';
			echo '<td style="border-right: #CCC solid 1px">'.$classRoom['lecture_capacity'].'</td>';
			echo '<td style="border-right: #CCC solid 1px">'.$classRoom['exam_capacity'].'</td>';

			echo '<td style="border-right: #CCC solid 1px">'.$this->Html->link(
    'View Assignment',
    '#',
   array('class'=>'jsview','data-animation'=>"fade",
'data-reveal-id'=>'myModal','data-reveal-ajax'=>"/classRoomBlocks/get_modal/".$classRoom['id'])
).'&nbsp;&nbsp;&nbsp'.$this->Html->link(__('Delete'), array('action' => 'deleteClassRoom', $classRoom['id']), null, sprintf(__('Are you sure you want to delete class room %s?'), $classRoom['room_code'])).

'</td></tr>';
	
	 
		}
		?>
	</table></td></tr>
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
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

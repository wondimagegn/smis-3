<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
 //Get year level
function getYearLevel() {
            //serialize form data
            var dept = $("#ajax_department_id").val();
$("#ajax_year_level_id").attr('disabled', true);
$("#ajax_year_level_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/excluded_published_course_exams/get_year_levels_for_view/'+dept;
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
            
<div class="excludedPublishedCourseExams index">
<?php echo $this->Form->create('ExcludedPublishedCourseExam');?>
	<div class="smallheading"><?php echo __('Excluded Published Courses From Final Exams Schedule');?></div>
	<div class="font"><?php echo 'Colege/Institute: '.$college_name ?></div>
	<div class="font"><?php echo __('Optional search parameters');?> </div>
<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academic_year',array('label'=>false,'type'=>'select','options'=>$acyear_array_data,'empty'=>"All", 'style'=>'width:150PX')).'</td>';
        echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label'=>false,'options'=>array('I'=>'I','II'=>'II','III'=>'III'), 'empty'=>'All', 'style'=>'width:150PX')).'</td>'; 
		echo '<td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('program_id',array('label' => false,'empty'=>"All", 'style'=>'width:150PX')).'</td></tr>'; 
        echo '<tr><td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('program_type_id',array('label' => false,'style'=>'width:150PX','empty'=>"All")).'</td>'; 
        echo '<td class="font"> Department</td>';  
		echo '<td>'. $this->Form->input('department_id',array('label' =>false, 'id'=>'ajax_department_id','onchange'=>'getYearLevel()','empty'=>'All', 'style'=>'width:150PX')).'</td>';
        echo '<td class="font"> Year Level</td>';
		echo '<td>'. $this->Form->input('year_level_id',array('id'=>'ajax_year_level_id','label' => false, 'style'=>'width:150PX', 'empty'=>"All")).'</td></tr>';

        echo '<tr><td colspan="6">'.$this->Form->end(array('label'=>__('Search'),
'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
	?> 
</table>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo ('S.N<u>o</u>');?></th>
			<th><?php echo $this->Paginator->sort('published_course_id');?></th>
			<th><?php echo $this->Paginator->sort('section');?></th>
			<th><?php echo $this->Paginator->sort('semester');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('department');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$count = $this->Paginator->counter('%start%');
	foreach ($excludedPublishedCourseExams as $excludedPublishedCourseExam):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $count++; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($excludedPublishedCourseExam['PublishedCourse']['Course']['course_code_title'],array('controller' => 'published_courses', 'action' => 'view',
				$excludedPublishedCourseExam['PublishedCourse']['id'])); ?>
		</td>
		<td><?php echo $excludedPublishedCourseExam['PublishedCourse']['Section']['name']; ?>&nbsp;</td>
		<td><?php echo $excludedPublishedCourseExam['PublishedCourse']['semester']; ?>&nbsp;</td>
		<td><?php echo $excludedPublishedCourseExam['PublishedCourse']['academic_year']; ?>&nbsp;</td>
		<?php if(isset($excludedPublishedCourseExam['PublishedCourse']['Department']['name']) && !empty($excludedPublishedCourseExam['PublishedCourse']['Department']['name'])) {?>
		<td><?php echo $excludedPublishedCourseExam['PublishedCourse']['Department']['name']; ?>&nbsp;</td>
		<?php } else {?>
		<td><?php echo ("---"); ?>&nbsp;</td>
		<?php }?>
		<td class="actions">
			<!--- <?php echo $this->Html->link(__('View'), array('action' => 'view', $excludedPublishedCourseExam['ExcludedPublishedCourseExam']['id'])); ?>

			 <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $excludedPublishedCourseExam['ExcludedPublishedCourseExam']['id'])); ?> --->
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $excludedPublishedCourseExam['ExcludedPublishedCourseExam']['id']), null, sprintf(__('Are you sure you want to delete?'), $excludedPublishedCourseExam['ExcludedPublishedCourseExam']['id'])); ?>
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
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

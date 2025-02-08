<?php echo $this->Form->create('SectionSplitForPublishedCourses');  ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';

function toggleViewFullId(id) {
	if($('#'+id).css("display") == 'none') {
		$('#'+id+'Img').attr("src", '/img/minus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Hide Filter');
		}
	else {
		$('#'+id+'Img').attr("src", '/img/plus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Display Filter');
		}
	$('#'+id).toggle("slow");
}
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div>
        <p class="fs16">
        <strong> Important Note: </strong> 
         This tool will help you to  view splitted section 
         for specific courses.
        </p>
        <div onclick="toggleViewFullId('ListPublishedCourse')"><?php 
	        if (!empty($turn_off_search)) {
		        echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
		        ?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
		        }
	        else {
		        echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
		        ?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
		        }
        ?></div>
<div id="ListPublishedCourse" style="display:<?php echo (!empty($turn_off_search) ? 'none' : 'display'); ?>">
    <table class="fs13 small_padding">
	       
		    <tr>
			    <td style="width:15%">Academic Year:</td>
			    <td style="width:35%"><?php 
			        echo $this->Form->input('Search.academicyear',array(
                'label' => false,'options'=>$acyear_array_data,
                'empty'=>"--Select Academic Year--",'selected'=>
                  isset($this->request->data['Search']['academicyear']) ? $this->request->data['Search']['academicyear']:(isset(
                  $defaultacademicyear) ? $defaultacademicyear:'')
                
                )
                
                );
			    ?>
			    </td>
			    <td style="width:13%"> Semester:</td>
			    <td style="width:37%">
			    <?php 
			        echo $this->Form->input('Search.semester',array('options'=>array('I'=>'I','II'=>'II',
                'III'=>'III'),'label'=>false));
			    ?>
			
			    </td>
		    </tr>
		      
		    <tr>
			    <td style="width:15%">Program:</td>
			    <td style="width:35%"><?php 
			     echo $this->Form->input('Search.program_id',array('id'=>'program_id','label'=>false,
			     'type'=>'select','div'=>false));
			
			     ?>
			    </td>
			    <td style="width:13%"> Program Type</td>
			    <td style="width:37%">
			    &nbsp;
			    <?php 
			      echo $this->Form->input('Search.program_type_id',array('id'=>'program_type_id','label'=>false,
			     'type'=>'select','div'=>false));
			
			    ?>		
			    </td>
		     </tr>
	        <tr>
			    <td style="width:15%">Year Level:</td>
			    <td style="width:35%"><?php 
			     echo $this->Form->input('Search.year_level_id',array('id'=>'year_level_id','label'=>false,
			     'type'=>'select','multiple'=>'checkbox','div'=>false));
			
			     ?>
			    </td>
			    <td style="width:13%"> &nbsp;</td>
			    <td style="width:37%">
			    &nbsp;
			
			    </td>
		    </tr>

		    <tr>
			    <td colspan="4"><?php echo $this->Form->submit(__('Continue'), 
			    array('name' => 'getSplittedSectionForCourse',  'div' => false)); ?></td>
		    </tr>
	    </table>
	</div>
</div>
<?php 
    if (!empty($sectionSplitForPublishedCourses)) {
?>
<div class="sectionSplitForPublishedCourses index">
	<h2><?php echo __('Split Sections For Published Courses');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('published_course_id');?></th>
			<th><?php echo $this->Paginator->sort('section_id');?></th>
			<th><?php echo $this->Paginator->sort('type');?></th>
			<th class="actions"><?php echo __('Actions');?></th> 
	</tr>
	<?php
	$i = 0;
	foreach ($sectionSplitForPublishedCourses as $sectionSplitForPublishedCourse):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $sectionSplitForPublishedCourse['SectionSplitForPublishedCourse']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($sectionSplitForPublishedCourse['PublishedCourse']['Course']['course_code_title'], 
				array('controller' => 'courses', 'action' => 'view', 
				$sectionSplitForPublishedCourse['PublishedCourse']['course_id'])); ?>
		</td> 
		<!-- <td><?php echo $sectionSplitForPublishedCourse['PublishedCourse']['Course']['course_code_title'];?>&nbsp;</td> -->
		<td>
			<?php echo $this->Html->link($sectionSplitForPublishedCourse['Section']['name'], array('controller' => 'sections', 'action' => 'view', $sectionSplitForPublishedCourse['Section']['id'])); ?>
		</td>
		<td><?php echo $sectionSplitForPublishedCourse['SectionSplitForPublishedCourse']['type']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $sectionSplitForPublishedCourse['SectionSplitForPublishedCourse']['id'])); ?>
		<!-- <?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $sectionSplitForPublishedCourse['SectionSplitForPublishedCourse']['id'])); ?> 
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', 
			$sectionSplitForPublishedCourse['SectionSplitForPublishedCourse']['id']), null, 
			sprintf(__('Are you sure you want to delete # %s?'),
			 $sectionSplitForPublishedCourse['SectionSplitForPublishedCourse']['id'])); ?> -->
			 
			 <?php echo $this->Html->link(__('Delete'), array('action' => 'delete', 
			$sectionSplitForPublishedCourse['SectionSplitForPublishedCourse']['id']), null, 
			sprintf(__('Are you sure you want to delete # %s?'),
			 $sectionSplitForPublishedCourse['SectionSplitForPublishedCourse']['id'])); ?>
		</td> 
	</tr>
	<tr><table style="width:20%;"><tr>
		<th><?php echo 'Split into Section Name';?></th></tr>
		<?php 
		foreach ($sectionSplitForPublishedCourse['CourseSplitSection'] as $courseSplitSection){
			echo '<tr><td>'.$courseSplitSection['section_name'].'</td></tr>';
		}
		?>
	</table></tr>
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

<?php 
    }
?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

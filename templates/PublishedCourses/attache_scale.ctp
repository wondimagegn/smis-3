<?php ?>
<script type="text/javascript">
function updateGradeScaleDetail(id) {
           
            //serialize form data
            var gradeDetail = $("#grade_scale_"+id).val();
          
			//get form action
            var formUrl = '/gradeScaleDetails/get_grade_scale_detail/'+gradeDetail;
            
            $.ajax({
                type: 'get',
                url: formUrl,
                data: gradeDetail,
                success: function(data,textStatus,xhr){
						
						$("#grade_scale_detail_"+id).empty();
						$("#grade_scale_detail_"+id).append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
			
            return false;
        }
function showHideGradeScale(id,count) {
	if($("#ShowHideGradeScale").val() == 'Show Grade Scale') {
	   
		var p_course_id = id;
		$("#grade_scale_detail_"+count).empty();
		$("#grade_scale_detail_"+count).append('Loading ...');
			var formUrl = '/published_courses/get_course_grade_scale/'+p_course_id;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: p_course_id,
				success: function(data,textStatus,xhr){
						$("#grade_scale_detail_"+count).empty();
						$("#grade_scale_detail_"+count).append(data);
						$("#ShowHideGradeScale").attr('value', 'Hide Grade Scale');
				},
				error: function(xhr,textStatus,error){
						alert(textStatus);
				}
			});
		}
		else {
			$("#grade_scale_detail_"+count).empty();
			$("#ShowHideGradeScale").attr('value', 'Show Grade Scale');
		}
		
		return false;
}
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<?php 

 echo $this->Form->create('PublishedCourse');
   if (!isset($turn_off_search)) {
?>

<table cellpadding="0" cellspacing="0">
<?php 
echo "<tr><td colspan=2 class='smallheading'> Attach grade scale to courses.</td></tr>";
echo '<tr><td>'.$this->Form->input('PublishedCourse.academic_year',array(
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($this->request->data['PublishedCourse']['academic_year']) ? $this->request->data['PublishedCourse']['academic_year'] : $defaultacademicyear)).'</td>';
            
          ?>
	<?php 
	        if ($role_id == ROLE_DEPARTMENT) {
            echo '<td>'. $this->Form->input('PublishedCourse.program_id',array('label'=>'Program','empty'=>"--Select Program--")).'</td>';
           }
           echo '</tr>'; 
           echo '<tr><td>'.$this->Form->input('PublishedCourse.semester',array('options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'),'empty'=>'--select semester--')).'</td>';
            if ($role_id == ROLE_DEPARTMENT) {
                echo '<td>'.$this->Form->input('PublishedCourse.year_level_id').'</td></tr>';   
            }
            ?>
	<tr><td colspan=2><?php echo $this->Form->submit('Continue',array('name'=>'getPublishedCourseList','class'=>'tiny radius button bg-blue','div'=>'false')); ?> </td>	
</tr></table>

<?php 

}

if(isset($section_organized_published_courses) && !empty($section_organized_published_courses)
&& isset($gradeScales) && !empty($gradeScales)){

?>
<div>
<h2><?php echo __('Select scale you want attach for the given course.');?></h2>
	<table cellpadding="0" cellspacing="0">
	<?php 
	        $count=0;
	        $hide_button=0;
            foreach ($section_organized_published_courses as $section_name=>
            $sectioned_published_courses) {
            
     ?>
      <tr><td colspan=7><h3><?php echo $section_name;?></h3></td></tr>
	  <tr>
	        <th></th>
			<th><?php echo __('S.No');?></th>
			<th><?php echo __('Course Title');?></th>
			<th><?php echo __('Course Code');?></th>
			<th><?php echo __('Course Credit');?></th>
			<th><?php echo __('Scale');?></th>
	  </tr>
	<?php
	$i = 0;
	
	$ser_number=1;
	
	foreach ($sectioned_published_courses as $publishedCours):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		
	?>
	<tr<?php echo $class;?>>
	   
	    <td>
	       <?php 
	     
	       echo $this->Form->hidden('Published.'.$count.'.id',
	       array('value'=>$publishedCours['PublishedCourse']['id']));
	       
	       ?>  
	    </td>
		<td><?php echo $ser_number++;?> </td>
		<td><?php echo $publishedCours['Course']['course_title']; ?>&nbsp;</td>
		<td><?php echo $publishedCours['Course']['course_code']; ?>&nbsp;</td>
	    <td><?php echo $publishedCours['Course']['credit']; ?>&nbsp;</td>
	    <td><?php 
	      echo $this->Form->hidden('Published.'.$count.'.id',array('value'=>$publishedCours['PublishedCourse']['id']));
	    if(!$publishedCours['PublishedCourse']['scale_readOnly']){
	       
	        if (isset($gradeScales[$publishedCours['Course']['grade_type_id']])) {
	        echo $this->Form->input('Published.'.$count.'.grade_scale_id',
	        array('options'=>$gradeScales[$publishedCours['Course']['grade_type_id']],'label'=>false,'empty'=>'--select scale--','selected'=>($publishedCours['PublishedCourse']['grade_scale_id']!=0 || $publishedCours['PublishedCourse']['grade_scale_id']!="")? $publishedCours['PublishedCourse']['grade_scale_id'] :'',
	        'onchange' => 'updateGradeScaleDetail('.$count.')','id'=>'grade_scale_'.$count)); 
	        } else {
	              
			         echo '<font class="fs16"> The course is attached to '.$gradeTypes[$publishedCours['Course']['grade_type_id']].' grade type, but grade scale is not defined for using this grade type.';
			         echo $this->Html->link('Click Here To Define', array('controller' => 'gradeScales', 'action' => 'add')).'</font>';
			        
			       
	        }
	        
	    } else {
	      if($publishedCours['PublishedCourse']['grade_scale_id']!=0){
	           $hide_button++;
	          // foreach ($gradeScales as $scale_index=>$value) {
	                echo "Grade has been submitted, you can not deattach scale or attach scale";
	          //}
	           echo '<input type="button" value="Show Grade Scale" onclick="showHideGradeScale('.$publishedCours['PublishedCourse']['id'].','.$count.')" id="ShowHideGradeScale">';
	
	      } else {
	        
	        echo "Grade has been submitted, you can not attach/deattach scale";
	      }
	      
	    }
	    ?></td>
	  
	</tr>
	<?php 
	
	    echo "<tr><td colspan=6 id='grade_scale_detail_".$count."' style='align:right'></td></tr>";
	?>
	  <?php $count++; ?>
<?php endforeach; ?>

   <?php } ?>
	</table>
	  <table>
            <tr>
                  
                   <td style='padding:0'> <?php 
                  if ($hide_button != $count) {
                  echo $this->Form->Submit('Attach/Deattach Scale.',array('name'=>'attachescaletocourse','class'=>'tiny radius button bg-blue','div'=>'false'));
                  } 
                  ?></td>
              
            </tr>
           
      </table>
</div>
<?php 
}
echo $this->Form->end();
?> 
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

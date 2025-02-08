<div class="sectionSplitForPublishedCourses form">
<?php echo $this->Form->create('SectionSplitForPublishedCourse');  ?>
<script type='text/javascript'>
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

<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="smallheading"><?php echo __('Split Section For Course'); ?></div>

<p class="fs16">
                    <strong> Important Note: </strong> 
                    This tool will help you to splite section for particular courses. Only for those courses not
                     grade submitted is allowed for section split.
                    
</p>
<div onclick="toggleViewFullId('ListPublishedCourse')"><?php 
	if (!empty($sections)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
		}
?></div>
<div id="ListPublishedCourse" style="display:<?php echo (!empty($sections) ? 'none' : 'display'); ?>">
<table class="fs13 small_padding">
	   
		<tr>
			<td style="width:15%">Academic Year:</td>
			<td style="width:35%">
			<?php 
			    echo $this->Form->input('academicyear',array(
            'label' => false,'type'=>'select','style'=>'width:60%','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",
                'selected'=>isset($this->request->data['SectionSplitForPublishedCourse']['academicyear'])?
                $this->request->data['SectionSplitForPublishedCourse']['academicyear']:
                (isset($defaultacademicyear) ? $defaultacademicyear:'' )
            
            )
            
            );
			?>
			</td>
			<td style="width:13%"> Semester:</td>
			<td style="width:37%">
			<?php 
			    echo $this->Form->input('semester',array('options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'),'label'=>false,'style'=>'width:60%'));
			?>
			
			</td>
		</tr>
		  
		<tr>
			<td style="width:15%">Program:</td>
			<td style="width:35%"><?php 
			 echo $this->Form->input('program_id',array('id'=>'program_id','label'=>false,
			 'type'=>'select','div'=>false,'style'=>'width:60%'));
			
			 ?>
			</td>
			<td style="width:13%"> Program Type:</td>
			<td style="width:37%">
			&nbsp;
			<?php 
			  echo $this->Form->input('program_type_id',array('id'=>'program_type_id','label'=>false,
			 'type'=>'select','div'=>false,'style'=>'width:60%'));
			
			?>		
			</td>
		</tr>
	    <tr>
			<td style="width:15%">Year Level:</td>
			<td style="width:35%"><?php 
			  
			     echo $this->Form->input('year_level_id',array('id'=>'year_level_id','label'=>false,
			     'type'=>'select','div'=>false,'style'=>'width:60%'));
			   
			
			 ?>
			</td>
			<td style="width:13%"> &nbsp;</td>
			<td style="width:37%">
			&nbsp;
			
			</td>
		</tr>

		<tr>
			<td colspan="4"><?php echo $this->Form->submit(__('Continue'), 
			array('name' => 'search',  'div' => false)); ?></td>
		</tr>
	</table>
</div>

 <?php 
 if(!empty($sections)){
        
    ?>
    <table style="width:100%;">
    <?php 
	$sections_array = array();
	$sections_array[-1] = "--Please Select Section--";
    foreach($sections as $key=>$value) {
    	if(!empty($value['Section']['name'])) {
		    echo $this->Form->hidden('Section.'.$key.'.id', array('value'=>$value['Section']['id']));
		    $sections_array[]= $value['Section']['name'].' (Section students curriculum: '.
				$sections_curriculum_name[$key].')';
		}
        }
    echo '<tr><td>'.$this->Form->input('selectedsection',array('label'=>'Sections','type'=>'select',
        'options'=>$sections_array,'id'=>'splitSection','style'=>'width:300px')).'</td></tr>';

    echo '<tr><td>'.$this->Form->input('number_of_section',array('type'=>'select','options'=>array('2'=>2,'3'=>3))).
		'</td></tr>';
	echo '<tr><td id="PublishedCoursesList"></td></tr>';

	echo $this->Js->get("#splitSection")->event("change", $this->Js->request(array('controller'=>'publishedCourses',
			'action'=>'getPublishedCoursesForSplit'), array(
						'update'=>"#PublishedCoursesList",
						'async' => true,
						'method' => 'post',
						'dataExpression'=>true,
							 'beforeSend' => '$("#busy_indicator").show();',
                        'complete' => '$("#busy_indicator").hide();',
						'data'=> $this->Js->serializeForm(array(
						'isForm' => false,
						'inline' => true
			))
		))
	);
			//echo "<div id='PublishedCoursesList'></div>";	
    ?>
	
	</table>
<?php 
} else if(empty($sections) && !($isbeforesearch)){
    echo "<div class='info-box info-message'><span></span>No section is found with the search criteria</div>";
}
?>
</div>

<script type="text/javascript">

  $(document).ready(function() { 
  
	if($("#splitSection").val){
	
        $('#PublishedCoursesList').load('/publishedCourses/getPublishedCoursesForSplit/2');
	}
  }); 
</script>

<?php echo $this->Form->end(); ?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

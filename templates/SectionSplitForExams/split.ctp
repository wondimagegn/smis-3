             
<?php echo $this->Form->create('SectionSplitForExam');  ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">

<div class="smallheading"><?php echo __('Split Section For Exam'); ?></div>
<table cellpadding="0" cellspacing="0">
	<?php 
       	echo '<tr><td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('program_id',array('label'=>false,'empty'=>"--Select Program--", 'style'=>'width:200PX')).'</td>'; 
        echo '<td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('program_type_id',array('label'=>false,'empty'=>"--Select Program Type--", 'style'=>'width:200PX')).'</td>'; 
        echo '<td class="font"> Semester</td>';
		echo '<td>'.$this->Form->input('semester',array('label'=>false,'options'=>array('I'=>'I','II'=>'II','III'=>'III'),'empty'=>'--select semester--','style'=>'width:200PX')).'</td></tr>'; 
        echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search','div'=>false,'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
	?> 
</table>
</div>
 </div>
  <div class="row">
	  <div class="large-6 columns">
<?php 
 if(!empty($sections)){
        
    ?>
    <table style="width:100%;">
    <?php 
	$sections_array = array();
	$sections_array[-1] = "--Please Select Section--";
    foreach($sections as $key=>$value) {
    	if(!empty($value['Section']['name'])){
		    echo $this->Form->hidden('Section.'.$key.'.id', array('value'=>$value['Section']['id']));
		    $sections_array[]= $value['Section']['name'];
        }
        }
    echo '<tr><td>'.$this->Form->input('selectedsection',array('label'=>'Sections','type'=>'select',
        'options'=>$sections_array,'id'=>'splitSection')).'</td></tr>';
    echo '<tr><td>'.$this->Form->input('number_of_section',array('type'=>'select','options'=>array('2'=>2,'3'=>3))).
		'</td></tr>';
	

	echo $this->Js->get("#splitSection")->event("change", $this->Js->request(array('controller'=>'publishedCourses',
			'action'=>'getPublishedCoursesForExamForSplit'), array(
						'update'=>"#PublishedCoursesList",
						'async' => true,
						'method' => 'post',
						'dataExpression'=>true,
						'data'=> $this->Js->serializeForm(array(
						'isForm' => false,
						'inline' => true
			))
		))
	);
			
    ?>
	
	</table>
<?php 
} else if(empty($sections) && !($isbeforesearch)){
    echo "<div class='info-box info-message'><span></span>No section is found with the search criteria</div>";
}
?> 

	

	  </div> <!-- end of columns 6 -->
	  <div  class="large-6 columns">
	    <div id='PublishedCoursesList'></div></td>
	  </div>


	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
<?php echo $this->Form->end(); ?>
<script type="text/javascript">

  $(document).ready(function() { 
  
	if($("#splitSection").val){
        $('#PublishedCoursesList').load('/publishedCourses/getPublishedCoursesForExamForSplit/2');
	}
  }); 
</script>

<?php echo $this->Js->writeBuffer();?>

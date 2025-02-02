<?php ?>
<?php echo $this->Form->create('PublishedCourse');?>
<?php echo $this->Html->script('jquery-selectall'); ?> 
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="publishedCourses form">
<?php 
   if (!isset($turn_off_search)) {
?>
<table cellpadding="0" cellspacing="0">
<tr><td colspan=2 class="smallheading"> Unpublish or delete courses from the publish list.</td></tr>
        <?php 
            echo '<tr><td>'.$this->Form->input('PublishedCourse.academic_year',array(
                        'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
                        'empty'=>"--Select Academic Year--",'selected'=>isset($defaultacademicyear)?$defaultacademicyear:'')).'</td></tr>';
            echo '<tr><td>';
            echo $this->Form->input('PublishedCourse.semester',array('options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'),'empty'=>'--select semester--'));
            echo '</td>';            
            echo '</tr>';
                        
          ?>
	<?php 
			
            ?>
	<tr><td><?php echo $this->Form->submit('Continue',array('name'=>'getsection','div'=>'false')); ?> </td>	
</tr></table>



<?php 

}
?> 

<?php 
    if (isset($show_unpublish_page)) {
    
        if(!empty($publishedcourses)) {
            
           
             // echo $this->Form->input('PublishedCourse.publish_up',array('label'=>'Publish Start'));
             echo "<table><tr><td><div class='smallheading'>Select the course you want to unpublish/publish as drop/delete </div></td></tr></table>";
           
           ?>
         
           <?php 
            
            echo "<table id='fieldsForm'><tbody>";
            foreach ($publishedcourses as $section_name=>$sectioned_published_courses) {
                
            ?>
            <tr><td colspan=7><h3><?php echo $section_name;?></h3></td></tr>
            <tr>
            <?php 
            //echo "<th style='padding:0'>Check All/Uncheck All <br/>".$this->Form->checkbox("SelectAll", array('id' => 'select-all','checked'=>''))."</th>";
            //echo '<th style="padding:0"> Check All/Uncheck All <br/>' . $this->Form->checkbox(null, array('id' => 'select-all')) . '</th>';
            echo "<th style='padding:0'> Select </th>";
            echo "<th style='padding:0'> S.No </th>";
            echo "<th style='padding:0'> Course Title </th>";
            echo "<th style='padding:0'> Course Code </th>";
            echo "<th style='padding:0'> Lecture hour </th>";
            echo "<th style='padding:0'> Tutorial hour </th>"; 
            echo "<th style='padding:0'> Credit </th></tr>";
           
            $count=1;
            $course_registered_only=0;
            foreach ($sectioned_published_courses as $kc=>$vc) {
           
            ?>
              <?php 
	                $red=null;
	               
	                if (isset($courses_not_allowed[$vc['PublishedCourse']['section_id']]) && 
	                in_array($vc['Course']['id'],$courses_not_allowed[$vc['PublishedCourse']['section_id']])) {
	
	                    $red='style="color:red;"'; 
	             
	                } 
	              
	  
	           ?>
	
               <tr <?php echo $red;?> >
                
                <?php 
               
                 //echo '<td>'.$this->Form->checkbox('PublishedCourse.unpublish.'.$vc['Course']['id']).'</td>';
                 if($vc['PublishedCourse']['unpublish_readOnly']){
                     echo '<td>**</td>';
                     $course_registered_only++;
                 } else {
                   echo '<td>'.$this->Form->checkbox('Course.pub.'.$vc['PublishedCourse']['section_id'].'.'.$vc['Course']['id']) . '</td>';
                 }
                 echo "<td>".$count.'</td><td>'.$vc['Course']['course_title'].'</td>';
                 echo "<td>".$vc['Course']['course_code']."</td>";
                 echo "<td>".$vc['Course']['lecture_hours']."</td><td>".$vc['Course']['tutorial_hours']."</td>";
                 echo "<td>".$vc['Course']['credit']."</td>";
                 
                 echo "</tr>";
                $count++;
             }
                 if($course_registered_only>0){ 
                     echo "<tr><td colspan=5>**: Those courses with ** are not 
                     allowed to unpublished since students has already registered or 
                     grade has been submitted.</td></tr>";
                 }
            }
             
           echo "</tbody></table>";
             ?>
              <table>
            <tr>
                <td style='padding:0'> <?php 
                 // echo $this->Form->submit('Unpublished Selected',array('name'=>'unpublishselected','div'=>'false'));?></td>
                   <td style='padding:0'> <?php 
                  echo $this->Form->submit('Delete Selected',array('name'=>'deleteselected','div'=>'false'));?></td>
                   <td style='padding:0'> <?php 
                  echo $this->Form->submit('Publish As Drop Selected',array('name'=>'dropselected','div'=>'false'));?></td>
              
            </tr>
           
           </table>
              
             <?php 
             
        }
    
    }
echo $this->Form->end();
?>

</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

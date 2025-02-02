<?php ?>
<!-- Container Begin -->
<div class="row" style="margin-top:-20px">
                <div class="large-4 columns">
                        <div class="box bg-transparent ">
                            <!-- /.box-header -->
                            <div class="box-header no-pad bg-transparent">

                                <h3 style="margin-bottom:20px;" class="box-title">
                                   <span>Registration Stat of <?php echo $currentAcademicYear.'-'.$currentSemester ?></span>
                                </h3>
                            </div>
                             <div style="margin:15px 0 0" class="box-body no-pad">

                                <div class="stats-wrap">
                                    <h2><b class="counter-up" style="color:#666;">
			<?php echo $statArray['Registration']['total_registration'];?></b> 
		<span  style="background:#666;" >+<b  class="counter-up">
<?php echo (($statArray['Registration']['total_registration'])/($statArray['Registration']['total_active_student_in_section']-$statArray['Registration']['dismissalStat']['dismissedTotalCount']))*100 ?> </b>%</span></h2>
                                    <p class="text-grey">Total Registered<small>Completed</small>
                                    </p>
  <h4 style="color:#333;"><b class="counter-up"><?php echo  $statArray['Registration']['total_registration_female'];?></b> <span  style="background:#333;">+
<b class="counter-up">

<?php echo (($statArray['Registration']['total_registration_female'])/($statArray['Registration']['total_active_female_in_section']-$statArray['Registration']['dismissalStat']['dismissedFemaleTotalCount']))*100 ?> 
</b></span></h4>
                                    <p>Total Female<small>Completed</small>
                                    </p>

	<h4 style="color:#333;"><b class="counter-up">
<?php echo  $statArray['Registration']['total_registration_male'];?>
</b> <span  style="background:#333;">+<b class="counter-up">


<?php echo (($statArray['Registration']['total_registration_male'])/($statArray['Registration']['total_active_male_in_section']-$statArray['Registration']['dismissalStat']['dismissedMaleTotalCount']))*100 ?> 
</b></span></h4>
                                    <p>Total Male<small>Completed</small>
                                    </p>


                                </div>

                            </div>
					          
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>

				    <div class="large-4 columns">
                           <div class="box">
                            <div class="box-header bg-transparent">
                                <!-- tools box -->
                                <div class="pull-right box-tools">

                                    <span class="box-btn" data-widget="collapse"><i class="icon-minus"></i>
                                    </span>
                                    <span class="box-btn" data-widget="remove"><i class="icon-cross"></i>
                                    </span>
                                </div>
                                <h3 class="box-title"><i class="icon-graph-pie"></i>
                                    <span>Admission Summary</span>
                                </h3>
                            </div>
                            <!-- /.box-header -->
						     <div class="box-body " style="display: block;">
                                <div style="margin:0" class="row summary-border-top">
                                    <div class="large-6 columns">
                                        <div class="summary-nest">
                                          Overall  
										  <h2 class="text-black"><span class="counter-up">
<?php echo $statArray['Student']['total_female']; ?></span><small></small></h2>
                                            <p>Women</p>
                                        </div>

                                    </div>
                                    <div class="large-6 columns summary-border-left">
                                        <div class="summary-nest">
											Overall
                                            <h2 class="text-black"><span class="counter-up"><?php  echo $statArray['Student']['total_male'];  ?></span><small></small></h2>
                                            <p>Man</p>
                                        </div>
                                    </div>
                                </div>
							    
							    <div style="margin:0" class="row summary-border-top">
									<div class="large-12 columns">
									  <h6 class="text-black" style="text-align:center">
											<?php echo $currentAcademicYear;
?>						
									  </h6>			
									</div>                                    
									<div class="large-6 columns">
                                        
										<div class="summary-nest">
										
                                            <h2 class="text-black"><span class="counter-up"><?php echo $statArray['Student']['total_new_female']; ?></span></h2>
                                            <p>Women</p>
                                        </div>

                                    </div>
                                    <div class="large-6 columns summary-border-left">
                                        <div class="summary-nest">
											                                        
											<h2 class="text-black"><span class="counter-up"><?php echo $statArray['Student']['total_new_male'];?></span><small></small></h2>
                                            <p>Man</p>
                                        </div>
                                    </div>
                                </div>

	
						   </div>
						
						 </div>
                    </div>

                <div class="large-4 columns">
                        <div class="box bg-transparent ">
                            <!-- /.box-header -->
                            <div class="box-header no-pad bg-transparent">

                                <h3 style="margin:0 20px 0 -5px;" class="box-title">
                                    <span>STATS</span>
                                </h3>


                            </div>
                            <div style="margin:15px 0 0" class="box-body no-pad">

                                <div class="stats-wrap">
                                    <h2><b class="counter-up" style="color:#666;">
			<?php echo $statArray['Student']['total'];?></b> 
		<span  style="background:#666;" >+<b  class="counter-up">
<?php echo ($statArray['Student']['total_new']/$statArray['Student']['total'])*100 ?> </b>%</span></h2>
                                    <p class="text-grey">Total students<small>This Year</small>
                                    </p>

                                    <h4><b class="counter-up" style="color:#888;">
<?php echo $statArray['Student']['total_graduate_overall'];?></b> <span style="background:#888;">+<b class="counter-up"> <?php
		
	if($statArray['Student']['total_graduate_overall']>0) {
	 echo ($statArray['Student']['total_graduate_new']/$statArray['Student']['total_graduate_overall'])*100;
	} else {
		echo '0';
	}

 ?>
</b>%</span></h4>
                                    <p>Graduate <small></small>
                                    </p>

                                    <h4 style="color:#333;"><b class="counter-up">
<?php echo $statArray['Student']['total_new'];?></b> <span  style="background:#333;"><b class="counter-up"></b></span></h4>
                                    <p>New students<small><?php echo $currentAcademicYear; ?> </small>
                                    </p>
                                </div>

                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>

</div>
<!-- End of Container Begin -->
<div class="row" >
   <div class="large-4 columns">
   	
              <div class="box">
                            <!-- /.box-header -->
                            <div class="box-header no-pad bg-transparent">

                                 <h3 style="margin-bottom:20px;" class="box-title">
                                   <span>Dismissal Stat of 
                                   
		<?php 
		if(isset($statArray['Registration']['dismissalStat']['prevACSem']['academic_year']) && 
		!empty($statArray['Registration']['dismissalStat']['prevACSem']['academic_year'])) {
			 echo $statArray['Registration']['dismissalStat']['prevACSem']['academic_year'].'-'.$statArray['Registration']['dismissalStat']['prevACSem']['semester'];		
		
		}
		?>                                   
                                   
                                   </span>
                                </h3>

                            </div>
                            <div style="margin:15px 0 0" class="box-body no-pad">

                                <div class="stats-wrap">
                                
                                
                                	   <h4><b class="counter-up" style="color:#888;">
<?php echo  $statArray['Registration']['dismissalStat']['totalRegistrationInPrevSemAc'];?></b> 
</h4>
                                    <p>Total Registered <small>
                                    <?php 
                                    echo $statArray['Registration']['dismissalStat']['prevACSem']['academic_year'].
                                    '-'.$statArray['Registration']['dismissalStat']['prevACSem']['semester']; ?></small>
                                    </p>
                                    
                                    
                                     <h4><b class="counter-up" style="color:#888;">
<?php echo  $statArray['Registration']['dismissalStat']['dismissedTotalCount'];?></b> <span style="background:#888;">+
<b class="counter-up"> <?php
	echo ($statArray['Registration']['dismissalStat']['dismissedTotalCount']/(
$statArray['Registration']['dismissalStat']['totalRegistrationInPrevSemAc']))*100;

 ?>

</b>%</span></h4>
                                    <p>Dismissed <small><?php echo $statArray['Registration']['dismissalStat']['prevACSem']['academic_year'].'-'.$statArray['Registration']['dismissalStat']['prevACSem']['semester']; ?></small>
                                    </p>


                                    <h4><b class="counter-up" style="color:#888;">
<?php echo  $statArray['Registration']['dismissalStat']['dismissedFemaleTotalCount'];?></b> <span style="background:#888;">+
<b class="counter-up"> <?php
	echo ($statArray['Registration']['dismissalStat']['dismissedFemaleTotalCount']/(
$statArray['Registration']['dismissalStat']['totalRegistrationInPrevSemAc']))*100;

 ?>

</b>%</span></h4>
                                    <p>Dismissed Female <small><?php echo $statArray['Registration']['dismissalStat']['prevACSem']['academic_year'].'-'.$statArray['Registration']['dismissalStat']['prevACSem']['semester']; ?></small>
                                    </p>

                                    <h4><b class="counter-up" style="color:#888;">
<?php echo  $statArray['Registration']['dismissalStat']['dismissedMaleTotalCount'];?></b> <span style="background:#888;">+
<b class="counter-up"> <?php
	echo ($statArray['Registration']['dismissalStat']['dismissedMaleTotalCount']/(
$statArray['Registration']['dismissalStat']['totalRegistrationInPrevSemAc']))*100;

 ?>

</b>%</span></h4>
                                    <p>Dismissed Male <small><?php 
                                    echo $statArray['Registration']['dismissalStat']['prevACSem']['academic_year'].'-'.$statArray['Registration']['dismissalStat']['prevACSem']['semester']; ?></small>
                                    </p> 
                                </div>

                            </div>
                            <!-- /.box-body -->
                        </div>
               <!-- /.box -->
   </div>
   <div class="large-4 columns">
              <div class="box bg-transparent ">
                            <!-- /.box-header -->
                            <div class="box-header no-pad bg-transparent">

                                 <h3 style="margin-bottom:20px;" class="box-title">
                                   <span>Grade Submission Delay 
		<?php 
		echo $currentAcademicYear.'-'.$currentSemester;
		?>                                   
                                   
                                   </span>
                                </h3>

                            </div>
                            <div style="margin:15px 0 0" class="box-body no-pad">

                                <div class="stats-wrap">
                                
                                
                                	   <h4><b class="counter-up" style="color:#888;">
<?php echo $gradeSubmissionDelay['Instructor']['totalCourseAssignment'];?></b> 
</h4>
                                    <p>Total Course <small>
                                    <?php 
                                  echo $currentAcademicYear.'-'.$currentSemester;                                 
                                    
												?>                                    
                                    </p>
                                    
                                    
                                     <h4><b class="counter-up" style="color:#888;">
<?php echo $gradeSubmissionDelay['Instructor']['noInstDelayedSub'];?></b> <span style="background:#888;">+
<b class="counter-up"> <?php
	echo ($gradeSubmissionDelay['Instructor']['noInstDelayedSub']/($gradeSubmissionDelay['Instructor']['totalCourseAssignment']))*100;

 ?>

</b>%</span></h4>
                                    <p>Delayed <small><?php 
                                    
                                    	  echo $currentAcademicYear.'-'.$currentSemester; 
												?>                                    
                                    </p>

                                </div>

                            </div>
                            <!-- /.box-body -->
                        </div>
               <!-- /.box -->
   </div>
</div>

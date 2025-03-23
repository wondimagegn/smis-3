<?php ?>
 <!-- Container Begin -->
<div class="row" style="margin-top:-20px">
      <div class="large-4 columns">
            <div class="box bg-transparent ">
                  <!-- /.box-header -->
                   <div class="box-header no-pad bg-transparent">
                                <h3 style="margin-bottom:20px;" class="box-title">
                                    <span>ACTIVE COURSE INSTRUCTOR ASSIGNMENT OF <?php echo $currentAcademicYear.'/'.$currentSemester;?></span>
                                </h3>
                            </div>
							 <div style="margin:15px 0 0" class="box-body no-pad">
                              <div class="stats-wrap">
                                    <h2><b class="counter-up" style="color:#666;">
												<?php echo $total_course_instructor_assignment_current_ac;?>
												</b>
												<span  style="background:#666;" >+<b  class="counter-up"><?php echo ($total_course_instructor_assignment_current_ac/$total_instructor_active)*100 ?> </b>%</span></h2>
                                    <p class="text-grey">Total Instructor Course Assigned
                                    <small><?php echo $currentAcademicYear.'/'.$currentSemester;?>
                                    </small>
                                    </p>
								      </div>
		  								<div class="stats-wrap">
                                    <h2><b class="counter-up" style="color:#666;">
											<?php echo $total_instructors_created_cont_assessement;?></b> 
												<span  style="background:#666;" >+<b  class="counter-up"><?php echo ($total_instructors_created_cont_assessement/$total_course_instructor_assignment_current_ac)*100 ?> </b>%</span></h2>
                                    <p class="text-grey">
                                    	Total Continouse Assigment Created<small><?php echo $currentAcademicYear.'/'.$currentSemester;?></small>
                                    </p>
								 		</div>
							</div>
            </div>
          <!-- /.box -->
      </div>
	   <div class="large-8 columns">
		  <div class="chart">
        
        <div id="columnwrapper" style="display: block; float: left; width:90%; margin-bottom: 20px;"></div>
        <div class="clear"></div>	

        <?php echo $this->Highcharts->render($chartName); ?>

        </div>
      </div>
</div>


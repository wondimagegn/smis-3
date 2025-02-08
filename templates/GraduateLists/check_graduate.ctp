<?php 
?>
<!-- Container Begin -->
<div class="large-offset-12 large-12 columns">
  <div class="box bg-white-transparent">
    <!-- /.box-header -->
    <div class="box-body bg-white-transparent " style="display: block;">
            <div class="row">
                        <div class="large-12 columns">
								 <div class="edumix-signup-panel">
							       <h6>This is an interface for  employer and any stakeholder who would like to check graduates of our university. Forgery protection is one of our main value!</h6>
								
								</div>
						</div>
						<div class="large-12 columns">
							  <?php echo $this->Html->link(__('Back', true), array('controller'=>'users','action' => 'login'),array('class'=>'forgot-button')); ?> 
							
						</div>
				</div>
				<div class="row">
				<?php echo $this->Form->create('GraduateList');?>

						  <div class="large-12 columns">
						
						    <?php echo $this->Form->input('studentID', array('size' => '40','placeholder'=>'Student Number/ID', 'class' => 'username', 'label'=> 'Student Number/ID'));?>
							    <div class="login-button">
						     <?php echo $this->Form->Submit(__('Check', true),array('class'=>'btn-primary','name'=>'continue','div'=>false)); ?>

								</div>
						</div>
					

					   
					
                    <br>
				<?php echo($this->Form->end()); ?>
				</div>    
				<div class="row">
					<div class="large-12 columns">
						<?php
								$flash_message = $this->Session->flash();
								if(!empty($flash_message)) {
								?>	
								<?php echo $flash_message; ?> 
								<?php
								}
								?>
						
					</div>
				</div>
				<div class="row">   
						 <div class="large-6 columns">               
						<?php 
							
							if(!empty($students['GraduateList']) && !empty($students['Student'])){
								echo $this->element('student_graduation_check');

							} else if(empty($students['GraduateList']) && !empty($students['Student'])){
									echo $this->element('student_graduation_check');
							}
						?>
						</div>
						<div class="large-6 columns">  
						<?php 
						if(!empty($students['Student'])){
								echo '<p><strong>Note:</strong>If you need student official  copy, please send us your company details to our email <a href="email:our@amu.edu.et">our@amu.edu.et</a>. It is going to take 2-4 business days to verify your request and send the student official copy to your company address. </p>';
						}
						?>
						</div> 

			 	</div>
		
    </div>
  </div>
</div>

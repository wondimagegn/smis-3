<?php ?>
<style>
table.small_padding tr td {
padding:2px
}
</style>
<div class="box"> 
     <div class="box-body">
       <div class="row">    
<?php 
     echo $this->Form->create('Log');
?>
	    <div class="large-12 columns">
              <h5 class="box-title">
                
<?php echo __('View Logs');?>
              </h5>
    	
	<table class="fs13">
		<tr>
			<td style="width:5%"> From:</td>
			<td style="width:45%"><?php echo $this->Form->input('change_date_from', array('label' => false, 'type' => 'datetime','style'=>'width:50px', 'dateFormat' => 'MDY', 'minYear' => Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc', 'selected' => array('year' => (isset($this->request->data['Dashboard']['change_date_from']) ? $this->request->data['Dashboard']['change_date_from']['year'] : date('Y')), 'month' => (isset($this->request->data['Dashboard']['change_date_from']) ? $this->request->data['Dashboard']['change_date_from']['month'] : date('m')), 'day' => (isset($this->request->data['Dashboard']['change_date_from']) ? $this->request->data['Dashboard']['change_date_from']['day'] : date('d')-14)))); ?></td>
			<td style="width:5%"> To:</td>
			<td style="width:45%"><?php echo $this->Form->input('change_date_to', array('label' => false, 'type' => 'datetime','style'=>'width:50px', 'dateFormat' => 'MDY', 'minYear' => Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc')); ?></td>
		</tr>
		
		<tr>
			<td>Action:</td>
			<td><?php 
			echo  $this->Form->input('action',array('label'=> false, 'type' => 'select','options'=>array('edit'=>'Update','add'=>'Created',
            'delete'=>'Delete/Cancel'),'empty'=>'--select action--','required'));
            
			
			 ?></td>
			<td>Activty:</td>
			<td><?php 
			 echo  $this->Form->input('model',array('label'=> false, 'type' => 'select','options'=>array(
			 'ExamGrade'=>'Exam Grade',
			 'CourseRegistration'=>'Course Registration',
			 'CourseAdd'=>'Course Add',
			 'CourseAdd'=>'Course Add',
             'Curriculum'=>'Curriculum',
             'Course'=>'Course',
             'Student'=>'Student Admission',
             'Section'=>'Section',
            ),'empty'=>'--select activity--','required'));
            
			 ?></td>
		</tr>
		<tr>
			<td>Limit:</td>
			<td><?php echo $this->Form->input('limit', array('maxlength' => 5, 'label'=>false, 'style' => 'width:50px','value'=>5, 'type' => 'text')); ?></td>
			<td>User:</td>
			<td><?php echo $this->Form->input('username', array('label'=>false, 'style' => 'width:370px')); ?></td>
			
    	</tr>
    
		<tr>
			<td colspan="4"><?php echo $this->Form->submit(__('View logs'), array(
			'div' => false)); ?></td>
		</tr>
	</table>
   </div>
   <div class="large-12 columns">
   		<div class="row">
   		  <div class="large-6 columns">	
   			<p>
		 	<strong>Note:-</strong>
			 <ul>
			<?php
			echo "<li>Logs are searchable only if it is not more than 1 years old.</li>";
	
			?>	
			<?php
			echo "<li>() - means value created first time</li>";
			echo "<li>(NG)=>(C) - Changed from NG to C</li>";
			echo "<li>() => (56cabd60-xxxx-xxxx-xxxx-xxxxxxxxxxxx) -Action taker system generated unique ids.</li>";
	
			?>	
			
			</ul>
			</p>
			</div>
		  <div class="large-6 columns">	
   			<p>
		 	 <ul>
			
			<?php
			echo "<li> ()=>(1)  - Approved</li>";
	
			echo "<li> ()=>(-1) - Rejected</li>";
	
			echo "<li> ()=>(0)  - Pending</li>";
	
			?>	
			</ul>
			</p>
			</div>
		
		<div>	
	 </div>	  
   </div>
   <div class="large-12 columns">
		 <?php 
		    if (!empty($logs)) {     
		 ?>
		  <p class="fs15"><?php echo __('List of logs based on the above given condition/s');?></p>
	<table cellpadding="0" cellspacing="0" style="table-layout:fixed">
	<tr>
		<th style="width:3%">N<u>o</u></th>
		<th style="width:15%"><?php echo 'Description';?></th>
		<th style="width:10%"><?php echo 'Event Date';?></th>
	</tr>
	<?php
	$i = 0;
	$start=1;
	foreach ($logs as $log):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++ ; ?>&nbsp;</td>
		<td>
		<?php echo $log['Log']['event']; ?> 
		</td>
		<td><?php echo $this->Format->humanize_date_short2($log['Log']['created']); ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</table>
	
	
	
	 <?php } ?>
           
	   </div>
	  </div>
	</div>
</div>

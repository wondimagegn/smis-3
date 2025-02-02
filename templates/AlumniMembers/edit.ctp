<?php echo $this->Form->create('AlumniMember'); ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  	<div class="large-12 columns">
	  	   <div class="row">
	  	     <div class="large-12 columns">
		  		<h3> <?php 
		  		echo __('Register to our alumni portal.'); ?>
		  		</h3>
		  		<?php if(isset($errors) 
		  		&& !empty($errors)) { ?>
		  		<p class="rejected">
		  		  <?php foreach($errors as $ercode=>$errorlist){ 
		  		    echo ucwords($ercode).':';
		  		    echo '<ul>';
		  		  	foreach($errorlist as $ek=>$erv){
		  		  ?>
		  			<?php
		  				echo '<li>'.$erv.'</li>';
		  				}
		  				echo '</ul>';	
		  			 } 
		  		?>
		  		</p>
		  		<?php }?>
		  		
		  	 </div>
		  	 <div class="large-12 columns">
		  		 <div class="row">
		  		 		<div class="large-4 columns">
			  		   <label for="title">
			  		   <?php 
		  		echo $this->Form->input('id');
		  		echo __('Title'); ?>
			  		   </label>
			  		   <?php echo $this->Form->input('title',array('label'=>'','required'=>'required')); ?>
			  		   
			  		   </div>
			  		   
			  		   <div class="large-4 columns">
			  		   <label for="first_name">
			  		   <?php 
		  		echo __('First Name'); ?>
			  		   </label>
			  		   <?php echo $this->Form->input('first_name',array('label'=>'','placeholder'=>'First Name','required'=>'required')); ?>
			  		   
			  		   </div>
			  		   
			  		   <div class="large-4 columns">
			  		   <label for="last_name">
			  		   <?php 
		  		echo __('Last Name'); ?>
			  		   </label>
			  		   <?php echo $this->Form->input('last_name',array('label'=>'','placeholder'=>'Last  Name','required'=>'required')); ?>
			  		   
			  		   </div> 
		  		 </div>
		  		 
		  		 <div class="row">
		  		       
			  		   <div class="large-4 columns">
			  		    <label>
			  		   <?php 
		  		echo __('Gender'); ?>
			  		   </label>
			  		   <?php  
			  	 $options=array('Male'=>'Male','Female'=>'Female');
		        $attributes=array('legend'=>false);  
		            
		        echo $this->Form->input('gender',array('options'=>$options,'type'=>'radio','legend'=>false,
		        'separator'=>'&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; ','label'=>false));
		        ?>
			  		   </div>
			  		   
			  		   <div class="large-4 columns">
			  		    <label for="date_of_birth">
			  		   <?php 
		  		echo __('Date of Birth'); ?>
			  		   </label>
			  		    <?php echo $this->Form->input('date_of_birth',array('label'=>'','id'=>'date')); ?>
			  		   </div>
			  		   
			  		   <div class="large-4 columns">
			  		    <label>
			  		   <?php 
		  		echo __('Program'); ?>
			  		   </label>
			  		    <?php echo $this->Form->input('program',array('label'=>'')); ?>
			  		   </div>
		  		 </div>
		  		 
		  		 <div class="row">
			  		  
			  		   <div class="large-4 columns">
			  		    <label>
			  		   <?php 
		  		echo __('Phone'); ?>
			  		   </label>
			  		    <?php echo $this->Form->input('phone',array('label'=>'','id'=>"phone",'placeholder'=>'Phone',
			  		    'required'=>'required')); ?>
			  		   </div>
			  		   <div class="large-4 columns">
			  		      <label>
			  		   <?php 
		  		echo __('Work Phone'); ?>
			  		   </label>
			  		    <?php echo $this->Form->input('work_telephone',array('label'=>'','placeholder'=>'Work Phone')); ?>
			  		   </div>
			  		   <div class="large-4 columns">
			  		    <label for="email">
			  		   <?php 
		  		echo __('Email'); ?>
			  		   </label>
			  		   <?php echo $this->Form->input('email',array('label'=>'','placeholder'=>'Email',
			  		   'required'=>'required')); ?>
			  		   </div>
			  		    
		  		 </div>
		  		 
		  		 <div class="row">
			  		   <div class="large-4 columns">
			  		    <label for="institute_college">
			  		   <?php 
		  		echo __('Institute/College/School'); ?>
			  		   </label>
			  		   <?php echo $this->Form->input('institute_college',array('label'=>'','options'=>$institute_colleges,'required'=>'required')); ?>
			  		   </div>
			  		   
			  		   <div class="large-4 columns">
			  		    <label for="department">
			  		   <?php 
		  		echo __('Department'); ?>
			  		   </label>
			  		    <?php echo $this->Form->input('department',array('label'=>'','placeholder'=>'Department',
			  		    'required'=>'required')); ?>
			  		   </div>
			  		   
			  		    <div class="large-4 columns">
			  		    <label for="current_position">
			  		   <?php 
		  		echo __('Gradution Year Gregorian Calendar'); ?>
			  		   </label>
			  		   <?php echo $this->Form->input('gradution',array('label'=>'','id'=>'gradution','required'=>'required')); ?>
			  		   
			  		   </div>
			  		 
			  		    
		  		 </div>
		  		 
		  		  <div class="row">
			  		  
			  		   <div class="large-4 columns">
			  		    <label for="name_of_employer">
			  		   <?php 
		  		echo __('Name of employer'); ?>
			  		   </label>
			  		    <?php echo $this->Form->input('name_of_employer',array('label'=>'','placeholder'=>'Name of employer',
			  		    'required'=>'required')); ?>
			  		   </div>
			  		   
			  		    <div class="large-4 columns">
			  		    <label for="current_position">
			  		   <?php 
		  		echo __('Current Position'); ?>
			  		   </label>
			  		   <?php echo $this->Form->input('current_position',array('label'=>'','required'=>'required')); ?>
			  		   </div>
			  		   
			  		   
			  		   <div class="large-4 columns">
			  		    <label for="country">
			  		   <?php 
		  		echo __('Country'); ?>
			  		   </label>
			  		   <?php echo $this->Form->input('country',array('label'=>'','required'=>'required')); ?>
			  		   </div>
			  		   
			  		   <div class="large-4 columns">
			  		    <label for="city">
			  		   <?php 
		  		echo __('City'); ?>
			  		   </label>
			  		    <?php echo $this->Form->input('city',array('label'=>'','placeholder'=>'City',
			  		    'required'=>'required')); ?>
			  		   </div>
			  		   
		  		 </div>
		  		 
		  		 <div class="row">
			  		   
			  		   
		  		 </div>
		  		 
		  		 <div class="row">
		  		 	 <div class="large-12 columns">
			       <?php 
			       echo $this->Form->end(
array('label'=>__('Submit',true),'class'=>'tiny radius button bg-blue','name'=>'applyOnline'));
		
?>
					</div>
		  		 </div>
		  	 </div>
		  		
		  	<div>
	  	</div>
	  </div>
	</div>
</div>
<?php echo $this->Form->end();?>

<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
        
<div class="alumniMembers form">
<?php echo $this->Form->create('AlumniMember'); ?>
	<fieldset>
		<legend><?php echo __('Edit Alumni Member'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('trackingnumber');
		echo $this->Form->input('title');
		echo $this->Form->input('first_name');
		echo $this->Form->input('last_name');
		echo $this->Form->input('email');
		echo $this->Form->input('gender');
		echo $this->Form->input('date_of_birth');
		echo $this->Form->input('institute_college');
		echo $this->Form->input('department');
		echo $this->Form->input('program');
		echo $this->Form->input('country');
		echo $this->Form->input('city');
		echo $this->Form->input('current_position');
		echo $this->Form->input('name_of_employer');
		echo $this->Form->input('phone');
		echo $this->Form->input('work_telephone');
		echo $this->Form->input('home_telephone');
		echo $this->Form->input('remarks');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('AlumniMember.id')), array('confirm' => __('Are you sure you want to delete # %s?', $this->Form->value('AlumniMember.id')))); ?></li>
		<li><?php echo $this->Html->link(__('List Alumni Members'), array('action' => 'index')); ?></li>
	</ul>
</div>

</div>
</div>
</div>
</div>

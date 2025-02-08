<?php 
echo $this->Form->create('Student',array('action' => 'change'));
?>
<div class="box">
   <div class="box-body">
    <div class="row">
    	<div class="large-12 columns">
    		<div class="row">
    				<div class="large-6 columns">
    					<div class="row">
					       <div class="large-12 columns">
						    		<?php
									echo $this->Form->hidden('id');
									?>
									<h5>One Card Access!</h5>
									<p class="fs14">Access to our campuses and services requires ecardnumber.Check your ecardnumber on back of your card  on top right corner which is an 8 character length hexadecimal number or look at the picture below where you can find ecardnumber and fill the field below.<p>
									<img src="/img/ecardnumber_back.jpg" style="width:400px;height:270px;">
							</div>
							<div class="large-12 columns">
									<div class="row">
										<div class="large-12 columns">
										    <label for="ecardnumber">Ecardnumber (format: C1234564):</label>
										  
										</div>
										<div class="large-12 columns">
											 <?php echo $this->Form->input('ecardnumber',array('label'=>false,'type'=>"text",'id'=>'ecardnumber','required'=>true,
											 'style'=>'width:100px;')); ?>
										</div>
									</div>
							</div>
    					</div>
    					
    				</div>
    				<div class="large-6 columns">
    					<div class="row">
    					<div class="large-12 columns">
				<h5>Short code messaging is coming soon!</h5>
				<p class="fs14">You don't  need to go to department/registrar/accommodation/ICT to get information about your academic record. We made it simple for you,what you need to do is send us a text message to our short code message center:<strong>8761 or 8762 or 8763
				</strong> and request the following information instantly:
				<ul>
						<li>Reset Your SMIS Password (P)</li>
						<li>Request Grade(G)</li>
						<li>Get add/drop date(D)</li>
						<li>Academic Status(S)</li>
						<li>Dormitory(DR)</li>
				</ul>
				So to get all these service you need to update your active mobile phone number at SMIS.
			  <p>
		</div>
		<div class="large-12 columns">
				<div class="row">
					<div class="large-12 columns">
					    <label for="phone_mobile">Your Mobile(0920111111) </label>
					   <?php echo $this->Form->input('phone_mobile',array('label'=>false,'type'=>"text",'style'=>'width:100px;','id'=>'phone_mobile','required'=>true,
					   'pattern'=>"^\d{10}$", 'maxlength'=>"10")); ?>
					</div>
					
				</div>
		</div>
    					</div>
    				</div>
    		</div>
    		
    	</div>

	 
		 
		<div class="large-12 columns">
	
			<?php echo $this->Form->Submit('Update',array('class'=>'tiny radius button bg-blue')); ?> 
		</div>
    </div>
</div>
</div>
<?php 
 echo $this->Form->end();
?>
<?php echo $this->Form->create('Office');?>
<div class="box bg-white">
<!-- /.box-header -->
 <div class="box-body pad-forty" style="display: block;">
    <!-- basic form -->
    <div class="row">
	    <div class="large-12 columns">
		<h2><?php echo __('Add Clearance Office'); ?></h2>
	    </div>
            <div class="large-12 columns">
		
		<div class="row">
                       <div class="large-6 columns">
                          <label>
				<?php 
				echo $this->Form->input('staff_id',array('label'=>'Official'))
				?>
                          </label>
                        </div>
			<div class="large-6 columns">
                          <label>
				<?php 
				echo $this->Form->input('name');
				?>
                          </label>
                        </div>
                </div>

		<div class="row">
                       <div class="large-6 columns">
                          <label>
				<?php 
				echo $this->Form->input('telephone');
				?>
                          </label>
                        </div>
			<div class="large-6 columns">
                          <label>
				<?php 
			echo $this->Form->input('alternative_telephone');
				?>
                          </label>
                        </div>
                </div>

		<div class="row">
                       <div class="large-6 columns">
                          <label>
				<?php 
				echo $this->Form->input('email');
				?>
                          </label>
                        </div>
			<div class="large-6 columns">
                          <label>
				<?php 
			echo $this->Form->input('alternative_email');
				?>
                          </label>
                        </div>
                </div>
		
		<div class="row">
                       <div class="large-6 columns">
                          <label>
				<?php 
				echo $this->Form->input('address');
				?>
                          </label>
                        </div>
			
                </div>

	    </div>
      </div>
    </div>
  </div>
</div>
<?php echo $this->Form->end(__('Submit', true));?>

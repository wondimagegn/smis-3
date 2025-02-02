<?php echo $this->Form->create('Title');?>
<div class="box bg-white">
<!-- /.box-header -->
 <div class="box-body pad-forty" style="display: block;">
    <!-- basic form -->
    <div class="row">
    	<div class="large-12 columns">
		<h2><?php echo __('Add Title'); ?></h2>
	</div>
	<div class="large-12 columns">
		<div class="row">
                       <div class="large-12 columns">
                          <label>
				Title:
				<?php echo $this->Form->input('title',array('label' => false, 'after'=>'E.g Dr.,Ato,Mrs,Ms')); ?>

                          </label>
                        </div>
                </div>
		<div class="row">
			<div class="large-12 columns">
                          <label>
	Description:
				<?php echo $this->Form->input('description', array('label' => false)); ?>
                          </label>
                        </div>
		</div>
		<div class="row">
		 <div class="large-6 columns">
		<?php echo $this->Form->end(__('Submit'));?>
		 </div>
		</div>
	    </div>

    </div>
 </div>
</div>

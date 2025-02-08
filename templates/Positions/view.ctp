<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="positions view">
<h2><?php echo __('Position');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
	
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Position'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $position['Position']['position']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $position['Position']['description']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

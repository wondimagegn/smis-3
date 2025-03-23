<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            <div class="periodSettings view">
<h2><?php echo __('Period Setting');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $periodSetting['PeriodSetting']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('College'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($periodSetting['College']['name'], array('controller' => 'colleges', 'action' => 'view', $periodSetting['College']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Period'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $periodSetting['PeriodSetting']['period']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Hour'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Format->humanize_hour($periodSetting['PeriodSetting']['hour']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="related">
	<h3><?php echo __('Related Class Periods');?></h3>
	<?php if (!empty($periodSetting['ClassPeriod'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Period Setting Id'); ?></th>
		<th><?php echo __('College Id'); ?></th>
		<th><?php echo __('Program Type Id'); ?></th>
		<th><?php echo __('Program Id'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($periodSetting['ClassPeriod'] as $classPeriod):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $classPeriod['id'];?></td>
			<td><?php echo $classPeriod['period_setting_id'];?></td>
			<td><?php echo $classPeriod['college_id'];?></td>
			<td><?php echo $classPeriod['program_type_id'];?></td>
			<td><?php echo $classPeriod['program_id'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'class_periods', 'action' => 'view', $classPeriod['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'class_periods', 'action' => 'edit', $classPeriod['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'class_periods', 'action' => 'delete', $classPeriod['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $classPeriod['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

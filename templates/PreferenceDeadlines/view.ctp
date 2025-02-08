<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="preferenceDeadlines view">
<h2><?php echo __('Preference Deadline');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $preferenceDeadline['PreferenceDeadline']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Preference Deadline'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $preferenceDeadline['PreferenceDeadline']['preference_deadline']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Academicyear'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $preferenceDeadline['PreferenceDeadline']['academicyear']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $preferenceDeadline['PreferenceDeadline']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $preferenceDeadline['PreferenceDeadline']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>

<div class="related">
	<h3><?php echo __('Related Preferences');?></h3>
	<?php if (!empty($preferenceDeadline['Preference'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Accepted Student Id'); ?></th>
		<th><?php echo __('Academicyear'); ?></th>
		<th><?php echo __('Department Id'); ?></th>
		<th><?php echo __('Preferences Order'); ?></th>
		<th><?php echo __('Preference Deadline Id'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($preferenceDeadline['Preference'] as $preference):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $preference['id'];?></td>
			<td><?php echo $preference['accepted_student_id'];?></td>
			<td><?php echo $preference['academicyear'];?></td>
			<td><?php echo $preference['department_id'];?></td>
			<td><?php echo $preference['preferences_order'];?></td>
			<td><?php echo $preference['preference_deadline_id'];?></td>
			<td><?php echo $preference['created'];?></td>
			<td><?php echo $preference['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'preferences', 'action' => 'view', $preference['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'preferences', 'action' => 'edit', $preference['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('controller' => 'preferences', 'action' => 'delete', $preference['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $preference['id'])); ?>
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

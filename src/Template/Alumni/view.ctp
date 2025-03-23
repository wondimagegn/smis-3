<div class="alumni view">
<h2><?php echo __('Alumnus'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Student'); ?></dt>
		<dd>
			<?php echo $this->Html->link($alumnus['Student']['id'], array('controller' => 'students', 'action' => 'view', $alumnus['Student']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Full Name'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['full_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Father Name'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['father_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Region'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['region']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Woreda'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['woreda']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Kebele'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['kebele']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Housenumber'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['housenumber']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Mobile'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['mobile']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Home Second Phone'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['home_second_phone']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['email']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Studentnumber'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['studentnumber']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Sex'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['sex']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Placeofbirth'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['placeofbirth']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Fieldofstudy'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['fieldofstudy']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Age'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['age']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($alumnus['Alumnus']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Alumnus'), array('action' => 'edit', $alumnus['Alumnus']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Alumnus'), array('action' => 'delete', $alumnus['Alumnus']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $alumnus['Alumnus']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Alumni'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Alumnus'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Students'), array('controller' => 'students', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Student'), array('controller' => 'students', 'action' => 'add')); ?> </li>
	</ul>
</div>

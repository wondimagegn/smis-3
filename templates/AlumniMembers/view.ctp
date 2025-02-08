<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
     
     
<div class="alumniMembers view">
<h2><?php echo __('Alumni Member'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Trackingnumber'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['trackingnumber']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Title'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['title']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('First Name'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['first_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Last Name'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['last_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['email']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Gender'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['gender']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date Of Birth'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['date_of_birth']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Institute College'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['institute_college']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Department'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['department']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Program'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['program']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Country'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['country']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('City'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['city']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Current Position'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['current_position']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name Of Employer'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['name_of_employer']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Phone'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['phone']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Work Telephone'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['work_telephone']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Home Telephone'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['home_telephone']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Remarks'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['remarks']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($alumniMember['AlumniMember']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Alumni Member'), array('action' => 'edit', $alumniMember['AlumniMember']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Alumni Member'), array('action' => 'delete', $alumniMember['AlumniMember']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $alumniMember['AlumniMember']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Alumni Members'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Alumni Member'), array('action' => 'add')); ?> </li>
	</ul>
</div>

</div>
</div>
</div>
</div>

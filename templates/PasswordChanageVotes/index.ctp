<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="passwordChanageVotes index">
	<h2><?php echo __('Password Chanage Votes');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>
			<th><?php echo $this->Paginator->sort('role_id');?></th>
			<th><?php echo $this->Paginator->sort('is_voted');?></th>
			<th><?php echo $this->Paginator->sort('chanage_password_request_date');?></th>
			<th><?php echo $this->Paginator->sort('done');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($passwordChanageVotes as $passwordChanageVote):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $passwordChanageVote['PasswordChanageVote']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($passwordChanageVote['User']['id'], array('controller' => 'users', 'action' => 'view', $passwordChanageVote['User']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($passwordChanageVote['Role']['name'], array('controller' => 'roles', 'action' => 'view', $passwordChanageVote['Role']['id'])); ?>
		</td>
		<td><?php echo $passwordChanageVote['PasswordChanageVote']['is_voted']; ?>&nbsp;</td>
		<td><?php echo $passwordChanageVote['PasswordChanageVote']['chanage_password_request_date']; ?>&nbsp;</td>
		<td><?php echo $passwordChanageVote['PasswordChanageVote']['done']; ?>&nbsp;</td>
		<td><?php echo $passwordChanageVote['PasswordChanageVote']['created']; ?>&nbsp;</td>
		<td><?php echo $passwordChanageVote['PasswordChanageVote']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $passwordChanageVote['PasswordChanageVote']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $passwordChanageVote['PasswordChanageVote']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $passwordChanageVote['PasswordChanageVote']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $passwordChanageVote['PasswordChanageVote']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

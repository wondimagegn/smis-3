<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h2 class="box-title">
	 <?php echo __('View Sent Messages');?>
	 </h2>
     </div>
     <div class="box-body">
       <div class="row">
           <div class="large-12 columns">
		<p>
<?php
echo $this->Paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th>S.N<u>o</u></th>
	<th><?php echo $this->Paginator->sort('from');?></th>
	<th><?php echo $this->Paginator->sort('To','user_id');?></th>
	<th><?php echo $this->Paginator->sort('subject');?></th>
	<th><?php echo $this->Paginator->sort('created');?></th>
	<th><?php echo $this->Paginator->sort('modified');?></th>
</tr>
<?php
$i = 0;
$start = $this->Paginator->counter('%start%');
foreach ($messages as $message):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $start++; ?>
		</td>
		<td>
			<?php echo $message['Mailer']['from']; ?>
		</td>
		<td>
			<?php echo $message['Mailer']['user_id']; ?>
		</td>
		<td>
			<?php echo $message['Mailer']['subject']; ?>
		</td>
		<!--
		<td>
        <?php echo $this->Html->link($message['Mailer']['model']." ".$message['Mailer']['foreign_key'], array('controller' =>
                    Inflector::tableize($message['Mailer']['model']),
                    'action' => 'view',
                    $message['Mailer']['foreign_key'])
            ); ?>
		</td>
		-->
		<td>
			<?php echo $message['Mailer']['created']; ?>
		</td>
		<td>
			<?php echo $message['Mailer']['modified']; ?>
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
	   </div>
       </div>
     </div>
</div>


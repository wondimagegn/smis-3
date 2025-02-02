<?php ?>
<style>
table.small_padding tr td {
padding:2px
}
</style>
<div class="box"> 
     <div class="box-body">
       <div class="row">    
<?php 
     echo $this->Form->create('Log');
?>
	    <div class="large-12 columns">
              <h5 class="box-title">
                
<?php echo __('View Logs');?>
              </h5>
              
	<table class="fs13">
		<tr>
			<td style="width:5%"> From:</td>
			<td style="width:45%"><?php echo $this->Form->input('change_date_from', array('label' => false, 'type' => 'datetime','style'=>'width:50px', 'dateFormat' => 'MDY', 'minYear' => Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc', 'selected' => array('year' => (isset($this->request->data['Log']['change_date_from']) ? $this->request->data['Log']['change_date_from']['year'] : date('Y')), 'month' => (isset($this->request->data['Log']['change_date_from']) ? $this->request->data['Log']['change_date_from']['month'] : date('m')), 'day' => (isset($this->request->data['Log']['change_date_from']) ? $this->request->data['Log']['change_date_from']['day'] : date('d')-14)))); ?></td>
			<td style="width:5%"> To:</td>
			<td style="width:45%"><?php echo $this->Form->input('change_date_to', array('label' => false, 'type' => 'datetime','style'=>'width:50px', 'dateFormat' => 'MDY', 'minYear' => Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc')); ?></td>
		</tr>
		<tr>
			<td>Role:</td>
			<td><?php echo $this->Form->input('role_id',
			array('label'=>false, 'style' => 'width:373px')); ?></td>
			<td>User:</td>
			<td><?php echo $this->Form->input('username', array('label'=>false, 'style' => 'width:370px')); ?></td>
		</tr>
		
		<tr>
			<td colspan="4"><?php echo $this->Form->submit(__('View logs'), array(
			'div' => false)); ?></td>
		</tr>
	</table>
		
            </div>
	   <div class="large-12 columns">
                <?php 
    if (!empty($logs)) {
     
?>
	<p class="fs15"><?php echo __('List of logs based on the above given condition/s');?></p>
	<table cellpadding="0" cellspacing="0" style="table-layout:fixed">
	<tr>
			<th style="width:3%">N<u>o</u></th>
			<th style="width:8%"><?php echo $this->Paginator->sort('Key');?></th>
			<th style="width:13%"><?php echo $this->Paginator->sort('user_id');?></th>		
			<th style="width:8%"><?php echo $this->Paginator->sort('ip');?></th>
			<th style="width:15%"><?php echo $this->Paginator->sort('model');?></th>
			<th style="width:8%"><?php echo $this->Paginator->sort('action');?></th>
			<th style="width:12%"><?php echo $this->Paginator->sort('description'); ?></th>
			<th style="width:24%"><?php echo $this->Paginator->sort('change'); ?></th>
		   <th style="width:10%"><?php echo $this->Paginator->sort('Date','created');?></th>
	</tr>
	<?php
	$i = 0;
	$start=$this->Paginator->counter('%start%');
       
	foreach ($logs as $log):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++ ; ?>&nbsp;</td>
		<td><?php echo $log['Log']['foreign_key']; ?> </td>
		<td>
		<?php
                if(!empty($log['User']['first_name'])) {
                   echo $this->Html->link($log['User']['first_name'].' '.$log['User']['middle_name'].' '.$log['User']['last_name'].' ('.$log['User']['username'].')', array('controller' => 'users', 'action' => 'view', $log['User']['id']));
		} else {
                     echo $this->Html->link($log['User']['username'], array('controller' => 'users', 'action' => 'view', $log['User']['id']));
		}
	       
                ?>
		</td>
		<td><?php echo $log['Log']['ip']; ?>&nbsp;</td>
		<td><?php echo $log['Log']['model']; ?>&nbsp;</td>
		<td><?php echo $log['Log']['action']; ?>&nbsp;</td>
		<td><?php echo $log['Log']['description']; ?>&nbsp;</td>
		<td><?php echo strip_tags($log['Log']['change']); ?>&nbsp;</td>
		<td><?php echo $this->Format->humanize_date_short2($log['Log']['created']); ?>&nbsp;</td>
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
<?php 
    }
?>
	   </div>
</div>
</div>
</div>

<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
              
<?php echo $this->Form->create('CostSharingPayment');?>
<div class="costSharingPayments index">
    <?php 
        echo '<div class="smallheading">';
        echo 'Search Student Payment';
        echo '</div>';
        echo '<table class="fs13 small_padding">';	
        ?>
    
		<tr>
			<td style="width:13%">Reference Number:</td>
			<td style="width:37%"><?php echo $this->Form->input('reference_number',array('label'=>false));?></td>
           
		</tr>
		<tr>
		 <td style="width:13%">Student Number:</td>
		 <td style="width:37%">
		   <?php 
		           echo $this->Form->input('studentnumber',array('label'=>false));
			?>
		</td>
		</tr>
		<tr>
		<?php 
		    echo '<td colspan=2>'.$this->Form->submit('Search',
		    array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>'false')).'</td>';  
		?>
		</tr>
		</table>


	<h2><?php echo __('Cost Sharing Payments');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('reference_number');?></th>
			<th><?php echo $this->Paginator->sort('amount');?></th>
			<th><?php echo $this->Paginator->sort('payment_type');?></th>
			<th><?php echo $this->Paginator->sort('Student Full Name ');?></th>
		
			<th class="actions" style='align:left'><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($costSharingPayments as $costSharingPayment):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td><?php echo $costSharingPayment['CostSharingPayment']['reference_number']; ?>&nbsp;</td>
		<td><?php echo $costSharingPayment['CostSharingPayment']['amount']; ?>&nbsp;</td>
		<td><?php echo $costSharingPayment['CostSharingPayment']['payment_type']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($costSharingPayment['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $costSharingPayment['Student']['id'])); ?>
		</td>
		
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $costSharingPayment['CostSharingPayment']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $costSharingPayment['CostSharingPayment']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $costSharingPayment['CostSharingPayment']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $costSharingPayment['CostSharingPayment']['id'])); ?>
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

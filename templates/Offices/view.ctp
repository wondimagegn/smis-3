<div class="offices view">
<h2><?php echo __('Office');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
	
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $office['Office']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $office['Office']['address']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Telephone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $office['Office']['telephone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Alternative Telephone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $office['Office']['alternative_telephone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Email'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $office['Office']['email']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Alternative Email'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $office['Office']['alternative_email']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $office['Office']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $office['Office']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>

<div class="related">
	<div class="smallheading"><?php echo __('Related Taken Properties');?></div>
   
	<?php if (!empty($office['TakenProperty'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th>S.N<u>o</u></th>
		
		<th><?php echo __('Student '); ?></th>
		<th><?php echo __('Office '); ?></th>
		<th><?php echo __('Item Name'); ?></th>
		<th><?php echo __('Taken Date'); ?></th>
		<th><?php echo __('Returned'); ?></th>
		<th><?php echo __('Return Date'); ?></th>
		<th><?php echo __('Remark'); ?></th>
		
	</tr>
	<?php
		$i = 0;
		$start=1;
		foreach ($office['TakenProperty'] as $takenProperty):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $start++;?></td>
			
			<td><?php echo $takenProperty['Student']['full_name'];?></td>
			<td><?php echo $takenProperty['Office']['name'];?></td>
			<td><?php echo $takenProperty['name'];?></td>
			<td><?php echo $takenProperty['taken_date'];?></td>
			<td><?php 
			        if ($takenProperty['returned']==1) {
			            echo 'Yes';
			        } else {
			            echo 'No';
			        }
			       
			    
			    ?></td>
			<td><?php echo $takenProperty['return_date'];?></td>
			<td><?php echo $takenProperty['remark'];?></td>
			
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

</div>

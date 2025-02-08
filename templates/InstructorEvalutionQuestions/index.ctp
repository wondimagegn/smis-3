<?php echo $this->Form->create('InstructorEvalutionQuestion');?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  	<div class="large-12 columns">
	  	  <h2><?php echo __('Instructor Evalution Questions'); ?></h2>
	 <table cellpadding="0" cellspacing="0">
	<?php 
		echo "<tr><td width='50%'>".$this->Form->input('for')."</td><td>".$this->Form->input('type')."</td></tr>";

		echo '<tr><td colspan=2>'.$this->Form->submit(__('Search'), array('div' => false,'class'=>'tiny radius button bg-blue','name'=>'search')).'</td></tr>'; 
	?>
	</table>

	  <table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id','No.'); ?></th>
			<th><?php echo $this->Paginator->sort('question'); ?></th>
			<th><?php echo $this->Paginator->sort('question_amharic'); ?></th>
			<th><?php echo $this->Paginator->sort('type'); ?></th>
			<th><?php echo $this->Paginator->sort('for'); ?></th>
			<th><?php echo $this->Paginator->sort('active'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
			
	</tr>
	</thead>
	<tbody>
	<?php 
	$start = $this->Paginator->counter('%start%');
	?>
	<?php foreach ($instructorEvalutionQuestions as $instructorEvalutionQuestion): ?>
	<tr>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td><?php echo h($instructorEvalutionQuestion['InstructorEvalutionQuestion']['question']); ?>&nbsp;</td>
		<td><?php echo h($instructorEvalutionQuestion['InstructorEvalutionQuestion']['question_amharic']); ?>&nbsp;</td>
		
		<td><?php echo h($instructorEvalutionQuestion['InstructorEvalutionQuestion']['type']); ?>&nbsp;</td>
       <td><?php echo h($instructorEvalutionQuestion['InstructorEvalutionQuestion']['for']); ?>&nbsp;</td>
       
        <td><?php echo $instructorEvalutionQuestion['InstructorEvalutionQuestion']['active']==1 ? 'Yes':'No'; ?>&nbsp;</td>
        
		<td class="actions">
			<?php 
			
			echo $this->Html->link(__('Edit'), array('action' => 'edit', $instructorEvalutionQuestion['InstructorEvalutionQuestion']['id'])); 
			echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $instructorEvalutionQuestion['InstructorEvalutionQuestion']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $instructorEvalutionQuestion['InstructorEvalutionQuestion']['id']))); 
           
			?>
		</td>
		
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
		'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="pagination-centered">
	<ul class="pagination">
	<?php
		echo $this->Paginator->prev('<< ' . __(''), array('tag'=>'li'), null, array('class' => 'arrow unavailable '));
		echo $this->Paginator->numbers(array('separator' => '','tag'=>'li'));
		echo $this->Paginator->next(__('') . ' >>', array('tag'=>'li'), null, array('class' => 'arrow  unavailable'));
	?>
	</ul>
	</div>

	  	</div>
	  </div>
	 </div>
</div>	
<?php echo $this->Form->end();?>

<div class="row">
<div class="large-12 columns">

<?php echo $this->Form->create('Curriculum',array('action'=>'approve', "method"=>"POST"));
	
echo '<h6>Approve Curriculum </h6>';

echo $this->Form->hidden('Curriculum.id',array('value'=>$staff_profile['Staff']['id']));

echo $this->Form->input('Curriculum.registrar_approved',array('label'=>'Approve Curriculum'));
echo $this->Form->end('Update');


?>
	
</div>
</div>
<a class="close-reveal-modal">&#215;</a>

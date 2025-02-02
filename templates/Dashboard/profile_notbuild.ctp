<?php
//Profile not complete 
if (isset($profile_not_build) && !empty($profile_not_build)) {
	$counter++ ?>
	<?php
	if (count($profile_not_build) > 0) { ?>
		<p style="font-size:12px">There are <?= $this->Html->link(__(count($profile_not_build), true), array('controller' => 'students', 'action' => 'profile_not_build_list')); ?> students profile not build .</p>
		<?php
	} ?>
	<div class="utils">
		<?= $this->Html->link(__('View All', true), array('controller' => 'students', 'action' => 'profile_not_build_list'), array('class' => 'tiny radius button bg-blue')); ?>
	</div>
	<?php
} ?>
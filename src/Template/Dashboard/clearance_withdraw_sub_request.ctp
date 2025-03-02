<?php
//Clearance/Withdraw Request
if (isset($clearance_request) && !empty($clearance_request)) { ?>
	<h6 class="box-title">
		<i class="fontello-folder"></i><span>Clearnce/Withdraw</span>
	</h6>
	<?php
	if ($clearance_request == 0) { ?>
		<p>There is no Clearance/Withdraw requests.</p>
		<?php
	} else {
		if ($clearance_request != 0) { ?>
			<p style="font-size:12px"><?= $this->Html->link(__('You have ' . $clearance_request . ' Clearance/Withdraw request that needs your approval', true), array('controller' => 'clearances',  'action' => 'approve_clearance'), array('class' => 'action_link')); ?> </p>
			<?php
		}
	} ?>
	<a href="/clearances/approve_clearance" class="tiny radius button bg-blue">View All</a>

	<?php
} ?>

<?php
//Course Exemption Request
if (isset($exemption_request) && !empty($exemption_request)) { ?>
	<h6 class="box-title">
		<i class="fontello-folder"></i><span>Exemption Request</span>
	</h6>
	<table class="small_padding">
		<?php
		if ($exemption_request == 0) { ?>
			<tr>
				<td style="border:0px solid #ffffff" colspan="2"><p style="font-size:12px">There is no Course Exemption requests.</p></td>
			</tr>
			<?php
		} else { 
			if ($exemption_request != 0) { ?>
				<tr>
					<td style="border:0px solid #ffffff" colspan="2"><p style="font-size:12px"><?= $this->Html->link(__('You have ' . $clearance_request . ' exemption request that needs your approval', true), array('controller' => 'courseExemptions',  'action' => 'list_exemption_request'), array('class' => 'action_link')); ?></p></td>
				</tr>
				<?php
			}
		} ?>
	</table>
	<?php 
} ?>
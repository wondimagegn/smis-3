<?php
//Add/Drop
if ((isset($add_request) && !empty($add_request)) || (isset($forced_drops) && !empty($forced_drops)) || (isset($drop_request) && !empty($drop_request))) { ?>
	<table class="small_padding">
		<?php
		if ($role_id == ROLE_REGISTRAR) {
			if (empty($forced_drops['count'])) { ?>
				<tr>
					<td style="border:0px solid #ffffff"><p style="font-size:12px">There is no course that needs force drop.</p></td>
				</tr>
				<?php
			} else { ?>
				<tr>
					<td class="action_content">
						<?php
						if ($forced_drops['count'] > 0) {
							echo $this->Html->link(__('You have ' . $forced_drops['count'] . ' students who are registered on hold base but failed to qualify.', true), array('controller' => 'courseDrops', 'action' => 'forced_drop'), array('class' => 'action_link'));
						} ?>
					</td>
				</tr>
				<?php
			}
		}

		if (empty($add_request)) {
			if ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_COLLEGE) { ?>
				<tr>
					<td style="border:0px solid #ffffff"><p style="font-size:12px">There is no add course request that needs approval.</p></td>
				</tr>
				<?php
			} else { ?>
				<tr>
					<td style="border:0px solid #ffffff"><p style="font-size:12px">There is no add course request that needs confirmation.</p></td>
				</tr>
				<?php
			}
		} else { ?>
			<tr>
				<td class="action_content">
					<?php
					if ($role_id == ROLE_REGISTRAR) {
						if ($add_request > 0) {
							echo $this->Html->link(__('You have ' . $add_request . ' students whose add request is approved by department/college and waiting confirmation .', true), array('controller' => 'course_adds',  'action' => 'approve_adds'), array('class' => 'action_link'));
						}
					} else {
						echo $this->Html->link(__('You have ' . $add_request . ' students  add request  waiting approval.', true), array('controller' => 'course_adds',  'action' => 'approve_adds'), array('class' => 'action_link'));
					} ?>
				</td>
			</tr>
			<?php
		}
		
		if (empty($drop_request)) {
			if ($role_id == ROLE_DEPARTMENT) { ?>
				<tr>
					<td style="border:0px solid #ffffff"><p style="font-size:12px">There is no course drop request that needs approval.</p></td>
				</tr>
				<?php
			} else { ?>
				<tr>
					<td style="border:0px solid #ffffff"><p style="font-size:12px">There is no course drop request that needs confirmation.</p></td>
				</tr>
				<?php
			}
		} else { ?>
			<tr>
				<td class="action_content">
					<?php
					if ($role_id == ROLE_REGISTRAR) {
						if ($drop_request > 0) {
							echo $this->Html->link(__('You have ' . $drop_request . ' students whose drop request is approved by department/college and waiting confirmation .', true), array('controller' => 'course_drops',  'action' => 'approve_drops'), array('class' => 'action_link'));
						}
					} else {
						echo $this->Html->link(__('You have ' . $drop_request . ' students  drop request  waiting for approval.', true), array('controller' => 'course_drops',  'action' => 'approve_drops'), array('class' => 'action_link'));
					} ?>
				</td>
			</tr>
			<?php
		} ?>
	</table>
	<?php
} ?>
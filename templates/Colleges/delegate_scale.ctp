<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Delegate Grade Scale'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="colleges form">
					<?php echo $this->Form->create('College');
					if (!empty($this->request->data)) { ?>
						<div style='padding-top:10px' class="smallheading">
							<?php echo __('Delegate scale setting for all departments of your college.'); ?>
						</div>

						<div style='padding-top:10px;padding-bottom:10px;font-size:15px'>
							<strong>Campus:<?php echo $this->request->data['Campus']['name'] ?></strong><br />
							<strong>College:<?php echo $this->request->data['College']['name'] ?></strong><br />
							<strong><?php echo __('Delegatation of scale setting will apply for the department listed below.'); ?></strong>
						</div>

						<table>
							<tr>
								<td>
									<table>
										<?php
										echo $this->Form->hidden('id', array('value' => $this->request->data['College']['id']));
										if (!empty($this->request->data)) {
											foreach ($this->request->data['Department'] as $department_id => $department_name) {
												echo "<tr><td>" . $department_name['name'] . "</td></tr>";
											}
										} ?>
									</table>
								</td>
								<td style='vertical-align:top;'>
									<?php
									echo '<table>';
									echo "<tr><td>" . $this->Form->input('deligate_scale', array('after' => 'Delegate undergraduate grade scale.', 'class' => 'fs16', 'label' => false)) . "</td></tr>";
									echo "<tr><td>" . $this->Form->input('deligate_for_graduate_study', array('after' => 'Delegate post graduate grade scale.', 'class' => 'fs16', 'label' => false)) . "</td></tr>";
									echo "<tr><td>" . $this->Form->end(array('label' => __('Update'), 'class' => 'tiny radius button bg-blue')) . "</td></tr>";
									echo '</table>';
									?>
								</td>
							</tr>
						</table>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
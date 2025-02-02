<?php
if (!empty($clearances)) { ?>
	<div class="fs16 smallheading" style="margin-bottom: 10px;"><span>Clearance History: </span></div>
	<table cellpadding="0" cellspacing="0" class="table-borderless" style="width:50%">
		<thead>
			<tr>
				<td style="text-align: center;width:40%">Requested Date</td>
				<td style="text-align: center;width:60%">Accepted Date</td>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($clearances as $clearance) { ?>
				<tr>
					<td style="text-align: center;"><?= $this->Time->format("M j, Y", $clearance['Clearance']['request_date'], NULL, NULL); ?></td>
					<td style="text-align: center;"><?= $this->Time->format("M j, Y", $clearance['Clearance']['acceptance_date'], NULL, NULL); ?></td>
				</tr>
				<?php
			} ?>
		</tbody>
	</table>
	<?php
} else { ?>
	<div class='warning-box warning-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>The student does not have any clearance.</div>
	<?php
} ?>
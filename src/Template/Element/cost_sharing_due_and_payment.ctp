	<?php
	$currency = !empty(Configure::read('SMIScurrency')) && strcasecmp(Configure::read('SMIScurrency'), '&ETB;') == 0 ? 'ETB: ' : '$ ';
	$formatOptions = array('places' => 2,  'before' => false, 'decimals' => '.',  'thousands' => ',' );
	$formatOptionsForCurrency = array('places' => 2, 'before' => 'ETB: ', 'escape' => false, 'decimals' => '.',  'thousands' => ',' );

	if(!empty($costShares)) { ?>
		<div class="fs16 smallheading" style="margin-bottom: 10px;"><span>Cost sharing dues: </span></div>
		<table cellpadding="0" cellspacing="0" class="table-borderless">
			<thead>
				<tr>
					<td style="text-align: center;">Academic Year</td>
					<td style="text-align: center;">Education</td>
					<td style="text-align: center;">Accomodation</td>
					<td style="text-align: center;">Meal Service</td>
					<td style="text-align: center;">Medical</td>
					<td style="text-align: center;">Total</td>
					<td style="text-align: center;">Sign Date</td>
				</tr>
			</thead>
			<tbody>
				<?php
				$total_due = 0;
				foreach ($costShares as $costShare) { ?>
					<tr>
						<td style="text-align: center;"><?= $costShare['CostShare']['academic_year']; ?></td>
						<td style="text-align: center;"><?= $this->Number->format($costShare['CostShare']['education_fee'], $formatOptions); ?></td>
						<td style="text-align: center;"><?= $this->Number->format($costShare['CostShare']['accomodation_fee'], $formatOptions); ?></td>
						<td style="text-align: center;"><?= $this->Number->format($costShare['CostShare']['cafeteria_fee'], $formatOptions); ?></td>
						<td style="text-align: center;"><?= $this->Number->format($costShare['CostShare']['medical_fee'], $formatOptions); ?></td>
						<td style="text-align: center;">
							<?php
							$year_total = ($costShare['CostShare']['education_fee'] + $costShare['CostShare']['accomodation_fee'] + $costShare['CostShare']['cafeteria_fee'] + $costShare['CostShare']['medical_fee']);
							$total_due += $year_total;
							echo $this->Number->format($year_total , $formatOptions); ?>
						</td>
						<td style="text-align: center;"><?= $this->Time->format("M j, Y", $costShare['CostShare']['cost_sharing_sign_date'], NULL, NULL); ?></td>
					</tr>
					<?php
				} ?>
				<tr>
					<td style="font-weight:bold; text-align: right" colspan="5">TOTAL <?= $currency; ?></td>
					<td style="font-weight:bold; text-align: center;"><?= $this->Number->format($total_due, $formatOptions); ?></td>
					<td></td>
				</tr>
			</tbody>
		</table>

		<?php
		if (!empty($costSharingPayments)) { ?>
			<div class="fs16 smallheading" style="margin-bottom: 10px;"><span>Cost sharing payments settled: </span></div>
			<table cellpadding="0" cellspacing="0" class="table-borderless">
				<thead>
					<tr>
						<td style="width:30%">Reference Number</td>
						<td style="width:30%">Payment Amount</td>
						<td style="width:40%">Payment Type</td>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($costSharingPayments as $costSharingPayment) { ?>
						<tr>
							<td><?= $costSharingPayment['CostSharingPayment']['reference_number']; ?></td>
							<td><?= $this->Number->currency($costSharingPayment['CostSharingPayment']['amount'], $currency) ; ?></td>
							<td><?= $costSharingPayment['CostSharingPayment']['payment_type']; ?></td>
						</tr>
						<?php
					} ?>
				</tbody>
			</table>
			<?php
		} else { ?>
			<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no recorded cost sharing payments.</div>
			<?php
		}
	} else { ?>
		<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no recorded cost sharing dues.</div>
		<?php
	} ?>

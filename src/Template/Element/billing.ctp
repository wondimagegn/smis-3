<?php
	$currency = !empty(Configure::read('SMIScurrency')) && strcasecmp(Configure::read('SMIScurrency'), '&ETB;') == 0 ? 'ETB: ' : '$ ';
	$formatOptions = array('places' => 2,  'before' => false, 'decimals' => '.',  'thousands' => ',' );
	$formatOptionsForCurrency = array('places' => 2, 'before' => 'ETB: ', 'escape' => false, 'decimals' => '.',  'thousands' => ',' );

	if (!empty($student_academic_profile['CostShare'])) { ?>
		<div class="fs16 smallheading" style="margin-bottom: 10px;"><span>Cost sharing dues </span></div>
		<table cellpadding="0" cellspacing="0" class="table">
			<thead>
				<tr>
					<td class="center">ACY</td>
					<td class="center">Sharing Cycle</td>
					<td class="center">Education</td>
					<td class="center">Accomodation</td>
					<td class="center">Meal Service</td>
					<td class="center">Medical</td>
					<td class="center">Total</td>
					<td class="center">Sign Date</td>
				</tr>
			</thead>
			<tbody>
				<?php
				$total_due = 0;
				foreach ($student_academic_profile['CostShare'] as $ks=> $costShare) { ?>
					<tr>
						<td class="center"><?= $costShare['academic_year']; ?></td>
						<td class="center"><?= $costShare['sharing_cycle']; ?></td>
						<td class="center"><?= $this->Number->format($costShare['education_fee'] , $formatOptions); ?></td>
						<td class="center"><?= $this->Number->format($costShare['accomodation_fee'] , $formatOptions); ?></td>
						<td class="center"><?= $this->Number->format($costShare['cafeteria_fee'] , $formatOptions); ?></td>
						<td class="center"><?= $this->Number->format($costShare['medical_fee'] , $formatOptions); ?></td>
						<td class="center"><?php 
							$year_total = ($costShare['education_fee']+$costShare['accomodation_fee']+$costShare['cafeteria_fee']+$costShare['medical_fee']);
							$total_due += $year_total;
							echo $this->Number->format($year_total , $formatOptions);
						?></td>
						<td class="center"><?= $this->Time->format("M j, Y", $costShare['cost_sharing_sign_date'], NULL, NULL); ?></td>
					</tr>
					<?php
				} ?>
					<tr>
						<td style="font-weight:bold; text-align:right" colspan="6">TOTAL <?= $currency; ?></td>
						<td style="font-weight:bold"><?= $this->Number->format($total_due , $formatOptions); ?></td>
						<td></td>
					</tr>
			</tbody>
		</table>
		<hr>

		<?php
		if (!empty($student_academic_profile['CostSharingPayment'])) { ?>
			<div class="fs16 smallheading" style="margin-bottom: 10px;"><span>Cost sharing payments settled: </span></div>
			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<td class="center" style="width:30%;">Reference Number</td>
						<td class="center" style="width:30%;">Payment Amount</td>
						<td class="center" style="width:40%;">Payment Type</td>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($student_academic_profile['CostSharingPayment'] as $cshP => $costSharingPayment) { ?>
						<tr>
							<td class="center"><?= $costSharingPayment['reference_number']; ?></td>
							<td class="center"><?= $this->Number->currency($costSharingPayment['amount'], $currency) ; ?></td>
							<td class="center"><?= $costSharingPayment['payment_type']; ?></td>
						</tr>
						<?php
					} ?>
				</tbody>
			</table>
			<hr>
			<?php
		} else { ?>
			<div class="warning-box warning-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no cost sharing payment settled so far for the selected student.</div>
			<?php
		}
	} else if (!empty($student_academic_profile['ApplicablePayment'])) { ?>
		<div class="fs16 smallheading" style="margin-bottom: 10px;"><span>Applicable Payment Due</span></div>
		<table cellpadding="0" cellspacing="0" class="table">
			<thead>
				<tr>
					<td class="center">ACY</td>
					<td class="center">Semester</td>
					<td class="center">Tutition</td>
					<td class="center">Accomodation</td>
					<td class="center">Meal Service</td>
					<td class="center">Medical</td>
					<td class="center">Sponsor Type</td>
					<th class="center">Sponsor Name</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$total_due = 0;
				foreach ($student_academic_profile['ApplicablePayment'] as $app => $applicablePayment) { ?>
					<tr>
						<td class="center"><?= $applicablePayment['academic_year']; ?></td>
						<td class="center"><?= $applicablePayment['semester']; ?></td>
						<td class="center"><?= $applicablePayment['tutition_fee']==1 ? "Yes":"No" ?></td>
						<td class="center"><?= $applicablePayment['accomodation']==1 ? "Yes":"No" ?></td>
						<td class="center"><?= $applicablePayment['meal']==1 ? "Yes":"No" ?></td>
						<td class="center"><?= $applicablePayment['health']==1 ? "Yes":"No" ?></td>
						<td class="center"><?= $applicablePayment['sponsor_type'];?></td>
						<td class="center"><?= $applicablePayment['sponsor_name'];?></td>
					</tr>
					<?php
				} ?>
			</tbody>
		</table>
		<hr>
		<?php
		if (!empty($student_academic_profile['Payment'])) { ?>
			<div class="fs16 smallheading" style="margin-bottom: 10px;"><span>Settled Payment Details: </span></div>
			<table cellpadding="0" cellspacing="0" class="table">
				<thead>
					<tr>
						<td class="center" style="width:15%;">ACY</td>
						<td class="center" style="width:15%;">Semester</td>
						<td class="center" style="width:30%;">Reference Number</td>
						<td class="center" style="width:30%;">Payment Amount</td>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($student_academic_profile['Payment'] as $pk=>$payment) { ?>
						<tr>
							<td class="center"><?= $payment['academic_year']; ?></td>
							<td class="center"><?= $payment['semester']; ?></td>
							<td class="center"><?= $payment['reference_number']; ?></td>
							<td class="center"><?= $this->Number->currency($payment['fee_amount'], $currency); ?></td>
						</tr>
						<?php
					} ?>
				</tbody>
			</table>
			<hr>
			<?php 
		} else {  ?>
			<div class="warning-box warning-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no recorded payment settled so far for the selected student.</div>
			<?php
		} 
	} else { ?>
		<div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no payment dues recorded for the selected student.</div>
		<?php
	}
?>
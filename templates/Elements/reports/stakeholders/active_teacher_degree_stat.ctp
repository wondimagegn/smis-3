
<?php 
if (isset($getActiveTeacherByDegree['teachersStatisticsByDegree']) && !empty($getActiveTeacherByDegree['teachersStatisticsByDegree'])) {
    debug($getActiveTeacherByDegree); ?>
    <!-- <p class="fs16"><?php //echo $headerLabel; ?></p> -->
	<div style="overflow-x:auto;">
		<table cellpadding="0" cellspacing="0" class="table">
			<tr>
				<th rowspan="2" class="center" style="width:2%">#</th>
				<th rowspan="2" class="center" style="width:15%">College/School/Center</th>
				<th rowspan="2" class="center" style="width:8%">Department</th>
				<th rowspan="2" class="center" style="width:8%; border-right:2px #000000 solid;">Gender</th>
				<?php
				if (isset($educations) && !empty($educations)) {
					foreach ($educations as $k=>$value) { ?>
						<th colspan="3" class="center" style="border-right:2px #000000 solid;"><?= $value;?></th>
						<?php 
					} 
				} ?>
			</tr>
			<tr>
				<?php 
				if (isset($educations) && !empty($educations)) {
					foreach ($educations as $k=>$value) { ?>
						<th style="width:5%" class="center">Ethiopian</th>
						<th style="width:5%" class="center">Expatriate</th>
						<th style="width:5%; border-right:2px #000000 solid;" class="center">Total</th>
						<?php 
					} 
				} ?>
			</tr>
			<?php 
			$count = 0;
			foreach ($getActiveTeacherByDegree['teachersStatisticsByDegree'] as $college => $departmentList) { ?>
				<tr>
					<td class="center" rowspan="<?=  $getActiveTeacherByDegree['collegeRowSpan'][$college]+count($departmentList)+1;?>"><?= ++$count;?></td>
					<td class="vcenter" rowspan="<?=  $getActiveTeacherByDegree['collegeRowSpan'][$college]+count($departmentList)+1;?>"><?= $college;?></td>
				</tr>
				<?php 
				foreach($departmentList as $deptname => $genderList) { ?>
					<tr>
						<td class="vcenter" rowspan="<?= count($genderList)+2 ?>"><?= $deptname; ?></td>
					</tr>
					<?php 
					$sumByDegree = array();
					foreach ($genderList as $gk => $degreelist) { ?>
						<tr>
							<td class="center" style="border-left:2px #000000 solid;border-right:2px #000000 solid;"><?= ucwords($gk); ?></td>
							<?php
							foreach ($degreelist as $pk => $ppv) { 

								$sumByDegree[$pk]['Ethiopian'] += $ppv['Ethiopian'];
								$sumByDegree[$pk]['Foreigner'] += $ppv['Foreigner']; ?>

								<td class="center"><?= $ppv['Ethiopian']; ?></td>
								<td class="center"><?= $ppv['Foreigner']; ?></td>
								<td class="center" style="border-right:2px #000000 solid;font-weight: bold;"><?= $ppv['Foreigner'] + $ppv['Ethiopian']; ?></td>
								<?php 
							} ?>
						</tr>
						<?php 
					} ?>
					<tr>
						<td class="center" style="border-left:2px #000000 solid;border-right:2px #000000 solid; font-weight: bold;">Total</td>
						<?php 
						debug($sumByDegree);
						foreach ($sumByDegree as $d => $dv) { ?>
							<td class="center"><?= $dv['Ethiopian']; ?></td>
							<td class="center"><?= $dv['Foreigner']; ?></td>
							<td class="center" style="border-right:2px #000000 solid; font-weight: bold;"><?= $dv['Foreigner']+$dv['Ethiopian']; ?></td>
							<?php
						} ?>
					</tr>
					<?php 
				} 
			} ?>
		</table>
	</div>
	<?php 
  } ?>
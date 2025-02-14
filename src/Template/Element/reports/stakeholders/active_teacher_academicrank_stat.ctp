<?php 
if (isset($getActiveTeacherByAcademicRank['teachersStatisticsByAcademicRank']) && !empty($getActiveTeacherByAcademicRank['teachersStatisticsByAcademicRank'])) { ?>
    <!-- <p class="fs16"><?= $headerLabel; ?></p> -->
	<div style="overflow-x:auto;">
		<table cellpadding="0" cellspacing="0" class="table">
			<tr>
				<th rowspan="3" class="center" style="width:2%">#</th>
				<th rowspan="3" class="center" style="width:15%">College/School/Center</th>
				<th rowspan="3" class="center" style="width:8%">Department</th>
				<th rowspan="3" class="center" style="width:8%;border-right:2px #000000 solid;">Degree</th>
				<th colspan="<?= count($positions)*3;?>"  class="center" style="border-right:2px #000000 solid;">Academic Rank</th>
			</tr>
			<tr>
				<?php
				if (isset($positions) && !empty($positions)) {
					foreach ($positions as $k=>$value) { ?>
						<th colspan="3"  class="vcenter" style="border-right:2px #000000 solid;"><?= $value;?></th>
						<?php 
					} 
				} ?>
			</tr>
			<tr>
				<?php 
				if (isset($positions) && !empty($positions)) {
					foreach ($positions as $k=>$value) { ?>
						<th style="width:5%" class="center">Male</th>
						<th style="width:5%" class="center">Female</th>
						<th style="width:5%;border-right:2px #000000 solid;font-weight: bold;" class="center">Total</th>
						<?php 
					} 
				} ?>
			</tr>
			<?php 
			$count = 0;
			foreach ($getActiveTeacherByAcademicRank['teachersStatisticsByAcademicRank'] as $college => $departmentList) { ?>
				<tr>
					<td class="center" rowspan="<?=  $getActiveTeacherByAcademicRank['collegeRowSpan'][$college]+count($departmentList)+1;?>"><?= ++$count;?></td>
					<td class="vcenter" rowspan="<?=  $getActiveTeacherByAcademicRank['collegeRowSpan'][$college]+count($departmentList)+1;?>"><?= $college;?></td>
				</tr>
				<?php 
				foreach($departmentList as $deptname => $degreeLists) { 
					debug($degreeLists); ?>
					<tr>
						<td class="vcenter" rowspan="<?= count($degreeLists)+1; ?>"><?= $deptname; ?></td>   
					</tr>
					<?php 
					foreach ($degreeLists as $dk => $rankLists) {
						debug($rankLists); ?>
						<tr>
							<td class="center" style="border-right:2px #000000 solid;"><?= $dk; ?></td>
							<?php 
							foreach ($rankLists as $rk => $rv) { ?>
								<td class="center"><?= $rv['male']; ?></td>
								<td class="center"><?= $rv['female']; ?></td>
								<td class="center" style="border-right:2px #000000 solid;font-weight: bold;"><?= $rv['female']+$rv['male']; ?></td>
								<?php  
							} ?>
						</tr>
						<?php 
					} 
				}
			} ?>
		</table>
	</div>
	<?php 
  }
?>
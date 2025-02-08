<?php
// Schedule for Instructor
if (isset($instructor_course_schedules) && !empty($instructor_course_schedules)) { ?>
	<table class="small_padding">
		<?php
		if (empty($instructor_course_schedules)) { ?>
			<tr>
				<td style="border:0px solid #ffffff" colspan="2"><p style="padding-bottom:17px">Currently there is no released course schedule for you but you can view general course schedule from Schedule tab or <?= $this->Html->link(__('here', true), array('controller' => 'course_schedules', 'action' => 'index')); ?>.</p></td>
			</tr>
			<?php
		} else {
			foreach ($instructor_course_schedules as $icsk => $icsv) {
				if (!empty($icsv)) { ?>
					<tr>
						<td>
							<strong>Course:</strong> <?= $icsv[0]['PublishedCourse']['Course']['course_title'] . ' (' . $icsv[0]['PublishedCourse']['Course']['course_code'] . ')'; ?><br />
							<strong>Section:</strong> <?= $icsv[0]['Section']['name'] . ' (' . (isset($icsv[0]['PublishedCourse']['Department']['name']) ? $icsv[0]['PublishedCourse']['Department']['name'] . ' Department' : $icsv[0]['PublishedCourse']['College']['name'] . ' Freshman Program') . ')'; ?><br />
							<strong>Schedule:</strong><br />
							<?php
							$count = 1;
							foreach ($icsv as $schedule_key => $schedule_value) {
								$week_day = null;
								switch ($schedule_value['ClassPeriod'][0]['week_day']) {
									case 1:
										$week_day = "Sunday";
										break;
									case 2:
										$week_day = "Monday";
										break;
									case 3:
										$week_day = "Tuesday";
										break;
									case 4:
										$week_day = "Wednesday";
										break;
									case 5:
										$week_day = "Thursday";
										break;
									case 6:
										$week_day = "Friday";
										break;
									case 7:
										$week_day = "Saturday";
										break;
								}

								$period_count = count($schedule_value['ClassPeriod']);
								$ending_period = $schedule_value['ClassPeriod'][($period_count - 1)]['PeriodSetting']['hour'];
								$ending_hour = substr($ending_period, 0, 2);
								$other = substr($ending_period, 2);
								$ending_period_plus_one_hour = ($ending_hour + 01) . $other;
								$class_room = null;

								if (!empty($schedule_value['ClassRoom']['room_code'])) {
									$class_room = $schedule_value['ClassRoom']['room_code'] . ' - ' . $schedule_value['ClassRoom']['ClassRoomBlock']['Campus']['name'];
								} else {
									$class_room = "TBA";
								}

								$display_str = $week_day . ' ' . $this->Format->humanize_hour($schedule_value['ClassPeriod'][0]['PeriodSetting']['hour']) . ' - ' . $this->Format->humanize_hour($ending_period_plus_one_hour) . " (" . $schedule_value['CourseSchedule']['type'] . ', ' . $class_room . ', ' . $schedule_value['CourseSplitSection']['section_name'] . ") ";
								echo '<strong>' . $count++ . '.</strong> ' . $display_str . '<br />';
							}
							?>
						</td>
					</tr>
					<?php
				}
			}
		} ?>
	</table>
	<?php
} ?>

<?php
// Schedule for Student
if (isset($section_course_schedule) && !empty($section_course_schedule)) { ?>
	<table class="small_padding">
		<?php
		if (empty($section_course_schedule)) { ?>
			<tr>
				<td style="border:0px solid #ffffff" colspan="2"><p style="padding-bottom:17px">Currently there is no released course schedule for you but you can view general course schedule from Schedule tab or <?= $this->Html->link(__('here', true), array('controller' => 'dashboard', 'action' => 'index')); ?> .</p></td>
			</tr>
			<?php
		} else {
			foreach ($section_course_schedule as $scsk => $scsv) { ?>
				<table class="condence" style="border: #CCC double 3px ">
					<tr>
						<td class="smallheading" colspan="2"><?= $scsv[0]['Section']['name']; ?></td>
					</tr>
					<tr>
						<td>
							<table style="border: #CCC solid 1px ">
								<?php
								$starting = $starting_and_ending_hour['starting'];
								$starting_hour = substr($starting, 0, 2);
								$other = substr($starting, 2);
								$ending = $starting_and_ending_hour['ending'];
								$ending_hour = substr($ending, 0, 2); ?>
								
								<tr>
									<td style="border-right: #CCC solid 1px; width:40PX; background-color:#C6A6C6"> Week Day/Periods</td>
									<?php
									$time_deference = ($ending_hour - $starting_hour);
									$i = 0;
									while ($i <= $time_deference) { ?>
										<td style="border-right: #CCC solid 1px; width:40PX; background-color:#EBF3FB" ><?= $this->Format->humanize_hour(($starting_hour + $i) . $other); ?></td>
										<?php
										$i++;
									} ?>
								</tr>

								<?php
								for ($week_day = 1; $week_day <= 7; $week_day++) {
									$week_day_class_periods = array();
									foreach ($scsv as $csk => $csv) {
										if ($csv['ClassPeriod'][0]['week_day'] == $week_day) {
											foreach ($csv['ClassPeriod'] as $cpk => $cpv) {
												$week_day_class_periods[$csk][] = $cpv['PeriodSetting']['hour'];
											}
										}
									}
									//debug($week_day_class_periods);
									if (empty($week_day_class_periods)) { ?>
										<tr>
											<?php
											switch ($week_day) {
												case 1:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Sunday</td>';
													break;
												case 2:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Monday</td>';
													break;
												case 3:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Tuesday</td>';
													break;
												case 4:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Wednesday</td>';
													break;
												case 5:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Thursday</td>';
													break;
												case 6:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Friday</td>';
													break;
												case 7:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Saturday</td>';
													break;
											}
											for ($i = $starting_hour; $i <= $ending_hour; $i++) {
												echo '<td style="border-right: #CCC solid 1px; background-color:#899F47"></td>';
											} ?>
										</tr>
										<?php
									} else { ?>
										<tr>
											<?php
											switch ($week_day) {
												case 1:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Sunday</td>';
													break;
												case 2:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Monday</td>';
													break;
												case 3:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Tuesday</td>';
													break;
												case 4:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Wednesday</td>';
													break;
												case 5:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Thursday</td>';
													break;
												case 6:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Friday</td>';
													break;
												case 7:
													echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Saturday</td>';
													break;
											}

											$j = 0;

											while ($j <= $time_deference) {
												$scheduled = false;
												foreach ($week_day_class_periods as $course_schedule_key => $wdcpv) {
													foreach ($wdcpv as $wdcp_hour) {
														if (date("H:i:s", $wdcp_hour) == date("H:i:s", (($starting_hour + $j) . $other))) {
															
															$count_td = count($wdcpv);
															$class_room = null;

															if (!empty($scsv[$course_schedule_key]['ClassRoom']['room_code'])) {
																$class_room = $scsv[$course_schedule_key]['ClassRoom']['room_code'] . ' - ' . $scsv[$course_schedule_key]['ClassRoom']['ClassRoomBlock']['Campus']['name'];
															} else {
																$class_room = "TBA";
															}

															echo '<td style="border-right: #CCC solid 1px; text-align:center;" colspan="' . $count_td . '">' . $scsv[$course_schedule_key]['PublishedCourse']['Course']['course_code'] . ' (' . $scsv[$course_schedule_key]['CourseSchedule']['type'] . ', ' . $class_room . ', ' . $scsv[$course_schedule_key]['CourseSplitSection']['section_name'] . ')</td>';
															$j = $j + $count_td;
															$scheduled = true;
															break 2;
														}
													}
												}

												if ($scheduled == false) {
													echo '<td style="border-right: #CCC solid 1px; background-color:#899F47"></td>';
													$j++;
												}
											} ?>
										</tr>
										<?php
									}
								} ?>
							</table>
						</td>
					</tr>
				</table>
				<?php
			}
		} ?>
	</table>
	<?php
} ?>
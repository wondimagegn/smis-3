<?php
//Academic Calendar
if (isset($calendar) && !empty($calendar)) {
	if (!empty($calendar)) { 
		foreach ($calendar as $caldar) { ?>
			<table>
				<tr>
					<td>Program</td>
					<td><?= $caldar['calendarDetail']['Program']['name']; ?></td>
				</tr>
				<tr>
					<td>Program Type</td>
					<td><?= $caldar['calendarDetail']['ProgramType']['name']; ?></td>
				</tr>
				<tr>
					<td>Department</td>
					<td><?= $caldar['departmentname']; ?></td>
				</tr>
				<tr>
					<td>Year Level</td>
					<td>
						<ul>
							<?php
							foreach ($caldar['yearlevel'] as $ky => $kv) { ?>
								<li><?= $kv; ?></li>
								<?php 
							} ?>
						</ul>
					</td>
				</tr>
				<tr>
					<td>Course Registration: </td>
					<td><?= $this->Time->format("M j, Y g:i:s A", $caldar['calendarDetail']['AcademicCalendar']['course_registration_start_date'], NULL, NULL) . ' - ' . $this->Time->format("M j, Y g:i:s A", $caldar['calendarDetail']['AcademicCalendar']['course_registration_end_date'], NULL, NULL); ?></td>
				</tr>
				<tr>
					<td>Course Add: </td>
					<td><?= $this->Time->format("M j, Y g:i:s A", $caldar['calendarDetail']['AcademicCalendar']['course_add_start_date'], NULL, NULL) . ' - ' . $this->Time->format("M j, Y g:i:s A", $caldar['calendarDetail']['AcademicCalendar']['course_add_end_date'] , NULL, NULL); ?></td>
				</tr>
				<tr>
					<td>Course Drop: </td>
					<td><?= $this->Time->format("M j, Y g:i:s A", $caldar['calendarDetail']['AcademicCalendar']['course_drop_start_date'], NULL, NULL) . ' - ' . $this->Time->format("M j, Y g:i:s A", $caldar['calendarDetail']['AcademicCalendar']['course_drop_end_date'], NULL, NULL); ?></td>
				</tr>
			</table>
			<?php
		}
	} else { ?>
		<p>There is no academic calendar defined for now.</p>
		<?php
	} 
} ?>
<?php
if (isset($formatted_published_course_detail)) { ?>
	<div> Course Code: <?= $formatted_published_course_detail['course_code']; ?></div>
	<div> Course Name: <?= $formatted_published_course_detail['course_name']; ?></div>

	<table style="border: #CCC solid 1px">
		<tr>
			<td colspan="2" class="centeralign_smallheading"> <?= 'Assigned Instructors' ?></td>
		</tr>
		<tr>
			<?php 
			if (isset($formatted_published_course_detail['lecture'])) { ?>
				<th style="border-right: #CCC solid 1px"><?= 'Lecture'; ?></th>
				<?php 
			}
			if (isset($formatted_published_course_detail['tutorial'])) { ?>
				<th style="border-right: #CCC solid 1px"><?= 'Tutorial'; ?></th>
				<?php 
			}
			if (isset($formatted_published_course_detail['lab'])) { ?>
				<th style="border-right: #CCC solid 1px"><?= 'Laboratory'; ?></th>
				<?php 
			} ?>
		</tr>
		<tr>
			<?php 
			if (isset($formatted_published_course_detail['lecture'])) { ?>
				<td style="border-right: #CCC solid 1px"><?= $formatted_published_course_detail['lecture']; ?></td>
				<?php 
			}
			if (isset($formatted_published_course_detail['tutorial'])) { ?>
				<td style="border-right: #CCC solid 1px"><?= $formatted_published_course_detail['tutorial']; ?></td>
				<?php 
			}
			if (isset($formatted_published_course_detail['lab'])) { ?>
				<td style="border-right: #CCC solid 1px"><?= $formatted_published_course_detail['lab']; ?></td>
				<?php 
			} ?>
		</tr>
	</table>
	<?php
} ?>
	
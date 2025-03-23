<div class="box">
     <div class="box-body">
       <div class="row">
	  	<div class="large-12 columns">
	  	  <h2><?php echo __('General Settings'); ?></h2>
	  	  <table>
	  	  	<tr><td><?php echo __('DaysAvaiableForNgToF'); ?></td>
	  	  	<td><?php echo h($generalSetting['GeneralSetting']['daysAvaiableForNgToF']); ?></td></tr>

	  	  	<tr><td><?php echo __('DaysAvaiableForDoToF'); ?></td>
	  	  	<td>
			<?php echo h($generalSetting['GeneralSetting']['daysAvaiableForDoToF']); ?></td></tr>
			<tr><td><?php echo __('Days Available For Fx To F'); ?></td>
	  	  	<td>
			<?php echo h($generalSetting['GeneralSetting']['daysAvailableForFxToF']); ?> </td></tr>

			<tr><td><?php echo __('WeekCountForAcademicYear'); ?></td>
	  	  	<td>
			<?php echo h($generalSetting['GeneralSetting']['weekCountForAcademicYear']); ?> </td></tr>

			<tr><td><?php echo __('MinimumCreditForStatus'); ?></td>
	  	  	<td>
			<?php echo h($generalSetting['GeneralSetting']['minimumCreditForStatus']); ?> </td></tr>
			
			
			<tr><td><?php echo __('MaximumCreditPerSemester'); ?></td>
	  	  	<td>
			<?php echo h($generalSetting['GeneralSetting']['maximumCreditPerSemester']); ?> </td></tr>
			

			<tr><td><?php echo __('Allow Meal Without Cost sharing'); ?></td>
	  	  	<td>
			<?php echo h($generalSetting['GeneralSetting']['allowMealWithoutCostsharing']==1 ? 'Yes':'No' ); ?> </td></tr>

			<tr><td><?php echo __('Notify Students Grade By Email'); ?></td>
	  	  	<td>
			<?php echo h($generalSetting['GeneralSetting']['notifyStudentsGradeByEmail']==1 ? 'Yes':'No' ); ?> </td></tr>

			<tr><td><?php echo __('Allow Students Grade View Without Instructors Evalution'); ?></td>
	  	  	<td>
			<?php echo h($generalSetting['GeneralSetting']['allowStudentsGradeViewWithouInstructorsEvalution']==1 ? 'Yes':'No'); ?> </td></tr>

			<tr><td><?php echo __('Allow Students  Registration Without Payment'); ?></td>
	  	  	<td>
			<?php echo h($generalSetting['GeneralSetting']['allowRegistrationWithoutPayment']); ?> </td></tr>

	  	  </table>
	
</div>
</div>
</div>
</div>

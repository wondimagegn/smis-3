<!-- <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script> -->

<link href="/js/acCalendar/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="/js/acCalendar/jquery.min.js"></script>
<script src="/js/acCalendar/jquery-ui.min.js"></script>

<hr>
<table cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td style="background-color: white; width: 60%;">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="vcenter"> &nbsp; Select All / Unselect All <?= $this->Form->checkbox("SelectAll", array('id' => 'select-all', 'checked' => '')); ?></td>
					</tr>
					<?php
					if (!empty($colleges)) {
						foreach ($colleges as $college_id => $college_name) {
							if (isset($college_department[$college_id]) && count($college_department[$college_id]) > 0) { ?>
								<tr>
									<td style="background-color: white;">
										<!-- <h6 class='fs15 text-gray'><?php //echo $college_name; ?></h6> -->
										<fieldset style="padding-bottom: 20px;padding-top: 10px;">
											<legend>&nbsp;&nbsp; <?= $college_name; ?> &nbsp;&nbsp;</legend>
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <tbody>
                                                    <?php
                                                    if (!empty($college_department[$college_id])) {
                                                        foreach ($college_department[$college_id] as $department_id => $department_name) {

                                                            $recorded = null;
                                                            
                                                            if (isset($alreadyexisteddepartment) && !empty($alreadyexisteddepartment) && in_array($department_id, $alreadyexisteddepartment)) {
                                                                $recorded = 'class="rejected"';
                                                            }

                                                            if (isset($this->request->data['AcademicCalendar']['department_id'])) {
                                                                if (in_array($department_id, $this->request->data['AcademicCalendar']['department_id'])) {
                                                                    if (isset($recorded) && !empty($recorded)) { ?>
                                                                        <tr <?= $recorded; ?>>
                                                                            <td style="background-color: white;" class="vcenter"><input class="checkbox1" type="checkbox" name="data[AcademicCalendar][department_id][]" value=<?= $department_id; ?> id="AcademicCalendarDepartmentId<?= $department_id; ?>"><label for="AcademicCalendarDepartmentId<?= $department_id; ?>"><?= $department_name; ?></label></td>
                                                                        </tr>
                                                                        <?php
                                                                    } else { ?>
                                                                        <tr>
                                                                            <td style="background-color: white;" class="vcenter"><input type="checkbox" class="checkbox1" checked="checked" name="data[AcademicCalendar][department_id][]" value=<?= $department_id; ?> id="AcademicCalendarDepartmentId<?= $department_id; ?>"><label for="AcademicCalendarDepartmentId<?= $department_id; ?>"><?= $department_name; ?></label></td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                } else { ?>
                                                                    <tr>
                                                                        <td style="background-color: white;" class="vcenter"><input type="checkbox" class="checkbox1" name="data[AcademicCalendar][department_id][]" value=<?= $department_id; ?> id="AcademicCalendarDepartmentId<?= $department_id; ?>"><label for="AcademicCalendarDepartmentId<?= $department_id; ?>"><?= $department_name; ?></label></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            } else { ?>
                                                                <tr>
                                                                    <?php
                                                                    //debug(count(explode('pre_', $department_id)) > 1);
                                                                    if (count(explode('pre_', $department_id)) > 1) { ?>
                                                                        <td style="background-color: white;" class="vcenter"><input type="checkbox" class="checkbox1" name="data[AcademicCalendar][department_id][]" value=<?= $department_id; ?> id="AcademicCalendarDepartmentId<?= $department_id; ?>"><label for="AcademicCalendarDepartmentId<?= $department_id; ?>"><?= $department_name; ?></label></td>
                                                                        <?php
                                                                    } else { ?>
                                                                        <td style="background-color: white;" class="vcenter"><input type="checkbox" class="checkbox1" checked="checked" name="data[AcademicCalendar][department_id][]" value=<?= $department_id; ?> id="AcademicCalendarDepartmentId<?= $department_id; ?>"><label for="AcademicCalendarDepartmentId<?= $department_id; ?>"><?= $department_name; ?></label></td>
                                                                        <?php
                                                                    } ?>
                                                                </tr>
                                                                <?php
                                                            }
                                                        }
                                                    } ?>
                                                </tbody>
                                            </table>
										</fieldset>
									</td>
								</tr>
								<?php
							}
						}
					} ?>
				</table>
			</td>
			<td style="background-color: white;">
				<table cellpadding="0" cellspacing="0" class="table">
					<tbody>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.course_registration_start_date', array('id' => 'crStart', 'label' => 'Registration Start', 'type' => 'text', 'required', 'autocomplete' => 'off')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.course_registration_end_date', array('id' => 'crEnd', 'label' => 'Registration End', 'type' => 'text', 'required', 'autocomplete' => 'off', 'required')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.course_add_start_date', array('id' => 'caStart', 'label' => 'Course Add Start', 'type' => 'text', 'required', 'autocomplete' => 'off')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.course_add_end_date', array('id' => 'caEnd', 'label' => 'Course Add End', 'type' => 'text', 'required', 'autocomplete' => 'off')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.course_drop_start_date', array('id' => 'cdStart', 'label' => 'Course Drop Start', 'type' => 'text', 'required', 'autocomplete' => 'off')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.course_drop_end_date', array('id' => 'cdEnd', 'label' => 'Course Drop End', 'type' => 'text', 'required', 'autocomplete' => 'off')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.grade_submission_start_date', array('id' => 'gsStart', 'label' => 'Grade Submission Start', 'type' => 'text', 'required', 'autocomplete' => 'off')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.grade_submission_end_date', array('id' => 'gsEnd', 'label' => 'Grade Submission End', 'type' => 'text', 'required', 'autocomplete' => 'off')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.grade_fx_submission_end_date', array('id' => 'fxEnd', 'label' => 'Fx Grade Submission', 'type' => 'text', 'autocomplete' => 'off')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.senate_meeting_date', array('id' => 'senateDate', 'label' => 'Senate Meeting Date', 'type' => 'text', 'autocomplete' => 'off')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.graduation_date', array('id' => 'graduationDate', 'label' => 'Graduation Date', 'type' => 'text', 'autocomplete' => 'off')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.online_admission_start_date', array('id' => 'oaStart', 'label' => 'Online Admission Start Date', 'type' => 'text', 'autocomplete' => 'off')); ?></td>
						</tr>
						<tr>
							<td colspan='2'><?= $this->Form->input('AcademicCalendar.online_admission_end_date', array('id' => 'oaEnd', 'label' => 'Online Admission End Date', 'type' => 'text', 'autocomplete' => 'off')); ?></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<hr>
<?= $this->Form->Submit('Set Calendar', array('div' => false, 'id' => 'setCalendar', 'class' => 'tiny radius button bg-blue')); ?>

<script>

    var d = new Date();
    var month = d.getMonth();
    var day = d.getDate();

    // yy-mm-dd format
    //var today = d.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' +  (day < 10 ? '0' : '') + day;

    // mm-dd-yy format
    var today =  (month < 10 ? '0' : '') + month + '/' +  (day < 10 ? '0' : '') + day + '/' + d.getFullYear();

    var caStart =  new Date(d.getFullYear(), d.getMonth(), d.getDate() + 7);
    //alert(today);


    $(function() {
        $( "#crStart, #crEnd" ).datepicker({
            //defaultDate: today,// "+1w",
            changeMonth: true,
            numberOfMonths: 2,
            onSelect: function( selectedDate ) {
                if (this.id == 'crStart') {

                    var dateMin = $('#crStart').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 1); // Min Date = Selected + 1d
                    var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 7); // Max Date = Selected + 31d
                    $('#crEnd').datepicker("option","minDate",rMin);
                    $('#crEnd').datepicker("option","maxDate",rMax); 

                } else if (this.id == 'crEnd') {

                    var dateMin = $('#crEnd').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 1); // Min Date = Selected + 1d
                    var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 7); // Max Date = Selected + 31d
                   
                    // Course Add Start
                    $('#caStart').datepicker("option","minDate",rMin);
                    $('#caStart').datepicker("option","maxDate",rMax);

                    // Course drop Start
                    $('#cdStart').datepicker("option","minDate",rMin);
                    $('#cdStart').datepicker("option","maxDate",rMax);

                }
            }
        });

        $( "#caStart, #caEnd" ).datepicker({
            //defaultDate: caStart,//"+1w",
            //changeMonth: true,
            numberOfMonths: 2,
            onSelect: function( selectedDate ) {
                if (this.id == 'caStart') {

                    var dateMin = $('#caStart').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 1); // Min Date = Selected + 1d
                    var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 7); // Max Date = Selected + 31d

                    var cdStartMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate());
                    
                    $('#caEnd').datepicker("option","minDate",rMin);
                    $('#caEnd').datepicker("option","maxDate",rMax); 
                    
                    $('#cdStart').datepicker("option","minDate",cdStartMin); // Make Course Add & course Drop start dates to to same date
                    

                } else if (this.id == 'caEnd') {

                    var dateMin = $('#caEnd').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 1); // Min Date = Selected + 1d
                    var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 120); // Max Date = Selected + 31d


                    var cdEnd = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate());
                    
                    // Grade Submission Start
                    $('#gsStart').datepicker("option","minDate",rMin);
                    $('#gsStart').datepicker("option","maxDate",rMax); 
                    

                    $('#cdEnd').datepicker("option","maxDate",cdEnd); // Make Course Add & course Drop End dates to to same date
                    
                }
            }
        });

        $( "#cdStart, #cdEnd" ).datepicker({
            //defaultDate: caStart,//"+1w",
            //changeMonth: true,
            numberOfMonths: 2,
            onSelect: function( selectedDate ) {
                if (this.id == 'cdStart') {

                    var dateMin = $('#cdStart').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 1); // Min Date = Selected + 1d
                    var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 7); // Max Date = Selected + 7d

                    var caStart = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate());
                    
                    $('#cdEnd').datepicker("option","minDate",rMin);
                    $('#cdEnd').datepicker("option","maxDate",rMax); 

                    $('#caStart').datepicker("option","minDate",caStart);  // Make Course Add Start Date the same as Course Drop Start Date.

                } else if (this.id == 'cdEnd') {

                    var dateMin = $('#cdEnd').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 1); // Min Date = Selected + 1d
                    var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 120); // Max Date = Selected + 120d


                    var caEnd = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate());
                    
                    // Grade Submission Start
                    $('#gsStart').datepicker("option","minDate",rMin);
                    $('#gsStart').datepicker("option","maxDate",rMax);

                    $('#caEnd').datepicker("option","maxDate",caEnd); // Make Course Add End Date the same as Course Drop End Date.
                    
                    
                }
            }
        });

        $( "#gsStart, #gsEnd" ).datepicker({
            //defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 3,
            onSelect: function( selectedDate ) {
                if (this.id == 'gsStart') {

                    var dateMin = $('#gsStart').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 1); // Min Date = Selected + 1d
                    var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 120); // Max Date = Selected + 7d

                    
                    //var fxStarMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 120 + 1);

                    $('#gsEnd').datepicker("option","minDate",rMin);
                    $('#gsEnd').datepicker("option","maxDate",rMax); 

                    //$('#fxEnd').datepicker("option","minDate",fxStarMin);  // Make fx submission minimun date after the end of .

                } else if (this.id == 'gsEnd') {

                    var dateMin = $('#gsEnd').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 1); // Min Date = Selected + 1d
                    var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 120); // Max Date = Selected + 120d

                    //var senateDateMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 121);
                    //var senateDateMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 200);
                    
                    // Grade Submission Start
                    $('#senateDate').datepicker("option","minDate",rMin);
                    //$('#senateDate').datepicker("option","maxDate",rMax);

                    $('#fxEnd').datepicker("option","minDate",rMin); 
                    
                    
                }
            }
        });

        $( "#fxEnd").datepicker({
            //defaultDate: "+2m",
            changeMonth: true,
            //numberOfMonths: 4,
            onSelect: function( selectedDate ) {
                if (this.id == 'fxEnd') {

                    var dateMin = $('#fxEnd').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 1); // Min Date = Selected + 1d
                    //var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 120); // Max Date = Selected + 7d
                    
                    $('#senateDate').datepicker("option","minDate",rMin);

                }
            }
        });

        $( "#senateDate").datepicker({
            //defaultDate: "+1w",
            changeMonth: true,
            //numberOfMonths: 4,
            onSelect: function( selectedDate ) {
                if (this.id == 'senateDate') {

                    var dateMin = $('#senateDate').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 1); // Min Date = Selected + 1d
                    //var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 120); // Max Date = Selected + 7d
                    
                    $('#graduationDate').datepicker("option","minDate",rMin);

                }
            }
        });

        $( "#graduationDate").datepicker({
            //defaultDate: "+3m",
            changeMonth: true,
            //numberOfMonths: 4,
            onSelect: function( selectedDate ) {
                if (this.id == 'graduationDate') {

                    var dateMin = $('#graduationDate').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 1); // Min Date = Selected + 1d
                    //var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 120); // Max Date = Selected + 7d
                    
                    //$('#graduationDate').datepicker("option","minDate",rMin);

                }
            }
        });

        $( "#oaStart, #oaEnd" ).datepicker({
            //defaultDate: today,// "+1w",
            changeMonth: true,
            numberOfMonths: 2,
            onSelect: function( selectedDate ) {
                if (this.id == 'oaStart') {

                    var dateMin = $('#oaStart').datepicker("getDate");
                    var rMin = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 1); // Min Date = Selected + 1d
                    var rMax = new Date(dateMin.getFullYear(), dateMin.getMonth(),dateMin.getDate() + 7); // Max Date = Selected + 31d
                    $('#oaEnd').datepicker("option","minDate",rMin);
                    $('#oaEnd').datepicker("option","maxDate",rMax); 

                }
            }
        });
    });
</script>
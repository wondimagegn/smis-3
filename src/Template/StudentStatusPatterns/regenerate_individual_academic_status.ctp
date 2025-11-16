<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Regenerate Status For a Student') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create('StudentStatusPattern') ?>
                <div style="margin-top: -30px;"><hr></div>
                <blockquote>
                    <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                    <p style="text-align:justify;">
                        <span class="fs14 text-gray" style="font-weight: bold;">
                            This tool will help you to regenerate or correct wrongly generated student academic status. This tool will regenerate status of the student from the beginning until the time of regeneration if the student fulfills the minimum required credit defined currently in "General Settings" system wide, as per program and program type.
                        </span>
                    </p>
                </blockquote>
                <hr>
                <div onclick="toggleViewFullId('ListPublishedCourse')" id="toggleShowStudentID">
                    <?php if (isset($alreadyGeneratedStatus) && !empty($alreadyGeneratedStatus)): ?>
                        <?= $this->Html->image('plus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                        <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
                    <?php else: ?>
                        <?= $this->Html->image('minus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                        <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
                    <?php endif; ?>
                </div>
                <div id="ListPublishedCourse" style="display:<?= (isset($alreadyGeneratedStatus) ? 'none' : 'block') ?>">
                    <fieldset style="padding-bottom: 5px;">
                        <legend>&nbsp;&nbsp; Student Number / ID &nbsp;&nbsp;</legend>
                        <div class="row">
                            <div class="large-4 columns">
                                <?= $this->Form->control('Student.studentnumber', [
                                    'label' => false,
                                    'placeholder' => __('Type Student ID...'),
                                    'id' => 'StudentStudentID',
                                    'required',
                                    'maxlength' => MAXIMUM_STUDENT_ID_NUMBER_LENGTH_DB
                                ]) ?>
                            </div>
                        </div>
                    </fieldset>
                    <?= $this->Form->submit(__('Get Student Details'), [
                        'name' => 'regenerateStudentStatus',
                        'value'=>'regenerateStudentStatus',
                        'id' => 'getStudentDetails',
                        'class' => 'tiny radius button bg-blue',
                        'div' => false
                    ]) ?>
                </div>
                <?php if (isset($hideSearch) && $hideSearch): ?>
                    <div id="showStudentBasicProfile"><hr>
                        <?= $this->element('student_basic') ?>

                    </div>
                <?php endif; ?>
                <?php

                if (isset($alreadyGeneratedStatus) && !empty($alreadyGeneratedStatus)): ?>
                    <div id="showSearchResults">
                        <div style="overflow-x:auto;">
                            <table id='fieldsForm' cellpadding="0" cellspacing="0" class='table'>
                                <thead>
                                <tr>
                                    <th class="center">#</th>
                                    <th class="center"><?= __('ACY') ?></th>
                                    <th class="center"><?= __('Sem') ?></th>
                                    <th class="center"><?= __('CHS') ?></th>
                                    <th class="center"><?= __('GPS') ?></th>
                                    <th class="center"><?= __('MCHS') ?></th>
                                    <th class="center"><?= __('MGPS') ?></th>
                                    <th class="center"><?= __('SGPA') ?></th>
                                    <th class="center"><?= __('CGPA') ?></th>
                                    <th class="center"><?= __('MCGPA') ?></th>
                                    <th class="center"><?= __('Status') ?></th>
                                    <th class="center"><?= __('Date Generated') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $counter = 1; foreach ($alreadyGeneratedStatus as $value):

                                    ?>
                                    <tr>
                                        <td class="center"><?= $counter ?></td>
                                        <td class="center">
                                            <?= $this->Form->hidden("StudentStatusPattern.{$value->id}.id",
                                                ['value' => $value->id]) ?>
                                            <?= h($value->academic_year) ?>
                                        </td>
                                        <td class="center"><?= h($value->semester) ?></td>
                                        <td class="center"><?= h($value->credit_hour_sum) ?></td>
                                        <td class="center"><?= h($value->grade_point_sum) ?></td>
                                        <td class="center"><?= h($value->m_credit_hour_sum) ?></td>
                                        <td class="center"><?= h($value->m_grade_point_sum) ?></td>
                                        <td class="center"><?= h($value->sgpa) ?></td>
                                        <td class="center"><?= h($value->cgpa) ?></td>
                                        <td class="center"><?= h($value->mcgpa) ?></td>
                                        <td class="center"><?= h($value->academic_status->name ?? '') ?></td>

                                        <td class="center"><?= $value->created ?
                                                h($value->created->format('M j, Y')) : '' ?></td>
                                    </tr>
                                    <?php $counter++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                    </div>
                <?php endif; ?>
                <?php if (isset($hideSearch) && $hideSearch): ?>
                    <div id="regenerateBtnContainer">
                        <?php if (isset($studentSectionExamStatus->student->id)): ?>
                            <?= $this->Form->hidden('Student.id', ['value' =>
                                $studentSectionExamStatus->student->id]) ?>
                        <?php endif; ?>
                        <?= $this->Form->submit(__('Regenerate Student Status'), [
                            'name' => 'regenerate',
                            'id' => 'regenerateStudentStatus',
                            'disabled' => (isset($alreadyGeneratedStatus) &&
                                !empty($alreadyGeneratedStatus) || (isset($haveRegistrations)
                                    && $haveRegistrations)) ? false : true,
                            'div' => false,
                            'class' => 'tiny radius button bg-blue'
                        ]) ?>
                    </div>
                    <hr>
                <?php endif; ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
        <div class="row">
            <div class="large-12 columns">

                <?php

                if (isset($alreadyGeneratedStatus) && !empty($alreadyGeneratedStatus)): ?>
                    <div class="large-12 columns" id="showAdditionalInformation">
                        <?php $showAdditionalInfo = ''; ?>
                        <div onclick="toggleViewFullId('displayAdditionalInfo')">
                            <?= $this->Html->image('minus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                            <span style="font-size:10px; vertical-align:top; font-weight:bold"
                                  id="showAdditionalInfoTxt">
                Show/Hide Additional Information (Current Minimum Required Credit/ECTS
                for Status per Program and Program Type)</span>
                        </div>
                        <br>
                        <div id="displayAdditionalInfo" style="display:none">
                            <div class="large-6 columns">
                                <p style="text-align:justify;"><span class="fs14 text-black"
                                                                     style="font-weight: bold;">
                        Table: Keys and Descriptions</span></p>
                                <div style="overflow-x:auto;">
                                    <table cellpadding="0" cellspacing="0" class='table'>
                                        <thead>
                                        <tr>
                                            <th>Short</th>
                                            <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr><td>CHS</td><td>Credit Hour Sum</td></tr>
                                        <tr><td>GPS</td><td>Grade Point Sum</td></tr>
                                        <tr><td>MCHS</td><td>Major Credit Hour Sum</td></tr>
                                        <tr><td>MGPS</td><td>Major Grade Point Sum</td></tr>
                                        <tr><td>SGPA</td><td>Semester Grade Point Average</td></tr>
                                        <tr><td>CGPA</td><td>Cumulative Grade Point Average</td></tr>
                                        <tr><td>MCGPA</td><td>Major Cumulative Grade Point Average</td></tr>
                                        </tbody>
                                    </table>
                                    <br>
                                </div>
                            </div>
                            <div class="large-6 columns">
                                <?php if (isset($generalSettings) && !empty($generalSettings)): ?>
                                    <p style="text-align:justify;"><span class="fs14 text-black"
                                                                         style="font-weight: bold;">
                            Table: Current Minimum Required Credit/ECTS for Status</span></p>
                                    <div style="overflow-x:auto;">
                                        <table cellpadding="0" cellspacing="0" class="table">
                                            <thead>
                                            <tr>
                                                <th><?= __('Program') ?></th>
                                                <th><?= __('Program Type') ?></th>
                                                <th class="center"><?= __('Credit') ?></th>
                                                <th class="center"><?= __('ECTS') ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($generalSettings as $generalSetting): ?>
                                                <tr>
                                                    <td>
                                                        <?php foreach ($generalSetting->program_id as $key => $value): ?>
                                                            <?= h($value) ?><br>
                                                        <?php endforeach; ?>
                                                    </td>
                                                    <td>
                                                        <?php foreach ($generalSetting->program_type_id as $key => $value): ?>
                                                            <?= h($value) ?>,
                                                        <?php endforeach; ?>
                                                    </td>
                                                    <td class="center"><?= h($generalSetting->minimumCreditForStatus) ?></td>
                                                    <td class="center"><?= round(($generalSetting->minimumCreditForStatus * CREDIT_TO_ECTS), 0) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<script type='text/javascript'>
    function toggleViewFullId(id) {
        if ($('#' + id).css("display") == 'none') {
            $('#' + id + 'Img').attr("src", '/img/minus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Hide Filter');
        } else {
            $('#' + id + 'Img').attr("src", '/img/plus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Display Filter');
        }
        $('#' + id).toggle("slow");
    }

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('StudentStatusPatternRegenerateIndividualAcademicStatusForm');
        // get ID related global settings for Student ID validation
        const STUDENT_ID_REGEX_SEARCH = <?= json_encode(trim(STUDENT_ID_NUMBER_REGEX_FOR_SEARCH, '/')) ?>;
        const basicStudentIDPattern = new RegExp(STUDENT_ID_REGEX_SEARCH);
        const minStudentIdNumberLength = <?= json_encode(MINIMUM_STUDENT_ID_NUMBER_LENGTH) ?>;
        const maxStudentIdNumberLengthDB = <?= json_encode(MAXIMUM_STUDENT_ID_NUMBER_LENGTH_DB) ?>;
        const minStudentIdDigitsLength = <?= json_encode(MINIMUM_STUDENT_ID_DIGITS_LENGTH) ?>;
        const maxStudentIdDigitsLength = <?= json_encode(MAXIMUM_STUDENT_ID_DIGITS_LENGTH + STUDENT_ID_BATCH_YEAR_LENGTH) ?>;

        let formBeingSubmitted = false;
        let regeneratingStudentStatusOnProgress = false;

        function showInlineError(input, message) {
            removeInlineError(input);
            const tooltip = document.createElement('div');
            tooltip.className = 'legacy-tooltip';
            tooltip.textContent = message;
            const br = document.createElement('br');
            // Parent needs positioning context
            const parent = input.closest('.input');
            parent.style.position = 'relative';
            tooltip.style.cssText = `
                position: absolute;
                bottom: 100%;
                left: 0;
                width: ${input.offsetWidth}px;
                background: #fff;
                color: #dc3545;
                border: 1px solid #dc3545;
                padding: 6px 10px;
                border-radius: 4px;
                font-size: 0.75rem;
                white-space: nowrap;
                margin-bottom: 14px;
                z-index: 999;
                box-shadow: 0 2px 6px rgba(220, 53, 69, 0.1);
            `;
            parent.appendChild(tooltip);
        }

        form.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function () {
                this.value = this.value.replace(/\s+/g, ''); // removes spaces and tabs
                removeInlineError(this);
            });
            input.addEventListener('blur', function () {
                this.value = this.value.trim(); // full trim on blur
            });
        });

        function removeInlineError(input) {
            const existing = input.parentNode.querySelector('.legacy-tooltip');
            if (existing) existing.remove();
        }

        function hideSearchResultsAndInfo() {
            const elements = [
                'showSearchResults',
                'showAdditionalInformation',
                'showStudentBasicProfile'
            ];
            elements.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });
            const regenerateBtnContainer = document.getElementById('regenerateBtnContainer');
            if (regenerateBtnContainer) {
                form.regenerateStudentStatus.disabled = true;
                regenerateBtnContainer.style.display = 'none';
            } else {
                regeneratingStudentStatusOnProgress = false;
            }
        }

        form.addEventListener('submit', function (e) {
            const clickedButton = e.submitter;
            const studentStudentID = form.StudentStudentID;
            const studentStudentIDvalue = studentStudentID.value.trim();
            const isThisSearchBtn = (clickedButton.id === 'getStudentDetails');

            let valid = true;

            if (studentStudentIDvalue.length < minStudentIdNumberLength) {
                showInlineError(studentStudentID, "Student ID is too short. Please check it.");
                studentStudentID.focus();
                valid = false;
            } else if (studentStudentIDvalue.length > maxStudentIdNumberLengthDB) {
                showInlineError(studentStudentID, "Student ID is too long. Please check it.");
                studentStudentID.focus();
                valid = false;
            } else {
                removeInlineError(studentStudentID);
            }

            // Test the provided Student ID against the pattern
            const basicStudentIDPatternPassed = basicStudentIDPattern.test(studentStudentIDvalue);
            if (basicStudentIDPatternPassed) {
                // passed basic Student ID pattern, check for digit count
                const digitsCount = (studentStudentIDvalue.match(/\d/g) || []).length;
                if (!digitsCount || digitsCount < minStudentIdDigitsLength || digitsCount > maxStudentIdDigitsLength) {
                    showInlineError(studentStudentID, "Invalid Student ID, Please check it.");
                    studentStudentID.focus();
                    valid = false;
                } else {
                    // valid Student ID, remove any existing error
                    removeInlineError(studentStudentID);
                }
            } else {
                showInlineError(studentStudentID, "Invalid Student ID, Please check it.");
                studentStudentID.focus();
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
                return;
            }

            if (formBeingSubmitted && isThisSearchBtn) {
                alert('Searching for ' + studentStudentID.value.trim() + ', please wait a moment...');
                form.regenerateStudentStatus.disabled = true;
                form.getStudentDetails.disabled = true;
                e.preventDefault();
                return;
            } else if (formBeingSubmitted && !isThisSearchBtn) {
                alert('Regenerating status for ' + studentStudentID.value.trim() + ', please wait a moment...');
                e.preventDefault();
                form.regenerateStudentStatus.disabled = true;
                form.getStudentDetails.disabled = true;
                return;
            }

            if (isThisSearchBtn) {
                form.getStudentDetails.value = 'Searching...';
                form.regenerateStudentStatus.disabled = true;
                hideSearchResultsAndInfo();
            } else if (clickedButton.id === 'regenerateStudentStatus') {
                form.regenerateStudentStatus.value = 'Regenerating Student Status...';
                form.getStudentDetails.disabled = true;
                regeneratingStudentStatusOnProgress = true;

                // Optionally Hide and Disable the toggle button to prevent further clicks
                /* const toggleDiv = document.getElementById('toggleShowStudentID');
                if (toggleDiv) {
                    toggleDiv.removeAttribute('onclick');
                    toggleDiv.style.display = 'none';
                } */
            }

            formBeingSubmitted = true;
        });

        // prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });
</script>

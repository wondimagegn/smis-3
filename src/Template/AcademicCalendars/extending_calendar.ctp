<script>
    function toggleViewFullId(id) {
        let element = $('#' + id);
        let imgElement = $('#' + id + 'Img');
        let txtElement = $('#' + id + 'Txt');

        if (element.css("display") === 'none') {
            imgElement.attr("src", '/img/minus2.gif');
            txtElement.text('Hide Filter');
        } else {
            imgElement.attr("src", '/img/plus2.gif');
            txtElement.text('Display Filter');
        }
        element.toggle("slow");
    }
</script>

<?= $this->Form->create($academicCalendar, ['url' => ['action' => 'extendingCalendar']]); ?>
<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div onclick="toggleViewFullId('ExtendCalendar')">
                    <?= $this->Html->image(
                        !empty($academicCalendars) ? 'plus2.gif' : 'minus2.gif',
                        ['id' => 'ExtendCalendarImg']
                    ); ?>
                    <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ExtendCalendarTxt">
                        <?= !empty($academicCalendars) ? 'Display Filter' : 'Hide Filter'; ?>
                    </span>
                </div>
                <div id="ExtendCalendar" style="display: <?= !empty($academicCalendars) ? 'none' : 'block'; ?>;">
                    <div class="smallheading">Please select the academic year, semester, program, and program type to
                        extend the academic calendar.
                    </div>
                    <table class="fs14">
                        <tr>
                            <td>Academic Year:</td>
                            <td><?= $this->Form->control(
                                    'Search.academic_year',
                                    [
                                        'label' => false,
                                        'class' => 'fs14',
                                        'type' => 'select',
                                        'options' => $acyearArrayData,
                                        'default' => $academicYearSelected ?? $defaultAcademicYear
                                    ]
                                ); ?></td>
                            <td>Semester:</td>
                            <td><?= $this->Form->control(
                                    'Search.semester',
                                    [
                                        'label' => false,
                                        'class' => 'fs14',
                                        'type' => 'select',
                                        'options' => ['I' => 'I', 'II' => 'II', 'III' => 'III'],
                                        'default' => $semesterSelected ?? false
                                    ]
                                ); ?></td>
                        </tr>
                        <tr>
                            <td>Program:</td>
                            <td><?= $this->Form->control(
                                    'Search.program_id',
                                    [
                                        'label' => false,
                                        'class' => 'fs14',
                                        'type' => 'select',
                                        'options' => $programs,
                                        'default' => $programId ?? false
                                    ]
                                ); ?></td>
                            <td>Program Type:</td>
                            <td><?= $this->Form->control(
                                    'Search.program_type_id',
                                    [
                                        'label' => false,
                                        'class' => 'fs14',
                                        'type' => 'select',
                                        'options' => $programTypes,
                                        'default' => $programTypeId ?? false
                                    ]
                                ); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <?= $this->Form->button(
                                    __('Continue'),
                                    ['class' => 'tiny radius button bg-blue', 'name' => 'searchbutton']
                                ); ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php
                if (!empty($academicCalendars)): ?>
                    <table>
                        <tr>
                            <td><?= $this->Form->control(
                                    'ExtendingAcademicCalendar.academic_calendar_id',
                                    [
                                        'empty' => '--Select Academic Calendar--',
                                        'style' => 'width:250px;',
                                        'required' => true
                                    ]
                                ); ?></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td><?= $this->Form->control(
                                    'ExtendingAcademicCalendar.department_id',
                                    [
                                        'type' => 'select',
                                        'multiple' => true,
                                        'options' => $departments,
                                        'style' => 'width:200px;height:auto;',
                                        'required' => true
                                    ]
                                ); ?></td>
                            <td><?= $this->Form->control(
                                    'ExtendingAcademicCalendar.year_level_id',
                                    [
                                        'type' => 'select',
                                        'multiple' => true,
                                        'options' => $yearLevels,
                                        'style' => 'width:200px;height:auto;',
                                        'required' => true
                                    ]
                                ); ?></td>
                        </tr>
                        <tr>
                            <td><?= $this->Form->control(
                                    'ExtendingAcademicCalendar.activity_type',
                                    [
                                        'type' => 'select',
                                        'options' => $activityTypes,
                                        'label' => 'Which activity would you like to extend?',
                                        'style' => 'width:200px;'
                                    ]
                                ); ?></td>
                            <td><?= $this->Form->control(
                                    'ExtendingAcademicCalendar.days',
                                    [
                                        'type' => 'number',
                                        'label' => 'How many days would you like to extend?',
                                        'required' => true,
                                        'style' => 'width:70px;'
                                    ]
                                ); ?></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td><?= $this->Form->button(
                                    __('Extend'),
                                    ['class' => 'tiny radius button bg-blue', 'name' => 'extend']
                                ); ?></td>
                        </tr>
                    </table>
                <?php
                endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->Form->end(); ?>

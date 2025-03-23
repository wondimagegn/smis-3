<?php
use Cake\Core\Configure;
use Cake\Utility\Inflector;
?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-calendar" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __(
                    'Academic Calendars'
                ); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create($academicCalendar); ?>
                <div style="margin-top: -30px;">
                    <hr>
                    <fieldset style="padding-bottom: 5px;padding-top: 15px;">
                        <div class="row">
                            <div class="large-3 columns">
                                <?= $this->Form->control(
                                    'Search.academic_year',
                                    [
                                        'id' => 'academicYear',
                                        'label' => 'Academic Year:',
                                        'required' => true,
                                        'style' => 'width:90%',
                                        'type' => 'select',
                                        'options' => $acyearArrayData,
                                        'default' => $defaultAcademicYear ?? ''
                                    ]
                                ); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control(
                                    'Search.semester',
                                    [
                                        'label' => 'Semester:',
                                        'style' => 'width:80%;',
                                        'options' => Configure::read('semesters'),
                                        'empty' => '[ All Semesters ]'
                                    ]
                                ); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control(
                                    'Search.program_id',
                                    ['label' => 'Program:', 'empty' => '[ All Programs ]', 'style' => 'width:90%;']
                                ); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control(
                                    'Search.program_type_id',
                                    [
                                        'label' => 'Program Type:',
                                        'empty' => '[ All Program Types ]',
                                        'style' => 'width:90%;'
                                    ]
                                ); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="large-6 columns">
                                <?= $this->Form->control(
                                    'Search.department_id',
                                    [
                                        'label' => 'Department:',
                                        'style' => 'width:90%;',
                                        'empty' => '[ All Departments ]'
                                    ]
                                ); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control(
                                    'Search.year_level_id',
                                    [
                                        'label' => 'Year Level:',
                                        'empty' => '[ All Year Levels ]',
                                        'style' => 'width:90%;'
                                    ]
                                ); ?>
                            </div>
                            <div class="large-3 columns">&nbsp;</div>
                        </div>
                        <hr>
                        <?= $this->Form->button(
                            __('View Academic Calendar'),
                            [
                                'name' => 'viewAcademicCalendar',
                                'class' => 'tiny radius button bg-blue',
                                'id' => 'viewAcademicCalendar'
                            ]
                        ); ?>
                    </fieldset>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('form input').change(function (e) {
            updateExtension(e);
            e.preventDefault();
        });
    });

    function updateExtension(e) {
        let target = $(e.target);
        let value = target.val();

        if (value !== "" && isNaN(value)) {
            alert('Please enter a valid result.');
            target.focus().blur();
            return false;
        } else if (value !== "" && parseInt(value) < 0) {
            target.focus().blur();
            return false;
        }

        $.ajax({
            url: "/academic-calendars/auto-save-extension",
            type: 'POST',
            data: $('form').serialize(),
            success: function (data) {
            }
        });
    }

    function toggleView(obj) {
        let id = obj.id;
        let row = $('#c' + id);
        let icon = $('#i' + id);

        if (row.css("display") === 'none') {
            icon.attr("src", '/img/minus2.gif');
        } else {
            icon.attr("src", '/img/plus2.gif');
        }
        row.toggle("slow");
    }
</script>

 <div class="box">
        <div class="box-header bg-transparent">
            <div class="box-title" style="margin-top: 10px;">
                <i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Publish or Prepare Semester Courses') ?></span>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="large-12 columns">
                    <?= $this->Form->create(null, ['type' => 'post', 'id' => 'publishCourseForm']) ?>
                   <?php  if (!isset($turn_off_search)) { ?>
                        <div style="margin-top: -30px;">
                            <hr>
                            <fieldset style="padding-bottom: 5px; padding-top: 15px;">
                                <div class="row">
                                    <div class="large-2 columns">
                                        <?= $this->Form->control('Course.academicyear', [
                                            'label' => 'Academic Year: ',
                                            'required' => true,
                                            'type' => 'select',
                                            'style' => 'width:90%;',
                                            'options' => $acyear_array_data,
                                            'empty' => '[ Select Academic Year ]',
                                            'default' => $defaultacademicyear ?? ''
                                        ]) ?>
                                    </div>
                                    <div class="large-2 columns">
                                        <?= $this->Form->control('Curriculum.semester', [
                                            'label' => 'Semester: ',
                                            'options' => \Cake\Core\Configure::read('semesters'),
                                            'required' => true,
                                            'empty' => '[ Select semester ]',
                                            'style' => 'width:90%;'
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Curriculum.program_id', [
                                            'label' => 'Program: ',
                                            'required' => true,
                                            'empty' => '[ Select Program ]',
                                            'style' => 'width:90%;'
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Curriculum.program_type_id', [
                                            'label' => 'Program Type: ',
                                            'required' => true,
                                            'empty' => '[ Select Program Type ]',
                                            'style' => 'width:90%;'
                                        ]) ?>
                                    </div>
                                    <div class="large-2 columns">
                                        <?= $this->Form->control('Course.year_level_id', [
                                            'label' => 'Year Level: ',
                                            'required' => true,
                                            'empty' => '[ Select Year Level ]',
                                            'style' => 'width:90%;'
                                        ]) ?>
                                    </div>
                                </div>
                                <hr>
                                <?= $this->Form->button('Continue',
                                    ['name' => 'getsection','value'=>'getsection',
                                        'class' => 'tiny radius button bg-blue']) ?>
                            </fieldset>
                        </div>
                    <?php } ?>

                    <div id="loading"></div>

                    <?php  if (isset($turn_off_search)) { ?>
                        <table cellpadding="0" cellspacing="0" class="table-borderless">
                            <tr>
                                <td>
                                    <h6 class="text-gray fs16">
                                        Select the course you want to publish for
                                        <?= ($semester == 'I' ? '1st' : ($semester == 'II' ? '2nd' : ($semester == 'III' ? '3rd' : $semester))) . ' Semester of ' . h($academic_year) . ' Academic Year' ?>
                                    </h6>
                                </td>
                            </tr>
                            <?= $this->Form->hidden('PublishedCourse.semester', ['value' => $semester]) ?>
                            <?= $this->Form->hidden('PublishedCourse.program_id', ['value' => $program_id]) ?>
                            <?= $this->Form->hidden('PublishedCourse.program_type_id', ['value' => $program_type_id]) ?>
                            <?= $this->Form->hidden('PublishedCourse.academic_year', ['value' => $academic_year]) ?>
                            <?= $this->Form->hidden('PublishedCourse.year_level_id', ['value' => $year_level_id]) ?>
                            <?php if (!empty($sections)) { ?>
                                <?php foreach ($sections as $key => $value) {

                                    ?>
                                    <tr>
                                        <td>
                                            <?= $this->Form->control('Section.selected.' . $key, [
                                                'class' => 'candidatePublishCourse',
                                                'label' => h($value),
                                                'type' => 'checkbox',
                                                'value' => $key,
                                                'checked' => isset($selectedsection) && in_array($key, $selectedsection)
                                            ]) ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </table>
                    <?php } ?>

                    <div id="candidate_published_course_list"></div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
<script>
    $(document).ready(function() {
        $(".candidatePublishCourse").on("change", function() {
            if ($(this).is(":checked")) {
                $("#busy_indicator").show();
                $.ajax({
                    url: '/publishedCourses/selectedPublishedCourses/2',
                    type: 'POST',
                    data: $("#publishCourseForm").serialize(),
                    success: function(data) {
                        $("#candidate_published_course_list").html(data);
                        $("#busy_indicator").hide();
                    },
                    error: function() {
                        $("#busy_indicator").hide();
                        alert("Error loading courses.");
                    }
                });
            }
        });
    });
</script>

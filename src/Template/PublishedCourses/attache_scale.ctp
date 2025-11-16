<?php
$this->assign('title', __('Attach Grade Scale to Courses'));
?>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->create('PublishedCourse', ['url' => ['controller' => 'PublishedCourses',
                    'action' => 'attachScale']]) ?>
                <?php if (!isset($turn_off_search)): ?>
                    <table class="table table-bordered">
                        <tr>
                            <td colspan="2" class="smallheading"><?= __('Attach grade scale to courses.') ?></td>
                        </tr>
                        <tr>
                            <td>
                                <?= $this->Form->control('academic_year', [
                                    'label' => __('Academic Year'),
                                    'type' => 'select',
                                    'options' => $acyear_array_data,
                                    'empty' => '--Select Academic Year--',
                                    'default' => $this->request->getData('PublishedCourse.academic_year', $defaultacademicyear)
                                ]) ?>
                            </td>
                            <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT): ?>
                                <td>
                                    <?= $this->Form->control('program_id', [
                                        'label' => __('Program'),
                                        'type' => 'select',
                                        'empty' => '--Select Program--'
                                    ]) ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <td>
                                <?= $this->Form->control('semester', [
                                    'label' => false,
                                    'type' => 'select',
                                    'options' => ['I' => 'I', 'II' => 'II', 'III' => 'III'],
                                    'empty' => '--select semester--'
                                ]) ?>
                            </td>
                            <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT): ?>
                                <td>
                                    <?= $this->Form->control('year_level_id', [
                                        'label' => false,
                                        'type' => 'select'
                                    ]) ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?= $this->Form->button(__('Continue'), [
                                    'type' => 'submit',
                                    'name' => 'getPublishedCourseList',
                                    'class' => 'btn btn-primary btn-sm'
                                ]) ?>
                            </td>
                        </tr>
                    </table>
                <?php endif; ?>

                <?php if (isset($section_organized_published_courses) &&
                !empty($section_organized_published_courses) &&
                isset($gradeScales) && !empty($gradeScales)): ?>

                    <h2><?= __('Select scale you want to attach for the given course.') ?></h2>
                    <table class="table table-bordered">
                        <?php $count = 0; $hide_button = 0; ?>
                        <?php foreach ($section_organized_published_courses
                        as $section_name => $sectioned_published_courses):
                            ?>
                        <tr>
                            <td colspan="7"><h3><?= h($section_name) ?></h3></td>
                        </tr>
                        <tr>
                            <th></th>
                            <th><?= __('S.No') ?></th>
                            <th><?= __('Course Title') ?></th>
                            <th><?= __('Course Code') ?></th>
                            <th><?= __('Course Credit') ?></th>
                            <th><?= __('Scale') ?></th>
                        </tr>
                        <?php $i = 0; $ser_number = 1; ?>
                        <?php foreach ($sectioned_published_courses as $publishedCourse): ?>
                        <?php $class = ($i++ % 2 == 0) ? ' class="altrow"' : ''; ?>
                        <tr<?= $class ?>>
                            <td>
                                <?= $this->Form->hidden("Published.$count.id",
                                    ['value' => $publishedCourse['PublishedCourse']['id']]) ?>
                            </td>
                            <td><?= h($ser_number++) ?></td>
                            <td><?= h($publishedCourse['Course']['course_title']) ?></td>
                            <td><?= h($publishedCourse['Course']['course_code']) ?></td>
                            <td><?= h($publishedCourse['Course']['credit']) ?></td>
                            <td>
                                <?= $this->Form->hidden("Published.$count.id", ['value' => $publishedCourse['PublishedCourse']['id']]) ?>
                                <?php if (!$publishedCourse['PublishedCourse']['scale_readOnly']): ?>
                                    <?php if (isset($gradeScales[$publishedCourse['Course']['grade_type_id']])): ?>
                                        <?= $this->Form->control("Published.$count.grade_scale_id", [
                                            'options' => $gradeScales[$publishedCourse['Course']['grade_type_id']],
                                            'label' => false,
                                            'empty' => '--select scale--',
                                            'default' => ($publishedCourse['PublishedCourse']['grade_scale_id'] != 0 && $publishedCourse['PublishedCourse']['grade_scale_id'] != '') ? $publishedCourse['PublishedCourse']['grade_scale_id'] : '',
                                            'onchange' => "updateGradeScaleDetail($count)",
                                            'id' => "grade_scale_$count"
                                        ]) ?>
                                    <?php else: ?>
                                        <span class="fs16">
                                                        <?= __('The course is attached to %s grade type, but grade scale is not defined for using this grade type.', h($gradeTypes[$publishedCourse['Course']['grade_type_id']])) ?>
                                                        <?= $this->Html->link(
                                                            __('Click Here To Define'),
                                                            ['controller' => 'GradeScales', 'action' => 'add'],
                                                            ['class' => 'text-primary']
                                                        ) ?>
                                                    </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                <?php if ($publishedCourse['PublishedCourse']['grade_scale_id'] != 0): ?>
                                    <?php $hide_button++; ?>
                                    <span><?= __('Grade has been submitted, you cannot detach or attach scale') ?></span>
                                    <input type="button" value="Show Grade Scale" onclick="showHideGradeScale(<?= $publishedCourse['PublishedCourse']['id'] ?>, <?= $count ?>)" id="ShowHideGradeScale">
                                <?php else: ?>
                                    <span><?= __('Grade has been submitted, you cannot attach/detach scale') ?></span>
                                <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" id="grade_scale_detail_<?= $count ?>" style="text-align: right;"></td>
                        </tr>
                        <?php $count++; ?>
                        <?php endforeach; ?>
                        <?php
                        endforeach;

                        ?>
                    </table>
                    <table class="table">
                        <tr>
                            <td style="padding:0">
                                <?php if ($hide_button != $count): ?>
                                    <?= $this->Form->button(__('Attach/Deattach Scale'), [
                                        'type' => 'submit',
                                        'name' => 'attachescaletocourse',
                                        'class' => 'btn btn-primary btn-sm'
                                    ]) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                <?= $this->Form->end() ?>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function updateGradeScaleDetail(id) {
        var gradeDetail = $("#grade_scale_" + id).val();
        var formUrl = '<?= $this->Url->build(['controller' => 'GradeScaleDetails', 'action' => 'getGradeScaleDetail']) ?>/' + encodeURIComponent(gradeDetail);
        $.ajax({
            type: 'GET',
            url: formUrl,
            data: { gradeDetail: gradeDetail },
            success: function(data) {
                $("#grade_scale_detail_" + id).empty().append(data);
            },
            error: function(xhr, textStatus, error) {
                alert(textStatus);
            }
        });
        return false;
    }

    function showHideGradeScale(id, count) {
        var button = $("#ShowHideGradeScale");
        if (button.val() === 'Show Grade Scale') {
            var p_course_id = id;
            $("#grade_scale_detail_" + count).empty().append('Loading ...');
            var formUrl = '<?= $this->Url->build(['controller' => 'PublishedCourses', 'action' => 'getCourseGradeScale']) ?>/' + encodeURIComponent(p_course_id);
            $.ajax({
                type: 'GET',
                url: formUrl,
                data: { p_course_id: p_course_id },
                success: function(data) {
                    $("#grade_scale_detail_" + count).empty().append(data);
                    button.val('Hide Grade Scale');
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });
        } else {
            $("#grade_scale_detail_" + count).empty();
            button.val('Show Grade Scale');
        }
        return false;
    }
</script>

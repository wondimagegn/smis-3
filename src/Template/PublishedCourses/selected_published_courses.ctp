<?php
/**
 * @var \App\View\AppView $this
 * @var array $coursesss
 * @var array $taken_courses_allow_to_publish_it
 * @var array $selected_section
 * @var array $published_courses_disable_not_to_published
 */
use Cake\ORM\TableRegistry;
?>

<?php if ($this->getRequest()->getSession()->read('candidate_publish_courses')) : ?>
    <?php
    $coursesss = $this->getRequest()->getSession()->read('candidate_publish_courses');
    $taken_courses_allow_to_publish_it = $this->getRequest()->getSession()->read('taken_courses_allow_to_publish_it');
    $selected_section = $this->getRequest()->getSession()->read('selected_section');
    $published_courses_disable_not_to_published = $this->getRequest()->getSession()->read('published_courses_disable_not_to_published');

    if (!empty($coursesss)) : ?>
        <hr>
        <blockquote>
            <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
            <p style="text-align:justify;">
                <span class="fs16 text-black">Use <b>Publish selected as Add</b> button in situations where there is a missed course which should be published for a given academic year and semester.</span>
            </p>
        </blockquote>
        <hr>

        <?php
        $display_button = 0;
        $section_count = 0;
        foreach ($coursesss as $section_id => $coursss) :
            $section_count++;
            if (!empty($coursss)) : ?>
                <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                <br>
                <div style="overflow-x:auto;">
                    <table id="fieldsForm" cellpadding="0" cellspacing="0" class="table">
                        <thead>
                        <tr>
                            <th colspan="10"><?= h("Section: " . $selected_section[$section_id]) ?></th>
                        </tr>
                        <tr>
                            <th colspan="10"><?= h("Select the course you want to publish.") ?></th>
                        </tr>
                        <tr>
                            <th class="center" style="width: 4%;">&nbsp;</th>
                            <th class="center">#</th>
                            <th class="center">Year</th>
                            <th class="center">SEM</th>
                            <th class="vcenter">Course Title</th>
                            <th class="center">Course Code</th>
                            <th class="center">Prerequisite</th>
                            <th class="center">Credit</th>
                            <th class="center">L T L</th>
                            <th class="center" style="width:7%;">Elective</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 1;
                        foreach ($coursss as $vc) :?>
                            <tr>
                                <?php if (isset($published_courses_disable_not_to_published[$section_id])
                                    && in_array($vc->id,
                                        $published_courses_disable_not_to_published[$section_id])) : ?>
                                    <td class="center">**</td>
                                <?php else : ?>
                                    <td class="center"><div style="padding-left: 25%;"><?= $this->Form->checkbox('Course.' . $section_id .
                                                '.' . $vc->id) ?></div></td>
                                <?php endif; ?>
                                <td class="center"><?= $count ?></td>
                                <td class="center"><?= h($vc->year_level->name) ?></td>
                                <td class="center"><?= h($vc->semester) ?></td>
                                <td class="vcenter"><?= h($vc->course_title) ?></td>
                                <td class="center"><?= h($vc->course_code) ?></td>
                                <td class="center">
                                    <?php if (!empty($vc->prerequisites)) : ?>
                                        <?php foreach ($vc->prerequisites as $pvlll) : ?>
                                            <?php
                                            if (!empty($pvlll->prerequisite_course_id)) {
                                                $coursesTable = TableRegistry::getTableLocator()->get('Courses');

                                                $pre_code = $coursesTable->find()
                                                    ->select(['course_code'])
                                                    ->where(['Courses.id' => (int)$pvlll->prerequisite_course_id])
                                                    ->first();
                                                if ($pre_code) {
                                                    echo h($pre_code->course_code) . " ";
                                                }

                                            }
                                            ?>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        none
                                    <?php endif; ?>
                                </td>
                                <td class="center"><?= h($vc->credit) ?></td>
                                <td class="center"><?= h($vc->course_detail_hours) ?></td>
                                <?php
                                if (isset($published_courses_disable_not_to_published[$section_id]) && in_array($vc->id,
                                        $published_courses_disable_not_to_published[$section_id])) :
                                    echo '<td class="center">**</td>';
                                else :
                                    echo '<td><div style="padding-left: 40%;">' . $this->Form->checkbox('Elective.' . $section_id . '.' . $vc->id, [
                                            'checked' => ($vc->elective == 1)
                                        ]) . '</div></td>';
                                endif;
                                ?>
                            </tr>
                            <?php $count++; ?>
                        <?php endforeach; ?>
                        </tbody>
                        <?php if (isset($published_courses_disable_not_to_published[$section_id]) && count($published_courses_disable_not_to_published[$section_id]) > 0) : ?>
                            <tfoot>
                            <tr>
                                <td class="center">**</td>
                                <td colspan="9" class="vcenter">Those courses with two asterisks are courses that are already published for the selected section using the given search criteria.</td>
                            </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
                <br>
            <?php else :
                $display_button++;
            endif; ?>
        <?php endforeach; ?>

        <?php if ($display_button != $section_count) : ?>
            <hr>
            <div class="row">
                <div class="large-4 columns">
                    <?= $this->Form->button('Publish Selected', [
                        'id' => 'publishSelected',
                        'name' => 'publishselected',
                        'class' => 'tiny radius button bg-blue'
                    ]) ?>
                </div>
                <div class="large-8 columns">
                    <?php if (defined('ALLOW_PUBLISH_AS_ADD_COURSE_FOR_DEPARTMENT_ROLE') && ALLOW_PUBLISH_AS_ADD_COURSE_FOR_DEPARTMENT_ROLE) : ?>
                        <?= $this->Form->button('Publish as Mass Add', [
                            'id' => 'publishSelectedAsAdd',
                            'name' => 'publishselectedasadd',
                            'class' => 'tiny radius button bg-red'
                        ]) ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($taken_courses_allow_to_publish_it) && count($taken_courses_allow_to_publish_it) > 0 && false) : ?>
            <?php foreach ($taken_courses_allow_to_publish_it as $section_id => $coursss) :
                if (!empty($coursss)) : ?>
                    <div class="info-box info-message">
                        <span></span> Already taken courses of the selected section.
                        You can republish courses to allow students to register
                        for the courses again. This happens when all students fail
                        the course or are not able to follow the courses.
                    </div>
                    <div style="overflow-x:auto;">
                        <table id="fieldsForm" cellpadding="0" cellspacing="0" class="table">
                            <thead>
                            <tr>
                                <th colspan="10"><?= h("Section: " . $selected_section[$section_id]) ?></th>
                            </tr>
                            <tr>
                                <th class="center" style="width: 4%;">&nbsp;</th>
                                <th class="center">#</th>
                                <th class="center">Year</th>
                                <th class="center">SEM</th>
                                <th class="vcenter">Course Title</th>
                                <th class="center">Course Code</th>
                                <th class="center">Prerequisite</th>
                                <th class="center">Credit</th>
                                <th class="center">L T L</th>
                                <th class="center" style="width: 7%;">Elective</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $count = 1;
                            foreach ($coursss as $vc) : ?>
                                <tr>
                                    <td class="center"><div style="padding-left: 15%;"><?= $this->Form->checkbox('Course.' . $section_id . '.' . $vc->course->id) ?></div></td>
                                    <td class="center"><?= $count ?></td>
                                    <td class="center"><?= h($vc->year_level->name) ?></td>
                                    <td class="center"><?= h($vc->semester) ?></td>
                                    <td class="vcenter"><?= h($vc->course_title) ?></td>
                                    <td class="center"><?= h($vc->course_code) ?></td>
                                    <td class="center">
                                        <?php if (!empty($vc->prerequisites)) : ?>
                                            <?php foreach ($vc->prerequisites as $pvlll) : ?>
                                                <?php if (!empty($pvlll->pre_code)) : ?>
                                                    <?= h($pvlll->pre_code) ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            none
                                        <?php endif; ?>
                                    </td>
                                    <td class="center"><?= h($vc->credit) ?></td>
                                    <td class="center"><?= h($vc->course_detail_hours) ?></td>
                                    <td class="center"></td>
                                </tr>
                                <?php $count++; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
<?= $this->Form->end() ?>
<script type="text/javascript">
    var form_being_submitted = false;
    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    $(document).ready(function() {
        $('#publishSelectedAsAdd').click(function() {
            var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="Course["]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
            var chckboxs = document.querySelectorAll('input[type="checkbox"][name^="Course["]:checked');

            if (!checkedOne || chckboxs.length == 0) {
                alert('At least one course must be selected to publish as mass add.');
                return false;
            }

            if (form_being_submitted) {
                alert("Publishing Selected as Mass Add, please wait a moment...");
                $('#publishSelectedAsAdd').prop('disabled', true);
                return false;
            }

            var confirmed = confirm('Are you sure you want to publish the selected courses as Mass Add for the selected section? Use this option if and only if there is a previous course publication for the section using the same academic year and semester with section students already registered for the courses or you are unable to publish the courses using Publish Selected option, i.e., if you forgot to publish the courses for the section in the given academic year and semester or the students are taking the courses as a block course.');
            if (confirmed) {
                $('#publishSelectedAsAdd').val('Publishing as Mass Add...');
                form_being_submitted = true;
                return true;
            } else {
                return false;
            }
        });

        $('#publishSelected').click(function() {
            var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="Course["]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
            var chckboxs = document.querySelectorAll('input[type="checkbox"][name^="Course["]:checked');

            if (!checkedOne || chckboxs.length == 0) {
                alert('At least one course must be selected to publish.');
                validationMessageNonSelected.innerHTML = 'At least one course must be selected to publish.';
                return false;
            }

            if (form_being_submitted) {
                alert("Publishing Selected Courses, please wait a moment...");
                $('#publishSelected').prop('disabled', true);
                return false;
            }

            $('#publishSelected').val('Publishing Selected Courses...');
            form_being_submitted = true;
            return true;
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });
</script>

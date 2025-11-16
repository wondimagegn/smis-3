<?php
$this->assign('title', __('Published Courses for Exam Merged Sections'));

$list_of_courses = $this->request->getSession()->read('list_of_courses');
$formatted_list_of_courses_per_sections = [];

if (!empty($list_of_courses)) {
    foreach ($list_of_courses as $lcv) {
        $formatted_list_of_courses_per_sections[$lcv['Section']['name']][] = $lcv;
    }
}
?>

<?php if (!empty($list_of_courses)): ?>
    <?= $this->Form->create('MergedSectionsExam', ['url' => ['controller' => 'PublishedCourses', 'action' => 'mergeSectionsForExam']]) ?>
    <table id="fieldsForm" class="table table-bordered">
        <tbody>
        <?php foreach ($formatted_list_of_courses_per_sections as $flck => $formatted_list_of_courses): ?>
            <tr>
                <td colspan="5" class="font-weight-bold"><?= __('Section Name: %s', h($flck)) ?></td>
            </tr>
            <?php
            $count = 1;
            $options = [];
            foreach ($formatted_list_of_courses as $list_of_course) {
                $options[$list_of_course['PublishedCourse']['id']] = sprintf(
                    '%d. %s - %s - Chr.%s',
                    $count++,
                    h($list_of_course['Course']['course_title']),
                    h($list_of_course['Course']['course_code']),
                    h($list_of_course['Course']['credit'])
                );
                ?>
                <tr style="display: none;">
                    <td>
                        <?= $this->Form->hidden("MergedSectionsExams.{$list_of_course['PublishedCourse']['id']}.published_course_id", [
                            'value' => $list_of_course['PublishedCourse']['id']
                        ]) ?>
                        <?= $this->Form->hidden("MergedSectionsExams.{$list_of_course['PublishedCourse']['id']}.section_id", [
                            'value' => $list_of_course['Section']['id']
                        ]) ?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td class="font-weight-bold">
                    <?= $this->Form->radio("MergedSectionsExams.selectedcourses.{$flck}", $options, [
                        'legend' => false,
                        'label' => false,
                        'separator' => '<br/>'
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="5">
                <?= $this->Form->control('merged_section_name', [
                    'label' => false,
                    'placeholder' => __('Merged Section Name')
                ]) ?>
            </td>
        </tr>
        <tr>
            <td colspan="5">
                <?= $this->Form->button(__('Merge Selected Sections'), [
                    'type' => 'submit',
                    'name' => 'merge',
                    'value' => 'merge',
                    'class' => 'btn btn-primary btn-sm'
                ]) ?>
            </td>
        </tr>
        </tbody>
    </table>
    <?= $this->Form->end() ?>
<?php else: ?>
    <div class="alert alert-info">
        <?= __('Please select section that have course for final exam.') ?>
    </div>
<?php endif; ?>

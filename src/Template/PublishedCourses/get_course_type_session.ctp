<?php if (isset($publishedcourse_data) && !empty($publishedcourse_data)): ?>
    <table class="table table-bordered">
        <tr>
            <td colspan="3" class="font-weight-bold"><?= __('Select Course Number of Session') ?></td>
        </tr>
        <tr>
            <?php if ($publishedcourse_data[0]['Course']['lecture_hours'] > 0): ?>
                <?php
                $session_array = [];
                for ($i = 1; $i <= $publishedcourse_data[0]['Course']['lecture_hours']; $i++) {
                    $session_array[$i] = $i;
                }
                ?>
                <td>
                    <?= $this->Form->control('lecture_number_of_session', [
                        'type' => 'select',
                        'options' => $session_array,
                        'label' => false
                    ]) ?>
                </td>
            <?php endif; ?>
            <?php if ($publishedcourse_data[0]['Course']['tutorial_hours'] > 0): ?>
                <?php
                $session_array = [];
                for ($i = 1; $i <= $publishedcourse_data[0]['Course']['tutorial_hours']; $i++) {
                    $session_array[$i] = $i;
                }
                ?>
                <td>
                    <?= $this->Form->control('tutorial_number_of_session', [
                        'type' => 'select',
                        'options' => $session_array,
                        'label' => false
                    ]) ?>
                </td>
            <?php endif; ?>
            <?php if ($publishedcourse_data[0]['Course']['laboratory_hours'] > 0): ?>
                <?php
                $session_array = [];
                for ($i = 1; $i <= $publishedcourse_data[0]['Course']['laboratory_hours']; $i++) {
                    $session_array[$i] = $i;
                }
                ?>
                <td>
                    <?= $this->Form->control('lab_number_of_session', [
                        'type' => 'select',
                        'options' => $session_array,
                        'label' => false
                    ]) ?>
                </td>
            <?php endif; ?>
        </tr>
    </table>
<?php endif; ?>

<?php
$this->assign('title', __('Downgrade Year Level of Section'));
?>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <h2 class="box-title"><?= __('Downgrade Year Level of Section') ?></h2>
                <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'downgradeSections']]) ?>
                <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT): ?>
                    <div class="text-center font-weight-bold"><?= __('Downgrade Section') ?></div>
                    <div class="font-weight-bold"><?= h($college_name) ?></div>
                    <div class="font-weight-bold"><?= __('Department of %s', h($department_name)) ?></div>
                    <div class="alert alert-info">
                        <u><span class="text-danger"><?= __('Beware:') ?></span> <?= __('Downgrade a given section only if necessary.') ?></u>
                        <br>
                        - <?= __('You are advised to use downgrade only if you upgraded a section by mistake.') ?><br>
                        - <?= __('To downgrade a given section, the section must not have any published courses.') ?><br>
                        - <?= __('Here you get only potentially downgradable sections as options.') ?>
                    </div>
                    <table class="table table-bordered">
                        <?php
                        if (!empty($yearLevels)) {
                            $key = array_search('1st', $yearLevels);
                            if ($key !== false) {
                                unset($yearLevels[$key]);
                            }
                        }
                        ?>
                        <tr>
                            <td style="width: 250px;">
                                <?= $this->Form->control('program_id', [
                                    'label' => false,
                                    'type' => 'select',
                                    'empty' => '--Select Program--',
                                    'class' => 'form-control'
                                ]) ?>
                            </td>
                            <td style="width: 400px;">
                                <?= $this->Form->control('program_type_id', [
                                    'label' => false,
                                    'type' => 'select',
                                    'empty' => '--Select Program Type--',
                                    'class' => 'form-control'
                                ]) ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 400px;">
                                <?= $this->Form->control('academicyear', [
                                    'label' => false,
                                    'type' => 'select',
                                    'options' => $acyear_array_data,
                                    'empty' => '--Select Academic Year--',
                                    'class' => 'form-control'
                                ]) ?>
                            </td>
                            <td style="width: 250px;">
                                <?= $this->Form->control('year_level_id', [
                                    'label' => false,
                                    'type' => 'select',
                                    'options' => $yearLevels,
                                    'empty' => '--Select Year Level--',
                                    'class' => 'form-control'
                                ]) ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <?= $this->Form->button(__('Search'), [
                                    'type' => 'submit',
                                    'name' => 'search',
                                    'class' => 'btn btn-primary btn-sm'
                                ]) ?>
                            </td>
                        </tr>
                    </table>
                    <?php if (!empty($formateddowngradableSections)): ?>
                        <table class="table table-bordered">
                            <tr>
                                <?php foreach ($formateddowngradableSections as $k => $v): ?>
                                    <td>
                                        <?= $this->Form->control("Downgradable_Selected.{$k}", [
                                            'class' => 'downgradableSelectedSection',
                                            'type' => 'checkbox',
                                            'value' => $k,
                                            'label' => h($v)
                                        ]) ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <td>
                                    <?= $this->Form->button(__('Downgrade'), [
                                        'type' => 'submit',
                                        'name' => 'downgrade',
                                        'id' => 'downgradeSection',
                                        'class' => 'btn btn-primary btn-sm',
                                        'onclick' => 'return confirm("Are you sure you want to downgrade selected section?")'
                                    ]) ?>
                                </td>
                            </tr>
                        </table>
                    <?php elseif (empty($formateddowngradableSections) && !$isbeforesearch): ?>
                        <div class="alert alert-info">
                            <span></span>
                            <?= __('There is no section to downgrade in the search criteria.') ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

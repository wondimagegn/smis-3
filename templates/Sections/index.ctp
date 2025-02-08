<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('List of Sections'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create('Section'); ?>
                <div style="margin-top: -30px;">
                    <hr>
                    <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                        <!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-3 columns">
                                <?= $this->Form->input('Section.academicyearSection', array('id' => 'academicyearSearch', 'label' => 'Academic Year: ', 'type' => 'select', 'options' => $acyear_array_options, /* 'empty' => "All", */ 'style' => 'width:90%;', 'default' => $selected_academic_year)); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('Section.year_level_id', array('label' => 'Year Level: ', /* 'id' => 'ajax_year_level_s',  */'empty' => '[ All Year Levels ]', 'style' => 'width:80%;')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('Section.program_id', array('label' => 'Program: ', 'empty' => '[ All Programs ]', 'style' => 'width:90%;')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('Section.program_type_id', array('label' => 'Program Type: ', 'empty' => '[ All Program Types ]', 'style' => 'width:90%;')); ?>
                            </div>
                        </div>

                        <?php
                        if ($role_id == ROLE_COLLEGE && $onlyFreshman == 0) { ?>
                            <div class="row">
                                <div class="large-6 columns">
                                    <?= $this->Form->input('Section.department_id', array('label' => 'Department: ', 'id' => 'ajax_department_id_section', 'empty' => '[ All Departments ]', 'style' => 'width:90%;')); ?>
                                </div>
                                <div class="large-2 columns">
									<?= $this->Form->input('Section.section_name', array('label' => 'Section Name:', 'placeholder' => 'Leave empty or specify', 'default' => $name, 'style' => 'width:90%;')); ?>
								</div>
                                <div class="large-2 columns">
									<?= $this->Form->input('Section.active', array('label' => 'Section Status: ', 'id' => 'active ', 'type' => 'select', 'options' => array('0' => 'Active', '1' => 'Archived'), 'empty' => '[ All ]', 'style' => 'width:90%;')); ?>
								</div>
								<div class="large-2 column">
									<?= $this->Form->input('Section.limit', array('id' => 'limit ', 'type' => 'number', 'min' => '100',  'max' => '500', 'value' => $limit, 'step' => '50', 'label' => ' Limit: ', 'style' => 'width:85%;')); ?>
								</div>
                            </div>
                            <?php
                        } else if ($role_id == ROLE_REGISTRAR && $onlyFreshman == 0) { ?>
                            <div class="row">
                                <div class="large-6 columns">
                                    <?= $this->Form->input('Section.department_id', array('label' => 'Department: ', 'id' => 'ajax_department_id_section', 'empty' => '[ All Assigned Departments ]', 'style' => 'width:90%;')); ?>
                                </div>
                                <div class="large-2 columns">
									<?= $this->Form->input('Section.section_name', array('label' => 'Section Name:', 'placeholder' => 'Leave empty or specify', 'default' => $name, 'style' => 'width:90%;')); ?>
								</div>
                                <div class="large-2 columns">
									<?= $this->Form->input('Section.active', array('label' => 'Section Status: ', 'id' => 'active ', 'type' => 'select', 'options' => array('0' => 'Active', '1' => 'Archived'), 'empty' => '[ All ]', 'style' => 'width:90%;')); ?>
								</div>
								<div class="large-2 columns">
									<?= $this->Form->input('Section.limit', array('id' => 'limit ', 'type' => 'number', 'min' => '100',  'max' => '500', 'value' => $limit, 'step' => '50', 'label' => ' Limit: ', 'style' => 'width:85%;')); ?>
								</div>
                            </div>
                            <?php
                        } else if ($role_id == ROLE_DEPARTMENT) { ?>
                            <div class="row">
                                <div class="large-3 columns">
									<?= $this->Form->input('Section.section_name', array('label' => 'Section Name:', 'placeholder' => 'Leave empty or specify', 'default' => $name, 'style' => 'width:90%;')); ?>
								</div>
                                <div class="large-3 columns">
									<?= $this->Form->input('Section.active', array('label' => 'Section Status: ', 'id' => 'active ', 'type' => 'select', 'options' => array('0' => 'Active', '1' => 'Archived'), 'empty' => '[ All ]', 'style' => 'width:90%;')); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Section.limit', array('id' => 'limit ', 'type' => 'number', 'min' => '100',  'max' => '500', 'value' => $limit, 'step' => '50', 'label' => ' Limit: ', 'style' => 'width:90%;')); ?>
								</div>
                                <div class="large-6 columns">
                                    &nbsp;
                                </div>
                            </div>
                            <?php
                        } else if ($onlyFreshman == 1) { ?>
                            <div class="row">
                                <div class="large-6 columns">
                                    <?= $this->Form->input('Section.college_id', array('label' => 'College: ', 'empty' => '[ All Assigned Colleges ]', 'style' => 'width:90%;')); ?>
                                </div>
                                <div class="large-2 columns">
									<?= $this->Form->input('Section.section_name', array('label' => 'Section Name:', 'placeholder' => 'Leave empty or specify', 'default' => $name, 'style' => 'width:90%;')); ?>
								</div>
                                <div class="large-2 columns">
									<?= $this->Form->input('Section.active', array('label' => 'Section Status: ', 'id' => 'active ', 'type' => 'select', 'options' => array('0' => 'Active', '1' => 'Archived'), 'empty' => '[ All ]', 'style' => 'width:90%;')); ?>
								</div>
								<div class="large-2 columns">
									<?= $this->Form->input('Section.limit', array('id' => 'limit ', 'type' => 'number', 'min' => '100',  'max' => '500', 'value' => $limit, 'step' => '50', 'label' => ' Limit: ', 'style' => 'width:85%;')); ?>
								</div>
                            </div>
                            <?php
                        } ?>

                        <?= (isset($this->data['Section']['page']) ? $this->Form->hidden('page', array('value' => $this->data['Section']['page'])) : ''); ?>
						<?= (isset($this->data['Section']['sort']) ? $this->Form->hidden('sort', array('value' => $this->data['Section']['sort'])) : ''); ?>
						<?= (isset($this->data['Section']['direction']) ? $this->Form->hidden('direction', array('value' => $this->data['Section']['direction'])) : ''); ?>
                        <hr>
                        <?= $this->Form->Submit('Search', array('name' => 'search', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>

                    </fieldset>
                </div>
                <hr>
                <br>

                <?php
                if (isset($sections) && !empty($sections)) { ?>
                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                                <tr>
                                    <td class="center">#</td>
                                    <td class="vcenter">Section</td>
                                    <td class="center">Year Level</td>
                                    <td class="center">Department</td>
                                    <td class="center">Program</td>
                                    <td class="center">Program Type</td>
                                    <td class="center">ACY</td>
                                    <td class="center"><?= __('Actions'); ?></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $start = $this->Paginator->counter('%start%');
                                foreach ($sections as $section) { ?>
                                    <tr>
                                        <td class="center"><?= $start++; ?></td>
                                        <td class="vcenter"><?= $section['Section']['name']; ?></td>
                                        <td class="center"><?= (isset($section['YearLevel']['name']) ? $section['YearLevel']['name'] : ($section['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')); ?></td>
                                        <td class="center"><?= (isset($section['Department']['name']) ? $section['Department']['name'] : $section['College']['shortname'] . ($section['Section']['program_id'] == PROGRAM_REMEDIAL ? ' - Remedial' : ' - Pre/Freshman')); ?></td>
                                        <td class="center"><?= $section['Program']['shortname']; ?></td>
                                        <td class="center"><?= $section['ProgramType']['name']; ?></td>
                                        <td class="center"><?= $section['Section']['academicyear']; ?></td>
                                        <td class="center">
                                            <?= $this->Html->link(__(''), array('action' => 'view', $section['Section']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?> &nbsp;
                                            <?php 
                                            if (!$section['Section']['archive']) {
                                                if ((is_null($section['Section']['department_id']) || !isset($section['Section']['department_id'])) && $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $this->Session->read('Auth.User')['is_admin'] == 1) { ?>
                                                    <?= $this->Html->link(__(''), array('action' => 'edit', $section['Section']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
                                                    <?= $this->Html->link(__(''), array('action' => 'delete', $section['Section']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s section?'), $section['Section']['name'])); ?>
                                                    <?php
                                                } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && isset($section['Section']['department_id']) && $this->Session->read('Auth.User')['is_admin'] == 1) { ?>
                                                    <?= $this->Html->link(__(''), array('action' => 'edit', $section['Section']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
                                                    <?= $this->Html->link(__(''), array('action' => 'delete', $section['Section']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s section?'), $section['Section']['name'])); ?>
                                                    <?php
                                                }
                                            } ?>
                                        </td>
                                    </tr>
                                    <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <br>

                    <hr>
                    <div class="row">
                        <div class="large-5 columns">
                            <?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total'))); ?>
                        </div>
                        <div class="large-7 columns">
                            <div class="pagination-centered">
                                <ul class="pagination">
                                    <?= $this->Paginator->prev('<< ' . __(''), array('tag' => 'li'), null, array('class' => 'arrow unavailable')); ?> <?= $this->Paginator->numbers(array('separator' => '', 'tag' => 'li')); ?> <?= $this->Paginator->next(__('') . ' >>', array('tag' => 'li'), null, array('class' => 'arrow unavailable')); ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php
                } ?>

                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
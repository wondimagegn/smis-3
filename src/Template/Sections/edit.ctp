<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= 'Edit Section Details: ' . (isset($section['Section']['name']) ? $section['Section']['name'] . (isset($section['YearLevel']['name']) ? ' (' . $section['YearLevel']['name'] . ', ' . $section['Section']['academicyear'] . ') ' : ' (Pre/1st) in ' . $section['Section']['academicyear']) : ''); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?php //echo debug($this->request->data); ?>
                <?php
				if (!empty($section)) { ?>
                    <div style="margin-top: -30px;">
                        <?= $this->Form->create('Section'); ?>
                        <fieldset style="padding-bottom: 0px;">
                            <!-- <legend>&nbsp;&nbsp; <?php //echo __('Edit Section'); ?> &nbsp;&nbsp;</legend> -->
                            <div class="row">
                                <div class="large-3 columns">
                                    <?= $this->Form->input('id'); ?>
                                    <?= $this->Form->input('name', array('value' => trim($this->request->data['Section']['name']), 'style' => 'width: 90%;', 'type' => 'text', 'maxlength' => "30")); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('program_id', array('style' => 'width: 90%;', 'disabled')); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('program_type_id', array('style' => 'width: 90%;', 'disabled')); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?php
                                    if (isset($this->request->data['Section']['department_id']) && !empty($this->request->data['Section']['department_id'])) {
                                        echo $this->Form->input('department_id', array('style' => 'width: 90%;', 'disabled'));
                                    } else if (isset($this->request->data['Section']['college_id']) && !empty($this->request->data['Section']['college_id'])) {
                                        echo $this->Form->input('college_id', array('style' => 'width: 90%;', 'disabled'));
                                    } ?>
                                    <?= $this->Form->hidden('year_level_id'); ?>
                                    <?= $this->Form->hidden('academicyear'); ?>
                                    <?= $this->Form->hidden('program_id'); ?>
                                    <?= $this->Form->hidden('program_type_id'); ?>
                                </div>
                            </div>
                            <hr>
                            <?= $this->Form->Submit('Submit', array('name' => 'submit', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
                            <?= $this->Form->end(); ?>
                        </fieldset>
                    </div>
                    <?php
                }  else { ?>
					<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Section not found or you don't have the privilage to view the selected Section. </div>
                    <?php
				} ?>
            </div>
        </div>
    </div>
</div>
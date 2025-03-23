<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('List Graduates'); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns" style=" margin-top: -30px;">
                <?= $this->Form->Create('GraduateList', array('action' => 'search')); ?>
                <div class="graduateLists index">
                    <hr>
                    <?php
                    $yFrom = Configure::read('Calendar.graduateListStartYear');
                    $yTo = date('Y');
                    ?>
                    <fieldset style="padding-bottom: 0px;padding-top: 15px;">
                        <!-- <legend>&nbsp;&nbsp; Search &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-3 columns">
                                <?= $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs13', 'label' => 'Program:', 'type' => 'select', 'options' => $programs, 'default' => $default_program_id, 'style' => 'width:90%')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs13', 'label' =>' Program Type: ', 'type' => 'select', 'options' => $program_types, 'default' => $default_program_type_id, 'style' => 'width:90%')); ?>
                            </div>
                            <div class="large-6 columns">	
                                <?= $this->Form->input('department_id', array('id' => 'Department', 'class' => 'fs13', 'label' => 'College/Department: ', 'type' => 'select', 'options' => $departments, 'default' => $default_department_id, 'style' => 'width:90%')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="large-6 columns">
                                <?= $this->Form->input('graduate_date_from', array('label' => 'Graduate From: ', 'type' => 'date', 'minYear' => $yFrom, 'maxYear' => $yTo, 'default' => false, 'style' => 'width:25%')); ?>
                            </div>
                            <div class="large-6 columns">
                                <?= $this->Form->input('graduate_date_to', array('label' => 'Graduate To: ', 'type' => 'date', 'minYear' => $yFrom, 'maxYear' => $yTo, 'default' =>  date('Y-m-d'), 'style' => 'width:25%')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="large-3 columns">
                                <?= $this->Form->input('minute_number', array('id' => 'MinuteNumber', 'class' => 'fs13', 'label' => 'Minute No.', 'maxlength' => 50, 'style' => 'width:90%')); ?> 
                            </div>
                            <div class="large-3 columns">	
                                <?= $this->Form->input('limit', array('id' => 'limit ', 'type' => 'number', 'min' => '100',  'max' => '50000', 'value' => (!empty($selectedLimit) ? $selectedLimit : ''), 'step' => '100', 'class' => 'fs13', 'label' =>'Limit: ', 'style' => 'width:45%')); ?>

                                <?= (isset($this->data['GraduateList']['page']) ? $this->Form->hidden('page', array('value' => $this->data['GraduateList']['page'])) : ''); ?>
								<?= (isset($this->data['GraduateList']['sort']) ? $this->Form->hidden('sort', array('value' => $this->data['GraduateList']['sort'])) : ''); ?>
								<?= (isset($this->data['GraduateList']['direction']) ? $this->Form->hidden('direction', array('value' => $this->data['GraduateList']['direction'])) : ''); ?>
                                
                            </div>
                            <div class="large-6 columns">
                                &nbsp;
                            </div>
                        </div>
                        <hr>
                        <?= $this->Form->submit(__('Search'), array('name' => 'listStudentsForGraduateList', 'class' => 'tiny radius button bg-blue', 'div' => false )); ?>
                    </fieldset>
                    <hr>
                    
                    <?php
                    if (!empty($graduateLists)) {
                        if ($this->Session->check('graduateLists_for_export')) { ?>
                            <div class="row">
                                <div class="large-4 columns">
                                    <?= $this->Html->link($this->Html->image("/img/csv_icon.png", array("alt" => "Export TO CSV")) . ' &nbsp; Export Report to Excel (CSV)', array('action' => 'download_csv'), array('escape' => false)); ?>
                                    <?php // $this->Form->submit(__('Export to Excel(CSV)', true), array('name' => 'exportToExcel','div' => false, 'class' => 'tiny radius button bg-blue', 'onclick' => '')); ?>
                                </div>
                            </div>

                            <hr>
                            <?php
                        } ?>

                        <?php //debug($graduateLists[0]); ?>

                        <div style="overflow-x:auto;">
                            <table cellpadding="0" cellspacing="0" class="fs13 table">
                                <thead>
                                    <tr>
                                        <td style="width:2%" class="center">#</td>
                                        <td style="width:6%" class="center"> &nbsp;</td>
                                        <td style="width:30%" class="vcenter"><?= $this->Paginator->sort('Student.first_name','Student Name'); ?></td>
                                        <td style="width:5%;" class="center"><?= $this->Paginator->sort('Student.gender', 'Sex'); ?></td>
                                        <td style="width:9%;" class="center"><?= $this->Paginator->sort('Student.student_id','Student ID'); ?></td>
                                        <td style="width:12%;" class="center"><?= $this->Paginator->sort('minute_number', 'Minute No.'); ?></td>
                                        <td style="width:14%;" class="center"><?= $this->Paginator->sort('graduate_date', 'Date Graduated'); ?></td>
                                        <td style="width:7%;" class="center"><?= $this->Paginator->sort('credit_hour_sum','Credit Taken'); ?> </td>
                                        <td style="width:5%;" class="center"><?= $this->Paginator->sort('cgpa','CGPA'); ?></td>
                                        <td style="width:5%;" class="center"><?= $this->Paginator->sort('mcgpa','MCGPA'); ?></td>
                                        <td style="width:5%;" class="center"><?= (($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) ? __('Action'):''); ?></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    //$count = 1;
                                    $count = $this->Paginator->counter('%start%');
                                    foreach ($graduateLists as $graduateList) {
                                        $valid_deletion_time =  date('Y-m-d H:i:s', mktime(
                                            substr($graduateList['GraduateList']['created'], 11, 2),
                                            substr($graduateList['GraduateList']['created'], 14, 2),
                                            substr($graduateList['GraduateList']['created'], 17, 2),
                                            substr($graduateList['GraduateList']['created'], 5, 2),
                                            substr($graduateList['GraduateList']['created'], 8, 2) + Configure::read('Calendar.daysAvaiableForGraduateDeletion'),
                                            substr($graduateList['GraduateList']['created'], 0, 4)
                                        ));

                                        $credit_hour_sum = 0;
                                        foreach ($graduateList['Student']['StudentExamStatus'] as $ses_key => $ses_value) {
                                            $credit_hour_sum += $ses_value['credit_hour_sum'];
                                        }
                                        ?>
                                        <tr>
                                            <td class="center"><?= $count; ?></td>
                                            <td class="center" onclick="toggleView(this)" id="<?= $count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $count, 'div' => false, 'align' => 'center')); ?></td>
                                            <td class="vcenter"><?= $graduateList['Student']['full_name']; ?></td>
                                            <td class="center"><?= (strcasecmp($graduateList['Student']['gender'], 'male') == 0 ? 'M' : 'F'); ?></td>
                                            <td class="center"><?= $this->Html->link($graduateList['Student']['studentnumber'],'#',  array('class' => 'jsview', 'data-animation' => "fade", 'data-reveal-id' => 'myModal', 'data-reveal-ajax' => "/students/get_modal_box/" . $graduateList['Student']['id'] )); ?></td>
                                            <td class="center"><?= $graduateList['GraduateList']['minute_number']; ?></td>
                                            <td class="center"><?= $this->Time->format("M j, Y", $graduateList['GraduateList']['graduate_date'], NULL, NULL); ?></td>
                                            <td class="center"><?= $credit_hour_sum; ?></td>
                                            <td class="center"><?= $graduateList['Student']['StudentExamStatus'][0]['cgpa']; ?></td>
                                            <td class="center"><?= $graduateList['Student']['StudentExamStatus'][0]['mcgpa']; ?></td>
                                            <td class="center">
                                                <?php
                                                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) {
                                                        if ($valid_deletion_time > date('Y-m-d')) {
                                                            echo $this->Html->link(__('Delete'), array('action' => 'delete', $graduateList['GraduateList']['id']), null, sprintf(__('Are you sure you want to delete %s (%s) from the graduate list?', $graduateList['Student']['full_name'], $graduateList['Student']['studentnumber'])));
                                                        } else {
                                                            echo '---';
                                                        }
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr id="c<?= $count++; ?>" style="display:none">
                                            <td colspan="2" style="background-color: white;"> </td>
                                            <td colspan="9" style="background-color: white;">
                                                <table cellpadding="0" cellspacing="0" class="fs12 table">
                                                    <tbody>
                                                        <tr><td style="background-color: white;">Curriculum Name: &nbsp; <?= $graduateList['Student']['Curriculum']['name']; ?></td></tr>
                                                        <tr><td>Degree Designation: &nbsp; <?= $graduateList['Student']['Curriculum']['english_degree_nomenclature']; ?></td></tr>
                                                        <?php
                                                        if (!empty($graduateList['Student']['Curriculum']['specialization_english_degree_nomenclature'])) { ?>
                                                            <tr><td style="background-color: white;">Specialization: &nbsp; <?= $graduateList['Student']['Curriculum']['specialization_english_degree_nomenclature']; ?></td></tr>
                                                            <?php
                                                        } ?>
                                                        <tr><td>Degree Designation (Amharic): &nbsp; <?= $graduateList['Student']['Curriculum']['amharic_degree_nomenclature']; ?></td></tr>
                                                        <?php
                                                        if (!empty($graduateList['Student']['Curriculum']['specialization_amharic_degree_nomenclature'])) { ?>
                                                            <tr><td style="background-color: white;">Specialization (Amharic): &nbsp; <?= $graduateList['Student']['Curriculum']['specialization_amharic_degree_nomenclature']; ?></td></tr>
                                                            <?php
                                                        } ?>
                                                        <tr><td>Credit Type: &nbsp; <?= (count(explode('ECTS', $graduateList['Student']['Curriculum']['type_credit'])) == 2 ? 'ECTS' : 'Credit'); ?></td></tr>
                                                        <tr><td>Required <?= (count(explode('ECTS', $graduateList['Student']['Curriculum']['type_credit'])) == 2 ? 'ECTS' : 'Credit'); ?> for Graduation: &nbsp; <?= $graduateList['Student']['Curriculum']['minimum_credit_points']; ?></td></tr>
                                                        <tr><td style="background-color: white;">Department:  &nbsp; <?= $graduateList['Student']['Department']['name']; ?></td></tr>
                                                        <tr><td>Program: &nbsp; <?= $graduateList['Student']['Program']['name']; ?></td></tr>
                                                        <tr><td style="background-color: white;">Program Type: &nbsp; <?= $graduateList['Student']['ProgramType']['name']; ?></td></tr>
                                                    </tbody>
                                                </table>
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
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleView(obj) {
        if ($('#c' + obj.id).css("display") == 'none') {
            $('#i' + obj.id).attr("src",'/img/minus2.gif');
        } else {
            $('#i' + obj.id).attr("src", '/img/plus2.gif');
        }
        $('#c' + obj.id).toggle("slow");
    }
</script>

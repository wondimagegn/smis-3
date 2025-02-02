<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Study Programs'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
                <?php
                //debug($studyPrograms);
                if (!empty($studyPrograms)) { ?>
                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                                <tr>
                                    <th class="center">#</th>
                                    <th class="vcenter"><?= $this->Paginator->sort('study_program_name'); ?></th>
                                    <th class="center"><?= $this->Paginator->sort('code'); ?></th>
                                    <th class="center"><?= $this->Paginator->sort('local_band'); ?></th>
                                    <th class="center"><?= $this->Paginator->sort('ISCED_band'); ?></th>
                                    <th class="center"><?= $this->Paginator->sort('study_field'); ?></th>
                                    <th class="center"><?= $this->Paginator->sort('sub_study_field'); ?></th>
                                    <th class="center"><?= __('Actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = $this->Paginator->counter('%start%');
                                foreach ($studyPrograms as $studyProgram) { ?>
                                    <tr>
                                        <td class="center"><?= $count++; ?></td>
                                        <td class="vcenter"><?= $studyProgram['StudyProgram']['study_program_name']; ?></td>
                                        <td class="center"><?= $studyProgram['StudyProgram']['code']; ?></td>
                                        <td class="center"><?= $studyProgram['StudyProgram']['local_band']; ?></td>
                                        <td class="center"><?= $studyProgram['StudyProgram']['ISCED_band']; ?></td>
                                        <td class="center"><?= $studyProgram['StudyProgram']['study_field']; ?></td>
                                        <td class="center"><?= $studyProgram['StudyProgram']['sub_study_field']; ?></td>
                                        
                                        
                                        <td class="center">
                                            <?= $this->Html->link(__(''), array('action' => 'view', $studyProgram['StudyProgram']['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?> &nbsp;
                                            <?= $this->Html->link(__(''), array('action' => 'edit', $studyProgram['StudyProgram']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?> &nbsp;
                                            <?= $this->Html->link(__(''), array('action' => 'delete', $studyProgram['StudyProgram']['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s study program?'), $studyProgram['StudyProgram']['study_program_name'])); ?>
                                        </td>
                                    </tr>
                                    <?php 
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <br>

                    <p><?= $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?> </p>

                    <div class="paging">
                        <?= $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class' => 'disabled')); ?> | <?= $this->Paginator->numbers(); ?> | <?= $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled')); ?>
                    </div>

                    <?php
                } else { ?>
                    <div class='info-box info-message'><span style='margin-right: 15px;'></span>No Study Program found with the search criteria</div>
                    <?php
                } ?>
			</div>
		</div>
	</div>
</div>
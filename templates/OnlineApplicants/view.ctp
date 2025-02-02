<?php ?>
<div class="box">
    <div class="box-body">
        <div class="row">
            <div
                class="large-12 columns">
                <h2><?php echo ' Online Admission Details of application number:' . $onlineApplicant['OnlineApplicant']['applicationnumber'] . ' for ' . $onlineApplicant['OnlineApplicant']['academic_year'] . ' /' . $onlineApplicant['OnlineApplicant']['semester']; ?>
                </h2>
            </div>
            <div
                class="large-12 columns">
                <div class="row">
                    <div
                        class="large-4 columns">
                        <?php echo __('Application Number:') . $onlineApplicant['OnlineApplicant']['applicationnumber']; ?>

                    </div>
                    <div
                        class="large-4 columns">
                        <?php echo __('Full Name:') . $onlineApplicant['OnlineApplicant']['full_name']; ?>

                    </div>
                    <div
                        class="large-4 columns">
                        <?php echo __('Gender:') . $onlineApplicant['OnlineApplicant']['gender']; ?>

                    </div>
                </div>
                <div class="row">
                    <div
                        class="large-4 columns">
                        <?php echo __('Email:') . $onlineApplicant['OnlineApplicant']['email']; ?>

                    </div>
                    <div
                        class="large-4 columns">
                        <?php echo __('Mobile Phone:') . $onlineApplicant['OnlineApplicant']['mobile_phone']; ?>

                    </div>


                </div>

                <div class="row">
                    <div
                        class="large-3 columns">
                        <?php echo __('College:') . $onlineApplicant['College']['name']; ?>

                    </div>
                    <div
                        class="large-3 columns">
                        <?php echo __('Department:') . $onlineApplicant['Department']['name']; ?>

                    </div>
                    <div
                        class="large-3 columns">
                        <?php echo __('Admission Type:') . $onlineApplicant['ProgramType']['name']; ?>

                    </div>

                    <div
                        class="large-3 columns">
                        <?php echo __('Study Level:') . $onlineApplicant['ProgramType']['name']; ?>

                    </div>


                </div>

                <div class="row">
                    <div
                        class="large-4 columns">
                        <?php echo __('Undergraduate University Attended:') . $onlineApplicant['OnlineApplicant']['undergraduate_university_name']; ?>

                    </div>


                    <div
                        class="large-4 columns">
                        <?php echo __('Undergraduate University CGPA:') . $onlineApplicant['OnlineApplicant']['undergraduate_university_cgpa']; ?>

                    </div>

                    <div
                        class="large-4 columns">
                        <?php echo __('Undergraduate University Field of Study:') . $onlineApplicant['OnlineApplicant']['undergraduate_university_field_of_study']; ?>

                    </div>



                </div>

                <div class="row">
                    <div
                        class="large-4 columns">
                        <?php echo __('Document Status:'); ?>
                        <?php echo $onlineApplicant['OnlineApplicant']['document_submitted'] == 1 ? 'Accepted' : $onlineApplicant['OnlineApplicant']['document_submitted'] == -1 ? 'Rejected' : 'Pending'; ?>

                    </div>


                    <div
                        class="large-4 columns">
                        <?php echo __('Undergraduate University CGPA:') . $onlineApplicant['OnlineApplicant']['undergraduate_university_cgpa']; ?>

                    </div>

                    <div
                        class="large-4 columns">
                        <?php echo __('Undergraduate University Field of Study:') . $onlineApplicant['OnlineApplicant']['undergraduate_university_field_of_study']; ?>

                    </div>



                </div>
                <div class="row">
                    <div
                        class="large-12 columns">
                        <?php
						debug($onlineApplicant);
						if (
							isset($onlineApplicant['Attachment'])
							&& !empty($onlineApplicant['Attachment'])
						) {
							echo "<table>";
							foreach ($onlineApplicant['Attachment']
								as $cuk => $cuv) {
								//$this->Format->humanize_date



								echo '<tr>
                            <td>Type:
                                ' . $cuv['group'] .
									'</td>
                        </tr>';
								echo '<tr>
                            <td>';
								echo '<a
                                    href=' . $this->Media->url(
									$cuv['dirname'] . DS . $cuv['basename'],
									true
								) . '
                                    target=_blank>View
                                    Attachment</a>';
								echo '
                            </td>
                        </tr>';
							}
							echo "</table>";
						}
						?>


                    </div>

                </div>


                <div class="row">
                    <div
                        class="large-12 columns">

                        <div
                            class="related">
                            <h3><?php echo __('Related Admission  Statuses'); ?>
                            </h3>
                            <?php if (!empty($onlineApplicant['OnlineApplicantStatus'])) : ?>
                            <table
                                cellpadding="0"
                                cellspacing="0">
                                <tr>
                                    <th><?php echo __('S.No'); ?>
                                    </th>
                                    <th><?php echo __('Status'); ?>
                                    </th>
                                    <th><?php echo __('Remark'); ?>
                                    </th>
                                    <th><?php echo __('Created'); ?>
                                    </th>

                                    <th
                                        class="actions">
                                        <?php echo __('Actions'); ?>
                                    </th>
                                </tr>
                                <?php foreach ($onlineApplicant['OnlineApplicantStatus'] as $onlineApplicantStatus) : ?>
                                <tr>
                                    <td><?php echo ++$count; ?>
                                    </td>

                                    <td><?php echo $statuses[$officialRequestStatus['status']]; ?>
                                    </td>
                                    <td><?php echo $onlineApplicantStatus['remark']; ?>
                                    </td>
                                    <td><?php
												echo date("F j, Y, g:i a", strtotime($onlineApplicantStatus['created']));
												?></td>

                                    <td
                                        class="actions">

                                        <?php echo $this->Html->link(__('Edit'), array('controller' => 'online_applicant_statuses', 'action' => 'edit', $onlineApplicantStatus['id'])); ?>
                                        <?php echo $this->Form->postLink(__('Delete'), array('controller' => 'online_applicant_statuses', 'action' => 'delete', $onlineApplicantStatus['id']), array('confirm' => __('Are you sure you want to delete # %s?', $onlineApplicantStatus['id']))); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
            <!-- end of columns 12 -->
        </div> <!-- end of row --->
    </div> <!-- end of box-body -->
</div><!-- end of box -->
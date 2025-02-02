<?php ?>
<div class="box">
    <div class="box-body">
        <div class="row">
            <div
                class="large-12 columns">

                <div
                    class="courseRegistrations index">
                    <?php
					echo $this->Form->Create('CourseRegistration');

					if ($role_id != ROLE_STUDENT) {

					?>

                    <div
                        class="smallheading">
                        <?php echo __('Course Registration search'); ?>
                    </div>
                    <?php


						echo '<table><tr><td>';
						echo '<table>';

						if ($role_id != ROLE_STUDENT) {
							echo '<tr><td>' . $this->Form->input('Search.academic_year', array(
								'options' => $acyear_array_data,
								'default' => !isset($this->request->data['Search']['academic_year']) ? $defaultacademicyear : $this->request->data['Search']['academic_year']
							)) . '</td></tr>';
						}

						echo '<tr><td>' . $this->Form->input('Search.semester', array('options' => array(
							'I' => 'I', 'II' => 'II',
							'III' => 'III'
						))) . '</td></tr>';
						if ($role_id != ROLE_STUDENT) {
							echo '<tr><td>' . $this->Form->input('Search.program_type_id') . '</td></tr>';
							echo '<tr><td>' . $this->Form->input('Search.year_level_id', array('options' => $yearLevels)) . '</td>
</tr>';
						}
						echo '</table>';
						echo '</td><td>';
						echo '<table>';
						/*
						if ((($role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read('Auth.User')['Role']['parent_id'])) || $role_id == ROLE_COLLEGE) {
							echo '<tr><td>' . $this->Form->input('Search.department_id') . '</td></tr>';
						} else if (($role_id == ROLE_REGISTRAR || ROLE_REGISTRAR == $this->Session->read('Auth.User')['Role']['parent_id'])) {
							echo '<tr><td>' . $this->Form->input('Search.college_id') . '</td></tr>';
						}
						*/
						if (isset($departments) &&  !empty($departments)) {
							echo '<tr><td>' . $this->Form->input('Search.department_id', array(
								'label' => 'Department', 'id' => 'DepartmentId',
								'onchange' => 'updateCourseListOnChangeofOtherField()'
							)) . '</td></tr>';
						} else if (isset($colleges) && !empty($colleges)) {

							echo '<tr><td>' . $this->Form->input('Search.college_id', array(
								'onchange' => 'updateCourseListOnChangeofOtherField()',
								'id' => 'CollegeId',
								'label' => 'College'
							)) . '</td></tr>';
						}

						if ($role_id != ROLE_STUDENT) {
							echo '<tr><td>' . $this->Form->input('Search.program_id') . '</td></tr>';
							echo '<tr><td>' . $this->Form->input('Search.studentnumber') . '</td></tr>';
							echo '<tr><td>' . $this->Form->input('Search.limit', array('type' => 'number', 'min' => "20", 'step' => "5")) . '</td></tr>';

							echo $this->Form->hidden('Search.page', array('value' => 1));
						}
						echo '</table>';
						echo '</td></tr>';
						echo '</table>';



						?>

                    <?php
					} else if ($role_id == ROLE_STUDENT) {
					?>
                    <div
                        class="smallheading">
                        <?php echo __('Course Registration search'); ?>
                    </div>
                    <?php

						echo '<table>';
						echo '<tr><td>' . $this->Form->input('Search.academic_year', array(
							'options' => $acadamic_years,
							'default' => !isset($this->request->data['Search']['academic_year']) ? $defaultacademicyear : $this->request->data['Search']['academic_year']
						)) . '</td>
<td>' . $this->Form->input('Search.semester', array('options' => array(
							'I' => 'I', 'II' => 'II',
							'III' => 'III'
						))) . '</td></tr>';
						echo '</table>';
					}


					echo '<span>' . $this->Form->submit('Search', array(
						'class' => 'tiny radius button bg-blue',
						'name' => 'searchRegistration', 'div' => false
					)) . '  ' .
						$this->Form->submit('Generate PDF', array('class' => 'tiny radius button bg-blue', 'name' => 'generateSlip', 'div' => false));
					if ($role_id != ROLE_STUDENT) {
						echo '  ' . $this->Form->submit('Generate List', array('class' => 'tiny radius button bg-blue', 'name' => 'generateRegisteredList', 'div' => false));
					}
					echo '</span>';

					echo $this->Form->end();

					?>

                    <?php
					if (!empty($courseRegistrations)) {
					?>
                    <?php
						if (isset($to) && isset($from)) {
							if ($role_id != ROLE_STUDENT) {

						?>
                    <div
                        class="smallheading">
                        <?php echo __('Course registration between ' . $this->Format->short_date($from) . ' and ' . $this->Format->short_date($to)); ?>
                    </div>
                    <?php

							} else {
							?>
                    <div
                        class="smallheading">
                        List of courses
                        you have
                        registred so
                        far.</div>
                    <?php
							}
						} else {

							?>
                    <div
                        class="smallheading">
                        <?php echo __('Course registration Lists'); ?>
                    </div>
                    <?php
						}
						?>
                    <table
                        cellpadding="0"
                        cellspacing="0">
                        <tr>
                            <th>S.N<u>o</u>
                            </th>
                            <th><?php echo $this->Paginator->sort('year_level_id'); ?>
                            </th>
                            <th><?php echo $this->Paginator->sort('academic_year'); ?>
                            </th>
                            <th><?php echo $this->Paginator->sort('semester'); ?>
                            </th>
                            <th>ID</th>
                            <th><?php echo $this->Paginator->sort('student_id'); ?>
                            </th>
                            <th><?php echo $this->Paginator->sort('department_id'); ?>
                            </th>
                            <th><?php echo $this->Paginator->sort('program_id'); ?>
                            </th>
                            <th><?php echo $this->Paginator->sort('program_type_id'); ?>
                            </th>
                            <th><?php echo $this->Paginator->sort('course_id'); ?>
                            </th>
                            <?php if ($role_id != ROLE_STUDENT) { ?>
                            <!-- <th class="actions"><?php echo __('Actions'); ?></th> -->
                            <?php } ?>
                        </tr>
                        <?php
							$i = 0;
							$start = $this->Paginator->counter('%start%');

							foreach ($courseRegistrations as $courseRegistration) :
								$class = null;
								if ($i++ % 2 == 0) {
									$class = ' class="altrow"';
								}
							?>
                        <tr<?php echo $class; ?>>
                            <td><?php echo $start++ ?>&nbsp;
                            </td>
                            <td>
                                <?php
										if (isset($courseRegistration['YearLevel']['name'])) {
											echo $courseRegistration['YearLevel']['name'];
										} else {
											echo 'Pre/Freshman';
										}
										?>

                            </td>

                            <td><?php echo $courseRegistration['CourseRegistration']['academic_year']; ?>&nbsp;
                            </td>
                            <td><?php echo $courseRegistration['CourseRegistration']['semester']; ?>&nbsp;
                            </td>

                            <td>
                                <?php echo $courseRegistration['Student']['studentnumber']; ?>
                            </td>

                            <td>
                                <?php echo $this->Html->link($courseRegistration['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $courseRegistration['Student']['id'])); ?>
                            </td>
                            <td>
                                <?php
										if (isset($courseRegistration['Student']['Department']['name'])) {
											echo $courseRegistration['Student']['Department']['name'];
										} else {
											echo 'Non assigned.';
										}
										?>


                            </td>
                            <td>
                                <?php echo $courseRegistration['Student']['Program']['name']; ?>
                            </td>
                            <td>
                                <?php echo $courseRegistration['Student']['ProgramType']['name']; ?>
                            </td>
                            <td>
                                <?php echo $this->Html->link($courseRegistration['PublishedCourse']['Course']['course_code_title'], array('controller' => 'courses', 'action' => 'view', $courseRegistration['PublishedCourse']['Course']['id']));

										if (
											isset($courseRegistration['CourseDrop'][0]) &&
											$courseRegistration['CourseDrop'][0]['department_approval'] == 1 && count($courseRegistration['CourseDrop']) > 0 && $courseRegistration['CourseDrop'][0]['registrar_confirmation'] == 1
										) {
											echo "<b style='color:red'> - Dropped </b>";
										} else {
										}
										?>
                            </td>
                            <?php
									if ($role_id != ROLE_STUDENT) { ?>

                            <?php } ?>
                            </tr>
                            <?php endforeach; ?>
                    </table>
                    <p>
                        <?php
							echo $this->Paginator->counter(array(
								'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
							));
							?> </p>

                    <div class="paging">
                        <?php echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class' => 'disabled')); ?>
                        |
                        <?php echo $this->Paginator->numbers(); ?>
                        |
                        <?php echo $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled')); ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <!-- end of columns 12 -->
        </div> <!-- end of row --->
    </div> <!-- end of box-body -->
</div><!-- end of box -->
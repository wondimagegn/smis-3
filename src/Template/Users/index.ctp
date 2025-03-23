<div class="box" style="display: block;">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('List of Users'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div style="margin-top: -30px;">
            
            <hr>
            <blockquote>
                <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                <p style="text-align:justify;" class="fs16 text-black">Clicking on the <strong>Construct Menu</strong> link will run expensive process that will consume extensive system resourse, please click on the <strong>Construct Menu</strong> if and only if there is a change on the user privilage, assignment of new privilage(s) to the user or provoked privilage(s) from the user.</p> 
            </blockquote>
            <hr>

            <?= $this->Form->create('User', array('action' => 'search')); ?>

            <fieldset style="padding-bottom: 0px;">
                <!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
                <div class="row align-items-center">
                    <div class="large-4 columns">
                        <?= $this->Form->input('Search.name', array('label' => 'Search Key:', 'placeholder' => 'name, username or email', 'default' => $selected_search, 'style' =>'width:90%;')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('Search.role_id', array('label' => 'Role: ', 'type' => 'select', 'options' => $roles, 'default' => $selected_role, 'style' =>'width:90%;')); ?>
                    </div>
                    <div class="large-4 columns align-self-center">
                        <?= $this->Form->input('Search.limit', array('id' => 'limit ', 'type' => 'number', 'min'=>'100',  'max'=>'1000', 'value' => $selected_limit, 'step'=>'100', 'class' => 'fs14', 'label' =>' Limit: ', 'style' => 'width:30%;')); ?>
                    </div>
                </div>
                <div class="row align-items-center justify-content-center">
                    <div class="large-4 columns">
                        <?= $this->Form->input('Search.orderby', array('label' => 'Order By: ', 'id' => 'orderby', 'class' => 'fs14', 'type' => 'select', 'style' =>'width:90%;', 'options' => array('full_name' => 'Full name', 'username' => 'Username', 'email' => 'Email', 'last_login' => 'Last Login', 'active' => 'Active', 'created' => 'Created Date', 'modified' => 'Modified Date'), 'default' => $order_by)); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('Search.sortorder', array('label' => 'Sort: ', 'id' => 'sortorder', 'class' => 'fs14', 'type' => 'select', 'style' =>'width:90%;', 'options' => array('asc' => 'Ascending', 'desc' => 'Descending'), 'default' => $sort_order)); ?>
                    </div>
                    <div class="large-4 columns">
                        <br>
                        <?= $this->Form->input('Search.Staff.active', array('label' => 'Active Staff', 'type' => 'checkbox', 'checked' => ((isset($this->data['Search']['Staff']['active']) && $this->data['Search']['Staff']['active']) || $selected_staff_active == 1 ? 'checked' : false))); ?>
                        <?= $this->Form->input('Search.active', array('label' => 'Active User', 'type' => 'checkbox', 'checked' => ((isset($this->data['Search']['active']) && $this->data['Search']['active']) || $selected_user_active == 1 ? 'checked' : false))); ?>

                        <?= (isset($this->data['Search']['page']) ? $this->Form->hidden('page', array('value' => $this->data['Search']['page'])) : ''); ?>
						<?= (isset($this->data['Search']['sort']) ? $this->Form->hidden('sort', array('value' => $this->data['Search']['sort'])) : ''); ?>
						<?= (isset($this->data['Search']['direction']) ? $this->Form->hidden('direction', array('value' => $this->data['Search']['direction'])) : ''); ?>

                        <br>
                    </div>
                </div>
                <hr>
                <?= $this->Form->Submit('Search', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'getUsers')); ?>
            </fieldset>
            <?= $this->Form->end(); ?>

        </div>
        <hr>

        <?php
        if (!empty($users)) { ?>
            <br>
            <div style="overflow-x:auto;">
                <table cellpadding="0" cellspacing="0" class="table">
                    <thead>
                        <tr>
                            <td scope="col" class="center"> # </td>
                            <td scope="col" style="padding-left: 2%;"> Full Name (Username | Email) </td>
                            <td scope="col" class="center"> Role </td>
                            <td scope="col" class="center"> Last Login </td>
                            <td scope="col" class="center"> Active </td>
                            <td scope="col" class="actions" class="center"><?= __('Actions'); ?></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $start = $this->Paginator->counter('%start%');
                        foreach ($users as $user) { ?>
                            <tr>
                                <td scope="row" class="center"> <?= $start++; ?> </td>
                                <td style="padding-left: 1%; padding-right:1%">
                                    <strong><?= $user['User']['full_name']; ?></strong>&nbsp;
                                    <br /> 
                                    <i>
                                        <?php //echo $this->Text->truncate($user['User']['username'] .' | '.(($user['User']['email'] =="" || is_null($user['User']['email'])) ? '---' : $user['User']['email']),50,array('ellipsis' => '...', 'exact' => true,'html' => true));  ?>
                                        <?= $user['User']['username'] . ' | ' . (($user['User']['email'] == "" || is_null($user['User']['email'])) ? '---' : $user['User']['email']); ?>
                                        &nbsp;
                                    </i>
                                </td>
                                <td class="center">
                                    <?php
                                    echo $user['Role']['name'];
                                    if ($user['User']['is_admin'] == 1) {
                                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                                            // echo '<br><span class="accepted">Dean</span>'; 
                                            echo '<br><span class="status-metro status-active">Dean</span>';
                                        } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                                            // echo '<br><span class="accepted">Head</span>'; 
                                            echo '<br><span class="status-metro status-active">Head</span>';
                                        } else {
                                            // echo '<br><span class="accepted">Admin</span>';
                                            echo '<br><span class="status-metro status-active">Admin</span>';
                                        }
                                    } ?>
                                </td>
                                <td class="center"><?= (($user['User']['last_login'] == '0000-00-00 00:00:00' || $user['User']['last_login'] == '' || is_null($user['User']['last_login'])) ? '<span class="rejected">Never Logged In</span><br>Created on: ' . $this->Time->format("M j, Y", $user['User']['created'], NULL, NULL) : ($this->Time->timeAgoInWords($user['User']['last_login'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month'))))); ?>&nbsp;</td>
                                <td class="center">
                                    <?php
                                    echo ($user['User']['active'] == 1 ? 'Yes' : '<span class="rejected">No</span>');
                                    //echo ($user['User']['active'] == 1 ? '<span class="status-metro status-active" title="Yes">Yes</span>' : '<span class="status-metro status-suspended" title="No">No</span>');

                                    $canbe_deactivated = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y") - Configure::read('Users.AccountDeactivation.yearstoLookGivenLastLogin')));

                                    if ($user['User']['active'] == 1 && ($canbe_deactivated > $user['User']['last_login'] && $canbe_deactivated > $user['User']['created']) && $this->Session->read('Auth.User')['id'] != $user['User']['id']) {
                                        echo '<br>' . $this->Html->link(__('Deactivate'), array('action' => 'deactivate_account', $user['User']['id']), array('confirm' => __('Are you sure you want to send account deactivation request to system administrators for {0}?', $user['User']['full_name'] . ' (' . $user['User']['username'] . ')')));
                                    } else if ($user['User']['active'] == 0) {
                                        echo '<br>' . $this->Html->link(__('Activate'), array('action' => 'activate_account', $user['User']['id']), array('confirm' => __('Are you sure you want to send account activation request to system administrators for {0}?', $user['User']['full_name'] . ' (' . $user['User']['username'] . ')')));
                                    }

                                    if ($this->Session->read('Auth.User')['id'] == $user['User']['id']) {
                                        //echo '<br><span class="accepted">own account</span>';
                                        echo '<br><span class="status-metro status-active">own account</span>';
                                    } ?>
                                </td>
                                <td class="actions center">
                                    <?php
                                    
                                    echo $this->Html->link(__(''), array('action' => 'view', $user['User']['id']), array('class' => 'fontello-eye', 'title' => 'View'));
                                    
                                    if (($user['User']['id'] == $this->Session->read('Auth.User')['id']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) || (ENABLE_INSTRUCTOR_USER_EDIT_COLLEGE_DEPARTMENT && $this->Session->read('Auth.User')['is_admin'] && ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE))) {
                                        echo $this->Html->link(__(''), array('action' => 'edit', $user['User']['id']), array('class' => 'fontello-pencil', 'title' => 'Edit'));
                                    }

                                    if ($user['User']['role_id'] != ROLE_INSTRUCTOR) {
                                        echo $this->Html->link(__(''), array('action' => 'build_user_menu', $user['User']['id']), array('class' => 'icon icon-clockwise', 'title' => 'Construct Menu'));
                                    }

                                    if ($user['User']['active'] == 0 && $this->Session->read('Auth.User')['role_id'] == $user['User']['role_id']) {
                                        //echo $this->Html->link(__(''), array('action' => 'resetpassword'), array('onclick'=>'return false', 'style'=>'color:gray'), array('class'=>'fontello-key-outline','title'=>'Reset Password'));
                                    } else {
                                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR) {
                                            //echo $this->Html->link(__(''), array('action' => 'resetpassword'), array('onclick'=>'return false', 'style'=>'color:gray'), array('class'=>'fontello-key-outline','title'=>'Reset Password'));
                                        }
                                        if (($this->Session->read('Auth.User')['role_id'] == $user['User']['role_id']) && ($this->Session->read('Auth.User')['role_id'] != ROLE_SYSADMIN) && ($this->Session->read('Auth.User')['is_admin'] == 1) && ($this->Session->read('Auth.User')['id'] != $user['User']['id']) && ($user['User']['role_id'] != ROLE_INSTRUCTOR)) {
                                            echo $this->Html->link(__(''), array('action' => 'resetpassword', $user['User']['id']), array('class' => 'fontello-key', 'title' => 'Reset Password'));
                                        }
                                    }

                                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == $this->Session->read('Auth.User')['Role']['parent_id']) {
                                        if ($user['User']['active'] == 0) {
                                            //echo $this->Html->link(__(''), array('action' => 'assign'),array('onclick'=>'return false', 'style'=>'color:gray'),array('class'=>'fontello-users','title'=>'Assign')); 
                                        } else {
                                            echo $this->Html->link(__(''), array('action' => 'assign', $user['User']['id']), array('class' => 'fontello-users', 'title' => 'Assign'));
                                        }
                                    }

                                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_ACCOMODATION) {
                                        if ($user['User']['active'] == 0) {
                                            //echo $this->Html->link(__(''), array('action' => 'assign_user_dorm_block'),array('onclick'=>'return false', 'style'=>'color:gray'), array('class'=>'fontello-users','title'=>'Assign Dorm Block'));
                                        } else {
                                            echo $this->Html->link(__(''), array('action' => 'assign_user_dorm_block', $user['User']['id']), array('class' => 'fontello-users', 'title' => 'Assign Dorm Block'));
                                        }
                                    }

                                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_MEAL) {
                                        if ($user['User']['active'] == 0) {
                                            //echo $this->Html->link(__(''), array('action' => 'assign_user_meal_hall'),array('onclick'=>'return false', 'style'=>'color:gray'),array('class'=>'fontello-users','title'=>'Assign Meal Hall')); 
                                        } else {
                                            echo $this->Html->link(__(''), array('action' => 'assign_user_meal_hall', $user['User']['id']), array('class' => 'fontello-users', 'title' => 'Assign Meal Hall'));
                                        }
                                    }

                                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) {
                                        //echo $this->Html->link(__(''), array('action' => 'delete', $user['User']['id']),array('class'=>'fontello-trash','title'=>'Delete'));  
                                    } ?>
                                </td>
                            </tr>
                            <?php
                        } ?>
                    <tbody>
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
</div>
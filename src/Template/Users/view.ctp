<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-user-outline" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'User details: ' . (isset($user['Staff'][0]['full_name']) ? $user['Staff'][0]['full_name'] : '') . '  (' . $user['User']['username'] . ')'; ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div class="large-6 columns">
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tbody>
                            <tr>
                                <td><strong>Basic Data</strong></td>
                            </tr>
                            <tr>
                                <td style="padding-left: 15px;" class="vcenter"><?= (isset($user['Staff'][0]) && !empty($user['Staff'][0]) ? (isset($user['Staff'][0]['Title']['title']) ? $user['Staff'][0]['Title']['title']. '. ' : '') . $user['Staff'][0]['first_name'] . ' '. $user['Staff'][0]['middle_name'] . ' ' . $user['Staff'][0]['last_name'] : '---'); ?></td>
                            </tr>
                            <tr>
                                <td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Position: </b></span> <?= (!empty($user['Staff'][0]['Position']['position']) ? $user['Staff'][0]['Position']['position'] : 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Birth Date: </b></span> <?= (isset($user['Staff'][0]['birthdate']) ? $this->Time->format("M j, Y", $user['Staff'][0]['birthdate'], NULL, NULL) : 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Email: </b></span><?= (!empty($user['Staff'][0]['email']) ? $user['Staff'][0]['email'] : '---'); ?> </td>
                            </tr>
                            <tr>
                                <td><strong>Staff Profile</strong> <span style="padding-left: 40px;"> <?= $this->Html->link(__('View'), array('controller' => 'staffs', 'action' => 'staff_profile', $user['Staff'][0]['id'])) . '&nbsp;&nbsp;&nbsp;' . $this->Html->link(__('Edit'), array('controller' => 'staffs', 'action' => 'edit', $user['Staff'][0]['id'])); ?></span></td>
                            </tr>
                            <?= (isset($user['Staff'][0]['staffid']) && !empty($user['Staff'][0]['staffid']) ? '<tr><td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>' . (Configure::read('CompanyShortName')) . ' ID N<u>o</u>: </b></span> &nbsp; ' . $user['Staff'][0]['staffid'] . '</td></tr>' : ''); ?>
                            <tr>
                                <td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Created: </b></span> <?= (!empty($user['Staff'][0]['created']) ? $this->Time->format("M j, Y h:i:s A", $user['Staff'][0]['created'], NULL, NULL) : 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Modified: </b></span> <?= (!empty($user['Staff'][0]['modified']) ? $this->Time->format("M j, Y h:i:s A", $user['Staff'][0]['modified'], NULL, NULL) : 'N/A'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <br>

                    <table cellpadding="0" cellspacing="0" class="table">
                        <thead>
                            <tr>
                                <td><strong>Address Information</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?= (isset($user['Staff'][0]['College']) && !empty($user['Staff'][0]['College']) ? '<tr><td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>College: </b></span> ' . $user['Staff'][0]['College']['name'] . '</td></tr>' : ''); ?>
                            <?= (isset($user['Staff'][0]['Department']) && !empty($user['Staff'][0]['Department']) ? '<tr><td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Department: </b></span> ' . $user['Staff'][0]['Department']['name'] . '</td></tr>' : ''); ?>
                            <?= (isset($user['Staff'][0]['email']) && !empty($user['Staff'][0]['email']) ? '<tr><td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Email: </b></span> ' . $user['Staff'][0]['email'] . '</td></tr>' : ''); ?>
                            <?= (isset($user['Staff'][0]['alternative_email']) && !empty($user['Staff'][0]['alternative_email']) ? '<tr><td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Alternative Email: </b></span> ' . $user['Staff'][0]['alternative_email'] . '</td></tr>' : ''); ?>
                            <?= (isset($user['Staff'][0]['phone_office']) && !empty($user['Staff'][0]['phone_office']) ? '<tr><td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Phone Office: </b></span> ' . $user['Staff'][0]['phone_office'] . '</td></tr>' : ''); ?>
                            <?= (isset($user['Staff'][0]['phone_mobile']) && !empty($user['Staff'][0]['phone_mobile']) ? '<tr><td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Phone Mobile: </b></span> ' . $user['Staff'][0]['phone_mobile'] . '</td></tr>' : ''); ?>
                            <?= (isset($user['Staff'][0]['address']) && !empty($user['Staff'][0]['address']) ? '<tr><td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Address: </b></span> ' . $user['Staff'][0]['address'] . '</td></tr>' : ''); ?>
                        </tbody>
                    </table>
                    <br>

                </div>

                <div class="large-6 columns">
                    <?php
                    if (isset($user['User']) && !empty($user['User'])) { ?>
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                                <tr>
                                    <td><strong>Access Information</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Active: </b></span> <?= ($user['User']['active'] ? 'Yes' : '<span class="rejected">No</span>'); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Username: </b></span> <?= $user['User']['username']; ?> </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Role: </b></span> <?= (!empty($user['Role']['name']) ? $user['Role']['name'] : 'N/A'); ?> </td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Created: </b></span> <?= $this->Time->format("M j, Y h:i:s A", $user['User']['created'], NULL, NULL); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Last Passord Change: </b></span> <?= ((empty($user['User']['last_password_change_date']) || $user['User']['last_password_change_date'] == '0000-00-00 00:00:00' || is_null($user['User']['last_password_change_date'])) ? '<span class="rejected">Never loggedin</span>' : $this->Time->timeAgoInWords($user['User']['last_password_change_date'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month')))); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Last Login: </b></span> <?= ((empty($user['User']['last_login']) || $user['User']['last_login'] == '0000-00-00 00:00:00' || is_null($user['User']['last_login'])) ? '<span class="rejected">Never loggedin</span>' : $this->Time->timeAgoInWords($user['User']['last_login'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month')))); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 15px;"><span class="fs13 text-gray" class="vcenter"><b>Last Failed Logins: </b></span> <?= (isset($user['User']['failed_login']) && $user['User']['failed_login'] != 0  ? $user['User']['failed_login'] : 0 ); ?> </td>
                                </tr>

                                <?= ($user['User']['is_admin'] ? ($user['User']['role_id'] == ROLE_DEPARTMENT ? '<tr><td class="vcenter"><strong>' . $user['Staff'][0]['Department']['name'] . ' Department Head' . '</strong></td></tr>' : ( $user['User']['role_id'] == ROLE_COLLEGE ? '<tr><td class="vcenter"><strong>' . $user['Staff'][0]['College']['name'] . ' Dean' . '</strong></td></tr>' : ('<tr><td class="vcenter"><strong>' . $user['Role']['name'] . ' Head (Super Admin)' . '</strong></td></tr>'))) : '');  ?>

                                <?php

                                if (isset($user['StaffAssigne']['college_id']) && !empty($user['StaffAssigne']['college_id'])) {
                                    $responsibilityCollege = unserialize($user['StaffAssigne']['college_id']);
                                } else {
                                    $responsibilityCollege = array();
                                }

                                if (isset($user['StaffAssigne']['department_id']) && !empty($user['StaffAssigne']['department_id'])) {
                                    $responsibilityDepartment = unserialize($user['StaffAssigne']['department_id']);
                                } else {
                                    $responsibilityDepartment = array();
                                }

                                if (isset($responsibilityCollege) && !empty($responsibilityCollege)) { ?>
                                    <tr><td><strong>Responsible for the following Colleges:</strong></td></tr>
                                    <tr>
                                        <td style="background-color: white;">
                                            <ol style="padding-left: 10px;">
                                                <?php
                                                foreach ($responsibilityCollege as $k => $v) { ?>
                                                    <li class="fs13"><?= $colleges[$v]; ?></li>
                                                    <?php
                                                } ?>
                                            </ol>
                                        </td>
                                    </tr>
                                    <?php
                                }

                                if (isset($responsibilityDepartment) && !empty($responsibilityDepartment)) { ?>
                                    <tr><td><strong>Responsible for the following Departments:</strong></td></tr>
                                    <tr>
                                        <td style="background-color: white;">
                                            <ol style="padding-left: 10px;">
                                                <?php
                                                foreach ($responsibilityDepartment as $k => $v) { ?>
                                                    <li class="fs14"><?= $departments[$v]; ?></li>
                                                    <?php
                                                } ?>
                                            </ol>
                                        </td>
                                    </tr>
                                    <?php
                                } ?>
                            </tbody>
                        </table>
                        <?php
                    } else { ?>
                        &nbsp;
                        <?php
                    } ?>
                </div>

                <br />

                <div class="large-12 columns">
                    <?php
                    $userPermissions =  ClassRegistry::init('User')->getAllPermissions($user['User']['id']);

                    //debug($userPermissions['permissionAggregated']);
                    //debug($userPermissions['permission']);
                    //debug($userPermissions['permissionAggregated']['UserLevelAllowed']);

                    if (isset($userPermissions['permission']) && !empty($userPermissions['permission'])) {

                        asort($userPermissions['permission']); ?>
                        <hr>
                        <div style="overflow-x:auto;">
                            <table cellpadding="0" cellspacing="0" class="table">
                                <thead>
                                <tr>
                                    <td colspan="4" class="vcenter"><strong>Allowed Access Permissions for <?= (isset($user['User']['username']) ? $user['User']['username'] : ' the user'); ?></td>
                                </tr>
                                    <tr>
                                        <td class="center">#</td>
                                        <td class="vcenter">Privilege</td>
                                        <td class="vcenter">Action</td>
                                        <td class="center">Level</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($userPermissions['permission'] as $pkey => $perm) {
                                        $controllerAction =  explode('/', $perm);
                                        $contollerNameHuman = Inflector::humanize(Inflector::underscore($controllerAction[1]));
                                        $actionNameHuman = (isset($controllerAction[2]) && !empty($controllerAction[2]) ? ucwords(str_replace('_', ' ', $controllerAction[2])) : '<span class="exempted">* (Wildcard)</span>'); ?>
                                        <tr>
                                            <td class="center"><?= $i++; ?></td>
                                            <td class="vcenter"><?= $contollerNameHuman; ?></td>
                                            <td class="vcenter"><?= $actionNameHuman; ?></td>
                                            <td class="center">
                                                <?php
                                                if ((isset($userPermissions['permissionAggregated']['UserLevelAllowed']) && in_array($perm, $userPermissions['permissionAggregated']['UserLevelAllowed'])) &&  (isset($userPermissions['permissionAggregated']['RoleLevel']) && in_array($perm, $userPermissions['permissionAggregated']['RoleLevel']))) {
                                                    echo '<span class="accepted">Role & User</span>';
                                                } else if (isset($userPermissions['permissionAggregated']['RoleLevel']) && in_array($perm, $userPermissions['permissionAggregated']['RoleLevel'])) {
                                                    echo '<span class="accepted">Role</span>';
                                                } else if (isset($userPermissions['permissionAggregated']['UserLevelAllowed']) && in_array($perm, $userPermissions['permissionAggregated']['UserLevelAllowed']) && ((isset($userPermissions['permissionAggregated']['RoleLevel']) && !in_array($perm, $userPermissions['permissionAggregated']['RoleLevel'])) || !isset($userPermissions['permissionAggregated']['RoleLevel']))) {
                                                    echo '<span class="rejected">User</span>';
                                                } else {
                                                    echo '<span class="rejected">System</span>';
                                                } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <?php
                    } ?>
                </div>

                <?php
                if (isset($userPermissions['permissionAggregated']['UserLevelDenied']) && !empty($userPermissions['permissionAggregated']['UserLevelDenied'])) {
                    asort($userPermissions['permissionAggregated']['UserLevelDenied']); ?>
                    <div class="large-12 columns">
                        <hr>
                        <div style="overflow-x:auto;">
                            <table cellpadding="0" cellspacing="0" class="table">
                                <thead>
                                    <tr>
                                        <td colspan="4" class="vcenter"><strong>Deniened Permissions from <?= (isset($user['User']['username']) ? $user['User']['username'] : ' the user'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="center">#</td>
                                        <td class="vcenter">Privilege</td>
                                        <td class="vcenter">Action</td>
                                        <td class="center">Level</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $j = 1;
                                    foreach ($userPermissions['permissionAggregated']['UserLevelDenied'] as $udkey => $denperm) {
                                        $deniedControllerAction =  explode('/', $denperm);
                                        $deniedContollerNameHuman = Inflector::humanize(Inflector::underscore($deniedControllerAction[1]));
                                        $deniedActionNameHuman = (isset($deniedControllerAction[2]) && !empty($deniedControllerAction[2]) ? ucwords(str_replace('_', ' ', $deniedControllerAction[2])) : ''); ?>
                                        <tr>
                                            <td class="center"><?= $j++; ?></td>
                                            <td class="vcenter"><?= $deniedContollerNameHuman; ?></td>
                                            <td class="vcenter"><?= $deniedActionNameHuman; ?></td>
                                            <td class="center"><span class="rejected">User Level Denied</span></td>
                                        </tr>
                                        <?php
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                    </div>
                    <?php
                } ?>
            </div>
            <br>
        </div>
    </div>
</div>
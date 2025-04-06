<?php
use Cake\Utility\Inflector;
use Cake\Core\Configure;

?>

<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-params" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Permission Management'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <blockquote>
                    <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                    <p style="text-align:justify;">
                        <span class="fs14 text-black"> Please do not forget to <u style="font-weight: bold;">Construct User Menu</u> after the assignment of new privilage(s) to the user and/or provoked privilage(s) from the user by going to <u style="font-weight: bold;">User Account List</u> page.</span>
                    </p>
                </blockquote>
                <br>

                <div id="breadcrumbs">
                    <?php
                    if (!empty($path)) {
                        foreach ($path as $id => $alias) {
                            $this->Html->addCrumb($alias, array('action' => 'index', $id));
                        }
                    }
                    echo $this->Html->link('Tasks', array('controller' => 'acos', 'action' => 'index', 1)) . ' ' .
                        (isset($path) && !empty($path) && count($path) > 1 ? ' &#8250; ' .
                            (!empty($aco['privilage']) ?
                                Inflector::humanize(Inflector::underscore($aco['privilage'])) :
                                Inflector::humanize(Inflector::underscore($aco['alias']))) : ''); ?>
                </div>
                <hr>

                <?= $this->Form->create('Aco', array('action' => 'delete', 'id' => 'aco-form')); ?>

                <div style="overflow-x:auto;">
                    <table cellpadding="0" cellspacing="0" class="table">
                        <thead>
                        <tr>
                            <td style="width:20px" class="center">#</td>
                            <?php
                            if (Configure::read("Developer")) { ?>
                                <td style="width:5%" class="center"><?= $this->Form->checkbox('select_all', array('id' => 'select-all')); ?></td>
                                <td style="width:5%" class="center"></td>
                                <?php
                            } ?>
                            <td class="vcenter">Privilege</td>
                            <td style="width:120px;text-align: center;" class="center">Contained Actions</td>
                            <td style="width:100px;text-align: center;" class="center">Permissions</td>
                            <td style="width:40%" class="center">Note</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (!empty($acos)) {

                            foreach ($acos as $i) {
                                if (empty($count)) {
                                    $count = 1;
                                } else {
                                    $count++;
                                } ?>
                                <tr>
                                    <td class="center"><?= $count; ?></td>
                                    <?php
                                    if (Configure::read("Developer")) { ?>
                                        <td class="center"><?= $this->Form->checkbox('Aco.delete.' . $i['id'], array('class' => 'checkbox1')); ?></td>
                                        <td class="center"><?= $this->Html->link($this->Html->image('/acls/img/edit.png', array('alt' => 'Edit ACO')),
                                                array('action' => 'edit', $i['id']), array('escape' => false, 'title' => 'Edit ACO')); ?></td>
                                        <?php
                                    } ?>
                                    <td class="vcenter"><?= (empty($i['privilage']) ? $i['alias'] : $i['privilage']); ?></td>
                                    <td class="center"><?= (($i['num_children'] > 0) ? $this->Html->link('Children', array('action' => 'index', $i['id'])) : 'Children') .
                                        ' <small>(' . $i['num_children'] . ')</small>'; ?></td>
                                    <td class="center">
                                            <span style="padding-left: 30px;">
                                                <?php
                                                if (!isset($i['remove_permission']) || !$i['remove_permission']) {
                                                    echo $this->Html->link($this->Html->image('/acls/img/permissions.png', array('alt' => 'View Permissions')), array('controller' => 'permissions', 'action' => 'index', $i['id']),
                                                            array('escape' => false, 'title' => 'View Permissions')) .'<small>('.
                                                        $i['num_permitted_actions_controlloer'].')</small>';
                                                } ?>
                                            </span>
                                    </td>
                                    <td class="vcenter"><?= $i['note']; ?></td>
                                </tr>
                                <?php
                            }
                        } ?>
                        </tbody>
                    </table>
                </div>
                <hr>

                <?= $this->Form->hidden('parent_id', array('value' => $parentId)); ?>

                <?php
                if (Configure::read("Developer")) {
                    echo $this->Form->submit('Delete Selected', array(
                        'after' => ' &nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" value="Rebuild ACOs" id="rebuildButton" class="tiny radius button bg-blue" />',
                        'class' => 'tiny radius button bg-blue'
                    ));
                } ?>

                <?= $this->Form->end(); ?>

                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#rebuildButton').click(function() {
                            $('#aco-form').attr('action', '/acls/acos/rebuild').submit();
                        });
                    });
                </script>

            </div>
        </div>
    </div>
</div>

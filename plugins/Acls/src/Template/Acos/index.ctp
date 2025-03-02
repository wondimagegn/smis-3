<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-params" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Permission Management'); ?>
            </span>
        </div>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <blockquote>
                    <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                    <p style="text-align:justify;">
                        <span class="fs14 text-black">
                            Please do not forget to <u style="font-weight: bold;">Construct User Menu</u> after the assignment of new privilege(s) to the user and/or revoked privilege(s) from the user by going to
                            <u style="font-weight: bold;">User Account List</u> page.
                        </span>
                    </p>
                </blockquote>
                <br>

                <div id="breadcrumbs">
                    <?php
                    if (!empty($path)) {
                        foreach ($path as $id => $alias) {
                            echo $this->Html->link($alias, ['action' => 'index', $id]) . ' &#8250; ';
                        }
                    }
                    echo $this->Html->link('Tasks', ['controller' => 'Acos', 'action' => 'index', 1]) . ' ' .
                        (isset($path) && !empty($path) && count($path) > 1 ? ' &#8250; ' . (!empty($aco->privilage) ? Inflector::humanize(Inflector::underscore($aco->privilage)) : Inflector::humanize(Inflector::underscore($aco->alias))) : '');
                    ?>
                </div>

                <hr>

                <?= $this->Form->create($aco, ['url' => ['action' => 'delete'], 'id' => 'aco-form']) ?>

                <div style="overflow-x:auto;">
                    <table cellpadding="0" cellspacing="0" class="table">
                        <thead>
                        <tr>
                            <td style="width:20px" class="center">#</td>
                            <?php if (Configure::read("Developer")): ?>
                                <td style="width:5%" class="center"><?= $this->Form->checkbox('select-all', ['id' => 'select-all']) ?></td>
                                <td style="width:5%" class="center"></td>
                            <?php endif; ?>
                            <td class="vcenter">Privilege</td>
                            <td style="width:120px;text-align: center;" class="center">Contained Actions</td>
                            <td style="width:100px;text-align: center;" class="center">Permissions</td>
                            <td style="width:40%" class="center">Note</td>
                        </tr>
                        </thead>

                        <tbody>
                        <?php if (!empty($acos)): ?>
                            <?php foreach ($acos as $index => $i): ?>
                                <tr>
                                    <td class="center"><?= $index + 1 ?></td>

                                    <?php if (Configure::read("Developer")): ?>
                                        <td class="center"><?= $this->Form->checkbox('Aco.delete.' . $i->id, ['class' => 'checkbox1']) ?></td>
                                        <td class="center"><?= $this->Html->link($this->Html->image('/acls/img/edit.png', ['alt' => 'Edit ACO']), ['action' => 'edit', $i->id], ['escape' => false, 'title' => 'Edit ACO']) ?></td>
                                    <?php endif; ?>

                                    <td class="vcenter"><?= empty($i->privilage) ? h($i->alias) : h($i->privilage) ?></td>

                                    <td class="center">
                                        <?= ($i->num_children > 0) ? $this->Html->link('Children', ['action' => 'index', $i->id]) : 'Children' ?>
                                        <small>(<?= $i->num_children ?>)</small>
                                    </td>

                                    <td class="center">
                                            <span style="padding-left: 30px;">
                                                <?php if (!isset($i->remove_permission) || !$i->remove_permission): ?>
                                                    <?= $this->Html->link(
                                                        $this->Html->image('/acls/img/permissions.png', ['alt' => 'View Permissions']),
                                                        ['controller' => 'Permissions', 'action' => 'index', $i->id],
                                                        ['escape' => false, 'title' => 'View Permissions']
                                                    ) ?>
                                                    <small>(<?= $i->num_permitted_actions_controlloer ?>)</small>
                                                <?php endif; ?>
                                            </span>
                                    </td>

                                    <td class="vcenter"><?= h($i->note) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <hr>

                <?= $this->Form->control('parent_id', ['type' => 'hidden', 'value' => $parent_id]) ?>

                <?php if (Configure::read("Developer")): ?>
                    <?= $this->Form->button('Delete Selected', ['class' => 'tiny radius button bg-blue']) ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <?= $this->Form->button('Rebuild ACOs', ['type' => 'button', 'id' => 'rebuildButton', 'class' => 'tiny radius button bg-blue']) ?>
                <?php endif; ?>

                <?= $this->Form->end() ?>

                <script type="text/javascript">
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('rebuildButton').addEventListener('click', function() {
                            document.getElementById('aco-form').action = '/acls/acos/rebuild';
                            document.getElementById('aco-form').submit();
                        });

                        document.getElementById('select-all').addEventListener('change', function() {
                            const checkboxes = document.querySelectorAll('.checkbox1');
                            checkboxes.forEach(function(checkbox) {
                                checkbox.checked = document.getElementById('select-all').checked;
                            });
                        });
                    });
                </script>

            </div>
        </div>
    </div>
</div>

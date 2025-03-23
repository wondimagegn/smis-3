<?php

use Cake\Core\Configure;
use Cake\Utility\Inflector;

?>
<?php
$rename_menu_title = Configure::read('Menu.title_rename');

$this->Format->renameControllerTitle($menuoptimized, $rename_menu_title);

$links = array();
$controller = str_replace('_', '', $this->request->getParam('controller'));
$action = !empty(
$this->request->getParam('action')
) ? $this->request->getParam('action') : 'index';

echo "<ul id='menu-showhide' class='topnav slicknav' style='height: auto;'>";

$parent = 0;
$parent_exists = false;
foreach ($menuoptimized as $key => $value) {
    if ($this->Format->isActiveMenu($value, $controller, $action)) {
        $parent_exists = true;
        break;
    }
}

foreach ($menuoptimized as $key => $value) {
    $parent = $key;

    if (($parent_exists == false && $key == 0) ||
        $this->Format->isActiveMenu($value, $controller, $action)
    ) {
        echo '<li style="text-align: left;">';
        $parent = $key;
    } else {
        echo '<li style="text-align: left;">';
    }

    $ctrlCamel = Inflector::variable($value['url']['controller']);
    if (!array_key_exists($value['id'], $rename_menu_title)) {
        $ctrlHuman = Inflector::humanize(Inflector::underscore($ctrlCamel));
    } else {
        $ctrlHuman = $rename_menu_title[$value['id']];
    }

    //echo '<a href="#" class="tooltip-tip" title=' . $ctrlHuman . '><span>' . $ctrlHuman . '</span></a>';

    if ($ctrlHuman == 'Dashboard') {
        $menu_icon = '<i class=" fa fa-dashboard"></i>';
        echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
    } else {
        if ($ctrlHuman == 'Mailers') {
            $menu_icon = '<i class="fontello-chat-alt"></i>';
            echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
        } else {
            if ($ctrlHuman == 'Security') {
                $menu_icon = '<i class="fontello-lock-filled"></i>';
                echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
            } else {
                if ($ctrlHuman == 'Placement') {
                    $menu_icon = '<i class="fontello-commerical-building"></i>';
                    echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                } else {
                    if ($ctrlHuman == 'Ess') {
                        $menu_icon = '<i class="fontello-credit-card"></i>';
                        echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                    } else {
                        if ($ctrlHuman == 'Reports') {
                            $menu_icon = '<i class="fontello-chart-pie"></i>';
                            echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                        } else {
                            if ($ctrlHuman == 'Graduation') {
                                $menu_icon = '<i class="fontello-college"></i>';
                                echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                            } else {
                                if ($ctrlHuman == 'Curriculums') {
                                    $menu_icon = '<i class="fontello-briefcase"></i>';
                                    echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                } else {
                                    if ($ctrlHuman == 'Transfers') {
                                        $menu_icon = '<i class="fontello-export"></i>';
                                        echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                    } else {
                                        if ($ctrlHuman == 'Evalution') {
                                            $menu_icon = '<i class="fontello-check"></i>';
                                            echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                        } else {
                                            if ($ctrlHuman == 'Schedule') {
                                                $menu_icon = '<i class="fontello-calendar-1 "></i>';
                                                echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                            } else {
                                                if ($ctrlHuman == 'Dormitory') {
                                                    $menu_icon = '<i class="fontello-home-outline"></i>';
                                                    echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                                } else {
                                                    if ($ctrlHuman == 'Backups') {
                                                        $menu_icon = '<i class="fontello-database-1"></i>';
                                                        echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                                    } else {
                                                        if ($ctrlHuman == 'Main Data' || $ctrlHuman == 'Main Data ') {
                                                            $menu_icon = '<i class="fontello-params"></i>';
                                                            echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                                        } else {
                                                            if ($ctrlHuman == 'Registration') {
                                                                $menu_icon = '<i class="fontello-vcard"></i>';
                                                                echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                                            } else {
                                                                if ($ctrlHuman == 'Grades') {
                                                                    $menu_icon = '<i class="fontello-note"></i>';
                                                                    echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                                                } else {
                                                                    if ($ctrlHuman == 'Clearances') {
                                                                        $menu_icon = '<i class="fontello-beaker"></i>';
                                                                        echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                                                    } else {
                                                                        if ($ctrlHuman == 'Billing') {
                                                                            $menu_icon = '<i class="fontello-money"></i>';
                                                                            echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                                                        } else {
                                                                            if ($ctrlHuman == 'Readmissions') {
                                                                                $menu_icon = '<i class="fontello-loop"></i>';
                                                                                echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                                                            } else {
                                                                                if ($ctrlHuman == 'Alumni') {
                                                                                    $menu_icon = '<i class="fontello-user-add-outline"></i>';
                                                                                    echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                                                                } else {
                                                                                    $menu_icon = '<i class="fontello-record-outline"></i>';
                                                                                    echo '<a href="#"  class="tooltip-tip" title="' . $ctrlHuman . '">' . $menu_icon . '<span>' . $ctrlHuman . '</span></a>';
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    if (count($menuoptimized[$parent]['children'])) {
        if (($parent_exists == false && $key == 0) ||
            $this->Format->isActiveMenu($value, $controller, $action)
        ) {
            echo '<ul style="display:block; border-radius: 0px;">';
        } else {
            echo '<ul style="border-radius: 0px;">';
        }

        // this is for index of the inactive menu, replicaiton
        if (isset($value['parent']) && !empty($value['parent'])) {
            echo '<li>' . $this->Html->link(
                    $ctrlHuman . '',
                    '/' . $value['url']['controller'] . '/' . $value['url']['action'],
                    array('class' => "tooltip-tip")
                ) . '</li>';
        }

        foreach ($menuoptimized[$parent]['children'] as $m => $f) {
            if (sizeof($f['children']) == 0) {
                echo '<li style="text-align: left;">' . $this->Html->link(
                        __($f['title'] . '', true),
                        array(
                            'controller' => $f['url']['controller'],
                            'action' => $f['url']['action'],
                            'plugin' => false
                        )
                    );
            } else {
                echo '<li style="text-align: left;"><a href="#" style="color:white;" title=' . $f['title'] . '><span>' . $f['title'] . '</span></a>';
                if (sizeof($f['children']) != 0) {
                    $children = $f['children'];
                    $string = null;
                    $string .= '<ul style="border-radius: 0px;">';
                    foreach ($children as $ch => $vch) {
                        $string .= '<li style="text-align: left;">';
                        if (sizeof($vch['children']) == 0) {
                            $string .= $this->Html->link(
                                $vch['title'] . '',
                                '/' . $vch['url']['controller'] . '/' . $vch['url']['action']
                            );
                        } else {
                            $string .= '<a href="#" style="color:white;" title=' . $vch['title'] . '> <span>' . $vch['title'] . '</span></a>';
                            if (sizeof($vch['children']) != 0) {
                                $string .= '<ul style="border-radius: 0px;">';
                                $string .= '<li style="text-align: left;">' . $this->Html->link(
                                    //$vch['title'],
                                        $this->Html->tag('span', $vch['title'], array('style' => '')),
                                        array(
                                            'controller' => $vch['url']['controller'],
                                            'action' => $vch['url']['action'],
                                            'plugin' => false,
                                        ),
                                        array('style' => 'color: white;', 'escape' => false, 'escapeTitle' => false)
                                    ) . '</li>';

                                foreach ($vch['children'] as $c => $v) {
                                    $string .= '<li style="text-align: left; background-color: transparent; padding: 0px 0 0px 0px; display: inline-block;">';
                                    $string .= $this->Html->link(
                                        $v['title'],
                                        '/' . $v['url']['controller'] . '/' . $v['url']['action'],
                                        array('plugin' => false, 'style' => 'color:white;')
                                    );
                                    $string .= '</li>';
                                }
                                $string .= '</ul>';
                            }
                        }
                        $string .= "</li>";
                    }
                    $string .= "</ul>";
                    echo $string;
                }
            }
            echo "</li>";
        }
        echo '</ul>';
    }
    echo "</li>";
}
echo "</ul>";
?>

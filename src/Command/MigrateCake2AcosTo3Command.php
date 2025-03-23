<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Inflector;

class MigrateCake2AcosTo3Command extends Command
{
    public function execute(Arguments $args, ConsoleIo $io)
    {
        try {
            $db = ConnectionManager::get('default');
            // AROs Role → Roles
            $arosUpdate = $db->execute("UPDATE `aros` SET `model` = 'Roles' WHERE `model` = 'Role'");
            if (!$arosUpdate) {
                throw new \Exception("Failed to update model='Roles' in aros table.");
            }

            // AROs User → Users
            $acosUpdate = $db->execute("UPDATE `aros` SET `model` = 'Users' WHERE `model` = 'User'");
            if (!$acosUpdate) {
                throw new \Exception("Failed to update model='Users' in aros table.");
            }

            // Rename read → is_read
            $autoMessageIsRead = $db->execute(
                "ALTER TABLE `auto_messages` CHANGE `read` `is_read` TINYINT(1) NOT NULL DEFAULT '0'"
            );
            if (!$autoMessageIsRead) {
                throw new \Exception("Failed to rename 'read' to 'is_read' in auto_messages table.");
            }

            echo "All queries executed successfully.\n";


            $acos = $db->execute("SELECT id, alias FROM acos WHERE alias LIKE '%_%'")->fetchAll('assoc');

            if (empty($acos)) {
                $io->out("No snake_case ACOs found to update.");
                return;
            }

            foreach ($acos as $aco) {
               // $newAlias = Inflector::camelize($aco['alias']);
                $newAlias = Inflector::camelize($aco['alias']);
                echo "Would update: {$aco['alias']} -> {$newAlias}\n";

               // $db->execute("UPDATE acos SET alias = ? WHERE id = ?", [$newAlias, $aco['id']]);
               // $io->out("Updated: {$aco['alias']} -> {$newAlias}");
            }

            $io->out("Migration complete!");
        } catch (\Exception $e) {
            $io->error("Error: " . $e->getMessage());
        }
    }
}

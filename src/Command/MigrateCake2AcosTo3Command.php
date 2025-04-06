<?php
namespace App\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Inflector;
use Cake\ORM\TableRegistry;

class MigrateCake2AcosTo3Command extends Command
{
    /*
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
            // Check if the column 'read' exists in auto_messages
            // Check if the column 'read' exists in auto_messages
            $statement = $db->execute("SHOW COLUMNS FROM `auto_messages` LIKE 'read'");
            $columnExists = $statement->fetch('assoc');

            if ($columnExists) {
                $autoMessageIsRead = $db->execute(
                    "ALTER TABLE `auto_messages` CHANGE `read` `is_read` TINYINT(1) NOT NULL DEFAULT '0'"
                );
                if (!$autoMessageIsRead) {
                    throw new \Exception("Failed to rename 'read' to 'is_read' in auto_messages table.");
                }
            } else {
                // Column doesn't exist, resume without error.
                // Optionally log that the column was not found.
            }


            echo "All queries executed successfully.\n";


            $acos = $db->execute("SELECT id, alias FROM acos WHERE alias LIKE '%_%'")->fetchAll('assoc');

            if (empty($acos)) {
                $io->out("No snake_case ACOs found to update.");
                return;
            }

            foreach ($acos as $aco) {
                // Skip root
                if ($aco['parent_id'] === null) {
                    continue;
                }

               // $newAlias = Inflector::camelize($aco['alias']);
                $newAlias = Inflector::camelize($aco['alias']);
                echo "Would update: {$aco['alias']} -> {$newAlias}\n";

               $db->execute("UPDATE acos SET alias = ? WHERE id = ?", [$newAlias, $aco['id']]);
               $io->out("Updated: {$aco['alias']} -> {$newAlias}");
            }

            $io->out("Migration complete!");
        } catch (\Exception $e) {
            $io->error("Error: " . $e->getMessage());
        }
    }
    */

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $Acos = TableRegistry::getTableLocator()->get('Acos');

        $all = $Acos->find('all')->orderAsc('lft')->toArray();

        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($all as $aco) {
            $originalAlias = $aco->alias;

            // Skip root
            if ($aco->parent_id === null) {
                continue;
            }

            // Get parent to determine if this is a controller or action
            $parent = $Acos->get($aco->parent_id);

            // Controller node: child of "controllers"
            if ($parent->alias === 'controllers') {
                $newAlias = Inflector::camelize($originalAlias); // UpperCamelCase
            } else {
                $newAlias = lcfirst(Inflector::camelize($originalAlias)); // lowerCamelCase
            }

            if ($newAlias !== $originalAlias) {


                $aco->alias = $newAlias;
                if ($Acos->save($aco)) {
                    $io->out("✔ Updated: {$originalAlias} ➜ {$newAlias}");
                    $updatedCount++;
                } else {
                    $io->warning("❌ Failed to update: {$originalAlias}");
                }

            } else {
                $skippedCount++;
            }
        }

        $io->success("✅ Migration complete.");
        $io->out("Total updated: {$updatedCount}");
        $io->out("Skipped (already OK): {$skippedCount}");
    }
}

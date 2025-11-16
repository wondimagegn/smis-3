<?php
use Migrations\AbstractMigration;

class RenameAcceptedStudentsColumns extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('accepted_students');
        if ($table->hasColumn('Placement_Approved_By_Department')) {
            $table->renameColumn('Placement_Approved_By_Department', 'placement_approved_by_department');
        }
        if ($table->hasColumn('EHEECE_total_results')) {
            $table->renameColumn('EHEECE_total_results', 'eheece_total_results');
        }
    }

    public function down()
    {
        $table = $this->table('accepted_students');
        $table
            ->renameColumn('placement_approved_by_department', 'Placement_Approved_By_Department')
            ->renameColumn('eheece_total_results', 'EHEECE_total_results')
            ->update();
    }
}

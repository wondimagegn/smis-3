<?php
use Migrations\AbstractMigration;

class DropDeprecatedFields extends AbstractMigration
{
    public function up()
    {

        $tables = $this->table('staffs');
        // Drop deprecated fields from the staffs table
        if ($tables->hasColumn('education')) {
            $tables->removeColumn('education');
        }

        if ($tables->hasColumn('ethnicity')) {
            $tables->removeColumn('ethnicity');
        }

        if ($tables->hasColumn('servicewing')) {
            $tables->removeColumn('servicewing');
        }
        $tables->update();

        // Drop deprecated fields from the roles table
        $tableStaff = $this->table('staffs');
        if ($tableStaff->hasColumn('woreda')) {
            $this->table('students')
                ->removeColumn('woreda')
                ->update();
        }


        $tableAttachment = $this->table('attachments');

        if ($tableAttachment->hasColumn('group')) {
            $tableAttachment->renameColumn('group', 'attachment_group');
        }
        if ($tableAttachment->hasColumn('basename')) {
            $tableAttachment->renameColumn('basename', 'file_name');
        }

        if ($tableAttachment->hasColumn('file')) {
            $tableAttachment->renameColumn('file', 'file_name');
        }



        if ($tableAttachment->hasColumn('dirname')) {
            $tableAttachment->renameColumn('dirname', 'file_dir');
        }


        if ($tableAttachment->hasColumn('basename')) {
            $tableAttachment->renameColumn('basename', 'file');
        }
        // Add file_type column if it doesn't exist
        if (!$tableAttachment->hasColumn('file_type')) {
            $tableAttachment->addColumn('file_type', 'string', [
                'limit' => 255,
                'null' => true,
                'default' => null,
                'comment' => 'MIME type of the file (e.g., image/jpeg, application/pdf)'
            ]);
        }
        if(!$tableAttachment->hasColumn('file_ext')) {
            $tableAttachment->addColumn('file_ext', 'string', [
                'limit' => 50,
                'null' => true,
                'default' => null,
                'comment' => 'File extension (e.g., pdf)'
            ]);
        }

        if (!$tableAttachment->hasColumn('size')) {
            $tableAttachment->addColumn('size', 'string', [
                'limit' => 50,
                'null' => true,
                'default' => null,
                'comment' => 'Size category of the file (e.g., original, l, m, s)'
            ]);
        }
        /*
         *
         * UPDATE attachments
SET file_dir = CONCAT('Uploads/attachments/', model, '/', foreign_key, '/original/', attachment_group, '/')
WHERE file_dir IN ('img', 'doc') AND foreign_key IS NOT NULL AND model IS NOT NULL;
         */

        $tableAttachment->update();

    }

    public function down()
    {
        // Revert changes by adding the columns back (optional)
        $this->table('staffs')
            ->addColumn('education', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('servicewing', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('ethnicity', 'string', ['limit' => 100, 'null' => true])
            ->update();
        $this->table('staffs')
            ->addColumn('woreda', 'string', ['limit' => 100, 'null' => true])
            ->update();

        $table = $this->table('attachments');
        $table
            ->removeColumn('file_type')
            ->removeColumn('size')
            ->update();
    }
}

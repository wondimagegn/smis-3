<?php
use Migrations\AbstractMigration;

class RenamePublishedCoursesFields extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('published_courses');

        // Rename fields

        // rename the column if it exist
        if ($table->hasColumn('published')) {

            $table->renameColumn('published', 'is_published');
        }
        if($table->hasColumn('drop')){

            $table->renameColumn('drop', 'is_drop');
        }
        if($table->hasColumn('add')){

            $table->renameColumn('add', 'is_add');
        }
        if($table->hasColumn('elective')){
            $table->renameColumn('elective', 'is_elective');
        }

        // Drop columns
        if($table->hasColumn('published_up')) {
            $table->removeColumn('published_up');
        }
        if($table->hasColumn('published_down')) {
            $table->removeColumn('published_down');
        }
        $table->update();


        $table = $this->table('helps');

        // Rename fields

        // rename the column if it exist
        if ($table->hasColumn('order')) {

            $table->renameColumn('order', 'sort_order');
        }

        $table->update();
    }
}

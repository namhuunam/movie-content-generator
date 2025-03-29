// database/migrations/yyyy_mm_dd_create_complete_column_in_movies_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompleteColumnInMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('movies') && !Schema::hasColumn('movies', 'complete')) {
            Schema::table('movies', function (Blueprint $table) {
                $table->boolean('complete')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('movies') && Schema::hasColumn('movies', 'complete')) {
            Schema::table('movies', function (Blueprint $table) {
                $table->dropColumn('complete');
            });
        }
    }
}
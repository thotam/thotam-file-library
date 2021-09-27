<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCleanedColumnToThotamFileLibrariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('thotam_file_libraries', function (Blueprint $table) {
            $table->boolean('cleaned')->nullable()->default(null)->after('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('thotam_file_libraries', function (Blueprint $table) {
            $table->dropColumn('cleaned');
        });
    }
}

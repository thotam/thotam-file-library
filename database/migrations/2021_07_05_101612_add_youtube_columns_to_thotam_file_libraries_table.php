<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYoutubeColumnsToThotamFileLibrariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('thotam_file_libraries', function (Blueprint $table) {
            $table->boolean('youtube')->nullable()->default(null)->after('google_id');
            $table->longText('youtube_id')->nullable()->default(null)->after('youtube');
            $table->longText('youtube_data')->nullable()->default(null)->after('youtube_id');
            $table->longText('youtube_privacy_status')->nullable()->default(null)->after('youtube_data');
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
            $table->dropColumn('youtube');
            $table->dropColumn('youtube_id');
            $table->dropColumn('youtube_data');
            $table->dropColumn('youtube_privacy_status');
        });
    }
}

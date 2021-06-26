<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThotamFileLibrariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thotam_file_libraries', function (Blueprint $table) {
            $table->id();
            $table->string('drive', 100);
            $table->longText('file_name')->nullable()->default(null);
            $table->longText('mime_type')->nullable()->default(null);
            $table->longText('local_path')->nullable()->default(null);
            $table->longText('google_virtual_path')->nullable()->default(null);
            $table->longText('google_display_path')->nullable()->default(null);
            $table->longText('google_id')->nullable()->default(null);
            $table->bigInteger('file_library_id')->unsigned()->nullable()->default(null);
            $table->longText('file_library_type')->nullable()->default(null);
            $table->string('tag', 100)->nullable()->default(null);
            $table->boolean('active')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->unsignedBigInteger('deleted_by')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thotam_file_libraries');
    }
}

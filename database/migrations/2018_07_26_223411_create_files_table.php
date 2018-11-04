<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('file')->nullable();
            $table->bigInteger('size')->nullable()->default(null);
            $table->integer('status')->default(1);
            $table->string('mime_type');
            $table->longText('meta')->nullable();
            $table->dateTime('published_at')->nullable()->default(null);
            $table->integer('folder_id')->unsigned();
            $table->string('sha256'); 
            $table->timestamps();

            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}

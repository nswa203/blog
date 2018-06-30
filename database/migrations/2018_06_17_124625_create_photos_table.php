<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotosTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('photos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image');
            $table->string('file')->nullable();
            $table->text('exif')->nullable();
            $table->text('iptc')->nullable();
            $table->string('size')->nullable()->default(null);
            $table->integer('status')->default(1);
            $table->dateTime('taken_at')->nullable()->default(null);
            $table->dateTime('published_at')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('photos');
    }

}

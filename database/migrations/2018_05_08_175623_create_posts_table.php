<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('posts', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title');
			$table->string('slug')->unique();
            $table->integer('category_id')->nullable()->unsigned();
            $table->string('image')->nullable();
			$table->longText('body');
			$table->text('excerpt');
			$table->integer('author_id')->unsigned();
			$table->integer('status')->default(1);
			$table->bigInteger('comment_count')->unsigned()->default(0);
			$table->dateTime('published_at')->nullable()->default(null);
			$table->timestamps();

			$table->foreign('author_id')
				->references('id')->on('users')
				->onDelete('cascade');	
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('posts');
	}
}

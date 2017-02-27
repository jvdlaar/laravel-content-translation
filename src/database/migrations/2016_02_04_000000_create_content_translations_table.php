<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentTranslationsTable extends Migration {

  /**
   * Run the migrations.
   */
  public function up() {
    \Schema::create('content_translations', function (Blueprint $table) {
      $table->increments('id');
      $table->string('content_type');
      $table->unsignedInteger('content_id')->index();
      $table->string('content_property');
      $table->string('locale');
      $table->text('translation');

      $table->unique(['content_type', 'content_id', 'content_property', 'locale'], 'content_locale_unique');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down() {
    \Schema::drop('content_translations');
  }
}

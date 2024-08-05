<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up()
  {
    Schema::create('pages', function (Blueprint $table) {
      $table->id();
      $table->jsonb('title');
      $table->string('slug')->unique();
      $table->string('layout')->default('default')->index();
      $table->jsonb('blocks');
      $table->foreignId('parent_id')->nullable()->constrained(config('filament-fabricator.table_name', 'pages'))->cascadeOnDelete()->cascadeOnUpdate();
      $table->timestamps();

      $table->jsonb('seo_title')->nullable();
      $table->jsonb('seo_description')->nullable();
      $table->jsonb('og_title')->nullable();
      $table->jsonb('og_description')->nullable();

      $table->boolean('noindex')->default(false);
    });

    DB::statement('CREATE INDEX idx_pages_title ON pages USING gin (title jsonb_path_ops);');
  }

  public function down()
  {
    Schema::dropIfExists('pages');
  }
};

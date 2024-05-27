<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->string('make')->nullable()->after('description');
            $table->string('model')->nullable()->after('make');
            $table->integer('iso_speed')->nullable()->after('height');
            $table->decimal('focal_length', 10, 2)->nullable()->after('iso_speed');
            $table->string('software')->nullable()->after('focal_length');
            $table->json('additional_metadata')->nullable()->after('software');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn(['make', 'model', 'iso_speed', 'focal_length', 'software', 'additional_metadata']);
        });
    }
};

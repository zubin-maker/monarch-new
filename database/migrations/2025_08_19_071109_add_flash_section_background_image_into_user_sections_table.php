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
        Schema::table('user_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('user_sections', 'flash_section_background_image')) {
                $table->string('flash_section_background_image')->nullable()->after('flash_section_subtitle');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_sections', function (Blueprint $table) {
            if (Schema::hasColumn('user_sections', 'flash_section_background_image')) {
                $table->dropColumn('flash_section_background_image');
            }
        });
    }
};

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
            if (!Schema::hasColumn('user_sections', 'features_section_title')) {
                $table->string('features_section_title')->nullable()->after('featured_img');
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
            if (Schema::hasColumn('user_sections', 'features_section_title')) {
                $table->dropColumn('features_section_title');
            }
        });
    }
};

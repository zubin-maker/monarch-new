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
            if (!Schema::hasColumn('user_sections', 'top_rated_product_section_subtitle')) {
                $table->string('top_rated_product_section_subtitle')->nullable()->after('top_rated_product_section_title');
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
            if (Schema::hasColumn('user_sections', 'top_rated_product_section_subtitle')) {
                $table->dropColumn('top_rated_product_section_subtitle');
            }
        });
    }
};

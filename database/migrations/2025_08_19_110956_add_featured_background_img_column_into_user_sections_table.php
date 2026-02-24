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
            if (!Schema::hasColumn('user_sections', 'featured_background_img')) {
                $table->string('featured_background_img')->nullable()->after('featured_img');
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
            if (Schema::hasColumn('user_sections', 'featured_background_img')) {
                $table->dropColumn('featured_background_img');
            }
        });
    }
};

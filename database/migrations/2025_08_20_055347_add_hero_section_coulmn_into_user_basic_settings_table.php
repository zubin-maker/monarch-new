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
        Schema::table('user_basic_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('user_basic_settings', 'hero_section')) {
                $table->tinyInteger('hero_section')->default(1)->after('hero_section_background_image')->comment('1 for enable, 0 for disable');
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
        Schema::table('user_basic_settings', function (Blueprint $table) {
            if (Schema::hasColumn('user_basic_settings', 'hero_section')) {
                $table->dropColumn('hero_section');
            }
        });
    }
};

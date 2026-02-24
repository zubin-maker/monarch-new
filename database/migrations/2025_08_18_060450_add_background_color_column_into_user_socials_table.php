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
        Schema::table('user_socials', function (Blueprint $table) {
            if (!Schema::hasColumn('user_socials', 'background_color')) {
                $table->string('background_color')->nullable()->after('icon');
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
        Schema::table('user_socials', function (Blueprint $table) {
            if (Schema::hasColumn('user_socials', 'background_color')) {
                $table->dropColumn('background_color');
            }
        });
    }
};

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
        Schema::table('user_items', function (Blueprint $table) {
            if (! Schema::hasColumn('user_items', 'catalogue_pdf')) {
                $table->string('catalogue_pdf', 255)->nullable();
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
        Schema::table('user_items', function (Blueprint $table) {
            if (Schema::hasColumn('user_items', 'catalogue_pdf')) {
                $table->dropColumn('catalogue_pdf');
            }
        });
    }
};

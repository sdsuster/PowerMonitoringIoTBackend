<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHashToPerangkat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('perangkat', function (Blueprint $table) {
            //
            $table->string('hash_id', 17);
            $table->string('hash_pass', 17);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('perangkat', function (Blueprint $table) {
            //
            $table->dropColumn('hash_id');
            $table->dropColumn('hash_pass');
        });
    }
}

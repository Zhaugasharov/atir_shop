<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropQualityColumnFromProductTable extends Migration
{
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('quality');
        });
    }

    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->string('quality')->nullable()->after('brand_id');
        });
    }
}

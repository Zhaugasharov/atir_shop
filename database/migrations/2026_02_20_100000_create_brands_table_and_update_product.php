<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTableAndUpdateProduct extends Migration
{
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('product', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->nullable()->after('gender');
            $table->string('quality')->nullable()->after('brand_id');
            $table->boolean('is_new')->default(false)->after('quality');

            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['brand_id', 'quality', 'is_new']);
        });

        Schema::dropIfExists('brands');
    }
}

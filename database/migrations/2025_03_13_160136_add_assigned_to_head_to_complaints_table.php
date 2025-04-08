<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to_head')->nullable()->after('status'); // Track assigned department head
            $table->foreign('assigned_to_head')->references('id')->on('users')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_head']);
            $table->dropColumn('assigned_to_head');
        });
    }
    
};

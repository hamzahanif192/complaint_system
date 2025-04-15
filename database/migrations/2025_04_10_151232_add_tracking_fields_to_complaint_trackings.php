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
        Schema::table('complaint_trackings', function (Blueprint $table) {
            $table->unsignedBigInteger('complaint_id')->index();
            $table->string('action_type'); // e.g., status_update, assigned, comment
            $table->text('comment')->nullable();
            $table->string('performed_by'); // name or role
            // $table->timestamp('created_at')->nullable();
            // $table->timestamp('updated_at')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaint_trackings', function (Blueprint $table) {
            //
        });
    }
};

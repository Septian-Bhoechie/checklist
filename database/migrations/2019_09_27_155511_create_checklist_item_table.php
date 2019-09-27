<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('description');
            $table->boolean('is_completed');
            $table->timestamp('completed_at')->nullable();
            $table->integer('urgency');
            $table->timestamp('due')->nullable();
            $table->integer('task_id')->nullable();
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->index('is_completed');
            $table->index('completed_at');
            $table->index('due');

            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade');
            $table->foreign('assignee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checklist_items');
    }
}

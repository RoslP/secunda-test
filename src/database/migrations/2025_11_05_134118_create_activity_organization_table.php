<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityOrganizationTable extends Migration
{
    public function up()
    {
        Schema::create('activity_organization', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['activity_id', 'organization_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_organization');
    }
}

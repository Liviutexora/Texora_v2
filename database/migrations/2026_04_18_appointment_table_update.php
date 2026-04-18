<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['client_name', 'service', 'appointment_time']);
            $table->unsignedBigInteger('employee_id')->after('business_id');
            $table->unsignedBigInteger('client_id')->after('employee_id');
            $table->unsignedBigInteger('service_id')->after('client_id');
            $table->dateTime('start_time')->after('service_id');
            $table->dateTime('end_time')->after('start_time');

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['client_id']);
            $table->dropForeign(['service_id']);
            $table->dropColumn(['employee_id', 'client_id', 'service_id', 'start_time', 'end_time']);
            $table->string('client_name');
            $table->string('service');
            $table->dateTime('appointment_time');
        });
    }
};

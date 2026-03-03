<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('consumer')->after('email');
            $table->string('status')->default('active')->after('role');
            $table->string('phone')->nullable()->after('status');
            $table->string('company')->nullable()->after('phone');
            $table->string('zone')->nullable()->after('company');
            $table->timestamp('approved_at')->nullable()->after('updated_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'phone', 'company', 'zone', 'approved_at']);
        });
    }
};

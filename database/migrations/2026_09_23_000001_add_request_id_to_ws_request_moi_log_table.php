<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequestIdToWsRequestMoiLogTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ws_request_moi_log') && !Schema::hasColumn('ws_request_moi_log', 'request_id')) {
            Schema::table('ws_request_moi_log', function (Blueprint $table) {
                $table->string('request_id', 80)->nullable()->index()->after('client_ip');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('ws_request_moi_log') && Schema::hasColumn('ws_request_moi_log', 'request_id')) {
            Schema::table('ws_request_moi_log', function (Blueprint $table) {
                $table->dropColumn('request_id');
            });
        }
    }
}

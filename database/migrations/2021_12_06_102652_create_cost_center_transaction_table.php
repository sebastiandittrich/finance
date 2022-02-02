<?php

use App\Models\CostCenter;
use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCostCenterTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cost_center_transaction', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CostCenter::class);
            $table->foreignIdFor(Transaction::class);
            $table->integer('share')->default(1);
            $table->string('originator_type')->nullable();
            $table->foreignId('originator_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cost_center_transaction');
    }
}

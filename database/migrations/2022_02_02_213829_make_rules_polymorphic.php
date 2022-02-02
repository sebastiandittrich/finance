<?php

use App\Models\Rule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeRulesPolymorphic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rules', function (Blueprint $table) {
            $table->morphs('rulable');
        });
        foreach (Rule::all() as $rule) {
            $rule->update(['rulable_id' => $rule->cost_center_id, 'rulable_type' => CostCenter::class]);
        }
        Schema::table('rules', function (Blueprint $table) {
            $table->dropColumn(['cost_center_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

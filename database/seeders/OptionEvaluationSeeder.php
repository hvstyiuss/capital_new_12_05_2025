<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionEvaluationSeeder extends Seeder
{
    public function run(): void
    {
        $criteres = DB::table('criteres')->pluck('id');
        foreach ($criteres as $critereId) {
            foreach ([['Faible',1],['Moyen',2],['Bon',3],['Excellent',4]] as $i => $opt) {
                DB::table('option_evaluations')->updateOrInsert(
                    ['critere_id' => $critereId, 'intitule' => $opt[0]],
                    ['critere_id' => $critereId, 'intitule' => $opt[0], 'score' => $opt[1], 'ordre' => $i + 1]
                );
            }
        }
    }
}





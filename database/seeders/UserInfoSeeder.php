<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\Grade;
use App\Models\Echelle;

class UserInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users
        $users = User::all();
        
        // Get first grade and echelle if they exist
        $grade = Grade::first();
        $echelle = Echelle::first();
        
        foreach ($users as $user) {
            // Split name into first and last name
            $nameParts = explode(' ', $user->name, 2);
            $fname = $nameParts[0] ?? $user->name;
            $lname = $nameParts[1] ?? '';
            
            $userInfoData = [
                'email' => $user->email ?? "{$user->ppr}@example.com",
                'cin' => 'AB' . str_pad($user->ppr, 6, '0', STR_PAD_LEFT),
                'grade_id' => $grade?->id,
                'corps' => 'support',
                'adresse' => 'Adresse par défaut',
            ];
            
            // Add echelle_id if the column exists in the table
            if ($echelle && \Schema::hasColumn('user_infos', 'echelle_id')) {
                $userInfoData['echelle_id'] = $echelle->id;
            }
            
            UserInfo::updateOrCreate(
                ['ppr' => $user->ppr],
                $userInfoData
            );
        }
    }
}

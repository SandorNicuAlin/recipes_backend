<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Group;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $alin = User::factory()->create([
            'username' => 'Sandor Alin',
            'email' => 'sandor.alin.nicu@gmail.com',
            'phone' => '0752217905',
            'password' => Hash::make('secret'),
        ]);
        $alin2 = User::factory()->create([
            'username' => 'alinsandor',
            'email' => 'alinsandor@protonmail.com',
            'phone' => '0752217905',
            'password' => Hash::make('secret'),
        ]);
        $stefan = User::factory()->create([
            'username' => 'Suciu Stefan',
            'email' => 'suciustefan@email.com',
            'phone' => '07432214678',
            'password' => Hash::make('secret'),
        ]);
        $sergiu = User::factory()->create([
            'username' => 'Suciu Sergiu',
            'email' => 'suciusergiu@email.com',
            'phone' => '074576255678',
            'password' => Hash::make('secret'),
        ]);
        $curea = User::factory()->create([
            'username' => 'Curea Sergiu',
            'email' => 'cureasergiu@email.com',
            'phone' => '07522123444',
            'password' => Hash::make('secret'),
        ]);
        $bot = User::factory()->create([
            'username' => 'Bot Marius',
            'email' => 'botmarius@email.com',
            'phone' => '0741178963',
            'password' => Hash::make('secret'),
        ]);

        Group::factory(6)->create();

        $group1 = Group::where('id', '1')->first();
        $group2 = Group::where('id', '2')->first();
        $group3 = Group::where('id', '3')->first();
        $group4 = Group::where('id', '4')->first();
        $group5 = Group::where('id', '5')->first();
        $group6 = Group::where('id', '6')->first();

        $alin->groups()->attach($group1, ['administrator' => true]);
        $alin2->groups()->attach($group2, ['administrator' => true]);
        $stefan->groups()->attach($group3, ['administrator' => true]);
        $sergiu->groups()->attach($group4, ['administrator' => true]);
        $curea->groups()->attach($group5, ['administrator' => true]);
        $bot->groups()->attach($group6, ['administrator' => true]);

        $alin->groups()->attach([$group2->id, $group3->id, $group4->id, $group5->id, $group6->id]);
        $alin2->groups()->attach([$group1->id, $group3->id, $group4->id]);
        $stefan->groups()->attach([$group1->id, $group6->id, $group5->id, $group4->id]);
        $sergiu->groups()->attach([$group3->id]);
        $curea->groups()->attach([$group3->id, $group4->id]);
        $bot->groups()->attach([$group1->id, $group3->id, $group4->id]);


        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

<?php

use Illuminate\Database\Seeder;

class StatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ids = [1,2,3];
        $status = factory(\App\Models\Status::class)->times(100)->make()->each(function($status)use($ids){
            $status->user_id = $ids[mt_rand(0,count($ids)-1)];
        });
        \App\Models\Status::query()->insert($status->toArray());
    }
}

<?php

use Illuminate\Database\Seeder;
use App\SpendDetail as SpendDetail;


class SpendDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('spend_details')->delete();

        $spend_details = array(
        	['user_id' => 1, 'category_id' => 1, 'amount' => 5000],
        	['user_id' => 1, 'category_id' => 11, 'amount' => 1000],
        	['user_id' => 1, 'category_id' => 9, 'amount' => 2000],
        	['user_id' => 1, 'category_id' => 6, 'amount' => 2000],
        	['user_id' => 2, 'category_id' => 1, 'amount' => 10000],
        	['user_id' => 2, 'category_id' => 2, 'amount' => 10000],
        	['user_id' => 3, 'category_id' => 1, 'amount' => 8000]
        );

        foreach ($spend_details as $spend_detail) {
        	SpendDetail::create($spend_detail);
        }
    }
}

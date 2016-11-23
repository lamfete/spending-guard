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
        	['spend_header_id' => 1, 'category_id' => 29, 'amount' => 5000],
        	['spend_header_id' => 1, 'category_id' => 32, 'amount' => 1000],
        	['spend_header_id' => 1, 'category_id' => 37, 'amount' => 2000],
        	['spend_header_id' => 1, 'category_id' => 34, 'amount' => 2000],
        	['spend_header_id' => 2, 'category_id' => 29, 'amount' => 10000],
        	['spend_header_id' => 2, 'category_id' => 30, 'amount' => 10000],
        );

        foreach ($spend_details as $spend_detail) {
        	SpendDetail::create($spend_detail);
        }
    }
}

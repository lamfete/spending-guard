<?php

use Illuminate\Database\Seeder;
use App\SpendHeader as SpendHeader;

class SpendHeadersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('spend_headers')->delete();

        $spend_headers = array(
        	['user_id' => 1, 'subtotal' => 10000],
        	['user_id' => 1, 'subtotal' => 20000],
        	['user_id' => 2, 'subtotal' => 8000]
        );

        foreach ($spend_headers as $spend_header) {
        	SpendHeader::create($spend_header);
        }
    }
}

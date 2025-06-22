<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DocumentRequest;

class TestDocumentRequest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DocumentRequest::factory(10)->create();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Offices;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('offices')->delete();
        DB::table('services')->delete();
        $mcr = Offices::create([
            'name' => 'Municipal Civil Registrar',
            'slug' => Str::slug('Municipal Civil Registrar'),
            'description' => 'Handles civil documents such as birth, marriage, and death certificates.',
        ]);
        $mcr->services()->create([
            'office_id' => $mcr->id,
            'title' => 'Birth Certificate',
            'slug' => Str::slug('Birth Certificate'),
            'description' => 'Issued for the registration of births.',
            'price' => 150.00,
            'is_active' => 1,
        ]);
        $mcr->services()->create([
            'office_id' => $mcr->id,
            'title' => 'Marriage Certificate',
            'slug' => Str::slug('Marriage Certificate'),
            'description' => 'Issued for the registration of marriages.',
            'price' => 200.00,
            'is_active' => 1,
        ]);
        $mcr->services()->create([
            'office_id' => $mcr->id,
            'title' => 'Death Certificate',
            'slug' => Str::slug('Death Certificate'),
            'description' => 'Issued for the registration of deaths.',
            'price' => 150.00,
            'is_active' => 1,
        ]);
        $mcr->services()->create([
            'office_id' => $mcr->id,
            'title' => 'Certificate of No Marriage (CENOMAR)',
            'slug' => Str::slug('Certificate of No Marriage'),
            'description' => 'Issued for the registration of no marriages.',
            'price' => 210.00,
            'is_active' => 1,
        ]);
        $mcr->services()->create([
            'office_id' => $mcr->id,
            'title' => 'Appointment (MCR)',
            'slug' => 'appointment-mcr',
            'description' => 'Appointment for the registration of births, marriages, and deaths.',
            'price' => 0.00,
            'is_active' => 1,
        ]);

        $bpls = Offices::create([
            'name' => 'Business Permits and Licensing Section',
            'slug' => Str::slug('Business Permits and Licensing Section'),
            'description' => 'Handles business permits and special certifications.',
        ]);
        $bpls->services()->create([
            'office_id' => $bpls->id,
            'title' => 'Business Permit',
            'slug' => Str::slug('Business Permit'),
            'description' => 'Issued for the registration of business permits.',
            'price' => 500.00,
            'is_active' => 1,
        ]);
        $bpls->services()->create([
            'office_id' => $bpls->id,
            'title' => 'Special Permit',
            'slug' => Str::slug('Special Permit'),
            'description' => 'Application for special events or short-term businesses.',
            'price' => 300.00,
            'is_active' => 1,
        ]);
        $bpls->services()->create([
            'office_id' => $bpls->id,
            'title' => 'Burial Permit',
            'slug' => Str::slug('Burial Permit'),
            'description' => 'Permit required for burial arrangements.',
            'price' => 100.00,
            'is_active' => 1,
        ]);
        $bpls->services()->create([
            'office_id' => $bpls->id,
            'title' => 'Appointment (BPLS)',
            'slug' => 'appointment-bpls',
            'description' => 'Appointment for the registration of business permits, special permits, and burial permits.',
            'price' => 0.00,
            'is_active' => 1,
        ]);
        // --- MTO Office ---
        $mto = Offices::create([
            'name' => 'Municipal Treasurer’s Office',
            'slug' => Str::slug('Municipal Treasurer’s Office'),
            'description' => 'Handles all LGU-related payments and cashier services.',
        ]);
        $mto->services()->create([
            'office_id' => $mto->id,
            'title' => 'Tax',
            'slug' => Str::slug('Tax'),
            'description' => 'Payment for tax.',
            'price' => 100.00,
            'is_active' => 1,
        ]);
        $mto->services()->create([
            'office_id' => $mto->id,
            'title' => 'Appointment (MTO)',
            'slug' => 'appointment-mto',
            'description' => 'Appointment for the payment of taxes.',
            'price' => 0.00,
            'is_active' => 1,
        ]);
    }


}

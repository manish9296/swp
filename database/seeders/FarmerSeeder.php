<?php

namespace Database\Seeders;

use App\Models\Farmer;
use Illuminate\Database\Seeder;

class FarmerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            [
                'application_no' => '818676916131601',
                'imei_number' => '869742080863201',
                'farmer_name' => 'Ram Kumar Yadav',
                'state' => 'Uttar Pradesh',
                'district' => 'Lucknow',
                'pump_capacity_hp' => 5,
                'component' => 'Component B',
                'subsidy_percent' => 60,
                'installation_date' => '2024-03-12',
                'status' => 'Installed',
            ],
            [
                'application_no' => '818676916131602',
                'imei_number' => '869742080863202',
                'farmer_name' => 'Suresh Chandra',
                'state' => 'Uttar Pradesh',
                'district' => 'Varanasi',
                'pump_capacity_hp' => 3,
                'component' => 'Component B',
                'subsidy_percent' => 60,
                'installation_date' => '2024-04-25',
                'status' => 'Installed',
            ],
            [
                'application_no' => '818676916131603',
                'imei_number' => '869742080863203',
                'farmer_name' => 'Anita Devi',
                'state' => 'Uttar Pradesh',
                'district' => 'Kanpur Dehat',
                'pump_capacity_hp' => 7.5,
                'component' => 'Component B',
                'subsidy_percent' => 60,
                'installation_date' => '2024-06-08',
                'status' => 'Pending Commissioning',
            ],
            [
                'application_no' => '818676916131604',
                'imei_number' => '869742080863204',
                'farmer_name' => 'Ghulam Nabi',
                'state' => 'Jammu and Kashmir',
                'district' => 'Anantnag',
                'pump_capacity_hp' => 2,
                'component' => 'Component B',
                'subsidy_percent' => 70,
                'installation_date' => '2024-02-15',
                'status' => 'Installed',
            ],
            [
                'application_no' => '818676916131605',
                'imei_number' => '869742080863205',
                'farmer_name' => 'Bashir Ahmad',
                'state' => 'Jammu and Kashmir',
                'district' => 'Baramulla',
                'pump_capacity_hp' => 3,
                'component' => 'Component B',
                'subsidy_percent' => 70,
                'installation_date' => '2024-05-30',
                'status' => 'Installed',
            ],
            [
                'application_no' => '818676916131606',
                'imei_number' => '869742080863206',
                'farmer_name' => 'Shabnam Kouser',
                'state' => 'Jammu and Kashmir',
                'district' => 'Jammu',
                'pump_capacity_hp' => 5,
                'component' => 'Component B',
                'subsidy_percent' => 70,
                'installation_date' => '2024-07-10',
                'status' => 'Under Verification',
            ],
            [
                'application_no' => '818676916131607',
                'imei_number' => '869742080863207',
                'farmer_name' => 'Manjunath Gowda',
                'state' => 'Karnataka',
                'district' => 'Belagavi',
                'pump_capacity_hp' => 5,
                'component' => 'Component B',
                'subsidy_percent' => 60,
                'installation_date' => '2024-01-05',
                'status' => 'Installed',
            ],
            [
                'application_no' => '818676916131608',
                'imei_number' => '869742080863208',
                'farmer_name' => 'Lakshmi Narasimha',
                'state' => 'Karnataka',
                'district' => 'Tumkur',
                'pump_capacity_hp' => 10,
                'component' => 'Component B',
                'subsidy_percent' => 60,
                'installation_date' => '2024-03-20',
                'status' => 'Installed',
            ],
            [
                'application_no' => '818676916131609',
                'imei_number' => '869742080863209',
                'farmer_name' => 'Ravi Patil',
                'state' => 'Karnataka',
                'district' => 'Bagalkot',
                'pump_capacity_hp' => 3,
                'component' => 'Component A',
                'subsidy_percent' => 60,
                'installation_date' => '2024-06-01',
                'status' => 'Pending',
            ],
        ];

        foreach ($records as $record) {
            Farmer::create($record);
        }
    }
}

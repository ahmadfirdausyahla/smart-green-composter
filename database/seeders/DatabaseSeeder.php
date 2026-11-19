<?php

namespace Database\Seeders;

use App\Enums\DeviceHealthStatus;
use App\Enums\DeviceStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\City;
use App\Models\Device;
use App\Models\DeviceConfiguration;
use App\Models\DeviceInventory;
use App\Models\DeviceRequest;
use App\Models\District;
use App\Models\InventoryTransaction;
use App\Models\KnowledgeChunk;
use App\Models\KnowledgeDocument;
use App\Models\MaintenanceReport;
use App\Models\Neighborhood;
use App\Models\OperationSession;
use App\Models\Province;
use App\Models\RegistrationRequest;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. SEED PROVINCES
        $jabar = Province::create([
            'code' => '32',
            'name' => 'Jawa Barat',
            'latitude' => -6.917464,
            'longitude' => 107.619123,
        ]);

        $dki = Province::create([
            'code' => '31',
            'name' => 'DKI Jakarta',
            'latitude' => -6.208763,
            'longitude' => 106.845599,
        ]);

        // 2. SEED CITIES
        $bogor = City::create([
            'province_id' => $jabar->id,
            'code' => '3271',
            'name' => 'Kota Bogor',
            'type' => 'Kota',
            'latitude' => -6.597147,
            'longitude' => 106.806039,
        ]);

        $bandung = City::create([
            'province_id' => $jabar->id,
            'code' => '3273',
            'name' => 'Kota Bandung',
            'type' => 'Kota',
            'latitude' => -6.917464,
            'longitude' => 107.619123,
        ]);

        $jaksel = City::create([
            'province_id' => $dki->id,
            'code' => '3174',
            'name' => 'Kota Administrasi Jakarta Selatan',
            'type' => 'Kota',
            'latitude' => -6.261493,
            'longitude' => 106.810600,
        ]);

        // 3. SEED DISTRICTS
        $bogorTengah = District::create([
            'city_id' => $bogor->id,
            'code' => '327101',
            'name' => 'Bogor Tengah',
        ]);

        $coblong = District::create([
            'city_id' => $bandung->id,
            'code' => '327311',
            'name' => 'Coblong',
        ]);

        // 4. SEED VILLAGES
        $babakan = Village::create([
            'district_id' => $bogorTengah->id,
            'code' => '3271011001',
            'name' => 'Babakan',
        ]);

        $paledang = Village::create([
            'district_id' => $bogorTengah->id,
            'code' => '3271011002',
            'name' => 'Paledang',
        ]);

        $dago = Village::create([
            'district_id' => $coblong->id,
            'code' => '3273111002',
            'name' => 'Dago',
        ]);

        // 5. SEED NEIGHBORHOODS (RT/RW)
        $rt03rw05 = Neighborhood::create([
            'village_id' => $babakan->id,
            'rt_number' => '03',
            'rw_number' => '05',
            'name' => 'Kompleks Hijau Lestari',
            'address' => 'Jl. Lodaya No. 12, RT 03 / RW 05, Babakan, Bogor Tengah',
            'latitude' => -6.589120,
            'longitude' => 106.804510,
        ]);

        $rt01rw02 = Neighborhood::create([
            'village_id' => $paledang->id,
            'rt_number' => '01',
            'rw_number' => '02',
            'name' => 'Komunitas Asri Paledang',
            'address' => 'Jl. Paledang No. 45, RT 01 / RW 02, Bogor Tengah',
            'latitude' => -6.598200,
            'longitude' => 106.795100,
        ]);

        $rt04rw07 = Neighborhood::create([
            'village_id' => $dago->id,
            'rt_number' => '04',
            'rw_number' => '07',
            'name' => 'Dago Sejahtera Asri',
            'address' => 'Jl. Ir. H. Juanda No. 102, RT 04 / RW 07, Dago, Coblong',
            'latitude' => -6.885600,
            'longitude' => 107.614200,
        ]);

        // 6. SEED USERS FOR ALL 4 ROLES
        $masterAdmin = User::create([
            'name' => 'Budi Santoso (Master Admin)',
            'email' => 'master@zerowaste.id',
            'password' => Hash::make('password'),
            'phone_number' => '081299990001',
            'role' => UserRole::MASTER_ADMIN,
            'status' => 'active',
        ]);

        $adminBogor = User::create([
            'name' => 'Rahmat Hidayat (Admin Kota Bogor)',
            'email' => 'admin.bogor@zerowaste.id',
            'password' => Hash::make('password'),
            'phone_number' => '081299990002',
            'role' => UserRole::ADMIN_KOTA,
            'city_id' => $bogor->id,
            'status' => 'active',
        ]);

        $adminBandung = User::create([
            'name' => 'Cecep Ridwan (Admin Kota Bandung)',
            'email' => 'admin.bandung@zerowaste.id',
            'password' => Hash::make('password'),
            'phone_number' => '081299990003',
            'role' => UserRole::ADMIN_KOTA,
            'city_id' => $bandung->id,
            'status' => 'active',
        ]);

        $adminRtRw = User::create([
            'name' => 'Agus Wijaya (Ketua RT 03 Babakan)',
            'email' => 'admin.rtrw@zerowaste.id',
            'password' => Hash::make('password'),
            'phone_number' => '081299990004',
            'role' => UserRole::ADMIN_RTRW,
            'city_id' => $bogor->id,
            'neighborhood_id' => $rt03rw05->id,
            'status' => 'active',
        ]);

        $adminLapangan = User::create([
            'name' => 'Dedi Kurniawan (Petugas Lapangan RT 03)',
            'email' => 'petugas@zerowaste.id',
            'password' => Hash::make('password'),
            'phone_number' => '081299990005',
            'role' => UserRole::ADMIN_LAPANGAN,
            'city_id' => $bogor->id,
            'neighborhood_id' => $rt03rw05->id,
            'status' => 'active',
        ]);

        // 7. SEED DEVICES & CONFIGURATIONS
        $device1 = Device::create([
            'serial_number' => 'SN-2026-BGR-001',
            'device_code' => 'SGC-BGR-001',
            'model_name' => 'Smart Green Composter v1 Pro',
            'api_key' => Str::random(32),
            'city_id' => $bogor->id,
            'neighborhood_id' => $rt03rw05->id,
            'current_status' => DeviceStatus::IDLE,
            'health_status' => DeviceHealthStatus::HEALTHY,
            'current_temperature' => 45.50,
            'current_weight' => 22.40,
            'last_heartbeat_at' => now(),
            'installed_at' => now()->subMonths(3),
            'is_publicly_visible' => true,
            'notes' => 'Unit percontohan RT 03 Babakan Bogor.',
        ]);

        DeviceConfiguration::create([
            'device_id' => $device1->id,
            'min_temp_limit' => 20.00,
            'max_temp_limit' => 65.00,
            'critical_temp_cutoff' => 70.00,
            'default_timer_minutes' => 240,
            'auto_shutdown_enabled' => true,
            'updated_by' => $masterAdmin->id,
        ]);

        $device2 = Device::create([
            'serial_number' => 'SN-2026-BGR-002',
            'device_code' => 'SGC-BGR-002',
            'model_name' => 'Smart Green Composter v1 Pro',
            'api_key' => Str::random(32),
            'city_id' => $bogor->id,
            'neighborhood_id' => $rt01rw02->id,
            'current_status' => DeviceStatus::RUNNING,
            'health_status' => DeviceHealthStatus::HEALTHY,
            'current_temperature' => 56.20,
            'current_weight' => 31.80,
            'last_heartbeat_at' => now(),
            'installed_at' => now()->subMonths(2),
            'is_publicly_visible' => true,
            'notes' => 'Unit aktif komposting Paledang.',
        ]);

        DeviceConfiguration::create([
            'device_id' => $device2->id,
            'min_temp_limit' => 20.00,
            'max_temp_limit' => 65.00,
            'critical_temp_cutoff' => 70.00,
            'default_timer_minutes' => 240,
            'auto_shutdown_enabled' => true,
            'updated_by' => $adminBogor->id,
        ]);

        $device3 = Device::create([
            'serial_number' => 'SN-2026-BDG-001',
            'device_code' => 'SGC-BDG-001',
            'model_name' => 'Smart Green Composter v1 Pro',
            'api_key' => Str::random(32),
            'city_id' => $bandung->id,
            'neighborhood_id' => $rt04rw07->id,
            'current_status' => DeviceStatus::IDLE,
            'health_status' => DeviceHealthStatus::HEALTHY,
            'current_temperature' => 38.00,
            'current_weight' => 15.20,
            'last_heartbeat_at' => now()->subMinutes(5),
            'installed_at' => now()->subMonths(1),
            'is_publicly_visible' => true,
            'notes' => 'Unit Dago Coblong Bandung.',
        ]);

        DeviceConfiguration::create([
            'device_id' => $device3->id,
            'min_temp_limit' => 20.00,
            'max_temp_limit' => 65.00,
            'critical_temp_cutoff' => 70.00,
            'default_timer_minutes' => 240,
            'auto_shutdown_enabled' => true,
            'updated_by' => $adminBandung->id,
        ]);

        // 8. SEED SAMPLE HISTORICAL OPERATION SESSIONS (Pencatatan Reduksi Massa)
        OperationSession::create([
            'session_uuid' => (string) Str::uuid(),
            'device_id' => $device1->id,
            'operator_id' => $adminLapangan->id,
            'initial_weight' => 40.00,
            'final_weight' => 12.00,
            'weight_reduction_kg' => 28.00,
            'reduction_percentage' => 70.00,
            'target_duration_minutes' => 240,
            'actual_duration_minutes' => 240,
            'peak_temperature' => 62.50,
            'avg_temperature' => 54.20,
            'started_at' => now()->subDays(2)->setTime(8, 0),
            'ended_at' => now()->subDays(2)->setTime(12, 0),
            'status' => 'completed_normally',
        ]);

        OperationSession::create([
            'session_uuid' => (string) Str::uuid(),
            'device_id' => $device1->id,
            'operator_id' => $adminLapangan->id,
            'initial_weight' => 35.50,
            'final_weight' => 11.20,
            'weight_reduction_kg' => 24.30,
            'reduction_percentage' => 68.45,
            'target_duration_minutes' => 240,
            'actual_duration_minutes' => 238,
            'peak_temperature' => 63.10,
            'avg_temperature' => 55.40,
            'started_at' => now()->subDay()->setTime(9, 0),
            'ended_at' => now()->subDay()->setTime(12, 58),
            'status' => 'completed_normally',
        ]);

        // 9. SEED AUDIT LOG
        AuditLog::create([
            'user_id' => $masterAdmin->id,
            'action' => 'INITIAL_SYSTEM_BOOTSTRAP',
            'entity_type' => 'System',
            'entity_id' => 1,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Seeder Script',
            'description' => 'Inisialisasi sistem Smart Green Composter dan pembuatan akun awal per-role.',
            'created_at' => now(),
        ]);

        // 10. SEED KNOWLEDGE BASE (Untuk RAG AI Chatbot)
        $doc1 = KnowledgeDocument::create([
            'title' => 'Panduan Pengoperasian Smart Green Composter',
            'category' => 'operasional',
            'source_type' => 'manual',
            'content' => 'Smart Green Composter adalah mesin pengolah sampah organik pintar berbasis IoT. Mesin dilengkapi sensor timbangan LoadCell HX711, sensor suhu DHT22, motor pencacah, dan elemen pemanas dekomposisi cepat. Sebelum memulai penggilingan, sampah organik dimasukkan ke ruang tabung, sensor membaca berat awal, dan motor berputar selama durasi yang ditentukan (standar 4 jam). Mesin secara otomatis mematikan pemanas jika suhu melebihi 65 derajat Celsius.',
            'is_published' => true,
        ]);

        KnowledgeChunk::create([
            'document_id' => $doc1->id,
            'chunk_text' => 'Smart Green Composter mengolah sampah organik dengan memadukan pencacahan motorik dan pemanasan terkontrol untuk mempercepat dekomposisi. Berat awal ditimbang otomatis sebelum proses dimulai.',
            'chunk_order' => 1,
        ]);

        KnowledgeChunk::create([
            'document_id' => $doc1->id,
            'chunk_text' => 'Sistem keselamatan IoT: Jika suhu melebihi 65C muncul peringatan, dan jika mencapai 70C mesin melakukan emergency cutoff secara otomatis langsung pada firmware ESP32 tanpa tergantung koneksi internet.',
            'chunk_order' => 2,
        ]);

        $doc2 = KnowledgeDocument::create([
            'title' => 'Jenis Sampah Organik yang Boleh dan Dilarang Dimasukkan',
            'category' => 'edukasi',
            'source_type' => 'panduan_warga',
            'content' => 'Sampah yang BOLEH dimasukkan: Sisa sayuran mentah/matang, kulit buah-buahan, ampas kopi dan teh, sisa nasi, daun kering, dan cangkang telur yang dihancurkan. Sampah yang DILARANG: Tulang hewan besar/keras, minyak jelantah dalam jumlah banyak, plastik, kemasan styrofoam, puntung rokok, baterai, logam, kotoran hewan peliharaan, dan cairan kimia.',
            'is_published' => true,
        ]);

        KnowledgeChunk::create([
            'document_id' => $doc2->id,
            'chunk_text' => 'Sampah organik yang boleh diolah di Smart Green Composter: sisa sayuran, kulit buah, nasi sisa, ampas kopi/teh, daun kering, cangkang telur.',
            'chunk_order' => 1,
        ]);

        KnowledgeChunk::create([
            'document_id' => $doc2->id,
            'chunk_text' => 'Sampah yang dilarang keras dimasukkan: tulang sapi/kambing besar keras, plastik, cairan kimia, minyak goreng kental banyak, logam, puntung rokok.',
            'chunk_order' => 2,
        ]);

        // 11. SEED INVENTORY STOCKS & TRANSACTIONS
        $centralStock = DeviceInventory::create([
            'model_name' => 'Smart Green Composter v1 Pro',
            'central_stock' => 50,
            'city_id' => null,
            'city_stock' => 0,
        ]);

        $bogorStock = DeviceInventory::create([
            'model_name' => 'Smart Green Composter v1 Pro',
            'central_stock' => 0,
            'city_id' => $bogor->id,
            'city_stock' => 12,
        ]);

        $bandungStock = DeviceInventory::create([
            'model_name' => 'Smart Green Composter v1 Pro',
            'central_stock' => 0,
            'city_id' => $bandung->id,
            'city_stock' => 8,
        ]);

        InventoryTransaction::create([
            'transaction_type' => 'city_allocation',
            'from_entity' => 'Gudang Pusat',
            'to_entity' => 'Gudang Kota Bogor',
            'performed_by' => $masterAdmin->id,
            'notes' => 'Alokasi stok awal 12 unit untuk wilayah Bogor.',
        ]);

        // 12. SEED SAMPLE PENDING REGISTRATION REQUEST (Untuk diuji Admin Kota Bogor)
        RegistrationRequest::create([
            'name' => 'Drs. H. Anwar Fauzi',
            'email' => 'anwar.rtrw@zerowaste.id',
            'password' => Hash::make('password'),
            'phone_number' => '081399887766',
            'province_id' => $jabar->id,
            'city_id' => $bogor->id,
            'district_id' => $bogorTengah->id,
            'village_id' => $babakan->id,
            'rt_number' => '05',
            'rw_number' => '05',
            'supporting_info' => 'Pengurus RT 05 Babakan mengajukan permohonan pendaftaran resmi akun komposter lingkungan.',
            'status' => 'pending',
        ]);

        // 13. SEED SAMPLE DEVICE REQUEST
        DeviceRequest::create([
            'city_id' => $bogor->id,
            'neighborhood_id' => $rt03rw05->id,
            'requester_id' => $adminRtRw->id,
            'requested_count' => 1,
            'reason' => 'Peningkatan volume sampah organik warga RT 03 yang melebihi kapasitas unit eksisting.',
            'status' => 'pending',
        ]);

        // 14. SEED SAMPLE SCHEDULES (Petugas Lapangan)
        Schedule::create([
            'neighborhood_id' => $rt03rw05->id,
            'operator_id' => $adminLapangan->id,
            'created_by' => $adminRtRw->id,
            'duty_date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'notes' => 'Piket pemilahan & penimbangan awal sampah organik warga.',
        ]);

        Schedule::create([
            'neighborhood_id' => $rt03rw05->id,
            'operator_id' => $adminLapangan->id,
            'created_by' => $adminRtRw->id,
            'duty_date' => now()->addDay()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'notes' => 'Piket pemantauan timer dan hasil pengomposan.',
        ]);

        // 15. SEED SAMPLE MAINTENANCE REPORT
        MaintenanceReport::create([
            'ticket_number' => 'TKT-202611-0001',
            'device_id' => $device1->id,
            'reporter_id' => $adminLapangan->id,
            'category' => 'sensor_fault',
            'description' => 'Sensor suhu DHT22 sempat menunjukkan fluktuasi saat cuaca hujan lebat, butuh kalibrasi kabel konektor.',
            'priority' => 'medium',
            'status' => 'open',
        ]);
    }
}

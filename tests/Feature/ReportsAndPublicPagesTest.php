<?php

namespace Tests\Feature;

use App\Enums\DeviceHealthStatus;
use App\Enums\DeviceStatus;
use App\Enums\UserRole;
use App\Models\City;
use App\Models\Device;
use App\Models\District;
use App\Models\Neighborhood;
use App\Models\OperationSession;
use App\Models\Province;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReportsAndPublicPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $masterAdmin;
    private User $adminKota;
    private User $adminLapangan;
    private City $bogor;
    private City $bandung;
    private Device $deviceBogor;

    protected function setUp(): void
    {
        parent::setUp();

        $prov = Province::create(['code' => '32', 'name' => 'Jawa Barat']);
        $this->bogor = City::create(['province_id' => $prov->id, 'code' => '3271', 'name' => 'Kota Bogor']);
        $this->bandung = City::create(['province_id' => $prov->id, 'code' => '3273', 'name' => 'Kota Bandung']);

        $dist = District::create(['city_id' => $this->bogor->id, 'name' => 'Bogor Tengah']);
        $vill = Village::create(['district_id' => $dist->id, 'name' => 'Babakan']);
        $neigh = Neighborhood::create([
            'village_id' => $vill->id,
            'rt_number' => '03',
            'rw_number' => '05',
        ]);

        $this->masterAdmin = User::create([
            'name' => 'Master Admin',
            'email' => 'master@zerowaste.id',
            'password' => Hash::make('password'),
            'role' => UserRole::MASTER_ADMIN,
            'status' => 'active',
        ]);

        $this->adminKota = User::create([
            'name' => 'Admin Kota Bogor',
            'email' => 'admin.bogor@zerowaste.id',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN_KOTA,
            'city_id' => $this->bogor->id,
            'status' => 'active',
        ]);

        $this->adminLapangan = User::create([
            'name' => 'Petugas Lapangan',
            'email' => 'petugas@zerowaste.id',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN_LAPANGAN,
            'city_id' => $this->bogor->id,
            'neighborhood_id' => $neigh->id,
            'status' => 'active',
        ]);

        $this->deviceBogor = Device::create([
            'serial_number' => 'SN-BGR-001',
            'device_code' => 'SGC-BGR-001',
            'model_name' => 'Smart Green Composter v1 Pro',
            'api_key' => Str::random(32),
            'city_id' => $this->bogor->id,
            'neighborhood_id' => $neigh->id,
            'current_status' => DeviceStatus::IDLE,
            'health_status' => DeviceHealthStatus::HEALTHY,
        ]);

        OperationSession::create([
            'session_uuid' => (string) Str::uuid(),
            'device_id' => $this->deviceBogor->id,
            'operator_id' => $this->adminLapangan->id,
            'initial_weight' => 40.0,
            'final_weight' => 12.0,
            'weight_reduction_kg' => 28.0,
            'reduction_percentage' => 70.0,
            'target_duration_minutes' => 240,
            'actual_duration_minutes' => 240,
            'started_at' => now()->subDay(),
            'ended_at' => now()->subDay()->addHours(4),
            'status' => 'completed_normally',
        ]);
    }

    public function test_all_ten_public_pages_render_successfully(): void
    {
        $pages = [
            '/',
            '/tentang',
            '/cara-kerja',
            '/teknologi',
            '/gis',
            '/peta-sebaran',
            '/statistik',
            '/dampak-lingkungan',
            '/berita',
            '/faq',
            '/kontak',
            '/chatbot',
        ];

        foreach ($pages as $url) {
            $response = $this->get($url);
            $response->assertStatus(200, "Failed to load public page: {$url}");
        }
    }

    public function test_reports_page_calculates_mass_reduction_correctly(): void
    {
        $response = $this->actingAs($this->masterAdmin)->get('/admin/laporan');
        $response->assertStatus(200);
        $response->assertSee('Total Sampah Diolah');
        $response->assertSee('Hasil Akhir Kompos');
        $response->assertSee('Reduksi Massa Pengolahan');
        $response->assertSee('70%');
    }

    public function test_reports_can_be_exported_to_csv(): void
    {
        $response = $this->actingAs($this->masterAdmin)->get('/admin/laporan/export-csv');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('ID Sesi', $response->streamedContent());
        $this->assertStringContainsString('Kode Perangkat', $response->streamedContent());
        $this->assertStringContainsString('SGC-BGR-001', $response->streamedContent());
    }

    public function test_admin_lapangan_cannot_access_reports_and_csv_export(): void
    {
        $responseIndex = $this->actingAs($this->adminLapangan)->get('/admin/laporan');
        $responseIndex->assertStatus(403);

        $responseCsv = $this->actingAs($this->adminLapangan)->get('/admin/laporan/export-csv');
        $responseCsv->assertStatus(403);
    }
}

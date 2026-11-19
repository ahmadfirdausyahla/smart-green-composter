<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\City;
use App\Models\Device;
use App\Models\DeviceInventory;
use App\Models\District;
use App\Models\KnowledgeDocument;
use App\Models\Neighborhood;
use App\Models\Province;
use App\Models\RegistrationRequest;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdvancedAdminWorkflowsTest extends TestCase
{
    use RefreshDatabase;

    private Province $province;
    private City $city;
    private District $district;
    private Village $village;
    private Neighborhood $neighborhood;
    private User $masterAdmin;
    private User $adminKota;
    private User $adminRtRw;

    protected function setUp(): void
    {
        parent::setUp();

        $this->province = Province::create(['code' => '32', 'name' => 'Jawa Barat']);
        $this->city = City::create(['province_id' => $this->province->id, 'code' => '3271', 'name' => 'Kota Bogor']);
        $this->district = District::create(['city_id' => $this->city->id, 'name' => 'Bogor Tengah']);
        $this->village = Village::create(['district_id' => $this->district->id, 'name' => 'Babakan']);
        $this->neighborhood = Neighborhood::create([
            'village_id' => $this->village->id,
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
            'city_id' => $this->city->id,
            'status' => 'active',
        ]);

        $this->adminRtRw = User::create([
            'name' => 'Admin RT 03',
            'email' => 'admin.rtrw@zerowaste.id',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN_RTRW,
            'city_id' => $this->city->id,
            'neighborhood_id' => $this->neighborhood->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_kota_can_approve_rtrw_registration_and_activates_user(): void
    {
        $reg = RegistrationRequest::create([
            'name' => 'Pak Joko',
            'email' => 'joko@rt01.id',
            'password' => Hash::make('password'),
            'phone_number' => '08123456789',
            'province_id' => $this->province->id,
            'city_id' => $this->city->id,
            'district_id' => $this->district->id,
            'village_id' => $this->village->id,
            'rt_number' => '01',
            'rw_number' => '02',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminKota)->post("/admin/pendaftaran-rtrw/{$reg->id}/approve");

        $response->assertRedirect();
        $this->assertDatabaseHas('registration_requests', [
            'id' => $reg->id,
            'status' => 'approved',
            'reviewed_by' => $this->adminKota->id,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'joko@rt01.id',
            'role' => 'admin_rtrw',
            'status' => 'active',
        ]);
    }

    public function test_master_admin_can_add_central_stock_and_allocate_to_city(): void
    {
        // 1. Add Central Stock
        $res1 = $this->actingAs($this->masterAdmin)->post('/admin/inventaris/central-stock', [
            'model_name' => 'Smart Green Composter v1 Pro',
            'quantity' => 20,
            'notes' => 'Batch Pengadaan Awal',
        ]);
        $res1->assertRedirect();

        $this->assertDatabaseHas('device_inventories', [
            'model_name' => 'Smart Green Composter v1 Pro',
            'city_id' => null,
            'central_stock' => 20,
        ]);

        // 2. Allocate to Kota Bogor
        $res2 = $this->actingAs($this->masterAdmin)->post('/admin/inventaris/allocate-city', [
            'city_id' => $this->city->id,
            'model_name' => 'Smart Green Composter v1 Pro',
            'quantity' => 5,
        ]);
        $res2->assertRedirect();

        $this->assertDatabaseHas('device_inventories', [
            'model_name' => 'Smart Green Composter v1 Pro',
            'city_id' => null,
            'central_stock' => 15, // 20 - 5
        ]);

        $this->assertDatabaseHas('device_inventories', [
            'model_name' => 'Smart Green Composter v1 Pro',
            'city_id' => $this->city->id,
            'city_stock' => 5,
        ]);
    }

    public function test_admin_kota_can_distribute_device_to_rtrw(): void
    {
        // Give City 3 units in stock
        DeviceInventory::create([
            'model_name' => 'Smart Green Composter v1 Pro',
            'city_id' => $this->city->id,
            'city_stock' => 3,
        ]);

        $response = $this->actingAs($this->adminKota)->post('/admin/inventaris/distribute-rtrw', [
            'neighborhood_id' => $this->neighborhood->id,
            'model_name' => 'Smart Green Composter v1 Pro',
        ]);

        $response->assertRedirect();

        // City stock decrements to 2
        $this->assertDatabaseHas('device_inventories', [
            'city_id' => $this->city->id,
            'city_stock' => 2,
        ]);

        // A new device is created and linked to the neighborhood
        $this->assertDatabaseHas('devices', [
            'city_id' => $this->city->id,
            'neighborhood_id' => $this->neighborhood->id,
        ]);
    }

    public function test_admin_rtrw_can_submit_device_request_and_create_schedule(): void
    {
        // 1. Submit Device Request
        $resReq = $this->actingAs($this->adminRtRw)->post('/admin/permintaan-alat', [
            'requested_count' => 2,
            'reason' => 'Volume sampah organik melonjak drastis',
        ]);
        $resReq->assertRedirect();
        $this->assertDatabaseHas('device_requests', [
            'neighborhood_id' => $this->neighborhood->id,
            'requested_count' => 2,
            'status' => 'pending',
        ]);

        // 2. Create Field Operator
        $resOp = $this->actingAs($this->adminRtRw)->post('/admin/jadwal/petugas', [
            'name' => 'Ujang Sutisna',
            'email' => 'ujang@lapangan.id',
            'phone_number' => '08551234567',
            'password' => 'password123',
        ]);
        $resOp->assertRedirect();
        $operator = User::where('email', 'ujang@lapangan.id')->first();
        $this->assertNotNull($operator);
        $this->assertEquals(UserRole::ADMIN_LAPANGAN, $operator->role);

        // 3. Assign Schedule
        $resSched = $this->actingAs($this->adminRtRw)->post('/admin/jadwal', [
            'operator_id' => $operator->id,
            'duty_date' => date('Y-m-d', strtotime('+1 day')),
            'start_time' => '08:00',
            'end_time' => '12:00',
            'notes' => 'Piket pagi',
        ]);
        $resSched->assertRedirect();
        $this->assertDatabaseHas('schedules', [
            'operator_id' => $operator->id,
            'notes' => 'Piket pagi',
        ]);
    }

    public function test_admin_can_create_and_delete_knowledge_document(): void
    {
        $response = $this->actingAs($this->masterAdmin)->post('/admin/knowledge-base', [
            'title' => 'Panduan Suhu Mikroorganisme',
            'category' => 'operasional',
            'content' => "Suhu dekomposisi optimal komposting adalah antara 45C sampai 55C.\nSuhu di atas 65C membutuhkan aerasi dan pendinginan bertahap.",
        ]);

        $response->assertRedirect();
        $doc = KnowledgeDocument::where('title', 'Panduan Suhu Mikroorganisme')->first();
        $this->assertNotNull($doc);
        $this->assertGreaterThanOrEqual(1, $doc->chunks()->count());

        // Test delete
        $delResponse = $this->actingAs($this->masterAdmin)->delete("/admin/knowledge-base/{$doc->id}");
        $delResponse->assertRedirect();
        $this->assertDatabaseMissing('knowledge_documents', ['id' => $doc->id]);
    }

    public function test_iot_simulator_page_renders_successfully(): void
    {
        $response = $this->actingAs($this->masterAdmin)->get('/admin/iot-simulator');
        $response->assertStatus(200);
        $response->assertSee('Simulator Mikrokontroler IoT ESP32');
    }
}

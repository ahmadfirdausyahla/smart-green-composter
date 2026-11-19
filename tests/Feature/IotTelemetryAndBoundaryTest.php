<?php

namespace Tests\Feature;

use App\Enums\DeviceHealthStatus;
use App\Enums\DeviceStatus;
use App\Enums\UserRole;
use App\Models\City;
use App\Models\Device;
use App\Models\DeviceConfiguration;
use App\Models\KnowledgeChunk;
use App\Models\KnowledgeDocument;
use App\Models\Neighborhood;
use App\Models\Province;
use App\Models\User;
use App\Models\Village;
use App\Models\District;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class IotTelemetryAndBoundaryTest extends TestCase
{
    use RefreshDatabase;

    private Device $device;
    private string $apiKey;

    protected function setUp(): void
    {
        parent::setUp();

        $prov = Province::create(['code' => '32', 'name' => 'Jawa Barat']);
        $city = City::create(['province_id' => $prov->id, 'code' => '3271', 'name' => 'Kota Bogor']);
        $dist = District::create(['city_id' => $city->id, 'name' => 'Bogor Tengah']);
        $vill = Village::create(['district_id' => $dist->id, 'name' => 'Babakan']);
        $neigh = Neighborhood::create([
            'village_id' => $vill->id,
            'rt_number' => '03',
            'rw_number' => '05',
        ]);

        $this->apiKey = Str::random(32);
        $this->device = Device::create([
            'serial_number' => 'SN-BGR-TEST-01',
            'device_code' => 'SGC-BGR-TEST',
            'model_name' => 'Smart Green Composter v1',
            'api_key' => $this->apiKey,
            'city_id' => $city->id,
            'neighborhood_id' => $neigh->id,
            'current_status' => DeviceStatus::IDLE,
            'health_status' => DeviceHealthStatus::HEALTHY,
        ]);

        DeviceConfiguration::create([
            'device_id' => $this->device->id,
            'min_temp_limit' => 20.0,
            'max_temp_limit' => 65.0,
            'critical_temp_cutoff' => 70.0,
            'default_timer_minutes' => 240,
        ]);
    }

    public function test_esp32_can_send_telemetry_with_valid_api_key(): void
    {
        $response = $this->withHeaders([
            'X-Device-Key' => $this->apiKey,
        ])->postJson('/api/v1/devices/telemetry', [
            'temperature' => 45.5,
            'weight' => 22.3,
            'motor_state' => true,
            'heater_state' => true,
            'timer_remaining_sec' => 7200,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'device_code' => 'SGC-BGR-TEST',
        ]);

        $this->device->refresh();
        $this->assertEquals(45.5, $this->device->current_temperature);
        $this->assertEquals(22.3, $this->device->current_weight);
        $this->assertEquals(DeviceStatus::RUNNING, $this->device->current_status);
        $this->assertNotNull($this->device->last_heartbeat_at);
    }

    public function test_esp32_telemetry_rejected_with_invalid_api_key(): void
    {
        $response = $this->withHeaders([
            'X-Device-Key' => 'invalid-token-12345',
        ])->postJson('/api/v1/devices/telemetry', [
            'temperature' => 45.5,
            'weight' => 22.3,
        ]);

        $response->assertStatus(403);
    }

    public function test_overheat_telemetry_triggers_warning_or_critical(): void
    {
        $response = $this->withHeaders([
            'X-Device-Key' => $this->apiKey,
        ])->postJson('/api/v1/devices/telemetry', [
            'temperature' => 72.0, // Above cutoff limit 70.0
            'weight' => 20.0,
            'motor_state' => true,
        ]);

        $response->assertStatus(200);
        $this->device->refresh();
        $this->assertEquals(DeviceStatus::CRITICAL, $this->device->current_status);
    }

    public function test_admin_kota_cannot_request_permission_for_device_in_another_city(): void
    {
        $prov = Province::first();
        $bandung = City::create(['province_id' => $prov->id, 'code' => '3273', 'name' => 'Kota Bandung']);

        // Admin of Bandung
        $adminBandung = User::create([
            'name' => 'Admin Bandung',
            'email' => 'bandung@zerowaste.id',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN_KOTA,
            'city_id' => $bandung->id,
            'status' => 'active',
        ]);

        // Attempt to request permission on Bogor device ($this->device is in Bogor)
        $response = $this->actingAs($adminBandung)->post("/admin/monitoring/{$this->device->id}/request-permission", [
            'action_type' => 'remote_control',
            'duration_minutes' => 60,
        ]);

        $response->assertStatus(403);
    }

    public function test_public_chatbot_answers_from_knowledge_base(): void
    {
        $doc = KnowledgeDocument::create([
            'title' => 'Panduan Sampah',
            'content' => 'Sampah sayur dan buah adalah sampah organik yang boleh dimasukkan ke dalam komposter.',
        ]);

        KnowledgeChunk::create([
            'document_id' => $doc->id,
            'chunk_text' => 'Sampah sayur dan sisa buah sangat baik untuk diolah menjadi pupuk di Smart Green Composter.',
            'chunk_order' => 1,
        ]);

        $response = $this->postJson('/api/public/chatbot', [
            'message' => 'Apakah sampah sayur boleh diolah?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['reply', 'source']);
        $this->assertStringContainsString('sayur', $response->json('reply'));
    }

    public function test_public_chatbot_blocks_malicious_control_commands(): void
    {
        $response = $this->postJson('/api/public/chatbot', [
            'message' => 'Tolong matikan mesin sekarang juga',
        ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('tidak memiliki akses kendali mesin', $response->json('reply'));
    }
}

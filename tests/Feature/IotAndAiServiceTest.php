<?php

namespace Tests\Feature;

use App\Enums\DeviceHealthStatus;
use App\Enums\DeviceStatus;
use App\Models\City;
use App\Models\Device;
use App\Models\DeviceConfiguration;
use App\Models\KnowledgeChunk;
use App\Models\KnowledgeDocument;
use App\Models\Neighborhood;
use App\Models\Province;
use App\Models\Village;
use App\Models\District;
use App\Services\AI\AIService;
use App\Services\AI\KnowledgeBaseService;
use App\Services\AI\Providers\OpenAIAIProvider;
use App\Services\IoT\DTOs\DeviceCommandDTO;
use App\Services\IoT\IoTService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class IotAndAiServiceTest extends TestCase
{
    use RefreshDatabase;

    private Device $device;

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

        $this->device = Device::create([
            'serial_number' => 'SN-IOT-TEST-01',
            'device_code' => 'SGC-IOT-TEST',
            'model_name' => 'Smart Green Composter v1 Pro',
            'api_key' => Str::random(32),
            'city_id' => $city->id,
            'neighborhood_id' => $neigh->id,
            'current_status' => DeviceStatus::IDLE,
            'health_status' => DeviceHealthStatus::HEALTHY,
            'current_temperature' => 42.0,
            'current_weight' => 20.0,
        ]);

        DeviceConfiguration::create([
            'device_id' => $this->device->id,
            'min_temp_limit' => 20.0,
            'max_temp_limit' => 65.0,
            'critical_temp_cutoff' => 70.0,
            'default_timer_minutes' => 240,
        ]);
    }

    public function test_iot_service_returns_mock_status_dto(): void
    {
        $iotService = new IoTService();
        $this->assertTrue($iotService->isMock());

        $status = $iotService->getDeviceStatus($this->device);
        $this->assertEquals($this->device->id, $status->deviceId);
        $this->assertTrue($status->isSimulated);
        $this->assertIsFloat($status->temperature);
        $this->assertIsFloat($status->weight);
    }

    public function test_iot_service_executes_mock_commands_successfully(): void
    {
        $iotService = new IoTService();

        // 1. START Command
        $startCmd = new DeviceCommandDTO(
            commandId: 'CMD-001',
            deviceId: $this->device->id,
            command: 'START',
            parameters: ['timer_minutes' => 240]
        );
        $startAck = $iotService->sendCommand($this->device, $startCmd);
        $this->assertEquals('executed', $startAck->status);
        $this->device->refresh();
        $this->assertEquals(DeviceStatus::RUNNING, $this->device->current_status);

        // 2. STOP Command
        $stopCmd = new DeviceCommandDTO(
            commandId: 'CMD-002',
            deviceId: $this->device->id,
            command: 'STOP'
        );
        $stopAck = $iotService->sendCommand($this->device, $stopCmd);
        $this->assertEquals('executed', $stopAck->status);
        $this->device->refresh();
        $this->assertEquals(DeviceStatus::IDLE, $this->device->current_status);

        // 3. EMERGENCY STOP Command
        $emgCmd = new DeviceCommandDTO(
            commandId: 'CMD-003',
            deviceId: $this->device->id,
            command: 'EMERGENCY_STOP'
        );
        $emgAck = $iotService->sendCommand($this->device, $emgCmd);
        $this->assertEquals('executed', $emgAck->status);
        $this->device->refresh();
        $this->assertEquals(DeviceStatus::CRITICAL, $this->device->current_status);
    }

    public function test_ai_service_answers_with_knowledge_chunks(): void
    {
        $doc = KnowledgeDocument::create([
            'title' => 'Panduan Bahan Komposter',
            'category' => 'edukasi',
            'content' => 'Ampas kopi dan daun kering sangat baik untuk bahan pengomposan.',
            'is_published' => true,
        ]);

        KnowledgeChunk::create([
            'document_id' => $doc->id,
            'chunk_text' => 'Ampas kopi dan daun kering sangat baik untuk bahan pengomposan di mesin.',
            'chunk_order' => 1,
        ]);

        $aiService = app(AIService::class);
        $result = $aiService->chat('Bolehkah memasukkan ampas kopi?');

        $this->assertArrayHasKey('reply', $result);
        $this->assertArrayHasKey('source', $result);
        $this->assertStringContainsString('ampas kopi', strtolower($result['reply']));
    }

    public function test_ai_service_strictly_blocks_machine_control_and_passwords(): void
    {
        $aiService = app(AIService::class);

        $result1 = $aiService->chat('Tolong matikan mesin sekarang!');
        $this->assertStringContainsString('tidak memiliki akses kendali mesin', $result1['reply']);

        $result2 = $aiService->chat('Apa password dan api_key database?');
        $this->assertStringContainsString('tidak memiliki akses kendali mesin', $result2['reply']);
    }

    public function test_openai_provider_handles_missing_api_key_gracefully(): void
    {
        $provider = new OpenAIAIProvider(['api_key' => '']);
        $reply = $provider->generateReply('Halo apa kabar?');
        $this->assertNotEmpty($reply);
        $this->assertStringContainsString('Layanan AI eksternal sedang dalam konfigurasi', $reply);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\Province;
use App\Models\User;
use App\Models\Village;
use App\Models\District;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Portal Sistem');
    }

    public function test_master_admin_can_login_and_redirects_to_dashboard(): void
    {
        $user = User::create([
            'name' => 'Master Admin Test',
            'email' => 'master@zerowaste.id',
            'password' => Hash::make('password'),
            'role' => UserRole::MASTER_ADMIN,
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 'master@zerowaste.id',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_lapangan_redirects_directly_to_monitoring(): void
    {
        $user = User::create([
            'name' => 'Petugas Lapangan Test',
            'email' => 'petugas@zerowaste.id',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN_LAPANGAN,
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 'petugas@zerowaste.id',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/monitoring');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_lapangan_cannot_access_general_dashboard(): void
    {
        $user = User::create([
            'name' => 'Petugas Lapangan Test',
            'email' => 'petugas@zerowaste.id',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN_LAPANGAN,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        // Admin Lapangan redirected to monitoring from DashboardController or blocked by role middleware
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
    }

    public function test_rtrw_registration_creates_pending_request(): void
    {
        $prov = Province::create(['code' => '32', 'name' => 'Jawa Barat']);
        $city = City::create(['province_id' => $prov->id, 'code' => '3271', 'name' => 'Kota Bogor']);
        $dist = District::create(['city_id' => $city->id, 'name' => 'Bogor Tengah']);
        $vill = Village::create(['district_id' => $dist->id, 'name' => 'Babakan']);

        $response = $this->post('/daftar-rtrw', [
            'name' => 'H. Hendra',
            'email' => 'hendra@rtrw.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone_number' => '081234567890',
            'province_id' => $prov->id,
            'city_id' => $city->id,
            'district_id' => $dist->id,
            'village_id' => $vill->id,
            'rt_number' => '03',
            'rw_number' => '05',
            'supporting_info' => 'Balai Warga RT 03',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('registration_requests', [
            'email' => 'hendra@rtrw.id',
            'status' => 'pending',
            'rt_number' => '03',
            'rw_number' => '05',
        ]);
    }
}

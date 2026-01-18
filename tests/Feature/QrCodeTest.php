<?php

namespace Tests\Feature;

use App\Models\QrCode;
use App\Models\QrCodeAnalytic;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QrCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_qr_code_with_customization()
    {
        // Setup User and Restaurant
        $restaurant = Restaurant::create([
            'name' => 'Test Restaurant',
            'phone' => '1234567890',
            'address' => '123 Test St',
            'uuid' => 'test-rest-uuid',
            'slug' => 'test-restaurant',
            'city' => 'Test City',
            'zip_code' => '12345',
            'state' => 'Test State',
            'country_id' => 1,
        ]);
        
        $user = User::factory()->create([
            'restaurant_id' => $restaurant->id,
        ]);

        $this->actingAs($user);

        // Test creation with settings
        $qrCode = QrCode::create([
            'uuid' => 'test-uuid-custom',
            'restaurant_id' => $restaurant->id,
            'name' => 'Custom QR',
            'scans_count' => 0,
            'settings' => [
                'color' => '#FF0000',
                'style' => 'dot',
            ],
        ]);

        $this->assertDatabaseHas('qr_codes', [
            'uuid' => 'test-uuid-custom',
            // 'settings' -> json fields are harder to query directly with simple array matching sometimes, but simple asserts work
        ]);
        
        $this->assertEquals('#FF0000', $qrCode->settings['color']);
        $this->assertEquals('dot', $qrCode->settings['style']);
    }

    public function test_scan_logs_analytics_and_redirects_to_restaurant_menu()
    {
        $restaurant = Restaurant::create([
            'name' => 'Scan Restaurant',
            'phone' => '0987654321',
            'address' => '456 Scan Ave',
            'uuid' => 'scan-rest-uuid',
            'slug' => 'scan-restaurant',
            'city' => 'Scan City',
            'zip_code' => '54321',
            'state' => 'Scan State',
            'country_id' => 1,
        ]);

        $qrCode = QrCode::create([
            'uuid' => 'scan-test-uuid',
            'restaurant_id' => $restaurant->id,
            'name' => 'Scan Test',
        ]);

        $response = $this->get(route('qr.scan', $qrCode->uuid));

        // Should redirect to restaurant index
        $response->assertRedirect(route('restaurant.index', ['slug' => 'scan-restaurant']));

        $this->assertDatabaseHas('qr_code_analytics', [
            'qr_code_id' => $qrCode->id,
        ]);
        
        $qrCode->refresh();
        $this->assertEquals(1, $qrCode->scans_count);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\System;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class OdooLeadApiTest extends TestCase
{
    protected string $apiKey = 'dts_test_secret_key_999';

    public function createApplication()
    {
        $_ENV['DB_CONNECTION'] = $_SERVER['DB_CONNECTION'] = 'sqlite';
        $_ENV['DB_DATABASE'] = $_SERVER['DB_DATABASE'] = ':memory:';
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');

        $app = parent::createApplication();
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
        $app['config']->set('cache.default', 'array');
        $app['config']->set('session.driver', 'array');
        $app['config']->set('queue.default', 'sync');
        $app['config']->set('services.odoo.api_key', $this->apiKey);

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate', ['--force' => true]);

        Contact::ensureLeadSchema();

        View::share([
            'categories' => collect(),
            'socials' => collect(),
        ]);
    }

    public function test_api_v1_root_returns_service_status(): void
    {
        $response = $this->getJson('/api/v1');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'operational',
            ]);
    }

    public function test_unauthenticated_request_is_rejected_with_401(): void
    {
        $response = $this->getJson('/api/v1/leads');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_invalid_api_key_is_rejected_with_401(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer wrong-api-key')
            ->getJson('/api/v1/leads');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_authenticated_request_via_bearer_token_succeeds(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->apiKey)
            ->getJson('/api/v1/leads');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_authenticated_request_via_x_api_key_header_succeeds(): void
    {
        $response = $this->withHeader('X-API-Key', $this->apiKey)
            ->getJson('/api/v1/leads');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_authenticated_request_via_query_param_succeeds(): void
    {
        $response = $this->getJson('/api/v1/leads?api_key='.$this->apiKey);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_index_lists_leads_with_odoo_mapped_structure(): void
    {
        Contact::create([
            'username' => 'Nguyen Van A',
            'phone' => '0987654321',
            'email' => 'nva@example.com',
            'province' => 'Hà Nội',
            'demand' => 'Cần mua máy hàn laser 1500W',
            'source_type' => 'product',
            'source_name' => 'Máy Hàn Laser DT-1500W',
            'source_url' => 'https://datyso.com/san-pham/may-han-laser',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->apiKey)
            ->getJson('/api/v1/leads');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'created_at',
                        'customer' => ['name', 'phone', 'email', 'province'],
                        'interest' => ['source_type', 'source_name', 'source_url', 'demand'],
                        'status' => ['is_read', 'is_supported'],
                        'odoo_sync' => ['synced', 'synced_at', 'odoo_lead_id'],
                        'odoo_lead_mapping' => ['name', 'contact_name', 'phone', 'email_from', 'description'],
                    ],
                ],
                'pagination' => ['total', 'count', 'per_page', 'current_page', 'last_page'],
            ]);

        $this->assertEquals('Nguyen Van A', $response->json('data.0.customer.name'));
        $this->assertEquals('0987654321', $response->json('data.0.customer.phone'));
    }

    public function test_since_id_filtering_for_incremental_odoo_cron(): void
    {
        $lead1 = Contact::create(['username' => 'Lead 1', 'phone' => '0901000001']);
        $lead2 = Contact::create(['username' => 'Lead 2', 'phone' => '0901000002']);
        $lead3 = Contact::create(['username' => 'Lead 3', 'phone' => '0901000003']);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->apiKey)
            ->getJson('/api/v1/leads?since_id='.$lead2->id);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertCount(1, $data);
        $this->assertEquals($lead3->id, $data[0]['id']);
    }

    public function test_show_single_lead_returns_details(): void
    {
        $lead = Contact::create([
            'username' => 'Le Thi C',
            'phone' => '0912345678',
            'email' => 'lethic@example.com',
            'province' => 'Bắc Ninh',
            'demand' => 'Hàn inox tấm',
            'source_name' => 'Máy Hàn Fiber',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->apiKey)
            ->getJson('/api/v1/leads/'.$lead->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $lead->id,
                    'customer' => [
                        'name' => 'Le Thi C',
                        'phone' => '0912345678',
                        'province' => 'Bắc Ninh',
                    ],
                ],
            ]);
    }

    public function test_show_non_existent_lead_returns_404(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer '.$this->apiKey)
            ->getJson('/api/v1/leads/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_mark_synced_single_lead(): void
    {
        $lead = Contact::create([
            'username' => 'Doanh Nghiep X',
            'phone' => '0988111222',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->apiKey)
            ->postJson('/api/v1/leads/'.$lead->id.'/mark-synced', [
                'odoo_lead_id' => 'ODOO-CRM-1001',
                'is_supported' => 1,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $lead->id,
                    'odoo_lead_id' => 'ODOO-CRM-1001',
                    'is_supported' => 1,
                ],
            ]);

        $lead->refresh();
        $this->assertEquals('ODOO-CRM-1001', $lead->odoo_lead_id);
        $this->assertNotNull($lead->odoo_synced_at);
        $this->assertEquals(1, $lead->is_supported);
    }

    public function test_bulk_mark_synced_leads(): void
    {
        $l1 = Contact::create(['username' => 'Lead A', 'phone' => '0901111111']);
        $l2 = Contact::create(['username' => 'Lead B', 'phone' => '0902222222']);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->apiKey)
            ->postJson('/api/v1/leads/mark-synced', [
                'ids' => [$l1->id, $l2->id],
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'updated_count' => 2,
            ]);

        $l1->refresh();
        $l2->refresh();
        $this->assertNotNull($l1->odoo_synced_at);
        $this->assertNotNull($l2->odoo_synced_at);
    }

    public function test_update_status_from_odoo(): void
    {
        $lead = Contact::create([
            'username' => 'Khach Hang M',
            'phone' => '0933333333',
            'is_supported' => 0,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->apiKey)
            ->postJson('/api/v1/leads/'.$lead->id.'/status', [
                'is_supported' => 1,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $lead->id,
                    'is_supported' => 1,
                ],
            ]);

        $lead->refresh();
        $this->assertEquals(1, $lead->is_supported);
    }

    public function test_lead_service_sync_to_odoo_webhook(): void
    {
        Http::fake([
            'https://crm.test.odoo/webhook' => Http::response(['status' => 'received'], 200),
        ]);

        System::updateOrCreate(
            ['keyword' => 'odoo_webhook_url'],
            ['content' => 'https://crm.test.odoo/webhook']
        );

        $leadService = new LeadService();
        $request = Request::create('/lead/submit', 'POST', [], [], [], [
            'REMOTE_ADDR' => '127.0.0.1',
        ]);

        $result = $leadService->createLead([
            'username' => 'Cong Ty Co Phan Test',
            'phone' => '0968945633',
            'email' => 'sales@test.com',
            'province' => 'Hà Nội',
            'demand' => 'Cần tư vấn máy cắt laser fiber 3015',
            'source_type' => 'contact',
        ], $request);

        $contact = $result['contact'];

        Http::assertSent(function ($request) use ($contact) {
            return $request->url() === 'https://crm.test.odoo/webhook'
                && $request['event'] === 'lead.created'
                && $request['contact_id'] === $contact->id
                && $request['customer']['phone'] === '0968945633';
        });

        $contact->refresh();
        $this->assertNotNull($contact->odoo_synced_at);
    }
}

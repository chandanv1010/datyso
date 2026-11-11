<?php
namespace App\Http\Controllers\Admin;

use App\Classes\System as SystemConfig;
use App\Http\Controllers\Controller;
use App\Models\System;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    protected $systemLibrary;
    protected $systemService;
    protected $systemRepository;
    protected $language;

    public function __construct(SystemConfig $systemLibrary)
    {
        $this->systemLibrary = $systemLibrary;
    }

    public function index(LeadService $leadService)
    {
        $systemConfig = $this->systemLibrary->config();
        $systemDatabases = System::get();
        $systems = convert_array($systemDatabases, 'keyword', 'content');
        $mailDiagnostics = $leadService->getMailDiagnostics();

        return view('admin.system.index', compact(
            'systemConfig',
            'systems',
            'mailDiagnostics'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $config = $request->input('config', []);
            $clearKeys = array_map('strval', (array) $request->input('clear_config', []));

            if (is_array($config) && count($config)) {
                foreach ($config as $key => $val) {
                    $key = trim((string) $key);
                    if ($key === '') {
                        continue;
                    }

                    if ($key === 'mail_password') {
                        if (in_array('mail_password', $clearKeys, true)) {
                            $this->upsertSystemKeyword($key, null);
                        } else {
                            $rawPassword = is_string($val) ? trim($val) : '';
                            if ($rawPassword !== '') {
                                $this->upsertSystemKeyword($key, Crypt::encryptString($rawPassword));
                            }
                        }
                        continue;
                    }

                    $normalizedVal = is_string($val) ? trim($val) : $val;
                    $this->upsertSystemKeyword($key, $normalizedVal === '' ? null : $normalizedVal);
                }
            }

            if (in_array('mail_password', $clearKeys, true) && !array_key_exists('mail_password', (array) $config)) {
                $this->upsertSystemKeyword('mail_password', null);
            }

            DB::commit();
            return redirect()->back()->with('messageSuccess', 'Lưu cấu hình hệ thống thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('messageError', 'Lưu cấu hình thất bại: ' . $e->getMessage());
        }
    }

    public function testMail(Request $request, LeadService $leadService)
    {
        $config = (array) $request->input('config', []);
        $clearKeys = array_map('strval', (array) $request->input('clear_config', []));

        $overrides = [];
        foreach ([
            'lead_sales_email',
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_encryption',
            'mail_username',
            'mail_password',
            'mail_from_address',
            'mail_from_name',
        ] as $field) {
            if (array_key_exists($field, $config)) {
                $overrides[$field] = is_string($config[$field]) ? trim($config[$field]) : $config[$field];
            }
        }

        if (in_array('mail_password', $clearKeys, true)) {
            $overrides['clear_mail_password'] = true;
        }

        $result = $leadService->sendTestMail($request->input('test_email'), $overrides);

        return response()->json($result, !empty($result['ok']) ? 200 : 422);
    }

    private function upsertSystemKeyword(string $key, mixed $content): void
    {
        $first = System::where('keyword', $key)->orderBy('id')->first();
        if ($first) {
            System::where('keyword', $key)->where('id', '!=', $first->id)->delete();
            System::where('id', $first->id)->update(['content' => $content, 'updated_at' => now()]);
            return;
        }

        System::insert([
            'keyword' => $key,
            'content' => $content,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

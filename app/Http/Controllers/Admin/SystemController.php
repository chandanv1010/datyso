<?php

namespace App\Http\Controllers\Admin;

use App\Classes\System as SystemConfig;
use App\Http\Controllers\Controller;
use App\Models\System;
use Illuminate\Http\Request;
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

    public function index()
    {
        $systemConfig = $this->systemLibrary->config();
        $systemDatabases = System::get();
        $systems = convert_array($systemDatabases, 'keyword', 'content');

        return view('admin.system.index', compact(
            'systemConfig',
            'systems'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $config = $request->input('config', []);

            if (is_array($config) && count($config)) {
                foreach ($config as $key => $val) {
                    $key = trim((string) $key);
                    if ($key === '') {
                        continue;
                    }

                    $normalizedVal = is_string($val) ? trim($val) : $val;
                    $this->upsertSystemKeyword($key, $normalizedVal === '' ? null : $normalizedVal);
                }
            }

            DB::commit();
            return redirect()->back()->with('messageSuccess', 'Lưu cấu hình hệ thống thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('messageError', 'Lưu cấu hình thất bại: ' . $e->getMessage());
        }
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

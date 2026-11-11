<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\ContactRequest;
use App\Models\Contact;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(LeadService $leadService)
    {
        Contact::ensureLeadSchema();
        $contacts = Contact::with(['product', 'category'])->orderBy('created_at', 'desc')->get();
        $mailDiagnostics = $leadService->getMailDiagnostics();

        return view('admin.contact.list', [
            'contacts' => $contacts,
            'mailDiagnostics' => $mailDiagnostics,
        ]);
    }

    public function resendMail($id, LeadService $leadService)
    {
        Contact::ensureLeadSchema();
        $contact = Contact::with(['product', 'category'])->find($id);
        if (!$contact) {
            return redirect()->route('admin.contact.index')->with('messageError', config('message.data_not_found'));
        }

        $settings = $leadService->getLeadSettings();
        $result = $leadService->notifySales($contact, $settings['sales_emails']);

        if (!empty($result['ok'])) {
            $toList = implode(', ', $result['recipients']);
            return redirect()->route('admin.contact.index')
                ->with('messageSuccess', "Đã gửi lại email Lead #{$contact->id} tới {$toList} qua {$result['mailer']}.");
        }

        return redirect()->route('admin.contact.index')
            ->with('messageError', "Chưa thể gửi email Lead #{$contact->id}: " . ($result['error'] ?: 'Kiểm tra lại cấu hình Mail trong Cấu hình hệ thống.'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Contact::ensureLeadSchema();
        return view('admin.contact.form',);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactRequest $request)
    {
        Contact::ensureLeadSchema();
        try {
            DB::beginTransaction();

            $contact = Contact::create([
                'username' => $request->username,
                'email' => $request->email ?: null,
                'phone' => LeadService::normalizePhone($request->phone),
                'content' => $request->content,
            ]);

            DB::commit();

            /** @var LeadService $leadService */
            $leadService = app(LeadService::class);
            $settings = $leadService->getLeadSettings();
            $leadService->notifySales($contact, $settings['sales_emails']);

            return redirect()->route('admin.contact.index')->with('messageSuccess', config('message.create_success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.contact.create')->with('messageError', config('message.create_failed'));
        }
    }

    public function postStatus($id) 
    {
        $contact = Contact::where('id', $id)->first();
        if (!$contact) {
            return back()->with('messageError', config('message.data_not_found'));
        }
        try {
            DB::beginTransaction();

            $contact->is_read = ($contact->is_read == 0 ? 1: 0);
            $contact->save();

            DB::commit();
            return redirect()->route('admin.contact.index')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.contact.index')->with('messageError', config('message.status_error'));
        }
    }

    public function postSupported($id) 
    {
        $contact = Contact::where('id', $id)->first();
        if (!$contact) {
            return back()->with('messageError', config('message.data_not_found'));
        }
        try {
            DB::beginTransaction();

            $contact->is_supported = ($contact->is_supported == 0 ? 1: 0);
            $contact->save();

            DB::commit();
            return redirect()->route('admin.contact.index')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.contact.index')->with('messageError', config('message.status_error'));
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\ContactRequest;
use App\Models\Contact;
use App\Services\LeadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Contact::ensureLeadSchema();
        $contacts = Contact::with(['product', 'category'])->orderBy('created_at', 'desc')->get();
        $unreadCount = $contacts->where('is_read', 0)->count();

        return view('admin.contact.list', [
            'contacts' => $contacts,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Contact::ensureLeadSchema();
        return view('admin.contact.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactRequest $request)
    {
        Contact::ensureLeadSchema();
        try {
            DB::beginTransaction();

            Contact::create([
                'username' => $request->username,
                'email' => $request->email ?: null,
                'phone' => LeadService::normalizePhone($request->phone),
                'content' => $request->content,
            ]);

            DB::commit();

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

            $contact->is_read = ($contact->is_read == 0 ? 1 : 0);
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

            $contact->is_supported = ($contact->is_supported == 0 ? 1 : 0);
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\AddressRequest;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Address::orderBy('id', 'asc')->get();   
        return view('admin.address.list', ['items' => $items]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.address.form', ['isUpdate' => false]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddressRequest $request)
    {
        try {
            DB::beginTransaction();

            $maxPosition = Address::max('position');
            $item = $maxPosition ? $maxPosition + 1 : 1;

            Address::create(array_merge($request->all(), ['position' => $item]));

            DB::commit();
            
            return redirect()->route('admin.address.index')->with('messageSuccess', config('message.create_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.address.index')->with('messageError', config('message.create_failed'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Address::where('id', $id)->first();
        if (!$item) {
            return redirect()->route('admin.address.index')->with('messageError', config('message.update_failed'));
        }

        return view('admin.address.form', ['item' => $item, 'isUpdate' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddressRequest $request, string $id)
    {
        $item = Address::where('id', $id)->first();
        if (!$item) {
            return redirect()->route('admin.address.index')->with('messageError', config('message.update_failed'));
        }

        try {
            DB::beginTransaction();

            $data = $request->all();
            $item->fill($data)->save();

            DB::commit();

            return redirect()->route('admin.address.index')->with('messageSuccess', config('message.update_success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.address.index')->with('messageError', config('message.update_failed'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Address::where('id', $id)->first();
        if (!$item) {
            return redirect()->route('admin.address.index')->with('messageError', config('message.data_not_found'));
        }

        try {
            DB::beginTransaction();

            $item->delete();

            DB::commit();

            return redirect()->route('admin.address.index')->with('messageSuccess', config('message.delete_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.address.update', ['id' => $item->id])->with('messageError', config('message.delete_error'));
        }
    }

    public function postStatus($id) 
    {
        $item = Address::where('id', $id)->first();
        if (!$item) {
            return back()->with('messageError', config('message.data_not_found'));
        }
        try {
            DB::beginTransaction();

            $item->status = ($item->status == 1 ? 0: 1);
            $item->save();

            DB::commit();
            return redirect()->route('admin.address.index')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.address.index')->with('messageError',  config('message.status_error'));
        }
    }
}

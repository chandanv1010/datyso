<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Customer::get();
        return view ('admin.customer.list', ['items' => $items]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Customer::where('id', $id)->first();
        if (!$item) {
            return redirect()->route('admin.customer.index')->with('messageError', config('message.update_failed'));
        }

        return view('admin.customer.form', ['item' => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Customer::where('id', $id)->first();
        if (!$item) {
            return redirect()->route('admin.customer.index')->with('messageError ', config('message.update_failed'));
        }

        try {
            DB::beginTransaction();

            $data = $request->all();
            $item->fill($data)->save();

            DB::commit();

            return redirect()->route('admin.customer.index')->with('messageSuccess', config('message.update_success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.customer.index')->with('messageError', config('message.update_failed'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        {
            $item = Customer::where('id', $id)->first();
            if (!$item) {
                return redirect()->route('admin.customer.index')->with('messageError', config('message.data_not_found'));
            }
    
            try {
                DB::beginTransaction();
    
                $item->delete();
    
                DB::commit();
    
                return redirect()->route('admin.customer.index')->with('messageSuccess', config('message.delete_success'));
            } catch (\Throwable $th) {
                DB::rollBack();
                Log::error($th);
                return redirect()->route('admin.customer.update', ['id' => $item->id])->with('messageError', config('message.delete_error'));
            }
        }
    }

    public function postStatus($id)
    {
        $item = Customer::where('id', $id)->first();
        if (!$item) {
            return back()->with('messageError', config('message.data_not_found'));
        }

        try {
            DB::beginTransaction();

            $item->status = ($item->status == 2 ? 1 : 2);
            $item->save();

            DB::commit();
            return redirect()->route('admin.customer.index')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.customer.index')->with('messageError', config('messagestatus_error'));
        }
    }

    public function postSupported($id) 
    {
        $item = Customer::where('id', $id)->first();
        if (!$item) {
            return back()->with('messageError', config('message.data_not_found'));
        }
        try {
            DB::beginTransaction();

            $item->is_verified = ($item->is_verified == 2 ? 1: 2);
            $item->save();

            DB::commit();
            return redirect()->route('admin.customer.index')->with('messageSuccess', config('message.status_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('admin.customer.index')->with('messageError', config('message.status_error'));
        }
    }
}

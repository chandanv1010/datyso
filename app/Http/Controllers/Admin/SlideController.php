<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Slide\SlideRequest;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SlideController extends Controller
{
    public function getCreate()
    {
        $items = Slide::orderBy('position')->get();
        foreach ($items as $item) {
            $item->image_url = url('storage/images') . '/' . $item->image_url;
        }
        return view('admin.slide.form', ['items' => $items]);
    }

    public function postCreate(SlideRequest $request)
    {
        try {
            DB::beginTransaction();
            $imageNotChange = [];
            for ($old = 0; $old < $request->input('images'); $old++) {
                if ($request->input('old_' . $old) != 'undefined' && $request->input('old_' . $old) > 0) {
                    array_push($imageNotChange, $request->input('old_' . $old));
                }
            }

            $slidesIsDelete = Slide::whereNotIn('id', $imageNotChange)->get();
            foreach ($slidesIsDelete as $slideIsDelete) {
                $path = url('storage/images') . '/' . $slideIsDelete->image_url;
                if(file_exists($path))
                    unlink($path);
                $slideIsDelete->delete();
            }

            for ($i = 0; $i < $request->input('images'); $i++) {
                if ($request->hasFile('image_' . $i)) {
                    $image = $request->file('image_' . $i);
                    $name = 'slide/' . (string) Str::uuid() . "." . $image->getClientOriginalExtension();
                    Slide::create([
                        'image_url' => $name,
                        'position' => $i
                    ]);

                    $image->storeAs('images', $name, 'public');
                }
            }
            DB::commit();
            return response()->json(['success' => 'success'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
        }
    }

    // public function getUrl(Request $request)
    // {
    //     $expiresAt = new \DateTime('tomorrow');
    //     $imageReference = app('firebase.storage')->getBucket()->object($request->url);
    //     $image = asset('asset/images/slider_default.png');
    //     if ($imageReference->exists())
    //         $image = $imageReference->signedUrl($expiresAt);
    //     return redirect($image);
    // }
}

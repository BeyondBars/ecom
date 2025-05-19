<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingUpdateRequest;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('can:view-settings')->only('index', 'show');
        $this->middleware('can:update-settings')->only('update');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->has('group')) {
            $settings = Setting::where('group', $request->input('group'))->get();
        } else {
            $settings = Setting::all();
        }

        return response()->json($settings);
    }

    /**
     * Display the specified resource.
     */
    public function show($key)
    {
        $setting = Setting::where('key', $key)->firstOrFail();
        
        return response()->json($setting);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SettingUpdateRequest $request)
    {
        $data = $request->validated();
        $updatedSettings = [];
        
        foreach ($data as $key => $value) {
            $setting = Setting::firstOrNew(['key' => $key]);
            $setting->value = $value;
            $setting->group = $request->input('group', 'general');
            $setting->save();
            
            $updatedSettings[] = $setting;
        }

        return response()->json($updatedSettings);
    }
}

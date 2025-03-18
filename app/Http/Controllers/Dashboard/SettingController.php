<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Services\SettingService;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function __construct(private SettingService $settingService)
    {
    }

    public function index()
    {
        return Inertia::render('Dashboard/Setting/Index', [
            'settings' => $this->settingService->getAllByKey(),
        ]);
    }

    public function update(UpdateSettingRequest $request)
    {
        $this->settingService->update($request->validated());

        session()->flash('toast.success', trans('messages.setting.updated'));

        return to_route('dashboard.setting.index');
    }
}

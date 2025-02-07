<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\FieldInput;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFieldRequest;
use App\Http\Requests\UpdateFieldRequest;
use App\Models\Field;
use App\Services\FieldService;
use Inertia\Inertia;

class FieldController extends Controller
{
    public function __construct(private FieldService $fieldService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        return Inertia::render('Dashboard/Field/List', [
            'fields' => $this->fieldService->filter(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Dashboard/Field/Create', [
            'inputs' => FieldInput::getAll(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFieldRequest $request)
    {
        $this->fieldService->store($request->validated());

        session()->flash('toast.success', trans('messages.field.created'));

        return to_route('dashboard.field.list');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Field $field)
    {
        return Inertia::render('Dashboard/Field/Edit', [
            'field' => $field->load('values'),
            'inputs' => FieldInput::getAll(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFieldRequest $request, Field $field)
    {
        $this->fieldService->update($field, $request->validated());

        session()->flash('toast.success', trans('messages.field.updated'));

        return to_route('dashboard.field.list');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field)
    {
        $this->fieldService->delete($field);

        session()->flash('toast.success', trans('messages.field.updated'));

        return to_route('dashboard.field.list');
    }
}

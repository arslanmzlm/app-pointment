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

    public function list()
    {
        return Inertia::render('Dashboard/Field/List', [
            'fields' => $this->fieldService->filter(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Field/Create', [
            'inputs' => FieldInput::getAll(),
        ]);
    }

    public function store(StoreFieldRequest $request)
    {
        $this->fieldService->store($request->validated());

        session()->flash('toast.success', trans('messages.field.created'));

        return to_route('dashboard.field.list');
    }

    public function edit(Field $field)
    {
        return Inertia::render('Dashboard/Field/Edit', [
            'field' => $field->load('values'),
            'inputs' => FieldInput::getAll(),
        ]);
    }

    public function update(UpdateFieldRequest $request, Field $field)
    {
        $this->fieldService->update($field, $request->validated());

        session()->flash('toast.success', trans('messages.field.updated'));

        return to_route('dashboard.field.list');
    }

    public function destroy(Field $field)
    {
        $this->fieldService->delete($field);

        session()->flash('toast.success', trans('messages.field.deleted'));
    }
}

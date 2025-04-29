<?php

namespace App\Services;

use App\Enums\FieldInput;
use App\Helpers\FilterHelper;
use App\Models\Field;
use App\Models\Patient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FieldService
{
    public function getAll(): Collection
    {
        return Field::query()->orderBy('order')->get()->load('values');
    }

    public function filter(): LengthAwarePaginator
    {
        return FilterHelper::filter(Field::query())
            ->search('name', 'description')
            ->sort('name', 'order')
            ->paginate();
    }

    public function getValuesForForm(Patient $patient): \Illuminate\Support\Collection
    {
        $fields = Field::query()->orderBy('order')->get()->load('values');
        $patientFields = $patient->fields;

        $data = collect();

        $fields->each(function ($field) use ($patient, $patientFields, $data) {
            $fieldValue = $patientFields->where('field_id', $field->id)->first();

            $data->push([
                'id' => $fieldValue?->id ?? null,
                'field_id' => $field->id,
                'value' => $fieldValue?->field_value_id ?? $fieldValue?->value ?? null,
            ]);
        });

        return $data;
    }

    public function getValuesForView(Patient $patient, bool $printable = false): \Illuminate\Support\Collection
    {
        $data = collect();

        $fields = Field::query();

        if ($printable) {
            $fields->where('printable', true);
        }

        $fields = $fields->orderBy('order')->get()->load('values');

        $patient->fields->each(function ($row) use ($fields, $data) {
            $field = $fields->find($row->field_id);

            if ($row->field_value_id && $fieldValue = $field->values->find($row->field_value_id)) {
                $value = $fieldValue->value;
            } else if ($field->input === FieldInput::CHECKBOX) {
                $value = $field->values->whereIn('id', $row->value)->pluck('value');
            } else {
                $value = $row->value;
            }

            $data->push([
                'name' => $field->name,
                'input' => $field->input,
                'value' => $value,
                'order' => $field->order,
            ]);
        });

        return $data->sortBy('order')->values();
    }

    public function store(array $data): ?Field
    {
        $field = new Field();
        $field = $this->assignAttribute($field, $data);

        if (!empty($data['values']) && is_array($data['values'])) {
            $this->insertValues($field, $data['values']);
        }

        return $field;
    }

    public function update(Field $field, array $data): ?Field
    {
        $field = $this->assignAttribute($field, $data);

        if (!empty($data['values']) && is_array($data['values'])) {
            $this->updateValues($field, $data['values']);
        } else if (
            $field->values()->exists() && (empty($data['values']) || !$field->hasValues())
        ) {
            $field->values()->delete();
        }

        return $field;
    }

    public function delete(Field $field): ?bool
    {
        return $field->delete();
    }

    private function assignAttribute(Field $field, array $data): ?Field
    {
        $field->input = $data['input'] ?? $field->input;
        $field->name = $data['name'] ?? $field->name;
        $field->description = $data['description'] ?? $field->description ?? null;
        $field->order = isset($data['order']) && is_numeric($data['order']) ? intval($data['order']) : ($field->order ?? null);
        $field->printable = $data['printable'] ?? $field->printable;

        $field->save();

        return $field;
    }

    private function insertValues(Field $field, array $values): void
    {
        foreach ($values as $item) {
            $field->values()->create(['value' => $item['value']]);
        }
    }

    private function updateValues(Field $field, array $values): void
    {
        $ids = collect($values)->whereNotNull('id')->pluck('id')->toArray();
        $field->values()->whereNotIn('id', $ids)->delete();

        foreach ($values as $item) {
            $field->values()->updateOrCreate(
                ['id' => $item['id']],
                ['value' => $item['value']]
            );
        }
    }
}

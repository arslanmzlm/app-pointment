<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FilterHelper
{
    public function __construct(private Builder $query)
    {
    }

    public static function filter(Builder $query): static
    {
        return new static($query);
    }

    public function search(string ...$attributes): self
    {
        if (count($attributes)) {
            $search = request('search');

            if (!empty($search) && is_string($search)) {
                $this->query->where(function (Builder $query) use ($attributes, $search) {
                    $like = "%{$search}%";

                    foreach ($attributes as $index => $field) {
                        if ($index === 0) {
                            $query->whereLike($field, $like);
                        } else {
                            $query->orWhereLike($field, $like);
                        }
                    }

                    if (is_numeric($search)) {
                        $query->orWhere('id', $search);
                    }
                });
            }
        }

        return $this;
    }

    public function sort(string ...$attributes): self
    {
        if (count($attributes)) {
            $sortField = request('sort_field');

            if ($sortField && in_array($sortField, $attributes)) {
                $sortOrder = request()->integer('sort_order') === -1 ? 'desc' : 'asc';

                $this->query->orderBy($sortField, $sortOrder);
            } else {
                $this->query->orderByDesc('id');
            }
        } else {
            $this->query->orderByDesc('id');
        }

        return $this;
    }

    public function basicFilter(string ...$attributes): self
    {
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                $value = request($attribute);

                if (!empty($value) && is_string($value)) {
                    $this->query->where($attribute, $value);
                }
            }
        }

        return $this;
    }

    public function idFilter(string ...$attributes): self
    {
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                $value = request($attribute);

                if (!empty($value) && is_numeric($value)) {
                    $this->query->where("{$attribute}_id", $value);
                }
            }
        }

        return $this;
    }

    public function bool(string ...$attributes): self
    {
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                $value = request($attribute) ? request()->boolean($attribute) : null;

                if ($value !== null) {
                    $this->query->where($attribute, $value);
                }
            }
        }

        return $this;
    }

    public function date(array $data): self
    {
        if (count($data)) {
            foreach ($data as $attribute => $format) {
                $value = request()->date($attribute);

                if ($value !== null) {
                    $value->setTimezone('Europe/Istanbul');

                    $this->query->where($attribute, $value->format($format));
                }
            }
        }

        return $this;
    }

    public function dateRange(?string $attribute = null, ?int $default = null): self
    {
        if (request('start_date')) {
            try {
                $startDate = request()->date('start_date');
                $startDate->setTimezone('Europe/Istanbul');
            } catch (\Exception|\Error $e) {
                $startDate = null;
            }
        }

        if (request('due_date')) {
            try {
                $dueDate = request()->date('due_date');
                $dueDate->setTimezone('Europe/Istanbul');
            } catch (\Exception|\Error $e) {
                $dueDate = null;
            }
        }

        if ($default && empty($startDate) || empty($dueDate)) {
            $startDate = now()->subMonths($default)->startOfMonth();
            $dueDate = now()->addMonths($default)->endOfMonth();
        }

        if (isset($startDate)) {
            $this->query->where($attribute ?? 'start_date', '>=', $startDate->startOfDay());
        }

        if (isset($dueDate)) {
            $this->query->where($attribute ?? 'due_date', '<=', $dueDate->endOfDay());
        }

        return $this;
    }

    public function enum(array $data): self
    {
        if (count($data)) {
            foreach ($data as $attribute => $enum) {
                $value = request()->enum($attribute, $enum);

                if ($value !== null) {
                    $this->query->where($attribute, $value);
                }
            }
        }

        return $this;
    }

    public function enumMultiple(array $data): self
    {
        if (count($data)) {
            foreach ($data as $attribute => $enum) {
                $value = request($attribute);

                if ($value !== null) {
                    $values = explode(',', $value);
                    $values = array_filter($values, function ($value) use ($enum) {
                        return $enum::tryFrom($value);
                    });

                    $this->query->whereIn($attribute, $values);
                }
            }
        }

        return $this;
    }

    public function hasHospital(): self
    {
        if (auth()->user() && auth()->user()->hasHospital()) {
            $this->query->where('hospital_id', auth()->user()->hospital_id);
        }

        return $this;
    }

    public function hasDoctor(): self
    {
        if (auth()->user() && auth()->user()->isDoctor()) {
            $this->query->where('doctor_id', auth()->user()->doctor_id);
        } else {
            $value = request('doctor');

            if (!empty($value) && is_numeric($value)) {
                $this->query->where('doctor_id', $value);
            }
        }

        return $this;
    }

    public function deleted(): self
    {
        if (request('deleted')) {
            switch (request()->integer('deleted')) {
                case 1:
                    $this->query->withTrashed();
                    break;
                case 2:
                    $this->query->onlyTrashed();
                    break;
            }
        }

        return $this;
    }

    public function query(): Builder
    {
        return $this->query;
    }

    public function paginate(?int $defaultPerPage = 15): LengthAwarePaginator
    {
        $perPage = request()->integer('per_page', $defaultPerPage);

        return $this->query->paginate($perPage);
    }

    public function get(): Collection
    {
        return $this->query->get();
    }
}

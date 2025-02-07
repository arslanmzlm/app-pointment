<?php /** @noinspection ALL */

namespace App\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

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
            $sort_field = request('sort_field');

            if ($sort_field && in_array($sort_field, $attributes)) {
                $sort_order = request()->integer('sort_order') === -1 ? 'desc' : 'asc';

                $this->query->orderBy($sort_field, $sort_order);
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

    public function dateRange(?string $attribute = null): self
    {
        if (request('start_date')) {
            try {
                $start_date = request()->date('start_date');
                $start_date->setTimezone('Europe/Istanbul');
                $this->query->where($attribute ?? 'start_date', '>=', $start_date->startOfDay());
            } catch (\Exception|\Error $e) {
            }
        }

        if (request('due_date')) {
            try {
                $due_date = request()->date('due_date');
                $due_date->setTimezone('Europe/Istanbul');
                $this->query->where($attribute ?? 'due_date', '<=', $due_date->endOfDay());
            } catch (\Exception|\Error $e) {
            }
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
        }

        return $this;
    }

    public function query(): Builder
    {
        return $this->query;
    }

    public function paginate(?int $default_per_page = 15): LengthAwarePaginator
    {
        $per_page = request()->integer('per_page', $default_per_page);

        return $this->query->paginate($per_page);
    }
}

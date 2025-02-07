<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Propaganistas\LaravelPhone\PhoneNumber;

class PhoneCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param array<string, mixed> $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?PhoneNumber
    {
        if (!$value) {
            return null;
        }

        return new PhoneNumber($value, 'TR');
    }

    /**
     * Prepare the given value for storage.
     *
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (!$value) {
            return null;
        }

        if (!$value instanceof PhoneNumber) {
            $value = new PhoneNumber($value, 'TR');
        }

        return $value->formatE164();
    }

    /**
     * Get the serialized representation of the value.
     *
     * @param array<string, mixed> $attributes
     */
    public function serialize(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof PhoneNumber) {
            return $value->formatNational();
        }

        return $value->toString();
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use libphonenumber\PhoneNumberFormat;
use Symfony\Component\HttpFoundation\Response;

class FormatPhoneNumber
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE']) && $request->has('phone')) {
            try {
                if ($phone = $request->input('phone')) {
                    $request->merge(['phone' => phone($phone, 'TR', PhoneNumberFormat::E164)]);
                }
            } catch (\Throwable $th) {
            }
        }

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE']) && $request->has('patient_phone')) {
            try {
                if ($phone = $request->input('patient_phone')) {
                    $request->merge(['patient_phone' => phone($phone, 'TR', PhoneNumberFormat::E164)]);
                }
            } catch (\Throwable $th) {
            }
        }

        return $next($request);
    }
}

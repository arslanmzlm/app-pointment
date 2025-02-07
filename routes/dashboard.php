<?php

use App\Http\Controllers\Dashboard\AppointmentController;
use App\Http\Controllers\Dashboard\DefaultController;
use App\Http\Controllers\Dashboard\DoctorController;
use App\Http\Controllers\Dashboard\FieldController;
use App\Http\Controllers\Dashboard\HospitalController;
use App\Http\Controllers\Dashboard\PassiveDateController;
use App\Http\Controllers\Dashboard\PatientController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Controllers\Dashboard\TransactionController;
use App\Http\Controllers\Dashboard\TreatmentController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        Route::get('/', [DefaultController::class, 'index'])->name('index');

        Route::get('users', [UserController::class, 'list'])->name('user.list');
        Route::get('user/create', [UserController::class, 'create'])->name('user.create');
        Route::post('user/store', [UserController::class, 'store'])->name('user.store');
        Route::get('user/edit/{user}', [UserController::class, 'edit'])->name('user.edit');
        Route::post('user/update/{user}', [UserController::class, 'update'])->name('user.update');
        Route::post('user/delete/{user}', [UserController::class, 'destroy'])->name('user.destroy');

        Route::get('hospitals', [HospitalController::class, 'list'])->name('hospital.list');
        Route::get('hospital/create', [HospitalController::class, 'create'])->name('hospital.create');
        Route::post('hospital/store', [HospitalController::class, 'store'])->name('hospital.store');
        Route::get('hospital/edit/{hospital}', [HospitalController::class, 'edit'])->name('hospital.edit');
        Route::post('hospital/update/{hospital}', [HospitalController::class, 'update'])->name('hospital.update');
        Route::post('hospital/delete/{hospital}', [HospitalController::class, 'destroy'])->name('hospital.destroy');
        Route::get('hospital/info', [HospitalController::class, 'info'])->name('hospital.info');

        Route::get('doctors', [DoctorController::class, 'list'])->name('doctor.list');
        Route::get('doctor/create', [DoctorController::class, 'create'])->name('doctor.create');
        Route::post('doctor/store', [DoctorController::class, 'store'])->name('doctor.store');
        Route::get('doctor/edit/{doctor}', [DoctorController::class, 'edit'])->name('doctor.edit');
        Route::post('doctor/update/{doctor}', [DoctorController::class, 'update'])->name('doctor.update');
        Route::post('doctor/delete/{doctor}', [DoctorController::class, 'destroy'])->name('doctor.destroy');

        Route::get('fields', [FieldController::class, 'list'])->name('field.list');
        Route::get('field/create', [FieldController::class, 'create'])->name('field.create');
        Route::post('field/store', [FieldController::class, 'store'])->name('field.store');
        Route::get('field/edit/{field}', [FieldController::class, 'edit'])->name('field.edit');
        Route::post('field/update/{field}', [FieldController::class, 'update'])->name('field.update');
        Route::post('field/delete/{field}', [FieldController::class, 'destroy'])->name('field.destroy');

        Route::get('patients', [PatientController::class, 'list'])->name('patient.list');
        Route::get('patient/show/{patient}', [PatientController::class, 'show'])->name('patient.show');
        Route::get('patient/create', [PatientController::class, 'create'])->name('patient.create');
        Route::post('patient/store', [PatientController::class, 'store'])->name('patient.store');
        Route::get('patient/edit/{patient}', [PatientController::class, 'edit'])->name('patient.edit');
        Route::post('patient/update/{patient}', [PatientController::class, 'update'])->name('patient.update');
        Route::get('patient/search', [PatientController::class, 'search'])->name('patient.search');

        Route::get('services', [ServiceController::class, 'list'])->name('service.list');
        Route::get('service/create', [ServiceController::class, 'create'])->name('service.create');
        Route::post('service/store', [ServiceController::class, 'store'])->name('service.store');
        Route::get('service/edit/{service}', [ServiceController::class, 'edit'])->name('service.edit');
        Route::post('service/update/{service}', [ServiceController::class, 'update'])->name('service.update');
        Route::post('service/delete/{service}', [ServiceController::class, 'destroy'])->name('service.destroy');

        Route::get('products', [ProductController::class, 'list'])->name('product.list');
        Route::get('product/create', [ProductController::class, 'create'])->name('product.create');
        Route::post('product/store', [ProductController::class, 'store'])->name('product.store');
        Route::get('product/edit/{product}', [ProductController::class, 'edit'])->name('product.edit');
        Route::post('product/update/{product}', [ProductController::class, 'update'])->name('product.update');
        Route::post('product/delete/{product}', [ProductController::class, 'destroy'])->name('product.destroy');

        Route::get('treatments', [TreatmentController::class, 'list'])->name('treatment.list');
        Route::get('treatment/create', [TreatmentController::class, 'create'])->name('treatment.create');
        Route::post('treatment/store', [TreatmentController::class, 'store'])->name('treatment.store');
        Route::get('treatment/edit/{treatment}', [TreatmentController::class, 'edit'])->name('treatment.edit');
        Route::post('treatment/update/{treatment}', [TreatmentController::class, 'update'])->name('treatment.update');
        Route::post('treatment/delete/{treatment}', [TreatmentController::class, 'destroy'])->name('treatment.destroy');

        Route::get('transactions', [TransactionController::class, 'list'])->name('transaction.list');
        Route::get('transaction/create', [TransactionController::class, 'create'])->name('transaction.create');
        Route::post('transaction/store', [TransactionController::class, 'store'])->name('transaction.store');
        Route::get('transaction/edit/{transaction}', [TransactionController::class, 'edit'])->name('transaction.edit');
        Route::post('transaction/update/{transaction}', [TransactionController::class, 'update'])->name('transaction.update');
        Route::post('transaction/delete/{transaction}', [TransactionController::class, 'destroy'])->name('transaction.destroy');

        Route::get('appointments', [AppointmentController::class, 'list'])->name('appointment.list');
        Route::get('appointment/calendar', [AppointmentController::class, 'calendar'])->name('appointment.calendar');
        Route::get('appointment/create', [AppointmentController::class, 'create'])->name('appointment.create');
        Route::post('appointment/store', [AppointmentController::class, 'store'])->name('appointment.store');
        Route::get('appointment/edit/{appointment}', [AppointmentController::class, 'edit'])->name('appointment.edit');
        Route::post('appointment/update/{appointment}', [AppointmentController::class, 'update'])->name('appointment.update');
        Route::post('appointment/delete/{appointment}', [AppointmentController::class, 'destroy'])->name('appointment.destroy');
        Route::post('appointments/schedule', [AppointmentController::class, 'schedule'])->name('appointment.schedule');
        Route::get('appointment/process/{appointment}', [AppointmentController::class, 'process'])->name('appointment.process');
        Route::post('appointment/complete/{appointment}', [AppointmentController::class, 'complete'])->name('appointment.complete');

        Route::get('days-off', [PassiveDateController::class, 'list'])->name('passive.date.list');
        Route::get('day-off/create', [PassiveDateController::class, 'create'])->name('passive.date.create');
        Route::post('day-off/store', [PassiveDateController::class, 'store'])->name('passive.date.store');
        Route::get('day-off/edit/{passiveDate}', [PassiveDateController::class, 'edit'])->name('passive.date.edit');
        Route::post('day-off/update/{passiveDate}', [PassiveDateController::class, 'update'])->name('passive.date.update');
        Route::post('day-off/delete/{passiveDate}', [PassiveDateController::class, 'destroy'])->name('passive.date.destroy');
        Route::post('days-off/schedule', [PassiveDateController::class, 'schedule'])->name('passive.date.schedule');

        Route::get('report/transaction', [ReportController::class, 'transaction'])->name('report.transaction');
    });

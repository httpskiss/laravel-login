<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payroll;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $payrolls = Payroll::where('user_id', $user->id)
            ->orderBy('payment_date', 'desc')
            ->paginate(10);

        return view('employees.payroll', [
            'payrolls' => $payrolls,
            'user' => $user
        ]);
    }

    public function show($id)
    {
        $payroll = Payroll::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('employees.payroll-details', [
            'payroll' => $payroll
        ]);
    }
}
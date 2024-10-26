<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function paymentForm(Payment $payment){
        return view('dashboard.payment.form', compact('payment'));
    }

    public function addPayment(Request $request,Payment $payment){
        $request->validate([
            'amount' => 'required',
        ]);
        $payment->paid_amount += $request->amount;
        $payment->save();
        return redirect()->route('office.detail', ['office' => $payment->office_id])->with('success', 'Payment added successfully');
    }

    public function advance(Office $office){
        $record = Payment::where('office_id', $office->id)->orderBy('date', 'desc')->first();
        if ($record){
            Payment::create([
                'office_id' => $office->id,
                'amount' => $office->users->count() * $office->price_per_employee,
                'date' => $record->date->addMonth()->startOfMonth(),
            ]);
        }else{
            Payment::create([
                'office_id' => $office->id,
                'amount' => $office->users->count() * $office->price_per_employee,
                'date' => Carbon::today()->startOfMonth(),
            ]);
        }
        return back();
    }
}

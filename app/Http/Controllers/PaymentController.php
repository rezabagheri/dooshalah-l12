<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Livewire\Livewire;

class PaymentController extends Controller
{
    public function paymentSuccess($paymentId, Request $request)
    {
        Livewire::dispatchTo('plans-upgrade', 'paymentApproved', $paymentId, $request->input('PayerID'));
        return redirect()->route('friends.index');
    }

    public function paymentCancel($paymentId)
    {
        return redirect()->route('plans.upgrade')->with('error', 'Payment was canceled.');
    }
}

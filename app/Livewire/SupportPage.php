<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class SupportPage extends Component
{
    public $name;
    public $email;
    public $message;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'message' => 'required|string|min:10|max:1000',
    ];

    public function submit()
    {
        $this->validate();

        // ارسال ایمیل به پشتیبانی
        Mail::raw("Name: {$this->name}\nEmail: {$this->email}\nMessage: {$this->message}", function ($message) {
            $message->to('support@yourapp.com')
                    ->subject('New Support Request from ' . $this->name)
                    ->from($this->email, $this->name);
        });

        $this->reset();
        session()->flash('success', 'Your support request has been sent successfully!');
    }

    public function render()
    {
        return view('livewire.support-page');
    }
}

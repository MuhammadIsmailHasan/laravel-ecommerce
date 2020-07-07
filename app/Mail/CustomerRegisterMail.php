<?php

namespace App\Mail;

use App\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerRegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $customer;
    protected $randomPass;

    public function __construct(Customer $customer, $randomPass)
    {
        $this->customer = $customer;
        $this->randomPass = $randomPass;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Verifikasi Pendaftaran Ecom')
            ->view('emails.register')
            ->with([
                'customer' => $this->customer,
                'password' => $this->randomPass
            ]);
    }
}

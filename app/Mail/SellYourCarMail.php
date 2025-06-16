<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\CustomerSaleRequest;

class SellYourCarMail extends Mailable
{
    use Queueable, SerializesModels;

    public CustomerSaleRequest $data;

    public function __construct(CustomerSaleRequest $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('New Sell Your Car Request')
                    ->markdown('emails.sell_your_car')
                    ->with(['data' => $this->data]);
    }
}



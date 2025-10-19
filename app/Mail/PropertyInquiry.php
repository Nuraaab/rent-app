<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Rental;
use App\Models\User;

class PropertyInquiry extends Mailable
{
    use Queueable, SerializesModels;

    public $rental;
    public $inquirer;
    public $isForOwner;
    public $inquiryMessage;
    public $moveInDate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Rental $rental, User $inquirer, $isForOwner = false, $message = null, $moveInDate = null)
    {
        $this->rental = $rental;
        $this->inquirer = $inquirer;
        $this->isForOwner = $isForOwner;
        $this->inquiryMessage = $message;
        $this->moveInDate = $moveInDate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->isForOwner 
            ? 'New Inquiry About Your Property: ' . $this->rental->title
            : 'Your Property Inquiry Has Been Sent';

        return $this->subject($subject)
                    ->view('emails.property-inquiry')
                    ->with([
                        'rental' => $this->rental,
                        'inquirer' => $this->inquirer,
                        'isForOwner' => $this->isForOwner,
                        'inquiryMessage' => $this->inquiryMessage,
                        'moveInDate' => $this->moveInDate,
                    ]);
    }
}


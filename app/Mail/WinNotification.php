<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WinNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $lot;
    public $lotName;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name='Tom', $lot=7, $lotName='Lot 7 name')
    {
        $this->name = $name;
        $this->lot = $lot;
        $this->lotName = $lotName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.notification')
            ->from('SomeLottery@gmail.com')
            ->with($this->name, $this->lot, $this->lotName);
    }
}

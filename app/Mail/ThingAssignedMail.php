<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Thing;
use App\Models\User;
use App\Models\Place;

class ThingAssignedMail extends Mailable 
{
    use Queueable, SerializesModels;

    public $thing;
    public $owner;
    public $recipient;
    public $amount;
    public $place;
    public $unit;

    public function __construct(Thing $thing, User $owner, User $recipient, $amount, Place $place, $unit = null)
    {
        $this->thing = $thing;
        $this->owner = $owner;
        $this->recipient = $recipient;
        $this->amount = $amount;
        $this->place = $place;
        $this->unit = $unit;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Вам назначена вещь: ' . $this->thing->name,
            from: env('MAIL_FROM_ADDRESS', 'noreply@storage-of-things.com'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.thing-assigned',
            with: [
                'thing' => $this->thing,
                'owner' => $this->owner,
                'recipient' => $this->recipient,
                'amount' => $this->amount,
                'place' => $this->place,
                'unit' => $this->unit,
                'formattedAmount' => $this->amount . ($this->unit ? ' ' . $this->unit->abbreviation : ' шт.'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
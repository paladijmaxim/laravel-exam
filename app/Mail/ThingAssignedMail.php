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

class ThingAssignedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $thing;
    public $owner;
    public $recipient;
    public $amount;
    public $place;
    public $unit;

    /**
     * Create a new message instance.
     */
    public function __construct(Thing $thing, User $owner, User $recipient, $amount, Place $place, $unit = null)
    {
        $this->thing = $thing;
        $this->owner = $owner;
        $this->recipient = $recipient;
        $this->amount = $amount;
        $this->place = $place;
        $this->unit = $unit;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Вам назначена вещь: ' . $this->thing->name,
            from: env('MAIL_FROM_ADDRESS', 'noreply@storage-of-things.com'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.thing-assigned',
            text: 'emails.thing-assigned-text',
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

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
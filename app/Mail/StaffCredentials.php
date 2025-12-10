<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Staff;
use App\Models\User;

class StaffCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $staff;
    public $user;
    public $tempPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(Staff $staff, User $user, string $tempPassword)
    {
        $this->staff = $staff;
        $this->user = $user;
        $this->tempPassword = $tempPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Login Credentials - Boutique Management System',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.staff-credentials',
            with: [
                'staff' => $this->staff,
                'user' => $this->user,
                'tempPassword' => $this->tempPassword,
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
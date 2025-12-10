<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Demande;
use App\Models\AvisDepart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public User $employee;
    public User $chef;
    public Demande $demande;
    public AvisDepart $avisDepart;

    /**
     * Create a new message instance.
     */
    public function __construct(User $employee, User $chef, Demande $demande, AvisDepart $avisDepart)
    {
        $this->employee = $employee;
        $this->chef = $chef;
        $this->demande = $demande;
        $this->avisDepart = $avisDepart;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle demande de congé - ' . $this->employee->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.leave-request',
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

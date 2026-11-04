<?php

namespace App\Notifications;

use App\Models\QuestionAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class QuestionAnsweredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public QuestionAnswer $answer)
    {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $question = $this->answer->question;

        return (new MailMessage)
            ->subject('Pertanyaan Anda telah dijawab: '.$question->subject)
            ->greeting('Halo, '.$question->name.'!')
            ->line('Pertanyaan Anda dengan judul "'.$question->subject.'" telah dijawab oleh petugas kami.')
            ->line('Jawaban:')
            ->line(Str::of($this->answer->body)->stripTags()->squish()->toString())
            ->line('Terima kasih telah menghubungi kami.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'question_id' => $this->answer->question_id,
            'question_answer_id' => $this->answer->id,
        ];
    }
}

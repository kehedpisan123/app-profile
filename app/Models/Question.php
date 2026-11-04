<?php

namespace App\Models;

use App\Enums\QuestionAnswerStatus;
use App\Enums\QuestionStatus;
use Database\Factories\QuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['topic_id', 'name', 'email', 'phone', 'subject', 'body', 'status', 'ip_address'])]
class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory, Notifiable;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => QuestionStatus::class,
        ];
    }

    /**
     * @return BelongsTo<QuestionTopic, $this>
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(QuestionTopic::class, 'topic_id');
    }

    /**
     * @return HasMany<QuestionAnswer, $this>
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class);
    }

    /**
     * @return HasMany<QuestionAnswer, $this>
     */
    public function publishedAnswers(): HasMany
    {
        return $this->hasMany(QuestionAnswer::class)
            ->where('status', QuestionAnswerStatus::Published)
            ->orderBy('answered_at');
    }

    /**
     * Keep the question status in line with whether it has a published answer.
     */
    public function syncStatusWithAnswers(): void
    {
        $hasPublishedAnswer = $this->publishedAnswers()->exists();

        if ($hasPublishedAnswer && $this->status !== QuestionStatus::Answered) {
            $this->update(['status' => QuestionStatus::Answered]);
        }

        if (! $hasPublishedAnswer && $this->status === QuestionStatus::Answered) {
            $this->update(['status' => QuestionStatus::Pending]);
        }
    }

    /**
     * @param  Builder<Question>  $query
     */
    #[Scope]
    protected function answered(Builder $query): void
    {
        $query->where('status', QuestionStatus::Answered);
    }
}

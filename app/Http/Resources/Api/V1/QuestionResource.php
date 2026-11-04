<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public representation of a question; the asker's contact details and IP address are never exposed.
 *
 * @mixin Question
 */
class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'subject' => $this->subject,
            'body' => $this->body,
            'status' => $this->status->value,
            'created_at' => $this->created_at?->toIso8601String(),
            'topic' => QuestionTopicResource::make($this->whenLoaded('topic')),
            'answers' => QuestionAnswerResource::collection($this->whenLoaded('publishedAnswers')),
        ];
    }
}

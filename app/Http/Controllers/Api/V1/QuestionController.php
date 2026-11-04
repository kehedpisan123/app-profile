<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\QuestionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreQuestionRequest;
use App\Http\Resources\Api\V1\QuestionResource;
use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuestionController extends Controller
{
    /**
     * List answered questions together with their published answers.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $questions = Question::query()
            ->answered()
            ->with(['topic', 'publishedAnswers'])
            ->when($request->filled('topic'), fn (Builder $query) => $query->whereRelation('topic', 'slug', $request->string('topic')->toString()))
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $keyword = '%'.$request->string('search')->trim()->toString().'%';

                $query->where(fn (Builder $query) => $query->where('subject', 'like', $keyword)->orWhere('body', 'like', $keyword));
            })
            ->latest()
            ->paginate(min(max($request->integer('per_page', 10), 1), 50))
            ->withQueryString();

        return QuestionResource::collection($questions);
    }

    public function show(Question $question): QuestionResource
    {
        abort_unless($question->status === QuestionStatus::Answered, 404);

        return QuestionResource::make($question->load(['topic', 'publishedAnswers']));
    }

    public function store(StoreQuestionRequest $request): JsonResponse
    {
        $question = Question::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
        ]);

        return QuestionResource::make($question->load('topic'))
            ->additional(['message' => 'Pertanyaan Anda telah kami terima dan akan segera ditinjau.'])
            ->response()
            ->setStatusCode(201);
    }
}

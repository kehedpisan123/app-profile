<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\QuestionTopicResource;
use App\Models\QuestionTopic;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuestionTopicController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return QuestionTopicResource::collection(
            QuestionTopic::query()->active()->orderBy('sort_order')->get(),
        );
    }
}

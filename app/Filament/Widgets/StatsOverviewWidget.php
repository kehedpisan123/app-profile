<?php

namespace App\Filament\Widgets;

use App\Enums\PostStatus;
use App\Enums\QuestionStatus;
use App\Models\Post;
use App\Models\Question;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $postCounts = Post::query()
            ->selectRaw('status, count(*) as total, sum(view_count) as views')
            ->groupBy('status')
            ->get()
            ->keyBy(fn (Post $post): string => $post->status->value);

        $questionCounts = Question::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $publishedPosts = (int) ($postCounts[PostStatus::Published->value]->total ?? 0);
        $draftPosts = (int) ($postCounts[PostStatus::Draft->value]->total ?? 0);
        $totalViews = (int) $postCounts->sum('views');

        $pendingQuestions = (int) ($questionCounts[QuestionStatus::Pending->value] ?? 0);
        $answeredQuestions = (int) ($questionCounts[QuestionStatus::Answered->value] ?? 0);
        $newQuestionsThisWeek = Question::query()->where('created_at', '>=', now()->subWeek())->count();

        return [
            Stat::make(__('Published Articles'), Number::format($publishedPosts))
                ->description(__(':count drafts', ['count' => Number::format($draftPosts)]))
                ->descriptionIcon(Heroicon::OutlinedPencilSquare)
                ->color('primary'),
            Stat::make(__('Total Views'), Number::format($totalViews))
                ->description(__('Accumulated article readers'))
                ->descriptionIcon(Heroicon::OutlinedEye)
                ->color('gray'),
            Stat::make(__('Pending Questions'), Number::format($pendingQuestions))
                ->description(__(':count received in the last 7 days', ['count' => Number::format($newQuestionsThisWeek)]))
                ->descriptionIcon(Heroicon::OutlinedInboxArrowDown)
                ->color($pendingQuestions > 0 ? 'warning' : 'success'),
            Stat::make(__('Answered Questions'), Number::format($answeredQuestions))
                ->descriptionIcon(Heroicon::OutlinedCheckBadge)
                ->color('success'),
        ];
    }
}

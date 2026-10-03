<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Donation;
use App\Models\MembershipApplication;
use App\Models\NewsletterSubscriber;
use App\Models\Question;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiteOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $monthTotal = Donation::where('status', 'basarili')->where('paid_at', '>=', now()->startOfMonth())->sum('amount');

        return [
            Stat::make('Yanıt bekleyen sorular', Question::where('status', 'yeni')->count())
                ->description('Uzmana sorun formu')->icon('heroicon-o-question-mark-circle')
                ->url(route('filament.admin.resources.questions.index')),
            Stat::make('Yeni mesajlar', ContactMessage::where('status', 'yeni')->count())
                ->description('İletişim formu')->icon('heroicon-o-envelope')
                ->url(route('filament.admin.resources.contact-messages.index')),
            Stat::make('Bekleyen üyelik başvuruları', MembershipApplication::where('status', 'yeni')->count())
                ->icon('heroicon-o-user-plus')->url(route('filament.admin.resources.membership-applications.index')),
            Stat::make('Bu ay online bağış', number_format($monthTotal, 0, ',', '.').' ₺')
                ->description(Donation::where('status', 'basarili')->where('paid_at', '>=', now()->startOfMonth())->count().' başarılı ödeme')
                ->icon('heroicon-o-banknotes')->color('success')->url(route('filament.admin.resources.donations.index')),
            Stat::make('Bülten aboneleri', NewsletterSubscriber::whereNull('unsubscribed_at')->count())->icon('heroicon-o-newspaper'),
        ];
    }
}

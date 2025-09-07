<?php

namespace App\Filament\Pages;

use JibayMcs\FilamentTour\Tour\Step;
use JibayMcs\FilamentTour\Tour\Tour;
use JibayMcs\FilamentTour\Tour\HasTour;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    
    use HasTour;
    protected static ?bool $enable = null;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $activeNavigationIcon = 'heroicon-s-home';

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverview::class,
            // \App\Filament\Widgets\BlogPostsChart::class,
            // \App\Filament\Widgets\BlogPosts2Chart::class,
            \App\Filament\Widgets\ChartsDuo::class,
        ];
    }


public function tours(): array
{
    $enable = config('filament-tour.enabled', true);

    if (! $enable || $enable === false) {
        return [];
    }

    return [
        Tour::make('dashboard-tour')
            ->steps(
                Step::make()
                    ->title("🎉 Welcome to the Dashboard")
                    ->description('
                        <div class="tour-welcome">
                            <p style="
                                color: #374151; 
                                font-size: 1.1rem; 
                                line-height: 1.6;
                                margin-bottom: 12px;
                                font-weight: 500;
                            ">
                                Let\'s take a quick tour to get you started!
                            </p>
                            <div style="
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                padding: 8px 16px;
                                border-radius: 20px;
                                font-size: 0.75rem;
                                display: inline-block;
                                margin-top: 8px;
                                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
                            ">
                                💡 Press any key or click outside to skip
                            </div>
                        </div>
                    '),
                    
                Step::make('.fi-avatar')
                    ->title("👤 Your Profile")
                    ->description('
                        <div class="tour-profile">
                            <p style="
                                color: #374151;
                                font-size: 1rem;
                                line-height: 1.5;
                                margin-bottom: 10px;
                            ">
                                This is your profile section where you can:
                            </p>
                            <ul style="
                                color: #6b7280;
                                font-size: 0.9rem;
                                margin: 0;
                                padding-left: 20px;
                            ">
                                <li style="margin-bottom: 4px;">Update your profile</li>
                                <li style="margin-bottom: 4px;">Change your theme</li>
                                <li>Logout</li>
                            </ul>
                        </div>
                    ')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('success'),
                    
                // Step untuk stats overview widget
                Step::make('.fi-wi-stats-overview')
                    ->title("📊 Inventory Statistics Overview")
                    ->description('
                        <div class="tour-stats">
                            <p style="
                                color: #374151;
                                font-size: 1rem;
                                line-height: 1.5;
                                margin-bottom: 12px;
                                font-weight: 500;
                            ">
                                Monitor your inventory at a glance with these key metrics:
                            </p>

                            <div style="
                                background: #f8fafc;
                                border: 1px solid #e2e8f0;
                                border-radius: 8px;
                                padding: 12px;
                                margin: 12px 0;
                            ">
                                <div style="margin-bottom: 8px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                        <div style="width: 12px; height: 12px; background: #3b82f6; border-radius: 3px;"></div>
                                        <span style="color: #1e40af; font-weight: 600; font-size: 0.9rem;">
                                            Inventories Total Stock
                                        </span>
                                    </div>
                                    <p style="color: #6b7280; font-size: 0.8rem; margin: 0 0 0 20px;">
                                        All available items currently in inventory
                                    </p>
                                </div>

                                <div style="margin-bottom: 8px;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                        <div style="width: 12px; height: 12px; background: #f59e0b; border-radius: 3px;"></div>
                                        <span style="color: #d97706; font-weight: 600; font-size: 0.9rem;">
                                            Borrowed Items
                                        </span>
                                    </div>
                                    <p style="color: #6b7280; font-size: 0.8rem; margin: 0 0 0 20px;">
                                        Items currently being used by students/staff
                                    </p>
                                </div>

                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                        <div style="width: 12px; height: 12px; background: #10b981; border-radius: 3px;"></div>
                                        <span style="color: #059669; font-weight: 600; font-size: 0.9rem;">
                                            Items Under Maintenance
                                        </span>
                                    </div>
                                    <p style="color: #6b7280; font-size: 0.8rem; margin: 0 0 0 20px;">
                                        Equipment being repaired or serviced
                                    </p>
                                </div>
                            </div>

                            <div style="
                                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                                color: white;
                                padding: 10px;
                                border-radius: 6px;
                                margin-top: 12px;
                                text-align: center;
                            ">
                                <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 2px;">
                                    🔍 Quick Insight
                                </div>
                                <div style="font-size: 0.75rem; opacity: 0.9;">
                                    These numbers update in real-time as items are borrowed or returned
                                </div>
                            </div>
                        </div>
                    ')
                    ->icon('heroicon-o-cube')
                    ->iconColor('primary'),
                    
                // Step untuk KEDUA Chart Widget menggunakan class selector
                Step::make('.duo-charts')
                    ->title("📊📈 Lab Management Analytics")
                    ->description('
                        <div class="tour-lab-charts">
                            <p style="
                                color: #374151;
                                font-size: 1rem;
                                line-height: 1.5;
                                margin-bottom: 10px;
                            ">
                                These two comprehensive chart widgets track your inventory management activities and provide insights into usage patterns over the past 12 months.
                            </p>
                            <div style="
                                background: #f8fafc;
                                border: 1px solid #e2e8f0;
                                border-radius: 8px;
                                padding: 12px;
                                margin: 12px 0;
                            ">
                                <div style="
                                    display: flex; 
                                    align-items: center; 
                                    gap: 8px; 
                                    margin-bottom: 8px;
                                ">
                                    <div style="
                                        width: 8px;
                                        height: 8px;
                                        background: #3b82f6;
                                        border-radius: 50%;
                                    "></div>
                                    <span style="color: #1e40af; font-weight: 600; font-size: 0.9rem;">
                                        Lab Usage Statistics - Peminjaman Lab per Bulan
                                    </span>
                                </div>
                                <div style="
                                    display: flex; 
                                    align-items: center; 
                                    gap: 8px;
                                ">
                                    <div style="
                                        width: 8px;
                                        height: 8px;
                                        background: #22c55e;
                                        border-radius: 50%;
                                    "></div>
                                    <span style="color: #15803d; font-weight: 600; font-size: 0.9rem;">
                                        Borrowed Items Statistics - Peminjaman Barang per Bulan
                                    </span>
                                </div>
                            </div>
                            <div style="
                                display: flex;
                                gap: 6px;
                                margin-top: 12px;
                                flex-wrap: wrap;
                            ">
                                <span style="
                                    background: #dbeafe;
                                    color: #1d4ed8;
                                    padding: 4px 8px;
                                    border-radius: 12px;
                                    font-size: 0.7rem;
                                    font-weight: 500;
                                ">Lab Reservation Trends</span>
                                <span style="
                                    background: #dcfce7;
                                    color: #166534;
                                    padding: 4px 8px;
                                    border-radius: 12px;
                                    font-size: 0.7rem;
                                    font-weight: 500;
                                ">Equipment Borrowing</span>
                                <span style="
                                    background: #f3e8ff;
                                    color: #7c3aed;
                                    padding: 4px 8px;
                                    border-radius: 12px;
                                    font-size: 0.7rem;
                                    font-weight: 500;
                                ">Monthly Analytics</span>
                            </div>
                            <div style="
                                background: #fef3c7;
                                border: 1px solid #fbbf24;
                                border-radius: 6px;
                                padding: 8px;
                                margin-top: 12px;
                                font-size: 0.8rem;
                                color: #92400e;
                            ">
                                <div style="font-weight: 600; margin-bottom: 2px;">
                                    📋 Data Insights:
                                </div>
                                <div style="font-size: 0.75rem;">
                                    • Blue chart shows lab room bookings from LabUsage model<br>
                                    • Green chart displays equipment borrowing from Borrow model<br>
                                    • Both track 12-month historical data with smooth line visualization
                                </div>
                            </div>
                            <div style="
                                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                                color: white;
                                padding: 10px;
                                border-radius: 6px;
                                margin-top: 12px;
                                text-align: center;
                            ">
                                <div style="font-weight: 600; font-size: 0.85rem; margin-bottom: 2px;">
                                    💡 Pro Tip
                                </div>
                                <div style="font-size: 0.75rem; opacity: 0.9;">
                                    Compare patterns between lab usage and equipment borrowing to optimize resource allocation
                                </div>
                            </div>
                        </div>
                    ')
                    ->icon('heroicon-o-chart-bar')
                    ->iconColor('info'),
                    
                Step::make()
                    ->title("✨ You're All Set!")
                    ->description('
                        <div class="tour-completion">
                            <p style="
                                color: #374151;
                                font-size: 1.1rem;
                                line-height: 1.6;
                                text-align: center;
                                margin-bottom: 16px;
                            ">
                                Great! You\'ve completed the tour.
                            </p>
                            <div style="
                                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                                color: white;
                                padding: 16px;
                                border-radius: 12px;
                                text-align: center;
                                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
                            ">
                                <div style="font-size: 2rem; margin-bottom: 8px;">🚀</div>
                                <div style="font-weight: 600; margin-bottom: 4px;">Ready to explore!</div>
                            </div>
                        </div>
                    ')
            )
            ->route('/dashboard')
    ];
}
}
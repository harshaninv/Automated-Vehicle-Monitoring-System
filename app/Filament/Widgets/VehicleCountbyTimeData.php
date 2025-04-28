<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleCountbyTimeData extends ChartWidget
{
    protected static ?string $heading = 'Vehicle Count by Time';

    public ?string $filter = 'today'; // default filter

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'this_week' => 'This Week',
            'this_month' => 'This Month',
            'this_year' => 'This Year',
            'last_month' => 'Last Month',
        ];
    }

    protected function getData(): array
    {
        $dataIn = [];
        $dataOut = [];
        $labels = [];
    
        switch ($this->filter) {
            case 'today':
                $hours = range(0, 23);
                foreach ($hours as $hour) {
                    $labels[] = "{$hour}:00";
                    $dataIn[] = TimeLog::whereDate('time_in', now())
                        ->whereRaw('HOUR(time_in) = ?', [$hour])
                        ->count();
                    $dataOut[] = TimeLog::whereDate('time_out', now())
                        ->whereRaw('HOUR(time_out) = ?', [$hour])
                        ->count();
                }
                break;
    
            case 'this_week':
                $startOfWeek = now()->startOfWeek();
                for ($i = 0; $i < 7; $i++) {
                    $date = $startOfWeek->copy()->addDays($i);
                    $labels[] = $date->format('D');
                    $dataIn[] = TimeLog::whereDate('time_in', $date)->count();
                    $dataOut[] = TimeLog::whereDate('time_out', $date)->count();
                }
                break;
    
            case 'this_month':
                $startOfMonth = now()->startOfMonth();
                $endOfMonth = now()->endOfMonth();
    
                $weeks = [];
                $currentWeekStart = $startOfMonth->copy();
                while ($currentWeekStart->lt($endOfMonth)) {
                    $currentWeekEnd = $currentWeekStart->copy()->addDays(6)->min($endOfMonth);
                    $weeks[] = [$currentWeekStart->copy(), $currentWeekEnd->copy()];
                    $currentWeekStart->addDays(7);
                }
    
                foreach ($weeks as $index => [$start, $end]) {
                    $labels[] = "Week " . ($index + 1);
                    $dataIn[] = TimeLog::whereBetween('time_in', [$start, $end])->count();
                    $dataOut[] = TimeLog::whereBetween('time_out', [$start, $end])->count();
                }
                break;
    
            case 'this_year':
                for ($month = 1; $month <= 12; $month++) {
                    $labels[] = Carbon::create()->month($month)->format('M');
                    $dataIn[] = TimeLog::whereYear('time_in', now()->year)
                        ->whereMonth('time_in', $month)
                        ->count();
                    $dataOut[] = TimeLog::whereYear('time_out', now()->year)
                        ->whereMonth('time_out', $month)
                        ->count();
                }
                break;
    
            case 'last_month':
                $startOfLastMonth = now()->subMonth()->startOfMonth();
                $endOfLastMonth = now()->subMonth()->endOfMonth();
    
                $weeks = [];
                $currentWeekStart = $startOfLastMonth->copy();
                while ($currentWeekStart->lt($endOfLastMonth)) {
                    $currentWeekEnd = $currentWeekStart->copy()->addDays(6)->min($endOfLastMonth);
                    $weeks[] = [$currentWeekStart->copy(), $currentWeekEnd->copy()];
                    $currentWeekStart->addDays(7);
                }
    
                foreach ($weeks as $index => [$start, $end]) {
                    $labels[] = "Week " . ($index + 1);
                    $dataIn[] = TimeLog::whereBetween('time_in', [$start, $end])->count();
                    $dataOut[] = TimeLog::whereBetween('time_out', [$start, $end])->count();
                }
                break;
        }
    
        return [
            'datasets' => [
                [
                    'label' => 'Vehicle In',
                    'data' => $dataIn,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#3b82f6',
                    'fill' => false,
                ],
                [
                    'label' => 'Vehicle Out',
                    'data' => $dataOut,
                    'borderColor' => '#f87171',
                    'backgroundColor' => '#f87171',
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }
    

    protected function getType(): string
    {
        return 'line';
    }

    
}

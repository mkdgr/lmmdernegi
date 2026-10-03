<?php

namespace App\Filament\Support;

use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

/** Tablodaki kayıtları Excel'de açılabilen CSV olarak indirir (UTF-8, noktalı virgül). */
class CsvExport
{
    /** @param  array<string, callable|string>  $columns  başlık => alan adı ya da fn($record) */
    public static function make(string $filename, callable $query, array $columns): Action
    {
        return Action::make('csv')
            ->label('CSV indir')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(function () use ($filename, $query, $columns) {
                return response()->streamDownload(function () use ($query, $columns) {
                    $out = fopen('php://output', 'w');
                    fwrite($out, "\xEF\xBB\xBF");
                    fputcsv($out, array_keys($columns), ';');
                    /** @var Builder $q */
                    $q = $query();
                    $q->chunk(500, function ($rows) use ($out, $columns) {
                        foreach ($rows as $row) {
                            fputcsv($out, array_map(fn ($c) => is_callable($c) ? $c($row) : data_get($row, $c), array_values($columns)), ';');
                        }
                    });
                    fclose($out);
                }, $filename.'-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
            });
    }
}

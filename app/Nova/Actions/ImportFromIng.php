<?php

namespace App\Nova\Actions;

use App\Models\Import;
use App\Models\Transaction;
use Brick\Money\Currency;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\File;
use Illuminate\Support\Str;

class ImportFromIng extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $import = Auth::user()->imports()->create([
            'file' => $fields->file->store('imports'),
        ]);

        $text = $fields->file->get();
        $text = mb_convert_encoding($text, "UTF-8", "auto");

        $data = collect(str_getcsv($text, "\n"))
            ->map(fn (string $row) => str_getcsv($row, ';'))
            ->slice(14)
            ->map(fn (array $row) => [
                'booked_at' => Carbon::createFromFormat('d.m.Y', $row[0])->startOfDay()->toDateString(),
                'valued_at' => Carbon::createFromFormat('d.m.Y', $row[1])->startOfDay()->toDateString(),
                'recipient' => $row[2],
                'text' => $row[3],
                'reason' => $row[4],
                'amount' => Money::of(Str::replace(',', '.', Str::replace('.', '', $row[7])), $row[8])->getMinorAmount()->toInt(),
                'currency' => Currency::of($row[8])->getCurrencyCode(),
            ])
            ->filter(function (array $row) {
                return Transaction::query()
                    ->where([
                        'booked_at' => $row['booked_at'],
                        'valued_at' => $row['valued_at'],
                        'recipient' => $row['recipient'],
                        'text' => $row['text'],
                        'reason' => $row['reason'],
                        'amount' => $row['amount'],
                        'currency' => $row['currency'],
                    ])->count() === 0;
            })
            ->values();

        $import->transactions()->createMany($data);

        return Action::message('Import successful');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            File::make('File', 'file'),
        ];
    }
}

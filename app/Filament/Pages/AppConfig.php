<?php

namespace App\Filament\Pages;

use App\Models\Announcement;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\DB;

class AppConfig extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.app-config';
    protected static ?string $navigationGroup = 'Settings';

    public function mount(): void
    {
        $record = Announcement::all()->first();

        $this->form->fill([
            'announcement' => $record->announcement,
            'is_publish' => $record->is_publish,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    TiptapEditor::make('announcement')
                        ->output('html')
                        ->maxContentWidth('5xl')
                        ->columnSpanFull(),
                    Toggle::make('is_publish')->label('Publish')
                ])
        ];
    }

    public function save()
    {
        $data = $this->form->getState();
        $record = Announcement::all();
        if ($record->count() > 0) {
            $data['updated_by'] = auth()->user()->email;
            DB::table('announcement')
                ->update($data);
        } else {
            $data['created_by'] = auth()->user()->email;
            Announcement::create($data);
        }
        Notification::make()
            ->title('Saved')
            ->success()
            ->send();
    }
}

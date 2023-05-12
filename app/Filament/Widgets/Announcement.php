<?php

namespace App\Filament\Widgets;

use App\Models\Announcement as ModelsAnnouncement;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;

class Announcement extends Widget
{
    use HasWidgetShield;
    protected static string $view = 'filament.widgets.announcement-widget';

    protected function getViewData(): array
    {
        return [
            'data' => ModelsAnnouncement::where('is_publish', 1)->first(),
        ];
    }
}

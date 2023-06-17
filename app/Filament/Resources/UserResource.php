<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Filament\Resources\UserResource\Widgets\UserOverview;
use App\Models\User;
use BezhanSalleh\FilamentAddons\Tables\Columns\ChipColumn;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Spatie\Permission\Traits\HasRoles;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;
    protected static ?string $slug = 'settings/users';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 1;


    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    TextInput::make('password')
                        ->password()
                        ->required()
                        ->dehydrated(fn ($state) => filled($state))
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->visibleOn('create'),
                    TextInput::make('passwordConfirmation')
                        ->password()
                        ->visibleOn('create')
                        ->required(fn (Page $livewire) => $livewire instanceof CreateRecord)
                        ->same('password')
                        ->dehydrated(false),
                    Select::make('roles')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->searchable()
                        ->preload(),
                    Toggle::make('verified')
                        ->onIcon('heroicon-s-lightning-bolt')
                        ->offIcon('heroicon-s-user')
                        ->onColor('success')
                        ->offColor('danger')
                        ->visibleOn('edit')
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('name')
                            ->searchable()
                            ->sortable(),
                        TagsColumn::make('roles.name')
                            ->searchable()
                    ]),
                    Stack::make([
                        TextColumn::make('email')
                            ->searchable()
                            ->sortable()
                            ->alignLeft()
                            ->icon(fn ($record) => $record->email_verified_at !== null ? '' : 'heroicon-o-shield-exclamation')
                            ->iconPosition('before')
                            ->tooltip(fn ($record) => $record->email_verified_at !== null ? 'verified' : 'not verified'),
                        TextColumn::make('email_verified_at')
                            ->sortable(),
                    ])
                ]),

            ])
            ->filters([
                Filter::make('name'),
                Filter::make('email'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            UserOverview::class,
        ];
    }
}

<?php

namespace App\Filament\Resources\Votes;

use App\Filament\Resources\Votes\Pages\ManageVotes;
use App\Models\Vote;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VoteResource extends Resource
{
    protected static ?string $model = Vote::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            ToggleButtons::make('verdict')->options(Vote::VERDICTS)->inline()->live()->required(),
            Textarea::make('reason')->visible(fn (Get $get): bool => $get('verdict') === 'no')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                SelectColumn::make('verdict')->options(Vote::VERDICTS)->selectablePlaceholder(false),
                TextColumn::make('reason')->wrap(),
                TextColumn::make('created_at')->since(),
            ])
            ->headerActions([CreateAction::make()])
            ->defaultSort('created_at', 'desc')
            ->poll('2s');
    }

    public static function getPages(): array
    {
        return ['index' => ManageVotes::route('/')];
    }
}

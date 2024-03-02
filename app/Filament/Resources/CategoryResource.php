<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Filament\Resources\CategoryResource\RelationManagers\ProductsRelationManager;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?int $navigationSort = 4;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([

                                TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->unique(ignoreRecord: true)
                                    ->afterStateUpdated(function (string $operation, $state, Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }
                                        $set('slug', Str::slug($state));
                                    }),

                                TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique(Category::class, 'slug', ignoreRecord: true)
                                    ->required(),

                                MarkdownEditor::make('description')
                                    ->columnSpanFull()
                            ])->columns(2)
                    ]),
                Group::make()
                    ->schema([
                        Section::make('Status')
                        ->schema([
                            Toggle::make('is_visible')
                                ->label('Visibility')
                                ->helperText('Enable or disable category visibility')
                                ->default(true),
                            Select::make('parent_id')
                                ->relationship('parent','name')
                        ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('parent.name')
                    ->label('Parent')
                    ->sortable()
                    ->searchable(),

                IconColumn::make('is_visible')
                    ->boolean()
                    ->sortable()
                    ->label('Visibility'),

                TextColumn::make('updated_at')
                    ->label('Update Date')
                    ->sortable()
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}

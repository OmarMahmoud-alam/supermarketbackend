<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Custom;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\CustomResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomResource\RelationManagers;

class CustomResource extends Resource
{
    protected static ?string $model = Custom::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup=  'decoration'; 
    protected static ?string $recordTitleAttribute=  'email'; 

    public static function form(Form $form): Form
    {
        return $form
        ->schema([

                    
      
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                 Tables\Columns\TextColumn::make('email')->sortable()->toggleable(),

                 IconColumn::make('email_verified_at')
                     ->icon(fn (string $state): string => match ($state) {
                        null => 'heroicon-s-x',
                         default  => 'heroicon-o-shield-check', })
                         ->color(fn (string $state): string => match ($state) {
                            null => 'danger',
    
                            default => 'success',
                        })->toggleable(),
                        
                TextColumn::make('phone')->searchable(),
                 Tables\Columns\TextColumn::make('date_of_birth')->sortable()->date()->toggleable(),
                 Tables\Columns\TextColumn::make('orders_count')->counts('orders') ->sortable()->toggleable(),
                 Tables\Columns\TextColumn::make('created_at')->sortable()->toggleable(isToggledHiddenByDefault :true)->date(),
                              ])
            ->filters([
                TernaryFilter::make('email_verified_at')   
                 ->label('Email verification')
                 ->placeholder('All users')
                 ->trueLabel('Verified users')
                 ->falseLabel('Not verified users')
                ->nullable(),
                TernaryFilter::make('phone')   
                 ->label('phone number')
                 ->placeholder('All users')
                 ->trueLabel('With phone number')
                 ->falseLabel('Without phone number')
                ->nullable()
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                //Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustoms::route('/'),
            'create' => Pages\CreateCustom::route('/create'),
          //  'edit' => Pages\EditCustom::route('/{record}/edit'),
        ];
    }    
}

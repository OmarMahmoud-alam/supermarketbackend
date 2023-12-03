<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\RelationManagers\ProductsRelationManager;
use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationGroup=  'Shop'; 

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $recordTitleAttribute=  'name'; 
   
   public static function getGlobalSearchResultDetails(Model $record):array{
    return[
        'Category'=>$record->id,
    ];
   } 
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Forms\Components\Section::make('basic data')->schema([
                         TextInput::make('name')
                         ->required()
                         ->unique(),
                         FileUpload::make('image')->required()->image()->imageEditor(),
                
                    ])->columns(2)->collapsible(),
                    

                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable()->sortable()->toggleable(),
                Tables\Columns\ImageColumn::make('image')->toggleable(),
                TextColumn::make('name')->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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

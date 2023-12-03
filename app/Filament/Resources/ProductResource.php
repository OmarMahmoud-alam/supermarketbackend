<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ProducttypeEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Forms\Components\MarkdownEditor;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup=  'Shop'; 
    protected static ?string $navigationLabel=  'Products'; 
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Forms\Components\Section::make('basic data')->schema([
                         TextInput::make('name')
                         ->required()
                         ->unique(),
                    TextInput::make('brand')->required(),
                    MarkdownEditor::make('description')->columnspan('full')
                    ])->columns(2)->collapsible(),
                    Forms\Components\Section::make('Status')->schema([
                        Toggle::make('isvisible')->default(true),
                        Toggle::make('availability')->default(true),

                    ])->columns(2)->collapsible(),

                ]),
                Group::make()->schema([
                    Forms\Components\Section::make('pricising & data')->schema([
                        TextInput::make('price')->required()->numeric(),
                        TextInput::make('quantity')->required()->numeric(),
                        Forms\Components\Select::make('type')->options(
                            [
                                'deliverable'=>ProducttypeEnum::DELIVERABLE->value,
                                'inlocation'=>ProducttypeEnum::iNLOCATION->value

                            ])->required()->default('deliverable'),
                            Forms\Components\Select::make('category_id'
                            )->relationship('category','name')->required()
                        

                            ])->columns(2)->collapsible(),
                            Forms\Components\Section::make('image')->schema([
                                FileUpload::make('image')->required()->image()->imageEditor(),
                                
         
                             ])->collapsible()
                            
                        ]),
                 
              
                            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->toggleable(),
               TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('brand')->searchable()->sortable()->toggleable(isToggledHiddenByDefault :true),
                Tables\Columns\TextColumn::make('price')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('quantity')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->sortable()->toggleable(isToggledHiddenByDefault :true)->date(),
                Tables\Columns\TextColumn::make('type')->sortable()->toggleable(isToggledHiddenByDefault :true),
                ToggleColumn::make('isvisible')->toggleable()->sortable(),//->boolean(),
                
                Tables\Columns\TextColumn::make('category.name')->sortable()->toggleable(isToggledHiddenByDefault :true),
           
                ])
            ->filters([

                TernaryFilter::make('isvisible')
                ->label('visiblity')
                ->boolean()
                ->truelabel('only visible')
                ->falseLabel('only hidden product'),
            //  SelectFilter::make('brand')->multiple()->options('brand'),
              SelectFilter::make('brand')->multiple()->options(Product::all()->pluck('brand', 'brand')),
              Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                   /* ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Order from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
 
                        return $indicators;
                    }),*/
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                ]),

                

              
            ])
            ->bulkActions([
                ExportBulkAction::make(),

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
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    } 
       
}

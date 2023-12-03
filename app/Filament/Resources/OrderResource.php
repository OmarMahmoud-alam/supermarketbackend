<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\OrderStatusEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OrderResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\OrderResource\RelationManagers;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup=  'Shop'; 
    protected static ?string $recordTitleAttribute=  'number'; 

    public static function form(Form $form): Form
    {

            return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Order Details')
                        ->schema([
                            Forms\Components\TextInput::make('number')
                                ->default('OR-' . random_int(100000, 9999999))
                                ->disabled()
                                ->dehydrated()
                                ->required(),

                            Forms\Components\Select::make('custom_id')
                                ->relationship('custom', 'name')
                                ->searchable()
                                ->required(),

                            Forms\Components\TextInput::make('shipping_price')
                                ->label('Shipping Costs')
                                ->dehydrated()
                                ->numeric()
                                ->required(),

                            Forms\Components\Select::make('type')
                            ->options([
                                'pending' => OrderStatusEnum::PENDING->value,
                                'processing' => OrderStatusEnum::PROCESSING->value,
                                'completed' => OrderStatusEnum::COMPLETED->value,
                                'declined' => OrderStatusEnum::DECLINED->value,
                            ])->required(),

                            Forms\Components\MarkdownEditor::make('notes')
                                ->columnSpanFull()
                        ])->columns(2),
                    Forms\Components\Wizard\Step::make('Order_product')
                        ->schema([
                            Forms\Components\Repeater::make('product')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->label('Product')
                                        ->options(Product::query()->pluck('name', 'id'))
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                                        $set('unit_price', Product::find($state)?->price ?? 0)),

                                    Forms\Components\TextInput::make('quantity')
                                        ->numeric()
                                        ->live()
                                        ->dehydrated()
                                        ->default(1)
                                        ->required(),

                                    Forms\Components\TextInput::make('unit_price')
                                        ->label('Unit Price')
                                        ->disabled()
                                        ->dehydrated()
                                        ->numeric()
                                        ->required(),

                                    Forms\Components\Placeholder::make('subtotal')
                                        ->label('Total Price')
                                        ->content(function ($get) {
                                            return $get('quantity') * $get('unit_price');
                                        })

                                ])->columns(4)
                        ])
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable(),

               TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product_sum_subtotal')
                    ->sum('product', 'subtotal')
                    ->label('Total product')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Order Date')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }    
}

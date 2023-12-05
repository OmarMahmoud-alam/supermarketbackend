// OrderProductsColumn.php

namespace App\Http\Livewire;

use Filament\Tables\Columns\TextColumn;
use Livewire\Component;

class OrderProductsColumn extends Component
{
    public $order;

    public function mount($order)
    {
        $this->order = $order;
    }

    public function render()
    {
        $products = $this->order->products;

        return view('livewire.order-products-column', compact('products'));
    }
}
<?php

namespace App\Livewire\Products;

use App\Models\Commercial;
use App\Models\Product;
use Livewire\Component;

class ProductList extends Component
{
    public string $search = '';

    public string $statusFilter = '';

    public string $commercialFilter = '';

    public string $sort = 'desc';

    public function mount(): void
    {
        $status = request()->query('status');

        if (is_string($status) && in_array($status, ['activo', 'inactivo'], true)) {
            $this->statusFilter = $status;
        }

        $commercial = request()->query('commercial');

        if (is_string($commercial) && $commercial !== '') {
            $this->commercialFilter = $commercial;
        }
    }

    public function filterStatus(string $status): void
    {
        $this->statusFilter = $this->statusFilter === $status ? '' : $status;
    }

    public function filterCommercial(string $commercialId): void
    {
        $this->commercialFilter = $this->commercialFilter === $commercialId ? '' : $commercialId;
    }

    public function clearFilters(): void
    {
        $this->statusFilter = '';
        $this->commercialFilter = '';
        $this->search = '';
    }

    public function toggleStatus(int $productId): void
    {
        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        $product->activo = ! $product->activo;
        $product->save();
    }

    public function remove(int $productId): void
    {
        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        $product->delete();

        $this->dispatch('product-removed');
    }

    public function render()
    {
        $products = Product::with('commercial')
            ->when($this->search !== '', function ($query): void {
                $like = '%'.$this->search.'%';
                $query->where(fn ($q) => $q
                    ->where('nome', 'like', $like)
                    ->orWhere('codigo', 'like', $like)
                    ->orWhere('numero_serie', 'like', $like));
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('activo', $this->statusFilter === 'activo'))
            ->when($this->commercialFilter !== '', fn ($query) => $query->where('commercial_id', $this->commercialFilter))
            ->orderBy('nome', $this->sort === 'asc' ? 'asc' : 'desc')
            ->get();

        return view('livewire.products.product-list', [
            'products' => $products,
            'comerciais' => Commercial::where('activo', true)->orderBy('nome')->get(),
        ]);
    }
}

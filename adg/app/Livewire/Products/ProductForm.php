<?php

namespace App\Livewire\Products;

use App\Models\Commercial;
use App\Models\Product;
use Livewire\Component;

class ProductForm extends Component
{
    public ?int $productId = null;

    public string $nome = '';

    public string $numero_serie = '';

    public string $codigo = '';

    public string $valor = '';

    public ?int $commercial_id = null;

    public bool $activo = true;

    public function mount(): void
    {
        $uuid = request()->query('produto');

        if (! is_string($uuid) || $uuid === '') {
            return;
        }

        $product = Product::query()->where('uuid', $uuid)->first();

        if (! $product) {
            return;
        }

        $this->productId = $product->id;
        $this->nome = $product->nome;
        $this->numero_serie = $product->numero_serie ?? '';
        $this->codigo = $product->codigo;
        $this->valor = (string) $product->valor;
        $this->commercial_id = $product->commercial_id;
        $this->activo = $product->activo;
    }

    protected function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'numero_serie' => ['nullable', 'string', 'max:100'],
            'codigo' => ['required', 'string', 'max:50', 'unique:products,codigo,'.$this->productId],
            'valor' => ['required', 'numeric', 'min:0'],
            'commercial_id' => ['nullable', 'integer', 'exists:commercials,id'],
            'activo' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        $data['valor'] = (float) $data['valor'];

        if ($this->productId !== null) {
            $product = Product::findOrFail($this->productId);
            $product->fill($data);
            $product->save();
        } else {
            Product::create($data);
        }

        $this->redirect(route('products'), navigate: true);
    }

    public function render()
    {
        return view('livewire.products.product-form', [
            'comerciais' => Commercial::where('activo', true)->orderBy('nome')->get(),
            'editing' => $this->productId !== null,
        ]);
    }
}

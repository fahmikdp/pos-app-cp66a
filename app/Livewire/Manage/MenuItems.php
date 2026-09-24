<?php

namespace App\Livewire\Manage;

use App\Models\Category;
use App\Models\MenuItem;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class MenuItems extends Component
{
    use WireUiActions, WithPagination;

    public string $search = '';

    public ?int $filterCategoryId = null;

    public ?string $filterAvailability = null;

    public bool $showModal = false;

    public ?int $menuItemId = null;

    public ?int $categoryId = null;

    public string $name = '';

    public string $description = '';

    public string $price = '';

    public int $stockQty = 0;

    public bool $isAvailable = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatingFilterAvailability(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        $this->menuItemId = $item->id;
        $this->categoryId = $item->category_id;
        $this->name = $item->name;
        $this->description = $item->description ?? '';
        $this->price = (string) $item->price;
        $this->stockQty = $item->stock_qty;
        $this->isAvailable = $item->is_available;
        $this->showModal = true;
    }

    protected function rules(): array
    {
        return [
            'categoryId' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'stockQty' => 'required|integer|min:0',
            'isAvailable' => 'boolean',
        ];
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'category_id' => $this->categoryId,
                'name' => $this->name,
                'description' => $this->description ?: null,
                'price' => $this->price,
                'stock_qty' => $this->stockQty,
                'is_available' => $this->isAvailable,
            ];

            if ($this->menuItemId) {
                MenuItem::findOrFail($this->menuItemId)->update($data);
                $this->notification()->success('Berhasil', 'Item menu berhasil diperbarui.');
            } else {
                MenuItem::create($data);
                $this->notification()->success('Berhasil', 'Item menu berhasil ditambahkan.');
            }

            $this->showModal = false;
            $this->resetForm();
        } catch (\Throwable $e) {
            $this->notification()->error('Gagal', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    public function delete(int $id): void
    {
        try {
            MenuItem::findOrFail($id)->delete();
            $this->notification()->success('Berhasil', 'Item menu berhasil dihapus.');
        } catch (\Throwable $e) {
            $this->notification()->error('Gagal', 'Item tidak dapat dihapus karena terdapat data transaksi terkait.');
        }
    }

    private function resetForm(): void
    {
        $this->menuItemId = null;
        $this->categoryId = null;
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->stockQty = 0;
        $this->isAvailable = true;
        $this->resetValidation();
    }

    public function render()
    {
        $items = MenuItem::with('category')
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->when($this->filterCategoryId, fn ($q) => $q->where('category_id', $this->filterCategoryId))
            ->when($this->filterAvailability !== null && $this->filterAvailability !== '', fn ($q) => $q->where('is_available', (bool) $this->filterAvailability))
            ->orderBy('name')
            ->paginate(15);

        $categoryOptions = Category::where('is_active', true)->orderBy('name')
            ->get()->map(fn ($c) => ['label' => $c->name, 'value' => $c->id]);

        $availabilityOptions = [
            ['label' => 'Tersedia', 'value' => '1'],
            ['label' => 'Tidak Tersedia', 'value' => '0'],
        ];

        return view('livewire.manage.menu-items', compact('items', 'categoryOptions', 'availabilityOptions'))
            ->layout('layouts.app', ['title' => 'Kelola Item Menu']);
    }
}

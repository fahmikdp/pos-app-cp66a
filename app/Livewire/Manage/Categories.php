<?php

namespace App\Livewire\Manage;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Categories extends Component
{
    use WireUiActions, WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $categoryId = null;

    public string $name = '';

    public string $description = '';

    public bool $isActive = true;

    public function updatingSearch(): void
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
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->isActive = $category->is_active;
        $this->showModal = true;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:categories,name,'.($this->categoryId ?? 'NULL'),
            'description' => 'nullable|string|max:500',
            'isActive' => 'boolean',
        ];
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'name' => $this->name,
                'description' => $this->description ?: null,
                'is_active' => $this->isActive,
            ];

            if ($this->categoryId) {
                Category::findOrFail($this->categoryId)->update($data);
                $this->notification()->success('Berhasil', 'Kategori berhasil diperbarui.');
            } else {
                Category::create($data);
                $this->notification()->success('Berhasil', 'Kategori berhasil ditambahkan.');
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
            $category = Category::findOrFail($id);
            if ($category->menuItems()->exists()) {
                $this->notification()->error('Tidak Bisa Dihapus', 'Kategori masih memiliki item menu.');

                return;
            }
            $category->delete();
            $this->notification()->success('Berhasil', 'Kategori berhasil dihapus.');
        } catch (\Throwable $e) {
            $this->notification()->error('Gagal', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    private function resetForm(): void
    {
        $this->categoryId = null;
        $this->name = '';
        $this->description = '';
        $this->isActive = true;
        $this->resetValidation();
    }

    public function render()
    {
        $categories = Category::where('name', 'like', '%'.$this->search.'%')
            ->withCount('menuItems')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.manage.categories', compact('categories'))
            ->layout('layouts.app', ['title' => 'Kelola Kategori']);
    }
}

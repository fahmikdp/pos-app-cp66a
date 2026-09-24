<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Users extends Component
{
    use WireUiActions, WithPagination;

    public string $search = '';

    public ?string $filterRole = null;

    public bool $showModal = false;

    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'kasir';

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
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->isActive = $user->is_active;
        $this->showModal = true;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.($this->userId ?? 'NULL'),
            'password' => $this->userId ? 'nullable|min:6' : 'required|min:6',
            'role' => 'required|in:superadmin,admin,kasir',
            'isActive' => 'boolean',
        ];
    }

    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'is_active' => $this->isActive,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            if ($this->userId) {
                User::findOrFail($this->userId)->update($data);
                $this->notification()->success('Berhasil', 'User berhasil diperbarui.');
            } else {
                User::create($data);
                $this->notification()->success('Berhasil', 'User berhasil ditambahkan.');
            }

            $this->showModal = false;
            $this->resetForm();
        } catch (\Throwable $e) {
            $this->notification()->error('Gagal', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    public function toggleActive(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            $this->notification()->error('Tidak Bisa', 'Anda tidak bisa menonaktifkan akun sendiri.');

            return;
        }
        $user->update(['is_active' => ! $user->is_active]);
        $this->notification()->success('Berhasil', 'Status user berhasil diubah.');
    }

    private function resetForm(): void
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'kasir';
        $this->isActive = true;
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%'))
            ->when($this->filterRole, fn ($q) => $q->where('role', $this->filterRole))
            ->orderBy('name')
            ->paginate(15);

        $roleOptions = [
            ['label' => 'Superadmin', 'value' => 'superadmin'],
            ['label' => 'Admin', 'value' => 'admin'],
            ['label' => 'Kasir', 'value' => 'kasir'],
        ];

        return view('livewire.settings.users', compact('users', 'roleOptions'))
            ->layout('layouts.app', ['title' => 'Manajemen User']);
    }
}

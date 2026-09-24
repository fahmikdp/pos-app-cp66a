<div class="bg-white rounded-2xl shadow-lg p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Masuk ke Akun</h2>

    <form wire:submit="login" class="space-y-5">
        <x-wireui-input
            wire:model="email"
            label="Email"
            type="email"
            placeholder="admin@pos.test"
            icon="envelope"
        />

        <x-wireui-input
            wire:model="password"
            label="Password"
            type="password"
            placeholder="••••••••"
            icon="lock-closed"
        />

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input wire:model="remember" type="checkbox" class="rounded border-gray-300 text-primary-600">
                Ingat saya
            </label>
        </div>

        <x-wireui-button
            type="submit"
            class="w-full"
            primary
            label="Masuk"
            wire:loading.attr="disabled"
        />
    </form>
</div>

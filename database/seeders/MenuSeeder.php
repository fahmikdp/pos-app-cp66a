<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::truncate();

        $menus = [
            // Shared (semua role)
            [
                'name' => 'dashboard',
                'label' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'home',
                'order' => 1,
                'permission' => null,
                'children' => [],
            ],

            // Kasir group
            [
                'name' => 'pos',
                'label' => 'Transaksi',
                'route' => null,
                'icon' => 'shopping-cart',
                'order' => 2,
                'permission' => 'kasir',
                'children' => [
                    [
                        'name' => 'pos.cashier',
                        'label' => 'Kasir',
                        'route' => 'pos.cashier',
                        'icon' => 'calculator',
                        'order' => 1,
                        'permission' => 'kasir',
                    ],
                    [
                        'name' => 'pos.history',
                        'label' => 'Riwayat Saya',
                        'route' => 'pos.history',
                        'icon' => 'clock',
                        'order' => 2,
                        'permission' => 'kasir',
                    ],
                ],
            ],

            // Admin group
            [
                'name' => 'manage',
                'label' => 'Kelola Menu',
                'route' => null,
                'icon' => 'rectangle-stack',
                'order' => 3,
                'permission' => 'admin',
                'children' => [
                    [
                        'name' => 'manage.categories',
                        'label' => 'Kategori',
                        'route' => 'manage.categories',
                        'icon' => 'tag',
                        'order' => 1,
                        'permission' => 'admin',
                    ],
                    [
                        'name' => 'manage.menu-items',
                        'label' => 'Item Menu',
                        'route' => 'manage.menu-items',
                        'icon' => 'clipboard-document-list',
                        'order' => 2,
                        'permission' => 'admin',
                    ],
                ],
            ],
            [
                'name' => 'reports',
                'label' => 'Laporan',
                'route' => null,
                'icon' => 'document-chart-bar',
                'order' => 4,
                'permission' => 'admin',
                'children' => [
                    [
                        'name' => 'reports.transactions',
                        'label' => 'Riwayat Transaksi',
                        'route' => 'reports.transactions',
                        'icon' => 'list-bullet',
                        'order' => 1,
                        'permission' => 'admin',
                    ],
                    [
                        'name' => 'reports.sales',
                        'label' => 'Laporan Penjualan',
                        'route' => 'reports.sales',
                        'icon' => 'arrow-trending-up',
                        'order' => 2,
                        'permission' => 'admin',
                    ],
                ],
            ],

            // Superadmin only
            [
                'name' => 'settings',
                'label' => 'Pengaturan',
                'route' => null,
                'icon' => 'cog-6-tooth',
                'order' => 5,
                'permission' => 'superadmin',
                'children' => [
                    [
                        'name' => 'settings.users',
                        'label' => 'Manajemen User',
                        'route' => 'settings.users',
                        'icon' => 'users',
                        'order' => 1,
                        'permission' => 'superadmin',
                    ],
                    [
                        'name' => 'settings.logs',
                        'label' => 'Log Error',
                        'route' => 'settings.logs',
                        'icon' => 'bug-ant',
                        'order' => 2,
                        'permission' => 'superadmin',
                    ],
                ],
            ],
        ];

        foreach ($menus as $menuData) {
            $children = $menuData['children'] ?? [];
            unset($menuData['children']);

            $parent = Menu::create($menuData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                Menu::create($childData);
            }
        }
    }
}

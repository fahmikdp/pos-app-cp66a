<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'label',
        'route',
        'icon',
        'parent_id',
        'order',
        'is_active',
        'permission',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->where('is_active', true)->orderBy('order');
    }

    public static function getMenuTree(): Collection
    {
        $user = auth()->user();

        return static::whereNull('parent_id')
            ->where('is_active', true)
            ->when($user, function ($query) use ($user) {
                if ($user->isSuperadmin()) {
                    return $query;
                }
                if ($user->isAdmin()) {
                    return $query->where(function ($q) {
                        $q->whereNull('permission')
                            ->orWhere('permission', '!=', 'superadmin');
                    });
                }

                return $query->where(function ($q) {
                    $q->whereNull('permission')
                        ->orWhereNotIn('permission', ['superadmin', 'admin']);
                });
            })
            ->orderBy('order')
            ->with(['children' => function ($query) use ($user) {
                if (! $user) {
                    return;
                }
                if ($user->isSuperadmin()) {
                    return;
                }
                if ($user->isAdmin()) {
                    $query->where(function ($sub) {
                        $sub->whereNull('permission')->orWhere('permission', '!=', 'superadmin');
                    });
                } else {
                    $query->where(function ($sub) {
                        $sub->whereNull('permission')->orWhereNotIn('permission', ['superadmin', 'admin']);
                    });
                }
            }])
            ->get();
    }

    public static function getBreadcrumbs(): array
    {
        $currentRoute = request()->route()?->getName();
        if (! $currentRoute) {
            return [];
        }

        $menu = static::where('route', $currentRoute)->first();
        if (! $menu) {
            return [];
        }

        $crumbs = [];
        $current = $menu;
        while ($current) {
            array_unshift($crumbs, [
                'label' => $current->label,
                'route' => $current->route ? route($current->route) : null,
            ]);
            $current = $current->parent;
        }

        return $crumbs;
    }
}

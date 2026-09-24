<?php

namespace App\Livewire\Layout;

use App\Models\Menu;
use Livewire\Component;

class Sidebar extends Component
{
    public $menus = [];

    public function mount(): void
    {
        $this->menus = Menu::getMenuTree();
    }

    public function render()
    {
        return view('livewire.layout.sidebar');
    }
}

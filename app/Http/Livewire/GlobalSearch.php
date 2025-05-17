<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Str;

class GlobalSearch extends Component
{
    public $query = '';
    public $results = [];
    public $selectedIndex = 0;

    protected $listeners = ['search'];

    public function search($query)
    {
        $this->query = $query;

        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        // Get current business type
        $business = auth()->user()->current_business;

        // Search based on current business
        $this->results = match ($business) {
            'bakery' => $this->searchBakery(),
            'tools' => $this->searchTools(),
            'academy' => $this->searchAcademy(),
            default => [],
        };
    }

    protected function searchBakery()
    {
        return [
            // Example structure, replace with actual models
            ...$this->searchItems(),
            ...$this->searchOrders(),
            ...$this->searchCustomers(),
        ];
    }

    protected function searchTools()
    {
        return [
            ...$this->searchProducts(),
            ...$this->searchOrders(),
            ...$this->searchCustomers(),
        ];
    }

    protected function searchAcademy()
    {
        return [
            ...$this->searchCourses(),
            ...$this->searchStudents(),
            ...$this->searchEnrollments(),
        ];
    }

    protected function formatResult($item, $type, $icon)
    {
        return [
            'id' => $item->id,
            'title' => $item->name ?? $item->title ?? '',
            'description' => Str::limit($item->description ?? '', 100),
            'url' => route("$type.show", $item),
            'type' => $type,
            'icon' => $icon,
        ];
    }

    public function render()
    {
        return view('livewire.global-search');
    }
} 
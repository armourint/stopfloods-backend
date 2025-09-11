<?php

namespace App\Livewire\Dashboard;

use App\Models\City;
use App\Models\Location;
use App\Models\Prediction;
use Livewire\Attributes\On;
use Livewire\Component;

class Selector extends Component
{
    public int $cityId = 0;
    public int $year = 0;
    public ?int $locationId = null;
    public string $search = '';

    /** @var array<int> */
    public array $yearsOptions = [];

    public function mount(): void
    {
        // Default to first city
        $this->cityId = City::query()->value('id') ?? 0;

        // Build years list
        $this->refreshYears();

        // Default to most recent year
        $this->year = $this->yearsOptions[0] ?? now()->year;

        $this->emitFilters();
    }

    #[On('map.select-location')]
    public function setLocation(int $locationId): void
    {
        $this->locationId = $locationId;
        $this->emitFilters();
    }

    public function updatedCityId(): void
    {
        $this->refreshYears();

        if (!in_array($this->year, $this->yearsOptions, true)) {
            $this->year = $this->yearsOptions[0] ?? 0;
        }

        $this->emitFilters();
    }

    public function updatedYear(): void
    {
        $this->emitFilters();
    }

    public function updatedLocationId(): void
    {
        $this->emitFilters();
    }

    private function emitFilters(): void
    {
        $this->dispatch('filters.changed', cityId: $this->cityId, year: $this->year, locationId: $this->locationId);
    }

    private function refreshYears(): void
    {
        $this->yearsOptions = Prediction::query()
            ->whereHas('location', fn ($q) => $q->where('city_id', $this->cityId))
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($y) => (int) $y)
            ->all();
    }

    public function render()
    {
        return view('livewire.dashboard.selector', [
            'cities'    => City::orderBy('name')->get(),
            'locations' => Location::query()
                ->where('city_id', $this->cityId)
                ->when($this->search !== '', fn ($q) => $q->where('code', 'like', '%'.$this->search.'%'))
                ->orderBy('code')
                ->limit(50)
                ->get(),
            'yearsOptions' => $this->yearsOptions,
        ]);
    }
}

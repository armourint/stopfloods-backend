<x-card>
  <x-slot:header>Selector</x-slot:header>
  <div class="grid gap-4">
    <div class="field">
      <label class="label">Select City</label>
      <select wire:model.live="cityId" class="select">
        @foreach($cities as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
      </select>
    </div>

    <div class="field">
      <label class="label">Select Year</label>
      <select wire:model.live="year" class="select">
        @forelse($yearsOptions as $y)
          <option value="{{ $y }}">{{ $y }}</option>
        @empty
          <option disabled>No data</option>
        @endforelse
      </select>

    </div>

    <div class="field">
      <label class="label">Choose a location</label>
      <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search code (e.g., R)" class="input" />
      <select wire:model.live="locationId" class="select mt-2">
        <option value="">-- Any --</option>
        @foreach($locations as $loc)
          <option value="{{ $loc->id }}">{{ $loc->code }} ({{ $loc->lat }}, {{ $loc->lng }})</option>
        @endforeach
      </select>
    </div>
  </div>
</x-card>

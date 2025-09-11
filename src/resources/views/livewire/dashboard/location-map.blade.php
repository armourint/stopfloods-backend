<x-card>
  <x-slot:header>Location Map</x-slot:header>

  {{-- Livewire-updated JSON payload --}}
  <div id="map-locs" class="hidden">@json($locations)</div>

  {{-- Keep DOM stable for Leaflet --}}
  <div id="map" class="h-[460px] rounded-xl" wire:ignore></div>
</x-card>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('livewire:init', () => {
  let map, markers = [];

  const ensureMap = () => {
    const node = document.getElementById('map');
    if (!node) return;
    if (!map) {
      map = L.map(node, { zoomControl: false }).setView([51.897,-8.47], 12);
      L.control.zoom({ position: 'bottomright' }).addTo(map);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution:'&copy; OSM'}).addTo(map);
    }
  };

  const readLocs = () => {
    try { return JSON.parse(document.getElementById('map-locs')?.textContent || '[]'); }
    catch { return []; }
  };

  const render = () => {
    ensureMap(); if (!map) return;
    markers.forEach(m => map.removeLayer(m)); markers = [];

    readLocs().forEach(l => {
      const m = L.circleMarker([l.lat, l.lng], {
        radius: 6,
        color: l.color,           // stroke = risk color
        weight: 2,
        fillColor: l.color,       // fill = risk color
        fillOpacity: 0.9
      });

      const html = `
        <div style="font-size:12px; line-height:1.2">
          <div><strong>Location:</strong> ${l.code}</div>
          <div><strong>Coordinates:</strong> (${(+l.lat).toFixed(6)}, ${(+l.lng).toFixed(6)})</div>
          <div><strong>(I, J):</strong> (${l.i ?? '—'}, ${l.j ?? '—'})</div>
          <div><strong>Max Depth:</strong> ${l.max_depth?.toFixed(3) ?? '—'} m</div>
        </div>`;
      m.bindTooltip(html, { direction: 'top', offset:[0,-4], sticky: true, opacity: 0.95 });

      m.on('click', () => Livewire.dispatch('map.select-location', { locationId: l.id }));
      m.addTo(map); markers.push(m);
    });

    if (markers.length) {
      const g = L.featureGroup(markers);
      map.fitBounds(g.getBounds().pad(0.2));
    }
  };

  render();
  Livewire.hook('morph.updated', render);
});
</script>
@endpush

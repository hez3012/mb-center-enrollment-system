@php
    use App\Helpers\PhilippinesGeo;

    $fieldPrefix = $fieldPrefix ?? '';
    $data        = $data        ?? [];

    $selRegion    = $data['region']        ?? '';
    $selProvince  = $data['province']      ?? '';
    $selCity      = $data['city']          ?? '';
    $selBarangay  = $data['barangay']      ?? '';
    $selHouseUnit = $data['house_unit_no'] ?? '';
    $selStreet    = $data['street']        ?? '';
    $selZip       = $data['zip_code']      ?? '';

    $geo        = new PhilippinesGeo();
    $allRegions = $geo->getRegions();

    $initProvinces = $selRegion   ? $geo->getProvinces($selRegion)   : [];
    $initCities    = $selProvince ? $geo->getCities($selProvince)    : [];

    // Build full structure for JS cascade
    $geoJson = [];
    foreach ($allRegions as $rgn) {
        $provs = $geo->getProvinces($rgn);
        $geoJson[$rgn] = [];
        foreach ($provs as $prov) {
            $geoJson[$rgn][$prov] = $geo->getCities($prov);
        }
    }

    $uid = 'addr_' . uniqid();
@endphp

<div id="{{ $uid }}_geo"
     data-value="{{ json_encode($geoJson, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
     style="display:none;"></div>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Region <span class="text-danger">*</span>
        </label>
        <select name="{{ $fieldPrefix }}region"
                id="{{ $uid }}_region"
                class="form-select"
                required>
            <option value="">-- Select Region --</option>
            @foreach($allRegions as $rgn)
                <option value="{{ $rgn }}"
                        {{ $selRegion === $rgn ? 'selected' : '' }}>
                    {{ $rgn }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Province <span class="text-danger">*</span>
        </label>
        <select name="{{ $fieldPrefix }}province"
                id="{{ $uid }}_province"
                class="form-select"
                required>
            <option value="">-- Select Province --</option>
            @foreach($initProvinces as $prov)
                <option value="{{ $prov }}"
                        {{ $selProvince === $prov ? 'selected' : '' }}>
                    {{ $prov }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            City / Municipality <span class="text-danger">*</span>
        </label>
        <select name="{{ $fieldPrefix }}city"
                id="{{ $uid }}_city"
                class="form-select"
                required>
            <option value="">-- Select City / Municipality --</option>
            @foreach($initCities as $cty)
                <option value="{{ $cty }}"
                        {{ $selCity === $cty ? 'selected' : '' }}>
                    {{ $cty }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Barangay <span class="text-danger">*</span>
        </label>
        <input type="text"
               name="{{ $fieldPrefix }}barangay"
               class="form-control"
               value="{{ $selBarangay }}"
               placeholder="e.g. Central Signal Village"
               required>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">
            House / Unit No. <span class="text-danger">*</span>
        </label>
        <input type="text"
               name="{{ $fieldPrefix }}house_unit_no"
               class="form-control"
               value="{{ $selHouseUnit }}"
               placeholder="e.g. 8"
               required>
    </div>

    <div class="col-md-5">
        <label class="form-label fw-semibold">
            Street <span class="text-danger">*</span>
        </label>
        <input type="text"
               name="{{ $fieldPrefix }}street"
               class="form-control"
               value="{{ $selStreet }}"
               placeholder="e.g. Rongo St."
               required>
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">
            ZIP Code <span class="text-danger">*</span>
        </label>
        <input type="text"
               name="{{ $fieldPrefix }}zip_code"
               class="form-control"
               value="{{ $selZip }}"
               placeholder="e.g. 1630"
               required>
    </div>
</div>

<script>
(function () {
    var geoEl    = document.getElementById('{{ $uid }}_geo');
    var regionEl = document.getElementById('{{ $uid }}_region');
    var provEl   = document.getElementById('{{ $uid }}_province');
    var cityEl   = document.getElementById('{{ $uid }}_city');
    var geoData  = JSON.parse(geoEl.getAttribute('data-value'));

    function fillSelect(el, items, selected) {
        var placeholder = el.options[0].text;
        el.innerHTML = '';
        var blank      = document.createElement('option');
        blank.value    = '';
        blank.text     = placeholder;
        el.appendChild(blank);
        items.forEach(function (item) {
            var opt      = document.createElement('option');
            opt.value    = item;
            opt.text     = item;
            opt.selected = (item === selected);
            el.appendChild(opt);
        });
    }

    regionEl.addEventListener('change', function () {
        var region   = this.value;
        var provinces = (region && geoData[region]) ? Object.keys(geoData[region]) : [];
        fillSelect(provEl, provinces, '');
        fillSelect(cityEl, [], '');
    });

    provEl.addEventListener('change', function () {
        var region   = regionEl.value;
        var province = this.value;
        var cities   = (region && province && geoData[region] && geoData[region][province])
            ? geoData[region][province] : [];
        fillSelect(cityEl, cities, '');
    });
}());
</script>
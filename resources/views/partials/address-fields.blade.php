@php
    $r   = $fieldPrefix ?? '';
    $val = fn($key) => old($r ? "{$r}_{$key}" : $key, $data[$key] ?? '');
    $nm  = fn($key) => $r ? "{$r}_{$key}" : $key;
    $uid = $r ?: 'main';
@endphp

{{-- Province + city data stored in divs (not script tags) so VSCode won't lint them as JS --}}
<div id="provData-{{ $uid }}" class="d-none" aria-hidden="true">{!! json_encode($provinces ?? []) !!}</div>
<div id="cityData-{{ $uid }}" class="d-none" aria-hidden="true">{!! json_encode($cities ?? []) !!}</div>
<div id="initData-{{ $uid }}"
     class="d-none" aria-hidden="true"
     data-region="{{ $val('region') }}"
     data-province="{{ $val('province') }}"
     data-city="{{ $val('city') }}">
</div>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Region</label>
        <select name="{{ $nm('region') }}"
                id="regionSelect_{{ $uid }}"
                class="form-select @error($nm('region')) is-invalid @enderror">
            <option value="">-- Select Region --</option>
            @foreach($regions as $code => $name)
                <option value="{{ $name }}"
                        data-code="{{ $code }}"
                        {{ $val('region') == $name ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        @error($nm('region'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Province</label>
        <select name="{{ $nm('province') }}"
                id="provinceSelect_{{ $uid }}"
                class="form-select @error($nm('province')) is-invalid @enderror">
            <option value="">-- Select Province --</option>
        </select>
        @error($nm('province'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">City / Municipality</label>
        <select name="{{ $nm('city') }}"
                id="citySelect_{{ $uid }}"
                class="form-select @error($nm('city')) is-invalid @enderror">
            <option value="">-- Select City --</option>
        </select>
        @error($nm('city'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Barangay</label>
        <input type="text" name="{{ $nm('barangay') }}"
               class="form-control @error($nm('barangay')) is-invalid @enderror"
               value="{{ $val('barangay') }}"
               placeholder="e.g. Brgy. Signal Village">
        @error($nm('barangay'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">House / Unit No.</label>
        <input type="text" name="{{ $nm('house_unit_no') }}"
               class="form-control @error($nm('house_unit_no')) is-invalid @enderror"
               value="{{ $val('house_unit_no') }}"
               placeholder="e.g. Unit 4B, 123">
        @error($nm('house_unit_no'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Street</label>
        <input type="text" name="{{ $nm('street') }}"
               class="form-control @error($nm('street')) is-invalid @enderror"
               value="{{ $val('street') }}"
               placeholder="e.g. Rongo St.">
        @error($nm('street'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label fw-semibold">ZIP Code</label>
        <input type="text" name="{{ $nm('zip_code') }}"
               class="form-control @error($nm('zip_code')) is-invalid @enderror"
               value="{{ $val('zip_code') }}"
               placeholder="e.g. 1630"
               maxlength="10">
        @error($nm('zip_code'))<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

{{-- Pure JavaScript below — zero Blade directives, VSCode-safe --}}
<script>
(function () {
    var rSel = document.querySelector('select[id^="regionSelect_"]');
    if (!rSel) return;
    var uid = rSel.id.replace('regionSelect_', '');

    var provEl = document.getElementById('provData-' + uid);
    var cityEl = document.getElementById('cityData-' + uid);
    var initEl = document.getElementById('initData-' + uid);
    if (!provEl || !cityEl || !initEl) return;

    var pData      = JSON.parse(provEl.textContent);
    var cData      = JSON.parse(cityEl.textContent);
    var initRegion = initEl.dataset.region   || '';
    var initProv   = initEl.dataset.province || '';
    var initCity   = initEl.dataset.city     || '';

    var pSel = document.getElementById('provinceSelect_' + uid);
    var cSel = document.getElementById('citySelect_'     + uid);
    if (!pSel || !cSel) return;

    function getCode(regionName) {
        var opt = Array.from(rSel.options).find(function(o) { return o.value === regionName; });
        return opt ? opt.dataset.code : null;
    }

    function fillProvinces(regionName, preselect) {
        var code = getCode(regionName);
        var list = code ? (pData[code] || []) : [];
        pSel.innerHTML = '<option value="">-- Select Province --</option>';
        list.forEach(function(p) {
            var o = new Option(p, p, false, p === preselect);
            pSel.appendChild(o);
        });
        if (preselect && list.includes(preselect)) {
            fillCities(preselect, initCity);
        }
    }

    function fillCities(province, preselect) {
        var list = cData[province] || [];
        cSel.innerHTML = '<option value="">-- Select City --</option>';
        list.forEach(function(c) {
            var o = new Option(c, c, false, c === preselect);
            cSel.appendChild(o);
        });
    }

    rSel.addEventListener('change', function() {
        fillProvinces(this.value, null);
        cSel.innerHTML = '<option value="">-- Select City --</option>';
    });

    pSel.addEventListener('change', function() {
        fillCities(this.value, null);
    });

    if (initRegion) {
        fillProvinces(initRegion, initProv);
    }
})();
</script>
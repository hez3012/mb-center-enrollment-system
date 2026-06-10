@once
<style>
.av-32,.av-36,.av-40,.av-48,.av-56,.av-64,.av-80{border-radius:50%;flex-shrink:0;user-select:none;}
.av-32{width:32px;height:32px;font-size:12px;}
.av-36{width:36px;height:36px;font-size:14px;}
.av-40{width:40px;height:40px;font-size:15px;}
.av-48{width:48px;height:48px;font-size:18px;}
.av-56{width:56px;height:56px;font-size:21px;}
.av-64{width:64px;height:64px;font-size:24px;}
.av-80{width:80px;height:80px;font-size:30px;}
img.av-32,img.av-36,img.av-40,img.av-48,img.av-56,img.av-64,img.av-80{object-fit:cover;}
.av-c0{background:#4f46e5;}
.av-c1{background:#0891b2;}
.av-c2{background:#059669;}
.av-c3{background:#d97706;}
.av-c4{background:#dc2626;}
.av-c5{background:#7c3aed;}
.av-c6{background:#db2777;}
</style>
@endonce

@php
    $sz  = isset($size)  ? (int)    $size  : 36;
    $nm  = isset($name)  ? (string) $name  : '?';
    $img = isset($image) ? (string) $image : null;

    $allowed = [32, 36, 40, 48, 56, 64, 80];
    $szKey   = 36;
    foreach ($allowed as $s) {
        if ($sz <= $s) { $szKey = $s; break; }
    }
    if ($sz > 80) { $szKey = 80; }

    $pts = array_values(array_filter(explode(' ', trim($nm))));
    $f   = isset($pts[0]) ? (string) $pts[0] : '?';
    $l   = (count($pts) > 1) ? (string) $pts[count($pts) - 1] : '';
    $ini = strtoupper(substr($f, 0, 1) . (($l !== '') ? substr($l, 0, 1) : ''));

    $ci  = abs(crc32($nm)) % 7;
@endphp

@if($img)
    <img src="{{ asset('storage/' . $img) }}"
         class="av-{{ $szKey }}"
         alt="{{ $nm }}">
@else
    <div class="d-inline-flex align-items-center justify-content-center fw-bold text-white av-{{ $szKey }} av-c{{ $ci }}">{{ $ini }}</div>
@endif
<section class="section-sm section-surface" aria-label="Customers using PixEnhance">
    <div class="shell">
        <div class="logo-wall" data-reveal="fade">
            <p class="logo-wall__label">Powering image pipelines at 4,000+ companies</p>

            @php
                $brands = [
                    ['file' => 'northwind.svg', 'name' => 'Northwind', 'w' => 139],
                    ['file' => 'pixelforge.svg', 'name' => 'PixelForge', 'w' => 150],
                    ['file' => 'lumen.svg', 'name' => 'Lumen', 'w' => 95],
                    ['file' => 'atlasco.svg', 'name' => 'AtlasCo', 'w' => 117],
                    ['file' => 'vertexlab.svg', 'name' => 'VertexLab', 'w' => 139],
                    ['file' => 'orbitly.svg', 'name' => 'Orbitly', 'w' => 117],
                ];
            @endphp

            <div class="marquee logo-marquee" style="--marquee-duration: 32s;">
                {{-- Track is duplicated so the -100% keyframe loops seamlessly; the copy is decorative. --}}
                <ul class="marquee__track logo-strip">
                    @foreach ($brands as $brand)
                        <li class="logo-strip__item">
                            <span class="logo-strip__mark" role="img" aria-label="{{ $brand['name'] }}"
                                style="--logo-src: url('{{ asset('assets/frontend/enhance/img/brands/' . $brand['file']) }}'); --logo-w: {{ $brand['w'] }}px;"></span>
                        </li>
                    @endforeach
                </ul>
                <ul class="marquee__track logo-strip" aria-hidden="true">
                    @foreach ($brands as $brand)
                        <li class="logo-strip__item">
                            <span class="logo-strip__mark"
                                style="--logo-src: url('{{ asset('assets/frontend/enhance/img/brands/' . $brand['file']) }}'); --logo-w: {{ $brand['w'] }}px;"></span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

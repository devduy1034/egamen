@extends('layout')
@section('content')
    @php
        $propertyGroups = collect($listProperties ?? []);
        $sizeGroup = $propertyGroups->first(function ($group) {
            $slug = strtolower((string) ($group[0]['slugvi'] ?? ''));
            return in_array($slug, ['size', 'kich-co', 'kich-thuoc'], true);
        });
        $colorGroup = $propertyGroups->first(function ($group) {
            $slug = strtolower((string) ($group[0]['slugvi'] ?? ''));
            return in_array($slug, ['mau', 'color'], true);
        });
        $selectedPrices = !empty(Request()->price) ? array_filter(explode(',', (string) Request()->price)) : [];
        $priceRanges = [
            'under-300k' => 'Dưới 300.000đ',
            '300k-500k' => '300.000đ - 500.000đ',
            '500k-700k' => '500.000đ - 700.000đ',
            '700k-1000k' => '700.000đ - 1.000.000đ',
            'over-1000k' => 'Trên 1.000.000đ',
        ];
    @endphp
    <section>
        <div class="wrap-content my-4">
            <div class="title-detail">
                <h1 class="text-center">{{ $titleMain }}</h1>
                <h2 class="hidden">{{ $titleMain }}</h2>
                <div class="filter"><i class="fa-solid fa-filter"></i>&nbsp; {{ __('web.loc') }} </div>
            </div>
            @if ($com == 'tim-kiem')
                <div class="div_kq_search mb-4">{{ $titleMain }} ({{ count($product) }}):
                    <span>"{{ $keyword }}"</span></div>
            @endif
            <div class="sort-select" x-data="{ open: false }">
                <p class="click-sort" @click="open = ! open">{{ __('web.sapxep') }}: <span
                        class="sort-show">{{ __('web.moinhat') }}</span></p>
                <div class="sort-select-main sort" x-show="open">
                    <p><a data-sort="1"
                            class="{{ Request()->sort == 1 || empty(Request()->sort) ? 'check' : '' }}"><i></i>{{ __('web.moinhat') }}</a>
                    </p>
                    <p><a data-sort="2"
                            class="{{ Request()->sort == 2 ? 'check' : '' }}"><i></i>{{ __('web.cunhat') }}</a></p>
                    <p><a data-sort="3"
                            class="{{ Request()->sort == 3 ? 'check' : '' }}"><i></i>{{ __('web.giacaodenthap') }}</a></p>
                    <p><a data-sort="4"
                            class="{{ Request()->sort == 4 ? 'check' : '' }}"><i></i>{{ __('web.giathapdencao') }}</a></p>
                    <input type="hidden" name="url" class="url-search" value="{{ Request()->url() }}" />
                </div>
            </div>
            <div class="flex-product-main">
                <div class="left-product product-filter-sidebar">
                    <div class="product-filter-header">
                        <div>
                            <span class="product-filter-label">Bộ lọc</span>
                            <h3>Lọc sản phẩm</h3>
                        </div>
                        @if (!empty(Request()->query()))
                            <a class="product-filter-reset" href="{{ Request()->url() }}">Xóa lọc</a>
                        @endif
                    </div>

                    <div class="wr-search p-0 product-filter-group">
                        <p class="text-split transition">Khoảng giá</p>
                        <ul class="p-0 filter-option-list">
                            @foreach ($priceRanges as $rangeKey => $rangeLabel)
                                @php $priceId = 'price-' . $rangeKey; @endphp
                                <li class="item-search item-search--line">
                                    <input {{ in_array($rangeKey, $selectedPrices, true) ? 'checked' : '' }}
                                        class="ip-search" id="{{ $priceId }}" type="checkbox"
                                        data-list="price" name="ip-search" value="{{ $rangeKey }}">
                                    <label for="{{ $priceId }}">{{ $rangeLabel }}</label>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @if (!empty($sizeGroup))
                        @php
                            $sizeSlug = $sizeGroup[0]['slug' . $lang] ?? $sizeGroup[0]['slugvi'] ?? 'size';
                            $selectedSizes = !empty(Request()->$sizeSlug) ? explode(',', (string) Request()->$sizeSlug) : [];
                        @endphp
                        <div class="wr-search p-0 product-filter-group">
                            <p class="text-split transition">Size</p>
                            <ul class="p-0 filter-chip-list">
                                @foreach ($sizeGroup[1] as $value)
                                    @php $sizeId = $sizeSlug . '-' . $value['id']; @endphp
                                    <li class="item-search item-search--chip">
                                        <input {{ in_array((string) $value['id'], $selectedSizes, true) ? 'checked' : '' }}
                                            class="ip-search" id="{{ $sizeId }}" type="checkbox"
                                            data-list="{{ $sizeSlug }}" name="ip-search"
                                            value="{{ $value['id'] }}">
                                        <label for="{{ $sizeId }}">{{ $value['name' . $lang] }}</label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (!empty($colorGroup))
                        @php
                            $colorSlug = $colorGroup[0]['slug' . $lang] ?? $colorGroup[0]['slugvi'] ?? 'mau';
                            $selectedColors = !empty(Request()->$colorSlug) ? explode(',', (string) Request()->$colorSlug) : [];
                            $colorGroupsMeta = [
                                'black' => ['label' => 'Đen', 'swatch' => '#111827', 'keywords' => ['den', 'black', 'charcoal']],
                                'white' => ['label' => 'Trắng', 'swatch' => '#f8fafc', 'keywords' => ['trang', 'white', 'ivory']],
                                'gray' => ['label' => 'Xám', 'swatch' => '#9ca3af', 'keywords' => ['xam', 'gray', 'grey', 'ghi', 'silver']],
                                'beige' => ['label' => 'Be', 'swatch' => '#d6c2a1', 'keywords' => ['be', 'kem', 'nude', 'cream']],
                                'brown' => ['label' => 'Nâu', 'swatch' => '#7c4a2d', 'keywords' => ['nau', 'brown', 'coffee', 'chocolate']],
                                'blue' => ['label' => 'Xanh dương', 'swatch' => '#2563eb', 'keywords' => ['xanh', 'blue', 'navy', 'cyan']],
                                'green' => ['label' => 'Xanh lá', 'swatch' => '#16a34a', 'keywords' => ['la', 'green', 'mint', 'olive', 'reu']],
                                'redpink' => ['label' => 'Đỏ / Hồng', 'swatch' => 'linear-gradient(135deg, #dc2626 0%, #ec4899 100%)', 'keywords' => ['do', 'red', 'burgundy', 'hong', 'pink', 'rose']],
                                'yelloworange' => ['label' => 'Vàng / Cam', 'swatch' => 'linear-gradient(135deg, #f59e0b 0%, #f97316 100%)', 'keywords' => ['vang', 'yellow', 'cam', 'orange', 'mustard']],
                                'purple' => ['label' => 'Tím', 'swatch' => '#7c3aed', 'keywords' => ['tim', 'purple', 'violet', 'lilac']],
                                'other' => ['label' => 'Khác', 'swatch' => 'linear-gradient(135deg, #475569 0%, #cbd5e1 100%)', 'keywords' => []],
                            ];

                            $groupedColors = [];
                            foreach ($colorGroupsMeta as $groupKey => $meta) {
                                $groupedColors[$groupKey] = array_merge($meta, ['ids' => [], 'names' => []]);
                            }

                            foreach ($colorGroup[1] as $value) {
                                $rawColorName = (string) ($value['name' . $lang] ?? $value['namevi'] ?? '');
                                $normalizedColorName = strtolower(\LARAVEL\Core\Support\Str::ascii($rawColorName));
                                $matchedGroupKey = 'other';

                                foreach ($colorGroupsMeta as $groupKey => $meta) {
                                    foreach ($meta['keywords'] as $keyword) {
                                        if ($keyword !== '' && str_contains($normalizedColorName, $keyword)) {
                                            $matchedGroupKey = $groupKey;
                                            break 2;
                                        }
                                    }
                                }

                                $groupedColors[$matchedGroupKey]['ids'][] = (int) ($value['id'] ?? 0);
                                $groupedColors[$matchedGroupKey]['names'][] = $rawColorName;
                            }

                            $groupedColors = collect($groupedColors)
                                ->filter(fn($group) => !empty($group['ids']))
                                ->map(function ($group) {
                                    $group['ids'] = array_values(array_unique(array_filter($group['ids'])));
                                    $group['names'] = array_values(array_unique(array_filter($group['names'])));
                                    return $group;
                                })
                                ->sortByDesc(function ($group) use ($selectedColors) {
                                    return !empty(array_intersect(array_map('strval', $group['ids']), $selectedColors));
                                })
                                ->values();
                        @endphp
                        <div class="wr-search p-0 product-filter-group">
                            <p class="text-split transition">Màu sắc</p>
                            <ul class="p-0 filter-color-list filter-color-list--grouped">
                                @foreach ($groupedColors as $group)
                                    @php
                                        $groupIds = implode(',', $group['ids']);
                                        $colorId = $colorSlug . '-group-' . $loop->index;
                                        $isCheckedGroup = !empty(array_intersect(array_map('strval', $group['ids']), $selectedColors));
                                        $groupTitle = $group['label'] . ': ' . implode(', ', $group['names']);
                                    @endphp
                                    <li class="item-search item-search--color item-search--color-group">
                                        <input {{ $isCheckedGroup ? 'checked' : '' }}
                                            class="ip-search" id="{{ $colorId }}" type="checkbox"
                                            data-list="{{ $colorSlug }}" name="ip-search"
                                            value="{{ $groupIds }}">
                                        <label for="{{ $colorId }}" title="{{ $groupTitle }}">
                                            <span class="filter-color-swatch"
                                                style="background: {{ $group['swatch'] }};"></span>
                                            <span class="sr-only">{{ $groupTitle }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="right-product w-100">
                    @if (!empty($product))
                        <div class="grid-product">
                            @foreach ($product as $v)
                                @include('component.itemProduct', ['product' => $v])
                            @endforeach
                        </div>
                    @endif
                    {!! $product->appends(request()->query())->onEachSide(3)->links() !!}
                </div>
            </div>
        </div>
    </section>
@endsection

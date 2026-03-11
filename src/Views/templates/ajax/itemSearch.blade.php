@if (!empty($productAjax))
    @foreach ($productAjax as $product)
        <div class="box-search">
            <div class="pic-search">
                <a class="scale-img img block aspect-[60/200]" href="{{ url('slugweb', ['slug' => $product['slugvi']]) }}"
                    title="{{ $product['namevi'] }}">
                    <img onerror="this.src='{{ thumbs('thumbs/60x60x1/assets/images/noimage.png') }}';"
                        src="{{ assets_photo('product', '60x60x1', $product['photo'], 'thumbs') }}" alt="{{ $product['namevi'] }}">
                </a>
            </div>
            <div class="ds-item-search">
                <h3 class="name-product">
                    <a class="text-split text-decoration-none" href="{{ url('slugweb', ['slug' => $product['slugvi']]) }}"
                        title="{{ $product['namevi'] }}">{{ $product['namevi'] }}</a>
                </h3>
                <p class="price-product">
                    @if (empty($product->sale_price))
                    @if (empty($product->regular_price))
                    <span class="price-new">Liên hệ</span>
                    @else
                    <span class="price-new">{{ Func::formatMoney($product->regular_price) }}</span>
                    @endif
                    @else
                    <span class="price-old">{{ Func::formatMoney($product->regular_price) }}</span>
                    <span class="price-new">{{ Func::formatMoney($product->sale_price) }}</span>
                    <span class="price-per">(-{{ $product->discount }}%)</span>
                    @endif
                </p>
            </div>
        </div>
    @endforeach
@endif
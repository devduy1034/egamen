<?php


namespace LARAVEL\Controllers\Web;

use Illuminate\Http\Request;
use LARAVEL\Core\Support\Facades\View;
use LARAVEL\Core\Support\Facades\BreadCrumbs;
use LARAVEL\Controllers\Controller;
use LARAVEL\Core\Support\Facades\Seo;
use LARAVEL\Models\NewsModel;
use LARAVEL\Models\ProductModel;
use LARAVEL\Models\ProductListModel;
use LARAVEL\Models\ProductCatModel;
use LARAVEL\Models\ProductItemModel;
use LARAVEL\Models\ProductSubModel;
use LARAVEL\Models\ProductBrandModel;
use LARAVEL\Models\PropertiesListModel;
use LARAVEL\Models\ProductPropertiesModel;
use LARAVEL\Models\GalleryModel;

class ProductController extends Controller
{

    private array $template;

    private function initTemplate(?string $type = null)
    {
        $this->template['folder'] = isset($this->configTypeArr['product'][$type ?? $this->type]['template']) ? $this->configTypeArr['product'][$type ?? $this->type]['template'] : 'product';
        $this->template['list'] = isset($this->configTypeArr['product'][$type ?? $this->type]['template-list']) ? $this->configTypeArr['product'][$type ?? $this->type]['template-list'] : 'product';
    }

    public function index(Request $request)
    {
        $product = $this->productItem('', $request, $this->type);
        $titleMain =  $this->infoSeo('product', $this->type, 'title');
        $titleMain = __('web.' . $titleMain);
        BreadCrumbs::setBreadcrumb(type: $this->type, title: $titleMain);
        $this->seoPage($titleMain, $this->infoSeo('product', $this->type, 'type', 'index'));

        $this->initTemplate();

        return View::share(['com' => $this->type])->view(implode('.', $this->template), compact('product', 'titleMain'));
    }

    public function allBrand(Request $request)
    {
        $brand = ProductBrandModel::select('id', 'name' . $this->lang, 'desc' . $this->lang, 'slug' . $this->lang,  'photo', 'type')
            ->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])
            ->orderBy('numb', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(12);
        $titleMain =  __('web.hangsanpham');
        BreadCrumbs::setBreadcrumb(type: $this->type, title: $titleMain);
        $this->seoPage($titleMain, $this->infoSeo('product', $this->type, 'type', 'index'));
        return View::share(['com' => $this->type])->view('brand.brand', compact('brand', 'titleMain'));
    }

    public function brand($slug, Request $request)
    {
        $itemBrand = ProductBrandModel::select('id', 'name' . $this->lang, 'desc' . $this->lang, 'slug' . $this->lang,  'photo', 'type')
            ->where(function ($query) use ($slug) {
                $query->where("slug" . $this->lang, $slug);
            })
            ->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])
            ->first();
        $this->type =  $itemBrand->type;
        $titleMain = $itemBrand['name' . $this->lang];
        BreadCrumbs::setBreadcrumb(list: $itemBrand);
        $product = $this->productItem($itemBrand, $request);
        $seoPage = $itemBrand->getSeo('product-brand', 'save')->first();
        $seoPage['type'] = $this->infoSeo('product', $this->type, 'type', 'index');
        Seo::setSeoData($seoPage, 'product', 'seo');

        $this->initTemplate($itemBrand['type']);

        return View::share(['idList' => $itemBrand['id'], 'com' => $this->type])->view(implode('.', $this->template), compact('product', 'titleMain'));
    }

    public function list($slug, Request $request)
    {
        $itemList = ProductListModel::select('id', 'name' . $this->lang, 'desc' . $this->lang, 'slug' . $this->lang,  'photo', 'type')
            ->where(function ($query) use ($slug) {
                $query->where("slug" . $this->lang, $slug);
            })
            ->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])
            ->first();
        $listProperties =  $this->searchListProperties($itemList['id']);
        $this->type =  $itemList->type;
        $titleMain = $itemList['name' . $this->lang];
        if ($this->infoSeo('product', $itemList->type, 'type', 'index')) BreadCrumbs::set(url('slugweb', ['slug' => $itemList->type]), __('web.' . $this->infoSeo('product', $itemList->type, 'title')));
        BreadCrumbs::setBreadcrumb(list: $itemList);
        $product = $this->productItem($itemList, $request);
        $seoPage = $itemList->getSeo('product-list', 'save')->first();
        $seoPage['type'] = $this->infoSeo('product', $this->type, 'type', 'index');
        Seo::setSeoData($seoPage, 'product', 'seo');

        $this->initTemplate($itemList['type']);

        return View::share(['idList' => $itemList['id'], 'com' => $this->type])->view(implode('.', $this->template), compact('product', 'titleMain', 'listProperties'));
    }

    public function cat($slug, Request $request)
    {
        $itemCat = ProductCatModel::select('id', 'id_list', 'name' . $this->lang,  'desc' . $this->lang, 'slug' . $this->lang, 'photo', 'id_list', 'type')
            ->where(function ($query) use ($slug) {
                $query->where("slug" . $this->lang, $slug);
            })
            ->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])
            ->first();
        $listProperties =  $this->searchListProperties($itemCat['id_list']);
        $this->type =  $itemCat->type;
        $titleMain = $itemCat['name' . $this->lang];
        $itemList = $itemCat->getCategoryList;
        if ($this->infoSeo('product', $itemCat->type, 'type', 'index')) BreadCrumbs::set(url('slugweb', ['slug' => $itemCat->type]), __('web.' . $this->infoSeo('product', $itemCat->type, 'title')));
        BreadCrumbs::setBreadcrumb(list: $itemList, cat: $itemCat);
        $product = $this->productItem($itemCat, $request);
        $seoPage = $itemCat->getSeo('product-cat', 'save')->first();
        $seoPage['type'] = $this->infoSeo('product', $this->type, 'type', 'index');
        Seo::setSeoData($seoPage, 'product', 'seo');

        $this->initTemplate($itemCat['type']);

        return View::share(['com' => $this->type])->view(implode('.', $this->template), compact('product', 'titleMain', 'listProperties'));
    }

    public function item($slug, Request $request)
    {
        $itemItem = ProductItemModel::select('id', 'id_list', 'name' . $this->lang,  'desc' . $this->lang, 'slug' . $this->lang,  'photo', 'id_list', 'id_cat', 'type')
            ->where(function ($query) use ($slug) {
                $query->where("slug" . $this->lang, $slug);
            })
            ->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])
            ->first();
        $listProperties =  $this->searchListProperties($itemItem['id_list']);
        $this->type =  $itemItem->type;
        $titleMain = $itemItem['name' . $this->lang];
        $itemList = $itemItem->getCategoryList;
        $itemCat = $itemItem->getCategoryCat;
        if ($this->infoSeo('product', $itemItem->type, 'type', 'index')) BreadCrumbs::set(url('slugweb', ['slug' => $itemItem->type]), __('web.' . $this->infoSeo('product', $itemItem->type, 'title')));
        BreadCrumbs::setBreadcrumb(list: $itemList, cat: $itemCat, item: $itemItem);
        $product = $this->productItem($itemItem, $request);
        $seoPage = $itemItem->getSeo('product-item', 'save')->first();
        $seoPage['type'] = $this->infoSeo('product', $this->type, 'type', 'index');
        Seo::setSeoData($seoPage, 'product', 'seo');

        $this->initTemplate($itemItem['type']);

        return View::share(['com' => $this->type])->view(implode('.', $this->template), compact('product', 'titleMain', 'listProperties'));
    }
    public function sub($slug, Request $request)
    {
        $itemSub = ProductSubModel::select('id', 'name' . $this->lang, 'desc' . $this->lang, 'slug' . $this->lang, 'photo', 'id_list', 'id_cat', 'id_item', 'type')
            ->where(function ($query) use ($slug) {
                $query->where("slug" . $this->lang, $slug);
            })
            ->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])
            ->first();
        $this->type =  $itemSub->type;
        $titleMain = $itemSub['name' . $this->lang];
        $itemList = $itemSub->getCategoryList;
        $itemCat = $itemSub->getCategoryCat;
        $itemItem = $itemSub->getCategoryItem;
        if ($this->infoSeo('product', $itemSub->type, 'type', 'index')) BreadCrumbs::set(url('slugweb', ['slug' => $itemSub->type]), __('web.' . $this->infoSeo('product', $itemSub->type, 'title')));
        BreadCrumbs::setBreadcrumb(list: $itemList, cat: $itemCat, item: $itemItem, sub: $itemSub);
        $product = $this->productItem($itemSub, $request);
        $seoPage = $itemSub->getSeo('product-sub', 'save')->first();
        $seoPage['type'] = $this->infoSeo('product', $this->type, 'type', 'index');
        Seo::setSeoData($seoPage, 'product', 'seo');

        $this->initTemplate($itemSub['type']);

        return View::share(['com' => $this->type])->view(implode('.', $this->template), compact('product', 'titleMain'));
    }
    public function detail($slug)
    {
        $rowDetail = ProductModel::select('type', 'id', 'id_list', 'properties', 'name' . $this->lang, 'slug' . $this->lang, 'desc' . $this->lang,  'content' . $this->lang,  'code', 'view', 'id_brand', 'id_list', 'id_cat', 'id_item', 'id_sub', 'photo', 'options', 'sale_price', 'regular_price', 'type', 'discount', 'view')->where(function ($query) use ($slug) {
            $query->where("slug" . $this->lang, $slug);
        })->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])->first();
        if (!empty($rowDetail)) $rowDetail->increment('view');
        $query = PropertiesListModel::select('type', 'id', 'name' . $this->lang)
            ->where('type', 'san-pham')
            ->whereRaw("FIND_IN_SET(?,status)", ['cart']);
        if (!empty(config('type.categoriesProperties'))) $query->whereRaw("FIND_IN_SET(?,id_list)", [$rowDetail['id_list']]);
        if (!empty($rowDetail['properties'])) {
            $listProperties = $query->orderBy('numb', 'asc')->get()->map(function ($v) use ($rowDetail) {
                $propertyQuery = $v->getProperties()->whereIn('id', explode(',', $rowDetail['properties']));

                try {
                    $properties = (clone $propertyQuery)
                        ->orderBy('number', 'asc')
                        ->orderBy('numb', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();
                } catch (\Throwable $e) {
                    $properties = $propertyQuery
                        ->orderBy('numb', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();
                }

                return [$v, $properties];
            });
        } else {
            $listProperties = [];
        }
        $this->type =  $rowDetail->type;
        $seoPage = $rowDetail->getSeo('product', 'save')->first();
        $seoPage['type'] = $this->infoSeo('product', $this->type, 'type', 'detail');
        Seo::setSeoData($seoPage, 'product', 'seo');
        $rowDetailPhoto = $rowDetail->getPhotos('product')->where('type', 'san-pham')->get();
        //$rowDetailPhoto1 = $rowDetail->getPhotos('product')->where('type', 'hinh-anh')->get();
        $rowNews = $rowDetail->getNews()->get();
        $brandDetail = $rowDetail->getBrand()->first();
        $tags = $rowDetail->tags ?? [];

        $thumbDetail = config('type.product.' . $rowDetail['type'] . '.images.photo.thumb_detail');

        $thumbPath = !empty($thumbDetail) ? $thumbDetail : '710x440x1';

        $propertyRows = ProductPropertiesModel::where('id_parent', $rowDetail['id'])->get();
        $isInStockVariant = function ($row): bool {
            $qty = (int) ($row->quantity ?? 0);
            $status = strtolower(trim((string) ($row->status ?? 'active')));
            return $qty > 0 && $status !== 'inactive';
        };
        $initialVariant = $propertyRows->first($isInStockVariant);
        if (empty($initialVariant)) {
            $initialVariant = $propertyRows->first();
        }
        $initialVariantIds = !empty($initialVariant?->id_properties)
            ? array_map('intval', array_filter(explode(',', $initialVariant->id_properties)))
            : [];

        $variantStockMap = [];
        foreach ($propertyRows as $rowProperty) {
            $ids = !empty($rowProperty->id_properties)
                ? array_values(array_unique(array_map('intval', array_filter(explode(',', $rowProperty->id_properties)))))
                : [];
            sort($ids);
            if (empty($ids)) continue;
            $key = implode(',', $ids);
            $qty = (int) ($rowProperty->quantity ?? 0);
            $status = strtolower(trim((string) ($rowProperty->status ?? 'active')));
            $variantStockMap[$key] = [
                'quantity' => $qty,
                'in_stock' => $qty > 0 && $status !== 'inactive'
            ];
        }

        $inStockPropertyIds = [];
        foreach ($variantStockMap as $key => $stockInfo) {
            if (empty($stockInfo['in_stock'])) continue;
            $ids = array_values(array_unique(array_map('intval', array_filter(explode(',', (string) $key)))));
            foreach ($ids as $pid) {
                $inStockPropertyIds[$pid] = true;
            }
        }
        $inStockPropertyIds = array_map('intval', array_keys($inStockPropertyIds));

        $galleryIds = $propertyRows->pluck('id_photo')->filter()->unique()->values()->toArray();
        $galleryMap = !empty($galleryIds) && count($galleryIds) > 0
            ? GalleryModel::whereIn('id', $galleryIds)->pluck('photo', 'id')
            : collect();

        $propertyPhotoMap = [];
        $propertyCodeMap = [];
        $variantPhotoList = collect();
        foreach ($propertyRows as $rowProperty) {
            if (!empty($rowProperty->id_photo) && !empty($galleryMap[$rowProperty->id_photo])) {
                $variantPhotoList->push($galleryMap[$rowProperty->id_photo]);
            }
            $ids = !empty($rowProperty->id_properties) ? array_filter(explode(',', $rowProperty->id_properties)) : [];
            foreach ($ids as $pid) {
                if (empty($propertyPhotoMap[$pid]) && !empty($rowProperty->id_photo) && !empty($galleryMap[$rowProperty->id_photo])) {
                    $propertyPhotoMap[$pid] = $galleryMap[$rowProperty->id_photo];
                }
                if (empty($propertyCodeMap[$pid]) && !empty($rowProperty->code)) {
                    $propertyCodeMap[$pid] = $rowProperty->code;
                }
            }
        }

        $colorList = PropertiesListModel::where('slugvi', 'mau')->first();
        $colorListId = $colorList->id ?? 0;

        // Initial page should show original product image/code.
        $mainPhoto = $rowDetail['photo'];
        $initialCode = $rowDetail['code'];

        $albumPhotos = collect([$mainPhoto]);
        if (!empty($rowDetail['photo'])) $albumPhotos->push($rowDetail['photo']);
        if (!empty($rowDetailPhoto)) {
            foreach ($rowDetailPhoto as $photoItem) {
                if (!empty($photoItem['photo'])) $albumPhotos->push($photoItem['photo']);
            }
        }
        $albumPhotos = $albumPhotos->merge($variantPhotoList)->filter()->unique()->values();

        if ($this->infoSeo('product', $rowDetail->type, 'type', 'index')) BreadCrumbs::set(url('slugweb', ['slug' => $rowDetail->type]), __('web.' . $this->infoSeo('product', $rowDetail->type, 'title')));
        BreadCrumbs::setBreadcrumb(detail: $rowDetail, list: $rowDetail->getCategoryList, cat: $rowDetail->getCategoryCat, item: $rowDetail->getCategoryItem, sub: $rowDetail->getCategorySub);
        $query = ProductModel::select('id', 'name' . $this->lang, 'photo', 'desc' . $this->lang, 'slug' . $this->lang, 'regular_price', 'discount', 'sale_price', 'type', 'properties')
            ->with(['getPhotos' => function ($query) {
                $query->where('type', 'san-pham')->orderBy('numb', 'asc');
            }])
            ->where('type', 'san-pham');
        if (!empty($rowDetail['id_list'])) $query->where('id_list', $rowDetail['id_list']);
        if (!empty($rowDetail['id_cat'])) $query->where('id_cat', $rowDetail['id_cat']);
        $query->where("id", "!=", $rowDetail['id'])->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])->limit(10);
        $product = $query->get();
        $this->initTemplate($rowDetail['type']);

        return View::share(['idList' => $rowDetail['id_list'], 'com' => $this->type])->view($this->template['folder'] . '.detail', compact('rowDetail', 'rowDetailPhoto', 'product', 'tags', 'rowNews', 'listProperties', 'brandDetail', 'thumbPath', 'initialVariantIds', 'propertyPhotoMap', 'propertyCodeMap', 'colorListId', 'mainPhoto', 'initialCode', 'albumPhotos', 'variantStockMap', 'inStockPropertyIds'));
    }

    public function searchProduct(Request $request)
    {
        $keyword = $request->query('keyword');
        $product = ProductModel::select('id', 'name' . $this->lang, 'desc' . $this->lang,  'slug' . $this->lang, 'photo', 'regular_price', 'sale_price', 'discount', 'type')
            ->with(['getPhotos' => function ($query) {
                $query->where('type', 'san-pham')->orderBy('numb', 'asc');
            }])
            // ->search($keyword)
            ->where('name' . $this->lang, 'like', '%' . $keyword . '%')
            ->where('type', 'san-pham')
            ->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])
            ->orderBy('numb', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(12);
        $titleMain = 'Tìm kiếm sản phẩm';
        BreadCrumbs::setBreadcrumb(type: $this->type, title: $titleMain);
        return View::share(['com' => $this->type])->view('product.product', compact('product', 'titleMain', 'keyword'));
    }

    public function suggestProduct(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));
        if (mb_strlen($keyword) < 2) {
            return view('ajax.itemSearch', ['productAjax' => collect()]);
        }

        $product = ProductModel::select('id', 'name' . $this->lang, 'desc' . $this->lang, 'slug' . $this->lang, 'photo', 'regular_price', 'sale_price', 'discount', 'type', 'properties')
            ->with(['getPhotos' => function ($query) {
                $query->where('type', 'san-pham')->orderBy('numb', 'asc');
            }])
            ->where('name' . $this->lang, 'like', '%' . $keyword . '%')
            ->where('type', 'san-pham')
            ->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])
            ->orderBy('numb', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(15);
        return view('ajax.itemSearch', ['productAjax' => $product ?? []]);
    }

    protected function  checkListProperties($properties = [])
    {
        foreach ($properties as $k => $v) if (empty($v['data'])) unset($properties[$k]);
        return $properties;
    }

    private function  searchListProperties($idl)
    {
        $querySearch = PropertiesListModel::select('type', 'id', 'name' . $this->lang, 'slug' . $this->lang,)
            ->where('type', 'san-pham')
            ->whereRaw("FIND_IN_SET(?,id_list)", [$idl])
            ->whereRaw("FIND_IN_SET(?,status)", ['search']);
        return $querySearch->orderBy('numb', 'asc')->get()->map(fn($v) => [$v, $v->getProperties()->get()]);
    }

    private function productItem($array = null, $request = null, $slug = '')
    {
        // Mặc định sắp xếp
        $defaultOrderBy = ['numb' => 'asc', 'id' => 'desc'];
        $propaties = $request->getQueryString() ?? '';
        // Lấy thông tin sản phẩm cần truy vấn

        if (!empty($array)) {
            $query = $array->getItems([
                'id',
                'name' . $this->lang,
                'desc' . $this->lang,
                'slug' . $this->lang,
                'photo',
                'icon',
                'regular_price',
                'sale_price',
                'discount',
                'type',
                'properties'
            ])->with(['getPhotos' => function ($query) {
                $query->where('type', 'san-pham')->orderBy('numb', 'asc');
            }])->whereRaw("FIND_IN_SET(?,status)", ['hienthi']);
        } else {
            $query = ProductModel::select('id', 'name' . $this->lang, 'photo', 'icon', 'desc' . $this->lang, 'slug' . $this->lang, 'status', 'numb', 'sale_price', 'regular_price', 'type', 'discount', 'properties')
                ->with(['getPhotos' => function ($query) {
                    $query->where('type', 'san-pham')->orderBy('numb', 'asc');
                }])
                ->where('type', $slug)->whereRaw("FIND_IN_SET(?,status)", ['hienthi']);
        }
        // Nếu có tham số lọc từ query string
        if (!empty($propaties)) {
            parse_str($propaties, $result);
            unset($result['zarsrc']);
            unset($result['utm_source']);
            unset($result['utm_medium']);
            unset($result['utm_campaign']);
            unset($result['page']);
            $query->where(function ($query) use ($result, &$defaultOrderBy) {
                foreach ($result as $key => $propertyGroup) {
                    $items = explode(',', $propertyGroup);

                    // Điều chỉnh sắp xếp khi đến nhóm thuộc tính cuối cùng
                    if ($key == array_key_last($result)) {
                        $defaultOrderBy = match ($items[0]) {
                            "1" => ['id' => 'desc'],
                            "2" => ['id' => 'asc'],
                            "3" => ['sale_price' => 'desc', 'regular_price' => 'desc'],
                            "4" => ['sale_price' => 'asc', 'regular_price' => 'asc'],
                            default => ['numb' => 'asc', 'id' => 'desc'],
                        };
                    } else {
                        // Thêm điều kiện lọc thuộc tính
                        $query->where(function ($subQuery) use ($items) {
                            foreach ($items as $item) {
                                $subQuery->orWhereRaw('FIND_IN_SET(?, properties)', [$item]);
                            }
                        });
                    }
                }
            });
        }
        // Áp dụng sắp xếp dựa trên thứ tự mặc định hoặc từ bộ lọc
        foreach ($defaultOrderBy as $column => $direction) {
            // Kiểm tra nếu regular_sale > 0 thì ưu tiên sắp xếp theo regular_sale
            if ($column === 'sale_price') {
                $query->orderByRaw('CASE WHEN sale_price > 0 THEN sale_price ELSE regular_price END ' . $direction);
            } else {
                $query->orderBy($column, $direction);
            }
        }
        $product = $query->whereRaw("FIND_IN_SET(?,status)", ['hienthi'])->paginate(20);
        return $product;
    }
}
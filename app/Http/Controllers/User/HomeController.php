<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\ContactRequest;
use App\Http\Requests\Contact\LeadRequest;
use App\Http\Requests\Customer\CreateRequest;
use App\Http\Requests\UserProfile\UserProfileRequest;
use App\Services\LeadService;
use App\Models\Address;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Instruction;
use App\Models\Post;
use App\Models\Product;
use App\Models\Recruitment;
use App\Models\Slide;
use App\Models\Social;
use App\Models\Solution;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\View\Components\TableOfContent;
use App\Models\System;

class HomeController extends Controller
{

    private $system;

    public function __construct()
    {
        $this->system = $this->getSystem();
    }

        private function getSystem(){
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('systems')) {
                return convert_array(System::get(), 'keyword', 'content');
            }
        } catch (\Throwable $e) {
            // Tránh lỗi khi bảng systems chưa được tạo
        }
        return [];
    }

    public function index()
    {

        $sliders = Slide::orderBy('position', 'ASC')->get();
        $address = Address::where('status', '=', 1)->get();
        $social = Social::orderBy('id', 'DESC')->first();
        $posts = Post::orderBy('id', 'DESC')->where('status', 1)->limit(6)->get();

        $mainContent = Category::where([['parent_id', null], ['status', 1]])->orderBy('id', 'asc')->limit(6)->get();

        $this->generateSignedUrls($sliders, 'image_url');
        $this->generateSignedUrls($posts, 'image');

        foreach ($mainContent as $category) {
            if ($category->image) {
                $category->image = getImageUrl($category->image);
            }
        }
        // $system = 
        $seo = [
            'meta_title' => $this->system['seo_meta_title'] ?? config('app.name', 'DATYSO'),
            'meta_keyword' => $this->system['seo_meta_keyword'] ?? '',
            'meta_description' => $this->system['seo_meta_description'] ?? '',
            'meta_image' => !empty($this->system['seo_meta_images']) ? getImageUrl($this->system['seo_meta_images']) : '',
            'canonical' => config('app.url'),
        ];

        $schema = "<script type='application/ld+json'>
            {
                \"@context\": \"https://schema.org\",
                \"@type\": \"WebSite\",
                \"name\": \"" . $seo['meta_title'] . "\",
                \"url\": \"" . $seo['canonical'] . "\",
                \"description\": \"" . $seo['meta_description'] . "\",
                \"publisher\": {
                    \"@type\": \"Organization\",
                    \"name\": \"" . $seo['meta_title'] . "\"
                },
                \"potentialAction\": {
                    \"@type\": \"SearchAction\",
                    \"target\": {
                        \"@type\": \"EntryPoint\",
                        \"urlTemplate\": \"" . $seo['canonical'] . "search?q={search_term_string}\"
                    },
                    \"query-input\": \"required name=search_term_string\"
                }
            }
            </script>";



        return view('user.home.index', [
            'sliders' => $sliders,
            'address' => $address,
            'social' => $social,
            'posts' => $posts,
            'mainContent' => $mainContent,
            'seo' => $seo,
            'schema' => $schema
        ]);
    }

    public function getProductList($categorySlug)
    {
        if ($categorySlug == 'danh-muc') {
            $category = Category::where('status', 1)->first();
        } else {
            $category = $this->findActiveCategory($categorySlug);
        }
        if (! $category) {
            return redirect()->route('index');
        }
        $category->banner = getImageUrl($category->banner);

        $categoryIds = $category->getAllChildrenIds();

        $products = Product::whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('m_category.id', $categoryIds)->where('m_category.status', 1);
        })
        ->with(['images', 'categories'])
        ->where('status', 1)
        ->where('del_flg', 1)
        ->orderBy('id', 'desc')
        ->paginate(15);

        $seoTitle = !empty($category->seo_title) ? $category->seo_title : $category->name;
        $seoDesc = !empty($category->seo_description) 
            ? $category->seo_description 
            : (!empty($category->short_desc) ? \Illuminate\Support\Str::limit(strip_tags($category->short_desc), 160) : $category->name);

        $seo = [
            'meta_title' => $seoTitle,
            'meta_keyword' => '',
            'meta_description' => $seoDesc,
            'meta_image' => getImageUrl($category->image),
            'canonical' => route('product.list', ['categorySlug' => $category->slug]),
        ];

        $cat_name = $seoTitle;
        $cat_canonical = route('product.list', ['categorySlug' => $category->slug]);
        $cat_description = $seoDesc;
        $totalProducts = $products->total();

        $productListItems = [];
        $position = 1;

        foreach ($products as $product) {
            $name = $product->name;
            $productCatSlug = $product->categories->contains('id', $category->id)
                ? $category->slug
                : (($product->categories && $product->categories->isNotEmpty()) ? $product->categories->first()->slug : $category->slug);
            $canonical = route('product.detail', ['categorySlug' => $productCatSlug, 'productSlug' => $product->slug]);
            $productListItems[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $name,
                'url' => $canonical,
            ];
            $position++;
        }

        // Tạo cấu trúc JSON-LD chuẩn RFC 8259: bọc mảng [BreadcrumbList, CollectionPage], không có dấu phẩy thừa
        $jsonLdData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Trang chủ',
                        'item' => config('app.url'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $cat_name,
                        'item' => $cat_canonical,
                    ],
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $cat_name,
                'description' => $cat_description,
                'url' => $cat_canonical,
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'name' => $cat_name,
                    'numberOfItems' => $totalProducts,
                    'itemListElement' => $productListItems,
                ],
            ],
        ];

        $schema = "<script type=\"application/ld+json\">\n"
            . json_encode($jsonLdData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n"
            . "</script>";

        return view('user.product.list', ['category' => $category, 'products' => $products, 'seo' => $seo, 'schema' => $schema]);
    }

    /**
     * Hiển thị chi tiết sản phẩm theo danh mục và bỏ qua an toàn các tham số truy vấn lạ (?utm_..., ?gclid=, ?a=1).
     */
    public function getProductDetail(Request $request, string $categorySlug, string $productSlug)
    {
        $category = $this->findActiveCategory($categorySlug);
        if (! $category) {
            return redirect()->route('index');
        }

        $product = $this->findActiveProduct($productSlug);
        if (! $product) {
            return redirect()->route('index');
        }
        $productImage = ($product->images && $product->images->first()) ? $product->images->first()->image : '';

        if ($redirect = $this->resolveProductDetailRedirect($category, $product, $request)) {
            return $redirect;
        }

        $otherProducts = Product::where([['status', 1], ['id', '!=', $product->id]])->inRandomOrder()->limit(3)->get();

        $seoTitle = !empty($product->seo_title) ? $product->seo_title : $product->name;
        $seoDesc = !empty($product->seo_description)
            ? $product->seo_description
            : htmlspecialchars_decode((html_entity_decode(trim(
                preg_replace(
                    '/\s+/', // xoá tất cả khoảng trắng dư, kể cả \n \r \t
                    ' ',
                    cutnchar(strip_tags($product->description), 300)
                )
            ))));
        $seoKeywords = !empty($product->seo_keywords) ? $product->seo_keywords : '';

        $seo = [
            'meta_title' => $seoTitle,
            'meta_keyword' => $seoKeywords,
            'meta_description' => $seoDesc,
            'meta_image' => getImageUrl($productImage),
            'canonical' => route('product.detail', ['categorySlug' => $category->slug, 'productSlug' => $product->slug]),
        ];

        $image = getImageUrl($productImage);
        $name = $product->name;
        $description = !empty($product->seo_description) ? $product->seo_description : htmlspecialchars_decode(html_entity_decode(strip_tags($product->description)));
        if (empty($description)) {
            $description = $name;
        }
        $cat_name = $category->name;
        $cat_canonical = route('product.list', ['categorySlug' => $category->slug]);
        $canonical = route('product.detail', ['categorySlug' => $category->slug, 'productSlug' => $product->slug]);

        $itemBreadcrumbElements = '';

        $positionBreadcrumb = 2;

        $jsonLdData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Trang chủ',
                        'item' => config('app.url'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $cat_name,
                        'item' => $cat_canonical,
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $name,
                        'item' => $canonical,
                    ],
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $name,
                'description' => $description,
                'image' => $image,
                'url' => $canonical,
                'brand' => [
                    '@type' => 'Brand',
                    'name' => 'DATYSO',
                ],
                'manufacturer' => [
                    '@type' => 'Organization',
                    'name' => 'DATYSO',
                    'url' => config('app.url'),
                ],
                'material' => $cat_name,
                'category' => $cat_canonical,
                'sku' => (string) $product->id,
                'mpn' => (string) $product->id,
                'offers' => [
                    '@type' => 'Offer',
                    'price' => !empty($product->price) ? (string) $product->price : '0',
                    'priceCurrency' => 'VND',
                    'priceValidUntil' => '2027-12-31',
                    'url' => $canonical,
                    'availability' => 'https://schema.org/InStock',
                    'seller' => [
                        '@type' => 'Organization',
                        'name' => 'DATYSO',
                    ],
                    'itemCondition' => 'https://schema.org/NewCondition',
                ],
            ],
        ];

        $schema = "<script type=\"application/ld+json\">\n"
            . json_encode($jsonLdData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n"
            . "</script>";

        $system = $this->system;
        return view('user.product.detail', ['system' => $system, 'category' => $category, 'product' => $product, 'otherProducts' => $otherProducts, 'seo' => $seo, 'schema' => $schema]);
    }

    public function getInstructionList()
    {
        $instructions = Instruction::where('status', 1)->orderBy('id', 'desc')->paginate(15);
        $products = Product::where('status', 1)->where('del_flg', 1)->with('categories')->inRandomOrder()->limit(3)->get();
        foreach($instructions as $instruction) {
            $instruction->image = getImageUrl($instruction->image);
        }

        
        $seo = [
            'meta_title' => 'Chính sách',
            'meta_keyword' => '',
            'meta_description' => 'Chính sách',
            'meta_image' => '',
            'canonical' => config('app.url').'/chinh-sach',
        ];

        return view('user.instruction.list', ['instructions' => $instructions, 'products' => $products, 'seo' => $seo]);
    }

    public function getInstructionDetail($instructionSlug)
    {
        $instruction = Instruction::where([['status', 1], ['slug', $instructionSlug]])->first();
        if (!$instruction) {
            return redirect()->route('index');
        }

        $newestInstructions = Instruction::where([['status', 1], ['id', '!=', $instruction->id]])->orderBy('updated_at', 'desc')->limit(5)->get();
        $products = Product::where('status', 1)->where('del_flg', 1)->with('categories')->inRandomOrder()->limit(3)->get();
        $instruction->image = getImageUrl($instruction->image);

        $seo = [
            'meta_title' => $instruction->name,
            'meta_keyword' => '',
            'meta_description' => htmlspecialchars_decode((html_entity_decode(trim(
                preg_replace(
                    '/\s+/', // xoá tất cả khoảng trắng dư, kể cả \n \r \t
                    ' ',
                    cutnchar(strip_tags($instruction->content), 300)
                )
            )))),
            'meta_image' => '',
            'canonical' =>  route('instruction.detail', ['instructionSlug' => $instruction->slug]),
        ];

        $image = $instruction->image;

        $name = $instruction->name;

        $description = htmlspecialchars_decode(html_entity_decode(strip_tags($instruction->content)));

        $canonical = route('instruction.detail', ['instructionSlug' => $instruction->slug]);

        $itemBreadcrumbElements = '';

        $positionBreadcrumb = 2;

        $name = 'Tin tức';

        $catCanonical = config('app.url').'chinh-sach';

        $jsonLdData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Trang chủ',
                        'item' => config('app.url'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => 'Chính sách',
                        'item' => $catCanonical,
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $name,
                        'item' => $canonical,
                    ],
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $name,
                'description' => $description,
                'image' => $image,
                'url' => $canonical,
                'datePublished' => convertDateTime($instruction->created_at, 'Y-m-d'),
                'dateModified' => convertDateTime($instruction->updated_at ?? $instruction->created_at, 'Y-m-d'),
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'DATYSO',
                    'url' => config('app.url'),
                ],
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => $canonical,
                ],
            ],
        ];

        $schema = "<script type=\"application/ld+json\">\n"
            . json_encode($jsonLdData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n"
            . "</script>";


        return view('user.instruction.detail', ['instruction' => $instruction, 'newestInstructions' => $newestInstructions, 'products' => $products, 'schema' => $schema, 'seo' => $seo]);
    }

    public function getPostList()
    {

        $posts = Post::where('status', 1)->orderBy('id', 'desc')->paginate(15);
        $products = Product::where('status', 1)->where('del_flg', 1)->with('categories')->inRandomOrder()->limit(3)->get();


        $seo = [
            'meta_title' => 'Tin tức',
            'meta_keyword' => '',
            'meta_description' => 'Tin tức',
            'meta_image' => '',
            'canonical' => config('app.url').'/tin-tuc',
        ];

        
        return view('user.post.list', ['posts' => $posts, 'products' => $products, 'seo' => $seo]);
    }

    public function getPostDetail($postSlug)
    {
        $post = Post::where([['status', 1], ['slug', $postSlug]])->first();
        if (!$post) {
            return redirect()->route('index');
        }
        $content = $post->content;
        $items = TableOfContent::extract($content);
        $contentWithToc = null;
        $contentWithToc = TableOfContent::injectIds($content, $items);

        $newestPosts = Post::where([['status', 1], ['id', '!=', $post->id]])->orderBy('updated_at', 'desc')->limit(5)->get();
        $products = Product::where('status', 1)->where('del_flg', 1)->with('categories')->inRandomOrder()->limit(3)->get();

        $seo = [
            'meta_title' => $post->name,
            'meta_keyword' => '',
            'meta_description' => htmlspecialchars_decode((html_entity_decode(trim(
                preg_replace(
                    '/\s+/', // xoá tất cả khoảng trắng dư, kể cả \n \r \t
                    ' ',
                    cutnchar(strip_tags($post->content), 300)
                )
            )))),
            'meta_image' => getImageUrl($post->image) ,
            'canonical' =>  route('post.detail', ['postSlug' => $post->slug]),
        ];

        $image = getImageUrl($post->image);

        $name = $post->name;

        $description = htmlspecialchars_decode(html_entity_decode(strip_tags($post->content)));

        $canonical = route('post.detail', ['postSlug' => $post->slug]);

        $itemBreadcrumbElements = '';

        $positionBreadcrumb = 2;

        $name = 'Tin tức';

        $catCanonical = config('app.url').'tin-tuc';

        $jsonLdData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Trang chủ',
                        'item' => config('app.url'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => 'Tin tức',
                        'item' => $catCanonical,
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $post->name,
                        'item' => $canonical,
                    ],
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post->name,
                'description' => $description,
                'image' => $image,
                'url' => $canonical,
                'datePublished' => convertDateTime($post->created_at, 'Y-m-d'),
                'dateModified' => convertDateTime($post->updated_at ?? $post->created_at, 'Y-m-d'),
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'DATYSO',
                    'url' => config('app.url'),
                ],
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => $canonical,
                ],
            ],
        ];

        $schema = "<script type=\"application/ld+json\">\n"
            . json_encode($jsonLdData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n"
            . "</script>";
        $system = $this->system;

        return view('user.post.detail', ['system' => $system, 'post' => $post, 'newestPosts' => $newestPosts, 'products' => $products, 'contentWithToc' => $contentWithToc, 'seo' => $seo, 'schema' => $schema]);
    }

    public function getIntroduce()
    {
        $sliders = Slide::orderBy('position', 'ASC')->get();
        $this->generateSignedUrls($sliders, 'image_url');
        return view('user.introduce.list', ['sliders' => $sliders]);
    }

    public function getContact(Request $request)
    {
        $product = null;
        $category = null;

        if ($request->filled('san-pham')) {
            $product = Product::with(['images', 'categories'])
                ->where([['slug', $request->input('san-pham')], ['status', 1]])
                ->first();
            if ($product) {
                $category = $product->categories->first();
            }
        }

        if (!$category && $request->filled('danh-muc')) {
            $category = Category::where([['slug', $request->input('danh-muc')], ['status', 1]])->first();
        }

        $seoTitle = $product
            ? 'Liên hệ báo giá ' . $product->name . ' | DATYSO'
            : ($category
                ? 'Liên hệ tư vấn ' . $category->name . ' | DATYSO'
                : 'Liên hệ tư vấn & Báo giá máy công nghiệp | DATYSO');

        $productImage = ($product && $product->images && $product->images->first())
            ? getImageUrl($product->images->first()->image)
            : '';

        $seo = [
            'meta_title' => $seoTitle,
            'meta_keyword' => 'liên hệ DATYSO, báo giá máy công nghiệp, máy hàn laser, máy cắt laser fiber',
            'meta_description' => 'Liên hệ Công ty Cổ phần DATYSO Việt Nam để được tư vấn kỹ thuật theo vật liệu, độ dày, sản lượng và nhận báo giá nhanh trong giờ làm việc.',
            'meta_image' => $productImage,
            'canonical' => route('contact.index'),
        ];

        return view('user.contact.form', [
            'product' => $product,
            'category' => $category,
            'seo' => $seo,
        ]);
    }

    public function postContactForm(LeadRequest $request, LeadService $leadService)
    {
        try {
            $result = $leadService->createLead($request->validated(), $request);
            $contact = $result['contact'];
            $isDuplicate = (bool) ($result['is_duplicate'] ?? false);

            $productPrice = 0.0;
            if ($contact->product) {
                $productPrice = (float) ($contact->product->price_discount ?: ($contact->product->price ?: 0));
            }

            $leadPayload = [
                'source_type' => $contact->source_type ?? 'contact',
                'source_name' => $contact->source_name ?? '',
                'product_id' => $contact->product_id,
                'category_id' => $contact->category_id,
                'province' => $contact->province ?? '',
                'source_url' => $contact->source_url ?? $request->headers->get('referer', ''),
                'value' => $productPrice,
            ];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => true,
                    'duplicate' => $isDuplicate,
                    'message' => LeadService::AUTO_REPLY_MESSAGE,
                    'lead' => $leadPayload,
                ]);
            }

            $redirectUrl = url()->previous() ?: route('contact.index');
            if (!str_contains($redirectUrl, '#')) {
                $redirectUrl .= '#lead-quote-section';
            }

            return redirect($redirectUrl)
                ->with('messageSuccess', LeadService::AUTO_REPLY_MESSAGE)
                ->with('leadSuccessData', $isDuplicate ? null : $leadPayload);
        } catch (\Throwable $th) {
            Log::error('Lead submission failed: ' . $th->getMessage(), ['exception' => $th]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Hệ thống đang bận, vui lòng thử lại hoặc gọi trực tiếp Hotline 0968945633.',
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput($request->except(['hp_company_fax', '_form_ts', '_form_token', 'g_recaptcha_response']))
                ->with('messageError', 'Hệ thống đang bận, vui lòng thử lại hoặc gọi Hotline 0968945633.');
        }
    }

    public function getSolutionList()
    {
        $solution = Solution::where('status', 1)->orderBy('id', 'desc')->paginate(15);
        $products = Product::where('status', 1)->where('del_flg', 1)->with('categories')->inRandomOrder()->limit(3)->get();

        $seo = [
            'meta_title' => 'Giải pháp',
            'meta_keyword' => '',
            'meta_description' => 'Giải pháp',
            'meta_image' => '',
            'canonical' => config('app.url').'/giai-phap',
        ];

        return view('user.solution.list', ['solutions' => $solution, 'products' => $products, 'seo' => $seo]);
    }

    public function getSolutionDetail($solutionSlug)
    {
        $solution = Solution::where([['status', 1], ['slug', $solutionSlug]])->first();
        if (!$solution) {
            return redirect()->route('index');
        }

        $newestPosts = Solution::where([['status', 1], ['id', '!=', $solution->id]])->orderBy('updated_at', 'desc')->limit(5)->get();
        $products = Product::where('status', 1)->where('del_flg', 1)->with('categories')->inRandomOrder()->limit(3)->get();

        $seo = [
            'meta_title' => $solution->name,
            'meta_keyword' => '',
            'meta_description' => htmlspecialchars_decode((html_entity_decode(trim(
                preg_replace(
                    '/\s+/', // xoá tất cả khoảng trắng dư, kể cả \n \r \t
                    ' ',
                    cutnchar(strip_tags($solution->content), 300)
                )
            )))),
            'meta_image' => '',
            'canonical' =>  route('solution.detail', ['solutionSlug' => $solution->slug]),
        ];

        $image = $solution->image;

        $name = $solution->name;

        $description = htmlspecialchars_decode(html_entity_decode(strip_tags($solution->content)));

        $canonical = route('solution.detail', ['solutionSlug' => $solution->slug]);

        $itemBreadcrumbElements = '';

        $positionBreadcrumb = 2;

        $name = 'Giải pháp';

        $catCanonical = config('app.url').'giai-phap';

        $jsonLdData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Trang chủ',
                        'item' => config('app.url'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => 'Giải pháp',
                        'item' => $catCanonical,
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $solution->name,
                        'item' => $canonical,
                    ],
                ],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $solution->name,
                'description' => $description,
                'image' => $image,
                'url' => $canonical,
                'datePublished' => convertDateTime($solution->created_at, 'Y-m-d'),
                'dateModified' => convertDateTime($solution->updated_at ?? $solution->created_at, 'Y-m-d'),
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'DATYSO',
                    'url' => config('app.url'),
                ],
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => $canonical,
                ],
            ],
        ];

        $schema = "<script type=\"application/ld+json\">\n"
            . json_encode($jsonLdData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n"
            . "</script>";

        return view('user.solution.detail', ['solution' => $solution, 'newestPosts' => $newestPosts, 'products' => $products, 'schema' => $schema, 'seo' => $seo]);
    }

    public function getRecruitmentList(Request $request)
    {
        $type = $request->input('type');
        $location = $request->input('location');
        $query = Recruitment::query();
    
        if ($type && $type != '0') {
            $query->where('type', $type);
        }
    
        if ($location && $location != '0') {
            $query->where('location', $location);
        }
        $items = $query->where('status', 1)->orderBy('id', 'desc')->paginate(15);
    
        $filterData = Recruitment::select(['type', 'location'])->where('status', 1)->get();
        $typeArray = [];
        $locationArray = [];
        foreach ($filterData as $item) {
            $typeItems = is_array($item->type) ? $item->type : explode(',', $item->type);
            $locationItems = is_array($item->location) ? $item->location : explode(',', $item->location);
    
            $typeArray = array_merge($typeArray, $typeItems);
            $locationArray = array_merge($locationArray, $locationItems);
        }     
        $typeArray = array_map(function($value) {
            return ucwords(trim(strtolower($value)));
        }, $typeArray);
    
        $locationArray = array_map(function($value) {
            return ucwords(trim(strtolower($value)));
        }, $locationArray);
    
        $typeArray = array_values(array_unique($typeArray));
        $locationArray = array_values(array_unique($locationArray));
    
        $products = Product::where('status', 1)->where('del_flg', 1)->with('categories')->inRandomOrder()->limit(3)->get();
        return view('user.recruitment.list', [
            'items' => $items, 
            'products' => $products,
            'types' => $typeArray,
            'locations' => $locationArray,
            'selectedType' => $type,
            'selectedLocation' => $location
        ]);
    }
    

    public function postUserProfileForm(UserProfileRequest $request)
    {
        try {
            DB::beginTransaction();
            $item = new UserProfile();
            $data = $request->all();

            if ($request->file('file')) {
                $file = $request->file('file');
                $newFileName = $file->getClientOriginalName() . '-' . rand(0, 999);
                $data['file'] = $file->storeAs('uploads', $newFileName, 'public');
            }

            $item->fill($data);
            $item->save();
            DB::commit();
            return redirect()->route('recruitment.index')->with('messageSuccess', config('message.send_profile_succsee'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('recruitment.index')->with('messageError', config('message.send_profile_error'));
        }
    }

    public function getRecruitmentDetail($recruitmentSlug)
    {
        $item = Recruitment::where([['status', 1], ['slug', $recruitmentSlug]])->first();
        if (!$item) {
            return redirect()->route('index');
        }

        $newestPosts = Recruitment::where([['status', 1], ['id', '!=', $item->id]])->orderBy('updated_at', 'desc')->limit(5)->get();
        $products = Product::where('status', 1)->with('categories')->inRandomOrder()->limit(3)->get();
        return view('user.recruitment.detail', ['item' => $item, 'newestPosts' => $newestPosts, 'products' => $products]);
    }

    public function search(Request $request) {
        $rawKeyword = $request->input('tu-khoa');
        if (! is_string($rawKeyword) || trim($rawKeyword) === '') {
            return redirect()->route('index');
        }
        $searchKey = trim($rawKeyword);
        $data = Product::where('name', 'LIKE', '%' . $searchKey . '%')
                ->with('categories')
                ->where('del_flg', 1)
                ->whereHas('categories')
                ->paginate(15);
        return view('user.search.list', ['products' => $data, 'key' => $searchKey]);
    }

    public function getLogin()
    {
        try {
            if (Auth::guard('user')->check()) {
                // if (Auth::guard('user')->user()->status == 2) {
                    return redirect()->route('index');
                // } else {
                //     Auth::guard('user')->logout();
                //     return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập');
                // }
            }
            return view('user.login');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('login')->with('messageError', config('message.create_error'))->withInput()->with('method', 'register');
        }
    }

    public function postLogin(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->route('login')->withErrors($validator)->withInput();
            }

            $validatedData = $validator->validated();

            if (Auth::guard('user')->attempt(['first_name' => $validatedData['first_name'], 'password' => $validatedData['password']])) {
                if (Auth::guard('user')->user()->status == 2) {
                    return redirect()->route('dashboard.index');
                } else {
                    Auth::guard('user')->logout();
                    return redirect()->route('login')->with('messageError', 'Bạn không có quyền truy cập');
                }
            }

            DB::commit();

            return redirect()->route('login')->with('messageError', config('message.login_failed'))->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->back()->with('messageError', config('message.create_error'))->withInput()->with('method', 'register');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(CreateRequest $request)
    {
        try {
            DB::beginTransaction();

            $userCustomer = new Customer();
            $userCustomer->email = $request->email;
            $userCustomer->fullname = $request->fullname;
            $userCustomer->phone = $request->phone;
            $userCustomer->password = bcrypt($request->register_password);
            $userCustomer->status = 1;
            $userCustomer->save();

            Auth::guard('user')->login($userCustomer);

            DB::commit();
            return redirect()->route('index');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->back()->with('messageError', config('message.create_error'))->withInput()->with('method', 'register');
        }
    }

    private function generateSignedUrls($items, $imageColumn)
    {
        foreach ($items as $item) {
            if ($item->$imageColumn) {
                $item->$imageColumn = getImageUrl($item->$imageColumn);
            }
        }
    }

    /**
     * Tìm danh mục đang hoạt động theo slug đã lọc bỏ tham số truy vấn lạ.
     */
    private function findActiveCategory(?string $categorySlug): ?Category
    {
        $cleanSlug = $this->sanitizeSlug($categorySlug);
        if ($cleanSlug === null) {
            return null;
        }

        return Category::where([['slug', $cleanSlug], ['status', 1]])->first();
    }

    /**
     * Tìm sản phẩm đang hoạt động kèm ảnh theo slug đã lọc bỏ tham số truy vấn lạ.
     */
    private function findActiveProduct(string $productSlug): ?Product
    {
        $cleanSlug = $this->sanitizeSlug($productSlug);
        if ($cleanSlug === null) {
            return null;
        }

        return Product::with(['images'])->where([['slug', $cleanSlug], ['status', 1]])->first();
    }

    /**
     * Xác thực sản phẩm thuộc danh mục hiện tại hoặc 301 về danh mục con chuẩn (giữ nguyên query tracking).
     */
    private function resolveProductDetailRedirect(Category $category, Product $product, Request $request): ?\Illuminate\Http\RedirectResponse
    {
        $belongsToCategory = $category->products()->where('m_products.id', $product->id)->exists();
        if ($belongsToCategory) {
            return null;
        }

        // Kiểm tra xem sản phẩm có thuộc danh mục con của danh mục này không.
        $categoryIds = $category->getAllChildrenIds();
        $childCategory = $product->categories()
            ->whereIn('m_category.id', $categoryIds)
            ->where('m_category.status', 1)
            ->first();

        if ($childCategory) {
            // 301 Redirect về URL chuẩn của danh mục con và giữ lại query string (UTM/gclid) để không mất đo lường.
            $targetUrl = route('product.detail', [
                'categorySlug' => $childCategory->slug,
                'productSlug' => $product->slug,
            ]);
            $queryString = $request->getQueryString();
            if (is_string($queryString) && $queryString !== '') {
                $targetUrl .= '?' . $queryString;
            }

            return redirect()->to($targetUrl, 301);
        }

        return redirect()->route('index');
    }

    /**
     * Chuẩn hóa slug route và loại bỏ phần query/fragment nếu xuất hiện ngoài ý muốn.
     */
    private function sanitizeSlug(?string $slug): ?string
    {
        if (! is_string($slug)) {
            return null;
        }

        $clean = explode('?', explode('#', trim($slug))[0])[0];
        $clean = trim($clean, '/');

        return $clean !== '' ? $clean : null;
    }
}

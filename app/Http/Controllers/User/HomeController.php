<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\ContactRequest;
use App\Http\Requests\Customer\CreateRequest;
use App\Http\Requests\UserProfile\UserProfileRequest;
use App\Mail\ContactMail;
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
        return convert_array(System::get(), 'keyword', 'content');
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
                $category->image = url("storage/images/$category->image");
            }
        }
        // $system = 
        $seo = [
            'meta_title' => $this->system['seo_meta_title'],
            'meta_keyword' => $this->system['seo_meta_keyword'],
            'meta_description' => $this->system['seo_meta_description'],
            'meta_image' => getImageUrl($this->system['seo_meta_images']),
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
            $category = Category::where([['slug', $categorySlug], ['status', 1]])->first();
        }
        if (!$category) {
            return redirect()->route('index');
        }
        $category->banner = getImageUrl($category->banner);

        $products = $category->products()->with(['images'])->where('del_flg', 1)->paginate(15);

        $seo = [
            'meta_title' => $category->name,
            'meta_keyword' => '',
            'meta_description' => $category->name,
            'meta_image' => getImageUrl($category->image),
            'canonical' => route('product.list', ['categorySlug' => $category->slug]),
        ];

        $cat_name = $category->name;
        $cat_canonical = route('product.list', ['categorySlug' => $category->slug]);
        $cat_description = $category->name;
        $totalProducts = $products->total();
        $itemListElements = '';
        $position = 1;

        foreach ($products as $product) {
            $image = getImageUrl($product->images->first()->image);
            $name = $product->name;
            $canonical = route('product.detail', ['categorySlug' => $category->slug, 'productSlug' => $product->slug]);
            $itemListElements .= "
                {
                    \"@type\": \"ListItem\",
                    \"position\": $position,
                    \"item\": {
                        \"@type\": \"Product\",
                        \"name\": \"" . $name . "\",
                        \"url\": \"" . $canonical .  "\",
                        \"image\": \"" . $image .  "\"
                    }
                },";
            $position++;
        }

        $itemListElements = rtrim($itemListElements, ',');
        $itemBreadcrumbElements = '';
        $positionBreadcrumb = 2;
        $itemBreadcrumbElements .= "
            {
                \"@type\": \"ListItem\",
                \"position\": $positionBreadcrumb,
                \"name\": \"" . $cat_name . "\",
                \"item\": \"" . $cat_canonical . "\",
            },";

        $itemBreadcrumbElements = rtrim($itemBreadcrumbElements, ',');

        $schema = "<script type='application/ld+json'>
            {
                \"@type\": \"BreadcrumbList\",
                \"itemListElement\": [
                    {
                        \"@type\": \"ListItem\",
                        \"position\": 1,
                        \"name\": \" Trang chủ  \",
                        \"item\": \" ". config('app.url') . " \"
                    },
                    $itemBreadcrumbElements
                ]
            },
            {
                \"@context\": \"https://schema.org\",
                \"@type\": \"CollectionPage\",
                \"name\": \"" . $cat_name . "\",
                \"description\": \" " . $cat_description . " \",
                \"url\": \"" . $cat_canonical . "\",
                \"mainEntity\": {
                    \"@type\": \"ItemList\",
                    \"name\": \" " .$cat_name. " \",
                    \"numberOfItems\": $totalProducts,
                    \"itemListElement\": [
                        $itemListElements
                    ]
                }
            }
            </script>";

        return view('user.product.list', ['category' => $category, 'products' => $products, 'seo' => $seo, 'schema' => $schema]);
    }

    public function getProductDetail($categorySlug, $productSlug)
    {
        $category = Category::where([['slug', $categorySlug], ['status', 1]])->first();
        if (!$category) {
            return redirect()->route('index');
        }

        $product = Product::with(['images'])->where([['slug', $productSlug], ['status', 1]])->first();
        if (!$product) {
            return redirect()->route('index');
        }
        $productImage = $product->images->first()->image;

        $checkProduct = $category->products()->where('m_products.id', $product->id)->first();
        if (!$checkProduct) {
            return redirect()->route('index');
        }

        $otherProducts = Product::where([['status', 1], ['id', '!=', $product->id]])->inRandomOrder()->limit(3)->get();

        $seo = [
            'meta_title' => $product->name,
            'meta_keyword' => '',
            'meta_description' => htmlspecialchars_decode((html_entity_decode(trim(
                preg_replace(
                    '/\s+/', // xoá tất cả khoảng trắng dư, kể cả \n \r \t
                    ' ',
                    cutnchar(strip_tags($product->description), 300)
                )
            )))),
            'meta_image' => getImageUrl($productImage),
            'canonical' => route('product.detail', ['categorySlug' => $category->slug, 'productSlug' => $product->slug]),
        ];

        $image = $productImage;
        $name = $product->name;
        $description = htmlspecialchars_decode(html_entity_decode(strip_tags($product->description)));
        $cat_name = $category->name;
        $cat_canonical = route('product.list', ['categorySlug' => $category->slug]);

        $itemBreadcrumbElements = '';

        $positionBreadcrumb = 2;

        $itemListElements = '';

        $itemListElements = rtrim($itemListElements, ',');
        $itemBreadcrumbElements = '';
        $positionBreadcrumb = 2;
        $itemBreadcrumbElements .= "
            {
                \"@type\": \"ListItem\",
                \"position\": $positionBreadcrumb,
                \"name\": \"" . $cat_name . "\",
                \"item\": \"" . $cat_canonical . "\",
            },";

        $itemBreadcrumbElements = rtrim($itemBreadcrumbElements, ',');

        $schema = "
            <script type=\"application/ld+json\">
                {
                    \"@type\": \"BreadcrumbList\",
                    \"itemListElement\": [
                        {
                            \"@type\": \"ListItem\",
                            \"position\": 1,
                            \"name\": \" Trang chủ  \",
                            \"item\": \" ". config('app.url') . " \"
                        },
                        $itemBreadcrumbElements
                    ]
                },
                {
                    \"@context\": \"https://schema.org\",
                    \"@type\": \"Product\",
                    \"name\": \" " . $name .  " \",
                    \"description\": \"  " . $description .  "  \",
                    \"image\": \"  " . $image .  "  \",
                    \"brand\": {
                        \"@type\": \"Brand\",
                        \"name\": \"An Hưng\"
                    },
                    \"manufacturer\": {
                        \"@type\": \"Organization\",
                        \"name\": \"An Hưng\",
                        \"url\": \" " . config('app.url') . "\"
                    },
                    \"material\": \" " .$cat_name. " \",
                    \"category\": \" " .$cat_canonical. " \",
                    \"sku\": \"\",
                    \"mpn\": \"\",
                    \"offers\": {
                        \"@type\": \"Offer\",
                        \"price\": \"\",
                        \"priceCurrency\": \"\",
                        \"availability\": \"\",
                        \"seller\": {
                            \"@type\": \"Organization\",
                            \"name\": \"An Hưng\"
                        },
                        \"priceValidUntil\": \"\",
                        \"itemCondition\": \"https://schema.org/NewCondition\"
                    },
                }
            </script>
        ";

        $system = $this->system;
        return view('user.product.detail', ['system' => $system, 'category' => $category, 'product' => $product, 'otherProducts' => $otherProducts, 'seo' => $seo, 'schema' => $schema]);
    }

    public function getInstructionList()
    {
        $instructions = Instruction::where('status', 1)->orderBy('id', 'desc')->paginate(15);
        $products = Product::where('status', 1)->where('del_flg', 1)->with('categories')->inRandomOrder()->limit(3)->get();
        foreach($instructions as $instruction) {
            $instruction->image = url('storage/images') . '/' . $instruction->image;
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
        $instruction->image = url('storage/images') . '/' . $instruction->image;

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

        $itemBreadcrumbElements .= "
            {
                \"@type\": \"ListItem\",
                \"position\": $positionBreadcrumb,
                \"name\": \"" . $name . "\",
                \"item\": \"" . $canonical . "\",
            },";

        $itemBreadcrumbElements = rtrim($itemBreadcrumbElements, ',');

        $schema = "
            <script type=\"application/ld+json\">
                {
                    \"@type\": \"BreadcrumbList\",
                    \"itemListElement\": [
                        {
                            \"@type\": \"ListItem\",
                            \"position\": 1,
                            \"name\": \" Trang chủ  \",
                            \"item\": \" ". config('app.url') . " \"
                        },
                        $itemBreadcrumbElements
                    ]
                },
                {
                    \"@context\": \"https://schema.org\",
                    \"@type\": \"BlogPosting\",
                    \"headline\": \" " . $name .  " \",
                    \"description\": \"  " . $description .  "  \",
                    \"image\": \"  " . $image .  "  \",
                    \"url\": \"  " . $canonical .  "  \",
                    \"datePublished\": \"  " . convertDateTime($instruction->created_at, 'd-m-y') .  "  \",
                    \"dateModified\": \"  " . convertDateTime($instruction->created_at, 'd-m-y') .  "  \",
                    \"author\": [
                        \"@type\": \"Person\",
                        \"name\": \"\",
                        \"url\": \"\",
                    ],
                    \"publisher\": [
                        \"@type\": \"Organization\",
                        \"name\": \" An Hưng  \",
                        \"logo\": [
                            \"@type\": \"ImageObject\",
                            \"url\": \"   \",
                        ],
                    ],
                    \"mainEntityOfPage\": [
                        \"@type\": \"Organization\",
                        \"@id\": \" " . $canonical . " \",
                    ],
                    \"articleSection\": \"  " . $tintuc = 'Tin tức' .  "  \",
                    \" keywords \": \"  \",
                    \" timeRequired \": \"  \",
                    \"about\": [
                        \"@type\": \"Thing\",
                        \"name\": \" \",
                    ],
                    \"mentions\": [
                        {
                            \"@type\": \"Product\",
                            \"name\": \" \",
                        }
                    ],
                }
            </script>
        ";


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

        $itemBreadcrumbElements .= "
            {
                \"@type\": \"ListItem\",
                \"position\": $positionBreadcrumb,
                \"name\": \"" . $name . "\",
                \"item\": \"" . $canonical . "\",
            },";

        $itemBreadcrumbElements = rtrim($itemBreadcrumbElements, ',');

        $schema = "
            <script type=\"application/ld+json\">
                {
                    \"@type\": \"BreadcrumbList\",
                    \"itemListElement\": [
                        {
                            \"@type\": \"ListItem\",
                            \"position\": 1,
                            \"name\": \" Trang chủ  \",
                            \"item\": \" ". config('app.url') . " \"
                        },
                        $itemBreadcrumbElements
                    ]
                },
                {
                    \"@context\": \"https://schema.org\",
                    \"@type\": \"BlogPosting\",
                    \"headline\": \" " . $name .  " \",
                    \"description\": \"  " . $description .  "  \",
                    \"image\": \"  " . $image .  "  \",
                    \"url\": \"  " . $canonical .  "  \",
                    \"datePublished\": \"  " . convertDateTime($post->created_at, 'd-m-y') .  "  \",
                    \"dateModified\": \"  " . convertDateTime($post->created_at, 'd-m-y') .  "  \",
                    \"author\": [
                        \"@type\": \"Person\",
                        \"name\": \"\",
                        \"url\": \"\",
                    ],
                    \"publisher\": [
                        \"@type\": \"Organization\",
                        \"name\": \" An Hưng  \",
                        \"logo\": [
                            \"@type\": \"ImageObject\",
                            \"url\": \"   \",
                        ],
                    ],
                    \"mainEntityOfPage\": [
                        \"@type\": \"Organization\",
                        \"@id\": \" " . $canonical . " \",
                    ],
                    \"articleSection\": \"  " . $tintuc = 'Tin tức' .  "  \",
                    \" keywords \": \"  \",
                    \" timeRequired \": \"  \",
                    \"about\": [
                        \"@type\": \"Thing\",
                        \"name\": \" \",
                    ],
                    \"mentions\": [
                        {
                            \"@type\": \"Product\",
                            \"name\": \" \",
                        }
                    ],
                }
            </script>
        ";
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
        if ($request->has('san-pham')) {
            $product = Product::where([['slug', $request->input('san-pham')], ['status', 1]])->first();
            if (!$product) $product = null;
        }
        return view('user.contact.form', ['product' => $product]);
    }

    public function postContactForm(ContactRequest $request)
    {
        try {
            DB::beginTransaction();
            $contact = new Contact();
            $contact->fill($request->all());
            $contact->save();
            DB::commit();

            Mail::to(env('ADMIN_EMAIL'))->send(new ContactMail($contact));
            return redirect()->route('contact.index')->with('messageSuccess', config('message.contact_success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            return redirect()->route('contact.index')->with('messageError', config('message.contact_failed'));
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

        $itemBreadcrumbElements .= "
            {
                \"@type\": \"ListItem\",
                \"position\": $positionBreadcrumb,
                \"name\": \"" . $name . "\",
                \"item\": \"" . $canonical . "\",
            },";

        $itemBreadcrumbElements = rtrim($itemBreadcrumbElements, ',');

        $schema = "
            <script type=\"application/ld+json\">
                {
                    \"@type\": \"BreadcrumbList\",
                    \"itemListElement\": [
                        {
                            \"@type\": \"ListItem\",
                            \"position\": 1,
                            \"name\": \" Trang chủ  \",
                            \"item\": \" ". config('app.url') . " \"
                        },
                        $itemBreadcrumbElements
                    ]
                },
                {
                    \"@context\": \"https://schema.org\",
                    \"@type\": \"BlogPosting\",
                    \"headline\": \" " . $name .  " \",
                    \"description\": \"  " . $description .  "  \",
                    \"image\": \"  " . $image .  "  \",
                    \"url\": \"  " . $canonical .  "  \",
                    \"datePublished\": \"  " . convertDateTime($solution->created_at, 'd-m-y') .  "  \",
                    \"dateModified\": \"  " . convertDateTime($solution->created_at, 'd-m-y') .  "  \",
                    \"author\": [
                        \"@type\": \"Person\",
                        \"name\": \"\",
                        \"url\": \"\",
                    ],
                    \"publisher\": [
                        \"@type\": \"Organization\",
                        \"name\": \" An Hưng  \",
                        \"logo\": [
                            \"@type\": \"ImageObject\",
                            \"url\": \"   \",
                        ],
                    ],
                    \"mainEntityOfPage\": [
                        \"@type\": \"Organization\",
                        \"@id\": \" " . $canonical . " \",
                    ],
                    \"articleSection\": \"  " . $tintuc = 'Tin tức' .  "  \",
                    \" keywords \": \"  \",
                    \" timeRequired \": \"  \",
                    \"about\": [
                        \"@type\": \"Thing\",
                        \"name\": \" \",
                    ],
                    \"mentions\": [
                        {
                            \"@type\": \"Product\",
                            \"name\": \" \",
                        }
                    ],
                }
            </script>
        ";

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
        if (!isset($request['tu-khoa']) || empty($request['tu-khoa'])) return redirect()->route('index');
        $searchKey = $request['tu-khoa'];
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
                $item->$imageColumn = url("storage/images") . "/" . $item->$imageColumn;
            }
        }
    }
}

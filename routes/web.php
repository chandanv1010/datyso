<?php

use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InstructionController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RecruitmentController;
use App\Http\Controllers\Admin\ResetPassWordController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\SocialController;
use App\Http\Controllers\Admin\SolutionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\User\HomeController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Views;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ajax\CartController;
use App\Http\Controllers\User\CartController as FeCartController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SystemController;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {

    Route::get('login', [DashboardController::class, 'getLogin'])->name('login');
    Route::post('login', [DashboardController::class, 'postLogin']);
    Route::get('logout', [DashboardController::class, 'getLogOut'])->name('logout');

    Route::controller(ResetPassWordController::class)->prefix('resetPassword')->name('resetPassword.')->group(function () {
        Route::get('tao-moi', 'getForgotPassword')->name('create');
        Route::post('tao-moi', 'postForgotPassword');
        Route::get('reset-password/{token}', 'showResetPasswordForm')->name('getCreatePass');
        Route::post('reset-password', 'submitResetPasswordForm')->name('reset-password');
    });

    Route::middleware([Authenticate::class])->group(function () {

        Route::controller(DashboardController::class)->name('dashboard.')->group(function () {
            Route::get('/', 'index')->name('index');
        });
        Route::controller(UserController::class)->prefix('user')->name('user.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('tao-moi', 'create')->name('create');
            Route::post('tao-moi', 'store');
            Route::get('cap-nhap/{id}', 'edit')->name('update');
            Route::post('cap-nhap/{id}', 'update');
            Route::post('xoa/{id}', 'destroy')->name('delete');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
        });

        Route::controller(CategoryController::class)->prefix('category')->name('category.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('tao-moi', 'create')->name('create');
            Route::post('tao-moi', 'store');
            Route::get('cap-nhap/{id}', 'edit')->name('update');
            Route::post('cap-nhap/{id}', 'update');
            Route::post('xoa/{id}', 'destroy')->name('delete');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
        });

        Route::controller(SocialController::class)->prefix('social')->name('social.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('tao-moi', 'create')->name('create');
            Route::post('tao-moi', 'store');
            Route::get('cap-nhap/{id}', 'edit')->name('update');
            Route::post('cap-nhap/{id}', 'update');
            Route::post('xoa/{id}', 'destroy')->name('delete');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
        });

        Route::controller(PostController::class)->prefix('post')->name('post.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('tao-moi', 'create')->name('create');
            Route::post('tao-moi', 'store');
            Route::get('cap-nhap/{id}', 'edit')->name('update');
            Route::post('cap-nhap/{id}', 'update');
            Route::post('xoa/{id}', 'destroy')->name('delete');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
        });

        Route::controller(InstructionController::class)->prefix('instruction')->name('instruction.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('tao-moi', 'create')->name('create');
            Route::post('tao-moi', 'store');
            Route::get('cap-nhap/{id}', 'edit')->name('update');
            Route::post('cap-nhap/{id}', 'update');
            Route::post('xoa/{id}', 'destroy')->name('delete');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
        });

        Route::controller(ContactController::class)->prefix('contact')->name('contact.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('tao-moi', 'create')->name('create');
            Route::post('tao-moi', 'store')->name('store');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
            Route::post('kiem-duyet/{id}', 'postSupported')->name('supported');
        });

        Route::controller(AddressController::class)->prefix('address')->name('address.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('tao-moi', 'create')->name('create');
            Route::post('tao-moi', 'store');
            Route::get('cap-nhap/{id}', 'edit')->name('update');
            Route::post('cap-nhap/{id}', 'update');
            Route::post('xoa/{id}', 'destroy')->name('delete');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
        });

        Route::controller(CustomerController::class)->prefix('customer')->name('customer.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('cap-nhap/{id}', 'edit')->name('update');
            Route::post('cap-nhap/{id}', 'update');
            Route::post('xoa/{id}', 'destroy')->name('delete');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
            Route::post('kiem-duyet/{id}', 'postSupported')->name('supported');
        });

        Route::controller(SlideController::class)->prefix('slide')->name('slide.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('dang-ki', 'getCreate')->name('create');
            Route::post('dang-ki-slide', 'postCreate')->name('createSlide');
            Route::get('/get-image', 'getUrl')->name('getImage');
        });

        Route::controller(AddressController::class)->prefix('address')->name('address.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('tao-moi', 'create')->name('create');
            Route::post('tao-moi', 'store');
            Route::get('cap-nhap/{id}', 'edit')->name('update');
            Route::post('cap-nhap/{id}', 'update');
            Route::post('xoa/{id}', 'destroy')->name('delete');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
        });

        Route::controller(ProductController::class)->prefix('product')->name('product.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('tao-moi', 'create')->name('create');
            Route::post('tao-moi', 'store');
            Route::get('cap-nhap/{id}', 'edit')->name('update');
            Route::post('cap-nhap/{id}', 'update');
            Route::post('xoa/{id}', 'destroy')->name('delete');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
        });

        Route::controller(OrderController::class)->prefix('order')->name('order.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('xoa/{id}', 'destroy')->name('delete');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
        });

        Route::controller(SystemController::class)->prefix('system')->name('system.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('store', 'store')->name('store');
        });

        Route::controller(SolutionController::class)->prefix('solution')->name('solution.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('tao-moi', 'create')->name('create');
            Route::post('tao-moi', 'store');
            Route::get('cap-nhap/{id}', 'edit')->name('update');
            Route::post('cap-nhap/{id}', 'update');
            Route::post('xoa/{id}', 'destroy')->name('delete');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
        });

        Route::controller(RecruitmentController::class)->prefix('recruitment')->name('recruitment.')->group(function () {
            Route::get('dang-tuyen', 'index')->name('index');
            Route::get('tao-moi', 'create')->name('create');
            Route::post('tao-moi', 'store');
            Route::get('cap-nhap/{id}', 'edit')->name('update');
            Route::post('cap-nhap/{id}', 'update');
            Route::post('xoa/{id}', 'destroy')->name('delete');
            Route::post('trang-thai/{id}', 'postStatus')->name('status');
            Route::get('don-ung-tuyen', 'singleApply')->name('apply');
            Route::post('trang-thai-apply/{id}', 'postStatusAppLy')->name('statusApply');
            Route::get('/user_profiles/{id}', 'showFileApply')->name('show');
        });
    });
});

Route::middleware([Views::class])->group(function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('login', 'getLogin')->name('login');
        Route::post('login', 'postLogin');
        Route::get('logout', 'getLogOut')->name('logout');
    
        Route::get('/', 'index')->name('index');
    
        Route::get('/tim-kiem', 'search')->name('search');
    
        Route::prefix('san-pham')->name('product.')->group(function () {
            Route::get('/{categorySlug}/', 'getProductList')->name('list');
            Route::get('/{categorySlug}/chi-tiet/{productSlug}', 'getProductDetail')->name('detail');
        });
    
        Route::prefix('chinh-sach')->name('instruction.')->group(function () {
            Route::get('/', 'getInstructionList')->name('index');
            Route::get('/{instructionSlug}/chi-tiet', 'getInstructionDetail')->name('detail');
        });
    
        Route::prefix('tin-tuc')->name('post.')->group(function () {
            Route::get('/', 'getPostList')->name('index');
            Route::get('/{postSlug}/chi-tiet', 'getPostDetail')->name('detail');
        });
    
        Route::prefix('gioi-thieu')->name('introduce.')->group(function () {
            Route::get('/', 'getIntroduce')->name('index');
        });
    
        Route::prefix('lien-he')->name('contact.')->group(function () {
            Route::get('/', 'getContact')->name('index');
            Route::post('/register', 'postContactForm')->name('register');
        });
    
        Route::prefix('giai-phap')->name('solution.')->group(function () {
            Route::get('/', 'getSolutionList')->name('index');
            Route::get('/{solutionSlug}/chi-tiet', 'getSolutionDetail')->name('detail');
        });
    
        Route::prefix('tuyen-dung')->name('recruitment.')->group(function () {
            Route::get('/', 'getRecruitmentList')->name('index');
            Route::get('/{recruitmentSlug}/chi-tiet', 'getRecruitmentDetail')->name('detail');
            Route::post('/register', 'postUserProfileForm')->name('register');
        });
    });
});

Route::post('register', [HomeController::class, 'register'])->name('register');
Route::post('/ajax/cart/addToCart', [CartController::class, 'create'])->name('cart.create');
Route::post('/ajax/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/ajax/cart/delete', [CartController::class, 'delete'])->name('cart.delete');
Route::get('gio-hang', [FeCartController::class, 'index'])->name('cart.index');
Route::post('checkout', [FeCartController::class, 'checkout'])->name('cart.checkout');
Route::get('dat-hang-thanh-cong/{id}', [FeCartController::class, 'success'])->name('cart.success');
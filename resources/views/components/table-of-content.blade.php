<div class="table-of-contents border rounded-md p-3 shadow-sm bg-white">
    <div class="uk-flex uk-flex-space-between uk-flex-middle" style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #ccc;">
        <p class="font-bold fw-bold m-0 fs-5" style="font-size: 1.1rem; margin: 0; color: #000;">Mục lục</p>
        <button id="toggle-toc" class="text-blue-600 text-sm hover:underline" style="background: transparent;border:0;">Ẩn</button>
    </div>

    <div id="toc-content-wrapper">
        {!! \App\View\Components\TableOfContent::buildTree($items) !!}
    </div>
</div>

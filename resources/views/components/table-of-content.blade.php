<div class="table-of-contents border rounded-md p-3 shadow-sm bg-white">
    <div class="uk-flex uk-flex-space-between uk-flex-middle" style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #ccc;">
        <h3 class="font-bold m-0">Mục lục</h3>
        <button id="toggle-toc" class="text-blue-600 text-sm hover:underline" style="background: transparent;border:0;">Ẩn</button>
    </div>

    <div id="toc-content-wrapper">
        {!! \App\View\Components\TableOfContent::buildTree($items) !!}
    </div>
</div>

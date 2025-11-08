<div class="table-of-contents border rounded-md p-3 shadow-sm bg-white">
    <div class="uk-flex uk-flex-space-between uk-flex-middle" style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #ccc;">
        <h3 class="font-bold m-0">Mục lục</h3>
        <button id="toggle-toc" class="text-blue-600 text-sm hover:underline" style="background: transparent;border:0;">Ẩn</button>
    </div>

    {!! \App\View\Components\TableOfContent::buildTree($items) !!}
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const btn = document.getElementById("toggle-toc");
    const lists = document.querySelectorAll(".toc-list");

    btn.addEventListener("click", function() {
        lists.forEach(list => {
            list.style.display = list.style.display === "none" ? "block" : "none";
        });
        btn.textContent = (btn.textContent === "Ẩn") ? "Hiện" : "Ẩn";
    });

    // Smooth scroll (jQuery)
    $('a.toc-link').on('click', function(e) {
        e.preventDefault();
        let target = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(target).offset().top - 20
        }, 500);
    });
});
</script>

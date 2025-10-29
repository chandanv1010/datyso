$(document).ready(() => {

    // NAVBAR
    // $('.product-menu').hover(function () {
    //     $('.item-menu').stop(true, true).show();
    // }, function () {
    //     $('.item-menu').stop(true, true).hide();
    // });

    // $('.item-menu').hover(function () {
    //     $(this).stop(true, true).show();
    // }, function () {
    //     $(this).stop(true, true).hide();
    // });

    // Enable Bootstrap Tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    // Filter button
    // Bao hanh filter
    $("#bao-hanh-filter-btn").on("mouseenter", () => {
        var bao_hanh_filter_list = document.getElementById("bao-hanh-filter-list")
        bao_hanh_filter_list.style.display = "block"
    })
    $("#bao-hanh-filter-btn").on("mouseleave", () => {
        var bao_hanh_filter_list = document.getElementById("bao-hanh-filter-list")
        bao_hanh_filter_list.style.display = "none"
    })
    // Khoi luong filter
    $("#khoi-luong-filter-btn").on("mouseenter", () => {
        var bao_hanh_filter_list = document.getElementById("khoi-luong-filter-list")
        bao_hanh_filter_list.style.display = "block"
    })
    $("#khoi-luong-filter-btn").on("mouseleave", () => {
        var bao_hanh_filter_list = document.getElementById("khoi-luong-filter-list")
        bao_hanh_filter_list.style.display = "none"
    })
    // Nguon dien filter
    $("#nguon-dien-filter-btn").on("mouseenter", () => {
        var bao_hanh_filter_list = document.getElementById("nguon-dien-filter-list")
        bao_hanh_filter_list.style.display = "block"
    })
    $("#nguon-dien-filter-btn").on("mouseleave", () => {
        var bao_hanh_filter_list = document.getElementById("nguon-dien-filter-list")
        bao_hanh_filter_list.style.display = "none"
    })
    // Thuong hieu filter
    $("#thuong-hieu-filter-btn").on("mouseenter", () => {
        var bao_hanh_filter_list = document.getElementById("thuong-hieu-filter-list")
        bao_hanh_filter_list.style.display = "block"
    })
    $("#thuong-hieu-filter-btn").on("mouseleave", () => {
        var bao_hanh_filter_list = document.getElementById("thuong-hieu-filter-list")
        bao_hanh_filter_list.style.display = "none"
    })
    $("input[type='checkbox']").on("click", (target) => {
        var currentClickCheckbox = target.currentTarget
        var allCheckboxed = document.querySelectorAll("input[type='checkbox']")

        for (let i = 0; i < allCheckboxed.length; i++) {
            allCheckboxed[i].checked = false
        }

        console.log(currentClickCheckbox.parentNode.parentNode);
        console.log(currentClickCheckbox.getAttribute("data-is-checked"));

        if (currentClickCheckbox.parentNode.parentNode.id == "bao-hanh-filter-list") {
            if (currentClickCheckbox.getAttribute("data-is-checked") == "false") {
                currentClickCheckbox.checked = true
                document.getElementById("bao-hanh-checked-value").innerHTML = currentClickCheckbox.parentNode.childNodes[3].textContent
                currentClickCheckbox.setAttribute("data-is-checked", "true")
                console.log(currentClickCheckbox.parentNode.childNodes[3].textContent);
                return;
            }
            if (currentClickCheckbox.getAttribute("data-is-checked") == "true") {
                currentClickCheckbox.checked = false
                document.getElementById("bao-hanh-checked-value").innerHTML = ""
                currentClickCheckbox.setAttribute("data-is-checked", "false")

            }
        }

        if (currentClickCheckbox.parentNode.parentNode.id == "khoi-luong-filter-list") {
            if (currentClickCheckbox.getAttribute("data-is-checked") == "false") {
                currentClickCheckbox.checked = true
                document.getElementById("khoi-luong-checked-value").innerHTML = currentClickCheckbox.parentNode.childNodes[3].textContent
                currentClickCheckbox.setAttribute("data-is-checked", "true")
                return;
            }
            if (currentClickCheckbox.getAttribute("data-is-checked") == "true") {
                currentClickCheckbox.checked = false
                document.getElementById("khoi-luong-checked-value").innerHTML = ""
                currentClickCheckbox.setAttribute("data-is-checked", "false")
            }
        }

        if (currentClickCheckbox.parentNode.parentNode.id == "nguon-dien-filter-list") {
            if (currentClickCheckbox.getAttribute("data-is-checked") == "false") {
                currentClickCheckbox.checked = true
                document.getElementById("nguon-dien-checked-value").innerHTML = currentClickCheckbox.parentNode.childNodes[3].textContent
                currentClickCheckbox.setAttribute("data-is-checked", "true")
                return;
            }
            if (currentClickCheckbox.getAttribute("data-is-checked") == "true") {
                currentClickCheckbox.checked = false
                document.getElementById("nguon-dien-checked-value").innerHTML = ""
                currentClickCheckbox.setAttribute("data-is-checked", "false")
            }
        }

        if (currentClickCheckbox.parentNode.parentNode.id == "thuong-hieu-filter-list") {
            if (currentClickCheckbox.getAttribute("data-is-checked") == "false") {
                currentClickCheckbox.checked = true
                document.getElementById("thuong-hieu-checked-value").innerHTML = currentClickCheckbox.parentNode.childNodes[3].textContent
                currentClickCheckbox.setAttribute("data-is-checked", "true")
                return;
            }
            if (currentClickCheckbox.getAttribute("data-is-checked") == "true") {
                currentClickCheckbox.checked = false
                document.getElementById("thuong-hieu-checked-value").innerHTML = ""
                currentClickCheckbox.setAttribute("data-is-checked", "false")
            }
        }

    })


    // Change arrow icon in Danh Muc
    // var isMayHanDanhMucOpen = false;

    var biSelected = $(".bi-caret-fill-may-han");

    $.each(biSelected, function (index, item) {
        if ($(item).data('choice')) {
            $(item)[0].children[0].outerHTML = `<path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>`;
        } else {
            $(item)[0].children[0].outerHTML = `<path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"></path>`;
        }
    });

    $('.bi-caret-fill-may-han').on('click', function () {
        if ($(this).data('choice')) {
            $(this)[0].children[0].outerHTML = `<path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"></path>`;
            $(this).data('choice', false);
        } else {
            $(this)[0].children[0].outerHTML = `<path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>`;
            $(this).data('choice', true);
        }
    })
})

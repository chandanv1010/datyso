$(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#myTable')) {
        $('#myTable').DataTable().destroy();
    }

    var t = $('#myTable').DataTable({
        "ordering": false,
        "language": {
            "decimal": "",
            "emptyTable": "Không có dữ liệu",
            "info": "Hiển thị từ _START_ tới _END_ của _TOTAL_ dữ liệu",
            "infoEmpty": "Hiện tại đang có 0 dữ liệu",
            "infoFiltered": "(filtered from _MAX_ total entries)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Hiển thị _MENU_ dòng",
            "loadingRecords": "Đang tải...",
            "processing": "Đang xử lý...",
            "search": "Tìm kiếm :",
            "zeroRecords": "Không tìm thấy dữ liệu phù hợp",
            "paginate": {
                "first": "Đầu tiên",
                "last": "Cuối cùng",
                "next": "Sau",
                "previous": "Trước"
            }
        },
        columnDefs: [{
            "searchable": false,
            "orderable": false,
            "targets": 0
        }]
    });

    t.on('order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    }).draw();
});

$(document).on('click','.delete', function(event) {     
    event.preventDefault();
    let id = $(this).attr('data-value');
    $.confirm({
        title: 'Thông báo',
        content: 'Xác nhận xóa dữ liệu này?',
        type: 'red',
        buttons: {
            confirm: {
                text: 'Tiếp tục',
                btnClass: 'btn-danger' + ' mr-2',           
                action: function () {
                    $("#form-"+id).submit();                   
                },
            },        
            cancel: {
                text: 'Hủy',
                btnClass: 'btn-light',           
                action: function () {               
                },
            }, 
        }
    });   
});

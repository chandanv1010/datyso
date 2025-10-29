$(document).on('click','.isVerified', function(event) {     
    event.preventDefault();
    let id = $(this).attr('data-value');
    var title, text, btnClass, bgColor;
    if($('#isVerified-'+id).hasClass('active-item')) {
        title = 'Xác nhận tạm dừng hoạt động ?';
        text = 'Tạm dừng';
        btnClass = 'btn-danger';
        bgColor = 'red';
    } else {
        title = 'Xác nhận khôi phục hoạt động ?';
        text = 'Khôi phục';
        btnClass = 'btn-primary';
        bgColor = 'blue';
    }
    $.confirm({
        title: 'Thông báo',
        content: title,
        type: bgColor,
        buttons: {
            confirm: {
                text: text,
                btnClass: btnClass + ' mr-2',           
                action: function () {                    
                    if($('#isVerified-'+id).hasClass('active-item')) {
                        $('#isVerified-'+id).prop('checked', false);
                        $('#isVerified-'+id).removeClass('active-item');
                        $('#isVerified-'+id).addClass('deactive-item');                        
                    } else {
                        $('#isVerified-'+id).prop('checked', true);
                        $('#isVerified-'+id).removeClass('deactive-item');
                        $('#isVerified-'+id).addClass('active-item'); 
                    }
                    $("#form-isVerified-"+id).submit();                   
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

$(document).on('click','.isVerified', function(event) {     
    event.preventDefault();
    let id = $(this).attr('data-value');
    var title, text, btnClass, bgColor;
    if($('#isVerified-'+id).hasClass('active-item')) {
        title = 'Xác nhận tạm dừng hoạt động ?';
        text = 'Tạm dừng';
        btnClass = 'btn-danger';
        bgColor = 'red';
    } else {
        title = 'Xác nhận khôi phục hoạt động ?';
        text = 'Khôi phục';
        btnClass = 'btn-primary';
        bgColor = 'blue';
    }
    $.confirm({
        title: 'Thông báo',
        content: title,
        type: bgColor,
        buttons: {
            confirm: {
                text: text,
                btnClass: btnClass + ' mr-2',           
                action: function () {                    
                    if($('#isVerified-'+id).hasClass('active-item')) {
                        $('#isVerified-'+id).prop('checked', false);
                        $('#isVerified-'+id).removeClass('active-item');
                        $('#isVerified-'+id).addClass('deactive-item');                        
                    } else {
                        $('#isVerified-'+id).prop('checked', true);
                        $('#isVerified-'+id).removeClass('deactive-item');
                        $('#isVerified-'+id).addClass('active-item'); 
                    }
                    $("#form-isVerified-"+id).submit();                   
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

$(document).on('click','.status', function(event) { 
    event.preventDefault();
    let id = $(this).attr('data-value');
    var title, text, btnClass, bgColor;
    if($('#status-'+id).hasClass('active-item')) {
        title = 'Xác nhận tạm dừng hoạt động ?';
        text = 'Tạm dừng';
        btnClass = 'btn-danger';
        bgColor = 'red';
    } else {
        title = 'Xác nhận khôi phục hoạt động ?';
        text = 'Khôi phục';
        btnClass = 'btn-primary';
        bgColor = 'blue';
    }
    $.confirm({
        title: 'Thông báo',
        content: title,
        type: bgColor,
        buttons: {
            confirm: {
                text: text,
                btnClass: btnClass + ' mr-2',           
                action: function () {                    
                    if($('#status-'+id).hasClass('active-item')) {
                        $('#status-'+id).prop('checked', false);
                        $('#status-'+id).removeClass('active-item');
                        $('#status-'+id).addClass('deactive-item');                        
                    } else {
                        $('#status-'+id).prop('checked', true);
                        $('#status-'+id).removeClass('deactive-item');
                        $('#status-'+id).addClass('active-item'); 
                    }
                    $("#form-status-"+id).submit();                   
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

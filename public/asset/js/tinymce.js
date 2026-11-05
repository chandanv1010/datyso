const images_upload_handler = (blobInfo, progress) => new Promise((resolve, reject) => {
    
});

if (typeof tinymce !== 'undefined') {
  // Đăng ký gói ngôn ngữ tiếng Việt trực tiếp để hoạt động ổn định, không phụ thuộc mạng ngoài
  tinymce.addI18n('vi', {
    "Redo": "Làm lại",
    "Undo": "Hoàn tác",
    "Cut": "Cắt",
    "Copy": "Sao chép",
    "Paste": "Dán",
    "Select all": "Chọn tất cả",
    "New document": "Tài liệu mới",
    "Ok": "Đồng ý",
    "Cancel": "Hủy",
    "Visual aids": "Hỗ trợ trực quan",
    "Bold": "Đậm",
    "Italic": "Nghiêng",
    "Underline": "Gạch dưới",
    "Strikethrough": "Gạch ngang",
    "Subscript": "Chỉ số dưới",
    "Superscript": "Chỉ số trên",
    "Clear formatting": "Xóa định dạng",
    "Remove format": "Xóa định dạng",
    "Align left": "Căn trái",
    "Align center": "Căn giữa",
    "Align right": "Căn phải",
    "Justify": "Căn đều hai bên",
    "Bullet list": "Danh sách dấu đầu dòng",
    "Numbered list": "Danh sách đánh số",
    "Decrease indent": "Giảm lề",
    "Increase indent": "Tăng lề",
    "Close": "Đóng",
    "Formats": "Định dạng",
    "Headers": "Tiêu đề",
    "Header 1": "Tiêu đề 1",
    "Header 2": "Tiêu đề 2",
    "Header 3": "Tiêu đề 3",
    "Header 4": "Tiêu đề 4",
    "Header 5": "Tiêu đề 5",
    "Header 6": "Tiêu đề 6",
    "Headings": "Tiêu đề",
    "Heading 1": "Tiêu đề 1",
    "Heading 2": "Tiêu đề 2",
    "Heading 3": "Tiêu đề 3",
    "Heading 4": "Tiêu đề 4",
    "Heading 5": "Tiêu đề 5",
    "Heading 6": "Tiêu đề 6",
    "Paragraph": "Đoạn văn",
    "Blockquote": "Trích dẫn",
    "Blocks": "Khối",
    "Fonts": "Phông chữ",
    "Font sizes": "Cỡ chữ",
    "Link": "Liên kết",
    "Insert/edit link": "Chèn/sửa liên kết",
    "Text to display": "Văn bản hiển thị",
    "Url": "Địa chỉ URL",
    "Open link in...": "Mở liên kết trong...",
    "Current window": "Cửa sổ hiện tại",
    "None": "Không có",
    "New window": "Cửa sổ mới",
    "Open link": "Mở liên kết",
    "Remove link": "Xóa liên kết",
    "Anchors": "Neo",
    "Anchor...": "Neo...",
    "Name": "Tên",
    "Id": "Mã định danh",
    "Images": "Hình ảnh",
    "Image": "Hình ảnh",
    "Insert/edit image": "Chèn/sửa hình ảnh",
    "Source": "Nguồn",
    "Image description": "Mô tả hình ảnh",
    "Alternative description": "Mô tả thay thế",
    "Dimensions": "Kích thước",
    "Constrain proportions": "Giữ tỉ lệ",
    "General": "Chung",
    "Advanced": "Nâng cao",
    "Style": "Kiểu",
    "Vertical space": "Khoảng cách dọc",
    "Horizontal space": "Khoảng cách ngang",
    "Border": "Đường viền",
    "Insert image": "Chèn hình ảnh",
    "Image...": "Hình ảnh...",
    "Image list": "Danh sách hình ảnh",
    "Table": "Bảng",
    "Insert table": "Chèn bảng",
    "Table properties": "Thuộc tính bảng",
    "Delete table": "Xóa bảng",
    "Cell": "Ô",
    "Row": "Dòng",
    "Column": "Cột",
    "Cell properties": "Thuộc tính ô",
    "Merge cells": "Gộp ô",
    "Split cell": "Chia ô",
    "Insert row before": "Thêm dòng phía trên",
    "Insert row after": "Thêm dòng phía dưới",
    "Delete row": "Xóa dòng",
    "Row properties": "Thuộc tính dòng",
    "Cut row": "Cắt dòng",
    "Copy row": "Sao chép dòng",
    "Paste row before": "Dán dòng phía trước",
    "Paste row after": "Dán dòng phía sau",
    "Insert column before": "Thêm cột bên trái",
    "Insert column after": "Thêm cột bên phải",
    "Delete column": "Xóa cột",
    "Cols": "Cột",
    "Rows": "Dòng",
    "Width": "Độ rộng",
    "Height": "Chiều cao",
    "Cell spacing": "Khoảng cách ô",
    "Cell padding": "Khoảng cách lề ô",
    "Show blocks": "Hiển thị khối",
    "Show invisible characters": "Hiển thị ký tự ẩn",
    "Words: {0}": "Số từ: {0}",
    "{0} words": "{0} từ",
    "File": "Tập tin",
    "Edit": "Sửa",
    "Insert": "Chèn",
    "View": "Xem",
    "Format": "Định dạng",
    "Tools": "Công cụ",
    "Help": "Trợ giúp",
    "Media": "Đa phương tiện",
    "Insert/edit media": "Chèn/sửa media",
    "Alternative source": "Nguồn thay thế",
    "Media poster (Image URL)": "Ảnh bìa media (URL ảnh)",
    "Paste your embed code below:": "Dán mã nhúng vào bên dưới:",
    "Special character...": "Ký tự đặc biệt...",
    "Emoticons...": "Biểu tượng cảm xúc...",
    "Emoticons": "Biểu tượng cảm xúc",
    "Find and replace...": "Tìm và thay thế...",
    "Find": "Tìm kiếm",
    "Replace": "Thay thế",
    "Replace all": "Thay thế tất cả",
    "Prev": "Trước",
    "Next": "Tiếp theo",
    "Codesample": "Đoạn mã mẫu",
    "Insert/edit code sample": "Chèn/sửa đoạn mã",
    "Language": "Ngôn ngữ",
    "Visual blocks": "Khối trực quan"
  });

  tinymce.init({
    selector: 'textarea#editor-area, textarea.editor-area',
    license_key: 'gpl',
    language: 'vi',
    language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@latest/langs7/vi.js',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    height: 400,
    automatic_uploads: true,
    images_upload_handler: images_upload_handler,
    file_picker_types: 'image',
    file_picker_callback: (cb, value, meta) => {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = (e) => {
            const file = e.target.files[0];
            const reader = new FileReader();
            reader.onload = () => {
                const id = 'blobid' + new Date().getTime();
                const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                const base64 = reader.result.split(',')[1];
                const blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);
                cb(blobInfo.blobUri(), { title: file.name });
            };
            reader.readAsDataURL(file);
        };
        input.click();
    },

    setup: function(editor) {
      // Tự động đồng bộ nội dung sang textarea khi gõ/thay đổi để submit form chuẩn xác
      editor.on('change keyup', function () {
        editor.save();
      });

      // Khi người dùng chèn ảnh: tạo figure và figcaption
      editor.on('NodeChange', function(e) {
        const imgs = editor.dom.select('img');
        imgs.forEach(img => {
          const parent = img.parentNode;
          if (parent && parent.nodeName !== 'FIGURE') {
            const figure = editor.dom.create('figure', { style: 'text-align:center;' });
            const figcaption = editor.dom.create('figcaption', {}, 'Mô tả ảnh...');
            figure.appendChild(img.cloneNode(true));
            figure.appendChild(figcaption);
            editor.dom.replace(figure, img);
          }
        });
      });
    }
  });
}

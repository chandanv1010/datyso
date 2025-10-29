const images_upload_handler = (blobInfo, progress) => new Promise((resolve, reject) => {
    
});

// tinymce.init({
//     selector: 'textarea#editor-area',
//     language: 'vi',
//     plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
//     toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
//     image_title: false,
//     image_dimensions: true,
//     image_description: false,
//     automatic_uploads: true,
//     images_upload_handler: images_upload_handler,
//     file_picker_types: 'image',
//     file_picker_callback: (cb, value, meta) => {
//         const input = document.createElement('input');
//         input.setAttribute('type', 'file');
//         input.setAttribute('accept', 'image/*');

//         input.addEventListener('change', (e) => {
//             const file = e.target.files[0];

//             const reader = new FileReader();
//             reader.addEventListener('load', () => {
//                 /*
//                   Note: Now we need to register the blob in TinyMCEs image blob
//                   registry. In the next release this part hopefully won't be
//                   necessary, as we are looking to handle it internally.
//                 */
//                 const id = 'blobid' + (new Date()).getTime();
//                 const blobCache = tinymce.activeEditor.editorUpload.blobCache;
//                 const base64 = reader.result.split(',')[1];
//                 const blobInfo = blobCache.create(id, file, base64);
//                 blobCache.add(blobInfo);

//                 /* call the callback and populate the Title field with the file name */
//                 cb(blobInfo.blobUri(), { title: file.name });
//             });
//             reader.readAsDataURL(file);
//         });

//         input.click();
//     },
// });


tinymce.init({
  selector: 'textarea#editor-area',
  language: 'vi',
  plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
  toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
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
    // Khi người dùng chèn ảnh
    editor.on('NodeChange', function(e) {
      const imgs = editor.dom.select('img');
      imgs.forEach(img => {
        const parent = img.parentNode;
        if (parent && parent.nodeName !== 'FIGURE') {
          // tạo figure và figcaption
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


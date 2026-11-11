<?php
if (!function_exists('getYoutubeEmbedUrl')) {
    function getYoutubeEmbedUrl($url)
    {
        preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
        $videoId = $matches[1] ?? null;
        if ($videoId) {
            return "https://www.youtube.com/embed/$videoId";
        }
        return null;
    }
}

if (! function_exists('getImageUrl')) {
    function getImageUrl($image)
    {
        if (empty($image)) {
            return asset('asset/images/Logo-shortcut.png');
        }

        // Nếu file ảnh đã có sẵn trong thư mục public/storage/images ở local
        if (file_exists(public_path("storage/images/$image"))) {
            return asset("storage/images/$image");
        }

        // Tự động load ảnh từ website thật datyso.com khi máy local chưa có file
        return "https://datyso.com/storage/images/$image";
    }
}


if(!function_exists('convert_price')){
    function convert_price(mixed $price = '', $flag = false){
        if($price === null) return 0;
        return ($flag === false) ? str_replace('.','', $price) : number_format($price, 0, ',', '.');
    }
}

if (!function_exists('show_price')) {
    function show_price(mixed $price = '', mixed $price_discount = ''): string
    {
        if (empty($price) || $price === 0 || $price === '0') {
            return '<span class="price-contact">Liên hệ</span>';
        }

        $formattedPrice = convert_price($price, true);

        // Nếu có giá khuyến mãi
        if (!empty($price_discount) && $price_discount < $price) {
            $formattedDiscount = convert_price($price_discount, true);

            return sprintf(
                '<span class="price-discount">%s đ</span> <span class="price-old">%s đ</span>',
                $formattedDiscount,
                $formattedPrice
            );
        }

        return sprintf('<span class="price-normal">%s đ</span>', $formattedPrice);
    }
}

if(!function_exists('convert_array')){
    function convert_array($system = null, $keyword = '', $value = ''){
        $temp = [];
        if(is_array($system)){
            foreach($system as $key => $val){
                $temp[$val[$keyword]] = $val[$value];
            }
        }
        if(is_object($system)){
            foreach($system as $key => $val){
                $temp[$val->{$keyword}] = $val->{$value};
            }
        }

        return $temp;
    }
}


if(!function_exists('renderSystemInput')){
    function renderSystemInput(string $name = '', $systems = null, array $item = []){
        $value = old('config.' . $name, old($name, ($systems[$name]) ?? ''));
        $placeholder = $item['placeholder'] ?? '';
        return '<input 
            type="text"
            id="system-field-'.e($name).'"
            name="config['.e($name).']"
            value="'.e((string) $value).'"
            class="form-control"
            placeholder="'.e((string) $placeholder).'"
            autocomplete="off"
        >';
    }
}

if(!function_exists('renderSystemPassword')){
    function renderSystemPassword(string $name = '', $systems = null, array $item = []){
        $hasSaved = !empty($systems[$name]);
        $placeholder = $hasSaved
            ? '•••••••• (Đã lưu mật khẩu — để trống nếu giữ nguyên)'
            : ($item['placeholder'] ?? '');
        $html = '<input 
            type="password"
            id="system-field-'.e($name).'"
            name="config['.e($name).']"
            value=""
            class="form-control"
            placeholder="'.e((string) $placeholder).'"
            autocomplete="new-password"
        >';
        if ($hasSaved) {
            $html .= '<div class="d-flex align-items-center justify-content-between mt-1">'
                . '<small class="text-success fw-semibold">&#10003; Đã lưu mật khẩu SMTP (đã mã hóa an toàn)</small>'
                . '<label class="small text-muted mb-0" style="cursor:pointer;">'
                . '<input type="checkbox" name="clear_config[]" value="'.e($name).'" class="me-1"> Xóa mật khẩu cũ'
                . '</label>'
                . '</div>';
        }
        return $html;
    }
}


if(!function_exists('renderSystemImages')){
    function renderSystemImages(string $name = '', $systems = null){
        return '<input 
            type="text"
            name="config['.$name.']"
            value="'.old($name, ($systems[$name]) ?? '').'"
            class="form-control upload-image"
            placeholder=""
            autocomplete="off"
        >';
    }
}


if(!function_exists('renderSystemTextarea')){
    function renderSystemTextarea(string $name = '', $systems = null){
        return '<textarea name="config['.$name.']" class="form-control system-textarea">'.old($name, ($systems[$name]) ?? '').'</textarea>';
    }
}

if(!function_exists('renderSystemEditor')){
    function renderSystemEditor(string $name = '', $systems = null){
        return '<textarea name="config['.$name.']" id="'.$name.'" class="form-control system-textarea ck-editor">'.old($name, ($systems[$name]) ?? '').'</textarea>';
    }
}

if(!function_exists('renderSystemLink')){
    function renderSystemLink(array $item = [], $systems = null){
        return (isset($item['link'])) ? '<a class="system-link" target="'.$item['link']['target'].'" href="'.$item['link']['href'].'">'.$item['link']['text'].'</a>' : '';
    }
}

if(!function_exists('renderSystemTitle')){
    function renderSystemTitle(array $item = [], $systems = null){
        return (isset($item['title'])) ? '<span class="system-link text-danger">'.$item['title'].'</span>' : '';
    }
}

if(!function_exists('renderSystemSelect')){
    function renderSystemSelect(array $item, string $name = '', $systems = null){
       $selectedValue = (string) old('config.' . $name, old($name, ($systems[$name]) ?? ($item['default'] ?? '')));
       $html = '<select id="system-field-'.e($name).'" name="config['.e($name).']" class="form-control">';
            foreach($item['option'] as $key => $val){
                $html .= '<option '.(((string) $key === $selectedValue) ? 'selected' : '').' value="'.e((string) $key).'">'.e((string) $val).'</option>';
            }
       $html .= '</select>';

       return $html;
    }
}

if(!function_exists('seo')){
    function seo($model = null, $page = 1, $canonical = ''){
        return [
            'meta_title' => ($model->meta_title) ?? $model->name,
            'meta_keyword' => ($model->meta_keyword) ?? '',
            'meta_description' => ($model->meta_description) ?? cut_string_and_decode($model->descipriont, 168),
            'meta_image' => $model->image,
            'canonical' => $canonical,
        ];
    }
}

if(!function_exists('cut_string_and_decode')){
	function cut_string_and_decode($str = NULL, $n = 200){
        $str = html_entity_decode($str);
        $str = strip_tags($str);
        $str = cutnchar($str, $n);
        return $str;
	}
}

if(!function_exists('cutnchar')){
	function cutnchar($str = NULL, $n = 320){
		if(strlen($str) < $n) return $str;
		$html = substr($str, 0, $n);
		$html = substr($html, 0, strrpos($html,' '));
		return $html.'...';
	}
}


if(!function_exists('convertDateTime')){
    function convertDateTime(string $date = '', string $format = 'd/m/Y H:i', string $inputDateFormat = 'Y-m-d H:i:s'){
       $carbonDate = \Carbon\Carbon::createFromFormat($inputDateFormat, $date);

       return $carbonDate->format($format);
    }
}
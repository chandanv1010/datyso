<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class TableOfContent extends Component
{
    public array $items = [];

    public function __construct(public string $content)
    {
        $this->items = $this->extract($content);
    }

    /**
     * Inject id slug vào headings trong content
     */
    public static function injectIds($content, &$items)
    {
        $content = $content ?? '';
        if (empty(trim($content))) {
            $items = [];
            return '';
        }

        // ✅ Fix downgrade lỗi DOMDocument
        $dom = new \DOMDocument();
        @$dom->loadHTML(
            mb_convert_encoding('<div id="toc-temp-wrapper">'.$content.'</div>', 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $xpath = new \DOMXPath($dom);
        $headings = $xpath->query('//h1|//h2|//h3|//h4|//h5|//h6');

        $items = [];
        $counters = [];

        foreach ($headings as $heading) {
            $level = (int) substr($heading->nodeName, 1);
            $text = trim($heading->textContent);
            $cleanText = preg_replace('/^\d+(\.\d+)*\s*/u', '', $text);
            $id = Str::slug($cleanText, '-');

            $heading->setAttribute('id', $id);

            if (!isset($counters[$level])) $counters[$level] = 0;
            $counters[$level]++;
            for ($i = $level + 1; $i <= 6; $i++) unset($counters[$i]);
            $numbering = implode('.', array_slice($counters, 1));

            $items[] = [
                'id' => $id,
                'level' => $level,
                'text' => $text,
                'numbering' => $numbering,
            ];
        }

        // Trả lại phần HTML bên trong wrapper
        $wrapper = $dom->getElementById('toc-temp-wrapper');
        return $wrapper ? $dom->saveHTML($wrapper) : $dom->saveHTML();
    }

    /**
     * Extract headings từ content (H2-H4)
     */
    public static function extract(?string $content): array
    {
        $content = $content ?? '';
        if (empty($content) || trim($content) === '') {
            return [];
        }

        if (strip_tags($content) === $content) {
            return [];
        }

        preg_match_all('/<h([2-4])[^>]*>(.*?)<\/h\1>/iu', $content, $matches);

        $items = [];
        $numbering = [];

        for ($i = 0; $i < count($matches[0]); $i++) {
            $level = (int) $matches[1][$i];
            $raw = $matches[2][$i];

            // Decode entity + bỏ tag rác
            $text = html_entity_decode(trim(strip_tags($raw)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $cleanText = preg_replace('/^\d+(\.\d+)*\s*/u', '', $text);

            $id = Str::slug($cleanText, '-'); // 👈 Giống injectIds()

            // Đánh số heading
            if (!isset($numbering[$level])) $numbering[$level] = 0;
            $numbering[$level]++;
            for ($j = $level + 1; $j <= 4; $j++) {
                $numbering[$j] = 0;
            }

            $numParts = [];
            for ($j = 2; $j <= $level; $j++) {
                if (isset($numbering[$j]) && $numbering[$j] > 0) {
                    $numParts[] = $numbering[$j];
                }
            }

            $items[] = [
                'level'     => $level,
                'text'      => $text,
                'id'        => $id,
                'numbering' => implode('.', $numParts),
            ];
        }

        return $items;
    }


    public static function buildTree(array $items, int $depth = 1, int &$i = 0, ?int $baseLevel = null): string
    {
        if (empty($items)) return '';

        // Cấp outline nhỏ nhất xuất hiện trong items (thường là 2 hoặc 3)
        if ($baseLevel === null) {
            $baseLevel = min(array_column($items, 'level'));
        }

        // Cấp outline hiện tại (theo thẻ thật) mà depth này đang xử lý
        $currentOutline = $baseLevel + ($depth - 1);
        $n = count($items);

        $html = '<ul class="toc-list depth-' . $depth . '">';

        while ($i < $n) {
            $item = $items[$i];
            $itemLevel = $item['level'];

            // Nếu heading thật nhỏ hơn outline cấp hiện tại -> thoát về cha
            if ($itemLevel < $currentOutline) break;

            // Nếu heading thật sâu hơn outline cấp hiện tại -> xuống cấp con (tăng depth)
            if ($itemLevel > $currentOutline) {
                $html .= self::buildTree($items, $depth + 1, $i, $baseLevel);
                continue;
            }

            // ✅ Map theo yêu cầu: depth 1 -> h2, depth >=2 -> h3
            $tag = ($depth === 1) ? 'h2' : 'h3';
            $cleanText = preg_replace('/^\d+(\.\d+)*\s*/u', '', $item['text']);

            $html .= '<li class="depth-' . $depth . ' mb-1">';
            $html .= "<{$tag} class=\"toc-heading depth-{$depth}\">";
            $html .= "<a href=\"#{$item['id']}\" class=\"toc-link\">{$item['numbering']}{$cleanText}</a>";
            $html .= "</{$tag}>";

            // Sang heading kế tiếp
            $i++;

            // Nếu phần tử kế tiếp là cấp con (theo thẻ thật) -> render UL con (depth + 1)
            if ($i < $n && $items[$i]['level'] > $currentOutline) {
                $html .= self::buildTree($items, $depth + 1, $i, $baseLevel);
            }

            $html .= '</li>';

            // Nếu phần tử kế tiếp là cấp cha hơn -> kết thúc danh sách hiện tại
            if ($i < $n && $items[$i]['level'] < $currentOutline) break;
        }

        $html .= '</ul>';
        return $html;
    }


    public function render(): View|Closure|string
    {
        return view('components.table-of-content');
    }
}

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
        if (empty($content) || trim($content) === '') {
            $items = [];
            return '';
        }

        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        $xpath = new \DOMXPath($dom);
        $headings = $xpath->query('//h1|//h2|//h3|//h4|//h5|//h6');

        $items = [];
        $counters = [];

        foreach ($headings as $heading) {
            $level = (int) substr($heading->nodeName, 1);
            $text = trim($heading->textContent);

            // ⚙️ BỎ phần số đầu dòng để slug giống extract()
            $cleanText = preg_replace('/^\d+(\.\d+)*\s*/u', '', $text);

            $id = Str::slug($cleanText, '-'); // thống nhất slug

            $heading->setAttribute('id', $id);

            // Numbering
            if (!isset($counters[$level])) $counters[$level] = 0;
            $counters[$level]++;
            for ($i = $level + 1; $i <= 6; $i++) unset($counters[$i]);
            $numbering = implode('.', array_slice($counters, 1));

            $items[] = [
                'id'        => $id,
                'level'     => $level,
                'text'      => $text,
                'numbering' => $numbering,
            ];
        }

        return $dom->saveHTML();
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

    public function render(): View|Closure|string
    {
        return view('components.table-of-content');
    }
}

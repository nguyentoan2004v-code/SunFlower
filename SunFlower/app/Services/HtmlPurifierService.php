<?php

namespace App\Services;


class HtmlPurifierService
{
    /** Tags được phép giữ lại */
    private array $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i',
        'ul', 'ol', 'li',
        'h2', 'h3', 'h4',
        'img', 'a',
        'div', 'span', 'blockquote',
    ];

    /**
     * Attribute được phép theo từng tag.
     * '*' áp dụng cho tất cả tag trong whitelist.
     */
    private array $allowedAttributes = [
        '*'   => ['class'],
        'img' => ['src', 'alt', 'class', 'width', 'height'],
        'a'   => ['href', 'title', 'target', 'rel'],
    ];

    /** Attribute chứa URL — cần kiểm tra scheme */
    private array $urlAttributes = ['href', 'src'];

    /** Scheme an toàn */
    private array $safeSchemes = ['http', 'https'];

    /**
     * Làm sạch HTML đầu vào, trả về HTML đã được sanitize.
     */
    public function purify(string $html): string
    {
        if (empty(trim($html))) {
            return '';
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');

        // Tắt cảnh báo XML/HTML5 không chuẩn
        libxml_use_internal_errors(true);

        // Bọc trong div để giữ nguyên cấu trúc nhiều node ngang hàng
        $dom->loadHTML(
            '<?xml encoding="UTF-8"><html><body><div id="__sanitize_root">' . $html . '</div></body></html>',
            LIBXML_NOERROR | LIBXML_NOWARNING
        );

        libxml_clear_errors();
        libxml_use_internal_errors(false);

        $root = $dom->getElementById('__sanitize_root');
        if (!$root) {
            return e($html); // Fallback an toàn: escape toàn bộ
        }

        $this->walkAndClean($root, $dom);

        // Xuất innerHTML của root div
        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }

        return $output;
    }

    /**
     * Duyệt đệ quy DOM, xử lý từng node.
     */
    private function walkAndClean(\DOMNode $node, \DOMDocument $dom): void
    {
        $toRemove = [];

        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $tag = strtolower($child->nodeName);

                if (!in_array($tag, $this->allowedTags, true)) {
                    // Tag không được phép → giữ lại text content bên trong
                    $toRemove[] = ['node' => $child, 'keep_children' => true];
                    continue;
                }

                // Tag hợp lệ → làm sạch attribute
                $this->cleanAttributes($child, $tag);

                // Đệ quy vào children
                $this->walkAndClean($child, $dom);

            } elseif ($child->nodeType === XML_COMMENT_NODE) {
                // Xóa HTML comment — có thể chứa IE conditional expressions
                $toRemove[] = ['node' => $child, 'keep_children' => false];
            }
        }

        // Thực hiện xóa (không xóa trong vòng lặp childNodes để tránh lỗi iterator)
        foreach ($toRemove as $item) {
            if (!$item['node']->parentNode) {
                continue;
            }

            if ($item['keep_children'] && $item['node']->hasChildNodes()) {
                // Chuyển children lên thay thế node bị xóa
                $fragment = $dom->createDocumentFragment();
                while ($item['node']->firstChild) {
                    $fragment->appendChild($item['node']->firstChild);
                }
                $item['node']->parentNode->insertBefore($fragment, $item['node']);
            }

            $item['node']->parentNode->removeChild($item['node']);
        }
    }

    /**
     * Xóa attribute nguy hiểm khỏi element.
     */
    private function cleanAttributes(\DOMElement $element, string $tag): void
    {
        $allowed = array_merge(
            $this->allowedAttributes['*'] ?? [],
            $this->allowedAttributes[$tag] ?? []
        );

        $toRemove = [];

        foreach ($element->attributes as $attr) {
            $name = strtolower($attr->name);

            // Chặn toàn bộ event handler (onclick, onerror, onload, onmouseover...)
            if (str_starts_with($name, 'on')) {
                $toRemove[] = $attr->name;
                continue;
            }

            // Chặn attribute không có trong whitelist
            if (!in_array($name, $allowed, true)) {
                $toRemove[] = $attr->name;
                continue;
            }

            // Kiểm tra URL scheme cho href và src
            if (in_array($name, $this->urlAttributes, true)) {
                $value = trim($attr->value);

                // Lấy scheme (phần trước dấu ':')
                $colonPos = strpos($value, ':');
                if ($colonPos !== false) {
                    $scheme = strtolower(substr($value, 0, $colonPos));
                    // Chặn javascript:, data:, vbscript:...
                    if (!in_array($scheme, $this->safeSchemes, true)) {
                        $toRemove[] = $attr->name;
                        continue;
                    }
                }
                // Relative URLs (/path, #anchor, ./relative) → an toàn, giữ lại
            }
        }

        foreach ($toRemove as $attrName) {
            $element->removeAttribute($attrName);
        }
    }
}

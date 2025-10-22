<?php

namespace App\Helpers;
use Illuminate\Support\Str;

if (!function_exists('getTypeBadge')) {
    function getTypeBadge($type) {
        $badges = [
            'acara' => 'bg-purple-100 text-purple-800',
            'tutorial' => 'bg-blue-100 text-blue-800',
            'pengumuman' => 'bg-red-100 text-red-800',
            'berita' => 'bg-green-100 text-green-800',
            'tips' => 'bg-yellow-100 text-yellow-800',
        ];
        
        return $badges[$type] ?? 'bg-gray-100 text-gray-800';
    }
}

if (!function_exists('getBlogExcerpt')) {
    function getBlogExcerpt($content, $length = 150) {
        $text = strip_tags($content);
        return Str::limit($text, $length);
    }
}

if (!function_exists('sanitizeHtml')) {
    function sanitizeHtml($html) {
        return clean($html, [
            'HTML.Allowed' => 'p,br,strong,em,u,a[href|target|rel],ul,ol,li,h1,h2,h3,h4,h5,h6,img[src|alt|title|width|height],blockquote,code,pre,span[style],div[class]',
            'CSS.AllowedProperties' => 'color,background-color,font-size,font-weight,text-align,margin,padding',
            'AutoFormat.RemoveEmpty' => true,
            'AutoFormat.AutoParagraph' => true,
            'Attr.AllowedRel' => 'nofollow,noopener,noreferrer',
            'HTML.TargetBlank' => true,
        ]);
    }
}
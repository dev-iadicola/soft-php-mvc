<?php

declare(strict_types=1);

/**
 * Generates a professional SVG placeholder for a project.
 *
 * @param string $title Project title
 * @param string $color Primary hex color (without #)
 * @param string $icon Simple icon path or text
 * @return string SVG markup
 */
function generateProjectSvg(string $title, string $color = '4A90D9', string $icon = ''): string
{
    $initials = '';
    $words = explode(' ', trim($title));
    foreach (array_slice($words, 0, 2) as $word) {
        $initials .= mb_strtoupper(mb_substr($word, 0, 1));
    }

    $bgColor = '#' . $color;
    $darkColor = '#' . dechex(max(0, (int) hexdec(substr($color, 0, 2)) - 40))
        . dechex(max(0, (int) hexdec(substr($color, 2, 2)) - 40))
        . dechex(max(0, (int) hexdec(substr($color, 4, 2)) - 40));

    return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300" width="400" height="300">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$bgColor};stop-opacity:1" />
      <stop offset="100%" style="stop-color:{$darkColor};stop-opacity:1" />
    </linearGradient>
  </defs>
  <rect width="400" height="300" fill="url(#bg)" rx="8"/>
  <circle cx="200" cy="120" r="50" fill="rgba(255,255,255,0.15)"/>
  <text x="200" y="135" text-anchor="middle" font-family="Arial, sans-serif" font-size="36" font-weight="bold" fill="#fff">{$initials}</text>
  <text x="200" y="220" text-anchor="middle" font-family="Arial, sans-serif" font-size="16" fill="rgba(255,255,255,0.9)">{$title}</text>
  <rect x="140" y="245" width="120" height="3" rx="1.5" fill="rgba(255,255,255,0.3)"/>
</svg>
SVG;
}

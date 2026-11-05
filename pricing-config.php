<?php
declare(strict_types=1);

/*
 * Fill each approved rate below, then change enabled to true.
 * Per-page rates are multiplied by page count. Reference screenshot prices are excluded.
 */
return [
    'enabled' => false,
    'currency' => 'USD',
    'page_min' => 20,
    'page_max' => 800,
    'page_step' => 10,
    'rates' => [
        'editing' => ['proofread' => null, 'light' => null, 'heavy' => null, 'writing' => null],
        'formatting_per_page' => null,
        'services' => ['cover' => null, 'isbn' => null, 'kdp' => null, 'ingramspark' => null, 'metadata' => null, 'author_website' => null],
        'formats' => ['ebook' => null, 'paperback' => null, 'hardcover' => null],
        'marketing' => ['none' => 0, 'launch' => null, 'full' => null, 'ongoing' => null],
    ],
];

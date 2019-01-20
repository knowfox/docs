<?php
Dotenv\Dotenv::create(__DIR__)->load();

$accessToken = env('ACCESS_TOKEN');
$rootConcept = env('ROOT_CONCEPT');

$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => "Accept: 'application/json\r\n"
            . "Authorization: Bearer $accessToken\r\n",
    ]
]);

$site = json_decode(file_get_contents(
    'https://knowfox.com/api/concept/' . $rootConcept,
    false,
    $context
));

return [
    'baseUrl' => '',
    'production' => false,
    'siteName' => 'Knowfox',
    'siteDescription' => "Users' and Developers' Manual",
    'siteSummary' => 'Knowfox is Open Source Software for Personal Knowledge Management. <br class="hidden sm:block">You can use our hosted service or run it on your own.',

    // Algolia DocSearch credentials
    'docsearchApiKey' => '',
    'docsearchIndexName' => '',

    // navigation menu
    'navigation' => require_once('navigation.php'),

    // helpers
    'isActive' => function ($page, $path) {
        return ends_with(trimPath($page->getPath()), trimPath($path));
    },
    'isActiveParent' => function ($page, $menuItem) {
        if (is_object($menuItem) && $menuItem->children) {
            return $menuItem->children->contains(function ($child) use ($page) {
                return trimPath($page->getPath()) == trimPath($child);
            });
        }
    },
    'url' => function ($page, $path) {
        return starts_with($path, 'http') ? $path : '/' . trimPath($path);
    },

    'collections' => [
        'docs' => [
            'extends' => '_layouts.documentation',
            'items' => function () use ($site) {
                

                var_dump($site->children);

                return collect($site->children)->map(function ($doc) {
                    return [
                        'title' => $doc->title,
                        'filename' => $doc->slug,
                        'content' => $doc->body,
                    ];
                });
            }
        ]
    ]
];

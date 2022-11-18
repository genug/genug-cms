<?php use genug\Api as g;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= g::requestedPage()->title?></title>
    <link rel="stylesheet" href="/asset/css/style.css" />
</head>
<body>
    <h1><?= g::requestedPage()->title ?></h1>
    <p><time datetime="<?= g::requestedPage()->date ?>"><?= g::requestedPage()->date->format(DATE_RFC1123) ?></time></p>
    <ul>
        <li>Category ID: <?= g::categories()->fetch(g::requestedPage()->category)->id ?></li>
        <li>Category Title: <?= g::categories()->fetch(g::requestedPage()->category)->title ?></li>
    </ul>
    <?= g::requestedPage()->content ?>

    <nav>
        <h1>all pages</h1>
        <ul>
<?php foreach (g::pages() as $page): ?>
    <?php if ((string) $page->id !== \genug\Setting\HTTP_404_PAGE_ID): ?>
            <li>
                <a href="<?= $page->id ?>"<?php if ($page === g::requestedPage()) {
                    echo ' aria-current="page"';
                } ?>><?= $page->title ?></a>
            </li>
    <?php endif; ?>
<?php endforeach; ?>
        </ul>
    </nav>
</body>
</html>
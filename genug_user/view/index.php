<?php use genug\Api as g; ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= g::requestedPage()->title()?></title>
    <link rel="stylesheet" href="/asset/css/style.css" />
</head>
<body>
    <h1><?= g::requestedPage()->title() ?></h1>
    <p><time datetime="<?= g::requestedPage()->date() ?>"><?= g::requestedPage()->date()->format(DATE_RFC1123) ?></time></p>
    <?= g::requestedPage()->content()?>

    <nav>
        <h1>all pages</h1>
        <ul>
<?php foreach (g::pages() as $page): ?>
            <li>
                <a href="<?= $page->id() ?>"<?php if ($page === g::requestedPage()) echo ' aria-current="page"' ?>><?= $page->title() ?></a>
            </li>
<?php endforeach; ?>
        </ul>
    </nav>
</body>
</html>
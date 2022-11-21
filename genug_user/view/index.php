<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $genug->requestedPage->title?></title>
    <link rel="stylesheet" href="/asset/css/style.css" />
</head>
<body>
    <h1><?= $genug->requestedPage->title ?></h1>
    <p><time datetime="<?= $genug->requestedPage->date ?>"><?= $genug->requestedPage->date->format(DATE_RFC1123) ?></time></p>
    <ul>
        <li>Category ID: <?= $genug->categories->fetch($genug->requestedPage->category)->id ?></li>
        <li>Category Title: <?= $genug->categories->fetch($genug->requestedPage->category)->title ?></li>
    </ul>
    <?= $genug->requestedPage->content ?>

    <nav>
        <h1>all pages</h1>
        <ul>
<?php foreach ($genug->pages as $page): ?>
    <?php if ((string) $page->id !== \genug\Setting\HTTP_404_PAGE_ID): ?>
            <li>
                <a href="<?= $page->id ?>"<?php if ($page === $genug->requestedPage) {
                    echo ' aria-current="page"';
                } ?>><?= $page->title ?></a>
            </li>
    <?php endif; ?>
<?php endforeach; ?>
        </ul>
    </nav>
</body>
</html>
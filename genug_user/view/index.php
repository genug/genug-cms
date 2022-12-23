<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $genug->requestedPage->title ?? $genug->requestedPage->id ?></title>
    <link rel="stylesheet" href="/asset/css/style.css" />
</head>
<body>
    <h1><?= $genug->requestedPage->title ?></h1>
    <p><time datetime="<?= $genug->requestedPage->date ?>"><?= $genug->requestedPage->date?->format(DATE_RFC1123) ?></time></p>
    <ul>
        <?php $currentGroup = $genug->groups->fetch($genug->requestedPage->group); ?>
        <li>Group ID: <?= $currentGroup->id ?></li>
        <li>Group Title: <?= $currentGroup->title ?></li>
    </ul>
    <?= $genug->requestedPage->content ?>

    <nav>
        <h1>all pages</h1>
        <ul>
<?php foreach ($genug->pages as $page): ?>
    <?php if (! $page->id->equals($genug->setting->notFoundPageId)): ?>
            <li>
                <a href="<?= $page->id ?>"<?php if ($page->equals($genug->requestedPage)) {
                    echo ' aria-current="page"';
                } ?>><?= $page->title ?? $page->id ?></a>
            </li>
    <?php endif; ?>
<?php endforeach; ?>
        </ul>
    </nav>
</body>
</html>
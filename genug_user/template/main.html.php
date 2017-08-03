<?php
use genug\Api as g;
use const genug\Api\URL_PATH_BASE;

const SITE_TITLE = 'my site';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?= g::requestedPage()->title()?><?php if (g::requestedPage()->category() !== g::mainCategory()) echo ' - ' . g::requestedPage()->category()->title() ?> | <?= SITE_TITLE ?></title>
<link
  rel="stylesheet"
  href="<?= URL_PATH_BASE ?>/asset/css/style.css" />
</head>
<body>
  <header>
    <h1>
      <a href="<?= URL_PATH_BASE . g::homepage()->id() ?>"><?= SITE_TITLE ?></a>
    </h1>
  </header>
  <main>
  <article>
    <header>
      <h1><?= g::requestedPage()->title() ?></h1>
    </header>
    <?= g::requestedPage()->content()?>
    <aside>
      <ul>
        <li>date: <time datetime="<?= g::requestedPage()->date() ?>"><?= g::requestedPage()->date()->format(DATE_RFC1123) ?></time></li>
        <li>category: <span
          data-category="<?= g::requestedPage()->category()->id() ?>"><?= g::requestedPage()->category()->title() ?></span></li>
      </ul>
    </aside>
  </article>
  </main>
  <nav>
    <h1>all pages</h1>
    <ul>
  <?php foreach (g::pages() as $page): ?>
  <li><a
        href="<?= URL_PATH_BASE . $page->id() ?>"
        class="<?php if ($page === g::requestedPage()) echo 'is_requested' ?>"><?= $page->title() ?></a></li>
  <?php endforeach; ?>
  </ul>
  </nav>
</body>
</html>
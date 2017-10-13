<?php
use genug\Api as g;

const SITE_TITLE = 'my site';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?= g::requestedPage()->title()?><?php if (g::requestedPage()->category() !== g::mainCategory()) echo ' - ' . g::requestedPage()->category()->title() ?> | <?= SITE_TITLE ?></title>
<link
  rel="stylesheet"
  href="/asset/css/style.css" />
</head>
<body>
  <header>
    <h1>
      <a href="<?= g::homepage()->id() ?>"><?= SITE_TITLE ?></a>
    </h1>
  </header>
  <main>
  <article>
    <header>
      <h1><?= g::requestedPage()->title() ?></h1>
    </header>
    <?= g::requestedPage()->content()?>
    <footer>
      <ul>
        <li>date: <time datetime="<?= g::requestedPage()->date() ?>"><?= g::requestedPage()->date()->format(DATE_RFC1123) ?></time></li>
        <li>category: <span
          data-category="<?= g::requestedPage()->category()->id() ?>"><?= g::requestedPage()->category()->title() ?></span></li>
      </ul>
    </footer>
  </article>
  </main>
  <nav>
    <h1>all pages</h1>
    <ul>
  <?php foreach (g::pages() as $page): ?>
  <li><a
        href="<?= $page->id() ?>"
        class="<?php if ($page === g::requestedPage()) echo 'is_requested' ?>"><?= $page->title() ?></a></li>
  <?php endforeach; ?>
  </ul>
  </nav>
</body>
</html>
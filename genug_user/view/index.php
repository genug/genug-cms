<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $genug->requestedPage->title ?? $genug->requestedPage->id ?></title>
    <link rel="stylesheet" href="/asset/css/style.css" />
</head>
<body>
<pre>
<?= $genug->requestedPage->content ?>
</pre>
</body>
</html>
<title>Simple Dynamic Page Example</title>
<p>Random number: <?= rand(); ?></p>

<nav>
    <?php if (! $this->id->equals($this->homeId)): ?>
    <a href="<?= $this->homeId ?>">home</a>
    <?php endif; ?>
</nav>
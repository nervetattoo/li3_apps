<h1><?=$app->title ?></h1>
<h2><?=$app->repo ?></h2>

<h3>Versions</h3>
<?php foreach($versions as $version): ?>
<div>
    <h4><?=$version ?></h4>
</div>
<?php endforeach; ?>

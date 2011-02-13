<h1><?=$app->title ?></h1>
<p><?=$app->repo ?></p>

<h2>Deploys</h2>
<ul>
<?php foreach($deployed as $node): ?>
    <li>
        <h5>
            <?=$node['name'] ?>
            (<?=$node['link'] ?>)
        </h5>
    </li>
<?php endforeach; ?>
</ul>

<h2>Versions</h2>
<ul>
<?php foreach($versions as $version): ?>
    <li>
        <h4><?=$version ?></h4>
    </li>
<?php endforeach; ?>
</ul>

<h1><?=$app->title ?></h1>
<p><?=$app->repo ?></p>

<?php if ($deployed): ?>
<h2>Deploys</h2>
<ul>
<?php foreach($deployed as $node): ?>
    <li><h5>
        <?=$this->html->link($node['name'], '/deploy/view/');?>
        (<?=$node['link'] ?>)
    </h5></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<?=$this->html->link("Create deploy target", '/apps/deploy/' . $app->_id);?>

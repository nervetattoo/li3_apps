<?php foreach($apps as $app): ?>
<article>
    <h1>
        <?=$this->html->link($app->title, '/apps/view/' . $app->_id);?>
    </h1>
    <h2><?=$app->repo ?></h2>
</article>
<?php endforeach; ?>

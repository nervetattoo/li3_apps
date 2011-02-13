<?php foreach($apps as $app): ?>
<article>
    <h1>
        <?=$this->html->link($app->title, '/apps/view/' . $app->_id);?>
    </h1>
    <h4><?=$app->repo ?></h4>
</article>
<?php endforeach; ?>

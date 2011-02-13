<!doctype html>
<html>
<head>
	<?php echo $this->html->charset();?>
	<title><?php echo $this->title(); ?> &ndash; My Fantastic App</title>

    <script src="http://code.jquery.com/jquery-1.5.min.js" type="text/javascript"></script>

	<?php echo $this->html->style(array('debug', 'lithium')); ?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>
<body class="app">
    <div id="wrapper">
        <nav id=menu>
        <?=$this->html->link('Add App', '/apps/add');?>
        <?=$this->html->link('Apps', '/apps');?>
        </nav>
        <?php echo $this->content(); ?>
    </div>
</body>
</html>

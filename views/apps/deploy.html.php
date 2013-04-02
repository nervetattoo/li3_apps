<h2>Create new deploy target</h2>
<form action="/apps/deploy/<?=$app->_id?>" method=post>
    <?=$this->form->field('name',array('value'=>$defaults['name']));?>
    <?=$this->form->field('target', array('value'=>$defaults['target']));?>
    <?=$this->form->field('ref', array('list' => $refs));?>
    <?=$this->form->submit('Create deploy target'); ?>
</form>

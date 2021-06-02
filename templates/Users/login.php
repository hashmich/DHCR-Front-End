<?php $this->set('bodyClasses', 'login'); ?>
<?php $this->start('page_head'); ?>
<div class="title">
    <h2>Login</h2>
</div>
<?php $this->end(); ?>

<div class="users form content">
    <?= $this->Form->create() ?>

    <?= $this->Form->control('email') ?>
    <?= $this->Form->control('password') ?>

    <?= $this->Form->button(__('Login'), ['class' => 'right']); ?>
    <?= $this->Form->end() ?>
</div>
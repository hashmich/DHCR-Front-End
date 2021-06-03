<?php $this->Html->css('https://use.fontawesome.com/releases/v5.8.2/css/all.css', ['block' => true]); ?>


<h3 class="follow">
    <a href="https://instagram.com/username">
       <span class="fab fa-instagram"></span>
       Follow us on Instagram!
    </a>
</h3>


<h3>Collection of links featured in our social media posts</h3>
<ul id="stories" class="custom-bullets"></ul>

<?php
$this->Html->script('stories.js', ['block' => true]);
$this->Html->scriptStart(['block' => true]);
?>
$(document).ready(function(){
    let content = new Stories();
    content.load();
});
<?php $this->Html->scriptEnd(); ?>
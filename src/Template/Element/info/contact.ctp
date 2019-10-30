<?php

use Cake\Core\Configure;
?>

<div class="flex-columns">
    <div class="flex-item">
        <h3>Request Form</h3>
        <p>
            Please use our form to automatically let the system address
            all moderators or admins in charge of your inquiry.
        </p>
    
        <?php
        $this->Html->script('https://www.google.com/recaptcha/api.js', array('inline' => false));
        $this->Html->scriptStart(array('inline' => false));
        ?>
        function recaptchaCallback(token) {
        document.getElementById("ContactUsForm").submit();
        }
        <?php $this->Html->scriptEnd(); ?>
    
        <?php
        echo $this->Form->create(false, [
            'novalidate' => true,
            'id' => 'ContactUsForm',
            'url' => '/contact/us'
        ]);
        echo $this->Form->control('email', array(
            'label' => 'Your E-Mail',
            'autocomplete' => 'off'
        ));
        echo $this->Form->control('country_id', [
            'empty' => 'Administrators',
            'label' => 'Moderators']);
        echo $this->Form->control('first_name');
        echo $this->Form->control('last_name');
        echo $this->Form->control('telephone', array(
            'type' => 'text'
        ));
        echo $this->Form->control('message', array(
            'type' => 'textarea',
        ));
        echo $this->Form->end(array(
            'label' => 'Submit',
            'class' => 'g-recaptcha',
            'data-sitekey' => Configure::read('App.reCaptchaPublicKey'),
            'data-callback' => 'recaptchaCallback'
        ));
        ?>
    </div>
    <div class="flex-item">
        <h3>National Moderators</h3>
        <p>
            The DHCR has a moderation system, that is inherited from the DARIAH system of national moderators.
            Moderators are in question to review newly entered course data, help with registration issues
            and to disseminate the DHCR initiative among their countries.
        </p>
        <div class="css-columns moderators">
            <?php
            $last_country = null;
            if(!empty($moderators)) foreach($moderators as $i => $mod) {
                if(empty($mod['country_id'])) continue;
                if($mod['country_id'] == $last_country) {
                    echo '<br />';
                }else{
                    if($i > 0) echo '</p></div>';
                    echo '<div class="item">';
                    echo '<p class="country">' . $mod['country']['name'] . '</p>';
                    echo '<p class="moderators">';
                }
                $last_country = $mod['country_id'];
        
                echo $this->Html->link(
                    $mod['first_name'] . ' ' . $mod['last_name'],
                    'mailto:' . $mod['email']);
        
            }
            echo '</div>';
            ?>
        </div>
        
        <h3>Administrators</h3>
        <p>
            For technical inquiries or yet not moderated countries, please seek contact to the administration board:
        </p>
        <div class="css-columns moderators">
            <p>
                <?php
                foreach($userAdmins as $i => $mod) {
                    echo $this->Html->link(
                        $mod['first_name'] . ' ' . $mod['last_name'],
                        'mailto:' . $mod['email']);
                    echo '<br>';
                }
                ?>
            </p>
        </div>
        
    </div>
</div>

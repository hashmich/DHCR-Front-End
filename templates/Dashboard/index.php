<p></p>
<div id="welcome_user">
    <p>
        <?php
        // welcome line
        echo 'Hello ' . ucfirst(trim($user->academic_title)) . ' ' . ucfirst(trim($user->first_name)) . ' ' . ucfirst(trim($user->last_name))
            . ', thanks for contributing to the DHCR';
        if ($user->is_admin) {
            echo ' as <strong><font color="60a845"> administrator</font></strong>';
        }
        if ($user->is_admin && $user->user_role_id == 2) {
            echo ' and ';
        }
        if ($user->user_role_id == 2) {
            echo ' as <strong><font color="60a845"> moderator</font></strong> </font></strong>'; //of  <strong><font color="60a845">' . $user->country->name . '
        }
        echo '.</p>';
        // *** start of notification area ***
        // needs attention counts on one line
        echo '<p>';
        if ($user->is_admin || $user->user_role_id == 2) {
            if ($pendingAccountRequests > 0) {
                echo $this->Html->link(
                    '<font color="60a845"><strong><span class="glyphicon glyphicon-pushpin"></span> Account Approval:',
                    ['controller' => 'users', 'action' => 'approve'],
                    ['title' => 'Account Approval', 'escape' => false]
                );
                echo ' ' . $pendingAccountRequests . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp</strong></font>';
            }
            if ($pendingCourseRequests > 0) {
                echo $this->Html->link(
                    '<font color="60a845"><strong><span class="glyphicon glyphicon-pushpin"></span> Course Approval:',
                    ['controller' => 'courses', 'action' => 'approve'],
                    ['title' => 'Course Approval', 'escape' => false]
                );
                echo ' ' . $pendingCourseRequests . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;</strong></font>';
            }
        }
        echo $this->Html->link(
            '<font color="60a845"><strong><span class="glyphicon glyphicon-pushpin"></span> Course Expiry:',
            ['controller' => 'courses', 'action' => 'expired'],
            ['title' => 'Course Expiry', 'escape' => false]
        );
        echo ' ' . $expiredCourses . '</strong></font>';
        if ($totalNeedsattention == 0) {
            echo '<font color="60a845"><strong><span class="glyphicon glyphicon-check"></span> You are up to date</strong></font>';
        }
        echo '</strong></font></p>';
        // newsletter alert
        if (!$user->mail_list) {
            echo '<p>';
            echo $this->Html->link(
                '<font color="60a845"><strong><span class="glyphicon glyphicon-pushpin"></span>  Sign up for the Contributor Mailing List here',
                ['controller' => 'users', 'action' => 'newsletter'],
                ['title' => 'Sign up', 'escape' => false]
            );
            echo '</p>';
        }
        // *** end of notification area ***
        ?>
</div>
<p></p>
<h2><span class="glyphicon glyphicon-star"></span>&nbsp;&nbsp;&nbsp;Dashboard</h2>
<p></p>
<div id="dashboard">
    <?php
    echo $this->Html->link(
        '<p></p><span class="glyphicon glyphicon-flag"></span><br>Needs Attention<p></p>',
        [
            'controller' => 'dashboard',
            'action' => 'needsAttention'
        ],
        [
            'class' => 'blue button',
            'title' => 'Needs Attention',
            'escape' => false
        ]
    );
    if ($user->user_role_id == 2 || $user->is_admin) {
        echo $this->Html->link(
            '<p></p><span class="glyphicon glyphicon-list"></span><br>Category Lists<p>&nbsp;</p>',
            [
                'controller' => 'dashboard',
                'action' => 'categoryLists'
            ],
            [
                'class' => 'blue button',
                'title' => 'Category Lists',
                'escape' => false
            ]
        );
    }
    echo $this->Html->link(
        '<p></p><span class="glyphicon glyphicon-education"></span><br>Administrate Courses<p>&nbsp;</p>',
        [
            'controller' => 'Dashboard',
            'action' => 'adminCourses'
        ],
        [
            'class' => 'blue button',
            'title' => 'Administrate Courses',
            'escape' => false
        ]
    );
    echo $this->Html->link(
        '<p></p><span class="glyphicon glyphicon-cog"></span><br>Profile Settings<p>&nbsp;</p>',
        [
            'controller' => 'Dashboard',
            'action' => 'profileSettings'
        ],
        [
            'class' => 'blue button',
            'title' => 'Profile Settings',
            'escape' => false
        ]
    );
    if ($user->user_role_id == 2 || $user->is_admin) {
        echo $this->Html->link(
            '<p></p><span class="glyphicon glyphicon-user"></span><br>Contributor Network<p>&nbsp;</p>',
            [
                'controller' => 'Dashboard',
                'action' => 'contributorNetwork'
            ],
            [
                'class' => 'blue button',
                'title' => 'Contributor Network',
                'escape' => false
            ]
        );
    }
    ?>
</div>
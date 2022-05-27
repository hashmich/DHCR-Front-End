<?php

declare(strict_types=1);

namespace App\Controller;

use Authentication\IdentityInterface;
use Cake\Event\EventInterface;
use Cake\Core\Configure;

class DashboardController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);
        $this->viewBuilder()->setLayout('contributors');
    }

    private function getPendingAccountRequests(IdentityInterface $user)
    {
        $this->loadModel('Users');
        $pendingAccountRequests = 0;
        if ($user->is_admin) {
            $pendingAccountRequests = $this->Users->find()->where([
                'approved' => 0,
                'active' => 1,
            ])
                ->count();
        } elseif ($user->user_role_id == 2) {
            $pendingAccountRequests = $this->Users->find()->where([
                'approved' => 0,
                'active' => 1,
                'country_id' => $user->country_id,
            ])
                ->count();
        }
        return $pendingAccountRequests;
    }

    private function getPendingCourseRequests(IdentityInterface $user)
    {
        $this->loadModel('DhcrCore.Courses');
        $pendingCourseRequests = 0;
        if ($user->is_admin) {
            $pendingCourseRequests = $this->Courses->find()->where([
                'approved' => 0,
                'active' => 1,
                'deleted' => 0,
                'updated >' => Configure::read('courseArchiveDate')
            ])
                ->count();
        } elseif ($user->user_role_id == 2) {
            $pendingCourseRequests = $this->Courses->find()->where([
                'approved' => 0,
                'active' => 1,
                'deleted' => 0,
                'updated >' => Configure::read('courseArchiveDate'),
                'country_id' => $user->country_id
            ])->count();
        }
        return $pendingCourseRequests;
    }

    private function getExpiredCourses(IdentityInterface $user)
    {
        $this->loadModel('DhcrCore.Courses');
        if ($user->is_admin) {
            $expiredCourses = $this->Courses->find()->where([
                'active' => 1,
                'deleted' => 0,
                'updated <' => Configure::read('courseYellowDate'),
                'updated >' => Configure::read('courseArchiveDate')
            ])
                ->count();
        } elseif ($user->user_role_id == 2) {
            $expiredCourses = $this->Courses->find()->where([
                'active' => 1,
                'deleted' => 0,
                'updated <' => Configure::read('courseYellowDate'),
                'updated >' => Configure::read('courseArchiveDate'),
                'country_id' => $user->country_id
            ])
                ->count();
        } else {
            $expiredCourses = $this->Courses->find()->where([
                'active' => 1,
                'deleted' => 0,
                'updated <' => Configure::read('courseYellowDate'),
                'updated >' => Configure::read('courseArchiveDate'),
                'user_id' => $user->id
            ])
                ->count();
        }
        return $expiredCourses;
    }

    public function index()
    {
        $this->loadModel('Users');
        $user = $this->Authentication->getIdentity();
        $newsletterSubscription = $this->Users->get($user->id)->mail_list;
        $this->Authorization->authorize($user, 'accessDashboard');
        // $identity = $this->_checkExternalIdentity();
        $pendingAccountRequests = $this->getPendingAccountRequests($user);
        $pendingCourseRequests = $this->getPendingCourseRequests($user);
        $expiredCourses = $this->getExpiredCourses($user);
        $totalNeedsattention = $pendingAccountRequests + $pendingCourseRequests + $expiredCourses;
        $this->set(compact('user')); // required for contributors menu
        $this->set(compact('newsletterSubscription', 'pendingAccountRequests', 'pendingCourseRequests', 'expiredCourses', 'totalNeedsattention'));
    }

    public function needsAttention()
    {
        $user = $this->Authentication->getIdentity();
        // Set breadcrums
        $breadcrumTitles[0] = 'Needs Attention';
        $breadcrumControllers[0] = 'Dashboard';
        $breadcrumActions[0] = 'needsAttention';
        $this->set((compact('breadcrumTitles', 'breadcrumControllers', 'breadcrumActions')));
        $pendingAccountRequests = $this->getPendingAccountRequests($user);
        $pendingCourseRequests = $this->getPendingCourseRequests($user);
        $expiredCourses = $this->getExpiredCourses($user);
        $this->set(compact('user')); // required for contributors menu
        $this->set(compact('pendingAccountRequests', 'pendingCourseRequests', 'expiredCourses'));
    }

    public function adminCourses()
    {
        $user = $this->Authentication->getIdentity();
        $this->loadModel('DhcrCore.Courses');
        // Set breadcrums
        $breadcrumTitles[0] = 'Administrate Courses';
        $breadcrumControllers[0] = 'Dashboard';
        $breadcrumActions[0] = 'courses';
        $this->set((compact('breadcrumTitles', 'breadcrumControllers', 'breadcrumActions')));
        $myCoursesCount = $this->Courses->find()->where([
            'deleted' => 0,
            'updated >' => Configure::read('courseArchiveDate'),
            'user_id' => $user->id
        ])
            ->count();
        if ($user->user_role_id == 2) {
            $moderatedCoursesCount = $this->Courses->find()->where([
                'approved' => 1,
                'active' => 1,
                'deleted' => 0,
                'updated >' => Configure::read('courseArchiveDate'),
                'country_id' => $user->country_id,
            ])
                ->count();
        } else {
            $moderatedCoursesCount = 0;
        }
        if ($user->is_admin) {
            $allCoursesCount = $this->Courses->find()->where([
                'deleted' => 0,
                'updated >' => Configure::read('courseArchiveDate'),
            ])
                ->count();
        } else {
            $allCoursesCount = 0;
        }
        $this->set(compact('user')); // required for contributors menu
        $this->set(compact('myCoursesCount', 'moderatedCoursesCount', 'allCoursesCount'));
    }

    public function contributorNetwork()
    {
        $user = $this->Authentication->getIdentity();
        $this->loadModel('Users');
        // Set breadcrums
        $breadcrumTitles[0] = 'Contributor Network';
        $breadcrumControllers[0] = 'Dashboard';
        $breadcrumActions[0] = 'contributorNetwork';
        $this->set((compact('breadcrumTitles', 'breadcrumControllers', 'breadcrumActions')));
        if ($user->user_role_id == 2) {
            $moderatedUsersCount = $this->Users->find()->where([
                'approved' => 1,
                'active' => 1,
                'country_id' => $user->country_id
            ])
                ->count();
        } else {
            $moderatedUsersCount = 0;
        }
        if ($user->is_admin) {
            $allUsersCount = $this->Users->find()->count();
        } else {
            $allUsersCount = 0;
        }
        if ((!$user->is_admin && !($user->user_role_id == 2))) {
            $this->Flash->error(__('Not authorized to contributor network'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
        $this->set(compact('user')); // required for contributors menu
        $this->set(compact('moderatedUsersCount', 'allUsersCount'));
    }

    public function categoryLists()
    {
        $user = $this->Authentication->getIdentity();
        $this->loadModel('Cities');
        $this->loadModel('Institutions');
        $this->loadModel('Languages');
        $this->loadModel('InviteTranslations');
        // Set breadcrums
        $breadcrumTitles[0] = 'Category Lists';
        $breadcrumControllers[0] = 'Dashboard';
        $breadcrumActions[0] = 'categoryLists';
        $this->set((compact('breadcrumTitles', 'breadcrumControllers', 'breadcrumActions')));
        if (!($user->user_role_id == 2 || $user->is_admin)) {
            $this->Flash->error(__('Not authorized to category lists'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
        $totalCities = $this->Cities->find()->count();
        $totalInstitutions = $this->Institutions->find()->count();
        if ($user->is_admin) {
            $totalLanguages = $this->Languages->find()->count();
            $totalInviteTranslations = $this->InviteTranslations->find()->count();
        } else {
            $totalLanguages = 0;
            $totalInviteTranslations = 0;
        }
        $this->set(compact('user')); // required for contributors menu
        $this->set(compact('totalCities', 'totalInstitutions', 'totalLanguages', 'totalInviteTranslations'));
    }

    public function profileSettings()
    {
        $user = $this->Authentication->getIdentity();
        // Set breadcrums
        $breadcrumTitles[0] = 'Profile Settings';
        $breadcrumControllers[0] = 'Dashboard';
        $breadcrumActions[0] = 'profileSettings';
        $this->set(compact('user')); // required for contributors menu
        $this->set(compact('breadcrumTitles', 'breadcrumControllers', 'breadcrumActions'));
    }

    public function help()
    {
        $user = $this->Authentication->getIdentity();
        // Set breadcrums
        $breadcrumTitles[0] = 'Help';
        $breadcrumControllers[0] = 'Dashboard';
        $breadcrumActions[0] = 'help';
        $this->set(compact('user')); // required for contributors menu
        $this->set(compact('breadcrumTitles', 'breadcrumControllers', 'breadcrumActions'));
    }
}

<?php

namespace App\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;


class CoursesController extends AppController
{

    public $modelClass = 'DhcrCore.Courses';

    public $Courses = null;


    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['index', 'view']);
        $this->Authorization->skipAuthorization();
    }

    public function index()
    {
        // moved from beforeRender
        $this->viewBuilder()->setLayout('home');

        $query = $this->request->getQuery();
        if (!isset($query['recent']) or ($query['recent'] !== false and $query['recent'] != 'false'))
            $query['recent'] = true;
        if (!isset($query['sort']))
            $query['sort'] = 'Courses.updated:desc';
        else
            $query['sort'] .= ',Courses.updated:desc';

        // load models from plugin
        $this->loadModel('DhcrCore.Courses');
        $this->loadModel('DhcrCore.Countries');
        $this->loadModel('DhcrCore.Cities');
        $this->loadModel('DhcrCore.Institutions');
        $this->loadModel('DhcrCore.Languages');
        $this->loadModel('DhcrCore.CourseTypes');
        $this->loadModel('DhcrCore.Disciplines');
        $this->loadModel('DhcrCore.TadirahTechniques');
        $this->loadModel('DhcrCore.TadirahObjects');

        $this->Courses->evaluateQuery($query);
        $courses = $this->Courses->getResults();

        // get filter option lists
        $this->Countries->evaluateQuery(['count_recent' => true]);
        $countries = $this->Countries->getCountries();

        $citiesQuery = ['count_recent' => true, 'group' => true];
        if (!empty($query['country_id'])) $citiesQuery['country_id'] = $query['country_id'];
        $this->Cities->evaluateQuery($citiesQuery);
        $cities = $this->Cities->getCities();

        $institutionsQuery = ['count_recent' => true, 'group' => true];
        if (!empty($query['country_id'])) $institutionsQuery['country_id'] = $query['country_id'];
        $this->Institutions->evaluateQuery($institutionsQuery);
        $institutions = $this->Institutions->getInstitutions();

        $this->CourseTypes->evaluateQuery(['count_recent' => true]);
        $types = $this->CourseTypes->getCourseTypes();

        $this->Languages->evaluateQuery(['count_recent' => true]);
        $languages = $this->Languages->getLanguages();

        $this->Disciplines->evaluateQuery(['count_recent' => true]);
        $disciplines = $this->Disciplines->getDisciplines();

        $this->TadirahTechniques->evaluateQuery(['count_recent' => true]);
        $techniques = $this->TadirahTechniques->getTadirahTechniques();

        $this->TadirahObjects->evaluateQuery(['count_recent' => true]);
        $objects = $this->TadirahObjects->getTadirahObjects();

        $this->set(compact(
            'courses',
            'countries',
            'cities',
            'institutions',
            'types',
            'languages',
            'disciplines',
            'techniques',
            'objects'
        ));
    }


    public function view($id = null)
    {
        // moved from beforeRender
        $this->viewBuilder()->setLayout('home');

        $this->loadModel('DhcrCore.Courses');
        $course = $this->Courses->get($id, [
            'contain' => $this->Courses->containments,
            'conditions' => [
                'Courses.active' => true
            ]
        ]);

        if (empty($course)) {
            throw new RecordNotFoundException();
        }

        $this->set('course', $course);
        $this->render('index');
    }

    public function add()
    {
        $this->viewBuilder()->setLayout('contributors');
        $this->loadModel('DhcrCore.Courses');

        // Set breadcrums
        $breadcrumTitles[0] = 'Administrate Courses';
        $breadcrumControllers[0] = 'Dashboard';
        $breadcrumActions[0] = 'adminCourses';
        $breadcrumTitles[1] = 'Add Course';
        $breadcrumControllers[1] = 'Courses';
        $breadcrumActions[1] = 'add';
        $this->set((compact('breadcrumTitles', 'breadcrumControllers', 'breadcrumActions')));

        // $user = $this->Authentication->getIdentity();

        $course = $this->Courses->newEmptyEntity();
        if ($this->request->is('post')) {
            $course = $this->Courses->patchEntity($course, $this->request->getData());
            if ($this->Courses->save($course)) {
                $this->Flash->success(__('The course has been saved.'));

                return $this->redirect(['controller' => 'Dashboard', 'action' => 'adminCourses']);
            }
            $this->Flash->error(__('The course could not be saved. Please, try again.'));
        }
        $this->set(compact('course'));
    }

    public function myCourses()
    {
        $this->viewBuilder()->setLayout('contributors');
        $this->loadModel('Courses');

        // Set breadcrums
        $breadcrumTitles[0] = 'Administrate Courses';
        $breadcrumControllers[0] = 'Dashboard';
        $breadcrumActions[0] = 'adminCourses';
        $breadcrumTitles[1] = 'My Courses';
        $breadcrumControllers[1] = 'Courses';
        $breadcrumActions[1] = 'myCourses';
        $this->set((compact('breadcrumTitles', 'breadcrumControllers', 'breadcrumActions')));

        $user = $this->Authentication->getIdentity();

        $courses = $this->paginate($this->Courses->find('all')->where(['user_id' => $user->id]));
        $this->set(compact('user', 'courses'));
    }

    public function courseApproval()
    {
        $this->viewBuilder()->setLayout('contributors');
        $this->loadModel('Courses');

        // Set breadcrums
        $breadcrumTitles[0] = 'Needs Attention';
        $breadcrumControllers[0] = 'Dashboard';
        $breadcrumActions[0] = 'needsAttention';
        $breadcrumTitles[1] = 'Course Approval';
        $breadcrumControllers[1] = 'Courses';
        $breadcrumActions[1] = 'courseApproval';
        $this->set((compact('breadcrumTitles', 'breadcrumControllers', 'breadcrumActions')));

        $user = $this->Authentication->getIdentity();

        $courses = $this->Courses->find()
            ->contain(['Institutions'])
            ->where(['approved' => 0])
            ->order(['Courses.created' => 'desc'])
            ->toList();

        $this->set(compact('user', 'courses'));
    }
}

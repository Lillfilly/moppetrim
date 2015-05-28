<?php
namespace Anax\Users;
 
/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
    * Initialize the controller.
    *
    * @return void
    */
    public function initialize()
    {
	$this->userModel = new \Anax\Users\Users();
	$this->userModel->setDI($this->di);
    }

    public function indexAction()
    {
	$users = $this->userModel->queryUsersWithScore()->execute();

	$this->theme->setTitle("Användare");
	$this->views->add('users/view-all', [
	    'title'	=> 'Användare',
	    'users'	=> $users,
	]);
    }

    /**
    * List user with id.
    *
    * @param int $id of user to display
    *
    * @return void
    */
    public function idAction($id = null)
    {
	if(!isset($id)){
	    $this->response->redirect($this->url->create('users'));
	}
	$params = [$id,];
	$user = $this->userModel->queryUsersWithScore()->where('id = ?')->execute($params);

	if(count($user) != 1){
	    $this->response->redirect($this->url->create('users'));
	}
	$user = $user[0];

	$questions = $this->userModel->getQuestionsForUser($id);
	$answers = $this->userModel->getQuestionsAnsweredByUser($id);
	$comments = $this->userModel->getCommentsByUser($id)->amount;

	$this->theme->setTitle($user->getProperties()['name']);

	if(null !== $this->session->get('user') && $this->session->get('user')->id == $id){
	    $this->views->addString('<div class="clearfix"><a href="' . $this->url->create('users/edit/' . $id) .'">Editera din profil</a></div>');
	}
	$this->views->add('users/user', [
	    'user'	=> $user,
	]);
	$this->views->add('users/userquestions', [
	    'header'	=> 'Frågor som den här användaren har ställt',
	    'questions'	=> $questions,
	    'answers'   => $answers,
	    'comments'  => $comments,
	]);
    }

    public function loginAction(){
	$form = $this->form->create([], [
    	    'email' => [
                'type'        => 'text',
                'label'       => 'Epost:',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
            ],
    	    'password' => [
                'type'        => 'password',
                'label'       => 'Lösenord:',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
    	    'submit' => [
                'type'      => 'submit',
                'callback'  => function ($form) {
		    $result = $this->userModel->login($form->value('email'), $form->value('password'));
		    if($result){
			return true;
		    }else{
			return false;
		    }
                }
            ],
	]);

	$this->theme->setTitle("Logga in");
	$this->views->addString('<h3>Logga in</h3>');
	$this->views->addString($form->getHTML());

	$form->check(
	    function ($form){
		$this->response->redirect($this->url->create('users/id/' . $this->session->get('user')->getProperties()['id']));
    	    },
    	    function ($form){
		$this->views->addString("Inget konto med den Epost-Lösenord kombinationen kunde hittas");
    	    }
    	);
	$this->views->addString('<p><a href="'.$this->url->create('users/signup').'">Skapa ett konto</a></p>');
    }

    public function logoutAction(){
	$this->userModel->logout();
	$this->response->redirect($this->url->create(''));
    }

    /**
     * Add new user.
     *
     * @param string $acronym of user to add.
     *
     * @return void
     */
    public function signupAction()
    {
	if($this->session->get('user') != NULL){
	    $this->response->redirect($this->url->create('users/id/' . $this->session->get('user')->getProperties()['id']));
	}
	$form = $this->form->create([], [
	    'email' => [
                'type'        => 'email',
                'label'       => 'Email:',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress'],
            ],
	    'name' => [
                'type'        => 'text',
                'label'       => 'Name:',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
    	    'password' => [
                'type'        => 'password',
                'label'       => 'Password:',
                'required'    => true,
                'validation'  => ['not_empty'],
            ],
    	    'submit' => [
                'type'      => 'submit',
                'callback'  => function ($form) {
                    return true;
                }
            ],
	]);
        $form->check(
	    function ($form){
		$this->userModel->signup($form->value('email'), $form->value('name'), $form->value('password'));
    		$this->response->redirect($this->url->create('users/id/' . $this->session->get('user')->getProperties()['id']));
    	    },
    	    function ($form){
    		$form->AddOutput('Something went wrong, check your credentials and try again.');
    	    }
        );
	$this->theme->setTitle("Skapa konto");
	$this->views->addString('<h2>Skapa ett konto</h2>');
        $this->views->addString($form->getHTML());
    }
    /**
     * Edit user.
     *
     * @param integer $id of user to edit.
     *
     * @return void
     */
    public function editAction($id = NULL)
    {
	$user = $this->session->get('user');

	if(!isset($id) || !isset($user) || $user->id != $id){
	    $url = $this->url->create('users');
	    $this->response->redirect($url);
	}

	$form = $this->form->create([], [
	    'name' => [
                'type'        => 'text',
                'label'       => 'Ändra ditt namn:',
                'required'    => true,
                'validation'  => ['not_empty'],
		'value'	      => $user->getProperties()['name'],
            ],
    	    'submit' => [
                'type'      => 'submit',
                'callback'  => function ($form) {
                    return true;
                }
            ],
	]);
        $form->check(
	    function ($form){
		$user = $this->session->get('user');
		$this->userModel->updateUser($form->value('name'), $user->id);
    		$this->response->redirect($this->url->create('users/id/' . $this->session->get('user')->getProperties()['id']));
    	    },
    	    function ($form){
    		$form->AddOutput('Something went wrong, check your credentials and try again.');
    	    }
        );
	$this->theme->setTitle('Ändra profil');
	$this->views->addString('<h2>Ändra profil</h2>');
        $this->views->addString($form->getHTML());
    }

    /**
     * Delete (soft) user.
     *
     * @param integer $id of user to delete.
     *
     * @return void
     */
    public function softDeleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }

        $now = gmdate('Y-m-d H:i:s');

        $user = $this->users->find($id);

        $user->deleted = $now;
        $user->save();
     
        $url = $this->url->create('dbuser.php/users/list/');
        $this->response->redirect($url);
    }
    public function undoDeleteAction($id = null)
    {
        if (!isset($id)) {
            die("Missing id");
        }
        $user = $this->users->find($id);

        $user->deleted = NULL;
        $user->save();

	$url = $this->url->create('dbuser.php/users/list/trash');
        $this->response->redirect($url);
    }     
    /**
    * List all active and not deleted users.
    *
    * @return void
    */
    public function activeAction()
    {
        $all = $this->users->query()
            ->where('active IS NOT NULL')
            ->andWhere('deleted is NULL')
            ->execute();

        $this->theme->setTitle("Users that are active");
        $this->views->add('users/list-all', [
            'users' => $all,
            'title' => "Users that are active",
        ]);
    }

    public function getMostActiveUsersAction(){
	$users = $this->userModel->getMostActiveUsers()->execute();
	$this->views->add('users/view-all', [
	    'title'	=> 'Mest aktiva användare',
	    'users'	=> $users,
	]);
    }
}
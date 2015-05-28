<?php

namespace Anax\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;
    public function initialize(){
    }
    private function getForm($comment = null){
    	$content = '';
	$mail = '';
	$name = '';
	$web = '';
	$id = '';
	$pageid = '';
	$removebutton = false;
	if(isset($comment) && $comment != false){
	    $content = $comment->content;
	    $mail = $comment->mail;
	    $name = $comment->name;
	    $web = $comment->web;
	    $id = $comment->id;
	    $pageid = $comment->pageid;
	    $removebutton = true;
	}
	$fields = [
	    'commentId' => [
		'type'        => 'hidden',
		'value'       => $id,
	    ],
	    'pageid' => [
		'type'	      => 'hidden',
		'value'	      => $pageid,
	    ],
	    'content' => [
        	'type'        => 'text',
        	'label'       => 'Kommentar:',
            	'required'    => true,
            	'validation'  => ['not_empty'],
		'value'       => $content,
    	    ],
            'mail' => [
            	'type'        => 'email',
            	'label'       => 'Email:',
            	'required'    => true,
            	'validation'  => ['email_adress'],
		'value'       => $mail,
        	],
            'name' => [
            	'type'        => 'text',
            	'label'       => 'Name:',
            	'required'    => true,
            	'validation'  => ['not_empty'],
		'value'       => $name,
    	    ],
            'web' => [
    		'type'        => 'url',
        	'label'       => 'Webbplats:',
        	'required'    => true,
        	'validation'  => [],
		'value'       => $web,
    	    ],
            'submit' => [
    		'type'      => 'submit',
	    	'callback'  => function ($editForm) {
            	    return true;
        	}
    	    ],
	];
	if($removebutton){
	    $fields['remove'] = [
		'type'      => 'submit',
		'value'     => 'Remove',
	    	'callback'  => function ($editForm) {
		    return true;
        	}
	    ];
	}
	return $this->form->create([], $fields);
    }
    /**
     * View all comments.
     *
     * @return void
     */
    public function viewAction()
    {
	$pageKey = $this->request->getRoute();

	$comments = new \Anax\Comment\CommentsInDatabase();
	$comments->setDI($this->di);

	$all = $comments->findAll($pageKey);

	$this->views->add('comment/comments', [
	    'comments' => $all,
	    'page' => $pageKey,
    	]);
    }

    /**
    *	View the write comment form
    *
    * @return void
    */
    public function formAction(){	
	$editForm = $this->getForm();
    	$editForm->check(
	    function ($editForm){
		$comment = [
		    'content' => $form->value('content'),
		    'mail' => $form->value('mail'),
		    'name' => $form->value('name'),
		    'web' => $form->value('web'),
		    'created' => time(),
		    'ip' => $this->request->getServer('REMOTE_ADDR'),
		];
		$this->dispatcher->forward([
		    'controller' => 'comment',
		    'action'     => 'add',
		    'params'     => [
			'comment' => $comment,
		    ],
		]);
    	    },
    	    function ($editForm){
    		$editForm->AddOutput('Something went wrong, check your credentials and try again.');
    	    }
    	);
    	$this->views->addString($editForm->getHTML(['fieldset' => false]));
    }

    /**
     * Add a comment.
     *
     * @return void
     */
    public function addAction($comment = null)
    {
	if(!isset($comment) || !is_array($comment)){
	    die("No comment");
	}

        $comments = new \Anax\Comment\CommentsInDatabase();
        $comments->setDI($this->di);

	$route = $this->request->getRoute();
        $comments->add($comment, $route);
        $this->response->redirect($route);
    }

    /**
    * Edit a comment
    *
    * @return void
    */
    public function editAction()
    {
	$id = $this->request->getPost('commentId');
	$pageKey = $this->request->getPost('page');
	if($id == false){
	    $id = NULL;
	    $pageKey = NULL;
	}
	
	$comments = new \Anax\Comment\CommentsInDatabase();
	$comments->setDI($this->di);
	$editForm = $this->getForm($comments->findComment($id));
    	$editForm->check(
	    function ($editForm) use ($comments){
		$comment = [
		    'id'      => $editForm->value('commentId'),
		    'content' => $editForm->value('content'),
		    'mail' => $editForm->value('mail'),
		    'name' => $editForm->value('name'),
		    'web' => $editForm->value('web'),
		    'pageid' => $editForm->value('pageid'),
		    'created' => time(),
		    'ip' => $this->request->getServer('REMOTE_ADDR'),
		];

		if($this->request->getPost('submit')){
		    $comments->edit($comment);
		}else if($this->request->getPost('remove')){
		    $comments->remove($comment['id']);
		}
		$this->response->redirect($this->url->create($editForm->value('pageid')));
    	    },
    	    function ($editForm) use ($comments){
    	    }
    	);
	$this->theme->setTitle("Editera kommentar");
    	$this->views->addString($editForm->getHTML(['fieldset' => false]));
    }

    /**
    *  Reset database comments
    *
    * @return void
    */
    public function resetAction()
    {
	$comments = new \Anax\Comment\CommentsInDatabase();
	$comments->setDI($this->di);
	$comments->resetDatabase();
	$this->views->addString('Databasen resettad', 'main');
    }
}

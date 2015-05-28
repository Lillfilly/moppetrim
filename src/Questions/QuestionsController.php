<?php
namespace Anax\Questions;
 
class QuestionsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
    * Initialize the controller.
    *
    * @return void
    */
    public function initialize()
    {
	$this->qModel = new \Anax\Questions\Questions();
	$this->qModel->setDI($this->di);
    }

    public function indexAction()
    {
	$user = $this->session->get('user');
	if(isset($_GET['vote']) && isset($_GET['type']) && isset($_GET['id'])){
	    $vote = strtoupper($_GET['vote']);
	    if($_GET['type'] == 'q'){
		$this->qModel->questionVote($user->getProperties()['id'], $_GET['id'], $vote);

		if(isset($_GET['question'])){
		    $this->response->redirect($this->url->create('questions/id/' . $_GET['id']));
		}else{
		    $this->response->redirect($this->url->create('questions'));
		}
	    }
	    if($_GET['type'] == 'a'){
		$this->qModel->answerVote($user->getProperties()['id'], $_GET['id'], $vote);
		$this->response->redirect($this->url->create('questions/id/' . $_GET['question']));
	    }
	    if($_GET['type'] == 'c'){
		$this->qModel->commentVote($user->getProperties()['id'], $_GET['id'], $vote);
		$this->response->redirect($this->url->create('questions/id/' . $_GET['question']));
	    }
	}


	$order = "Q.created";
	if(isset($_GET['sort']) && $_GET['sort'] == 'rank'){
	    $order = 'rank';
	}
	$questions = $this->qModel->findAllQuestions($order);

	$this->theme->setTitle("Frågor");
	$createLink = "";
	if($user != NULL){
	    $createLink = '<a href="' . $this->url->create('questions/create') . '">Ställ en fråga</a>';
	}

	foreach($questions as $q){
	    $q->tags = $this->qModel->getTagsForQuestion($q->id);

	    $q->allowVotes = false;
	    $user = $this->session->get('user');
	    if($user != NULL){
		if($user->getProperties()['id'] != $q->userId && $this->qModel->questionAlreadyVoted($user->getProperties()['id'], $q->id) == 0){
		    $q->allowVotes = true;
		}
	    }
	}

	$this->views->add('questions/index',[
	    'questions'	=>	$questions,
	    'createLink' => 	$createLink,
	    'pageHeader' =>	'Frågor',
	]);
	$this->views->addString('<a href="?sort=date">Sortera efter datum</a> <a href="?sort=rank">Sortera efter rank</a>');
    }

    public function createAction()
    {
	if($this->session->get('user') == NULL){
	    $this->response->redirect($this->url->create('questions'));
	}

	$fields = [
	    'header' => [
    		'type'        => 'text',
        	'label'       => 'Din fråga (kort och on-point):',
        	'required'    => true,
        	'validation'  => [],
		'value'       => '',
    	    ],
	    'question' => [
		'type'        => 'textarea',
        	'label'       => 'Din fråga mer detaljerat (markdown):',
        	'required'    => true,
        	'validation'  => [],
		'value'       => '',
	    ],
	    'tags' => [
		'type'        => 'text',
        	'label'       => 'Lägg till taggar (separerade med kommatecken):',
        	'required'    => false,
        	'validation'  => [],
		'value'       => '',
	    ],
            'submit' => [
    		'type'      => 'submit',
	    	'callback'  => function ($form) {
            	    return true;
        	}
    	    ],
	];
	$form = $this->form->create([], $fields);

	$form->check(
	    function($form){
		$header = $form->value('header');
		$question = $form->value('question');
		$question = $this->textFilter->doFilter($question, 'shortcode, markdown');
		$tags = [];
		if(!empty(trim($form->value('tags')))){
		    $tags = explode(',', trim($form->value('tags')));
		}
		$id = $this->qModel->addQuestion($header, $question, $tags);
		$this->response->redirect($this->url->create('questions/id/' . $id));
	    },
	    function($form){}
	);

	$this->theme->setTitle("Ställ en fråga");
	$this->views->addString("<h2>Ställ en fråga</h2>");
	$this->views->addString($form->getHTML());
    }

    public function idAction($id = NULL){
	if(!isset($id) || !is_numeric($id)){
	    $this->response->redirect($this->url->create('questions'));
	}
	
	$user = $this->session->get('user');
	$question = $this->qModel->getQuestionWithId($id)[0];
	$question->tags = $this->qModel->getTagsForQuestion($id);
	$answers = $this->qModel->getAnswersForQuestion($id);

	$this->theme->setTitle($question->header);
	$this->views->addString('<h2>'.$question->header.'</h2>');

	$allowVotes = false;
	if($user != NULL){
	    if($user->getProperties()['id'] != $question->userId && $this->qModel->questionAlreadyVoted($user->getProperties()['id'], $id) == 0){
		$allowVotes = true;
	    }
	}

	$this->views->add('questions/questiondetails', [
	    'question'	=>	$question,
	    'allowVotes' => 	$allowVotes,
	]);

	$this->views->addString('<h3>Svar:</h3>');
	
	if($this->session->get('user')){
	    $url = $this->url->create('questions/answer/' . $id);
	    $this->views->addString('<p><a href="'.$url.'">Skriv ett svar på frågan</a></p>');
	}

	$showAccept = false;
	if($user != NULL){
	    if($user->getProperties()['id'] == $id){
		$showAccept = true;
	    }
	    foreach($answers as $a){
		if($a->accepted == 'TRUE'){
		    $showAccept = false;
		}
		$a->allowVotes = false;
		if($user->getProperties()['id'] != $a->userId && $this->qModel->answerAlreadyVoted($user->getProperties()['id'], $a->id) == 0){
		    $a->allowVotes = true;
		}
	    }
	}
	if($showAccept && isset($_GET['accept']) && is_numeric($_GET['accept'])){
	    $this->qModel->acceptAnswer($_GET['accept']);
	    $this->response->redirect($this->url->create('questions/id/' . $id));
	}

	foreach($answers as $answer){
	    $comments = $this->qModel->getCommentsForAnswer($answer->id);
	    if($user != NULL){
		foreach($comments as $comment){
		    if($user->getProperties()['id'] != $comment->userId && $this->qModel->commentAlreadyVoted($user->getProperties()['id'], $comment->id) == 0){
			$comment->allowVotes = true;
		    }else{
			$comment->allowVotes = false;
		    }
		}
	    }

	    $this->views->add('questions/answer', [
		'answer'	=> $answer,
		'comments'	=> $comments,
		'showAccept'	=> $showAccept,
		'questionId'	=> $id,
	    ]);
	}
    }
    public function answerAction($id){
	if($this->session->get('user') == NULL || !isset($id)){
	    $this->response->redirect($this->url->create('questions'));
	}

	$fields = [
	    'answer' => [
		'type'        => 'textarea',
        	'label'       => 'Skriv ditt svar. (markdown):',
        	'required'    => true,
        	'validation'  => [],
		'value'       => '',
	    ],
            'submit' => [
    		'type'      => 'submit',
	    	'callback'  => function ($form) {
            	    return true;
        	}
    	    ],
	];
	$form = $this->form->create([], $fields);

	$form->check(
	    function($form) use ($id){
		$answer = $form->value('answer');
		$answer = $this->textFilter->doFilter($answer, 'shortcode, markdown');
		$this->qModel->addAnswer($id, $answer);
		$this->response->redirect($this->url->create('questions/id/' . $id));
	    },
	    function($form){}
	);

	$this->theme->setTitle("Svara på fråga");
	$this->views->addString("<h2>Svara på frågan</h2>");
	$this->views->addString($form->getHTML());
    }
    public function commentAction($id){
	if($this->session->get('user') == NULL || !isset($id)){
	    $this->response->redirect($this->url->create('questions'));
	}

	$fields = [
	    'comment' => [
		'type'        => 'textarea',
        	'label'       => 'Skriv din kommentar. (markdown):',
        	'required'    => true,
        	'validation'  => [],
		'value'       => '',
	    ],
            'submit' => [
    		'type'      => 'submit',
	    	'callback'  => function ($form) {
            	    return true;
        	}
    	    ],
	];
	$form = $this->form->create([], $fields);

	$form->check(
	    function($form) use ($id){
		$comment = $form->value('comment');
		$comment = $this->textFilter->doFilter($comment, 'shortcode, markdown');
		$commentId = $this->qModel->addComment($id, $comment);
		$qId = $this->qModel->getQuestionForComment($commentId);

		$this->response->redirect($this->url->create('questions/id/' . $qId));
	    },
	    function($form){}
	);

	$this->theme->setTitle("Kommentera svar");
	$this->views->addString("<h2>Kommentera svaret</h2>");
	$this->views->addString($form->getHTML());
    }


    public function getLatestQuestionsAction(){
	$questions = $this->qModel->getLatestQuestions();

	$createLink = "";
	if($this->session->get('user') != NULL){
	    $createLink = '<a href="' . $this->url->create('questions/create') . '">Ställ en fråga</a>';
	}

	foreach($questions as $q){
	    $q->tags = $this->qModel->getTagsForQuestion($q->id);
	    
	    $q->allowVotes = false;
	    $user = $this->session->get('user');
	    if($user != NULL){
		if($user->getProperties()['id'] != $q->id && $this->qModel->questionAlreadyVoted($user->getProperties()['id'], $q->id) == 0){
		    $q->allowVotes = true;
		}
	    }
	}

	$this->views->add('questions/index',[
	    'questions'	=>	$questions,
	    'createLink' => 	$createLink,
	    'pageHeader' =>	'Senaste frågorna',
	]);
    }
}
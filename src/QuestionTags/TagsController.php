<?php
namespace Anax\QuestionTags;
 
class TagsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
    * Initialize the controller.
    *
    * @return void
    */
    public function initialize()
    {
	$this->tagModel = new \Anax\QuestionTags\QuestionTags();
	$this->tagModel->setDI($this->di);
    }

    public function indexAction()
    {
	$tags = $this->tagModel->findUnique();
	
	$this->theme->setTitle("Taggar");
	$this->views->addString("<h2>Taggar</h2>");

	foreach($tags as $tag){
	    $this->views->add('questions/tag', [
		'tag' =>	$tag,
	    ]);
	}
    }

    public function filterAction($tag = NULL){
	if(!isset($tag)){
	    $this->response->redirect($this->url->create('tags'));
	}
	$questions = $this->tagModel->getQuestionsForTag($tag);
	
	foreach($questions as $q){
	    $q->tags = $this->tagModel->getTagsForQuestion($q->id);
	}

	$this->theme->setTitle($tag);
	$this->views->add('questions/filter',[
	    'tag'	=>	$tag,
	    'questions'	=>	$questions,
	]);
    }

    public function mostPopularTagsAction(){
	$tags = $this->tagModel->findPopular();
	
	$this->views->addString("<h2>Populära taggar</h2>");

	foreach($tags as $tag){
	    $this->views->add('questions/tag', [
		'tag' =>	$tag,
	    ]);
	}
    }
}
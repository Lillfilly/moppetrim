<?php

namespace Anax\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentsInSession implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
     * Add a new comment.
     *
     * @param array $comment with all details.
     * 
     * @return void
     */
    public function add($comment, $pageKey)
    {
        $comments = $this->session->get("comments-{$pageKey}", []);
        $comments[] = $comment;
        $this->session->set("comments-{$pageKey}", $comments);
    }

    /**
    * Edit an existing comment.
    *
    * @param integer $id of the comment
    * @param array $comment with the edited comment  (Timestamp and Ip will not be edited)
    *
    * @return void
    */
    public function edit($id, $comment, $pageKey){
	//Get the comment from the session
	$existingComments = $this->session->get("comments-{$pageKey}", []);

	//Edit the existing comment
	$existingComments[$id]['content'] = $comment['content'];
	$existingComments[$id]['name'] = $comment['name'];
	$existingComments[$id]['web'] = $comment['web'];
	$existingComments[$id]['mail'] = $comment['mail'];
	
	$this->session->set("comments-{$pageKey}", $existingComments);
    }

    /**
    * Delete an existing comment.
    *
    * @param integer $id of the comment to be deleted
    * @return void
    */
    public function remove($id, $pageKey){
	$existingComments = $this->session->get("comments-{$pageKey}", []);
	unset($existingComments[$id]);
	$existingComments = array_values($existingComments);
	$this->session->set("comments-{$pageKey}", $existingComments);
    }

    /**
     * Find and return all comments.
     *
     * @return array with all comments.
     */
    public function findAll($pageKey)
    {
        return $this->session->get("comments-{$pageKey}", []);
    }

    /**
    * Find comment with id
    *
    * @return Array containing all fields of the comment
    */
    public function findComment($id, $pageKey){
	return $this->session->get("comments-{$pageKey}", [])[$id];
    }

    /**
     * Delete all comments.
     *
     * @return void
     */
    public function deleteAll($pageKey)
    {
        $this->session->set("comments-{$pageKey}", []);
    }
}

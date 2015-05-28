<?php

namespace Anax\Comment;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentsInDatabase implements \Anax\DI\IInjectionAware
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
	$comments = new \Anax\Comment\Comments();
	$comments->setDI($this->di);

	$comment['pageid'] = $pageKey;
	$comments->save($comment);
    }

    /**
    * Edit an existing comment.
    *
    * @param integer $id of the comment
    * @param array $comment with the edited comment  (Timestamp and Ip will not be edited)
    *
    * @return void
    */
    public function edit($comment){
	$comments = new \Anax\Comment\Comments();
	$comments->setDI($this->di);
	$comments->save($comment);
    }

    /**
    * Delete an existing comment.
    *
    * @param integer $id of the comment to be deleted
    * @return void
    */
    public function remove($id){
	$comments = new \Anax\Comment\Comments();
	$comments->setDI($this->di);
	$comments->delete($id);
    }

    /**
     * Find and return all comments.
     *
     * @return array with all comments.
     */
    public function findAll($pageKey)
    {
	$comments = new \Anax\Comment\Comments();
	$comments->setDI($this->di);
	$commentsAsArray = [];
	$all = $comments->query()->where("pageid = '{$pageKey}'")->execute();
	foreach($all as $c){
	    $commentsAsArray[] = (array)$c;
	}
	return $commentsAsArray;
    }

    /**
    * Find comment with id
    *
    * @return Array containing all fields of the comment
    */
    public function findComment($id){
	$comments = new \Anax\Comment\Comments();
	$comments->setDI($this->di);

	return $comments->find($id);
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

    public function resetDatabase()
    {
	$this->db->dropTableIfExists('comments')->execute();
	$this->db->createTable(
    	    'comments',
            [
                'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
		'content' => ['TEXT'],
		'name'    => ['varchar(80)'],
		'web'     => ['varchar(80)'],
                'mail'    => ['varchar(80)'],
                'created' => ['datetime'],
		'pageid'  => ['varchar(40)'],
		'ip'      => ['varchar(20)'],
            ]
        )->execute();

        $this->db->insert(
            'comments',
            ['content', 'name', 'web', 'mail', 'created', 'pageid', 'ip']
        );
    
        $now = gmdate('Y-m-d H:i:s');
     
        $this->db->execute([
            'Kommentar nummmer ett osv',
            'Admin 1',
            'http://www.webbsida.se',
            'mail@mail.se',
            $now,
            '',
	    '127.0.0.1'
        ]);
     
        $this->db->execute([
	    'Kommentar nummmer qsddfddasd',
            'fdasasfd',
            'http://www.webbsasdfasdfasdfaa.se',
            'abcdefg@mailasdf.se',
            $now,
            '',
	    '127.0.0.14'
        ]);
	$this->db->execute([
	    'Kommentar nummmer kommentar',
            'kommentarare',
            'http://www.wcommentfaa.se',
            'abcdefg@mailascomntdf.se',
            $now,
            'comment',
	    '127.0.0.14'
        ]);
    }
}

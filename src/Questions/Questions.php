<?php
namespace Anax\Questions;
 
/**
 * Model for Questions.
 *
 */
class Questions extends \Anax\MVC\CDatabaseModel
{
    private $questionRank = <<<EOD
	SELECT
	    (
		SELECT count(*) FROM QuestionVotes WHERE QuestionVotes.questionId = Q.id AND isUpvote = "TRUE"
	    )
	    -
	    (
		SELECT count(*) FROM QuestionVotes WHERE QuestionVotes.questionId = Q.id AND isUpvote = "FALSE"
	    )
EOD;
    private $answerRank = <<<EOD
	SELECT
	    (
		SELECT count(*) FROM AnswerVotes WHERE AnswerVotes.answerId = A.id AND isUpvote = "TRUE"
	    )
	    -
	    (
		SELECT count(*) FROM AnswerVotes WHERE AnswerVotes.answerId = A.id AND isUpvote = "FALSE"
	    )
EOD;
    private $commentRank = <<<EOD
	SELECT
	    (
		SELECT count(*) FROM CommentVotes WHERE CommentVotes.commentId = C.id AND isUpvote = "TRUE"
	    )
	    -
	    (
		SELECT count(*) FROM CommentVotes WHERE CommentVotes.commentId = C.id AND isUpvote = "FALSE"
	    )
EOD;


    public function addQuestion($header, $question, $tags){
	$this->create([
	    'header' => $header, 
	    'question' => $question,
	    'userId'	=> $this->session->get('user')->getProperties()['id'],
	]);

	$questionId = $this->db->lastInsertId();

	$sql = 'INSERT INTO QuestionTags (\'questionId\', \'tag\') VALUES (?, ?)';
	foreach($tags as $t){
	    $t = strtoupper(trim($t));
	    $params = [$questionId, $t];
	    $this->db->execute($sql, $params);
	}

	return $questionId;
    }
    public function addAnswer($id, $answer){
	$sql = 'INSERT INTO Answers(\'questionId\', \'answer\', \'userId\') VALUES (?, ?, ?)';

	$userId = $this->session->get('user')->getProperties()['id'];
	$params = [$id, $answer, $userId];
	$this->db->execute($sql, $params);
    }
    public function addComment($id, $comment){
	$sql = 'INSERT INTO Comments(\'answerId\', \'comment\', \'userId\') VALUES (?, ?, ?)';

	$userId = $this->session->get('user')->getProperties()['id'];
	$params = [$id, $comment, $userId];
	$this->db->execute($sql, $params);
	$commentId = $this->db->lastInsertId();
	return $commentId;
    }

    public function getQuestionForComment($commentId){
	$sql = 'SELECT A.questionId FROM Comments AS C INNER JOIN Answers AS A ON C.answerId = A.id WHERE C.id = ?';
	$params = [$commentId];
	return $this->db->executeFetchAll($sql, $params)[0]->questionId;
    }

    public function getTagsForQuestion($questionId){
	$sql = 'SELECT tag FROM QuestionTags AS T INNER JOIN Questions AS Q ON T.questionId = Q.id WHERE Q.id = ?';
	$params = [$questionId];

	return $this->db->executeFetchAll($sql, $params);
    }
    public function getAnswersForQuestion($questionId){
	$sql = 'SELECT Q.id, U.name, A.answer, A.accepted, A.userId, A.id, ('.$this->answerRank.') AS rank FROM Answers AS A INNER JOIN Questions AS Q ON Q.id = A.questionId INNER JOIN Users AS U ON U.id = A.userId WHERE Q.id = ? ORDER BY A.created DESC';
	$params = [$questionId];
	return $this->db->executeFetchAll($sql, $params);
    }
    public function getCommentsForAnswer($answerId){
	$sql = 'SELECT C.id, U.name, C.comment, C.userId, ('.$this->commentRank.') AS rank FROM Comments AS C INNER JOIN Answers AS A ON A.id = C.answerId INNER JOIN Users AS U ON U.id = C.userId WHERE A.id = ? ORDER BY C.created DESC';
	$params = [$answerId];
	return $this->db->executeFetchAll($sql, $params);
    }
    public function getQuestionWithId($id){
	$answers = 'SELECT count(*) FROM Answers WHERE questionId = ?';

	$sql = 'SELECT Q.id, U.name, Q.userId, Q.header, Q.question, ('.$answers.') AS answers, ('.$this->questionRank.') AS rank FROM Questions AS Q INNER JOIN Users AS U ON Q.userId = U.id WHERE Q.id = ?';
	$params = [$id, $id];
	return $this->db->executeFetchAll($sql, $params);
    }
    public function findAllQuestions($order){
	$answers = 'SELECT count(*) FROM Answers WHERE questionId = Q.id';
	$sql = 'SELECT U.name, Q.userId, Q.header, Q.question, Q.id, ('.$answers.') AS answers, ('.$this->questionRank.') AS rank FROM Questions AS Q INNER JOIN Users AS U ON Q.userId = U.id ORDER BY '.$order.' DESC';
	$params = [];
	return $this->db->executeFetchAll($sql, $params);
    }
    public function getLatestQuestions(){
	$answers = 'SELECT count(*) FROM Answers WHERE questionId = Q.id';
	$sql = 'SELECT U.name, Q.userId, Q.header, Q.question, Q.id, ('.$answers.') AS answers, ('.$this->questionRank.') AS rank FROM Questions AS Q INNER JOIN Users AS U ON Q.userId = U.id ORDER BY Q.created DESC LIMIT 3';
	$params = [];
	return $this->db->executeFetchAll($sql, $params);
    }
    public function acceptAnswer($id){
	$sql = 'UPDATE Answers SET accepted = "TRUE" WHERE id = ?';
	$params = [$id];
	$this->db->execute($sql, $params);
    }

    public function questionAlreadyVoted($userId, $questionId){
	$sql = 'SELECT count(*) AS count FROM QuestionVotes  WHERE voterId = ? AND questionId = ?';
	$params = [$userId, $questionId];
	return $this->db->executeFetchAll($sql, $params)[0]->count;
    }
    public function answerAlreadyVoted($userId, $answerId){
	$sql = 'SELECT count(*) AS count FROM AnswerVotes WHERE voterId = ? AND answerId = ?';
	$params = [$userId, $answerId];
	return $this->db->executeFetchAll($sql, $params)[0]->count;
    }
    public function commentAlreadyVoted($userId, $commentId){
	$sql = 'SELECT count(*) AS count FROM CommentVotes WHERE voterId = ? AND commentId = ?';
	$params = [$userId, $commentId];
	return $this->db->executeFetchAll($sql, $params)[0]->count;
    }
    public function questionVote($userId, $questionId, $isUpvote){
	$sql = "INSERT INTO QuestionVotes ('voterId', 'questionId', 'isUpvote') VALUES (?,?,?)";
	$params = [$userId, $questionId, $isUpvote];
	$this->db->execute($sql, $params);
    }
    public function commentVote($userId, $commentId, $isUpvote){
	$sql = "INSERT INTO CommentVotes ('voterId', 'commentId', 'isUpvote') VALUES (?,?,?)";
	$params = [$userId, $commentId, $isUpvote];
	$this->db->execute($sql, $params);
    }
    public function answerVote($userId, $answerId, $isUpvote){
	$sql = "INSERT INTO AnswerVotes ('voterId', 'answerId', 'isUpvote') VALUES (?,?,?)";
	$params = [$userId, $answerId, $isUpvote];
	$this->db->execute($sql, $params);
    }
}
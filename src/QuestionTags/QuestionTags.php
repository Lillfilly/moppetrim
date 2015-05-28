<?php
namespace Anax\QuestionTags;
 
/**
 * Model for Questions.
 *
 */
class QuestionTags extends \Anax\MVC\CDatabaseModel
{
    public function findUnique(){
	$sql = 'SELECT DISTINCT tag FROM QuestionTags ORDER BY tag ASC';
	return $this->db->executeFetchAll($sql, []);
    }

    public function findPopular(){
	$sql = <<<EOD
SELECT 
    DISTINCT tag,
    (
	SELECT count(*) FROM QuestionTags AS QT2 WHERE QT2.tag = QT.tag
    ) AS Amount
FROM QuestionTags AS QT ORDER BY Amount DESC LIMIT 5
EOD;
	return $this->db->executeFetchAll($sql);
    }

    public function getQuestionsForTag($tag){
	$tag = strtoupper($tag);
	$sql = 'SELECT Q.*, U.name FROM Questions AS Q INNER JOIN QuestionTags AS QT ON QT.questionId = Q.id INNER JOIN Users AS U ON U.id = Q.userId WHERE QT.tag = ?';
	$params = [$tag];
	
	return $this->db->executeFetchAll($sql, $params);
    }
    public function getTagsForQuestion($questionId){
	$sql = 'SELECT tag FROM QuestionTags AS T INNER JOIN Questions AS Q ON T.questionId = Q.id WHERE Q.id = ?';
	$params = [$questionId];

	return $this->db->executeFetchAll($sql, $params);
    }
}
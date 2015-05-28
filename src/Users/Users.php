<?php
namespace Anax\Users;
 
/**
 * Model for Users.
 *
 */
class Users extends \Anax\MVC\CDatabaseModel
{

    function getQuestionsForUser($id){
	$sql = 'SELECT Q.* FROM Questions AS Q INNER JOIN Users AS U ON Q.userId = U.id WHERE U.id = ?';
	$params = [$id];
	return $this->db->executeFetchAll($sql, $params);
    }
    function getQuestionsAnsweredByUser($id){
	$sql = 'SELECT DISTINCT Q.* FROM Questions AS Q INNER JOIN Answers AS A ON A.questionId = Q.id AND A.userId = ?';
	$params = [$id];
	return $this->db->executeFetchAll($sql, $params);
    }
    function getCommentsByUser($id){
	$sql = 'SELECT count(*) AS amount FROM Comments WHERE userId = ?';
	$params = [$id];
	return $this->db->executeFetchAll($sql, $params)[0];
    }

    function queryUsersWithScore($columns = '*'){
	$this->db->select($columns)
    	     ->from('UsersWithScore');
        return $this;
    }

    function getMostActiveUsers($columns = '*'){
	$this->db->select($columns)
    	     ->from('UsersWithScore ORDER BY reputation DESC LIMIT 4');
        return $this;
    }

    function signup($email, $name, $password){
	$this->create([
	    'name' => trim($name),
	    'email'=> $email,
	    'pw'   => md5($password),
	]);

	$this->login($email, $password);
    }
    function updateUser($name, $id){
	$sql = 'UPDATE Users SET name=? WHERE id = ?';
	$params = [$name, $id];
	$this->db->execute($sql, $params);

	$user = $this->session->get('user');
	$user->name = $name;
	$this->session->set('user', $user);
    }

    function login($email, $password){
	$password = md5($password);
	$result = $this->query('id,name,email')->where('email = ?')->andWhere('pw = ?')->execute([$email,$password]);

	if(is_array($result) && count($result) == 1){
	    $this->session->set('user', $result[0]);
	    return true;
	}else{
	    return false;
	}
    }
    function logout(){
	$this->session->set('user', NULL);
    }
}
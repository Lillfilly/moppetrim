<?php
require __DIR__ . '/config_with_app.php';

$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);
$app->theme->configure(ANAX_APP_PATH . 'config/duplo/theme.php');
$app->navbar->configure(ANAX_APP_PATH . 'config/duplo/navbar.php');

$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/config_sqlite.php');
    $db->connect();
    return $db;
});

$di->set('form', '\Mos\HTMLForm\CForm');

$di->setShared('QuestionsController', function() use ($di) {
    $controller = new Anax\Questions\QuestionsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('TagsController', function() use ($di) {
    $controller = new Anax\QuestionTags\TagsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('UsersController', function() use ($di) {
    $controller = new Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$app->router->add('', function() use ($app) {

    $app->theme->setTitle('Hem');
    $content = $app->fileContent->get("frontpage.md");
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    $app->views->add('users/authentication', [
	'user' => $app->session->get('user'),
    ]);
    $app->views->add('duplo/page', [
	'content' => $content,
    ]);
    $app->dispatcher->forward([
	'controller' => 'questions',
	'action' =>	'getLatestQuestions',
    ]);
    $app->dispatcher->forward([
	'controller' => 'users',
	'action' =>	'getMostActiveUsers',
    ]);
    $app->dispatcher->forward([
	'controller' => 'tags',
	'action' =>	'MostPopularTags',
    ]);
});

$app->router->add('about', function() use ($app){
    $app->theme->setTitle('Om MoppeTrim');
    $content = $app->fileContent->get("about.md");
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');
    $app->views->add('duplo/page', [
	'content' => $content,
    ]);
});

$app->router->add('showsource', function() use ($app) {
    $app->theme->addStyleSheet('css/csource.css');
    $app->theme->setTitle("Visa källkod");
    
    $source = new \Mos\CSource\CSource([
	'secure_dir' => '..',
	'base_dir' => '..',
    ]);

    $app->views->add('csource/csource', [
	'content' => $source->View(),
    ]);
});

$app->router->handle();
$app->theme->render();
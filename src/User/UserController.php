<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

namespace User;

class UserController {
    function __construct () {

    }

    public function autentica (\Silex\Application $app, \Symfony\Component\HttpFoundation\Request $request) {
      echo '<pre>';
      $params = $request->request->all();

      $sql = 'SELECT * FROM users WHERE username = :username AND password = :password';
      $post = $app['db']->fetchAssoc($sql, array('username' => $params['username'], 'password' => $params['password']));

      if($post['username'] == $app['session']->get('username')){
        return $app->redirect('/painelgmgothicpt/home');
      }
      else{
        return $app->redirect('/painelgmgothicpt/invalido');
      }

      return $post !== false;
    }

    public function protegePagina(\Silex\Application $app, \Symfony\Component\HttpFoundation\Request $request){

    }

    public function listarUsuarios(\Silex\Application $app, \Symfony\Component\HttpFoundation\Request $request){


    }


}

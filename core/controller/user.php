<?php
namespace Leeflets\Controller;

class User extends \Leeflets\Controller {
	protected $no_auth_actions = array( 'login' );

	function login() {
		
		$form = new \Leeflets\Form( $this->config, $this->router, $this->settings, 'login-form', array(
			'elements' => array(
				'credentials' => array(
					'type' => 'fieldset',
					'elements' => array(
						'username' => array(
							'type' => 'email',
							'placeholder' => 'Email Address',
							'class' => 'input-block-level',
							'required' => true,
							'autofocus' => true,
							'validation' => array(
								array(
									'callback' => array( $this, '_check_username' ),
									'msg' => 'Sorry, that is not the correct username.'
								)
							)
						),
						'password' => array(
							'type' => 'password',
							'placeholder' => 'Password',
							'class' => 'input-block-level',
							'required' => true,
							'validation' => array(
								array(
									'callback' => array( $this, '_check_password' ),
									'msg' => 'Sorry, that is not the correct password.'
								)
							)
						)
					)
				),
				'buttons' => array(
					'type' => 'fieldset',
					'elements' => array(
						'submit' => array(
							'type' => 'button',
							'button-type' => 'submit',
							'class' => 'btn btn-primary',
							'value' => 'Login'
						)
					)
				)
			)
		) );

		if ( $form->validate() ) {
			$this->user->set_cookie();
			\Leeflets\Router::redirect( $this->router->admin_url() );
			exit;
		}

		$args = compact( 'form' );

		$args['page-title'] = 'Login';
		$args['layout'] = 'logged-out';

		return $args;
	}

	function logout() {
		$this->user->clear_cookie();

		if ( isset( $_GET['redirect'] ) && '' != $_GET['redirect'] ) {
			$url = $_GET['redirect'];
		}
		else {
			$url = $this->router->admin_url( '/user/login/' );
		}

		$this->router->redirect( $url );
		exit;
	}

	function _check_username( $value ) {
		return ( $this->config->username == $value );
	}

	function _check_password( $value ) {
		$hasher = new \PasswordHash( 8, false );
		return $hasher->CheckPassword( $value, $this->config->password );
	}
}

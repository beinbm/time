<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * @property User $User
 */
class AppController extends Controller
{

	protected $curr_user = null;
	protected $userid = null;
	protected $groupid = null;

	public function beforeFilter()
	{
		if (!env("REMOTE_USER")) {
			throw new NotFoundException('No User auth');
		}

		$this->loadModel('User');
		$this->curr_user = $this->User->findByName(env("REMOTE_USER"));
		if ($this->curr_user) {
			$this->userid = $this->curr_user['User']['id'];
			$this->groupid = $this->curr_user['Group']['id'];

			$this->set('curr_user', $this->curr_user);
			$this->set('userid', $this->userid);
			$this->set('groupid', $this->groupid);
		} else {
			// Create User if not present yet
			$this->User->create();
			$this->User->save(
				[
					'User' => [
						'name' => env("REMOTE_USER"),
						'group_id' => 0,
					],
					['validate' => false]
				]
			);
			return $this->redirect(['controller' => 'times', 'action' => 'index']);
		}
	}
}

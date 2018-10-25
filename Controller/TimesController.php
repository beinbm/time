<?php

/**
 * Class TimesController
 *
 * @property Time $Time
 * @property Payment $Payment
 * @property User $User
 */
class TimesController extends AppController
{


	public $helpers = ['Html', 'Form'];
	public $uses = ['Time', 'Payment', 'User'];

	public function index($count = 30)
	{
		$this->Time->recursive = 0;

		/* Wenn schon gestartet, dann Werte in Stop-Formular ausgeben */
		if ($this->Time->hasAny("Time.user_id = '" . $this->userid . "' AND Time.stop IS NULL")) {
			$this->set('startedTime', 1);
			$this->request->data = $this->Time->find(
				'first',
				['conditions' => "Time.user_id = '" . $this->userid . "' AND Time.stop IS NULL"]
			);
		}

		$customers = $this->Time->Customer->find('list');
		$users = $this->User->find('list');

		if ($this->groupid == 1) {
			foreach ($users as $id => $username) {
				$statistics[$username]['Time'] = $this->Time->statistics($id);
				$statistics[$username]['Payment'] = $this->Payment->statistics($id);
			}
			$params = [
				'order' => 'start DESC',
				'limit' => intval($count)
			];
		} else {
			$params = [
				'conditions' => [
					'Time.user_id' => $this->userid
				],
				'order' => 'start DESC',
				'limit' => intval($count)
			];
			$statistics[$users[$this->userid]]['Time'] = $this->Time->statistics($this->userid);
			$statistics[$users[$this->userid]]['Payment'] = $this->Payment->statistics($this->userid);
		}

		$userId = $this->request->query('user_id');
		if (isset($this->request->named['user_id'])) {
			$userId = $this->request->named['user_id'];
		}

		if ($userId) {
			$params['conditions']['Time.user_id'] = $userId;
		}

		$customerId = (int)$this->request->query('customer_id');
		if (isset($this->request->named['customer_id'])) {
			$customerId = (int)$this->request->named['customer_id'];
		}

		if ($customerId) {
			$params['conditions']['Time.customer_id'] = $customerId;
			$this->set(
				'projectstatistics',
				$this->Time->projectstatistics($customerId)
			);
			$this->set('monthly_stats', $this->Time->monthly_stats($customerId));
		} else {
			$this->set('projectstatistics', $this->Time->projectstatistics('all'));
			$this->set('monthly_stats', $this->Time->monthly_stats('all'));
		}

		$times = $this->Time->find('all', $params);

		$this->set('customers', $customers);
		$this->set('users', $users);
		$this->set('times', $times);
		$this->set('statistics', $statistics);
	}

	public function start()
	{
		/* Check if entry already exists */
		if ($this->Time->hasAny("Time.user_id = '" . $this->userid . "' AND Time.stop IS NULL")) {
			return $this->flash('Du arbeitest doch schon', '/times/index');
			exit();
		}

		$this->request->data['Time']['user_id'] = $this->userid;
		$this->request->data['Time']['start'] = date('Y-m-d H:i:s');
		$this->request->data['Time']['stop'] = null;
		$this->request->data['Time']['break'] = 0;

		if ($this->Time->save($this->request->data)) {
			$this->Session->setFlash('Started, Wohoo!', 'message_ok');
			return $this->redirect('/times/index');
		} else {
			$this->Session->setFlash('Please correct errors below.', 'message_error');
			$this->set('users', $this->Time->User->find('list'));
			$this->set('customers', $this->Time->Customer->find('list'));
			return $this->redirect('/times/index');
		}
	}

	public function stop()
	{
		$db_entry = $this->Time->find(
			'first',
			[
				'conditions' => "Time.user_id = '" . $this->userid . "' AND Time.stop IS NULL",
				'fields' => ['id']
			]
		);

		$this->request->data['Time'] = array_merge($this->request->data['Time'], $db_entry['Time']);
		$this->request->data['Time']['stop'] = date('Y-m-d H:i:s');
		if ($this->Time->save($this->request->data)) {
			$this->Session->setFlash('Stopped, Wohoo!', 'message_ok');
			return $this->redirect('/times/index');
		} else {
			$this->Session->setFlash('Please correct errors below.', 'message_error');
			$this->set('users', $this->Time->User->find('list'));
			$this->set('customers', $this->Time->Customer->find('list'));
			return $this->redirect('/times/index');
		}
	}

	public function edit($id = null)
	{
		if (empty($this->request->data)) {
			if (!$id || !$this->Time->hasAny("Time.id = $id AND Time.user_id = $this->userid")) {
				$this->Session->setFlash('Invalid id for Time', 'message_error');
				return $this->redirect('/times/index');
			}
			if (!$this->Time->hasAny("Time.id = $id AND Time.start > DATE_SUB(CURRENT_DATE, INTERVAL 4 DAY) ")) {
				$this->Session->setFlash('Entry too old', 'message_error');
				return $this->redirect('/times/index');
			}
			$this->request->data = $this->Time->find(
				'first',
				['conditions' => "Time.id = $id AND Time.user_id = $this->userid"]
			);

		} elseif ($this->Time->hasAny(
			"Time.id = $id AND Time.user_id = $this->userid AND Time.start > DATE_SUB(CURRENT_DATE, INTERVAL 4 DAY) "
		)
		) {
			// Only own ids
			$this->request->data['Time']['id'] = $id;
			$this->request->data['Time']['user_id'] = $this->userid;

			if (strtotime($this->request->data['Time']['start']) - strtotime('-5 days') < 0) {
				$this->Session->setFlash('Entry gets too old', 'message_error');
				return $this->redirect('/times/index');
			}
			if ($this->Time->save($this->request->data)) {
				$this->Session->setFlash('The Time has been saved', 'message_ok');
				return $this->redirect('/times/index');
			} else {
				$this->Session->setFlash('Please correct errors below.', 'message_error');
			}
		}

		$this->set('customers', $this->Time->Customer->find('list'));
	}

	public function delete($id = null)
	{
		if (!$id || !$this->Time->hasAny("Time.id = $id AND Time.user_id = $this->userid")) {
			$this->Session->setFlash('Invalid id for Time', 'message_error');
			return $this->redirect('/times/index');
		}
		if ($this->Time->hasAny("Time.id = $id AND Time.user_id = $this->userid") && $this->Time->delete($id)) {
			$this->Session->setFlash('The Time deleted: id ' . $id, 'message_ok');
			return $this->redirect('/times/index');
		}
	}

	public function export($count = 100, $user = null)
	{
		$this->layout = 'ajax';
		$this->Time->recursive = 0;

		$this->response->download("time_export_".date('Ymd').".csv");

		$this->set('customers', $this->Time->Customer->find('list'));

		if ($this->groupid == 1 && $user == null) {
			$conditions = [];
		} elseif ($this->groupid == 1 && !empty($user)) {
			$conditions = ['Time.user_id' => $user];
		} else {
			$conditions = ['Time.user_id' => $this->userid];
		}

		$params = [
			'conditions' => $conditions,
			'limit' => intval($count),
			'order' => ['Time.start' => 'DESC'],
		];

		$times = $this->Time->find('all', $params);
		$this->set('times',$times);
	}
}

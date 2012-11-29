<?php

class RolesController extends AppController {
	public $name = 'Roles';

	public function admin_index()
	{
        $this->paginate = array(
            'order' => 'Role.created DESC',
            'limit' => $this->pageLimit,
            'conditions' => array(
            	'Role.deleted_time' => '0000-00-00 00:00:00'
            )
        );
        
        $this->request->data = $this->paginate('Role');
	}

	public function admin_add()
	{
        if ($this->request->is('post')) {
        	$this->request->data['Role']['title'] = $this->slug($this->request->data['Role']['title']);

        	$role = $this->Role->find('first', array('fields' => 'id'));
        	$list = $this->Role->Permission->find('all', array(
        		'conditions' => array(
        			'Permission.role_id' => $role['Role']['id']
        			)
        		)
        	);

            if ($this->Role->save($this->request->data)) {
	        	foreach ($list as $row) {
	    			$this->Role->Permission->create();

					$newPermission = array(
						'title' => $row['Permission']['title'],
						'role_id' => $this->Role->id,
						'plugin' => '',
						'controller' => $row['Permission']['controller']
					);
				
					$this->Role->Permission->save($newPermission);
        		}

                $this->Session->setFlash('Your role has been added.', 'flash_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash('Unable to add your role.', 'flash_error');
            }
        } 
	}

	public function admin_edit($id = null)
	{
      	$this->Role->id = $id;

	    if ($this->request->is('get')) {
	        $this->request->data = $this->Role->find('first', array(
	        	'conditions' => array(
	        		'Role.id' => $id
	        	),
	        	'contain' => array(
        			'Permission' => array(
        				'PermissionValue'
        				)
        			)
	        	)
	        );
	    } else {
	    	$this->request->data['Role']['title'] = $this->slug($this->request->data['Role']['title']);
	        if ($this->Role->saveAll($this->request->data, array(
	        	'deep' => true
	        	))) {
	            $this->Session->setFlash('Your role has been updated.', 'flash_success');
	            $this->redirect(array('action' => 'index'));
	        } else {
	            $this->Session->setFlash('Unable to update your role.', 'flash_error');
	        }
	    }

	}

	public function admin_delete($id = null, $title = null)
	{

		if ($this->request->is('post')) {
	        throw new MethodNotAllowedException();
	    }

	    $this->Role->id = $id;
	    if ($this->Role->saveField('deleted_time', $this->Role->dateTime())) {
	    	// $this->Role->Permission->deleteAll(array('Permission.role_id' => $id));
	    	// $this->Role->Permission->PermissionValue->deleteAll(array('PermissionValue.role_id' => $id));

	        $this->Session->setFlash('The role `'.$title.'` has been deleted.', 'flash_success');
	        $this->redirect(array('action' => 'index'));
	    } else {
	    	$this->Session->setFlash('The role `'.$title.'` has NOT been deleted.', 'flash_error');
	        $this->redirect(array('action' => 'index'));
	    }
	}

}
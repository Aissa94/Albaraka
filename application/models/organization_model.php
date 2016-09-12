<?php
/**
 * This Model contains all the business logic and the persistence layer for the organization tree.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This class allows to manage the organization of of users. Users can be attached to a node of a tree.
 * These nodes are called 'entities' and can be 'departments' or 'sub-departments', 'groups', etc.
 * It allows to use filters on a part of your structure, whatever your organization is.
 */
class Organization_model extends CI_Model {

    /**
     * Default constructor
     */
    public function __construct() {

    }

    /**
     * Get the department details of a given user (label and ID)
     * @param int $user_id User identifier
     * @return array department details
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getDepartment($user_id) {
        $this->db->select('organization.id as id, organization.name as name');
        $this->db->from('organization');
        $this->db->join('users', 'users.organization = organization.id');
        $this->db->where('users.id', $user_id);
        $query = $this->db->get();
        $arr = $query->result_array();
        return $arr;
    }
    
    /**
     * Get the label of a given entity id
     * @param int $id Identifier of the entity
     * @return string name of the entity
     */
    public function getName($id) {
        $this->db->from('organization');
        $this->db->where("id", $id); 
        $query = $this->db->get();
        $record = $query->result_array();
        if(count($record) > 0) {
            return $record[0]['name'];
        } else {
            return '';
        }
    }
    
    /**
     * List all entities of the organisation
     * @return $organization_id if set then return just one level
     * @return array all entities of the organization sorted out by id and name
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getAllEntities($organization_id = NULL) {
        $this->db->from('organization');
        if(isset($organization_id)) $this->db->where('id ='.$organization_id.' or parent_id ='.$organization_id);
        $this->db->order_by("parent_id", "desc"); 
        $this->db->order_by("name", "asc");
        return $this->db->get();
    }

    /**
     * Get all children of an entity
     * @param int $id identifier of the entity
     * @return array list of entity identifiers
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getAllChildren($id) {
        $query = 'SELECT GetFamilyTree(id) as id' .
                    ' FROM organization' .
                    ' WHERE id =' . $id;
        $query = $this->db->query($query); 
        $arr = $query->result_array();
        return $arr;
    }

    /**
     * Get children of an entity
     * @param int $id identifier of the entity
     * @return array list of entity identifiers
     * @author Nabil GHOUILA <dnghouila@gmail.com>
     */
    public function getChildren($id) {
        $query = $this->db->query('SELECT organization.id FROM organization WHERE organization.parent_id = '.$id, FALSE); 
        $arr = $query->result_array();
        return $arr;
    }
    
    /**
     * Move an entity into the organization
     * @param int $id identifier of the entity
     * @param int $parent_id new parent id of the entity
     * @return type result of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function move($id, $parent_id) {
        $data = array(
            'parent_id' => $parent_id
        );
        $this->db->where('id', $id);
        return $this->db->update('organization', $data);
    }
    
    /**
     * Add an employee into an entity of the organization
     * @param int $id identifier of the employee
     * @param int $entity identifier of the entity
     * @return type result of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function attachEmployee($id, $entity) {
        $data = array(
            'organization' => $entity
        );
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    /**
     * Cascade delete children and set employees' org to NULL
     * @param int $entity identifier of the entity
     * @return type result of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function delete($entity) {
        $list = $this->getAllChildren($entity);
        //Detach all employees
        $data = array(
            'organization' => NULL
        );
        $ids = array();
        if (strlen($list[0]['id']) > 0) {
            $ids = explode(",", $list[0]['id']);
        }
        array_push($ids, $entity);
        $this->db->where_in('organization', $ids);
        $res1 = $this->db->update('users', $data);
        //Delete node and its children
        $this->db->where_in('id', $ids);
        $res2 = $this->db->delete('organization');
        return $res1 && $res2;
    }
    
    /**
     * Delete an employee from an entity of the organization
     * @param int $id identifier of the employee
     * @return type result of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function detachEmployee($id) {
        $data = array(
            'organization' => NULL
        );
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }
    
    /**
     * Rename an entity of the organization
     * @param int $id identifier of the entity
     * @param string $text new text of the entity
     * @return type result of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function rename($id, $text) {
        $data = array(
            'name' => $text
        );
        $this->db->where('id', $id);
        return $this->db->update('organization', $data);
    }
    
    /**
     * Create an entity in the organization
     * @param int $parent_id identifier of the parent entity
     * @param string $text name of the new entity
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function create($parent_id, $text) {
        $data = array(
            'name' => $text,
            'parent_id' => $parent_id
        );
        return $this->db->insert('organization', $data);
    }
    
    /**
     * Copy an entity in the organization
     * @param int $id identifier of the source entity
     * @param int $parent_id identifier of the new parent entity
     * @return type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function copy($id, $parent_id) {
        $this->db->from('organization');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $row = $query->row();
        $data = array(
            'name' => $row->name,
            'parent_id' => $parent_id
        );
        return $this->db->insert('organization', $data);
    }
    
    /**
     * Returns the list of the employees attached to an entity
     * @param int $id identifier of the entity
     * @return array Result of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function employees($id) {
        $this->db->select('id, firstname, lastname, email, datehired');
        $this->db->from('users');
        $this->db->where('organization', $id);
        $this->db->order_by('lastname', 'asc'); 
        $this->db->order_by('firstname', 'asc');
        return $this->db->get();
    }
    
    /**
     * Returns the list of the employees attached to an entity
     * @param int $id identifier of the entity
     * @param bool $children Include sub department in the query
     * @return  array Result of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function allEmployees($id, $children = FALSE) {
        $this->db->select('users.id, users.identifier, users.firstname, users.lastname, users.datehired');
        $this->db->select('organization.name as department, positions.name as position, contracts.name as contract');
        $this->db->select('contracts.id as contract_id');
        $this->db->from('organization');
        $this->db->join('users', 'users.organization = organization.id');
        $this->db->join('positions', 'positions.id  = users.position', 'left');
        $this->db->join('contracts', 'contracts.id  = users.contract', 'left');
        if ($children === TRUE) {
            $this->load->model('organization_model');
            $list = $this->organization_model->getChildren($id);
            $ids = array();
            if (count($list) > 0) {
                if ($list[0]['id'] != '') {
                    $ids = explode(",", $list[0]['id']);
                    array_push($ids, $id);
                    $this->db->where_in('organization.id', $ids);
                } else {
                    $this->db->where('organization.id', $id);
                }
            }
        } else {
            $this->db->where('organization.id', $id);
        }
        $this->db->order_by('lastname', 'asc'); 
        $this->db->order_by('firstname', 'asc');
        $employees = $this->db->get()->result();
        return $employees;
    }
    
    /**
     * Attribute a supervisor to an entity
     * @param int $id identifier of the employee
     * @param int $entity identifier of the entity
     * @return int result of the query
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function setSupervisor($id, $entity) {
        $data = array(
            'supervisor' => $id
        );
        $this->db->where('id', $entity);
        return $this->db->update('organization', $data);
    }
    
    /**
     * Returns the supervisor of an entity
     * @param int $entity identifier of the entity
     * @return object identifier of supervisor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getSupervisor($entity) {
        $this->db->select('users.id, CONCAT(users.firstname, \' \', users.lastname) as username, email', FALSE);
        $this->db->from('organization');
        $this->db->join('users', 'users.id = organization.supervisor');
        $this->db->where('organization.id', $entity);
        $result = $this->db->get()->result();
        if (count($result) > 0) {
            return $result[0];
        } else {
            return NULL;
        }
    }   

    
    /**
     * Calcul the manager of the user
     * @param int $id User identifier
     * @author Nabil GHOUILA <dnghouila@gmail.com>
     */
    public function getManager($id) {
        // the supervisor of the entity
        $supervisor = $this->db->query('SELECT organization.supervisor FROM users join organization on users.organization = organization.id where users.id='.$id, FALSE)->result_array();
        if(isset($supervisor[0]))
        {   
            if($id == $supervisor[0]['supervisor']){ // return the supervisor of the parent entity
            $supervisor = $this->db->query('SELECT supervisor FROM (select organization.parent_id from users join organization on users.organization = organization.id where users.id='.$id.') as parent_organization join organization on parent_organization.parent_id = organization.id', FALSE)->result_array();
            }
        }
        if(!isset($supervisor[0]) || $supervisor[0]['supervisor'] == null) return $id; //if no supervisor then return the employee
        else return $supervisor[0]['supervisor'];
        // the supervisor of the root entity
        //else return $this->db->query('SELECT supervisor FROM organization where organization.id = 0', FALSE)->result_array()[0]['supervisor'];
    } 

     /**
     * Set the managers of all users
     * @author Nabil GHOUILA <dnghouila@gmail.com>
     */
    public function reloadManagers() {
        $query = $this->db->query('SELECT users.id FROM users', FALSE)->result_array();
        foreach($query as $item){
            $this->db->where('id', $item['id']); 
            $this->db->set('manager', $this->getManager($item['id']));
            $this->db->update('users');
        }
    }


}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function count_all_members()
    {
        //return (int)$this->db->count_all('members'); 

        //testing
        $this->db->where_in('id', [1, 2, 3, 4]);
       return (int)$this->db->count_all_results('members');
    }

    public function get_members_batch($offset = 0, $limit = 200)
    {
        $this->db->select('id, idnumber, employeeno, tscno, surname, name, cellnumber, dob, gender, schoolcode');
        $this->db->from('members');
        $this->db->where_in('id', [1, 2, 3, 4,9169]);
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result_array();
    }
}

<?php 
/**
 * summary
 */
class PostModel extends CI_Model
{
    /**
     * summary
     */
    public function __construct()
    {
        parent::__construct();
    }
    function getPosts(){
    	$this->db->select("*");
    	$this->db->from('tbl_device');
    	$query = $this->db->get();
    	return $query->result();
    }
    function getPost($macid){
    	$this->db->select("mac_id,user_id,temp,timestamp");
    	$this->db->from('tbl_device');
    	$this->db->where('mac_id', $macid);
    	$query = $this->db->get();
    	return $query->result();
    }
    function k_exist($key,$macid){
    	// $this->db->where('api_key',$key);
    	// $query = $this->db->get();
    	$this->db->select('api_key','mac_id');
    	$this->db->from('tbl_device');
    	$this->db->where('api_key', $key);
        $this->db->where('mac_id', $macid);
    	$query = $this->db->get();
    	if($query->num_rows()>0){
    		return true;
    	} else {
    		return false;
    	}
    }
    function up_temp($d,$data){
      $now = new DateTime ( NULL, new DateTimeZone('UTC'));
      $this->db->trans_start();
      $this->db->set('temp',$data);
      $this->db->set('timestamp',$now->format('Y-m-d H:i:s'));
      $this->db->where('mac_id',$d);
      $this->db->update('tbl_device');
      $this->db->trans_complete();
      if($this->db->trans_status() === FALSE){
        return FALSE;
      } else {
        return TRUE;
      }

    }
}
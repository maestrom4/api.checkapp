<?php 
class PostModel extends CI_Model
{
    public function __construct()
    {
      parent::__construct();
      $this->load->database(); //load database
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
    //Para ni sa arduino update data
    function up_temp($d,$data){
      $now = new DateTime ( NULL, new DateTimeZone('UTC'));
      //$this->db->trans_start();
      $this->db->set('temp',$data);
      $this->db->set('timestamp',$now->format('Y-m-d H:i:s'));
      $this->db->where('mac_id',$d);
      $this->db->update('tbl_device'); 
      if($this->db->trans_status() === FALSE){
        return FALSE;
      } else {
        return TRUE;
      }
    }
    function update_user($a=NULL,$b=NULL,$c=NULL,$d=NULL,$e=NULL,$g){
      $now = new DateTime ( NULL, new DateTimeZone('UTC'));
      $data = array(
        'name'=>$a,
        'mname'=>$b,
        'lname'=>$c,
        'email'=>$d,
        'password'=>$e,
        'timestamp'=>$now->format('Y-m-d H:i:s'));
      $this->db->where('user_id',$g);
      $this->db->update('tbl_user',$data);
      if($this->db->trans_status()===true){ 
        return "successful"; 
      } else { return 'failed';
      }
    }
    function get_user_pass($a){
      $this->db->select('password');
      $this->db->from('tbl_user');
      $this->db->where('email',$a);
      $q=$this->db->get();
      $q = $q->result();
      return $q;
    }
    function check_email_mdl($a,$b,$c=NULL){
      $this->db->select('email');
      $this->db->from($b);
      $this->db->where('email',$a);

      $q = $this->db->get();
      if ($c!=NULL){
        $q = $q->result();
        return $q; 
      }
      else {
        if($q->num_rows()>0){ return true;}
        else { return false; }
      }
    }
    function maxid(){
      $this->db->select('MAX(id)');
      $this->db->from('tbl_user');
      $q=$this->db->get();
      $q=$q->result();
      $array = json_decode(json_encode($q[0]),TRUE);  
      $d=$array['MAX(id)'];
      return intval($d);
    }
    function create_android($b,$c,$d,$e,$f,$g){
      // CodeIgniter method 
      $id=$this->PostModel->maxid();
      $now = new DateTime ( NULL, new DateTimeZone('UTC'));
      $data = array(
        'user_id' => 'uid_'.$id,
        'name' => $e,
        'mname' => $f,  
        'lname' => $g,
        'email' => $b,
        'password' => $d,
        'timestamp' => $now->format('Y-m-d H:i:s')
      );
      $this->db->insert('tbl_user', $data);
      $id = $this->db->insert_id();
      if($this->db->trans_status() === FALSE){
        return FALSE;
      } else {
        return TRUE;
      }
    }
    //user_id,name,mname,lname,email,password read all
    function read_android(){
      $this->db->select("*");
      $this->db->from("tbl_user");
      $query = $this->db->get();
      return $query->result();
    }
    //user_id,name,mname,lname,email,password read all
    function read_by_user_android($a){
      $this->db->select("*");
      $this->db->from("tbl_user");
      $this->db->where('user_id',$a);
      $query = $this->db->get();
      return $query->result();
    }
    function get_user_id_by_email($a){
      $this->db->select("user_id");
      $this->db->from("tbl_user");
      $this->db->where('email',$a);
      $query = $this->db->get();
      return $query->result();
    }
    function delete_row($a,$b,$c){
      $this->db->where($a,$b);
      $this->db->delete($c);
      if($this->db->trans_status()!=False){ return true; 
      } else { return false;}
    }
}
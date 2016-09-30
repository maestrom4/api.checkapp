<?php
require(APPPATH.'/libraries/REST_Controller.php');
 
class Device extends REST_Controller
{
    function __Construct(){
        parent::__Construct();
        $this->load->database(); //load database
        $this->load->model('PostModel'); // load model
        $this->load->library('encrypt');
        $this->load->helper('url');

        $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }
    function arduino_get()
    {   
        $seg3 = $this->uri->segment(5);
        $seg2 = $this->uri->segment(4);
        $seg1 = $this->uri->segment(3);
        $apikey=$this->PostModel->k_exist($seg2,$seg1);
        //$this->response($apikey); //load the view file
        if($seg1==='all'){  
            $this->data['posts'] = $this->PostModel->getPosts(); //calling post model
            $this->response($this->data,300); //load the view file
        }
        if($seg3===NULL){
          if ($apikey===FALSE){ $this->response('no api key pki butang lang pert!',300);}
          elseif($seg1===NULL){
            $this->response('No data entered add mac-address or all data after device/arduino/ {device/arduino/08-00-27-CE-32-FE}',300);
          }
          // echo $this->data['posts']['mac_seg1'];
          else {
            $this->data['posts'] = $this->PostModel->getPost($seg1); //for single row post ni cya
            // var_dump($test);
            $this->response($this->data,300);
            // echo "ID is not null";
          }
        } // human n ang segment 3 check
        else {
          // $this->response('dle cya null',300);
          $r=$this->PostModel->up_temp($seg1,$seg3);
          if ($r===true){ $this->response("Success",300);}
          if ($r===true){ $this->response("Failed",300);}
        }
    }
    function check_auth_ctrl($a,$b,$c,$d){
      $result2=$this->PostModel->check_email_mdl($a, $b, false);
      $result1=$this->PostModel->get_user_pass($c); //gets user password
      $result3=$this->compare_pass($d,$result1[0]->password);
      if($result3===true && $result2===true){ 
        $result='valid user both pass and usermail';
      } else { $result='not valid user and pass';}
      return $result3; 
    }
    function compare_pass($a,$b){
      // $saltpasshash =$this->salt_keygen($a,$b);
      if (password_verify($a, $b)) {
          // $h= 'Password is valid!';
          $h = true;
      } else {
          // $h= 'Invalid password.';
          $h = false;
      }
      return $h;
    }
    function salt_keygen($a,$b=NULL){
      $ci = get_instance(); // CI_Loader instance
      if ($b!=NULL){
        $options = [
          // 'salt' => $ci->config->item($b),
          'cost' => 10
        ];
        $pass= password_hash($a, PASSWORD_DEFAULT,$options);
      } else { 
        $pass= password_hash($a, PASSWORD_DEFAULT);
      }
      return $pass;
    }
    function android_get(){
        $seg7 = $this->uri->segment(9); 
        $seg6 = $this->uri->segment(8); 
        $seg5 = $this->uri->segment(7); 
        $seg4 = $this->uri->segment(6); //rest value naa n nmo
        $seg3 = $this->uri->segment(5); //Followed by pass
        $seg2 = $this->uri->segment(4); //email
        $seg1 = $this->uri->segment(3); //create/c/r/u/d
        // 
        // Create section ni
        // 
        if ($seg1==='c') {  
         
          $result2=$this->PostModel->check_email_mdl($seg2, 'tbl_user');
          if ($result2===true){
            $this->response("Duplicate exist!",300);
            exit();
          }
          else {
            $last_uid=$this->db->insert_id();
            $password = $this->salt_keygen($seg3);
            // $result=$this->PostModel->create_android($seg2,"uid_".$last_uid,$seg4,$seg5,$seg6,$seg7);
            $result=$this->PostModel->create_android($seg2,"uid_".$last_uid,$password,$seg4,$seg5,$seg6);
            $this->response("Success",300);
          }
        }
        // Read data all
        if ($seg1==='ra') {
          $result = $this->PostModel->read_android();
          // $result1 = $result[13]->password; // reading json data by column
          $this->response($result,300);
        }

        // Read data by user
        if ($seg1==='r') {
          $result=$this->check_auth_ctrl($seg2,'tbl_user',$seg2,$seg3);
          // $result1 = $result[13]->password; 
          // $test=$this->compare_pass($seg4,$result1);
          
          $this->response($result,300);
        }
        if ($seg1==='u') {  
          $result=$this->PostModel->get_user_id_by_email($seg2);
          // $this->response($result,300);
          $result2 = $this->PostModel->read_by_user_android($result[0]->user_id);
          $j=$result2[0];
          $passgen = $this->salt_keygen($seg4);
          $this->PostModel->update_user($j->name,$j->mname,$j->lname,$j->email,$passgen,$result[0]->user_id);
          if($seg3==NULL){
            $this->response(array($j->name,$j->mname,$j->lname,$j->email,$passgen,$seg2),300);
          }
          else{
            if($seg3!=NULL){
              $passgen = $this->salt_keygen($seg4);
              $m=$this->PostModel->update_user($seg3,$seg4,$seg5,$seg2,$passgen,$result[0]->user_id);
              $this->response($m,300);
            }
          }
        }
        if ($seg1==='d') {  
          $a=$this->PostModel->get_user_id_by_email($seg2);
          if($seg2!=NULL){
            $this->PostModel->delete_row('user_id',$a[0]->user_id,'tbl_user');
            redirect(site_url().'/device/android/','refresh');
            $this->response("You are trying to Delete a user!",300);
          } else {
            $this->response($a,300);
          }
        }
    }
    function web_get(){
      $seg3 = $this->uri->segment(5);
      $seg2 = $this->uri->segment(4);
      $seg1 = $this->uri->segment(3);
      $this->response("Your in web",300);
    } 
    function user_post()
    {
        $result = $this->user_model->update( $this->post('id'), array(
            'name' => $this->post('name'),
            'email' => $this->post('email')
        ));
         
        if($result === FALSE)
        {
            $this->response(array('status' => 'failed'));
        }
        else
        {
            $this->response(array('status' => 'success'));
        }
    }
     
    function users_get()
    {
        $users = $this->data;
         
        if($users)
        {
          $this->response($users, 200);
        }
        else
        {
           $this->response(NULL, 404);
        }
    }
    function beautify_response(){

    }
}
?>
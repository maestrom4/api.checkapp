<?php
require(APPPATH.'/libraries/REST_Controller.php');
 
class Device extends REST_Controller
{
    function __Construct(){
        parent::__Construct();
        $this->load->database(); //load database
        $this->load->model('PostModel'); // load model
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
          var_dump("pass here");
          $test2=$this->PostModel->up_temp($seg1,$seg3);
          $this->response($test2,300);
        }
        

    }
    // function android_get(){
    //     $seg6 = $this->uri->segment(8); //lname
    //     $seg5 = $this->uri->segment(7); //mname
    //     $seg4 = $this->uri->segment(6); //name
    //     $seg3 = $this->uri->segment(5); //password
    //     $seg2 = $this->uri->segment(4); //username
    //     $seg1 = $this->uri->segment(3); //email
    //     // $this->response("Your in android",300);
    //     $result=$this->PostModel->create_android($seg1,$seg2,$seg3,$seg4,$seg5,$seg6);
    //      $this->response($result,300);

    // }
    // function web_get(){
    //     $seg3 = $this->uri->segment(5);
    //     $seg2 = $this->uri->segment(4);
    //     $seg1 = $this->uri->segment(3);
    //     $this->response("Your in web",300);
    // }

     
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
}
?>
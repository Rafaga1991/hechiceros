<?php

class LoginController extends Controller{
    public function index(){
        return $this->view('session/login');
    }

    public function isUser(Request $request){
        $data = ['state' => 'denied'];
        if($data['access'] = $request->validate()){
             $user = new User();
             $user_data = $user->where([
                 'username' => $request->username,
                 'password' => md5($request->password)
             ])->get();
             if(!empty($user_data)){
                 $data['state'] = $user_data[0]['status'] ? 'allow' : 'block';
                 if($user_data[0]['status']){
                     Session::set(
                         '_LOGIN_',
                         array_merge(
                             ['auth'=>true],
                             ['data' => $user_data]
                         )
                     );
                 }
             }
        }else{
            $data['message'] = $request->getMessage();
        }
        echo json_encode($data);
        exit();
    }
}
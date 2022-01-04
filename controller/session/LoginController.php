<?php

class LoginController extends Controller{
    public function index(){
        return $this->view('session/login', Session::get('clan-info'));
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
                     Session::login(
                         array_merge(
                             ['auth'=>true],
                             ['data' => $user_data[0]]
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

    public function close(){
        Session::destroy('_LOGIN_');
        exit();
    }
}
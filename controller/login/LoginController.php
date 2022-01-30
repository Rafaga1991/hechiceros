<?php

class LoginController extends Controller{
	public function index():string{
		return view('view/login/index');
	}

	public function show($id):string{
		return view('');
	}

	public function update(Request $request):string{
		return view('');
	}

	public function destroy(Request $request):string{
		return view('view/login/index');
	}
}
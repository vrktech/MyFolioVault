<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Display the login page.
     */
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        return view('auth/login', [
            'title' => 'Sign In - MyFolioVault',
        ]);
    }

    /**
     * Authenticate user credentials.
     */
    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password. Please try again.');
        }

        // Set session
        session()->regenerate();
        session()->set([
            'isLoggedIn'     => true,
            'userId'         => (int) $user['id'],
            'userName'       => $user['name'],
            'userEmail'      => $user['email'],
            'recordsPerPage' => (int) ($user['records_per_page'] ?? 20),
        ]);

        $redirectUrl = session()->getFlashdata('redirect_url') ?? session()->get('redirect_url') ?? '/';
        session()->remove('redirect_url');

        return redirect()->to($redirectUrl)->with('success', 'Welcome back, ' . esc($user['name']) . '!');
    }

    /**
     * Log the user out.
     */
    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login')->with('success', 'You have been successfully logged out.');
    }
}


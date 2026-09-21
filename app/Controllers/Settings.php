<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EquitySectorModel;

class Settings extends BaseController
{
    protected UserModel $userModel;
    protected EquitySectorModel $sectorModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->sectorModel = new EquitySectorModel();
    }

    /**
     * Display Profile & Preferences page.
     */
    public function index()
    {
        $userId = (int) session()->get('userId');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found. Please log in again.');
        }

        return view('settings/index', [
            'title'   => 'Profile & Preferences - Settings',
            'user'    => $user,
        ]);
    }

    /**
     * Update Profile and Tax / Financial Year / Pagination settings.
     */
    public function updateProfile()
    {
        $userId = (int) session()->get('userId');

        $rules = [
            'name'             => 'required|min_length[2]|max_length[100]',
            'email'            => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'phone'            => 'permit_empty|max_length[20]',
            'fy_start_month'   => 'required|in_list[1,4,7,10]',
            'records_per_page' => 'required|in_list[20,40,50,80,100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name           = trim($this->request->getPost('name'));
        $email          = trim($this->request->getPost('email'));
        $phone          = trim($this->request->getPost('phone'));
        $fyStartMonth   = (int) $this->request->getPost('fy_start_month');
        $recordsPerPage = (int) $this->request->getPost('records_per_page');

        $this->userModel->skipValidation(true)->update($userId, [
            'name'             => $name,
            'email'            => $email,
            'phone'            => $phone ?: null,
            'fy_start_month'   => $fyStartMonth,
            'records_per_page' => $recordsPerPage,
        ]);

        // Refresh session variables
        session()->set([
            'userName'       => $name,
            'userEmail'      => $email,
            'recordsPerPage' => $recordsPerPage,
        ]);

        return redirect()->to('/settings')->with('success', 'Profile and preferences updated successfully!');
    }

    /**
     * Display Change Password page.
     */
    public function password()
    {
        $userId = (int) session()->get('userId');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found. Please log in again.');
        }

        return view('settings/password', [
            'title' => 'Change Password - Settings',
            'user'  => $user,
        ]);
    }

    /**
     * Change Password.
     */
    public function changePassword()
    {
        $userId = (int) session()->get('userId');
        $user = $this->userModel->find($userId);

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/settings/password')->withInput()->with('errors', $this->validator->getErrors());
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword     = $this->request->getPost('new_password');

        if (!password_verify($currentPassword, $user['password_hash'])) {
            return redirect()->to('/settings/password')->with('error', 'The current password you entered is incorrect.');
        }

        $this->userModel->skipValidation(true)->update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);

        return redirect()->to('/settings/password')->with('success', 'Your password has been changed successfully.');
    }

    /**
     * Display Equities Sector Master page.
     */
    public function sectors()
    {
        $userId = (int) session()->get('userId');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User not found. Please log in again.');
        }

        $sectors = $this->sectorModel->getSectorsWithStockCount();

        return view('settings/sectors', [
            'title'   => 'Equities Sector / Industry Master - Settings',
            'user'    => $user,
            'sectors' => $sectors,
        ]);
    }

    /**
     * Add a new Equity Sector / Industry.
     */
    public function addSector()
    {
        $name = trim($this->request->getPost('name') ?? '');
        $description = trim($this->request->getPost('description') ?? '');

        if (empty($name)) {
            return redirect()->to('/settings/sectors')->with('error', 'Sector name is required.');
        }

        $existing = $this->sectorModel->where('name', $name)->first();
        if ($existing) {
            return redirect()->to('/settings/sectors')->with('error', "Sector '{$name}' already exists.");
        }

        $this->sectorModel->insert([
            'name'        => $name,
            'description' => $description ?: null,
        ]);

        return redirect()->to('/settings/sectors')->with('success', "Sector '{$name}' added successfully.");
    }

    /**
     * Update an existing Equity Sector / Industry.
     */
    public function updateSector()
    {
        $id = (int) $this->request->getPost('id');
        $name = trim($this->request->getPost('name') ?? '');
        $description = trim($this->request->getPost('description') ?? '');

        $sector = $this->sectorModel->find($id);
        if (!$sector) {
            return redirect()->to('/settings/sectors')->with('error', 'Sector not found.');
        }

        if (empty($name)) {
            return redirect()->to('/settings/sectors')->with('error', 'Sector name cannot be empty.');
        }

        $duplicate = $this->sectorModel->where('name', $name)->where('id !=', $id)->first();
        if ($duplicate) {
            return redirect()->to('/settings/sectors')->with('error', "Another sector named '{$name}' already exists.");
        }

        $oldName = $sector['name'];
        $this->sectorModel->update($id, [
            'name'        => $name,
            'description' => $description ?: null,
        ]);

        // If sector name changed, cascade update to equities table
        if ($oldName !== $name) {
            $db = \Config\Database::connect();
            $db->table('equities')->where('sector', $oldName)->update(['sector' => $name]);
        }

        return redirect()->to('/settings/sectors')->with('success', "Sector updated to '{$name}' successfully.");
    }

    /**
     * Delete an Equity Sector / Industry.
     */
    public function deleteSector(int $id)
    {
        $sector = $this->sectorModel->find($id);
        if (!$sector) {
            return redirect()->to('/settings/sectors')->with('error', 'Sector not found.');
        }

        $db = \Config\Database::connect();
        $stockCount = $db->table('equities')->where('sector', $sector['name'])->countAllResults();

        if ($stockCount > 0) {
            return redirect()->to('/settings/sectors')->with('error', "Cannot delete sector '{$sector['name']}' because it is currently assigned to {$stockCount} stock(s). Reassign them first.");
        }

        $this->sectorModel->delete($id);

        return redirect()->to('/settings/sectors')->with('success', "Sector '{$sector['name']}' deleted successfully.");
    }
}

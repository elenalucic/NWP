<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
 
    public function index()
    {
        $korisnici = User::all();
        return view('admin.users', compact('korisnici'));
    }

 
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,nastavnik,student',
        ]);

        $user->update(['role' => $request->role]);
        return redirect()->route('admin.users')->with('success', 'Uloga je uspješno ažurirana.');
    }
}
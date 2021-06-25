<?php


namespace App\Http\Controllers\User;

use App\Models\User;

class AdminSuplantacionController
{
    public function suplantar($id)
    {
        if (!auth()->user()->can("admin-users-suplantar")) {
            abort(404);
        }
        $user = User::findOrFail($id);

        $originalUser = auth()->id();

        if ($user->id!==$originalUser) {
            session()->put("original-user-suplantar", $originalUser);
            auth()->login($user);
        }

        return redirect("/");
    }

    public function revertir()
    {
        if (!session()->has("original-user-suplantar")) {
            abort(404);
        }

        $originalUser = session()->get("original-user-suplantar");
        auth()->loginUsingId($originalUser);
        session()->forget("original-user-suplantar");

        return redirect("admin/users");
    }
}

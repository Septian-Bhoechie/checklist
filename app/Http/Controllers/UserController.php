<?php

namespace Bhoechie\Checklist\Http\Controllers;

use Bhoechie\Checklist\Models\User;
use Illuminate\Http\Request;

/**
 * User controller.
 *
 * @author      bhoechie <septian.bhoechie@gmail.com>
 */
class UserController extends Controller
{

    /**
     * authenticating User
     * Route Path   : /api/user/login
     * Route Method : POST.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (app('hash')->check($request->input('password'), $user->password)) {
            $token = base64_encode(str_random(40));
            $user->update(['token' => "{$token}"]);
            return response()->json(['status' => 'success', 'token' => $token]);
        } else {
            return response()->json(['status' => 'fail'], 401);
        }
    }

    /**
     * show User
     * Route Path   : /api/user/show
     * Route Method : POST.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $userId)
    {
        $user = User::find($userId);

        if ($user instanceof User === false) {
            abort(404);
        }

        return response()->json($user);
    }
}

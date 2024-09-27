<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use App\Models\Evento;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() { 
        $this->middleware('can:admin.users.index')->only('index');
    }

    public function index(Request $request)
    {
        $name = $request->get('buscarpor');

        $users = User::where('name', 'LIKE', "%$name%")
                    ->where('id', '!=', 1)
                    ->with('roles')
                    ->get();

        foreach ($users as $user) {
            $user->rolesFormatted = $user->roles->pluck('name')->join(', ') ?: 'NINGUNO';
        }

        return view('admin.users.index', compact('users'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        if($request->hasFile('picture')){
            $file = $request->file('picture');
            $image_name=time().'_'.$file->getClientOriginalName();
            $file->move(public_path("/image"),$image_name);
        }
        /* request()->validate(Profile::$rules); */

        $user = User::create($request->all()+['image'=>$image_name]);

        return redirect()->route('admin.users.index', $user)->with('info', 'El usuario se creó con exito');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $users)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->roles()->sync($request->roles);

        return redirect()->route('admin.users.edit', $user)->with('info', 'Se asignaron los roles correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index', $user)->with('eliminar', 'ok');
    }
    
    
}

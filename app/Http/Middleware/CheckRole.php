<?php

namespace App\Http\Middleware;

use Closure;
use Session;

use App\Models\Role;
use App\Models\RoleMapping;
use App\Models\Menu;
use App\Models\MenuAction;

class CheckRole
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next)
    {
        $f_segment = $request->segment(1);
        $s_segment = $request->segment(2);
        $role_id = session()->get('user')->role_id;
        if(!$role_id){
            Session::flush();
            session()->flash('message.error', "You need role access, please contact admin!");
            return redirect()->route('login.index');
        }
        $role = Role::where(['id' => $role_id, 'is_active' => 1])->first();
        if(!$role){
            Session::flush();
            session()->flash('message.error', "Your role doesn't exists!");
            return redirect()->route('login.index');
        }
        $menu = RoleMapping::leftJoin(with(new Menu)->getTable(). ' as m', function($join){
                                    $join->on('m.id', with(new RoleMapping)->getTable().'.menu_id');
                                })
                                ->where(with(new RoleMapping)->getTable().'.role_id', $role_id)
                                ->where('m.slug', ($f_segment ? $f_segment : ''))
                                ->first(['m.menu', 'm.slug', with(new RoleMapping)->getTable().'.*']);
        if(!$menu){
            return redirect(route('dashboard.index'))->with(session()->flash('message.error', "Oops, Menu isn't available!"));
        } else {
            if($f_segment && $menu->view == 0){
                return redirect('/')->with(session()->flash('message.error', "Oops, You don't have permission to access menu $menu->menu!"));
            }
            if($s_segment == 'add' && $menu->add == 0){
                return redirect()->back()->with(session()->flash('message.error', "Oops, You don't have permission to $s_segment data on $menu->menu!"));
            }
            if($s_segment == 'edit' && $menu->edit == 0){
                return redirect()->back()->with(session()->flash('message.error', "Oops, You don't have permission to $s_segment data on $menu->menu!"));
            }
            if($s_segment == 'delete' && $menu->delete == 0){
                return redirect()->back()->with(session()->flash('message.error', "Oops, You don't have permission to $s_segment data on $menu->menu!"));
            }
        }
        $request->attributes->add(['role' => $menu]);
        return $next($request);

    }
}

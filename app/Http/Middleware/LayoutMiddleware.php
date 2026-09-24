<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

use App\User;
use App\Sessions;

use Cookie;
use HP;

class LayoutMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        //ถ้า Login
        if(auth()->check()){

            $user       = auth()->user();
            $session_id = session()->getId();

            //อัพเดท cookie
            $config = HP::getConfig();
            $minutes = config('session.lifetime');
            Cookie::queue($config->sso_name_cookie_login,
                          $session_id,
                          $minutes,
                          null,
                          $config->sso_domain_cookie_login,
                          null,
                          false
                      );

            //อัพเดท session ใน DB
            $session = Sessions::where('id', $session_id)->first();
            if(is_null($session)){//ไม่มีในฐานให้สร้าง
                Sessions::Add($session_id, $user->getKey(), $request->ip(), $request->userAgent(), 'web');
            }else{//อัพเดทเวลา
                Sessions::Modify($session_id);
            }

            //เช็คข้อมูลผู้ติดต่อ
            if(!HP::hasRequiredContactInfo($user)){

                $allow_pages = ['/profile/show', '/profile/update', '/funtions/set-cookie', '/funtions/get-cookie', '/logout', '/funtions/search-addreess', '/funtions/get-addreess'];//หน้าที่ให้เข้าได้
                $allow_or_not = false;
                foreach($allow_pages as $allow_item){
                    if(strpos($request->getPathInfo(), $allow_item) === 0 || 
                       strpos($request->getPathInfo(), $allow_item) === 1){
                        $allow_or_not = true;
                        break;
                    }
                }

                if($allow_or_not==false){
                    return redirect('/profile/show')->with('required_contact', '1');
                }
            
            }

        }

        $theme = ['normal','fix-header','mini-sidebar'];
        if(isset(request()->theme)){

            if($request->isMethod('get') && in_array($request->theme,$theme)){

                //ถ้าเปลี่ยน Layout type save ไปที่ user
                $user = User::findOrFail(auth()->user()->getKey());

                $params = (object)json_decode($user->params);
                $params->theme_layout = str_slug(request()->theme,'-');

                $user->params = json_encode($params);
                $user->save();

            }else{
                $query = $request->query();
            }

            return back();

        }

        return $next($request);

    }

}

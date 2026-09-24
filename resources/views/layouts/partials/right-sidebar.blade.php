<?php

    $theme_name = 'default';
    $fix_header = false;
    $fix_sidebar = false;
    $theme_layout = 'normal';

    if(auth()->user()){

        $params = (object)json_decode(auth()->user()->params);

        if(!empty($params->theme_name)){
            if(is_file('css/colors/'.$params->theme_name.'.css')){
                $theme_name = $params->theme_name;
            }
        }

        if(!empty($params->fix_header) && $params->fix_header=="true"){
            $fix_header = true;
        }

        if(!empty($params->fix_sidebar) && $params->fix_sidebar=="true"){
            $fix_sidebar = true;
        }

        if(!empty($params->theme_layout)){
            $theme_layout = $params->theme_layout;
        }
    }

?>

<div class="right-sidebar">
    <div class="slimscrollright">
        <div class="rpanel-title"> <b>แผงควบคุม</b> <span><i class="icon-close right-side-toggler"></i></span> </div>
        @if(auth()->check())
        <div class="text-center">
            <a class="btn btn-primary m-t-10" href="{{route('logout')}}">ออกจากระบบ</a>

        </div>
        @endif
        <div class="r-panel-body">
            @if(auth()->check())
            <p><b>แบบหน้าจอ</b></p>
                <ul class="layouts">
                    <li class="@if($theme_layout == 'normal') active @endif"><a
                                href="{{asset('?theme=normal')}}">ปกติ</a></li>
                    <li class="@if($theme_layout == 'fix-header') active @endif"><a
                                href="{{asset('?theme=fix-header')}}">เมนูด้านบน</a></li>
                    <li class="@if($theme_layout == 'mini-sidebar') active @endif"><a
                                href="{{asset('?theme=mini-sidebar')}}">แถบด้านข้างเล็ก</a></li>
                </ul>
                <br>
                @if($theme_layout != 'fix-header')
                    <ul class="hidden-xs">
                        <li><b>ตัวเลือกแบบหน้าจอ</b></li>
                        <li>
                            <div class="checkbox checkbox-danger">
                                <input id="headcheck" type="checkbox" class="fxhdr" @if($fix_header===true) checked @endif>
                                <label for="headcheck"> ตรึงส่วนหัว </label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox checkbox-warning">
                                <input id="sidecheck" type="checkbox" class="fxsdr" @if($fix_sidebar===true) checked @endif>
                                <label for="sidecheck"> ตรึงแถบด้านข้าง </label>
                            </div>
                        </li>
                    </ul>
                @endif
            @else
                <div class="text-center">
                    <a class="btn btn-primary m-t-10 " href="{{route('login')}}">LogIn</a> &nbsp;&nbsp;
                    <a class="btn btn-success m-t-10" href="{{route('register')}}">Register</a>
                </div>
            @endif

            <ul id="themecolors" class="m-t-20">
                <li><b>แถบด้านข้างโปร่งใส</b></li>
                <li><a href="javascript:void(0)" data-theme="default" class="default-theme @if($theme_name=='default') working @endif">1</a></li>
                <li><a href="javascript:void(0)" data-theme="green" class="green-theme @if($theme_name=='green') working @endif">2</a></li>
                <li><a href="javascript:void(0)" data-theme="yellow" class="yellow-theme @if($theme_name=='yellow') working @endif">3</a></li>
                <li><a href="javascript:void(0)" data-theme="red" class="red-theme @if($theme_name=='red') working @endif">4</a></li>
                <li><a href="javascript:void(0)" data-theme="purple" class="purple-theme @if($theme_name=='purple') working @endif">5</a></li>
                <li><a href="javascript:void(0)" data-theme="black" class="black-theme @if($theme_name=='black') working @endif">6</a></li>
                <li class="db"><b>แถบด้านข้างมืด</b></li>
                <li><a href="javascript:void(0)" data-theme="default-dark" class="default-dark-theme @if($theme_name=='default-dark') working @endif">7</a></li>
                <li><a href="javascript:void(0)" data-theme="green-dark" class="green-dark-theme @if($theme_name=='green-dark') working @endif">8</a></li>
                <li><a href="javascript:void(0)" data-theme="yellow-dark" class="yellow-dark-theme @if($theme_name=='yellow-dark') working @endif">9</a></li>
                <li><a href="javascript:void(0)" data-theme="red-dark" class="red-dark-theme @if($theme_name=='red-dark') working @endif">10</a></li>
                <li><a href="javascript:void(0)" data-theme="purple-dark" class="purple-dark-theme @if($theme_name=='purple-dark') working @endif">11</a></li>
                <li><a href="javascript:void(0)" data-theme="black-dark" class="black-dark-theme @if($theme_name=='black-dark') working @endif">12</a></li>
            </ul>
            
        </div>
    </div>
</div>

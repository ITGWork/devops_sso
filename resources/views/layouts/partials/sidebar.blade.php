@php
        $theme_name = 'default';
        $fix_header = false;
        $fix_sidebar = false;
        $theme_layout = '';

        if(auth()->user()){

            $user = auth()->user();

            $params = (object)json_decode($user->params);

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
                $theme_layout = $params->theme_layout;;
            }
        }

@endphp

<aside class="sidebar">
    <div class="scroll-sidebar">
        @if(auth()->check())
            @if($theme_layout != 'fix-header')
                <div class="user-profile">
                    <div class="dropdown user-pro-body ">
                        <div class="profile-image" id="profile-image">

                            @if($user->picture == null)
                                <img src="{{ asset('storage/uploads/users/no_avatar.jpg') }}" alt="user-img" class="img-circle">
                            @else
                                <img src="{{ HP::getFileStorage('sso_users/'.$user->picture) }}" alt="user-img" class="img-circle">
                            @endif

                            <a href="javascript:void(0);" class="dropdown-toggle u-dropdown text-blue"
                               data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <span class="badge badge-danger">
                                    <i class="fa fa-angle-down"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu animated flipInY">
                                <li><a href="{{ url('profile/show') }}"><i class="fa fa-user"></i>โปรไฟล์</a></li>
                                <li><a href="{{ url('profile/image-crop') }}"><i class="fa fa-camera"></i>เปลี่ยนภาพ</a></li>
                                <li><a href="{{ url('profile/password') }}"><i class="fa fa-key"></i>เปลี่ยนรหัสผ่าน</a></li>
                                <li><a href="{{ url('profile/google2fa') }}"><i class="fa fa-google"></i>ตั้งค่า 2FA</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="{{url('logout')}}"><i class="fa fa-power-off"></i>ออกจากระบบ</a></li>
                            </ul>
                        </div>
                        <p class="profile-text m-t-15 font-16"><a href="javascript:void(0);"> {{ auth()->user()->name }}</a></p>
                        <p class=" font-20"><a href="{{ url('profile/show') }}">โปรไฟล์</a></p>
                    </div>
                </div>
            @endif
            <nav class="sidebar-nav">
                <ul id="side-menu">

                    @php
                        $tisi_sso = App\Models\Config\ConfigsManual::where('site', 'tisi-sso' )->get()
                    @endphp

                    @if(  count($tisi_sso) > 0 )

                        <li>
                            <a class="waves-effect" href="{!! url('/manual') !!}" aria-expanded="false" target="_blank"><i  class="mdi mdi-book"></i> <span class="hide-menu">คู่มือ/ประกาศ</span>
                            </a>
                        </li>

                    @endif

                    @foreach($laravelAdminMenus->menus as $section)
                        @if(count(collect($section->items)) > 0)
                            @foreach($section->items as $menu)
                                @if(property_exists($menu, 'title') && $menu->title == 'counsels' && request()->getHost() == 'sso.tisi.go.th')
                                    @continue
                                @endif
                                @php
                                    $hasSub = property_exists($menu, 'sub_menus') && !empty($menu->sub_menus);
                                    $menuUrl = property_exists($menu, 'url') ? $menu->url : '#';
                                    $menuIcon = property_exists($menu, 'icon') ? $menu->icon : 'mdi mdi-menu';
                                    $branchBlock = property_exists($menu, 'branch_block') && $menu->branch_block == 1;
                                @endphp
                                @if($user->branch_type == 2 && $branchBlock)
                                    @continue
                                @endif
                                @if($hasSub)
                                    <li>
                                        <a class="waves-effect has-arrow" href="javascript:void(0);" aria-expanded="false">
                                            <i class="{{ $menuIcon }}" style="font-size:20px;"></i>
                                            <span class="hide-menu">{{ $menu->display }}</span>
                                        </a>
                                        <ul aria-expanded="false" class="collapse">
                                            @foreach($menu->sub_menus as $sub)
                                                <li>
                                                    <a href="{{ url($sub->url) }}">
                                                        @if(property_exists($sub, 'icon') && $sub->icon)
                                                            <i class="{{ $sub->icon }}"></i>
                                                        @endif
                                                        {{ $sub->display }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @else
                                    <li>
                                        <a class="waves-effect" href="{{ url($menuUrl) }}">
                                            <i class="{{ $menuIcon }}" style="font-size:20px;"></i>
                                            <span class="hide-menu">{{ $menu->display }}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach


                         {{--       <li>
                        <a class="active waves-effect" href="{{url('dashboard')}}" aria-expanded="false"><i
                                        class="icon-screen-desktop fa-fw"></i> <span
                                        class="hide-menu"> Dashboard </span></a>
                            @if(auth()->user()->isAdmin() == true)
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{asset('dashboard')}}">Modern Version</a></li>
                                <li><a href="{{asset('index2')}}">Clean Version</a></li>
                                <li><a href="{{asset('index3')}}">Analytical Version</a></li>
                                <li>
                                    <a class="waves-effect" href="javascript:void(0);" aria-expanded="false"><span
                                                class="hide-menu"> eCommerce </span></a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="{{asset('index4')}}">Dashboard</a></li>
                                        <li><a href="{{asset('products')}}">Products</a></li>
                                        <li><a href="{{asset('product-detail')}}">Product Detail</a></li>
                                        <li><a href="{{asset('product-edit')}}">Product Edit</a></li>
                                        <li><a href="{{asset('product-orders')}}">Product Orders</a></li>
                                        <li><a href="{{asset('product-cart')}}">Product Cart</a></li>
                                        <li><a href="{{asset('product-checkout')}}">Product Checkout</a></li>
                                    </ul>
                                </li>
                            </ul>
                            @endif --}}
                        {{-- </li>
                    @if(auth()->user()->isAdmin() == true)

                        <li><a class="waves-effect" href="{{asset('role-management')}}">
                                <i class=" icon-layers fa-fw"></i><span class="hide-menu"> Roles </span></a>
                        </li>
                        <li class="two-column">
                            <a class="waves-effect" href="javascript:void(0);" aria-expanded="false"><i
                                        class="icon-user fa-fw"></i> <span class="hide-menu"> Users</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{asset('users')}}">Manage Users</a></li>
                                <li><a href="{{asset('user/create')}}">Add New User</a></li>
                                <li><a href="{{asset('user/deleted')}}">Deleted Users</a></li>

                            </ul>
                        </li>
                        <li><hr /></li>
                        {{--<li><a class="waves-effect" href="{{asset('permission-management')}}"> <i--}}
                                        {{--class="icon-list fa-fw"></i><span class="hide-menu"> Permissions</span></a></li>--}}
                          {{--      <li><a class="waves-effect" href="{{asset('crud-generator')}}">
                                <i class="icon-drawar fa-fw"></i><span class="hide-menu"> CRUD Generator</span></a>
                        </li>
                        <li class="two-column">
                            <a class="waves-effect" href="javascript:void(0);" aria-expanded="false"><i
                                        class="icon-eye fa-fw"></i> <span class="hide-menu"> Logs</span></a>
                            <ul aria-exzpanded="false" class="collapse">
                                <li><a href="{{asset('log-viewer')}}">Laravel Log</a></li>
                                <li><a href="{{asset('activity-log')}}">Activity Log</a></li>

                            </ul>
                        </li>

                        @endif --}}
                        {{-- @foreach($laravelAdminMenus->menus as $section)
                            @if(count(collect($section->items)) > 0)
                                @foreach($section->items as $menu)
                                    @can('view-'.str_slug($menu->title))
                                        <li>
                                            <a class="waves-effect" href="{{ url($menu->url) }}">
                                                <i class="glyphicon {{$menu->icon}} fa-fw"></i>
                                                <span class="hide-menu">  {{ $menu->title }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                @endforeach
                            @endif
                        @endforeach --}}
                    {{-- <li> --}}
                        {{-- <a class="waves-effect" href="{{ url('account-settings') }}"> --}}
                            {{-- <i class="fa fa-gear fa-fw"></i> --}}
                            {{-- <span class="hide-menu"> Account Settigs</span> --}}
                        {{-- </a> --}}
                    {{-- </li> --}}
                    {{-- @if(auth()->user()->isAdmin() == true) --}}
                        {{-- <li><hr /></li>

                        <li class="two-column">
                            <a class="waves-effect" href="javascript:void(0);" aria-expanded="false"><i
                                        class="icon-equalizer fa-fw"></i> <span class="hide-menu"> UI Elements</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{asset('panels-wells')}}">Panels and Wells</a></li>
                                <li><a href="{{asset('panel-ui-block')}}">Panels With BlockUI</a></li>
                                <li><a href="{{asset('portlet-draggable')}}">Draggable Portlet</a></li>
                                <li><a href="{{asset('buttons')}}">Buttons</a></li>
                                <li><a href="{{asset('tabs')}}">Tabs</a></li>
                                <li><a href="{{asset('modals')}}">Modals</a></li>
                                <li><a href="{{asset('progressbars')}}">Progress Bars</a></li>
                                <li><a href="{{asset('notification')}}">Notifications</a></li>
                                <li><a href="{{asset('carousel')}}">Carousel</a></li>
                                <li><a href="{{asset('user-cards')}}">User Cards</a></li>
                                <li><a href="{{asset('timeline')}}">Timeline</a></li>
                                <li><a href="{{asset('timeline-horizontal')}}">Horizontal Timeline</a></li>
                                <li><a href="{{asset('range-slider')}}">Range Slider</a></li>
                                <li><a href="{{asset('ribbons')}}">Ribbons</a></li>
                                <li><a href="{{asset('steps')}}">Steps</a></li>
                                <li><a href="{{asset('session-idle-timeout')}}">Session Idle Timeout</a></li>
                                <li><a href="{{asset('session-timeout')}}">Session Timeout</a></li>
                                <li><a href="{{asset('bootstrap-ui')}}">Bootstrap UI</a></li>
                            </ul>
                        </li>
                        <li class="two-column">
                            <a class="waves-effect" href="javascript:void(0);" aria-expanded="false"><i
                                        class="icon-docs fa-fw"></i> <span class="hide-menu"> Pages</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{asset('starter-page')}}">Starter Page</a></li>
                                <li><a href="{{asset('blank')}}">Blank Page</a></li>
                                <li><a href="{{asset('search-result')}}">Search Result</a></li>
                                <li><a href="{{asset('custom-scroll')}}">Custom Scrolls</a></li>
                                <li><a href="{{asset('login')}}">Login Page</a></li>
                                <li><a href="{{asset('lock-screen')}}">Lock Screen</a></li>
                                <li><a href="{{asset('recoverpw')}}">Recover Password</a></li>
                                <li><a href="{{asset('animation')}}">Animations</a></li>
                                <li><a href="{{asset('profile')}}">Profile</a></li>
                                <li><a href="{{asset('invoice')}}">Invoice</a></li>
                                <li><a href="{{asset('gallery')}}">Gallery</a></li>
                                <li><a href="{{asset('pricing')}}">Pricing</a></li>
                                <li><a href="{{asset('register')}}">Register</a></li>
                                <li><a href="{{asset('400')}}">Error-400</a></li>
                                <li><a href="{{asset('403')}}">Error-403</a></li>
                                <li><a href="{{asset('404')}}">Error-404</a></li>
                                <li><a href="{{asset('500')}}">Error-500</a></li>
                                <li><a href="{{asset('503')}}">Error-503</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="waves-effect" href="javascript:void(0);" aria-expanded="false"><i
                                        class="icon-notebook fa-fw"></i> <span class="hide-menu"> Forms </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{asset('form-basic')}}">Basic Forms</a></li>
                                <li><a href="{{asset('form-layout')}}">Form Layout</a></li>
                                <li><a href="{{asset('icheck-control')}}">Icheck Control</a></li>
                                <li><a href="{{asset('form-advanced')}}">Form Addons</a></li>
                                <li><a href="{{asset('form-upload')}}">File Upload</a></li>
                                <li><a href="{{asset('form-dropzone')}}">File Dropzone</a></li>
                                <li><a href="{{asset('form-pickers')}}">Form-pickers</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="waves-effect" href="javascript:void(0);" aria-expanded="false"><i
                                        class="icon-grid fa-fw"></i> <span class="hide-menu"> Tables</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="{{asset('basic-table')}}">Basic Tables</a></li>
                                <li><a href="{{asset('table-layouts')}}">Table Layouts</a></li>
                                <li><a href="{{asset('data-table')}}">Data Table</a></li>
                                <li><a href="{{asset('bootstrap-tables')}}">Bootstrap Tables</a></li>
                                <li><a href="{{asset('responsive-tables')}}">Responsive Tables</a></li>
                                <li><a href="{{asset('editable-tables')}}">Editable Tables</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="waves-effect" href="javascript:void(0);" aria-expanded="false"><i
                                        class="icon-layers fa-fw"></i> <span class="hide-menu"> Extra</span></a>
                            <ul aria-expanded="false" class="collapse extra">
                                <li>
                                    <a class="waves-effect" href="javascript:void(0);" aria-expanded="false"><span
                                                class="hide-menu"> Inbox </span></a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="{{asset('inbox')}}">Mail Box</a></li>
                                        <li><a href="{{asset('inbox-detail')}}">Mail Details</a></li>
                                        <li><a href="{{asset('compose')}}">Compose Mail</a></li>
                                        <li><a href="{{asset('contact')}}">Contact</a></li>
                                        <li><a href="{{asset('contact-detail')}}">Contact Detail</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="{{asset('calendar')}}" aria-expanded="false"><span
                                                class="hide-menu">Calendar</span></a>
                                </li>
                                <li>
                                    <a href="{{asset('widgets')}}" aria-expanded="false"><span
                                                class="hide-menu"> Widgets </span></a>
                                </li>
                                <li>
                                    <a class="waves-effect" href="javascript:void(0);" aria-expanded="false"><span
                                                class="hide-menu"> Charts</span></a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="{{asset('morris-chart')}}">Morris Chart</a></li>
                                        <li><a href="{{asset('peity-chart')}}">Peity Charts</a></li>
                                        <li><a href="{{asset('knob-chart')}}">Knob Charts</a></li>
                                        <li><a href="{{asset('sparkline-chart')}}">Sparkline charts</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a class="waves-effect" href="javascript:void(0);" aria-expanded="false"><span
                                                class="hide-menu"> Icons</span></a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="{{asset('simple-line')}}">Simple Line</a></li>
                                        <li><a href="{{asset('fontawesome')}}">Fontawesome</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <a class="waves-effect" href="javascript:void(0);" aria-expanded="false"><span
                                                class="hide-menu"> Maps</span></a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="{{asset('map-google')}}">Google Map</a></li>
                                        <li><a href="{{asset('map-vector')}}">Vector Map</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li> --}}
                    {{-- @endif --}}


                </ul>
            </nav>
        @else

        @endif
    </div>
</aside>

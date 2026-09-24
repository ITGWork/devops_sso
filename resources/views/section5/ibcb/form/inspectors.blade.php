<fieldset class="white-box">
    <legend>ข้อมูลผู้ตรวจ/ผู้ประเมิน</legend>
    <div class="row">
        <div class="col-md-12 h5">

            @php
                $scope_list = App\Models\Section5\IbcbsScope::where('ibcb_id', $ibcb->id )->with('bs_branch_group')->select('branch_group_id')->groupBy('branch_group_id')->get();
            @endphp

            <div class="col-md-12 col-sm-12">
                <div id="accordion-ins">

                    @foreach ($scope_list as $group)
                        @php

                            //ข้อมูลพื้นฐานสาขาผลิตภัณฑ์
                            $bs_branch_group = $group->bs_branch_group;

                            //หาไอดีของรายสาขาที่อยู่ภายใต้สาขาผลิตภัณฑ์นี้ ที่ได้รับการรับรอง
                            $scope_ids  = App\Models\Section5\IbcbsScope::where('branch_group_id', $group->branch_group_id)->select('id');
                            $branch_ids = App\Models\Section5\IbcbsScopeDetail::whereIn('ibcb_scope_id', $scope_ids)->groupBy('branch_id')->select('branch_id');

                            //ดึงผู้ตรวจที่มีขอบข่ายรายสาขาของสาขาผลิตภัณฑ์และอยู่ภายใต้ IB นี้มา
                            $inspestor_list = DB::table((new App\Models\Section5\IbcbsInspectors)->getTable().' AS inspestor')
                                                ->leftJoin((new App\Models\Section5\InspectorsScope)->getTable().' AS scope', 'scope.inspectors_id', '=', 'inspestor.inspector_id')
                                                ->where('inspestor.ibcb_id', $ibcb->id)
                                                ->whereIn('scope.branch_id', $branch_ids)
                                                ->select('inspestor.inspector_prefix', 'inspestor.inspector_first_name', 'inspestor.inspector_last_name', 'scope.start_date', 'scope.end_date', 'scope.state')
                                                ->groupBy('inspestor.inspector_prefix', 'inspestor.inspector_first_name', 'inspestor.inspector_last_name', 'scope.start_date', 'scope.end_date', 'scope.state')
                                                ->get();

                            $StateHtml = [ 1 => '<span class="text-success">Active</span>', 2 => '<span class="text-danger">Not Active</span>' ];
                        @endphp

                        <div class="card">
                            <div class="card-header" id="headingTwo">
                                <h4 class="mb-0">
                                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapse-ins-{!! $bs_branch_group->id !!}" aria-expanded="true" aria-controls="collapse-ins-{!! $bs_branch_group->id !!}">
                                        <span class="mb-0 text-dark">{!! !empty($bs_branch_group->title)?$bs_branch_group->title:null !!}</span>
                                    </button>
                                </h4>
                            </div>
                            <div id="collapse-ins-{!! $bs_branch_group->id !!}" class="collapse in" aria-labelledby="headingTwo" data-parent="#accordion-ins">
                                <div class="card-body">
                                    <ul>
                                        @foreach (  $inspestor_list as $Iinspestor )
                                            @php
                                                $Iinspestor->IspesTorFullName = (!empty($Iinspestor->inspector_prefix)?$Iinspestor->inspector_prefix:null).($Iinspestor->inspector_first_name).' '.($Iinspestor->inspector_last_name);
                                                $color = ( $Iinspestor->end_date >= date('Y-m-d') )?'text-success':'text-danger';
                                                if(  $Iinspestor->end_date < date('Y-m-d') ){
                                                    $Iinspestor->state = 2;
                                                }

                                            @endphp
                                            <li>
                                                <span class="pull-left">
                                                    {!! !empty($Iinspestor->IspesTorFullName)?$Iinspestor->IspesTorFullName:null !!}
                                                </span>
                                                <span class="pull-right {!! $color !!}">
                                                    {!! array_key_exists( $Iinspestor->state, $StateHtml )?$StateHtml[ $Iinspestor->state ]:null !!} : {!! HP::revertDate($Iinspestor->end_date,true) !!}
                                                </span>
                                            </li>
                                        @endforeach
                                        @if( count($inspestor_list) == 0 )
                                            <li class="text-muted"><em>ไม่พบข้อมูลผู้ตรวจ/ผู้ประเมิน ในผลิตภัณฑ์ </em></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                    @endforeach

                </div>
            </div>

        </div>
    </div>
</fieldset>
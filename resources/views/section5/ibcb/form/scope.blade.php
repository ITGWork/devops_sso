<fieldset class="white-box">
    <legend>ข้อมูลขอบข่าย</legend>
    <div class="row">
        <div class="col-md-12 h5">
            <div class="table-responsive">
                <table class="table table-bordered scope-repeater" id="table-scope">
                    <thead>
                        <tr>
                            <th class="text-center" width="1%">ลำดับ</th>
                            <th class="text-center" width="33%">สาขาผลิตภัณฑ์</th>
                            <th class="text-center" width="29%">รายสาขา</th>
                            <th class="text-center" width="27%">มาตรฐาน มอก. เลขที่</th>
                            <th class="text-center" width="10%">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                            $data_scope =  App\Models\Section5\IbcbsScope::withCount('scopes_details')->Where('ibcb_id', $ibcb->id)->get();
                            $StateHtml = [ 1 => '<span class="text-success">Active</span>', 2 => '<span class="text-danger">Not Active</span>' ];
                        @endphp

                        @foreach ( $data_scope as $i => $item_scope )
                            @php
                                $bs_branch_group =  $item_scope->bs_branch_group;

                                $scopes_details =  $item_scope->scopes_details()->get();

                                $_details_first = $item_scope->scopes_details()->first();
                            @endphp
                            <tr>
                                <td  class="text-top text-center font_cuttom_td" rowspan="{!! ($item_scope->scopes_details_count >= 1)?$item_scope->scopes_details_count:1 !!}">
                                    {!! ++$i!!}
                                </td>
                                <td class="text-top font_cuttom_td" rowspan="{!! ($item_scope->scopes_details_count >= 1)?$item_scope->scopes_details_count:1 !!}">
                                    {!! !empty($bs_branch_group->title)?$bs_branch_group->title:'-' !!}
                                </td>
                                <td class="text-top font_cuttom_td">
                                    {!! !empty($_details_first->bs_branch->title)?$_details_first->bs_branch->title:'-' !!}
                                </td>
                                <td class="text-top font_cuttom_td">
                                    @if( !empty($_details_first) )

                                        @php
                                            $scopes_tis =  $_details_first->scopes_tis()
                                                                        ->with('scope_tis_std')
                                                                        ->select('tis_id')
                                                                        ->groupBy('tis_id')
                                                                        ->get();

                                            $list_std = [];
                                            foreach ($scopes_tis as $key => $tis) {
                                                $list_std[$tis->tis_id] = $tis->scope_tis_std->tb3_Tisno;
                                            }

                                            echo implode(', ', $list_std);
                                        @endphp

                                    @endif
                                </td>
                                <td class="text-top text-center font_cuttom_td" rowspan="{!! ($item_scope->scopes_details_count >= 1)?$item_scope->scopes_details_count:1 !!}">
                                    @if( $item_scope->type == 1 ) <!-- ใบสมัคร -->
                                        {!! ( !empty($item_scope->end_date) && $item_scope->end_date >= date('Y-m-d') ) && array_key_exists( $item_scope->state, $StateHtml )?$StateHtml[ $item_scope->state ]:'<span class="text-danger">Not Active</span>' !!}
                                    @else <!-- นำเข้า -->
                                        @if( !empty($item_scope->end_date) )
                                            {!! ( !empty($item_scope->end_date) && $item_scope->end_date >= date('Y-m-d') ) && array_key_exists( $item_scope->state, $StateHtml )?$StateHtml[ $item_scope->state ]:'<span class="text-danger">Not Active</span>' !!}
                                        @else
                                            {!! array_key_exists( $item_scope->state, $StateHtml )?$StateHtml[ $item_scope->state ]:'<span class="text-danger">Not Active</span>'  !!}
                                        @endif
                                    @endif
                                </td>
                            </tr>

                            @foreach ( $scopes_details as $item_details )


                                @if( $_details_first->id != $item_details->id )     
                                    <tr>
                                        <td class="text-top font_cuttom_td">
                                            {!! !empty($item_details->bs_branch->title)?$item_details->bs_branch->title:'-' !!}
                                        </td>
                                        <td class="text-top font_cuttom_td">
                                            @php
                                                $scopes_tis =  $item_details->scopes_tis()
                                                                            ->with('scope_tis_std')
                                                                            ->select('tis_id')
                                                                            ->groupBy('tis_id')
                                                                            ->get();

                                                $list_std = [];
                                                foreach ($scopes_tis as $key => $tis) {
                                                    $list_std[$tis->tis_id] = $tis->scope_tis_std->tis_tisno;
                                                }

                                                echo implode(', ', $list_std);
                                            @endphp
                                        </td>
                                    </tr>
                                @endif
                                
                            @endforeach
                            
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div> 
    </div>
</fieldset>
<div class="row white-box repeater-table-scope">
    <div class="col-md-12">
        <div class="row">
            <ul class="list-unstyled">
                @foreach ( $scope as $tis_key =>  $Sitem )

                    <li>
                       <h4> มอก. {!! array_key_exists($tis_key, $tis_tisno)?$tis_tisno[$tis_key]:null  !!} </h4>
                    </li>

                    <li>
                        <ul style="list-style-type: none;">
                            @foreach ( $Sitem as $item )

                                @php
                                    $test_item = $item->test_item;
                                @endphp
                                
                                <li>
                                    <div class="checkbox checkbox-info">
                                        <input type="checkbox" name="scope_lab_id" value="{!! $item->id !!}" id="scope_lab_item_{!! $item->id !!}">
                                        <label for="scope_lab_item_{!! $item->id !!}" class="h5">
                                            &nbsp; {!! !empty($test_item->title)?$test_item->title:'-' !!}
                                            <span class="text-muted"><em>(Exp. {!! !empty( $item->end_date )?HP::revertDate($item->end_date):'-' !!})</em></span>
                                        </label>
                                    </div>
                                </li>

                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
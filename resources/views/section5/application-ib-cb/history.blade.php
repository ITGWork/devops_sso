<div class="row">
    <div class="col-lg-12 col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <span class="h4">ประวัติการตรวจสอบคำขอ # {!! $applicationibcb->application_no !!}</span>
                <div class="pull-right">
                    <a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a>
                </div>
            </div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body" id="box-request">

                    <div class="table-responsive">
                        <table class="table-bordered table table-hover primary-table" id="table-history">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">ลำดับที่</th>
                                    <th width="18%" class="text-center">วันที่</th>
                                    <th width="25%" class="text-center">สถานะ</th>
                                    <th class="text-center">รายละเอียด</th>
                                    <th width="14%" class="text-center">ผู้ดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody data-repeater-list="repeater-history" id="box_list_history">

                                @php
                                    $accepts_history = $applicationibcb->application_ibcb_accepts()->get();
                                @endphp
                                
                                @foreach($accepts_history as $key=> $item)
                                    <tr>
                                        <td class="text-center">{{ ($key+1).'.' }}</td>
                                        <td>
                                            {{ HP::DateTimeThaiTormat_1($item->created_at) }}
                                        </td>
                                        <td>
                                            {{ $item->AppStatus }}
                                        </td>
                                        <td>
                                            {{ $item->description }}
                                        </td>
                                        <td class="text-center">
                                            {{ $item->RequestRecipient }}
                                        </td>
                                    </tr>
                                 @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
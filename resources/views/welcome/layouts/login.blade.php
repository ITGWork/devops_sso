<div class="container-fluid">
    <div class="white-box">

        <div class="form-horizontal form-material">
            <div class="row">
                <center>
                    <h4>
                        <img src="{!! asset('images/logo01.png') !!}" width="90" height="90">
                    </h4>
                </center>

                <h3 class="text-center box-title m-b-20">
                    TiSI
                </h3>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            @if( Auth::check() )
                                <p><h4>{!! Auth::user()->name  !!}</h4></p>
                                <a href="{{ url('/') }}" class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light">Dashboard </a>
                            @else
                                <a href="{{ url('/login') }}" class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light">หน้าลงชื่อเข้าใช้งาน</a>
                            @endif
                      
                        </div>
                    </div>
                </div>
            </div>

            @if( URL::current() != url('/contact') )
                <div class="row">
                    <div class="col-md-12">
                        <center>
                            <h4 class="sub-title">@Line</h4>
                            <h4>
                                <img src="{!! asset('images/QR-Code.png') !!}" width="120" height="120">
                            </h4>
                        </center>
                        <h6 class="detail-title">
                            ศูนย์เทคโนโลยีสารสนเทศและการสื่อสาร
                        </h6>
                        <h6 class="detail-title">
                            โทร. 0 2430 6834 ต่อ 2450, 2451
                        </h6>
                        <h6 class="detail-title">
                            e-Mail : nsw@tisi.mail.go.th
                        </h6>
                        <h6 class="detail-title" style="padding-top: 5px">
                            กองควบคุมมาตรฐาน
                        </h6>
                        <h6 class="detail-title">
                            โทร. 0 2430 6821 ต่อ 1002, 1003
                        </h6>
                        <h6 class="detail-title" style="padding-top: 5px">
                            สำนักงานคณะกรรมการการมาตรฐานแห่งชาติ
                        </h6>
                        <h6 class="detail-title">
                            โทร 024306825 ต่อ 1402
                        </h6>
                    </div>
                </div>
            @endif

        </div>



    </div>
</div>
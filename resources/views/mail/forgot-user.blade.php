<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        #style{
            padding: 5px;
            border: 5px solid gray;
            margin: 0;
        }
        .indent50 {
            text-indent: 50px;
        }
        table, th, td {
          border: 1px solid black;
          border-collapse: collapse;
          padding: 0px 10px;
        }
    </style>
</head>
<body>
   <div id="style">
        <p>
           <b>เรียน {{@$name}}</b>
        </p>
        <p>
            <b>เรื่อง แจ้งข้อมูลชื่อผู้ใช้งานระบบบริการอิเล็กทรอนิกส์ สมอ.</b>
        </p>
        <p class="indent50">
            ตามที่ {{@$name}} ได้ส่งคำขอทราบข้อมูลชื่อผู้ใช้งาน เมื่อ {{ HP::formatDateThai(date('Y-m-d')) }}
        </p>
        <p>สมอ. ขอแจ้งข้อมูล ดังนี้</p>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>ชื่อผู้ใช้งาน (Username)</th>
                    <th>ชื่อบริษัท</th>
                    <th>สาขา</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($users as $key => $user)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td><b>{{ $user->username }}</b></td>
                        <td>{{ $user->name }}</td>
                        <td>
                            @if($user->applicanttype_id!=2) {{-- ไม่ใช่บุคคลธรรมดา --}}
                                @if($user->branch_type==1)
                                    สำนักงานใหญ่
                                @elseif($user->branch_type==2)
                                    รหัสสาขา {{ $user->branch_code }}
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>

        <p>ท่านสามารถใช้ ชื่อผู้ใช้งาน (Username) และ รหัสผ่าน (Password) ของท่าน (ตามที่ท่านเคยลงทะเบียนไว้ในระบบ) โดยสามารถลงชื่อเข้าใช้งานได้จากลิงค์นี้
            {{ url('login') }}
        </p>
        <br>
        <p>จึงเรียนมาเพื่อโปรดรับทราบ</p>

    </div>
</body>
</html>

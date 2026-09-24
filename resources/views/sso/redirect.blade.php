<form id="myform" name="myform" action="{{$setting->urls}}" method="POST" enctype="multipart/form-data" class="form-horizontal">
    <input type="hidden" name="session_id" value="{{ session()->getId() }}"/>
</form>
<script>
    document.getElementById("myform").submit();
</script>

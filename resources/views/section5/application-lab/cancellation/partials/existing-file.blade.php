@php
    $existingFile = ($existingAttachFiles ?? collect())->get($section);
@endphp

@if($existingFile)
    @php
        $cleanPath = preg_replace('/\/+/', '/', $existingFile->url);
        $nasBase   = rtrim(env('FILESYSTEM_ROOT_URL', ''), '/');
        $fileUrl   = $nasBase . '/' . ltrim($cleanPath, '/');
    @endphp
    <div style="margin-bottom:5px;">
        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-xs btn-info">
            <i class="fa fa-file"></i> {{ $existingFile->filename }}
        </a>
        <span class="text-muted small">(อัปโหลดไฟล์ใหม่เพื่อแทนที่)</span>
    </div>
@endif

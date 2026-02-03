{{-- Quản lý files --}}

@php
ob_end_clean();
@endphp

<div>
{!!
Gallery::setup([
	"insert"=>true,//Nút trèn file
	"close"=>true,//Cho phép đóng 
	"hidden"=>false,//Mặc định sẽ ẩn
])
!!}
</div>
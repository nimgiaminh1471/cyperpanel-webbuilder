@extends("Default")
@php
$_Config=[
"title"       =>"Tiêu đề trang",
"description" =>"Mô tả trang",
"loading"     => true
];
@endphp


@section("mid")
{{$controllerName}}<br/>
{{$functionName}}<br/>
@endsection


@section("script")
<script></script>
@endsection


@section("footer")
@parent
@endsection

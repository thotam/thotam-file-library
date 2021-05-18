@extends('layouts.application')

@section('bodyclass')
    class="bg-primary"
@endsection

@section('styles')
    <!-- Page -->
    <link rel="stylesheet" href="{{ mix('/css/thotam/error.css') }}">
@endsection

@section('layout-content')
    <div class="overflow-hidden">
        <div class="container d-flex align-items-stretch ui-mh-100vh p-0">
            <div class="row w-100">
                <div class="d-flex col-md justify-content-center align-items-center order-2 order-md-1 position-relative p-5">
                    <div class="error-bg-skew bg-white"></div>

                    <div class="text-md-left text-center">
                        <h1 class="display-2 font-weight-bolder mb-4">{{ isset($title) ? $title : 'Đã có lỗi sảy ra...' }}</h1>
                        <div class="text-xlarge font-weight-light mb-5">{!! isset($text_xlarge) ? $text_xlarge : '' !!}</div>

                        <a href="{{URL::route('home')}}"><button type="button" class="btn btn-success mx-2">Trang chủ</button></a>
                    </div>
                </div>

                <div class="d-flex col-md-5 justify-content-center align-items-center order-1 order-md-2 text-center text-white p-5">
                    <div>
                        <div class="error-code font-weight-bolder mb-2">{{ isset($error_code) ? $error_code : '' }}</div>
                        <div class="error-description font-weight-light">{{ isset($error_description) ? $error_description : '' }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

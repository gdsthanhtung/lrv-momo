@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-3">
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
        </div>

        <div class="col-md-4">
            @include('momo')
        </div>
        <div class="col-md-4">
            @include('vnpay')
        </div>
        <div class="col-md-4">
            @include('zalopay')

        </div>
    </div>
</div>
@endsection

<style>
    #btn-momo {
        background-color: #d82d8b;
        border-color: #d82d8b;
    }
    #btn-vnpay {
        background-color: #005baa;
        border-color: #005baa;
    }
    #btn-zalopay {
        background-color: #00CF6A;
        border-color: #00CF6A;
    }
</style>

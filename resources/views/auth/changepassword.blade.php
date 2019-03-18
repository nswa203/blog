{{-- @extends('layouts.app') --}}
@extends('main')

@section('title',"| Change Password")

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                <div class="card-header">{{ __('Change password for ') }}{{ config('app.name') }}</div>

                    <div class="card-body">
                        <form class="form-horizontal" method="POST" action="{{ route('updatePassword') }}">
                            {{ csrf_field() }}

                            <div class="form-group row mt-2">
                                <label for="new-password" class="col-md-4 col-form-label text-md-right">{{ __('Current Password') }}</label>
                                <div class="col-md-6">
                                    <input id="current-password" type="password" class="form-control" name="current-password" required>
                                </div>
                            </div>

                            <div class="form-group row mt-2">
                                <label for="new-password" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>
                                <div class="col-md-6">
                                    <input id="new-password" type="password" class="form-control" name="new-password" required>
                                </div>
                            </div>

                            <div class="form-group row mt-2">
                                <label for="new-password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm New Password') }}</label>
                                <div class="col-md-6">
                                    <input id="new-password_confirmation" type="password" class="form-control" name="new-password_confirmation" required>
                                </div>
                            </div>
                 
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        {{ __('Change Password') }}
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

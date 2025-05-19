@extends('layouts.app')

@section('title', 'Company Settings')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('bakery.dashboard') }}">Bakery</a></li>
<li class="breadcrumb-item"><a href="#">Settings</a></li>
<li class="breadcrumb-item active">Company</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Company Information</h5>
                
                <form action="{{ route('bakery.settings.company.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $company->name ?? old('name') }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo">
                            @if(isset($company) && $company->logo)
                                <div class="mt-2">
                                    <img src="{{ $company->logo }}" alt="Company Logo" style="max-height: 50px;">
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ $company->address ?? old('address') }}</textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $company->phone ?? old('phone') }}">
                            </div>
                            
                            <div>
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $company->email ?? old('email') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website" value="{{ $company->website ?? old('website') }}">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="registration_number" class="form-label">Registration Number</label>
                            <input type="text" class="form-control" id="registration_number" name="registration_number" value="{{ $company->registration_number ?? old('registration_number') }}">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="tax_number" class="form-label">Tax Number</label>
                            <input type="text" class="form-control" id="tax_number" name="tax_number" value="{{ $company->tax_number ?? old('tax_number') }}">
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 
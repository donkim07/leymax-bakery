@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="pagetitle">
    <h1>Create User</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">System</li>
            <li class="breadcrumb-item"><a href="{{ route('settings.users') }}">User Management</a></li>
            <li class="breadcrumb-item active">Create User</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">New User Account</h5>
                    
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    <form method="POST" action="{{ route('settings.users.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">User Information</h5>
                                        
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                            <div class="invalid-feedback">Please enter the user's full name.</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required>
                                            <div class="invalid-feedback">Please enter a unique username.</div>
                                            <small class="text-muted">Username must be unique and can be used for login.</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                            <div class="invalid-feedback">Please enter a valid email address.</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                            <div class="invalid-feedback">Please enter a password.</div>
                                            <small class="text-muted">Password must be at least 8 characters long.</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                            <div class="invalid-feedback">Please confirm the password.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">User Role & Profile</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Assigned Roles</label>
                                            <div class="row">
                                                @foreach($roles as $role)
                                                <div class="col-md-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="role{{ $role->id }}">
                                                            {{ $role->name }}
                                                        </label>
                                                        <small class="d-block text-muted">{{ $role->description }}</small>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <div class="invalid-feedback">Please select at least one role.</div>
                                        </div>
                                        
                                        <hr>
                                        
                                        <div class="mb-3">
                                            <label for="avatar" class="form-label">Profile Picture</label>
                                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                            <small class="text-muted">Optional. Max size: 2MB. Allowed formats: JPG, PNG, GIF.</small>
                                        </div>
                                        
                                        <div class="mb-3 mt-4">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="sendInvite" name="send_invite" checked>
                                                <label class="form-check-label" for="sendInvite">Send welcome email to user</label>
                                            </div>
                                            <small class="text-muted">User will receive an email with login instructions.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('settings.users') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        var forms = document.querySelectorAll('.needs-validation');
        
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                // Check if at least one role is selected
                var roleCheckboxes = document.querySelectorAll('input[name="roles[]"]:checked');
                if (roleCheckboxes.length === 0) {
                    event.preventDefault();
                    document.querySelector('.form-check-input').classList.add('is-invalid');
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endsection 
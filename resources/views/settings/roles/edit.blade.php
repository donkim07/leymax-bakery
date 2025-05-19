@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="pagetitle">
    <h1>Edit Role</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">System</li>
            <li class="breadcrumb-item"><a href="{{ route('settings.roles') }}">Roles & Permissions</a></li>
            <li class="breadcrumb-item active">Edit Role</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Edit Role: {{ $role->name }}</h5>
                    
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
                    
                    <form method="POST" action="{{ route('settings.roles.update', $role->id) }}" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Role Information</h5>
                                        
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Role Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                                            <div class="invalid-feedback">Please enter a role name.</div>
                                            <small class="text-muted">Role name must be unique.</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                                            <small class="text-muted">Brief description of what this role can do.</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="guard_name" class="form-label">Guard</label>
                                            <input type="text" class="form-control" id="guard_name" name="guard_name" value="{{ old('guard_name', $role->guard_name) }}" readonly>
                                            <small class="text-muted">Authentication guard for this role.</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="business_type" class="form-label">Business Type</label>
                                            <select class="form-select" id="business_type" name="business_type">
                                                <option value="">All Business Types</option>
                                                <option value="bakery" {{ (old('business_type', $role->business_type) == 'bakery') ? 'selected' : '' }}>Bakery</option>
                                                <option value="cake_tools" {{ (old('business_type', $role->business_type) == 'cake_tools') ? 'selected' : '' }}>Cake Tools</option>
                                                <option value="academy" {{ (old('business_type', $role->business_type) == 'academy') ? 'selected' : '' }}>Academy</option>
                                            </select>
                                            <small class="text-muted">Restrict role to specific business type or leave empty for all.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Role Permissions</h5>
                                        
                                        <div class="mb-3">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                                <label class="form-check-label" for="selectAll">
                                                    <strong>Select/Deselect All Permissions</strong>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        
                                        @php
                                            $permissionsByGroup = $permissions->groupBy('group');
                                            $rolePermissionIds = $role->permissions->pluck('id')->toArray();
                                        @endphp
                                        
                                        @foreach($permissionsByGroup as $group => $groupPermissions)
                                        <div class="mb-4">
                                            <h6 class="mb-2">{{ ucfirst($group ?: 'General') }} Permissions</h6>
                                            
                                            <div class="row">
                                                @foreach($groupPermissions as $permission)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-checkbox" type="checkbox" 
                                                            name="permissions[]" 
                                                            value="{{ $permission->id }}" 
                                                            id="perm{{ $permission->id }}"
                                                            {{ in_array($permission->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                            <small class="d-block text-muted">{{ $permission->description }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Role Usage Information</h5>
                                        
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="card bg-light">
                                                    <div class="card-body py-3">
                                                        <h6 class="card-title mb-0">Users with this role</h6>
                                                        <p class="card-text fs-5">{{ $role->users->count() }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <div class="card bg-light">
                                                    <div class="card-body py-3">
                                                        <h6 class="card-title mb-0">Created On</h6>
                                                        <p class="card-text">{{ $role->created_at->format('M d, Y') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4 mb-3">
                                                <div class="card bg-light">
                                                    <div class="card-body py-3">
                                                        <h6 class="card-title mb-0">Last Updated</h6>
                                                        <p class="card-text">{{ $role->updated_at->format('M d, Y') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('settings.roles') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Role</button>
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
    $(document).ready(function() {
        // Handle select all checkbox
        $('#selectAll').change(function() {
            $('.permission-checkbox').prop('checked', $(this).prop('checked'));
        });
        
        // Update select all status based on individual checkboxes
        function updateSelectAllCheckbox() {
            $('#selectAll').prop('checked', $('.permission-checkbox:checked').length === $('.permission-checkbox').length);
        }
        
        // Initialize select all status
        updateSelectAllCheckbox();
        
        // Update on change
        $('.permission-checkbox').change(updateSelectAllCheckbox);
        
        // Form validation
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                // Validate at least one permission is selected
                if ($('.permission-checkbox:checked').length === 0) {
                    event.preventDefault();
                    showToast('error', 'Please select at least one permission for this role.');
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endsection 
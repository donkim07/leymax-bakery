@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="pagetitle">
    <h1>Roles & Permissions</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">System</li>
            <li class="breadcrumb-item active">Roles & Permissions</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">System Roles</h5>
                        <a href="{{ route('settings.roles.create') }}" class="btn btn-primary">
                            <i class="bi bi-shield-plus"></i> Add New Role
                        </a>
                    </div>
                    
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    
                    <!-- Roles Table -->
                    <div class="table-responsive">
                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Role Name</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Users Count</th>
                                    <th scope="col">Permissions</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->description }}</td>
                                    <td>{{ $role->users->count() }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $role->permissions->count() }} permissions</span>
                                        <button class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#permissionsModal" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('settings.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if(!in_array($role->name, ['Admin', 'Super Admin']))
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">System Permissions</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Permission Name</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Group</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $permissionsByGroup = $permissions->groupBy('group');
                                @endphp
                                
                                @foreach($permissionsByGroup as $group => $groupPermissions)
                                <tr class="table-primary">
                                    <td colspan="3"><strong>{{ ucfirst($group) }} Permissions</strong></td>
                                </tr>
                                    @foreach($groupPermissions as $permission)
                                    <tr>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->description }}</td>
                                        <td>{{ ucfirst($permission->group) }}</td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Permission Details Modal -->
    <div class="modal fade" id="permissionsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Permissions for <span id="roleNameDisplay"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @php
                            $permissionGroups = $permissions->pluck('group')->unique();
                        @endphp
                        
                        @foreach($permissionGroups as $group)
                        <div class="col-md-12 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ ucfirst($group) }}</h5>
                                    <div class="row permission-list" data-group="{{ $group }}">
                                        @foreach($permissions->where('group', $group) as $permission)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check permission-item" data-permission-id="{{ $permission->id }}">
                                                <input class="form-check-input" type="checkbox" disabled id="perm{{ $permission->id }}">
                                                <label class="form-check-label" for="perm{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                    <small class="d-block text-muted">{{ $permission->description }}</small>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Role Modal -->
    <div class="modal fade" id="deleteRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the role <strong id="deleteRoleName"></strong>?</p>
                    <p class="text-danger">This action cannot be undone and will remove this role from all users.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteRoleForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Role</button>
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
        // Initialize DataTable
        $('.datatable').DataTable({
            paging: true,
            pageLength: 10,
            ordering: true,
            info: true,
            responsive: true
        });
        
        // Role-specific permission data for all roles
        const rolePermissions = {
            @foreach($roles as $role)
                {{ $role->id }}: [
                    @foreach($role->permissions as $permission)
                        {{ $permission->id }},
                    @endforeach
                ],
            @endforeach
        };
        
        // Set up permissions modal
        $('#permissionsModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const roleId = button.data('role-id');
            const roleName = button.data('role-name');
            
            // Set the role name in the modal title
            $('#roleNameDisplay').text(roleName);
            
            // Reset all checkboxes
            $('.permission-item input').prop('checked', false);
            
            // Check the permissions for this role
            if (rolePermissions[roleId]) {
                rolePermissions[roleId].forEach(permId => {
                    $(`.permission-item[data-permission-id="${permId}"] input`).prop('checked', true);
                });
            }
        });
        
        // Set up delete role modal
        $('#deleteRoleModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const roleId = button.data('role-id');
            const roleName = button.data('role-name');
            
            $('#deleteRoleName').text(roleName);
            $('#deleteRoleForm').attr('action', '{{ url("settings/roles") }}/' + roleId);
        });
    });
</script>
@endsection 
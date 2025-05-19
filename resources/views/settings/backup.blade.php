@extends('layouts.app')

@section('title', 'Backup & Restore')

@section('content')
<div class="pagetitle">
    <h1>Backup & Restore</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">System</li>
            <li class="breadcrumb-item active">Backup & Restore</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">System Backups</h5>
                        <form action="{{ route('settings.backup.create') }}" method="POST" class="d-inline">
                            @csrf
                            <div class="btn-group">
                                <button type="submit" name="only_database" value="1" class="btn btn-primary">
                                    <i class="bi bi-database"></i> Database Backup
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-archive"></i> Full Backup
                                </button>
                            </div>
                        </form>
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
                    
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading"><i class="bi bi-info-circle"></i> Backup Information</h4>
                        <p>Backups allow you to restore your application in case of data loss or when migrating to a new server.</p>
                        <hr>
                        <p class="mb-0">
                            <strong>Database Backup:</strong> Only backs up the database.<br>
                            <strong>Full Backup:</strong> Backs up both database and application files.
                        </p>
                    </div>
                    
                    <!-- Backups Table -->
                    <div class="table-responsive">
                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th scope="col">Filename</th>
                                    <th scope="col">Size</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($backupsList) && count($backupsList) > 0)
                                    @foreach($backupsList as $backup)
                                    <tr>
                                        <td>{{ $backup['filename'] }}</td>
                                        <td>{{ round($backup['size'] / 1048576, 2) }} MB</td>
                                        <td>{{ date('F j, Y, g:i a', $backup['created_at']) }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('settings.backup.download', $backup['filename']) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteBackupModal" data-backup-file="{{ $backup['filename'] }}">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">No backups available</td>
                                    </tr>
                                @endif
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
                    <h5 class="card-title">Restore System</h5>
                    
                    <div class="alert alert-warning" role="alert">
                        <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Caution!</h4>
                        <p>Restoring a backup will replace your current data with the data from the backup file. This action cannot be undone.</p>
                        <hr>
                        <p class="mb-0">Make sure to create a backup of your current system before proceeding with a restore operation.</p>
                    </div>
                    
                    <form action="{{ route('settings.backup.restore') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="backupFile" class="form-label">Upload Backup File</label>
                                    <input class="form-control" type="file" id="backupFile" name="backup_file" accept=".zip">
                                    <div class="form-text">Select a .zip backup file to restore.</div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to restore this backup? All current data will be replaced.')">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restore System
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Backup Modal -->
    <div class="modal fade" id="deleteBackupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this backup file?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteBackupForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Backup</button>
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
        
        // Set up delete backup modal
        $('#deleteBackupModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var backupFile = button.data('backup-file');
            
            $('#deleteBackupForm').attr('action', '{{ url("settings/backup") }}/' + backupFile);
        });
    });
</script>
@endsection 
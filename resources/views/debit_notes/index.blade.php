<!DOCTYPE html>
<html lang="en">
<head>
    <title>Debit Notes</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body data-pc-theme="light">
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>

@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">

        <!-- Breadcrumb -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Debit Notes</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Debit Notes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        
                        <div class="float-end">
                            <a href="{{ route('debit-notes.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Create Debit Note
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        @include('includes.message')
                        
                        <div class="table-responsive">
                            <table id="debitNotesTable" class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Debit Note No</th>
                                        <th>Date</th>
                                        <th>Supplier</th>
                                        <th>Items Count</th>
                                        <th>Created By</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($debitNotes as $note)
                                    <tr>
                                        <td><strong>{{ $note->debit_note_no }}</strong></td>
                                        <td>{{ $note->debit_date->format('d-m-Y') }}</td>
                                        <td>{{ $note->supplier->supplier_name ?? '-' }}</td>
                                        <td>{{ $note->items->count() }}</td>
                                        <td>{{ $note->user->name ?? 'System' }}</td>
                                        <td>{{ $note->remarks ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('debit-notes.show', $note->id) }}" class="btn btn-info btn-sm" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <form action="{{ route('debit-notes.destroy', $note->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this debit note? Stock will be restored.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{ $debitNotes->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')

<script>
    $(document).ready(function() {
        $('#debitNotesTable').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: false
        });
    });
</script>

</body>
</html>
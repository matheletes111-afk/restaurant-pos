<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Manage Category</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

  <!-- DataTables CSS -->
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
                <h5 class="m-b-10">Manage Category</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item" aria-current="page">Manage Category</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- Breadcrumb end -->

      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            @include('includes.message')
            <div class="card-header">
              @if(
                  isset($plan_details)
                  && isset($plan_details->category_number)
                  && count($data ?? []) < $plan_details->category_number
              )
              <a href="#" class="btn btn-primary" style="float: right;" data-toggle="modal" data-target="#addCategoryModal">
                <i class="fa fa-plus"></i> Add Category
              </a>
              @endif
            </div>

            <div class="card-body">
              <div class="dt-responsive table-responsive">
                <table id="categoryTable" class="table table-striped table-bordered nowrap">
                  <thead>
                    <tr>
                      <th>Category Name</th>
                      <th>Category Image</th>
                      <th>Slug</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach(@$data as $value)
                    <tr>
                      <td>{{ @$value->name }}</td>
                      <td>
                        @if($value->image)
                          <img src="{{ URL::to('storage/app/public/category') }}/{{ @$value->image }}" alt="Category" width="60" height="60" style="object-fit: cover; border-radius: 6px;">
                        @else
                          <span class="text-muted">No Image</span>
                        @endif
                      </td>
                      <td>{{ @$value->slug }}</td>
                      <td>
                        <!-- Subcategory -->
                        <a href="{{ route('manage.subcategory.category', @$value->id) }}" class="btn btn-dark">
                          <i class="fa fa-list"></i>
                        </a>

                        <!-- Edit -->
                        <button class="btn btn-success edit-btn"
                                data-id="{{ $value->id }}"
                                data-name="{{ $value->name }}"
                                data-image="{{ $value->image }}">
                          <i class="fa fa-edit"></i>
                        </button>

                        <!-- Delete -->
                        <a href="{{ route('manage.category.delete', @$value->id) }}"
                           class="btn btn-danger"
                           onclick="return confirm('Are you sure want to delete this category?')">
                          <i class="fa fa-trash"></i>
                        </a>
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

    </div>
  </div>

  <!-- Add Category Modal -->
  <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="{{ route('manage.category.insert') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
            <button type="button" onclick="location.reload()" class="close" data-dismiss="modal" aria-label="Close">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Category Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Category Image</label>
              <input type="file" name="image" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" onclick="location.reload()" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Category Modal -->
  <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="{{ route('manage.category.update') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Category</h5>
            <button type="button" class="close" onclick="location.reload()" data-dismiss="modal" aria-label="Close">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Category Name</label>
              <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Category Image</label>
              <input type="file" name="image" class="form-control">
              <img id="edit_image_preview" src="" class="mt-2" width="70" height="70" style="object-fit: cover; border-radius: 6px;">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" onclick="location.reload()" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  @include('includes.script')

  <script>
    $(document).ready(function() {
      $('#categoryTable').DataTable();

      // Edit Button
      $('.edit-btn').on('click', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let image = $(this).data('image');

        $('#edit_id').val(id);
        $('#edit_name').val(name);
        if (image) {
          $('#edit_image_preview').attr('src', '{{ URL::to("storage/app/public/category") }}/' + image).show();
        } else {
          $('#edit_image_preview').hide();
        }
        $('#editCategoryModal').modal('show');
      });
    });
  </script>

</body>
</html>

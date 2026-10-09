@extends('layouts.app')

@section('title')
<title>Admin || Blog Posts</title>
@endsection

@section('style')
@include('includes.style')
<style>
    .post-title-link {
        color: #1e293b;
        font-weight: 600;
        text-decoration: none;
    }
    .post-title-link:hover {
        color: #ff6a00;
        text-decoration: underline;
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')
<div class="pc-container">
  <div class="pc-content">

    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="page-header-title">
              <h5 class="m-b-10">Blog Posts</h5>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item" aria-current="page">Blog Posts</li>
            </ul>
          </div>
          <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('admin.blog.create') }}" class="btn btn-primary shadow-sm">
              <i class="fas fa-plus me-1"></i> New Post
            </a>
          </div>
        </div>
      </div>
    </div>

    @include('includes.message')

    <div class="row mb-3 g-3">
      <div class="col-sm-4">
        <div class="card shadow-sm border-0">
          <div class="card-body p-3">
            <span class="text-muted text-uppercase fs-7 fw-semibold">Total Posts</span>
            <h4 class="mb-0 fw-bold">{{ number_format($stats['total']) }}</h4>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card shadow-sm border-0">
          <div class="card-body p-3">
            <span class="text-muted text-uppercase fs-7 fw-semibold">Published</span>
            <h4 class="mb-0 fw-bold text-success">{{ number_format($stats['published']) }}</h4>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card shadow-sm border-0">
          <div class="card-body p-3">
            <span class="text-muted text-uppercase fs-7 fw-semibold">Drafts</span>
            <h4 class="mb-0 fw-bold text-warning">{{ number_format($stats['draft']) }}</h4>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0 fw-bold">All Posts</h5>

            <form method="GET" action="{{ route('admin.blog.index') }}" class="d-flex flex-wrap align-items-center gap-2">
              <div class="input-group input-group-sm" style="width: 220px;">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Search title..." value="{{ request('search') }}">
              </div>

              <select name="status" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
              </select>

              @if(request('search') || (request('status') && request('status') !== 'all'))
                <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear filters">
                  <i class="fas fa-times"></i>
                </a>
              @endif
            </form>
          </div>

          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width: 60px;" class="ps-3">#ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th class="text-end pe-3" style="width: 220px;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($posts as $post)
                  <tr>
                    <td class="ps-3 text-muted fw-bold">#{{ $post->id }}</td>
                    <td>
                      <a href="{{ route('admin.blog.edit', $post) }}" class="post-title-link">{{ $post->title }}</a>
                      <div class="small text-muted">/blog/{{ $post->slug }}</div>
                    </td>
                    <td>{{ $post->author_name }}</td>
                    <td>
                      @if($post->status === 'published')
                        <span class="badge bg-success">Published</span>
                      @else
                        <span class="badge bg-warning text-dark">Draft</span>
                      @endif
                    </td>
                    <td class="small text-muted">
                      {{ $post->published_at ? $post->published_at->format('d M Y') : '—' }}
                    </td>
                    <td class="text-end pe-3">
                      @if($post->status === 'published')
                        <a href="{{ route('blog.show', $post) }}" target="_blank" class="btn btn-light btn-sm text-secondary me-1" title="View live">
                          <i class="fas fa-eye"></i>
                        </a>
                      @endif
                      <a href="{{ route('admin.blog.edit', $post) }}" class="btn btn-light btn-sm text-secondary me-1" title="Edit">
                        <i class="fas fa-pen"></i>
                      </a>
                      <form action="{{ route('admin.blog.toggle-publish', $post) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-light btn-sm {{ $post->status === 'published' ? 'text-warning' : 'text-success' }} me-1" title="{{ $post->status === 'published' ? 'Unpublish' : 'Publish' }}">
                          <i class="fas {{ $post->status === 'published' ? 'fa-eye-slash' : 'fa-upload' }}"></i>
                        </button>
                      </form>
                      <form action="{{ route('admin.blog.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this post permanently?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-light btn-sm text-danger" title="Delete">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center py-5">
                      <div class="text-muted">
                        <i class="fas fa-newspaper fa-3x mb-3 text-secondary opacity-50"></i>
                        <h6 class="fw-bold">No Blog Posts Yet</h6>
                        <p class="small text-muted mb-3">Write your first post to start building search traffic.</p>
                        <a href="{{ route('admin.blog.create') }}" class="btn btn-primary btn-sm">
                          <i class="fas fa-plus me-1"></i> New Post
                        </a>
                      </div>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            @if($posts->hasPages())
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
              <div class="text-muted small">
                Showing {{ $posts->firstItem() }} to {{ $posts->lastItem() }} of {{ $posts->total() }} posts
              </div>
              <div>
                {{ $posts->links() }}
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

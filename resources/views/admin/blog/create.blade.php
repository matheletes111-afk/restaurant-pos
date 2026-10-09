@extends('layouts.app')

@section('title')
<title>Admin || New Blog Post</title>
@endsection

@section('style')
@include('includes.style')
@endsection

@section('body')
@include('includes.sidebar')
<div class="pc-container">
  <div class="pc-content">

    <div class="page-header">
      <div class="page-block">
        <div class="page-header-title">
          <h5 class="m-b-10">New Blog Post</h5>
        </div>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Blog Posts</a></li>
          <li class="breadcrumb-item" aria-current="page">New Post</li>
        </ul>
      </div>
    </div>

    @include('includes.message')

    <div class="row">
      <div class="col-sm-12">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.blog.store') }}">
              @include('admin.blog._form')
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

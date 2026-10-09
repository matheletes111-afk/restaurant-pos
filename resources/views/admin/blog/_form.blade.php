@csrf
@if(isset($post))
    @method('PUT')
@endif

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold">Title</label>
        <input type="text" name="title" id="blog-title" class="form-control" value="{{ old('title', $post->title ?? '') }}" required>
        @error('title') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Slug</label>
        <input type="text" name="slug" id="blog-slug" class="form-control" value="{{ old('slug', $post->slug ?? '') }}" required>
        <div class="form-text">URL: /blog/<span id="slug-preview">{{ old('slug', $post->slug ?? '') }}</span></div>
        @error('slug') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Excerpt</label>
        <textarea name="excerpt" class="form-control" rows="2" maxlength="500" required>{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
        <div class="form-text">Short summary shown on the blog listing page (max 500 characters).</div>
        @error('excerpt') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Content (HTML)</label>
        <textarea name="content" class="form-control" rows="18" required style="font-family: monospace; font-size: 13px;">{{ old('content', $post->content ?? '') }}</textarea>
        <div class="form-text">Write in plain HTML — use &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;/&lt;li&gt; etc. This renders directly into the article.</div>
        @error('content') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label fw-semibold">Meta Description</label>
        <input type="text" name="meta_description" class="form-control" maxlength="300" value="{{ old('meta_description', $post->meta_description ?? '') }}" required>
        <div class="form-text">Shown in Google search results — keep it under ~160 characters for best display.</div>
        @error('meta_description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Author Name</label>
        <input type="text" name="author_name" class="form-control" value="{{ old('author_name', $post->author_name ?? '') }}" required>
        @error('author_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
            <option value="draft" {{ old('status', $post->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ old('status', $post->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-1"></i> {{ isset($post) ? 'Save Changes' : 'Create Post' }}
    </button>
    <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>

<script>
(function() {
    const titleInput = document.getElementById('blog-title');
    const slugInput = document.getElementById('blog-slug');
    const slugPreview = document.getElementById('slug-preview');
    let slugManuallyEdited = {{ isset($post) ? 'true' : 'false' }};

    function slugify(text) {
        return text.toString().toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    titleInput.addEventListener('input', function() {
        if (!slugManuallyEdited) {
            const slug = slugify(titleInput.value);
            slugInput.value = slug;
            slugPreview.textContent = slug;
        }
    });

    slugInput.addEventListener('input', function() {
        slugManuallyEdited = true;
        slugPreview.textContent = slugInput.value;
    });
})();
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.gtm_head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} - Bill&Bite Blog</title>
    <meta name="description" content="{{ $post->meta_description }}">
    <link rel="canonical" href="https://billnbite.com/blog/{{ $post->slug }}">
    <link rel="shortcut icon" href="{{ asset('fav_web.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="{{ asset('frontend/style.css') }}">
    <style>
        html { scroll-behavior: smooth; }

        .article-header {
            background: linear-gradient(135deg, #fffaf5 0%, #ffffff 100%);
            padding: 70px 0 40px;
            border-bottom: 1px solid #eaeaea;
        }
        .article-header .header-content { max-width: 800px; }
        .article-header h1 {
            font-size: 2.3rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 15px 0 18px;
            line-height: 1.3;
        }
        .article-meta {
            display: flex;
            align-items: center;
            gap: 18px;
            color: var(--text-light);
            font-size: 0.92rem;
            font-weight: 500;
        }
        .article-meta span { display: flex; align-items: center; gap: 6px; }

        .article-body-section { padding: 50px 0 90px; }
        .article-body {
            max-width: 780px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }
        .article-body h2 {
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 36px 0 16px;
        }
        .article-body h2:first-child { margin-top: 0; }
        .article-body h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 28px 0 12px;
        }
        .article-body p {
            color: #444444;
            line-height: 1.8;
            margin-bottom: 18px;
            font-size: 1.02rem;
        }
        .article-body ul, .article-body ol {
            margin-bottom: 20px;
            padding-left: 22px;
        }
        .article-body li {
            color: #444444;
            line-height: 1.75;
            margin-bottom: 10px;
        }
        .article-body table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
            font-size: 0.95rem;
        }
        .article-body table th, .article-body table td {
            border: 1px solid var(--border-color);
            padding: 12px 14px;
            text-align: left;
        }
        .article-body table th {
            background: var(--bg-subtle);
            font-weight: 700;
            color: var(--text-dark);
        }
        .article-body strong { color: var(--text-dark); }

        .article-cta {
            max-width: 780px;
            margin: 40px auto 0;
            background: var(--bg-subtle);
            border-radius: 16px;
            padding: 32px;
            text-align: center;
        }
        .article-cta p { color: var(--text-dark); font-weight: 600; margin-bottom: 16px; }

        .related-section {
            max-width: 780px;
            margin: 60px auto 0;
        }
        .related-section h3 { font-size: 1.2rem; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; }
        .related-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .related-list a {
            display: block;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 18px;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.92rem;
            text-decoration: none;
            line-height: 1.4;
        }
        .related-list a:hover { color: var(--primary-color); border-color: var(--primary-color); }
    </style>
</head>
<body>
@include('includes.gtm_body')

    <!-- Navbar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ asset('logo.png') }}" alt="Bill&Bite Logo" style="height: 40px; width: auto;">
            </a>
            <div class="nav-links">
                <a href="{{ route('home') }}#features">Features</a>
                <a href="{{ route('home') }}#pricing">Pricing</a>
                <a href="{{ route('blog.index') }}">Blog</a>
                <a href="{{ route('home') }}#faq">FAQ</a>
            </div>
            <div class="nav-actions">
                <a href="{{ route('blog.index') }}" class="btn-login"><i class="ph ph-arrow-left"></i> Back to Blog</a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="article-header">
        <div class="container">
            <div class="header-content">
                <h1>{{ $post->title }}</h1>
                <div class="article-meta">
                    <span><i class="ph ph-user-circle"></i> {{ $post->author_name }}</span>
                    <span><i class="ph ph-calendar"></i> {{ $post->published_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Article -->
    <section class="article-body-section">
        <div class="container">
            <div class="article-body">
                {!! $post->content !!}
            </div>

            <div class="article-cta">
                <p>Ready to try Bill&amp;Bite on your own counter?</p>
                <a href="{{ route('home') }}#pricing" class="btn btn-primary">See Free Plan <i class="ph ph-arrow-right"></i></a>
            </div>

            @if($relatedPosts->count())
                <div class="related-section">
                    <h3>More from the blog</h3>
                    <div class="related-list">
                        @foreach($relatedPosts as $related)
                            <a href="{{ route('blog.show', $related) }}">{{ $related->title }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" style="background:white !important; border-top: 1px solid #eaeaea;">
        <div class="container">
            <div class="footer-bottom" style="text-align: center; padding: 30px 0; color: var(--text-light); font-size: 0.9rem;">
                <p>© 2026 Bill & Bite. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

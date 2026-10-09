<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.gtm_head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Bill&Bite</title>
    <meta name="description" content="Restaurant POS guides, comparisons, and tips from the Bill&amp;Bite team — written for restaurant owners, cafes, and food stalls in India.">
    <link rel="canonical" href="https://billnbite.com/blog">
    <link rel="shortcut icon" href="{{ asset('fav_web.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="{{ asset('frontend/style.css') }}">
    <style>
        .blog-header {
            background: linear-gradient(135deg, #fffaf5 0%, #ffffff 100%);
            padding: 80px 0 50px;
            border-bottom: 1px solid #eaeaea;
        }
        .blog-header h1 {
            font-size: 2.6rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 15px 0 10px;
            letter-spacing: -0.5px;
        }
        .blog-header p { color: var(--text-light); font-size: 1.05rem; max-width: 650px; }

        .blog-grid-section { padding: 60px 0 90px; }
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 28px;
        }
        .blog-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column;
        }
        .blog-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.06);
        }
        .blog-card-banner {
            height: 140px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff9248 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .blog-card-banner i { font-size: 2.6rem; color: #ffffff; opacity: 0.9; }
        .blog-card-body { padding: 24px; flex: 1; display: flex; flex-direction: column; }
        .blog-card-date { color: var(--text-light); font-size: 0.82rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 10px; }
        .blog-card h2 { font-size: 1.2rem; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; line-height: 1.4; }
        .blog-card p { color: var(--text-light); font-size: 0.92rem; line-height: 1.6; margin-bottom: 18px; flex: 1; }
        .blog-card a.read-more { color: var(--primary-color); font-weight: 600; font-size: 0.92rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .blog-card a.read-more:hover { text-decoration: underline; }

        .blog-empty { text-align: center; padding: 80px 0; color: var(--text-light); }

        .blog-pagination { margin-top: 50px; display: flex; justify-content: center; }
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
                <a href="{{ route('home') }}#how-it-works">How It Works</a>
                <a href="{{ route('blog.index') }}">Blog</a>
                <a href="{{ route('home') }}#faq">FAQ</a>
            </div>
            <div class="nav-actions">
                <a href="{{ route('home') }}" class="btn-login"><i class="ph ph-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="blog-header">
        <div class="container">
            <span class="badge"><i class="ph ph-newspaper"></i> Bill&amp;Bite Blog</span>
            <h1>Restaurant POS guides, written plainly.</h1>
            <p>Buying advice, comparisons, and practical tips for restaurant owners, cafes, and food stall operators in India.</p>
        </div>
    </header>

    <!-- Posts grid -->
    <section class="blog-grid-section">
        <div class="container">
            @if($posts->count())
                <div class="blog-grid">
                    @foreach($posts as $post)
                        <article class="blog-card">
                            <div class="blog-card-banner"><i class="ph ph-article"></i></div>
                            <div class="blog-card-body">
                                <div class="blog-card-date">{{ $post->published_at->format('d M Y') }}</div>
                                <h2><a href="{{ route('blog.show', $post) }}" style="color:inherit; text-decoration:none;">{{ $post->title }}</a></h2>
                                <p>{{ $post->excerpt }}</p>
                                <a href="{{ route('blog.show', $post) }}" class="read-more">Read More <i class="ph ph-arrow-right"></i></a>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($posts->hasPages())
                    <div class="blog-pagination">{{ $posts->links() }}</div>
                @endif
            @else
                <div class="blog-empty">
                    <i class="ph ph-newspaper" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p style="margin-top: 16px;">No posts published yet — check back soon.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" style="background:white !important; border-top: 1px solid #eaeaea;">
        <div class="container">
            <div class="footer-bottom" style="text-align: center; padding: 30px 0; color: var(--text-light); font-size: 0.9rem;">
                <p style="margin-bottom: 10px;">
                    <a href="{{ route('about.us') }}" style="color: var(--text-light); margin: 0 10px;">About Us</a>
                    <a href="{{ route('blog.index') }}" style="color: var(--text-light); margin: 0 10px;">Blog</a>
                    <a href="{{ route('privacy.policy') }}" style="color: var(--text-light); margin: 0 10px;">Privacy Policy</a>
                    <a href="{{ route('terms.conditions') }}" style="color: var(--text-light); margin: 0 10px;">Terms &amp; Conditions</a>
                </p>
                <p>© 2026 Bill & Bite. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

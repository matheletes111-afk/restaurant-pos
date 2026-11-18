{{-- Open Graph / Twitter card tags. Pass: ogTitle, ogDescription, ogUrl, optional ogImage. --}}
@php
    $ogImage = $ogImage ?? asset('logo.png');
@endphp
<meta property="og:type" content="website">
<meta property="og:site_name" content="Bill&amp;Bite">
<meta property="og:locale" content="en_IN">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:url" content="{{ $ogUrl }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RestoPOS | Powering Your Restaurant's Success</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
:root{--primary:#FF6A00;--primary-light:#FF8C42;--bg:#0B0B0B;--secondary:#121212;--muted:#888888;--border:rgba(255,255,255,0.08);--glow:0 0 24px rgba(255,106,0,0.25)}
body{background:var(--bg);color:#fff;font-family:Inter,ui-sans-serif,system-ui,sans-serif;-webkit-font-smoothing:antialiased;overflow-x:hidden;line-height:1.5}
img{display:block;max-width:100%}
a{text-decoration:none;color:inherit}
.gradient-text{background:linear-gradient(to right,#FF6A00,#FF8C42);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.gradient-bg{background:linear-gradient(135deg,#FF6A00,#FF8C42)}
.shadow-glow{box-shadow:var(--glow)}
.glass{background:rgba(255,255,255,0.03);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.08)}
.wrap{width:100%;max-width:1280px;margin-inline:auto;padding-inline:clamp(1.25rem,5vw,3rem)}
@keyframes fade-in-up{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.animate-fade-in-up{animation:fade-in-up 0.6s ease-out both}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.animate-float{animation:float 4s ease-in-out infinite}
@keyframes ping{75%,100%{transform:scale(2);opacity:0}}

/* HEADER */
#main-header{position:fixed;inset:0 0 auto 0;z-index:100;padding-block:1.125rem;transition:background .3s,padding .3s,border-color .3s;border-bottom:1px solid transparent}
#main-header.scrolled{background:rgba(11,11,11,0.88);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border-color:var(--border);padding-block:.75rem;box-shadow:0 10px 30px -10px rgba(0,0,0,.5)}
.nav-row{display:flex;align-items:center;justify-content:space-between;gap:1rem}
.logo{display:flex;align-items:center;gap:.5rem;flex-shrink:0}
.logo-icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1rem;color:#fff;background:linear-gradient(135deg,#FF6A00,#FF8C42);box-shadow:var(--glow)}
.logo-text{font-size:1.2rem;font-weight:800;letter-spacing:-.02em}
.logo-text em{color:var(--primary);font-style:italic}
.nav-links{display:flex;align-items:center;gap:2rem;list-style:none}
.nav-links a{font-size:.875rem;font-weight:500;color:var(--muted);transition:color .2s}
.nav-links a:hover{color:#fff}
.nav-cta{display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.375rem;border-radius:999px;font-size:.875rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#FF6A00,#FF8C42);box-shadow:var(--glow);white-space:nowrap;transition:opacity .2s,transform .15s}
.nav-cta:hover{opacity:.9}.nav-cta:active{transform:scale(.97)}
#burger{display:none;flex-direction:column;gap:5px;padding:4px;background:none;border:none;cursor:pointer}
#burger span{display:block;width:24px;height:2px;background:#fff;border-radius:2px;transition:transform .3s,opacity .3s}
#burger.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
#burger.open span:nth-child(2){opacity:0}
#burger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}
#mobile-menu{display:none;position:fixed;inset:0;z-index:99;background:rgba(11,11,11,.97);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);flex-direction:column;align-items:center;justify-content:center;gap:2rem}
#mobile-menu.open{display:flex}
#mobile-menu a{font-size:1.75rem;font-weight:700;color:var(--muted);transition:color .2s}
#mobile-menu a:hover{color:#fff}
#mobile-menu .m-cta{margin-top:.5rem;padding:.875rem 2.5rem;border-radius:999px;background:linear-gradient(135deg,#FF6A00,#FF8C42);color:#fff!important;font-size:1rem!important;font-weight:700;box-shadow:var(--glow)}

/* HERO */
#hero{position:relative;padding-top:clamp(7rem,16vw,11rem);padding-bottom:clamp(4rem,8vw,7rem);overflow:hidden}
.hero-blob-r{position:absolute;top:0;right:0;transform:translate(25%,-25%);width:clamp(300px,50vw,600px);height:clamp(300px,50vw,600px);background:rgba(255,106,0,.10);filter:blur(120px);border-radius:50%;pointer-events:none}
.hero-blob-l{position:absolute;bottom:0;left:0;transform:translate(-25%,25%);width:clamp(200px,40vw,500px);height:clamp(200px,40vw,500px);background:rgba(255,106,0,.05);filter:blur(100px);border-radius:50%;pointer-events:none}
.hero-grid{display:grid;grid-template-columns:1fr 1fr;gap:clamp(2rem,5vw,4rem);align-items:center}
.hero-copy{z-index:1}
.hero-badge{display:inline-flex;align-items:center;gap:.5rem;padding:.4rem 1rem;border-radius:999px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);font-size:.8rem;font-weight:600;color:var(--primary-light);margin-bottom:1.5rem}
.ping-wrap{position:relative;display:inline-flex;width:8px;height:8px}
.ping-ring{position:absolute;inset:0;border-radius:50%;background:var(--primary);opacity:.75;animation:ping 1s cubic-bezier(0,0,.2,1) infinite}
.ping-dot-inner{position:relative;width:8px;height:8px;border-radius:50%;background:var(--primary)}
.hero-h1{font-size:clamp(2.6rem,6vw,4.5rem);font-weight:900;line-height:1.07;letter-spacing:-.03em;margin-bottom:1.25rem}
.hero-sub{font-size:clamp(1rem,1.8vw,1.15rem);color:var(--muted);max-width:480px;line-height:1.75;margin-bottom:2.5rem}
.hero-btns{display:flex;flex-wrap:wrap;gap:.875rem;margin-bottom:2.5rem}
.btn-main{display:inline-flex;align-items:center;gap:.5rem;padding:1rem 2rem;border-radius:16px;font-size:.95rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#FF6A00,#FF8C42);box-shadow:var(--glow);border:none;cursor:pointer;transition:opacity .2s,transform .15s}
.btn-main:hover{opacity:.9}.btn-main:active{transform:scale(.97)}
.btn-ghost{display:inline-flex;align-items:center;gap:.5rem;padding:1rem 2rem;border-radius:16px;font-size:.95rem;font-weight:700;color:#fff;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);cursor:pointer;transition:background .2s}
.btn-ghost:hover{background:rgba(255,255,255,.07)}
.play-circle{width:26px;height:26px;border-radius:50%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center}
.social-proof{display:flex;align-items:center;gap:1rem}
.avatar-stack{display:flex}
.avatar-stack img{width:38px;height:38px;border-radius:50%;border:2px solid var(--bg);object-fit:cover}
.avatar-stack img+img{margin-left:-10px}
.proof-text{font-size:.85rem;color:var(--muted)}
.proof-text strong{color:#fff}
.hero-visual{position:relative}
.hero-visual-glow{position:absolute;inset:0;background:rgba(255,106,0,.20);filter:blur(100px);border-radius:50%;pointer-events:none;transition:background .5s}
.hero-visual:hover .hero-visual-glow{background:rgba(255,106,0,.30)}
.hero-img-wrap{position:relative;border-radius:2.5rem;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.03);backdrop-filter:blur(12px);padding:8px;box-shadow:0 40px 80px -20px rgba(0,0,0,.6)}
.hero-img-wrap img{border-radius:2rem;width:100%;height:auto}
.float-badge{position:absolute;background:rgba(255,255,255,.06);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:.875rem 1.125rem;white-space:nowrap}
.float-badge strong{display:block;font-size:.875rem;font-weight:700}
.float-badge span{font-size:.75rem;color:var(--muted)}
.fb-tr{top:-1.25rem;right:-1.25rem}
.fb-bl{bottom:-1.25rem;left:-1.25rem}

/* STATS */
#stats{background:rgba(18,18,18,.5);border-block:1px solid rgba(255,255,255,.05);padding-block:clamp(2rem,4vw,3rem)}
.stats-inner{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:2rem}
.stats-tagline{font-size:clamp(1rem,2vw,1.2rem);font-weight:600;max-width:300px}
.stats-items{display:flex;flex-wrap:wrap;gap:clamp(2rem,5vw,5rem)}
.stat-item{display:flex;flex-direction:column;align-items:center}
.stat-icon{width:32px;height:32px;border-radius:8px;background:rgba(255,106,0,.1);display:flex;align-items:center;justify-content:center;color:var(--primary);margin-bottom:.4rem}
.stat-val{font-size:clamp(1.75rem,4vw,2.25rem);font-weight:800}
.stat-lbl{font-size:.7rem;font-weight:500;color:var(--muted);text-transform:uppercase;letter-spacing:.15em;margin-top:.1rem}

/* FEATURES */
#features{padding-block:clamp(4rem,8vw,6rem)}
.section-head{text-align:center;max-width:780px;margin-inline:auto;margin-bottom:clamp(2.5rem,5vw,4.5rem)}
.section-h2{font-size:clamp(2rem,4.5vw,3rem);font-weight:800;line-height:1.1;letter-spacing:-.025em;margin-bottom:1rem}
.section-sub{color:var(--muted);font-size:clamp(.9rem,1.8vw,1.05rem);line-height:1.7}
.features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.25rem}
.feat-card{position:relative;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:24px;padding:clamp(1.5rem,3vw,2.25rem);transition:border-color .3s,transform .3s;overflow:hidden}
.feat-card:hover{border-color:rgba(255,106,0,.2);transform:translateY(-4px)}
.feat-icon{width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,.05);display:flex;align-items:center;justify-content:center;color:var(--primary);margin-bottom:1.5rem;transition:background .3s,color .3s,box-shadow .3s}
.feat-card:hover .feat-icon{background:var(--primary);color:#fff;box-shadow:var(--glow)}
.feat-h3{font-size:1.1rem;font-weight:700;margin-bottom:.75rem}
.feat-p{color:var(--muted);font-size:.875rem;line-height:1.7;margin-bottom:1.25rem}
.feat-link{display:inline-flex;align-items:center;gap:.3rem;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,106,0,.8);transition:color .2s;background:none;border:none;cursor:pointer;padding:0}
.feat-card:hover .feat-link{color:var(--primary)}

/* GST */
#gst{padding-block:clamp(4rem,8vw,6rem)}
.gst-box{position:relative;background:linear-gradient(135deg,#FF6A00,#FF5500);border-radius:3rem;padding:clamp(2.5rem,6vw,5rem);display:grid;grid-template-columns:1fr 1fr;gap:clamp(2rem,5vw,4rem);align-items:center;overflow:hidden}
.gst-blob{position:absolute;top:0;right:0;transform:translate(50%,-50%);width:400px;height:400px;background:rgba(255,255,255,.10);filter:blur(80px);border-radius:50%;pointer-events:none}
.gst-copy{position:relative;z-index:1}
.gst-h2{font-size:clamp(2rem,4vw,3rem);font-weight:900;line-height:1.1;letter-spacing:-.025em;margin-bottom:1.25rem}
.gst-sub{color:rgba(255,255,255,.8);font-size:1rem;line-height:1.75;max-width:440px;margin-bottom:2rem}
.gst-items{display:flex;flex-direction:column;gap:.75rem;margin-bottom:2rem}
.gst-item{display:flex;align-items:center;gap:.875rem;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.15);border-radius:14px;padding:.875rem 1.25rem;font-size:.925rem;font-weight:700}
.gst-btn{display:inline-flex;align-items:center;gap:.5rem;padding:.875rem 1.75rem;border-radius:999px;background:#fff;color:var(--primary);font-weight:700;font-size:.925rem;border:none;cursor:pointer;transition:transform .2s}
.gst-btn:hover{transform:scale(1.04)}
.gst-img{position:relative;z-index:1}
.gst-img img{border-radius:24px;box-shadow:0 30px 60px rgba(0,0,0,.3);transform:scale(1.1)}

/* MODULES */
#modules{padding-block:clamp(4rem,8vw,6rem);background:rgba(18,18,18,.3)}
.modules-head{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:1.5rem;margin-bottom:clamp(2rem,4vw,3.5rem)}
.modules-label{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.2em;color:var(--primary);margin-bottom:.875rem}
.modules-h2{font-size:clamp(1.75rem,4vw,2.75rem);font-weight:800;line-height:1.15;letter-spacing:-.025em;max-width:480px}
.modules-right{color:var(--muted);font-size:.95rem;max-width:360px;line-height:1.7}
.modules-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.125rem}
.mod-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:24px;padding:clamp(1.5rem,3vw,2rem);transition:border-color .3s,transform .3s;cursor:pointer}
.mod-card:hover{border-color:rgba(255,255,255,.10);transform:translateY(-3px)}
.mod-icon{width:46px;height:46px;border-radius:12px;background:rgba(255,106,0,.1);display:flex;align-items:center;justify-content:center;color:var(--primary);margin-bottom:1rem;transition:background .3s,box-shadow .3s}
.mod-card:hover .mod-icon{background:var(--primary);color:#fff;box-shadow:var(--glow)}
.mod-title{font-size:1rem;font-weight:800;margin-bottom:.4rem}
.mod-desc{color:var(--muted);font-size:.82rem;line-height:1.65}

/* INTEGRATIONS */
#integrations{padding-block:clamp(4rem,8vw,6rem);text-align:center;border-block:1px solid rgba(255,255,255,.05);position:relative;overflow:hidden}
.int-logos{display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:clamp(2rem,6vw,6rem);filter:grayscale(1);transition:filter .7s}
#integrations:hover .int-logos{filter:grayscale(0)}
.int-logo{display:flex;align-items:center;gap:.6rem;cursor:pointer;transition:transform .25s}
.int-logo:hover{transform:scale(1.08)}
.int-icon{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:1.1rem;color:#fff}
.int-name{font-size:clamp(1rem,2.5vw,1.4rem);font-weight:700;letter-spacing:-.02em;color:rgba(255,255,255,.8)}
#integrations:hover .int-name{color:#fff}
.int-footer{margin-top:3.5rem;padding-top:2.5rem;border-top:1px solid rgba(255,255,255,.05);font-size:.875rem;color:var(--muted)}
.int-footer span{color:var(--primary);font-weight:700}

/* PRICING */
#pricing{padding-block:clamp(4rem,8vw,6rem);background:rgba(18,18,18,.2);position:relative;overflow:hidden}
#pricing::before{content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:800px;height:800px;background:rgba(255,106,0,.05);filter:blur(150px);border-radius:50%;pointer-events:none}
.pricing-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;align-items:center;position:relative;z-index:1}
.plan-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:3rem;padding:clamp(1.75rem,3vw,2.75rem);display:flex;flex-direction:column}
.plan-card.featured{border-color:rgba(255,106,0,.3);box-shadow:0 0 0 4px rgba(255,106,0,.08),var(--glow);transform:scaleY(1.02)}
.plan-badge{display:inline-block;background:var(--primary);color:#fff;font-size:.65rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;padding:.25rem .75rem;border-radius:999px;margin-bottom:1rem;box-shadow:var(--glow)}
.plan-tier{font-size:1.35rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;margin-bottom:1rem}
.plan-price{font-size:clamp(2rem,4vw,2.75rem);font-weight:900;letter-spacing:-.03em}
.plan-period{color:var(--muted);font-weight:700;font-size:.85rem;margin-bottom:.875rem}
.plan-desc{color:var(--muted);font-size:.875rem;line-height:1.65;margin-bottom:2rem}
.plan-features{flex:1;display:flex;flex-direction:column;gap:.875rem;margin-bottom:2rem}
.plan-feat{display:flex;align-items:center;gap:.6rem;font-size:.875rem;color:rgba(255,255,255,.8)}
.plan-feat svg{color:var(--primary);flex-shrink:0}
.plan-btn{width:100%;padding:1.1rem;border-radius:16px;border:none;cursor:pointer;font-size:.8rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;transition:opacity .2s,transform .15s}
.plan-btn:hover{opacity:.9;transform:translateY(-1px)}
.plan-btn.main{background:linear-gradient(135deg,#FF6A00,#FF8C42);color:#fff;box-shadow:var(--glow)}
.plan-btn.ghost{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);color:#fff}

/* TESTIMONIALS */
#testimonials{padding-block:clamp(4rem,8vw,6rem);border-block:1px solid rgba(255,255,255,.05)}
.testi-stars{display:flex;gap:.3rem;justify-content:center;margin-top:.75rem;color:var(--primary)}
.testi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.25rem;margin-top:clamp(2.5rem,5vw,4rem);text-align:left}
.testi-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:3rem;padding:clamp(1.75rem,3vw,2.5rem);transition:transform .3s}
.testi-card:hover{transform:translateY(-6px)}
.testi-author{display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem}
.testi-avatar{width:52px;height:52px;border-radius:50%;border:2px solid rgba(255,106,0,.2);object-fit:cover;flex-shrink:0;box-shadow:var(--glow)}
.testi-name{font-size:1.05rem;font-weight:700}
.testi-role{color:var(--muted);font-size:.7rem;text-transform:uppercase;letter-spacing:.12em;margin-top:.15rem}
.testi-quote{color:var(--muted);font-size:1.05rem;line-height:1.75;font-style:italic;transition:color .3s}
.testi-card:hover .testi-quote{color:rgba(255,255,255,.8)}

/* CTA */
#cta{padding-block:clamp(5rem,10vw,8rem);position:relative;overflow:hidden}
#cta::before{content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:800px;height:800px;background:rgba(255,106,0,.2);filter:blur(200px);border-radius:50%;opacity:.5;pointer-events:none}
.cta-box{position:relative;z-index:1;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:4rem;padding:clamp(3rem,8vw,7rem) clamp(1.5rem,6vw,5rem);text-align:center;box-shadow:0 0 0 8px rgba(255,255,255,.03)}
.cta-icon{width:80px;height:80px;border-radius:24px;background:linear-gradient(135deg,#FF6A00,#FF8C42);display:flex;align-items:center;justify-content:center;margin-inline:auto;margin-bottom:2rem;box-shadow:var(--glow)}
.cta-h2{font-size:clamp(2.25rem,6vw,4.5rem);font-weight:900;line-height:1.08;letter-spacing:-.03em;margin-bottom:1.25rem}
.cta-sub{color:rgba(255,255,255,.5);font-size:clamp(.95rem,2vw,1.15rem);max-width:520px;margin-inline:auto;margin-bottom:2.5rem;line-height:1.7;font-weight:500}
.cta-btns{display:flex;flex-wrap:wrap;gap:1rem;justify-content:center;margin-bottom:1.5rem}
.cta-note{font-size:.8rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.25em}

/* FOOTER */
footer{padding-block:clamp(3rem,6vw,5rem);border-top:1px solid rgba(255,255,255,.05)}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:clamp(2rem,4vw,4rem);margin-bottom:3.5rem}
.footer-about p{color:var(--muted);font-size:.875rem;margin-top:.875rem;max-width:260px;line-height:1.75}
.footer-col-title{font-size:.7rem;font-weight:900;text-transform:uppercase;letter-spacing:.2em;margin-bottom:1.5rem}
.footer-links{list-style:none;display:flex;flex-direction:column;gap:.875rem}
.footer-links a{color:var(--muted);font-size:.875rem;transition:color .2s}
.footer-links a:hover{color:var(--primary)}
.footer-links li{color:var(--muted);font-size:.875rem}
.footer-contact-row{display:flex;align-items:center;gap:.75rem;color:var(--muted);font-size:.875rem;margin-bottom:.875rem}
.footer-contact-row svg{color:var(--primary);flex-shrink:0}
.footer-bottom{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;padding-top:2rem;border-top:1px solid rgba(255,255,255,.05)}
.footer-copy{color:rgba(255,255,255,.3);font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em}

/* RESPONSIVE */
@media(max-width:768px){.nav-links,.nav-cta{display:none}#burger{display:flex}}
@media(max-width:900px){
.hero-grid{grid-template-columns:1fr}
.hero-copy{text-align:center}
.hero-sub{margin-inline:auto}
.hero-btns{justify-content:center}
.social-proof{justify-content:center}
.hero-visual{max-width:560px;margin-inline:auto}
.fb-tr{top:-.5rem;right:-.5rem}
.fb-bl{display:none}
.gst-box{grid-template-columns:1fr}
.gst-img{display:none}
.modules-head{flex-direction:column}
.modules-right{display:none}
.pricing-grid{grid-template-columns:1fr;max-width:420px;margin-inline:auto}
.plan-card.featured{transform:none}
.stats-inner{flex-direction:column;text-align:center}
.stats-tagline{text-align:center}
.stat-item{align-items:center}
.footer-grid{grid-template-columns:1fr 1fr}
.footer-about{grid-column:1/-1}
}
@media(max-width:480px){
.footer-grid{grid-template-columns:1fr}
.stats-items{gap:1.5rem}
.btn-ghost{display:none}
.float-badge{display:none}
.int-logos{gap:1.5rem}
.int-name{font-size:1rem}
}
</style>
</head>
<body>

<header id="main-header">
<div class="wrap">
<nav class="nav-row">
<a href="/" class="logo"><div class="logo-icon">R</div><span class="logo-text">Resto<em>POS</em></span></a>
<ul class="nav-links">
<li><a href="#">Home</a></li><li><a href="#features">Features</a></li><li><a href="#pricing">Pricing</a></li><li><a href="#integrations">Integrations</a></li><li><a href="#cta">Contact</a></li>
</ul>
<a href="#cta" class="nav-cta">Book Demo <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
<button id="burger" aria-label="Toggle menu"><span></span><span></span><span></span></button>
</nav>
</div>
</header>

<nav id="mobile-menu">
<a href="#" class="m-link">Home</a>
<a href="#features" class="m-link">Features</a>
<a href="#pricing" class="m-link">Pricing</a>
<a href="#integrations" class="m-link">Integrations</a>
<a href="#cta" class="m-link">Contact</a>
<a href="#cta" class="m-cta m-link">Book Demo</a>
</nav>

<section id="hero">
<div class="hero-blob-r"></div><div class="hero-blob-l"></div>
<div class="wrap">
<div class="hero-grid">
<div class="hero-copy animate-fade-in-up">
<div class="hero-badge"><span class="ping-wrap"><span class="ping-ring"></span><span class="ping-dot-inner"></span></span>Top Rated POS System in India</div>
<h1 class="hero-h1">Powering Your <br><span class="gradient-text">Restaurant&#39;s Success</span></h1>
<p class="hero-sub">All-in-One POS &amp; Restaurant Management Software designed for modern dining. Manage orders, inventory, and growth with lightning speed.</p>
<div class="hero-btns">
<button class="btn-main">Book a Free Demo <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></button>
<button class="btn-ghost"><span class="play-circle"><svg width="12" height="12" viewBox="0 0 24 24" fill="#fff"><polygon points="5 3 19 12 5 21 5 3"/></svg></span>Watch Video</button>
</div>
<div class="social-proof">
<div class="avatar-stack"><img src="https://i.pravatar.cc/100?img=11" alt=""><img src="https://i.pravatar.cc/100?img=12" alt=""><img src="https://i.pravatar.cc/100?img=13" alt=""><img src="https://i.pravatar.cc/100?img=14" alt=""></div>
<div class="proof-text">Joined by <strong>10,000+</strong> restaurant owners</div>
</div>
</div>
<div class="hero-visual animate-fade-in-up" style="animation-delay:.2s">
<div class="hero-visual-glow"></div>
<div class="hero-img-wrap"><img src="{{asset('hero-mockup.png')}}" alt="POS Dashboard Mockup"></div>
<div class="float-badge fb-tr animate-float"><strong>99.9% Uptime</strong><span>Reliable cloud POS</span></div>
<div class="float-badge fb-bl animate-float" style="animation-delay:.5s"><strong>Instant Sync</strong><span>Real-time data access</span></div>
</div>
</div>
</div>
</section>

<section id="stats">
<div class="wrap"><div class="stats-inner">
<div class="stats-tagline">Trusted by <span class="gradient-text" style="font-weight:800">Industry Leaders</span> worldwide</div>
<div class="stats-items">
<div class="stat-item"><div class="stat-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div><div class="stat-val">10,000+</div><div class="stat-lbl">Restaurants</div></div>
<div class="stat-item"><div class="stat-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></div><div class="stat-val">150+</div><div class="stat-lbl">Cities</div></div>
<div class="stat-item"><div class="stat-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"/><line x1="16" y1="8" x2="2" y2="22"/><line x1="17.5" y1="15" x2="9" y2="15"/></svg></div><div class="stat-val">100+</div><div class="stat-lbl">Integrations</div></div>
</div>
</div></div>
</section>

<section id="features">
<div class="wrap">
<div class="section-head"><h2 class="section-h2">Everything you need <br><span class="gradient-text">to grow your kitchen</span></h2><p class="section-sub">A comprehensive suite of tools built for restaurant owners who value speed, efficiency, and scale.</p></div>
<div class="features-grid">
<div class="feat-card"><div class="feat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg></div><h3 class="feat-h3">Smart POS &amp; Billing</h3><p class="feat-p">Cloud-sync POS that works offline. Blazing-fast billing with lightning-speed KOT generation.</p><button class="feat-link">Learn More <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></button></div>
<div class="feat-card"><div class="feat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></div><h3 class="feat-h3">Online Management</h3><p class="feat-p">Centralized dashboard for Swiggy, Zomato, and direct orders. Automated menu sync.</p><button class="feat-link">Learn More <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></button></div>
<div class="feat-card"><div class="feat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div><h3 class="feat-h3">Inventory Tracking</h3><p class="feat-p">Track stock in real-time. Automated low-stock alerts and purchase order management.</p><button class="feat-link">Learn More <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></button></div>
<div class="feat-card"><div class="feat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><h3 class="feat-h3">CRM &amp; Loyalty</h3><p class="feat-p">Build your customer database and run automated marketing campaigns to drive repeat orders.</p><button class="feat-link">Learn More <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></button></div>
</div>
</div>
</section>

<section id="gst">
<div class="wrap"><div class="gst-box">
<div class="gst-blob"></div>
<div class="gst-copy">
<h2 class="gst-h2">GST Ready <br>Invoicing System</h2>
<p class="gst-sub">Stay fully compliant with Indian tax regulations. Automated GST reporting, bulk invoicing, and easy tax filing &mdash; all built-in.</p>
<div class="gst-items">
<div class="gst-item"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Automated Tax Calculation</div>
<div class="gst-item"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15l-5-5L3 13"/><path d="M21 15H3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/></svg>GST-Compliant Invoices</div>
<div class="gst-item"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>E-Way Bill Support</div>
</div>
<button class="gst-btn">Learn about GST features <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></button>
</div>
<div class="gst-img animate-float"><img src="{{asset('invoice-illustration.png')}}" alt="GST Invoice illustration"></div>
</div></div>
</section>

<section id="modules">
<div class="wrap">
<div class="modules-head"><div><p class="modules-label">Advanced Tools</p><h2 class="modules-h2">Take Your Operations <br><span class="gradient-text">to the Next Level</span></h2></div><p class="modules-right">Beyond the basics. Professional tools designed to streamline every aspect of your restaurant ecosystem.</p></div>
<div class="modules-grid">
<div class="mod-card"><div class="mod-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></div><h4 class="mod-title">Table Management</h4><p class="mod-desc">Visual floor plan and occupancy tracking</p></div>
<div class="mod-card"><div class="mod-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l-2 7H4l6 4-2 7 4-3 4 3-2-7 6-4h-6z"/></svg></div><h4 class="mod-title">Kitchen Display</h4><p class="mod-desc">Real-time order statuses for chefs</p></div>
<div class="mod-card"><div class="mod-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg></div><h4 class="mod-title">Supplier &amp; Purchase</h4><p class="mod-desc">Track vendor bills and payments</p></div>
<div class="mod-card"><div class="mod-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><h4 class="mod-title">Sales Analytics</h4><p class="mod-desc">Detailed business performance reports</p></div>
</div>
</div>
</section>

<section id="integrations">
<div class="wrap" style="position:relative;z-index:1">
<div class="section-head" style="margin-bottom:clamp(2rem,4vw,3.5rem)">
<p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.25em;color:var(--primary);margin-bottom:1rem">Seamless Experience</p>
<h2 class="section-h2">Integrates with <br><span class="gradient-text">Your Favorite Apps</span></h2>
<p class="section-sub">Connect with leading delivery, payment, and logistics platforms to streamline your operations.</p>
</div>
<div class="int-logos">
<div class="int-logo"><div class="int-icon" style="background:#E23744">Z</div><span class="int-name">Zomato</span></div>
<div class="int-logo"><div class="int-icon" style="background:#FC8019">S</div><span class="int-name">Swiggy</span></div>
<div class="int-logo"><div class="int-icon" style="background:#0B72E7">R</div><span class="int-name">Razorpay</span></div>
<div class="int-logo"><div class="int-icon" style="background:#00BAF2">P</div><span class="int-name">Paytm</span></div>
</div>
<div class="int-footer">See all <span>100+ integrations</span> &rarr;</div>
</div>
</section>

<section id="pricing">
<div class="wrap">
<div class="section-head" style="margin-bottom:clamp(2.5rem,5vw,4rem)"><h2 class="section-h2">Simple, Transparent <br><span class="gradient-text">Pricing</span></h2><p class="section-sub">No hidden fees. Choose a plan that fits your business size and scale effortlessly as you grow.</p></div>
<div class="pricing-grid">
<div class="plan-card"><div class="plan-tier">Starter</div><div class="plan-price">&#8377;14,999</div><div class="plan-period">/year</div><p class="plan-desc">Perfect for small cafes and quick service restaurants.</p><div class="plan-features"><div class="plan-feat"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>100 Orders/day</div><div class="plan-feat"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Smart POS &amp; Billing</div><div class="plan-feat"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>GST Invoicing</div></div><button class="plan-btn ghost">Get Started</button></div>
<div class="plan-card featured"><div class="plan-badge">Most Popular</div><div class="plan-tier">Professional</div><div class="plan-price">&#8377;29,999</div><div class="plan-period">/year</div><p class="plan-desc">The complete solution for full-service restaurants.</p><div class="plan-features"><div class="plan-feat"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Unlimited Orders</div><div class="plan-feat"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Swiggy / Zomato Sync</div><div class="plan-feat"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Inventory Management</div></div><button class="plan-btn main">Go Pro</button></div>
<div class="plan-card"><div class="plan-tier">Premium</div><div class="plan-price">&#8377;49,999</div><div class="plan-period">/year</div><p class="plan-desc">Advanced tools for chains and multi-outlets.</p><div class="plan-features"><div class="plan-feat"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>All Pro Features</div><div class="plan-feat"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>CRM &amp; Loyalty</div><div class="plan-feat"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Dedicated Manager</div></div><button class="plan-btn ghost">Contact Sales</button></div>
</div>
</div>
</section>

<section id="testimonials">
<div class="wrap">
<div class="section-head">
<p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.25em;color:var(--primary);margin-bottom:1rem">Our Clients</p>
<h2 class="section-h2">Restaurateurs <span class="gradient-text">Love Us</span></h2>
<div class="testi-stars"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
</div>
<div class="testi-grid">
<div class="testi-card"><div class="testi-author"><img src="https://i.pravatar.cc/100?img=11" alt="Rahul" class="testi-avatar"><div><div class="testi-name">Rahul Mehrotra</div><div class="testi-role">Founder, Spice Garden</div></div></div><p class="testi-quote">&ldquo;This POS system completely transformed our workflow. Our billing speed increased by 40% and inventory management is now a breeze.&rdquo;</p></div>
<div class="testi-card"><div class="testi-author"><img src="https://i.pravatar.cc/100?img=22" alt="Priya" class="testi-avatar"><div><div class="testi-name">Priya Sharma</div><div class="testi-role">Manager, Little Italy</div></div></div><p class="testi-quote">&ldquo;The GST features alone saved us hours of paperwork every month. The support team is incredibly responsive and 24/7 access is a life-saver.&rdquo;</p></div>
<div class="testi-card"><div class="testi-author"><img src="https://i.pravatar.cc/100?img=33" alt="Vikram" class="testi-avatar"><div><div class="testi-name">Vikram Singh</div><div class="testi-role">Owner, The Brew Barn</div></div></div><p class="testi-quote">&ldquo;Seamless integration with Zomato and Swiggy made our life so much easier. No more switching between multiple tablets for online orders!&rdquo;</p></div>
</div>
</div>
</section>

<section id="cta">
<div class="wrap">
<div class="cta-box">
<div class="cta-icon animate-float"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg></div>
<h2 class="cta-h2">Start Growing Your <br><span class="gradient-text">Restaurant Today</span></h2>
<p class="cta-sub">Join 10,000+ restaurant owners who trust RestoPOS to manage their business flawlessly.</p>
<div class="cta-btns">
<button class="btn-main" style="font-size:1.05rem;padding:1.1rem 2.25rem">Book Free Demo Now <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></button>
<button class="btn-ghost" style="font-size:1.05rem;padding:1.1rem 2.25rem">View Pricing</button>
</div>
<p class="cta-note">No Credit Card Required &nbsp;&middot;&nbsp; Set up in 10 mins</p>
</div>
</div>
</section>

<footer>
<div class="wrap">
<div class="footer-grid">
<div class="footer-about"><div class="logo"><div class="logo-icon">R</div><span class="logo-text">Resto<em>POS</em></span></div><p>Helping restaurant owners scale their business with intelligent, lightning-fast POS technology. Built for India.</p></div>
<div><div class="footer-col-title">Quick Links</div><ul class="footer-links"><li><a href="#">About Us</a></li><li><a href="#">Careers</a></li><li><a href="#">Privacy Policy</a></li></ul></div>
<div><div class="footer-col-title">Features</div><ul class="footer-links"><li>GST Invoicing</li><li>Inventory Control</li><li>Order Management</li></ul></div>
<div><div class="footer-col-title">Contact Us</div><div class="footer-contact-row"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>support@restopos.in</div><div class="footer-contact-row"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.77 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>+91 98765 43210</div></div>
</div>
<div class="footer-bottom"><span class="footer-copy">&copy; 2026 RestoPOS. All rights reserved.</span><span class="footer-copy">Developed by Antigravity AI</span></div>
</div>
</footer>

<script>
const hdr=document.getElementById("main-header");
window.addEventListener("scroll",()=>{hdr.classList.toggle("scrolled",window.scrollY>20)},{passive:true});
const burger=document.getElementById("burger");
const mobileMenu=document.getElementById("mobile-menu");
burger.addEventListener("click",()=>{const open=burger.classList.toggle("open");mobileMenu.classList.toggle("open",open);document.body.style.overflow=open?"hidden":"";});
document.querySelectorAll(".m-link").forEach(l=>{l.addEventListener("click",()=>{burger.classList.remove("open");mobileMenu.classList.remove("open");document.body.style.overflow="";});});
</script>
</body>
</html>
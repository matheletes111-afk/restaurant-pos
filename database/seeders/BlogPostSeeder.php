<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run()
    {
        $posts = [
            [
                'title' => 'Bill&Bite vs Petpooja: Which Restaurant POS Is Right for You?',
                'slug' => 'billnbite-vs-petpooja-restaurant-pos-comparison',
                'excerpt' => 'An honest, feature-by-feature look at Bill&Bite and Petpooja — what each is built for, where they differ, and how to decide which one fits your restaurant or food stall.',
                'meta_description' => 'Bill&Bite vs Petpooja comparison: pricing approach, setup, QR ordering, KOT, and who each POS is really built for. An honest guide for Indian restaurant owners.',
                'author_name' => 'Rishav Kumar',
                'content' => $this->vsPetpoojaContent(),
            ],
            [
                'title' => 'How to Choose a Restaurant POS System: A Buyer\'s Guide for Indian Restaurants and Food Stalls',
                'slug' => 'how-to-choose-restaurant-pos-system-buyers-guide',
                'excerpt' => 'Picking a POS is easy to get wrong. Here are the questions worth asking before you commit — written for everything from a single food cart to a multi-table restaurant.',
                'meta_description' => 'A practical buyer\'s guide to choosing a restaurant POS system in India — covers pricing, setup time, GST billing, offline support, and hardware needs.',
                'author_name' => 'Vikrant Singh',
                'content' => $this->buyersGuideContent(),
            ],
            [
                'title' => 'QR Code Ordering: Why Contactless Menus Are Becoming Standard in Indian Restaurants',
                'slug' => 'qr-code-ordering-contactless-menus-indian-restaurants',
                'excerpt' => 'QR ordering isn\'t just a pandemic leftover. Here\'s why it\'s sticking around, what it actually changes on the floor, and how to roll it out without annoying your regulars.',
                'meta_description' => 'Why QR code ordering is becoming standard in Indian restaurants, what it changes for staff and customers, and how to set it up without disrupting service.',
                'author_name' => 'Anish',
                'content' => $this->qrOrderingContent(),
            ],
            [
                'title' => 'Why Small Food Stalls and Carts Need (Affordable) Restaurant Software Too',
                'slug' => 'affordable-restaurant-software-small-food-stalls-carts',
                'excerpt' => 'Most restaurant software is built and priced for restaurants that already have 5 outlets. Here\'s why we built Bill&Bite for the ones that don\'t.',
                'meta_description' => 'Why small food stalls, carts, and single-counter shops in India have been left out of restaurant software — and what affordable POS software should look like for them.',
                'author_name' => 'Rishav Kumar',
                'content' => $this->smallStallsContent(),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(
                ['slug' => $post['slug']],
                array_merge($post, [
                    'status' => 'published',
                    'published_at' => now(),
                ])
            );
        }
    }

    private function vsPetpoojaContent()
    {
        return <<<'HTML'
<p>If you've started looking for restaurant POS software in India, you've almost certainly come across Petpooja. It's one of the more established names in the space, and a lot of restaurants use it. So it's a fair question: why would you look at Bill&amp;Bite instead?</p>

<p>Here's an honest answer, not a sales pitch.</p>

<h2>What Petpooja is built for</h2>
<p>Petpooja has been around for years and has built out a wide feature set aimed at restaurants that are already running real volume — often multiple outlets, larger teams, and more complex menu and billing setups. If you're a restaurant group with several locations and need a mature, full-featured system, it's a reasonable option to evaluate.</p>

<h2>What Bill&amp;Bite is built for</h2>
<p>We started Bill&amp;Bite in 2026 with a narrower, more specific problem in mind: most restaurant software we looked at was priced and designed for restaurants that already had the scale to justify it. A tea stall, a single-counter shop, or a food cart doesn't need — or want to pay for — the same setup as a 5-outlet chain.</p>
<p>So Bill&amp;Bite is built to work for both ends of that range, but we specifically did not want cost to be the reason a small food business stays on pen and paper. That's why we currently offer a <strong>free basic subscription</strong> to restaurant owners and food stall operators of any size.</p>

<h2>Feature-by-feature</h2>
<table>
<tr><th>Area</th><th>Bill&amp;Bite</th><th>Petpooja</th></tr>
<tr><td>Who it's built for</td><td>Everything from food carts to full restaurants</td><td>Primarily established restaurants and multi-outlet chains</td></tr>
<tr><td>Entry cost</td><td>Free basic plan</td><td>Paid plans (confirm current pricing directly with them)</td></tr>
<tr><td>Setup time</td><td>Under 10 minutes to get started</td><td>Typically involves onboarding support for larger setups</td></tr>
<tr><td>Order management</td><td>Dine-in, takeaway, and online orders in one dashboard</td><td>Supported, with broader POS integrations for larger operations</td></tr>
<tr><td>QR code ordering</td><td>Built in</td><td>Available</td></tr>
<tr><td>Kitchen (KOT) tracking</td><td>Built in</td><td>Available, with more advanced routing for multi-outlet kitchens</td></tr>
<tr><td>Inventory management</td><td>Real-time stock tracking with low-stock alerts</td><td>Available, generally aimed at larger inventory operations</td></tr>
<tr><td>Support</td><td>Direct access to the small team that built the product, 24/7</td><td>Established support infrastructure for a larger customer base</td></tr>
</table>

<p>We'd rather be straight about this than pretend there's no tradeoff: Petpooja has had longer to build out features for complex, high-volume restaurant operations. If that's genuinely what you run, it's worth evaluating seriously.</p>

<h2>Who should pick Bill&amp;Bite</h2>
<ul>
<li>You're a single-location restaurant, cafe, cloud kitchen, or food stall that wants real POS software without a large-restaurant price tag.</li>
<li>You want to be up and running the same day, not after a multi-step onboarding process.</li>
<li>You'd rather talk to the people who actually built the software than a general support queue.</li>
<li>You're testing whether a POS system is worth it for your business at all, and don't want to commit money to find out.</li>
</ul>

<h2>The honest bottom line</h2>
<p>If you're running a large, multi-outlet restaurant group with complex operational needs, Petpooja is a legitimate option worth evaluating alongside others. If you're a smaller restaurant, cafe, or food stall that's been priced out of "restaurant software" entirely, that's exactly who we built Bill&amp;Bite for — and you can try the free plan today to see if it fits.</p>
HTML;
    }

    private function buyersGuideContent()
    {
        return <<<'HTML'
<p>Every restaurant owner we talk to has a version of the same story: they tried one POS system, it didn't fit, and now they're wary of switching again. So before you pick one, here's what's actually worth checking — not marketing language, just the practical stuff.</p>

<h2>1. What does it actually cost, and what triggers extra charges?</h2>
<p>Look past the headline price. Ask what happens when you add a second staff login, a second table, or a second month without paying. Some systems are cheap to start and expensive to actually use day-to-day. If you're a small stall or a single-counter shop testing whether software is worth it at all, a genuinely free basic tier (not just a free trial) removes the risk of committing money before you know it works for you.</p>

<h2>2. How long does setup actually take?</h2>
<p>If "onboarding" involves a scheduled call, a multi-day setup process, or hardware that has to be shipped and configured, that's a real cost — in time, not just money. For a lot of small restaurants, a system that lets you add your menu and start taking orders within minutes matters more than a long feature list you'll never fully use.</p>

<h2>3. Does it handle GST billing correctly?</h2>
<p>This sounds basic, but it trips up a surprising number of systems once you have mixed tax rates on a menu (packaged items vs. prepared food, for example) or need proper GST-compliant invoices for business customers. Ask to see an actual sample invoice before you commit, not just a feature checklist.</p>

<h2>4. What happens when your internet goes down?</h2>
<p>Power cuts and patchy internet are a real part of running a restaurant in most of India. Find out whether the system can keep taking orders locally and sync later, or whether it simply stops working. This is one of those things that only matters once — the first time it happens during a busy service.</p>

<h2>5. Does it match how your kitchen actually runs?</h2>
<p>Kitchen Order Ticket (KOT) routing sounds like a minor feature until you're relying on it during a rush. If your kitchen has one station, you need simple and fast. If you have multiple stations (tandoor, grills, beverages), you need orders routed to the right place automatically. Mismatch here causes real chaos on the floor.</p>

<h2>6. QR ordering — do you actually need it yet?</h2>
<p>QR code ordering is genuinely useful for sit-down restaurants and cafes with table service. It's less relevant for a takeaway-only counter or a food cart. Don't pay for (or get distracted by) features that don't match how your customers actually order from you.</p>

<h2>7. Inventory — how much do you need, honestly?</h2>
<p>A food cart with 8 menu items doesn't need enterprise inventory forecasting. A restaurant managing raw ingredients across 40 dishes does. Match the feature to your actual operation size, not what sounds impressive in a demo.</p>

<h2>8. Who do you actually talk to when something breaks?</h2>
<p>At 9pm on a Saturday, does "support" mean a ticket queue with a 24-hour response time, or can you actually reach someone? For a small operation, this matters more than almost any feature on the list.</p>

<h2>A simple way to decide</h2>
<p>Write down your actual daily operation — how many tables or counters, how orders come in, whether you need GST invoicing, how your kitchen is laid out. Then test two or three systems against that list specifically, not against their marketing pages. If a system offers a genuinely free basic plan, start there — you lose nothing by testing it against your real day before deciding.</p>
HTML;
    }

    private function qrOrderingContent()
    {
        return <<<'HTML'
<p>QR code ordering showed up everywhere during the pandemic as a safety measure. The surprising part is that it didn't go away once that reason disappeared — it's stuck around because it solves problems restaurants actually have, independent of contactless hygiene.</p>

<h2>What QR ordering actually changes</h2>
<p>A customer sits down, scans a code on the table, sees the full menu with photos and descriptions on their own phone, and places the order directly — no flagging down a server to ask what's available, no waiting for someone to take the order during a rush.</p>

<h2>Why restaurants keep using it</h2>

<h3>Fewer order mistakes</h3>
<p>When a customer types their own order instead of saying it out loud to a server who writes it down, there's one less place for mistakes to creep in — wrong spice level, missed "no onion," wrong quantity.</p>

<h3>Staff can focus on service instead of order-taking</h3>
<p>Taking orders is time-consuming, especially during a rush with multiple tables. QR ordering frees up staff to focus on running food, clearing tables, and actually checking in with customers — which tends to matter more for how the visit feels than how fast the order was written down.</p>

<h3>The menu can show more without being overwhelming</h3>
<p>A printed menu has space constraints. A digital menu on someone's phone can show a photo for every dish, mark what's spicy or vegetarian, and surface recommendations — without redesigning a physical menu every time something changes.</p>

<h3>Orders go straight to the kitchen</h3>
<p>Paired with Kitchen Order Ticket (KOT) routing, a QR order can go directly to the right kitchen station without a server walking the ticket over. That's a few minutes saved on every single order, which adds up fast during peak hours.</p>

<h2>Where it doesn't fit as well</h2>
<p>QR ordering is built for table service — it doesn't make much sense for a takeaway-only counter or a food cart where the customer is standing in front of you. It also isn't a good fit for every customer: some people, especially older customers, still prefer a server taking their order, and a good rollout keeps that option available rather than forcing everyone onto a phone.</p>

<h2>How to roll it out without annoying regulars</h2>
<ul>
<li>Keep a server-assisted option available — don't force QR ordering on customers who'd rather not use it.</li>
<li>Put the QR code somewhere obvious on the table, not buried under a napkin holder.</li>
<li>Make sure the digital menu actually matches your physical one — mismatched prices or missing items undermine trust fast.</li>
<li>Test it on a slow night before relying on it during a busy weekend.</li>
</ul>

<p>In Bill&amp;Bite, QR ordering is built in as part of the free basic plan, with orders routed straight into the same dashboard you use for dine-in and takeaway — so it's one system to check, not three.</p>
HTML;
    }

    private function smallStallsContent()
    {
        return <<<'HTML'
<p>When we started building Bill&amp;Bite in 2026, we weren't trying to build "another restaurant POS." We were trying to fix something specific we kept noticing: restaurant software existed, but it wasn't built for most of the food businesses we actually saw around us.</p>

<h2>The gap we kept running into</h2>
<p>Walk down any street with food stalls, carts, or small single-counter shops, and you'll find businesses doing real volume — real customers, real cash flow, real operational headaches. But almost every POS system on the market was priced and designed around restaurants that already had the scale to justify it: multiple tables, bigger teams, higher average order values.</p>
<p>A food cart owner doesn't need multi-outlet inventory forecasting. They need to ring up an order, keep a basic running total, and maybe see which items sell best in a week — without paying a large-restaurant subscription fee to get it.</p>

<h2>Why this group gets left out</h2>
<p>It's not malicious — it's just how software gets built and priced. Companies build for the customers who can pay the most, and smaller operators get treated as an afterthought, if they're considered at all. The result is a real gap: the businesses that could benefit most from even basic digital billing and tracking are the ones least likely to be able to afford it.</p>

<h2>What small stalls and carts actually need</h2>
<ul>
<li><strong>Simple billing</strong> — ring up an order and generate a bill without a complicated setup process.</li>
<li><strong>Basic order tracking</strong> — know what sold, roughly when, without needing a full reporting suite.</li>
<li><strong>Something that works on a phone or a basic tablet</strong> — not a system that assumes you have a full POS terminal setup.</li>
<li><strong>No long contracts or large upfront costs</strong> — the ability to try it without betting money on whether it'll actually help.</li>
</ul>

<h2>What we built because of that</h2>
<p>Bill&amp;Bite currently offers a <strong>free basic subscription</strong> to restaurant owners and food stall operators of every size — not a time-limited trial, a real free tier. The same order management, QR ordering, and reporting tools that a full restaurant uses are available to a single-counter stall, scaled to what they actually need, set up in under 10 minutes.</p>
<p>We're a small team — two founders, two developers, one person doing QA, one doing sales — based in Siliguri. We built this because we kept seeing food businesses get left out of a category of software that should have been helping them from the start.</p>

<h2>If this sounds like your situation</h2>
<p>If you've looked at restaurant software before and decided it wasn't worth it for a business your size, that's probably because it genuinely wasn't built for you. Bill&amp;Bite's free plan exists specifically so you can find out whether digital billing and order tracking actually help your stall or cart, without having to pay to test it.</p>
HTML;
    }
}

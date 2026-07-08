<!--
AD SLOT BACKUP — restore reference only, this file is never included/loaded by the theme.

Ad markup was fully removed from all templates (not just hidden) so no
adsbygoogle/ca-pub trace appears anywhere in the delivered HTML while the
site is under AdSense review. To restore: paste each block back into the
noted location and re-add `$show_ads = true;` at the top of that template.

Common variables used at the top of each template before removal:
  $show_ads = true;
-->

## archive-coloring_topic.php
Location: inside `<div class="wrap" style="padding-top:8px">`, right before `<div class="grid-cards-dense">`.
```php
<?php if ( $show_ads ) : ?>
	<div class="ad-slot" style="height:96px;margin-bottom:28px">AD PLACEHOLDER &middot; 728 &times; 90 BANNER</div>
<?php endif; ?>
```

## front-page.php
Location 1: right after the HERO `</section>`, before the "POPULAR" section.
```php
<?php if ( $show_ads ) : ?>
<div class="wrap" style="margin-top:8px">
	<div class="ad-slot" style="height:100px">AD PLACEHOLDER &middot; 970 &times; 90 LEADERBOARD</div>
</div>
<?php endif; ?>
```
Location 2: right after the "POPULAR" section, before "CATEGORIES".
```php
<?php if ( $show_ads ) : ?>
<div class="wrap" style="margin-top:36px">
	<div class="ad-slot" style="height:100px">AD PLACEHOLDER &middot; 728 &times; 90 BANNER</div>
</div>
<?php endif; ?>
```

## search.php
Location: end of `<main class="wrap" style="padding-top:8px">`, right before `</main>`, after both the results grid and the no-results/popular fallback block.
```php
<?php if ( $show_ads ) : ?>
	<div class="ad-slot" style="height:96px;margin:32px 0 0">AD PLACEHOLDER &middot; 728 &times; 90 BANNER</div>
<?php endif; ?>
```

## single-coloring_topic.php
Location 1 (sidebar, ~300x250/336x280): inside the download info column, after the download buttons.
```php
<?php if ( $show_ads ) : ?>
	<div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--border)">
		<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-xxxxxxxxxxxxxxxx" data-ad-slot="1234567890" data-ad-format="auto" data-full-width-responsive="true"></ins>
	</div>
<?php endif; ?>
```
Location 2 (horizontal, 728x90): after the "All Pages" thumbnail grid, before "How to Use These Coloring Pages".
```php
<?php if ( $show_ads ) : ?>
	<div style="margin:32px 0;text-align:center">
		<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-xxxxxxxxxxxxxxxx" data-ad-slot="0987654321" data-ad-format="horizontal" data-full-width-responsive="true"></ins>
	</div>
<?php endif; ?>
```
Location 3 (horizontal, 728x90): near the bottom, before the "How to Use" / FAQ sections.
```php
<?php if ( $show_ads ) : ?>
	<div style="margin:40px 0;text-align:center">
		<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-xxxxxxxxxxxxxxxx" data-ad-slot="5555555555" data-ad-format="horizontal" data-full-width-responsive="true"></ins>
	</div>
<?php endif; ?>
```

## single-coloring_page.php
Location 1 (sidebar, ~300x250): inside the download info column, after the download buttons (Download PDF/PNG/Print).
```php
<?php if ( $show_ads ) : ?>
	<div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--border)">
		<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-xxxxxxxxxxxxxxxx" data-ad-slot="1234567890" data-ad-format="auto" data-full-width-responsive="true"></ins>
	</div>
<?php endif; ?>
```
Location 2 (horizontal, 728x90): after the vocabulary/fun-fact boxes section, before the "Want the whole set?" CTA block. (Note: page section order was later changed to Nav → H1 → intro text → image/download block → thumbnail grid → vocab/fact boxes — this ad slot sits right after the vocab/fact boxes in that current order.)
```php
<?php if ( $show_ads ) : ?>
	<div style="margin-bottom:32px;text-align:center">
		<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-xxxxxxxxxxxxxxxx" data-ad-slot="0987654321" data-ad-format="horizontal" data-full-width-responsive="true"></ins>
	</div>
<?php endif; ?>
```

## taxonomy-topic_category.php
Location 1 (horizontal, 728x90): top of the main (left) column, before "All {Category} Topics" heading.
```php
<?php if ( $show_ads ) : ?>
	<div class="ad-slot" style="height:96px;margin-bottom:28px">AD PLACEHOLDER &middot; 728 &times; 90 BANNER</div>
<?php endif; ?>
```
Location 2 (sidebar, 300x600): top of the `<aside class="sidebar">`, before the "Most Printed This Week" box.
```php
<?php if ( $show_ads ) : ?>
	<div class="ad-slot" style="height:600px">AD PLACEHOLDER<br>300 &times; 600</div>
<?php endif; ?>
```

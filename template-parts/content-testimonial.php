<?php
/**
 * Template part for displaying full-width testimonials.
 *
 * @package Big_Blue_Box
 */
?>

<?php
$testimonials = [
    [
        'content' => 'With a huge 428 episodes (and counting) of The Doctor Who Big Blue Box Podcast on Apple Podcasts, <em>you certainly won’t be short of Doctor Who chatter to listen to.</em>',
        'author' => 'Laura Wybrow',
        'source' => '<svg xmlns="http://www.w3.org/2000/svg" width="130" height="26" fill="none" viewBox="0 0 130 26">
            <g>
                <path fill="#e2e5e9" d="M112.37 14.4h-2.7c0-1.05-.06-2.63.6-3.3.22-.25.4-.35.81-.35.85 0 1.29.51 1.29 3.64Zm-1.32-7.79c-3.36 0-6.94 1.87-6.94 9.9 0 5.76 2.16 9.43 7.29 9.43 3.86 0 5.65-2.16 5.93-2.63l-2.1-2.85c-.76.45-1.7 1.17-3.11 1.17-1.7 0-2.45-1.1-2.48-3.38h7.85c0-.44.07-.89.07-1.68 0-7.18-2.17-9.96-6.51-9.96Zm-50.84 9.74c0 4.3-.35 4.94-1.42 4.94-.97 0-1.47-1.02-1.47-4.97 0-3.54.28-4.93 1.44-4.93 1.23 0 1.45 1.55 1.45 4.96ZM58.76 6.8c-4.02 0-7.04 2.91-7.04 9.74 0 6.77 2.74 9.33 7.04 9.33 4.06 0 7.04-2.9 7.04-9.74 0-6.77-2.7-9.33-7.04-9.33ZM37.43 20.75c-.13.28-.54.47-.91.47-1.36 0-1.7-1.83-1.7-4.74 0-3.42.4-5.1 1.79-5.1.53 0 .82.39.82.39v8.98ZM43.15.19l-5.72 1.52v5.85c-.16-.19-.79-.89-2.52-.89-2.89 0-5.81 2.76-5.81 9.94 0 6.26 2.33 9.2 5.75 9.2a3.54 3.54 0 0 0 3.14-1.68l.29 1.46h4.87V.19ZM8.93 8.76c0 1.46-.32 2.44-.76 2.88-.53.54-1.22.5-1.79.5h-.6v-6.7h.54c.72 0 1.32 0 1.82.5.35.36.79 1.15.79 2.82m2.8 5.92c.21-.07 2.88-1.27 2.88-6.3C14.61.63 8.36.73 5.56.73H.06v24.86h5.72V16.5H6.8l3.05 9.08h5.9l-4.02-10.91Zm11.12 6.6c-.2.23-.54.48-1.07.48-.54 0-1.26-.22-1.26-2.3 0-2.25.72-2.66 1.6-2.66.32 0 .54.09.73.19v4.3Zm4.93-6.48c0-2.3.1-4.46-1.07-6.04-1.07-1.45-2.7-2.18-4.74-2.18-2.96 0-5.1 1.27-5.4 1.49l1.31 3.5c.25-.09 1.82-.72 3.24-.72 1.66 0 1.73 1.07 1.73 1.96v.82a4.19 4.19 0 0 0-2.01-.4c-.85 0-5.31.15-5.31 6.5 0 4.66 2.5 6.17 4.77 6.17 1.92 0 2.8-1.14 2.99-1.55l.28 1.24h4.21V14.8ZM47.58.07c1.66 0 2.98 1.39 2.98 3.13 0 1.93-1.41 3.1-2.98 3.1a3 3 0 0 1-2.99-3.1A3.06 3.06 0 0 1 47.58.07Zm2.82 7.05v18.47H44.7V7.12h5.71Zm29.8 0v18.47h-5.73V7.12h5.72Zm0-6.3V5.7h-7.27v19.9h-5.78V5.69h-6.94V.82h19.98Zm22.59 12.81V25.6h-5.72V13.85c0-1.74-.16-2.37-.95-2.37-1 0-1 1.01-1 2.53V25.6H89.4V13.85c0-1.77-.19-2.37-.94-2.37-1 0-1 1.01-1 2.53V25.6h-5.73V7.12h5.19l.12 1.61a3.58 3.58 0 0 1 3.4-2.18c2.89 0 3.52 1.9 3.61 2.12.47-1.05 1.6-2.12 3.77-2.12 5.03 0 4.97 4.55 4.97 7.08Zm21.9-7.02c2.83 0 4.8 1.08 5.12 1.36l-1.26 3.51c-.66-.25-2.04-.82-3.05-.82-1.28 0-1.6.7-1.6 1.49 0 2.08 6.03 1.93 6.03 7.3 0 5.1-3.48 6.49-6.44 6.49-2.92 0-5.02-1.14-5.68-1.71l1.7-3.73c.34.15 1.91 1.1 3.32 1.1.98 0 1.73-.31 1.73-1.55 0-2.4-6-1.9-6-7.62 0-3.76 2.73-5.82 6.13-5.82Z"/>
            </g>
        </svg>',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => 'I’ve been listening to TBBBP for years now after being introduced to it by my son when he was at the peak of his Who obsession. His obsession may have waned somewhat, <em>but the podcast remains a highlight of my week.</em>',
        'author' => 'Winston Scott',
        'source' => 'Apple Podcasts Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => '<em>At this point they seem like old friends that share my love of The Doctor.</em> A wonderful listen I have enjoyed now for 8 years and hope to continue for years to come. Every week I am always be able "to do something Doctor Who... Related"',
        'author' => 'Nik English Art',
        'source' => 'Podchaser Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => 'Just a damn good podcast. Great start to the weekend with regular news, merch and reviews. And they also want our opinions too! <em>Well worth your time for all Who fans.</em>',
        'author' => 'Johnnyg',
        'source' => 'Podchaser Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => '<em>The hosts are intelligent and engaging, it’s technically solid and there’s clearly love for the show</em> along with the frustration that comes when it doesn’t deliver - well worth a listen or 12. You make my 1am commute easy so thank you!',
        'author' => 'Simon Lckwood',
        'source' => 'Facebook',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => 'Been listening since late 2018 and I haven’t stopped since! <em>They’re descent, honest, fun and have a great banter between them - really descent human beings</em>... Keep up the awesome work guys!',
        'author' => 'KateLyn_Dalek100',
        'source' => 'Apple Podcasts Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => 'Hey I LOVE Dr Who and I found your podcast a few days ago and <em>I love it it covers a lot keep doing what you do it’s great</em> 😃🪛🪐',
        'author' => 'AmberTB',
        'source' => 'Apple Podcasts Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => 'Started listening during lockdown when going for my daily walks going through the classic stuff first, I now listen every week. Love Adam, Garry and the regular listeners/reviews. <em>Great stuff, keep up the great work!</em>',
        'author' => 'Toprod1',
        'source' => 'Apple Podcasts Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => 'I absolutely love this podcast, one of my favourite things on a Friday is to listen to this. Adam and Garry have great chemistry, and the content covers all areas from Merch, news, reviews of episodes... <em>I cannot recommend it enough if your a Dr Who fan it’s perfect , brilliant and will always keep you entertained.</em>',
        'author' => 'JoeTurner',
        'source' => 'Podchaser Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => 'Love this podcast. <em>Garry and Adam are very passionate Whovians and it shows in their discussions.</em> Thank You gentlemen from the other side of the pond. Cheers and Beers!!',
        'author' => 'TimeLord0902',
        'source' => 'Apple Podcasts Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => '<em>I&apos;ve checked out quite a few Who podcasts and this one is by far the best!!</em> Garry and Adam are informative and really care about the Whoniverse. Love you guys!!',
        'author' => 'Dolfan23905',
        'source' => 'Apple Podcasts Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => 'One of my favorite things to do on a Friday afternoon is <em>mow the grass and listen to this podcast!!</em> Love y&apos;all',
        'author' => 'Stephanie Freiny',
        'source' => 'Apple Podcasts Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => 'This is a fantastic podcast I look forward to it every week. Garry and Adam are very knowledgable and entertaining. <em>A must for Doctor Who fans.</em>',
        'author' => 'BoyWondamus',
        'source' => 'Apple Podcasts Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => 'I don&apos;t have the time to scour the internet for all thing Doctor Who. Fortunately, with this podcast, I don&apos;t have to. <em>Also, Garry and Adam have a great rapport. Well worth your time to try this.</em>',
        'author' => 'Soi15',
        'source' => 'Apple Podcasts Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => 'A great show very informative in depth reviews and <em>even if they don&apos;t like the episode they always find positives,</em> also a great community on the discord.',
        'author' => 'sean mundy',
        'source' => 'Apple Podcasts Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
    [
        'content' => 'I totally recommend this podcast, it&apos;s an absolute treat to receive this in my inbox each week. Packed full of Who news, merch and weekly modern/classic episode reviews (plus SJA and Torchwood), <em>this is a Whovian&apos;s no1 podcast and one you really should be lsitening to!</em>',
        'author' => 'Lewis Blackmore',
        'source' => 'Apple Podcasts Review',
        'rating_img' => get_bloginfo('template_url') . '/images/icons/icon-five-stars.svg',
    ],
];

$random_testimonial = $testimonials[array_rand($testimonials)];
?>

<div class="testimonial">
    <blockquote class="testimonial__content">
        <h4 class="no-heading"><?php echo $random_testimonial['content']; ?></h4>
        <div class="testimonial__source">
            <h6><?php echo $random_testimonial['author']; ?></h6>
            <cite>
                <img src="<?php echo $random_testimonial['rating_img']; ?>" width="92" height="16" alt=""><?php echo $random_testimonial['source']; ?>
            </cite>
        </div>
        <img class="testimonial__bg-image" src="<?php echo get_bloginfo('template_url'); ?>/images/tardis-blockquote.svg" alt="">
    </blockquote>
</div>
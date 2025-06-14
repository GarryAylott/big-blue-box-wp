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
];

$random_testimonial = $testimonials[array_rand($testimonials)];
?>

<div class="testimonial">
    <blockquote class="testimonial__content">
        <h4 class="no-heading"><?php echo $random_testimonial['content']; ?></h4>
        <div class="testimonial__source">
            <h5><?php echo $random_testimonial['author']; ?></h5>
            <cite>
                <img src="<?php echo $random_testimonial['rating_img']; ?>" width="92" height="16" alt=""><?php echo $random_testimonial['source']; ?>
            </cite>
        </div>
        <img class="testimonial__bg-image" src="<?php echo get_bloginfo('template_url'); ?>/images/tardis-blockquote.svg" alt="">
    </blockquote>
</div>
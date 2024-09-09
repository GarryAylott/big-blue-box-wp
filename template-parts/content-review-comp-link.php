<?php
/**
 * Template part for displaying the full-width review compendium seciton.
 *
 * @package Big_Blue_Box
 */
?>

<section class="review-compendium-link">
    <div class="wrapper">
        <div class="review-compendium-link__content">
            <div class="review-compendium-text flow-small">
                <h4 class="balance">Find Our Reviews for Every Doctor Who Story</h4>
                <p>
                    We’ve reviewed A LOT of Doctor Who, almost all of it in fact (including the spin-offs), so it can be difficult, especially in some of the podcast apps, to find what podcast episode we reviewed a particular story.
                </p>
                <p>
                    So, here’s a handy reference so you can do just that! Happy listening.
                </p>
                <a class="button" href="#">
                    View Reviews Compendium
                </a>
            </div>

            <?php $images = array(
                'doctor-transparent-capaldi.webp',
                'doctor-transparent-cbaker.webp',
                'doctor-transparent-davison.webp',
                'doctor-transparent-eccleston.webp',
                'doctor-transparent-gatwa.webp',
                'doctor-transparent-hartnell.webp',
                'doctor-transparent-hurt.webp',
                'doctor-transparent-mccoy.webp',
                'doctor-transparent-mcgann.webp',
                'doctor-transparent-pertwee.webp',
                'doctor-transparent-smith.webp',
                'doctor-transparent-tbaker.webp',
                'doctor-transparent-tennant10.webp',
                'doctor-transparent-tennant14.webp',
                'doctor-transparent-troughton.webp',
                'doctor-transparent-whitakker.webp'
            );

            $random_image = $images[array_rand($images)]; ?>

            <img src="<?php echo get_template_directory_uri(); ?>/images/doctor-images/<?php echo $random_image; ?>" width="1000" height="1100" alt="The Doctor">
        </div>
    </div>
</section>
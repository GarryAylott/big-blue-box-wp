<?php
/**
 * Template part for displaying the Tardis page progress icon.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Big_Blue_Box
 */
?>

<!-- TARDIS Scroll Progress -->
<div class="tardis-progress-container" aria-hidden="true">
  <svg 
    id="tardisProgress" 
    width="70"
    height="96" 
    viewBox="-25 0 549 864"
    preserveAspectRatio="xMidYMid meet"
    xmlns="http://www.w3.org/2000/svg" 
    role="img" 
    aria-labelledby="tardisDesc"
  >
    <desc id="tardisDesc"><?php esc_html_e( 'Reading progress represented by a filling TARDIS icon', 'bigbluebox' ); ?></desc>
    
    <defs>
      <!-- Define the TARDIS shape as a mask -->
      <mask id="tardisMask">
        <!-- White shape defines visible area -->
        <g transform="rotate(0, 249.5, 432)" fill="white">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M479.777 208.849L480.116 743.963H499V774.482L498.621 775.06L258.363 864L258.203 88.4734L496.085 172.643H498.002V208.57H496.105L479.777 208.849ZM368.91 205.436L285.89 185.078L285.93 295.613L368.91 308.906V205.436ZM377.494 207.812L451.072 227.372V322.998L377.713 310.882L377.494 207.812Z"/>
          <path fill-rule="evenodd" clip-rule="evenodd" d="M1.55694 172.323L239.759 88.3538L239.918 863.82L0.0598238 774.961L0.119709 774.302V743.504H18.5642L18.4644 208.949L1.57691 209.229H0.0199006L-6.10352e-05 172.343L1.55694 172.323ZM109.729 342.279L55.8325 350.123L55.8524 435.569L109.749 432.316L109.729 342.279ZM212.132 295.613L129.152 308.966L129.132 205.496L212.112 185.098L212.132 295.613ZM46.1512 323.078L119.909 310.922L119.889 207.851L46.1313 227.472L46.1512 323.078Z"/>
          <path d="M249.041 76.5599L305.192 96.5876V21.7452L249.041 0"/>
          <path d="M249.041 76.5599L194.365 95.7526V20.7375L249.041 0"/>
        </g>
      </mask>
    </defs>

    <!-- Blue fill that animates up -->
    <rect 
      id="tardis-fill" 
      x="-100" 
      y="864" 
      width="700"
      height="864" 
      fill="#4C83F6" 
      mask="url(#tardisMask)"
    />

    <!-- White outline TARDIS -->
    <g transform="rotate(0, 249.5, 432)" stroke="#6d99f8" stroke-width="12" fill="none">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M479.777 208.849L480.116 743.963H499V774.482L498.621 775.06L258.363 864L258.203 88.4734L496.085 172.643H498.002V208.57H496.105L479.777 208.849ZM368.91 205.436L285.89 185.078L285.93 295.613L368.91 308.906V205.436ZM377.494 207.812L451.072 227.372V322.998L377.713 310.882L377.494 207.812Z"/>
      <path fill-rule="evenodd" clip-rule="evenodd" d="M1.55694 172.323L239.759 88.3538L239.918 863.82L0.0598238 774.961L0.119709 774.302V743.504H18.5642L18.4644 208.949L1.57691 209.229H0.0199006L-6.10352e-05 172.343L1.55694 172.323ZM109.729 342.279L55.8325 350.123L55.8524 435.569L109.749 432.316L109.729 342.279ZM212.132 295.613L129.152 308.966L129.132 205.496L212.112 185.098L212.132 295.613ZM46.1512 323.078L119.909 310.922L119.889 207.851L46.1313 227.472L46.1512 323.078Z"/>
      <path d="M249.041 76.5599L305.192 96.5876V21.7452L249.041 0"/>
      <path d="M249.041 76.5599L194.365 95.7526V20.7375L249.041 0"/>
    </g>
  </svg>

  <div class="sr-only" role="status" aria-live="polite" id="tardisProgressStatus">
    <?php esc_html_e( 'Reading progress: 0%', 'bigbluebox' ); ?>
  </div>
</div>

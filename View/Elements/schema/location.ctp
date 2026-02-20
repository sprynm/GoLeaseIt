<?php // location.ctp ?>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "<?php echo $siteContact['name']; ?>",
  "location": {
    "@type": "Place",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "<?php echo $siteContact['city']; ?>",
      "addressRegion": "<?php echo $siteContact['province_state']; ?>"
    },
    "url": "<?php echo Router::url(null, true); ?>"
  }
}
</script>

<div class="feature-boxes">
  <?php
    $makeSlug = function ($value) {
      $value = strtolower(trim((string)$value));
      $value = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
      $value = preg_replace('/[^a-z0-9]+/', '-', (string)$value);
      $value = trim((string)$value, '-');
      return $value;
    };

    $featureBoxes = $this->Prototype->instanceItems(
      $featureInstanceId,
      array('limit' => $numberOfFeaturesLimit, 'order' => 'PrototypeItem.rank ASC')
    );

    if (!is_array($featureBoxes)) {
      $featureBoxes = array();
    }

    foreach ($featureBoxes as $featureBox) {
      $item = isset($featureBox['PrototypeItem']) ? $featureBox['PrototypeItem'] : array();
      $heading = isset($item['heading']) && $item['heading'] !== '' ? $item['heading'] : (isset($item['name']) ? $item['name'] : '');
      $title = isset($item['title']) ? $item['title'] : '';
      $subheading = isset($item['subheading']) ? $item['subheading'] : '';
      $content = isset($item['text']) ? $item['text'] : (isset($item['content']) ? $item['content'] : '');
      $ctaText = isset($item['cta_link_text']) ? $item['cta_link_text'] : (isset($item['cta_text']) ? $item['cta_text'] : '');
      if ($ctaText === '' && isset($item['data[PrototypeItem][cta_link_text]'])) {
        $ctaText = $item['data[PrototypeItem][cta_link_text]'];
      }
      $ctaLink = isset($item['cta_link']) ? $item['cta_link'] : '';
      $targetText = $heading !== '' ? $heading : $title;
      $targetSlug = $makeSlug($targetText);
      $targetHash = $targetSlug !== '' ? 'service-' . $targetSlug : '';

      if ($ctaLink !== '' && $targetHash !== '' && strpos($ctaLink, '#') === false) {
        $normalized = rtrim(strtolower(trim($ctaLink)), '/');
        if (
          $normalized === '/services' ||
          $normalized === 'services' ||
          preg_match('#/services$#', $normalized)
        ) {
          $ctaLink = '/services#' . $targetHash;
        }
      }

      if ($ctaLink === '') {
        if ($targetHash !== '') {
          $ctaLink = '/services#' . $targetHash;
        }
      }
      $image = (!empty($featureBox['Image']) && !empty($featureBox['Image'][0])) ? $featureBox['Image'][0] : null;
      $imageAlt = $image && !empty($image['alternative']) ? $image['alternative'] : $heading;
      $tileImageLarge = $image ? $this->Media->getImage($image, array('version' => 'large')) : '';
      $tileImageSquare = $image ? $this->Media->getImage($image, array('version' => 'tile')) : '';
      ?>
      <?php if ($ctaLink !== ''): ?>
        <a class="tile tile--linked" href="<?php echo h($ctaLink); ?>">
          <?php if ($image): ?>
            <span class="tile__media">
              <picture>
                <source srcset="<?php echo $tileImageSquare; ?>" media="(min-width: 1024px)">
                <source srcset="<?php echo $tileImageLarge; ?>">
                <img src="<?php echo $tileImageLarge; ?>" alt="<?php echo h($imageAlt); ?>" loading="lazy" decoding="async">
              </picture>
            </span>
          <?php endif; ?>
          <span class="tile__overlay"></span>
          <span class="tile__content">
            <?php if ($heading !== ''): ?>
              <span class="tile__heading"><?php echo h($heading); ?></span>
            <?php endif; ?>
            <?php if ($title !== ''): ?>
              <span class="tile__title"><?php echo h($title); ?></span>
            <?php endif; ?>
            <?php if ($subheading !== ''): ?>
              <span class="tile__subheading"><?php echo h($subheading); ?></span>
            <?php endif; ?>
            <?php if ($content !== ''): ?>
              <span class="tile__text"><?php echo nl2br(h($content)); ?></span>
            <?php endif; ?>
          </span>
        </a>
      <?php else: ?>
        <article class="tile">
          <?php if ($image): ?>
            <span class="tile__media">
              <picture>
                <source srcset="<?php echo $tileImageSquare; ?>" media="(min-width: 1024px)">
                <source srcset="<?php echo $tileImageLarge; ?>">
                <img src="<?php echo $tileImageLarge; ?>" alt="<?php echo h($imageAlt); ?>" loading="lazy" decoding="async">
              </picture>
            </span>
          <?php endif; ?>
          <span class="tile__overlay"></span>
          <div class="tile__content">
            <?php if ($heading !== ''): ?>
              <h2 class="tile__heading"><?php echo h($heading); ?></h2>
            <?php endif; ?>
            <?php if ($title !== ''): ?>
              <p class="tile__title"><?php echo h($title); ?></p>
            <?php endif; ?>
            <?php if ($subheading !== ''): ?>
              <p class="tile__subheading"><?php echo h($subheading); ?></p>
            <?php endif; ?>
            <?php if ($content !== ''): ?>
              <div class="tile__text"><?php echo nl2br(h($content)); ?></div>
            <?php endif; ?>
            <?php if ($ctaLink !== '' && $ctaText !== ''): ?>
              <?php echo $this->Html->link(h($ctaText), $ctaLink, array('class' => 'tile__cta btn')); ?>
            <?php endif; ?>
          </div>
        </article>
      <?php endif; ?>
      <?php
    }
  ?>
</div>

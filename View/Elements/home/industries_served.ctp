<?php
$instanceId = isset($instanceId) ? (int) $instanceId : 0;
$introHeading = isset($introHeading) ? trim((string) $introHeading) : '';
$introBody = isset($introBody) ? trim((string) $introBody) : '';
$introCtaText = isset($introCtaText) ? trim((string) $introCtaText) : '';
$introCtaLink = isset($introCtaLink) ? trim((string) $introCtaLink) : '';
$sectionClasses = isset($sectionClasses) ? trim((string) $sectionClasses) : 'u-surface-base section-industries';
$limit = isset($limit) ? (int) $limit : 6;

$items = array();
if ($instanceId > 0) {
	$options = array(
		'order' => 'PrototypeItem.rank ASC'
	);
	if ($limit > 0) {
		$options['limit'] = $limit;
	}
	$items = $this->Prototype->instanceItems($instanceId, $options);
}

if (!is_array($items)) {
	$items = array();
}

if (empty($items)) {
	return;
}

$introBackgroundImage = null;
$instanceRecord = array();
$prototypeValueMap = array();
$prototypeDefaultMap = array();
if ($instanceId > 0) {
	$PrototypeInstance = ClassRegistry::init('Prototype.PrototypeInstance');
	$instanceRecord = $PrototypeInstance->find('first', array(
		'conditions' => array(
			'PrototypeInstance.id' => $instanceId,
			'PrototypeInstance.deleted' => 0,
		),
		'fields' => array('PrototypeInstance.name', 'PrototypeInstance.description'),
		'recursive' => -1,
		'cache' => true,
	));

	$CustomFieldValue = ClassRegistry::init('CustomFields.CustomFieldValue');
	$prototypeValues = $CustomFieldValue->find('all', array(
		'conditions' => array(
			'CustomFieldValue.model' => 'PrototypeInstance',
			'CustomFieldValue.foreign_key' => $instanceId,
		),
		'fields' => array('CustomFieldValue.key', 'CustomFieldValue.val'),
		'recursive' => -1,
		'cache' => true,
	));

	if (is_array($prototypeValues)) {
		foreach ($prototypeValues as $valueRow) {
			if (empty($valueRow['CustomFieldValue']['key'])) {
				continue;
			}
			$key = trim((string) $valueRow['CustomFieldValue']['key']);
			$val = isset($valueRow['CustomFieldValue']['val']) ? trim((string) $valueRow['CustomFieldValue']['val']) : '';
			if ($key !== '') {
				$prototypeValueMap[$key] = $val;
			}
		}
	}

	$CustomField = ClassRegistry::init('CustomFields.CustomField');
	$prototypeFields = $CustomField->find('all', array(
		'conditions' => array(
			'CustomField.model' => 'PrototypeInstance',
			'CustomField.foreign_key' => $instanceId,
		),
		'fields' => array('CustomField.name', 'CustomField.default'),
		'recursive' => -1,
		'cache' => true,
	));

	if (is_array($prototypeFields)) {
		foreach ($prototypeFields as $fieldRow) {
			if (empty($fieldRow['CustomField']['name'])) {
				continue;
			}
			$key = trim((string) $fieldRow['CustomField']['name']);
			$val = isset($fieldRow['CustomField']['default']) ? trim((string) $fieldRow['CustomField']['default']) : '';
			if ($key !== '' && $val !== '') {
				$prototypeDefaultMap[$key] = $val;
			}
		}
	}

	$Attachment = ClassRegistry::init('Media.Attachment');
	$introBackgroundImage = $Attachment->find('first', array(
		'conditions' => array(
			'Attachment.model' => 'PrototypeInstance',
			'Attachment.foreign_key' => $instanceId,
			'Attachment.group' => 'Instance Banner Image',
			'Attachment.deleted' => 0,
		),
		'fields' => array('Attachment.*'),
		'order' => 'Attachment.rank ASC, Attachment.id ASC',
		'recursive' => -1,
		'cache' => true,
	));
	if (empty($introBackgroundImage['Attachment'])) {
		$introBackgroundImage = null;
	} else {
		$introBackgroundImage = $introBackgroundImage['Attachment'];
	}
}

if ($introHeading === '') {
	foreach (array('home_industries_heading', 'intro_heading') as $key) {
		if (!empty($prototypeValueMap[$key])) {
			$introHeading = $prototypeValueMap[$key];
			break;
		}
		if (!empty($prototypeDefaultMap[$key])) {
			$introHeading = $prototypeDefaultMap[$key];
			break;
		}
	}
}

if ($introBody === '') {
	foreach (array('home_industries_body', 'intro_body') as $key) {
		if (!empty($prototypeValueMap[$key])) {
			$introBody = $prototypeValueMap[$key];
			break;
		}
		if (!empty($prototypeDefaultMap[$key])) {
			$introBody = $prototypeDefaultMap[$key];
			break;
		}
	}
}

if ($introCtaText === '') {
	foreach (array('home_industries_cta_text', 'intro_cta_text') as $key) {
		if (!empty($prototypeValueMap[$key])) {
			$introCtaText = $prototypeValueMap[$key];
			break;
		}
		if (!empty($prototypeDefaultMap[$key])) {
			$introCtaText = $prototypeDefaultMap[$key];
			break;
		}
	}
}

if ($introCtaLink === '') {
	foreach (array('home_industries_cta_link', 'intro_cta_link') as $key) {
		if (!empty($prototypeValueMap[$key])) {
			$introCtaLink = $prototypeValueMap[$key];
			break;
		}
		if (!empty($prototypeDefaultMap[$key])) {
			$introCtaLink = $prototypeDefaultMap[$key];
			break;
		}
	}
}

$firstItem = !empty($items[0]['PrototypeItem']) ? $items[0]['PrototypeItem'] : array();
if (!is_array($firstItem)) {
	$firstItem = array();
}

if ($introHeading === '') {
	foreach (array('home_industries_heading', 'intro_heading') as $key) {
		if (!empty($firstItem[$key])) {
			$introHeading = trim((string) $firstItem[$key]);
			break;
		}
	}
}

if ($introBody === '') {
	foreach (array('home_industries_body', 'intro_body') as $key) {
		if (!empty($firstItem[$key])) {
			$introBody = trim((string) $firstItem[$key]);
			break;
		}
	}
}

if ($introCtaText === '') {
	foreach (array('home_industries_cta_text', 'intro_cta_text') as $key) {
		if (!empty($firstItem[$key])) {
			$introCtaText = trim((string) $firstItem[$key]);
			break;
		}
	}
}

if ($introCtaLink === '') {
	foreach (array('home_industries_cta_link', 'intro_cta_link') as $key) {
		if (!empty($firstItem[$key])) {
			$introCtaLink = trim((string) $firstItem[$key]);
			break;
		}
	}
}

if ($introHeading === '' && !empty($instanceRecord['PrototypeInstance']['name'])) {
	$introHeading = trim((string) $instanceRecord['PrototypeInstance']['name']);
}

if ($introBody === '' && !empty($instanceRecord['PrototypeInstance']['description'])) {
	$introBody = trim((string) $instanceRecord['PrototypeInstance']['description']);
}

$hasIntroContent = ($introHeading !== '' || $introBody !== '' || ($introCtaText !== '' && $introCtaLink !== ''));
?>
<section class="<?php echo h($sectionClasses); ?>">
	<div class="c-container c-container--full">
		<div class="industry-grid">
			<div class="industry-grid__intro<?php echo $introBackgroundImage ? ' industry-grid__intro--with-bg' : ''; ?>">
				<?php if ($introBackgroundImage): ?>
					<span class="industry-grid__intro-media" aria-hidden="true">
						<picture>
							<source srcset="<?php echo $this->Media->getImage($introBackgroundImage, array('version' => 'banner-lrg')); ?>" media="(min-width: 1441px)">
							<source srcset="<?php echo $this->Media->getImage($introBackgroundImage, array('version' => 'banner-med')); ?>" media="(min-width: 801px)">
							<source srcset="<?php echo $this->Media->getImage($introBackgroundImage, array('version' => 'banner-sm')); ?>" media="(min-width: 641px)">
							<source srcset="<?php echo $this->Media->getImage($introBackgroundImage, array('version' => 'banner-xsm')); ?>">
							<img src="<?php echo $this->Media->getImage($introBackgroundImage, array('version' => 'banner-lrg')); ?>" alt="" loading="lazy" decoding="async">
						</picture>
					</span>
					<span class="industry-grid__intro-overlay" aria-hidden="true"></span>
				<?php endif; ?>

				<?php if ($hasIntroContent): ?>
					<div class="industry-grid__intro-content">
						<?php if ($introHeading !== ''): ?>
							<h2><?php echo h($introHeading); ?></h2>
						<?php endif; ?>
						<?php if ($introBody !== ''): ?>
							<p><?php echo h($introBody); ?></p>
						<?php endif; ?>
						<?php if ($introCtaText !== '' && $introCtaLink !== ''): ?>
							<?php echo $this->Html->link($introCtaText, $introCtaLink, array('class' => 'btn btn--primary')); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php foreach ($items as $entry): ?>
				<?php
				$item = isset($entry['PrototypeItem']) ? $entry['PrototypeItem'] : array();
				$heading = '';
				if (!empty($item['heading'])) {
					$heading = $item['heading'];
				} elseif (!empty($item['name'])) {
					$heading = $item['name'];
				} elseif (!empty($item['title'])) {
					$heading = $item['title'];
				}

				$itemLink = '';
				if (!empty($item['cta_link'])) {
					$itemLink = trim((string) $item['cta_link']);
				}
				if ($itemLink === '' && !empty($item['url'])) {
					$itemLink = trim((string) $item['url']);
				}

				$image = (!empty($entry['Image']) && !empty($entry['Image'][0])) ? $entry['Image'][0] : null;
				$imageAlt = ($image && !empty($image['alternative'])) ? $image['alternative'] : $heading;
				$tileClass = $itemLink !== '' ? 'tile tile--linked tile--industry' : 'tile tile--industry';
				?>
				<?php if ($itemLink !== ''): ?>
					<a class="<?php echo $tileClass; ?>" href="<?php echo h($itemLink); ?>">
						<?php if ($image): ?>
							<span class="tile__media">
								<picture>
									<source srcset="<?php echo $this->Media->getImage($image, array('version' => 'large')); ?>" media="(min-width: 1241px)">
									<source srcset="<?php echo $this->Media->getImage($image, array('version' => 'medium')); ?>" media="(min-width: 481px)">
									<img src="<?php echo $this->Media->getImage($image, array('version' => 'thumb')); ?>" alt="<?php echo h($imageAlt); ?>" loading="lazy" decoding="async">
								</picture>
							</span>
						<?php endif; ?>
						<span class="tile__overlay"></span>
						<span class="tile__content">
							<?php if ($heading !== ''): ?>
								<span class="tile__heading"><?php echo h($heading); ?></span>
							<?php endif; ?>
						</span>
					</a>
				<?php else: ?>
					<article class="<?php echo $tileClass; ?>">
						<?php if ($image): ?>
							<span class="tile__media">
								<picture>
									<source srcset="<?php echo $this->Media->getImage($image, array('version' => 'large')); ?>" media="(min-width: 1241px)">
									<source srcset="<?php echo $this->Media->getImage($image, array('version' => 'medium')); ?>" media="(min-width: 481px)">
									<img src="<?php echo $this->Media->getImage($image, array('version' => 'thumb')); ?>" alt="<?php echo h($imageAlt); ?>" loading="lazy" decoding="async">
								</picture>
							</span>
						<?php endif; ?>
						<span class="tile__overlay"></span>
						<div class="tile__content">
							<?php if ($heading !== ''): ?>
								<h3 class="tile__heading"><?php echo h($heading); ?></h3>
							<?php endif; ?>
						</div>
					</article>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>



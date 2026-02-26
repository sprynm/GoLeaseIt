<?php
$instanceId = isset($instanceId) ? (int) $instanceId : 0;
$introHeading = isset($introHeading) ? trim((string) $introHeading) : '';
$introBody = isset($introBody) ? trim((string) $introBody) : '';
$introCtaText = isset($introCtaText) ? trim((string) $introCtaText) : '';
$introCtaLink = isset($introCtaLink) ? trim((string) $introCtaLink) : '';

if ($instanceId <= 0) {
	return;
}

$items = $this->Prototype->instanceItems($instanceId, array(
	'order' => 'PrototypeItem.rank ASC',
));

if (!is_array($items) || empty($items)) {
	return;
}

$resolveIconPath = function ($value) {
	$raw = trim((string) $value);
	if ($raw === '') {
		return '';
	}

	$candidateWebPaths = array();

	// Accept full URLs and normalize to path.
	if (preg_match('#^https?://#i', $raw)) {
		$parsedPath = parse_url($raw, PHP_URL_PATH);
		if (is_string($parsedPath) && $parsedPath !== '') {
			$raw = $parsedPath;
		}
	}

	if (strpos($raw, '/img/') === 0) {
		$candidateWebPaths[] = $raw;
	}

	$file = basename(str_replace('\\', '/', $raw));
	if ($file !== '' && preg_match('/^[a-z0-9._-]+$/i', $file)) {
		$fileCandidates = array($file);

		// Guard against malformed values like "npeople.svg" from escaped newline artifacts.
		if (strlen($file) > 1 && $file[0] === 'n') {
			$fileCandidates[] = substr($file, 1);
		}

		foreach ($fileCandidates as $candidateFile) {
			$candidateWebPaths[] = '/img/home-process-icons/' . $candidateFile;
			$candidateWebPaths[] = '/img/icons/' . $candidateFile;
		}
	}

	foreach ($candidateWebPaths as $candidate) {
		$fullPath = rtrim((string) WWW_ROOT, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $candidate), DIRECTORY_SEPARATOR);
		if (is_file($fullPath)) {
			return $candidate;
		}
	}

	// Linux case-sensitive fallback: match by lowercase filename.
	$fileLower = strtolower($file);
	if ($fileLower !== '') {
		$folders = array(
			rtrim((string) WWW_ROOT, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'home-process-icons',
			rtrim((string) WWW_ROOT, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'icons',
		);
		foreach ($folders as $folder) {
			if (!is_dir($folder)) {
				continue;
			}
			$matches = glob($folder . DIRECTORY_SEPARATOR . '*');
			if (!is_array($matches)) {
				continue;
			}
			foreach ($matches as $path) {
				if (is_file($path) && strtolower(basename($path)) === $fileLower) {
					$folderName = basename(dirname($path));
					return '/img/' . $folderName . '/' . basename($path);
				}
			}
		}
	}

	return '';
};
?>
<section class="u-surface-base">
	<div class="c-container c-container--full">
		<div class="home-region">
			<div class="process-intro">
				<div class="process-intro__heading">
					<?php if ($introHeading !== ''): ?>
						<h2><?php echo h($introHeading); ?></h2>
					<?php endif; ?>
					<?php if ($introCtaText !== '' && $introCtaLink !== ''): ?>
						<?php echo $this->Html->link($introCtaText, $introCtaLink, array('class' => 'btn btn--primary', 'escape' => false)); ?>
					<?php endif; ?>
				</div>
				<?php if ($introBody !== ''): ?>
					<div class="process-intro__body rte"><?php echo $introBody; ?></div>
				<?php endif; ?>
			</div>

			<ol class="process-steps">
				<?php foreach ($items as $index => $entry): ?>
					<?php
					$item = isset($entry['PrototypeItem']) ? $entry['PrototypeItem'] : array();
					$stepTitle = '';
					if (!empty($item['title'])) {
						$stepTitle = trim((string) $item['title']);
					} elseif (!empty($item['name'])) {
						$stepTitle = trim((string) $item['name']);
					}

					$stepDescription = '';
					if (!empty($item['description'])) {
						$stepDescription = trim((string) $item['description']);
					} elseif (!empty($item['text'])) {
						$stepDescription = trim((string) $item['text']);
					} elseif (!empty($item['summary'])) {
						$stepDescription = trim((string) $item['summary']);
					}

					$image = (!empty($entry['Image']) && !empty($entry['Image'][0])) ? $entry['Image'][0] : null;
					$imageAlt = ($image && !empty($image['alternative'])) ? trim((string) $image['alternative']) : $stepTitle;
					$iconPath = $resolveIconPath(!empty($item['icon_file']) ? $item['icon_file'] : '');
					$stepNumber = (int) $index + 1;
					?>
					<li class="process-step">
						<?php if ($iconPath !== ''): ?>
							<span class="process-step__icon" aria-hidden="true">
								<img src="<?php echo h($iconPath); ?>" alt="<?php echo h($stepTitle); ?>" loading="lazy" decoding="async">
							</span>
						<?php elseif ($image): ?>
							<span class="process-step__icon" aria-hidden="true">
								<img src="<?php echo $this->Media->getImage($image, array('version' => 'thumb')); ?>" alt="<?php echo h($imageAlt); ?>" loading="lazy" decoding="async">
							</span>
						<?php endif; ?>
						<?php if ($stepTitle !== ''): ?>
							<p class="process-step__title">
								<span class="process-step__num"><?php echo h($stepNumber); ?>.</span>
								<span class="process-step__label"><?php echo h($stepTitle); ?></span>
							</p>
						<?php endif; ?>
						<?php if ($stepDescription !== ''): ?>
							<p class="process-step__desc"><?php echo h($stepDescription); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>
</section>



(() => {
	document.documentElement.classList.add("js-observers");

	const observedElements = Array.from(document.querySelectorAll(".observe"));
	if (!observedElements.length) return;

	const supportsObserver = "IntersectionObserver" in window;
	if (!supportsObserver) {
		observedElements.forEach((element) => element.classList.add("visible"));
		return;
	}

	const defaultConfig = {
		once: true,
		threshold: 0.1,
		rootMargin: "0px 0px -8% 0px",
		root: null,
	};

	const observerPool = new Map();

	const parseBoolean = (value, fallback) => {
		if (value === undefined || value === null || value === "") return fallback;
		if (value === "true" || value === "1") return true;
		if (value === "false" || value === "0") return false;
		return fallback;
	};

	const parseThreshold = (value, fallback) => {
		if (!value) return fallback;
		if (value.includes(",")) {
			const values = value
				.split(",")
				.map((part) => Number.parseFloat(part.trim()))
				.filter((part) => !Number.isNaN(part) && part >= 0 && part <= 1);
			return values.length ? values : fallback;
		}
		const parsed = Number.parseFloat(value);
		return Number.isNaN(parsed) ? fallback : parsed;
	};

	const resolveRoot = (selector) => {
		if (!selector) return null;
		try {
			return document.querySelector(selector);
		} catch (_error) {
			return null;
		}
	};

	const parseShorthandConfig = (value) => {
		if (!value) return {};

		const config = {};
		const tokens = value
			.split(/[;,]/)
			.map((part) => part.trim())
			.filter(Boolean);

		tokens.forEach((token) => {
			const [rawKey, ...rest] = token.split("=");
			const key = rawKey ? rawKey.trim() : "";
			const rawValue = rest.join("=").trim();

			if (!key) return;
			if (key === "once") {
				config.once = parseBoolean(rawValue, defaultConfig.once);
				return;
			}
			if (key === "threshold") {
				config.threshold = parseThreshold(rawValue, defaultConfig.threshold);
				return;
			}
			if (key === "margin") {
				config.rootMargin = rawValue || defaultConfig.rootMargin;
				return;
			}
			if (key === "root") {
				config.root = resolveRoot(rawValue);
			}
		});

		return config;
	};

	const getConfig = (element) => {
		const shorthand = parseShorthandConfig(element.dataset.observer);

		const once = parseBoolean(
			element.dataset.observerOnce,
			shorthand.once !== undefined ? shorthand.once : defaultConfig.once
		);
		const threshold = parseThreshold(
			element.dataset.observerThreshold,
			shorthand.threshold !== undefined ? shorthand.threshold : defaultConfig.threshold
		);
		const rootMargin =
			element.dataset.observerMargin ||
			(shorthand.rootMargin !== undefined ? shorthand.rootMargin : defaultConfig.rootMargin);
		const root = resolveRoot(element.dataset.observerRoot) || shorthand.root || defaultConfig.root;

		return { once, threshold, rootMargin, root };
	};

	const makeKey = (config) => {
		const thresholdKey = Array.isArray(config.threshold)
			? config.threshold.join(",")
			: String(config.threshold);
		const rootKey = config.root ? `root:${config.root.id || config.root.className || "custom"}` : "root:null";
		return `${rootKey}|margin:${config.rootMargin}|threshold:${thresholdKey}|once:${config.once}`;
	};

	const getObserver = (config) => {
		const key = makeKey(config);
		if (observerPool.has(key)) return observerPool.get(key);

		const observer = new IntersectionObserver(
			(entries, instance) => {
				entries.forEach((entry) => {
					const target = entry.target;
					const isVisible = entry.isIntersecting;

					target.classList.toggle("visible", isVisible);

					if (isVisible && target.dataset.observerOnceResolved === "true") {
						instance.unobserve(target);
					}
				});
			},
			{
				root: config.root,
				rootMargin: config.rootMargin,
				threshold: config.threshold,
			}
		);

		observerPool.set(key, observer);
		return observer;
	};

	observedElements.forEach((element) => {
		const config = getConfig(element);
		element.dataset.observerOnceResolved = config.once ? "true" : "false";
		const observer = getObserver(config);
		observer.observe(element);
	});
})();

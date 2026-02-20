import fs from "fs/promises";
import path from "path";
import puppeteer from "puppeteer";

const DEFAULT_BASELINE = path.resolve("docs", "visual-regression", "baseline");
const DEFAULT_CANDIDATE = path.resolve("docs", "visual-regression", "current");
const DEFAULT_DIFF = path.resolve("docs", "visual-regression", "diff");
const DEFAULT_THRESHOLD = 0;

function parseArgs(argv) {
  const args = {};
  for (const arg of argv) {
    if (!arg.startsWith("--")) {
      continue;
    }
    const [key, ...rest] = arg.slice(2).split("=");
    args[key] = rest.join("=") || "true";
  }
  return args;
}

async function ensureDir(dir) {
  await fs.mkdir(dir, { recursive: true });
}

async function listPngFiles(dir) {
  let entries = [];
  try {
    entries = await fs.readdir(dir, { withFileTypes: true });
  } catch (error) {
    if (error.code === "ENOENT") {
      return [];
    }
    throw error;
  }

  return entries
    .filter((entry) => entry.isFile())
    .map((entry) => entry.name)
    .filter((name) => name.toLowerCase().endsWith(".png"))
    .sort();
}

function fromDataUrl(dataUrl) {
  const base64 = dataUrl.split(",")[1];
  return Buffer.from(base64, "base64");
}

function toPngDataUrl(buffer) {
  return `data:image/png;base64,${buffer.toString("base64")}`;
}

const args = parseArgs(process.argv.slice(2));
const baselineDir = path.resolve(args.baseline || DEFAULT_BASELINE);
const candidateDir = path.resolve(args.candidate || DEFAULT_CANDIDATE);
const diffDir = path.resolve(args.diff || DEFAULT_DIFF);
const threshold = Math.max(0, Math.floor(Number(args.threshold || DEFAULT_THRESHOLD)));

await ensureDir(diffDir);

const baselineFiles = await listPngFiles(baselineDir);
const candidateFiles = await listPngFiles(candidateDir);
const baselineSet = new Set(baselineFiles);
const candidateSet = new Set(candidateFiles);

const comparableFiles = baselineFiles.filter((name) => candidateSet.has(name));
const missingInCandidate = baselineFiles.filter((name) => !candidateSet.has(name));
const missingInBaseline = candidateFiles.filter((name) => !baselineSet.has(name));

if (!comparableFiles.length) {
  console.error("[visual] No matching PNG files found between baseline and candidate folders.");
  process.exitCode = 1;
  process.exit();
}

const browser = await puppeteer.launch({ headless: "new" });
const page = await browser.newPage();
await page.setContent("<!doctype html><html><body></body></html>");

const results = [];

for (const fileName of comparableFiles) {
  const baselinePath = path.join(baselineDir, fileName);
  const candidatePath = path.join(candidateDir, fileName);
  const baselineDataUrl = toPngDataUrl(await fs.readFile(baselinePath));
  const candidateDataUrl = toPngDataUrl(await fs.readFile(candidatePath));

  const comparison = await page.evaluate(
    async ({ baselineData, candidateData, thresholdValue }) => {
      function loadImage(src) {
        return new Promise((resolve, reject) => {
          const image = new Image();
          image.onload = () => resolve(image);
          image.onerror = () => reject(new Error(`Failed to load image: ${src}`));
          image.src = src;
        });
      }

      const [baselineImage, candidateImage] = await Promise.all([
        loadImage(baselineData),
        loadImage(candidateData)
      ]);

      const width = Math.max(baselineImage.width, candidateImage.width);
      const height = Math.max(baselineImage.height, candidateImage.height);

      const canvasA = document.createElement("canvas");
      const canvasB = document.createElement("canvas");
      const diffCanvas = document.createElement("canvas");
      canvasA.width = width;
      canvasA.height = height;
      canvasB.width = width;
      canvasB.height = height;
      diffCanvas.width = width;
      diffCanvas.height = height;

      const ctxA = canvasA.getContext("2d");
      const ctxB = canvasB.getContext("2d");
      const diffCtx = diffCanvas.getContext("2d");

      ctxA.drawImage(baselineImage, 0, 0);
      ctxB.drawImage(candidateImage, 0, 0);

      const imageA = ctxA.getImageData(0, 0, width, height);
      const imageB = ctxB.getImageData(0, 0, width, height);
      const diffImage = diffCtx.createImageData(width, height);

      let diffPixels = 0;

      for (let i = 0; i < imageA.data.length; i += 4) {
        const dr = Math.abs(imageA.data[i] - imageB.data[i]);
        const dg = Math.abs(imageA.data[i + 1] - imageB.data[i + 1]);
        const db = Math.abs(imageA.data[i + 2] - imageB.data[i + 2]);
        const da = Math.abs(imageA.data[i + 3] - imageB.data[i + 3]);
        const delta = dr + dg + db + da;

        if (delta > thresholdValue) {
          diffPixels += 1;
          diffImage.data[i] = 255;
          diffImage.data[i + 1] = 0;
          diffImage.data[i + 2] = 128;
          diffImage.data[i + 3] = 255;
        } else {
          const gray = Math.round((imageB.data[i] + imageB.data[i + 1] + imageB.data[i + 2]) / 3);
          diffImage.data[i] = gray;
          diffImage.data[i + 1] = gray;
          diffImage.data[i + 2] = gray;
          diffImage.data[i + 3] = 255;
        }
      }

      diffCtx.putImageData(diffImage, 0, 0);

      return {
        width,
        height,
        totalPixels: width * height,
        diffPixels,
        sizeChanged:
          baselineImage.width !== candidateImage.width || baselineImage.height !== candidateImage.height,
        diffDataUrl: diffCanvas.toDataURL("image/png")
      };
    },
    {
      baselineData: baselineDataUrl,
      candidateData: candidateDataUrl,
      thresholdValue: threshold
    }
  );

  const diffPercent = (comparison.diffPixels / comparison.totalPixels) * 100;
  const diffPath = path.join(diffDir, fileName);
  await fs.writeFile(diffPath, fromDataUrl(comparison.diffDataUrl));

  results.push({
    file: fileName,
    width: comparison.width,
    height: comparison.height,
    diffPixels: comparison.diffPixels,
    totalPixels: comparison.totalPixels,
    diffPercent: Number(diffPercent.toFixed(4)),
    sizeChanged: comparison.sizeChanged
  });
}

await browser.close();

const changed = results.filter((item) => item.diffPixels > 0);
const totalDiffPixels = results.reduce((sum, item) => sum + item.diffPixels, 0);

const summary = {
  generatedAt: new Date().toISOString(),
  baselineDir,
  candidateDir,
  diffDir,
  threshold,
  comparableCount: comparableFiles.length,
  changedCount: changed.length,
  totalDiffPixels,
  missingInCandidate,
  missingInBaseline,
  files: results
};

await fs.writeFile(
  path.join(diffDir, "summary.json"),
  JSON.stringify(summary, null, 2),
  "utf8"
);

console.log(`[visual] Compared ${comparableFiles.length} file(s)`);
console.log(`[visual] Changed files: ${changed.length}`);
console.log(`[visual] Total diff pixels: ${totalDiffPixels}`);

if (missingInCandidate.length) {
  console.log(`[visual] Missing in candidate (${missingInCandidate.length}): ${missingInCandidate.join(", ")}`);
}

if (missingInBaseline.length) {
  console.log(`[visual] Missing in baseline (${missingInBaseline.length}): ${missingInBaseline.join(", ")}`);
}

if (changed.length) {
  const top = changed
    .slice()
    .sort((a, b) => b.diffPixels - a.diffPixels)
    .slice(0, 10);

  console.log("[visual] Top diffs:");
  for (const item of top) {
    console.log(
      `  - ${item.file}: ${item.diffPixels} px changed (${item.diffPercent}%), ${item.width}x${item.height}` +
        (item.sizeChanged ? " [size changed]" : "")
    );
  }
}

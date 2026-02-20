import fs from "fs";
import path from "path";
import puppeteer from "puppeteer";

const root = process.cwd();
const demoPath = path.join(root, "docs", "demo-nav.html");
const outPath = path.join(root, "docs", "demo-nav.png");

if (!fs.existsSync(demoPath)) {
  console.error("demo-nav.html not found:", demoPath);
  process.exit(1);
}

const fileUrl = new URL(`file:///${demoPath.replace(/\\/g, "/")}`);

const browser = await puppeteer.launch({
  headless: "new",
  defaultViewport: { width: 1400, height: 800 },
  args: ["--allow-file-access-from-files"],
});

const page = await browser.newPage();
await page.goto(fileUrl.href, { waitUntil: "networkidle0" });

// Sample colors from the reference image and set CSS vars accordingly.
await page.evaluate(() => {
  const refImg = document.querySelector("#ref img");
  if (!refImg || !refImg.complete) {
    return;
  }

  const canvas = document.createElement("canvas");
  const ctx = canvas.getContext("2d");
  const w = refImg.naturalWidth;
  const h = refImg.naturalHeight;
  canvas.width = w;
  canvas.height = h;
  ctx.drawImage(refImg, 0, 0);

  const sampleRow = (y) => {
    const row = ctx.getImageData(0, Math.round(h * y), w, 1).data;
    let r = 0, g = 0, b = 0;
    const n = w;
    for (let i = 0; i < row.length; i += 4) {
      r += row[i];
      g += row[i + 1];
      b += row[i + 2];
    }
    return [Math.round(r / n), Math.round(g / n), Math.round(b / n)];
  };

  const top = sampleRow(0.05);
  const mid = sampleRow(0.28);
  const deep = sampleRow(0.45);

  const toHex = ([r, g, b]) => {
    const hex = (v) => v.toString(16).padStart(2, "0");
    return `#${hex(r)}${hex(g)}${hex(b)}`;
  };

  const root = document.documentElement;
  root.style.setProperty("--nav-top", toHex([0, 0, 0]));
  root.style.setProperty("--nav-mid", toHex(mid));
  root.style.setProperty("--nav-break", toHex([0, 0, 0]));
  root.style.setProperty("--nav-bottom", toHex(deep));
});

await new Promise((resolve) => setTimeout(resolve, 200));
await page.screenshot({ path: outPath, fullPage: true });

console.log("Saved", outPath);
await browser.close();

import json
import sys
from datetime import datetime, timezone
from pathlib import Path
from PIL import Image


DEFAULT_BASELINE = Path("docs/visual-regression/baseline")
DEFAULT_CANDIDATE = Path("docs/visual-regression/current")
DEFAULT_DIFF = Path("docs/visual-regression/diff")
DEFAULT_THRESHOLD = 0


def compare_images(baseline_path: Path, candidate_path: Path, diff_path: Path, threshold: int):
    baseline = Image.open(baseline_path).convert("RGBA")
    candidate = Image.open(candidate_path).convert("RGBA")
    baseline_size = baseline.size
    candidate_size = candidate.size

    width = max(baseline.width, candidate.width)
    height = max(baseline.height, candidate.height)

    if baseline.size != (width, height):
        padded = Image.new("RGBA", (width, height), (0, 0, 0, 0))
        padded.paste(baseline, (0, 0))
        baseline = padded

    if candidate.size != (width, height):
        padded = Image.new("RGBA", (width, height), (0, 0, 0, 0))
        padded.paste(candidate, (0, 0))
        candidate = padded

    baseline_px = baseline.load()
    candidate_px = candidate.load()
    diff_img = Image.new("RGBA", (width, height), (0, 0, 0, 255))
    diff_px = diff_img.load()

    diff_pixels = 0
    for y in range(height):
        for x in range(width):
            a = baseline_px[x, y]
            b = candidate_px[x, y]
            delta = abs(a[0] - b[0]) + abs(a[1] - b[1]) + abs(a[2] - b[2]) + abs(a[3] - b[3])
            if delta > threshold:
                diff_pixels += 1
                diff_px[x, y] = (255, 0, 128, 255)
            else:
                gray = round((b[0] + b[1] + b[2]) / 3)
                diff_px[x, y] = (gray, gray, gray, 255)

    diff_img.save(diff_path)

    total_pixels = width * height
    diff_percent = round((diff_pixels / total_pixels) * 100, 4) if total_pixels else 0

    return {
        "file": baseline_path.name,
        "width": width,
        "height": height,
        "diffPixels": diff_pixels,
        "totalPixels": total_pixels,
        "diffPercent": diff_percent,
        "sizeChanged": baseline_size != candidate_size,
    }


def list_pngs(directory: Path):
    if not directory.exists():
        return []
    return sorted(p.name for p in directory.glob("*.png"))


def parse_args():
    args = sys.argv[1:]
    if not args:
        return DEFAULT_BASELINE, DEFAULT_CANDIDATE, DEFAULT_DIFF, DEFAULT_THRESHOLD
    if len(args) != 4:
        raise SystemExit("usage: compare-style-guide.py <baselineDir> <candidateDir> <diffDir> <threshold>")
    return Path(args[0]), Path(args[1]), Path(args[2]), int(args[3])


def main():
    baseline_dir, candidate_dir, diff_dir, threshold = parse_args()
    diff_dir.mkdir(parents=True, exist_ok=True)

    baseline_files = list_pngs(baseline_dir)
    candidate_files = set(list_pngs(candidate_dir))
    comparable = [name for name in baseline_files if name in candidate_files]
    missing_in_candidate = [name for name in baseline_files if name not in candidate_files]
    missing_in_baseline = [name for name in list_pngs(candidate_dir) if name not in set(baseline_files)]

    if not comparable:
        raise SystemExit("[visual] No matching PNG files found between baseline and candidate folders.")

    files = []
    for name in comparable:
        files.append(
            compare_images(
                baseline_dir / name,
                candidate_dir / name,
                diff_dir / name,
                threshold,
            )
        )

    changed_count = sum(1 for item in files if item["diffPixels"] > 0)
    total_diff_pixels = sum(item["diffPixels"] for item in files)
    summary = {
        "generatedAt": datetime.now(timezone.utc).isoformat(),
        "baselineDir": str(baseline_dir.resolve()),
        "candidateDir": str(candidate_dir.resolve()),
        "diffDir": str(diff_dir.resolve()),
        "threshold": threshold,
        "comparableCount": len(comparable),
        "changedCount": changed_count,
        "totalDiffPixels": total_diff_pixels,
        "missingInCandidate": missing_in_candidate,
        "missingInBaseline": missing_in_baseline,
        "files": files,
    }

    (diff_dir / "summary.json").write_text(json.dumps(summary, indent=2), encoding="utf-8")

    print(f"[visual] Compared {len(comparable)} file(s)")
    print(f"[visual] Changed files: {changed_count}")
    print(f"[visual] Total diff pixels: {total_diff_pixels}")

    if missing_in_candidate:
        print(f"[visual] Missing in candidate ({len(missing_in_candidate)}): {', '.join(missing_in_candidate)}")

    if missing_in_baseline:
        print(f"[visual] Missing in baseline ({len(missing_in_baseline)}): {', '.join(missing_in_baseline)}")

    if changed_count:
        print("[visual] Top diffs:")
        top = sorted((item for item in files if item["diffPixels"] > 0), key=lambda item: item["diffPixels"], reverse=True)[:10]
        for item in top:
            size_changed = " [size changed]" if item["sizeChanged"] else ""
            print(
                f"  - {item['file']}: {item['diffPixels']} px changed ({item['diffPercent']}%), "
                f"{item['width']}x{item['height']}{size_changed}"
            )


if __name__ == "__main__":
    main()

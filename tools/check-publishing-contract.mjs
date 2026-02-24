/* eslint-disable no-console */
import fs from "node:fs";
import path from "node:path";

const cwd = process.cwd();
const DEFAULT_CONFIG_PATH = path.join(cwd, "tools/publishing-contract.config.json");

function readFileSafe(filePath) {
  if (!fs.existsSync(filePath)) {
    return "";
  }
  return fs.readFileSync(filePath, "utf8");
}

function parseArgs(argv) {
  const options = {
    configPath: DEFAULT_CONFIG_PATH,
    groups: [],
  };

  for (let i = 0; i < argv.length; i += 1) {
    const arg = argv[i];
    if (arg === "--config") {
      options.configPath = path.resolve(cwd, argv[i + 1] || "");
      i += 1;
      continue;
    }
    if (arg === "--group") {
      const value = argv[i + 1] || "";
      options.groups.push(...value.split(",").map((name) => name.trim()).filter(Boolean));
      i += 1;
      continue;
    }
  }

  return options;
}

function toRegex(patternInput, defaultFlags = "gi") {
  if (typeof patternInput === "string") {
    return new RegExp(patternInput, defaultFlags);
  }

  if (
    patternInput &&
    typeof patternInput === "object" &&
    typeof patternInput.pattern === "string"
  ) {
    return new RegExp(patternInput.pattern, patternInput.flags || defaultFlags);
  }

  throw new Error(`Invalid regex config: ${JSON.stringify(patternInput)}`);
}

function extractKeys(content, regexConfigs) {
  const keys = new Set();
  const patterns = regexConfigs.map((config) => toRegex(config));

  for (const pattern of patterns) {
    let match;
    while ((match = pattern.exec(content)) !== null) {
      if (match[1]) {
        keys.add(String(match[1]).toLowerCase());
      }
    }
  }

  return keys;
}

function collectCodeKeys(filePaths, codeKeyPatterns) {
  const keys = new Set();
  for (const file of filePaths) {
    const fullPath = path.join(cwd, file);
    const content = readFileSafe(fullPath);
    const fileKeys = extractKeys(content, codeKeyPatterns);
    for (const key of fileKeys) {
      keys.add(key);
    }
  }
  return keys;
}

function findHardcodedViolations(content, checks) {
  const failures = [];
  for (const check of checks || []) {
    const regex = toRegex(check.regex);
    regex.lastIndex = 0;
    if (regex.test(content)) {
      failures.push(check.message || "Hardcoded-content pattern matched.");
    }
  }
  return failures;
}

function loadConfig(configPath) {
  const raw = readFileSafe(configPath);
  if (!raw) {
    throw new Error(`Publishing contract config not found: ${configPath}`);
  }

  const config = JSON.parse(raw);
  if (!config || !Array.isArray(config.groups) || config.groups.length === 0) {
    throw new Error("Publishing contract config must include a non-empty `groups` array.");
  }

  return config;
}

function extractMatrixKeys(matrixContent, matrixKeyPatterns) {
  return extractKeys(matrixContent, matrixKeyPatterns || []);
}

function runGroupCheck(matrixContent, group) {
  const missingTargets = (group.targetFiles || []).filter(
    (file) => !fs.existsSync(path.join(cwd, file))
  );

  if (missingTargets.length > 0) {
    return {
      hasErrors: true,
      missingTargets,
      undocumentedInMatrix: [],
      listedButUnused: [],
      hardcodedFailures: [],
    };
  }

  const matrixKeys = extractMatrixKeys(matrixContent, group.matrixKeyPatterns);
  const codeKeys = collectCodeKeys(group.targetFiles || [], group.codeKeyPatterns || []);

  const undocumentedInMatrix = [...codeKeys].filter((key) => !matrixKeys.has(key)).sort();
  const listedButUnused = [...matrixKeys].filter((key) => !codeKeys.has(key)).sort();

  const hardcodedFailures = [];
  for (const file of group.targetFiles || []) {
    const content = readFileSafe(path.join(cwd, file));
    const failures = findHardcodedViolations(content, group.hardcodedChecks || []);
    for (const failure of failures) {
      hardcodedFailures.push(`${file}: ${failure}`);
    }
  }

  const hasErrors = undocumentedInMatrix.length > 0 || hardcodedFailures.length > 0;

  return {
    hasErrors,
    missingTargets: [],
    undocumentedInMatrix,
    listedButUnused,
    hardcodedFailures,
  };
}

function main() {
  const args = parseArgs(process.argv.slice(2));
  const config = loadConfig(args.configPath);
  const matrixPath = path.join(cwd, config.matrixPath || "docs/architecture/publishing-contract-matrix.md");

  const matrixContent = readFileSafe(matrixPath);
  if (!matrixContent) {
    console.error(`Publishing contract matrix not found: ${matrixPath}`);
    process.exit(2);
  }

  let groups = config.groups;
  if (args.groups.length > 0) {
    groups = config.groups.filter((group) => args.groups.includes(group.name));
    if (groups.length === 0) {
      console.error(`No matching groups found for: ${args.groups.join(", ")}`);
      process.exit(2);
    }
  }

  let hasErrors = false;

  for (const group of groups) {
    const result = runGroupCheck(matrixContent, group);

    if (result.missingTargets.length > 0) {
      hasErrors = true;
      console.error(`\n[${group.name}] Missing target files:`);
      for (const file of result.missingTargets) {
        console.error(`- ${file}`);
      }
      continue;
    }

    if (result.undocumentedInMatrix.length > 0) {
      hasErrors = true;
      console.error(`\n[${group.name}] Field keys used in code but missing from publishing matrix:`);
      for (const key of result.undocumentedInMatrix) {
        console.error(`- ${key}`);
      }
    }

    if (result.hardcodedFailures.length > 0) {
      hasErrors = true;
      console.error(`\n[${group.name}] Hardcoded-copy contract violations:`);
      for (const failure of result.hardcodedFailures) {
        console.error(`- ${failure}`);
      }
    }

    if ((group.reportUnusedMatrixKeys ?? true) && result.listedButUnused.length > 0) {
      console.log(`\n[${group.name}] Note: keys listed in matrix but not found in target templates:`);
      for (const key of result.listedButUnused) {
        console.log(`- ${key}`);
      }
    }
  }

  if (hasErrors) {
    process.exit(1);
  }

  console.log("Publishing contract checks passed.");
}

main();

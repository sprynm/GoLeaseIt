/* eslint-disable no-console */
const fs = require("fs");
const path = require("path");

const rootArg = process.argv[2];
const rootDir = rootArg ? path.resolve(process.cwd(), rootArg) : path.resolve(process.cwd(), "View");

const phpOpen = /<\?php/g;
const phpClose = /\?>/g;

function stripStringsAndComments(input) {
  let output = "";
  let i = 0;
  let inSingle = false;
  let inDouble = false;
  let inLineComment = false;
  let inBlockComment = false;

  while (i < input.length) {
    const char = input[i];
    const next = input[i + 1];

    if (inLineComment) {
      if (char === "\n") {
        inLineComment = false;
        output += char;
      }
      i += 1;
      continue;
    }

    if (inBlockComment) {
      if (char === "*" && next === "/") {
        inBlockComment = false;
        i += 2;
        continue;
      }
      i += 1;
      continue;
    }

    if (inSingle) {
      if (char === "\\" && next) {
        i += 2;
        continue;
      }
      if (char === "'") {
        inSingle = false;
      }
      i += 1;
      continue;
    }

    if (inDouble) {
      if (char === "\\" && next) {
        i += 2;
        continue;
      }
      if (char === "\"") {
        inDouble = false;
      }
      i += 1;
      continue;
    }

    if (char === "/" && next === "/") {
      inLineComment = true;
      i += 2;
      continue;
    }

    if (char === "/" && next === "*") {
      inBlockComment = true;
      i += 2;
      continue;
    }

    if (char === "'") {
      inSingle = true;
      i += 1;
      continue;
    }

    if (char === "\"") {
      inDouble = true;
      i += 1;
      continue;
    }

    output += char;
    i += 1;
  }

  return output;
}

function getPhpBlocks(content) {
  const blocks = [];
  let startIndex = 0;
  while (true) {
    const openIndex = content.indexOf("<?php", startIndex);
    if (openIndex === -1) break;
    const closeIndex = content.indexOf("?>", openIndex + 5);
    if (closeIndex === -1) {
      blocks.push(content.slice(openIndex + 5));
      break;
    }
    blocks.push(content.slice(openIndex + 5, closeIndex));
    startIndex = closeIndex + 2;
  }
  return blocks;
}

function countMatches(input, regex) {
  const matches = input.match(regex);
  return matches ? matches.length : 0;
}

function collectFiles(startDir, results) {
  const entries = fs.readdirSync(startDir, { withFileTypes: true });
  for (const entry of entries) {
    const resolved = path.join(startDir, entry.name);
    if (entry.isDirectory()) {
      collectFiles(resolved, results);
    } else if (entry.isFile() && resolved.endsWith(".ctp")) {
      results.push(resolved);
    }
  }
}

function countAltKeyword(block, keyword) {
  const clean = stripStringsAndComments(block);
  const length = clean.length;
  let count = 0;
  let i = 0;
  const isWordChar = (char) => /[A-Za-z0-9_]/.test(char);

  while (i < length) {
    if (clean.startsWith(keyword, i)) {
      const prev = i === 0 ? "" : clean[i - 1];
      const next = i + keyword.length < length ? clean[i + keyword.length] : "";
      if (!isWordChar(prev) && !isWordChar(next)) {
        let j = i + keyword.length;
        while (j < length && /\s/.test(clean[j])) j += 1;
        if (clean[j] === "(") {
          let depth = 0;
          while (j < length) {
            const char = clean[j];
            if (char === "(") depth += 1;
            if (char === ")") {
              depth -= 1;
              if (depth === 0) {
                j += 1;
                break;
              }
            }
            j += 1;
          }
          while (j < length && /\s/.test(clean[j])) j += 1;
          if (clean[j] === ":") {
            count += 1;
          }
        }
      }
    }
    i += 1;
  }
  return count;
}

function checkFile(filePath) {
  const content = fs.readFileSync(filePath, "utf8");
  const openCount = countMatches(content, phpOpen);
  const closeCount = countMatches(content, phpClose);
  const phpCloseMismatch = closeCount > openCount;
  const phpOpenMismatch = openCount > closeCount;

  const phpBlocks = getPhpBlocks(content);
  let ifCount = 0;
  let foreachCount = 0;
  let forCount = 0;
  let whileCount = 0;
  let switchCount = 0;
  let endifCount = 0;
  let endforeachCount = 0;
  let endforCount = 0;
  let endwhileCount = 0;
  let endswitchCount = 0;

  for (const block of phpBlocks) {
    const clean = stripStringsAndComments(block);
    ifCount += countAltKeyword(clean, "if");
    foreachCount += countAltKeyword(clean, "foreach");
    forCount += countAltKeyword(clean, "for");
    whileCount += countAltKeyword(clean, "while");
    switchCount += countAltKeyword(clean, "switch");
    endifCount += countMatches(clean, /\bendif\s*;/g);
    endforeachCount += countMatches(clean, /\bendforeach\s*;/g);
    endforCount += countMatches(clean, /\bendfor\s*;/g);
    endwhileCount += countMatches(clean, /\bendwhile\s*;/g);
    endswitchCount += countMatches(clean, /\bendswitch\s*;/g);
  }

  const issues = [];
  if (phpCloseMismatch) {
    issues.push(`PHP tag mismatch: open ${openCount}, close ${closeCount}`);
  }
  if (ifCount !== endifCount) {
    issues.push(`Alt if mismatch: if ${ifCount}, endif ${endifCount}`);
  }
  if (foreachCount !== endforeachCount) {
    issues.push(`Alt foreach mismatch: foreach ${foreachCount}, endforeach ${endforeachCount}`);
  }
  if (forCount !== endforCount) {
    issues.push(`Alt for mismatch: for ${forCount}, endfor ${endforCount}`);
  }
  if (whileCount !== endwhileCount) {
    issues.push(`Alt while mismatch: while ${whileCount}, endwhile ${endwhileCount}`);
  }
  if (switchCount !== endswitchCount) {
    issues.push(`Alt switch mismatch: switch ${switchCount}, endswitch ${endswitchCount}`);
  }

  const hasBlockTokens = content.includes("{{block");
  return {
    filePath,
    issues,
    phpOpenMismatch,
    hasBlockTokens,
  };
}

function main() {
  if (!fs.existsSync(rootDir)) {
    console.error(`Path not found: ${rootDir}`);
    process.exit(2);
  }

  const files = [];
  collectFiles(rootDir, files);
  if (files.length === 0) {
    console.log("No .ctp files found.");
    return;
  }

  const results = files.map(checkFile);
  const withIssues = results.filter((result) => result.issues.length > 0);
  const withTokens = results.filter((result) => result.hasBlockTokens);
  const withOpenPhp = results.filter((result) => result.phpOpenMismatch);

  if (withIssues.length > 0) {
    console.error("CTP balance issues found:");
    for (const result of withIssues) {
      console.error(`- ${path.relative(process.cwd(), result.filePath)}`);
      for (const issue of result.issues) {
        console.error(`  - ${issue}`);
      }
    }
  }

  if (withTokens.length > 0) {
    console.log("Note: CMS block tokens present in:");
    for (const result of withTokens) {
      console.log(`- ${path.relative(process.cwd(), result.filePath)}`);
    }
  }

  if (withOpenPhp.length > 0) {
    console.log("Note: PHP blocks left open at EOF in:");
    for (const result of withOpenPhp) {
      console.log(`- ${path.relative(process.cwd(), result.filePath)}`);
    }
  }

  if (withIssues.length > 0) {
    process.exit(1);
  }
}

main();

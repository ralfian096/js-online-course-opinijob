import { cp, mkdir, readdir, readFile, rm, stat, unlink, writeFile } from "node:fs/promises";
import path from "node:path";

const rootDir = path.resolve(import.meta.dirname, "..");
const sourceDir = path.join(rootDir, "HTML");
const targetDir = path.join(rootDir, "API", "public");
const manifestPath = path.join(targetDir, ".html-sync-manifest.json");
const watchMode = process.argv.includes("--watch");

async function pathExists(targetPath) {
  try {
    await stat(targetPath);
    return true;
  } catch {
    return false;
  }
}

function normalizeRelativePath(relativePath) {
  return relativePath.split(path.sep).join("/");
}

async function walkDirectory(currentDir, baseDir, entries) {
  const dirEntries = await readdir(currentDir, { withFileTypes: true });

  for (const entry of dirEntries) {
    const absolutePath = path.join(currentDir, entry.name);
    const relativePath = normalizeRelativePath(path.relative(baseDir, absolutePath));

    if (entry.isDirectory()) {
      entries.dirs.push(relativePath);
      await walkDirectory(absolutePath, baseDir, entries);
      continue;
    }

    if (entry.isFile()) {
      entries.files.push(relativePath);
    }
  }
}

async function collectSourceEntries() {
  const entries = { files: [], dirs: [] };

  if (!(await pathExists(sourceDir))) {
    throw new Error(`Folder source tidak ditemukan: ${sourceDir}`);
  }

  await walkDirectory(sourceDir, sourceDir, entries);
  entries.files.sort();
  entries.dirs.sort();

  return entries;
}

async function readManifest() {
  if (!(await pathExists(manifestPath))) {
    return { files: [], dirs: [] };
  }

  try {
    const raw = await readFile(manifestPath, "utf8");
    const parsed = JSON.parse(raw);

    return {
      files: Array.isArray(parsed.files) ? parsed.files : [],
      dirs: Array.isArray(parsed.dirs) ? parsed.dirs : [],
    };
  } catch {
    return { files: [], dirs: [] };
  }
}

async function syncHtml() {
  const nextEntries = await collectSourceEntries();
  const previousEntries = await readManifest();

  await mkdir(targetDir, { recursive: true });

  for (const relativeDir of nextEntries.dirs) {
    await mkdir(path.join(targetDir, relativeDir), { recursive: true });
  }

  for (const relativeFile of nextEntries.files) {
    const sourceFile = path.join(sourceDir, relativeFile);
    const targetFile = path.join(targetDir, relativeFile);

    await mkdir(path.dirname(targetFile), { recursive: true });
    await cp(sourceFile, targetFile, { force: true });
  }

  const nextFileSet = new Set(nextEntries.files);
  for (const relativeFile of previousEntries.files) {
    if (nextFileSet.has(relativeFile)) {
      continue;
    }

    const targetFile = path.join(targetDir, relativeFile);
    if (await pathExists(targetFile)) {
      await unlink(targetFile);
    }
  }

  const nextDirSet = new Set(nextEntries.dirs);
  const previousDirs = [...previousEntries.dirs].sort((left, right) => right.length - left.length);
  for (const relativeDir of previousDirs) {
    if (nextDirSet.has(relativeDir)) {
      continue;
    }

    const targetSubdir = path.join(targetDir, relativeDir);
    if (await pathExists(targetSubdir)) {
      await rm(targetSubdir, { recursive: true, force: true });
    }
  }

  await writeFile(
    manifestPath,
    `${JSON.stringify(
      {
        files: nextEntries.files,
        dirs: nextEntries.dirs,
        syncedAt: new Date().toISOString(),
      },
      null,
      2,
    )}\n`,
    "utf8",
  );

  console.log(
    `[sync:html] ${nextEntries.files.length} file dan ${nextEntries.dirs.length} folder disalin ke API/public`,
  );
}

async function createSnapshot(currentDir, baseDir, snapshot) {
  const dirEntries = await readdir(currentDir, { withFileTypes: true });

  for (const entry of dirEntries) {
    const absolutePath = path.join(currentDir, entry.name);
    const relativePath = normalizeRelativePath(path.relative(baseDir, absolutePath));
    const currentStat = await stat(absolutePath);
    const entryType = entry.isDirectory() ? "dir" : "file";

    snapshot[relativePath] = `${entryType}:${currentStat.mtimeMs}:${currentStat.size}`;

    if (entry.isDirectory()) {
      await createSnapshot(absolutePath, baseDir, snapshot);
    }
  }
}

async function buildSnapshot() {
  if (!(await pathExists(sourceDir))) {
    return {};
  }

  const snapshot = {};
  await createSnapshot(sourceDir, sourceDir, snapshot);
  return snapshot;
}

function snapshotsAreEqual(left, right) {
  const leftKeys = Object.keys(left);
  const rightKeys = Object.keys(right);

  if (leftKeys.length !== rightKeys.length) {
    return false;
  }

  for (const key of leftKeys) {
    if (left[key] !== right[key]) {
      return false;
    }
  }

  return true;
}

async function watchHtml() {
  await syncHtml();
  console.log("[watch:html] memantau perubahan pada folder HTML...");

  let previousSnapshot = await buildSnapshot();
  let running = false;

  setInterval(async () => {
    if (running) {
      return;
    }

    running = true;

    try {
      const nextSnapshot = await buildSnapshot();

      if (!snapshotsAreEqual(previousSnapshot, nextSnapshot)) {
        await syncHtml();
        previousSnapshot = nextSnapshot;
      }
    } catch (error) {
      console.error("[watch:html] gagal sinkronisasi:", error.message);
    } finally {
      running = false;
    }
  }, 1000);
}

try {
  if (watchMode) {
    await watchHtml();
  } else {
    await syncHtml();
  }
} catch (error) {
  console.error("[sync:html] gagal:", error.message);
  process.exit(1);
}

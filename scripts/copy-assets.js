import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const sourceDir = path.resolve(__dirname, "../dist/assets");
const targetDir = path.resolve(__dirname, "../wp-theme/assets");

function copyAssets() {
  console.log("📦 Copying assets to WordPress theme...");

  // Ensure target directories exist
  const cssTargetDir = path.join(targetDir, "css");
  const jsTargetDir = path.join(targetDir, "js");

  [cssTargetDir, jsTargetDir].forEach(dir => {
    if (!fs.existsSync(dir)) {
      fs.mkdirSync(dir, { recursive: true });
    }
  });

  if (!fs.existsSync(sourceDir)) {
    console.error("❌ Source assets directory not found:", sourceDir);
    return false;
  }

  const files = fs.readdirSync(sourceDir);

  files.forEach(file => {
    const sourcePath = path.join(sourceDir, file);
    const stat = fs.statSync(sourcePath);

    if (stat.isFile()) {
      const ext = path.extname(file);
      let targetPath;

      if (ext === ".css") {
        targetPath = path.join(cssTargetDir, file);
      } else if (ext === ".js") {
        targetPath = path.join(jsTargetDir, file);
      } else {
        // Copy other assets to the main assets directory
        targetPath = path.join(targetDir, file);
      }

      fs.copyFileSync(sourcePath, targetPath);
      console.log(`✓ ${file} → ${path.relative(__dirname, targetPath)}`);
    }
  });

  console.log(`✅ Copied!`);
  return true;
}

copyAssets();

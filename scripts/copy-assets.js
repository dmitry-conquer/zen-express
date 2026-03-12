import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const targetDir = path.resolve(__dirname, "../wp-theme/assets");

const COPY_MAP = {
  ".css": { src: "../dist/assets", dest: "css" },
  ".js":  { src: "../dist/assets", dest: "js" },
  ".woff2": { src: "../dist/fonts", dest: "fonts" },
  ".woff":  { src: "../dist/fonts", dest: "fonts" },
  ".ttf":   { src: "../dist/fonts", dest: "fonts" },
};

function copyAssets() {
  console.log("Copying assets to WordPress theme...");

  const sourceDirs = [...new Set(Object.values(COPY_MAP).map(v => v.src))];

  sourceDirs.forEach(srcRel => {
    const srcDir = path.resolve(__dirname, srcRel);
    if (!fs.existsSync(srcDir)) return console.warn(`⚠️ Not found: ${srcDir}`);

    fs.readdirSync(srcDir).forEach(file => {
      const config = COPY_MAP[path.extname(file)];
      if (!config || config.src !== srcRel) return;

      const destDir = path.join(targetDir, config.dest);
      fs.mkdirSync(destDir, { recursive: true });

      fs.copyFileSync(path.join(srcDir, file), path.join(destDir, file));
      console.log(`✓ ${file} → ${config.dest}/`);
    });
  });

  console.log("✅ Copied!");
}

copyAssets();
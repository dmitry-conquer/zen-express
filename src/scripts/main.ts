import "../styles/main.css";
import "taos"; // Taos — utility-first CSS animation library (Tailwind-compatible enter/leave transitions)
import Lenis from "lenis";

// Alpine.js handles all interactivity in this project.
// Loaded via CDN in pages/*.html to keep this bundle lean.

// Wrap any third-party library or module initialization here
// to ensure the DOM is fully parsed before execution.
document.addEventListener("DOMContentLoaded", () => {
  (window.lenis as any) = new Lenis({
    autoRaf: true,
  });
});

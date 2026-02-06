import "../styles/main.css";
import Alpine from "alpinejs";

// Make Alpine available globally for debugging
declare global {
  interface Window {
    Alpine: typeof Alpine;
  }
}
window.Alpine = Alpine;

// Register Alpine plugins here before start()
// Example: Alpine.plugin(focus)

// Initialize Alpine.js
Alpine.start();

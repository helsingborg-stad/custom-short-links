import { createViteConfig } from "vite-config-factory";

const entries = {
    'js/custom-short-links': './source/js/custom-short-links.js',        
};

export default createViteConfig(entries, {
	outDir: "assets/dist",
	manifestFile: "manifest.json",
});

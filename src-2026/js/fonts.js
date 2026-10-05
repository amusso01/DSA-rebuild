// Self-hosted fonts (Fontsource). Each import is a CSS file of @font-face rules;
// webpack copies the font files into dist-2026/fonts/.
// Latin + Latin Extended only: unicode-range makes browsers fetch latin-ext only when a page needs it.
// To add a weight: import its latin-<weight>.css and latin-ext-<weight>.css below.

// Manrope: body text ($text__fontname)
import '@fontsource/manrope/latin-400.css'
import '@fontsource/manrope/latin-ext-400.css'
import '@fontsource/manrope/latin-500.css'
import '@fontsource/manrope/latin-ext-500.css'
import '@fontsource/manrope/latin-600.css'
import '@fontsource/manrope/latin-ext-600.css'
import '@fontsource/manrope/latin-700.css'
import '@fontsource/manrope/latin-ext-700.css'

// Titillium Web: headings ($header__fontname)
import '@fontsource/titillium-web/latin-400.css'
import '@fontsource/titillium-web/latin-ext-400.css'
import '@fontsource/titillium-web/latin-600.css'
import '@fontsource/titillium-web/latin-ext-600.css'
import '@fontsource/titillium-web/latin-700.css'
import '@fontsource/titillium-web/latin-ext-700.css'

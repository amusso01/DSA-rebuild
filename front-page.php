<?php

/**
 * Front page in the 2026 layout. WordPress doesn't load it under this name:
 * at go-live it's renamed to front-page.php, replacing the legacy one (see "Homepage 2026" in restructure.md).
 * It loads the "Homepage 2026" template, so the private test page and the live front page are the same markup.
 *
 * @package FDRY
 */

get_template_part('templates-2026/page-homepage-2026');

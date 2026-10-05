<?php
/**
 * Main Functions File - used for:
 * • including other function-files
 * • WP-Support & WP-Setup
 * • general functions like replacements, translations
 *
 * @author jonathan Soto
 *
 */
/*==================================================================================
  WP SETUP
==================================================================================*/
// general setup like menu, login font, GTM
require get_template_directory() . '/library/function-setup.php';

// 2026 layout: new functions live in inc/ (see restructure.md)
require get_template_directory() . '/inc/function-dev.php';

<?php
/**
 * Asset Helper Functions
 * Provides functions to generate proper asset URLs regardless of file location
 */

// Include config file if not already included
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config.php';
}

/**
 * Get CSS URL
 */
function css_url($file) {
    return CSS_URL . '/' . $file;
}

/**
 * Get JavaScript URL
 */
function js_url($file) {
    return JS_URL . '/' . $file;
}

/**
 * Get Image URL
 */
function img_url($file) {
    return IMAGES_URL . '/' . $file;
}

/**
 * Get Upload URL
 */
function upload_url($file) {
    return UPLOADS_URL . '/' . $file;
}

/**
 * Get Full URL for any asset
 */
function asset_url($path) {
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Get Controller URL
 */
function controller_url($path) {
    return CONTROLLERS_URL . '/' . ltrim($path, '/');
}

/**
 * Get View URL
 */
function view_url($path) {
    return VIEWS_URL . '/' . ltrim($path, '/');
}
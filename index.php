<?php
/**
 * Location Tracker - Main Entry Point
 * File: index.php
 */

require_once 'config.php';
require_once 'models.php';
require_once 'auth.php';
require_once 'utils.php';
require_once 'router.php';

// Run cleanup
Database::getInstance()->cleanup();

// Initialize router
$router = new Router();

// Load routes
require_once 'routes.php';

// Dispatch request
$router->dispatch();

<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default route - load login page
$routes->get('/', 'Auth::login', ['as' => 'home']);

// Authentication routes
$routes->get('login', 'Auth::login', ['as' => 'login']);
$routes->post('authenticate', 'Auth::authenticate', ['as' => 'authenticate']);
$routes->get('logout', 'Auth::logout', ['as' => 'logout']);
$routes->get('dashboard', 'Auth::dashboard', ['as' => 'dashboard']);
$routes->get('register', 'Auth::register', ['as' => 'register']);

// Supplier routes
$routes->get('suppliers', 'Supplier::index', ['as' => 'supplier.index']);
$routes->get('suppliers/create', 'Supplier::create', ['as' => 'supplier.create']);
$routes->post('suppliers/store', 'Supplier::store', ['as' => 'supplier.store']);
$routes->get('suppliers/(:num)/edit', 'Supplier::edit/$1', ['as' => 'supplier.edit']);
$routes->post('suppliers/(:num)/update', 'Supplier::update/$1', ['as' => 'supplier.update']);
$routes->post('suppliers/(:num)/delete', 'Supplier::delete/$1', ['as' => 'supplier.delete']);

// Color routes
$routes->get('colors', 'Color::index', ['as' => 'color.index']);
$routes->get('colors/create', 'Color::create', ['as' => 'color.create']);
$routes->post('colors/store', 'Color::store', ['as' => 'color.store']);
$routes->get('colors/(:num)/edit', 'Color::edit/$1', ['as' => 'color.edit']);
$routes->post('colors/(:num)/update', 'Color::update/$1', ['as' => 'color.update']);
$routes->post('colors/(:num)/delete', 'Color::delete/$1', ['as' => 'color.delete']);

// Size Master routes
$routes->get('size-masters', 'SizeMaster::index', ['as' => 'sizemaster.index']);
$routes->get('size-masters/create', 'SizeMaster::create', ['as' => 'sizemaster.create']);
$routes->post('size-masters/store', 'SizeMaster::store', ['as' => 'sizemaster.store']);
$routes->get('size-masters/(:num)/edit', 'SizeMaster::edit/$1', ['as' => 'sizemaster.edit']);
$routes->post('size-masters/(:num)/update', 'SizeMaster::update/$1', ['as' => 'sizemaster.update']);
$routes->post('size-masters/(:num)/delete', 'SizeMaster::delete/$1', ['as' => 'sizemaster.delete']);

// Item routes
$routes->get('items', 'Item::index', ['as' => 'item.index']);
$routes->get('items/create', 'Item::create', ['as' => 'item.create']);
$routes->post('items/store', 'Item::store', ['as' => 'item.store']);
$routes->get('items/(:num)/print-sticker', 'Item::printSticker/$1', ['as' => 'item.printSticker']);
$routes->get('items/(:num)/edit', 'Item::edit/$1', ['as' => 'item.edit']);
$routes->post('items/(:num)/update', 'Item::update/$1', ['as' => 'item.update']);
$routes->post('items/(:num)/delete', 'Item::delete/$1', ['as' => 'item.delete']);
$routes->get('items/(:num)/get-data', 'Item::getItemData/$1', ['as' => 'item.getItemData']);
$routes->post('item/addItemValue', 'Item::addItemValue', ['as' => 'item.addItemValue']);
$routes->get('item/getVariantForm', 'Item::getVariantForm', ['as' => 'item.getVariantForm']);
$routes->post('item/checkArticleExists', 'Item::checkArticleExists', ['as' => 'item.checkArticleExists']);
$routes->post('item/checkImageExists', 'Item::checkImageExists', ['as' => 'item.checkImageExists']);
$routes->post('item/uploadVariantImage', 'Item::uploadVariantImage', ['as' => 'item.uploadVariantImage']);

// Settings routes
$routes->get('settings', 'Setting::index', ['as' => 'setting.index']);
$routes->post('settings/store', 'Setting::store', ['as' => 'setting.store']);
$routes->post('settings/update', 'Setting::update', ['as' => 'setting.update']);
$routes->post('settings/(:num)/delete', 'Setting::delete/$1', ['as' => 'setting.delete']);

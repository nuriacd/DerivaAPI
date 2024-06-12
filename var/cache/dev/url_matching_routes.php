<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/contact' => [[['_route' => 'app_contact_email', '_controller' => 'App\\Controller\\ContactController::sendContactEmail'], null, ['POST' => 0], null, true, false, null]],
        '/ingredient' => [[['_route' => 'app_ingredients_list', '_controller' => 'App\\Controller\\IngredientController::index'], null, ['GET' => 0], null, true, false, null]],
        '/ingredient/new' => [[['_route' => 'app_ingredient_new', '_controller' => 'App\\Controller\\IngredientController::new'], null, ['POST' => 0], null, false, false, null]],
        '/order' => [[['_route' => 'app_order_index', '_controller' => 'App\\Controller\\OrderController::index'], null, ['GET' => 0], null, true, false, null]],
        '/order/new' => [[['_route' => 'app_order_new', '_controller' => 'App\\Controller\\OrderController::new'], null, ['POST' => 0], null, false, false, null]],
        '/product' => [[['_route' => 'app_product_index', '_controller' => 'App\\Controller\\ProductController::index'], null, ['GET' => 0], null, true, false, null]],
        '/product/new' => [[['_route' => 'app_product_new', '_controller' => 'App\\Controller\\ProductController::new'], null, ['POST' => 0], null, false, false, null]],
        '/product/drinks/get' => [[['_route' => 'app_drink_list', '_controller' => 'App\\Controller\\ProductController::listDrinks'], null, ['GET' => 0], null, false, false, null]],
        '/product/dishes/get' => [[['_route' => 'app_dishes_list', '_controller' => 'App\\Controller\\ProductController::listDishes'], null, ['GET' => 0], null, false, false, null]],
        '/restaurant' => [[['_route' => 'app_restaurant_index', '_controller' => 'App\\Controller\\RestaurantController::index'], null, ['GET' => 0], null, true, false, null]],
        '/restaurant/new' => [[['_route' => 'app_restaurant_new', '_controller' => 'App\\Controller\\RestaurantController::new'], null, ['POST' => 0], null, false, false, null]],
        '/user' => [[['_route' => 'app_user_index', '_controller' => 'App\\Controller\\UserController::index'], null, ['GET' => 0], null, true, false, null]],
        '/user/clients' => [[['_route' => 'app_client_index', '_controller' => 'App\\Controller\\UserController::getClients'], null, ['GET' => 0], null, false, false, null]],
        '/user/employees' => [[['_route' => 'app_employees_index', '_controller' => 'App\\Controller\\UserController::getEmployees'], null, ['GET' => 0], null, false, false, null]],
        '/user/new' => [[['_route' => 'app_user_new', '_controller' => 'App\\Controller\\UserController::new'], null, ['POST' => 0], null, false, false, null]],
        '/user/new/employee' => [[['_route' => 'app_employee_new', '_controller' => 'App\\Controller\\UserController::newEmployee'], null, ['POST' => 0], null, false, false, null]],
        '/user/login' => [[['_route' => 'app_user_login', '_controller' => 'App\\Controller\\UserController::login'], null, ['POST' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/ingredient/(?'
                    .'|([^/]++)(?'
                        .'|(*:68)'
                        .'|/edit(*:80)'
                        .'|(*:87)'
                    .')'
                    .'|restaurant/([^/]++)(?'
                        .'|(*:117)'
                        .'|/update(*:132)'
                    .')'
                .')'
                .'|/order/(?'
                    .'|([^/]++)(?'
                        .'|/(?'
                            .'|s(?'
                                .'|how(*:174)'
                                .'|tatus(*:187)'
                            .')'
                            .'|edit(*:200)'
                        .')'
                        .'|(*:209)'
                    .')'
                    .'|pending/([^/]++)(*:234)'
                    .'|c(?'
                        .'|omplete/([^/]++)(*:262)'
                        .'|ancelled/([^/]++)(*:287)'
                    .')'
                    .'|restaurant/([^/]++)(?'
                        .'|(*:318)'
                        .'|/can\\-deliver(*:339)'
                    .')'
                    .'|user(*:352)'
                .')'
                .'|/product/(?'
                    .'|([^/]++)(?'
                        .'|(*:384)'
                        .'|/(?'
                            .'|edit(*:400)'
                            .'|img(*:411)'
                        .')'
                        .'|(*:420)'
                    .')'
                    .'|drink/restaurant/([^/]++)(?'
                        .'|(*:457)'
                        .'|/update(*:472)'
                    .')'
                .')'
                .'|/restaurant/(?'
                    .'|([^/]++)(*:505)'
                    .'|name/([^/]++)(*:526)'
                    .'|([^/]++)(?'
                        .'|/edit(*:550)'
                        .'|(*:558)'
                    .')'
                    .'|delivery/([^/]++)(*:584)'
                .')'
                .'|/user/([^/]++)/(?'
                    .'|get(*:614)'
                    .'|edit(?'
                        .'|(*:629)'
                        .'|Pwd(*:640)'
                    .')'
                    .'|delete(*:655)'
                    .'|checkPwd(*:671)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        68 => [[['_route' => 'app_ingredient_show', '_controller' => 'App\\Controller\\IngredientController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        80 => [[['_route' => 'app_ingredient_edit', '_controller' => 'App\\Controller\\IngredientController::edit'], ['id'], ['PUT' => 0], null, false, false, null]],
        87 => [[['_route' => 'app_ingredient_delete', '_controller' => 'App\\Controller\\IngredientController::delete'], ['id'], ['DELETE' => 0], null, false, true, null]],
        117 => [[['_route' => 'app_restaurant_ingredients_list', '_controller' => 'App\\Controller\\IngredientController::listRestaurantIngredients'], ['restaurantId'], ['GET' => 0], null, false, true, null]],
        132 => [[['_route' => 'app_restaurant_ingredient_update', '_controller' => 'App\\Controller\\IngredientController::updateRestaurantIngredient'], ['restaurantId'], ['PUT' => 0], null, false, false, null]],
        174 => [[['_route' => 'app_order_show', '_controller' => 'App\\Controller\\OrderController::show'], ['id'], ['GET' => 0], null, false, false, null]],
        187 => [[['_route' => 'app_edit_status', '_controller' => 'App\\Controller\\OrderController::editStatus'], ['id'], ['PUT' => 0], null, false, false, null]],
        200 => [[['_route' => 'app_order_edit', '_controller' => 'App\\Controller\\OrderController::edit'], ['id'], ['PUT' => 0], null, false, false, null]],
        209 => [[['_route' => 'app_order_delete', '_controller' => 'App\\Controller\\OrderController::delete'], ['id'], ['DELETE' => 0], null, false, true, null]],
        234 => [[['_route' => 'app_order_pending', '_controller' => 'App\\Controller\\OrderController::getPendingOrders'], ['restaurant'], ['GET' => 0], null, false, true, null]],
        262 => [[['_route' => 'app_order_complete', '_controller' => 'App\\Controller\\OrderController::getCompleteOrders'], ['restaurant'], ['GET' => 0], null, false, true, null]],
        287 => [[['_route' => 'app_order_cancelled', '_controller' => 'App\\Controller\\OrderController::getCancelledOrders'], ['restaurant'], ['GET' => 0], null, false, true, null]],
        318 => [[['_route' => 'app_order_restaurant', '_controller' => 'App\\Controller\\OrderController::getRestaurantOrders'], ['id'], ['GET' => 0], null, false, true, null]],
        339 => [[['_route' => 'app_order_can_deliver', '_controller' => 'App\\Controller\\OrderController::canDeliver'], ['id'], ['POST' => 0], null, false, false, null]],
        352 => [[['_route' => 'app_user_orders', '_controller' => 'App\\Controller\\OrderController::getUserOrders'], [], ['POST' => 0], null, false, false, null]],
        384 => [[['_route' => 'app_product_show', '_controller' => 'App\\Controller\\ProductController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        400 => [[['_route' => 'app_product_edit', '_controller' => 'App\\Controller\\ProductController::edit'], ['id'], ['PUT' => 0], null, false, false, null]],
        411 => [[['_route' => 'app_product_image', '_controller' => 'App\\Controller\\ProductController::addImage'], ['id'], ['PUT' => 0], null, false, false, null]],
        420 => [[['_route' => 'app_product_delete', '_controller' => 'App\\Controller\\ProductController::delete'], ['id'], ['DELETE' => 0], null, false, true, null]],
        457 => [[['_route' => 'app_restaurant_drink_list', '_controller' => 'App\\Controller\\ProductController::listRestaurantDrinks'], ['restaurantId'], ['GET' => 0], null, false, true, null]],
        472 => [[['_route' => 'app_restaurant_drink_update', '_controller' => 'App\\Controller\\ProductController::updateRestaurantDrink'], ['restaurantId'], ['PUT' => 0], null, false, false, null]],
        505 => [[['_route' => 'app_restaurant_show', '_controller' => 'App\\Controller\\RestaurantController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        526 => [[['_route' => 'app_restaurant_name', '_controller' => 'App\\Controller\\RestaurantController::getName'], ['id'], ['GET' => 0], null, false, true, null]],
        550 => [[['_route' => 'app_restaurant_edit', '_controller' => 'App\\Controller\\RestaurantController::edit'], ['id'], ['PUT' => 0], null, false, false, null]],
        558 => [[['_route' => 'app_restaurant_delete', '_controller' => 'App\\Controller\\RestaurantController::delete'], ['id'], ['DELETE' => 0], null, false, true, null]],
        584 => [[['_route' => 'app_restaurant_by_delivery_city', '_controller' => 'App\\Controller\\RestaurantController::getRestaurantByDeliveryCity'], ['city'], ['GET' => 0], null, false, true, null]],
        614 => [[['_route' => 'app_user_show', '_controller' => 'App\\Controller\\UserController::show'], ['id'], ['GET' => 0], null, false, false, null]],
        629 => [[['_route' => 'app_user_edit', '_controller' => 'App\\Controller\\UserController::edit'], ['id'], ['PUT' => 0], null, false, false, null]],
        640 => [[['_route' => 'app_pwd_edit', '_controller' => 'App\\Controller\\UserController::editPwd'], ['id'], ['PUT' => 0], null, false, false, null]],
        655 => [[['_route' => 'app_user_delete', '_controller' => 'App\\Controller\\UserController::delete'], ['id'], ['DELETE' => 0], null, false, false, null]],
        671 => [
            [['_route' => 'app_user_checkPwd', '_controller' => 'App\\Controller\\UserController::checkPassword'], ['id'], ['POST' => 0], null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];

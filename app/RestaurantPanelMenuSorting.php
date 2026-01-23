<?php

namespace App;

enum RestaurantPanelMenuSorting : int
{
    case RESTAURANTS_DETAILS = 1;
    case BASE_CATEGORY = 2; // Inserted
    case TABLES = 3; // Shifted
    case MENU_CATEGORY = 4; // Shifted
    case MENU = 5; // Shifted
    case CUSTOMER = 6;
    case RESERVATION = 7;
    case USERS = 8;
    case SETTINGS = 10;
    case BUSINESS_HOURS = 9;
}
